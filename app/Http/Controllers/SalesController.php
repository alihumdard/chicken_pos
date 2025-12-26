<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RateFormula;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DailyRate;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesController extends Controller
{
public function index()
{
    $customers = Customer::orderBy('name')->get();
    $dailyRates = DailyRate::latest()->first();
    
    // 🟢 Fetch all formulas and key them by their rate_key (e.g., 'wholesale_rate')
    $formulas = RateFormula::all()->keyBy('rate_key');

    $rates = [
        'wholesale' => [
            'wholesale_rate'                 => $dailyRates->wholesale_rate ?? 0.00,
            'wholesale_mix_rate'             => $dailyRates->wholesale_mix_rate ?? 0.00,
            'wholesale_chest_rate'           => $dailyRates->wholesale_chest_rate ?? 0.00,
            'wholesale_thigh_rate'           => $dailyRates->wholesale_thigh_rate ?? 0.00,
            'wholesale_customer_piece_rate'  => $dailyRates->wholesale_customer_piece_rate ?? 0.00,
            'wholesale_chest_and_leg_pieces' => $dailyRates->wholesale_chest_and_leg_pieces ?? 0.00,
            'wholesale_drum_sticks'          => $dailyRates->wholesale_drum_sticks ?? 0.00,
            'wholesale_chest_boneless'       => $dailyRates->wholesale_chest_boneless ?? 0.00,
            'wholesale_thigh_boneless'       => $dailyRates->wholesale_thigh_boneless ?? 0.00,
            'wholesale_kalagi_pot_gardan'    => $dailyRates->wholesale_kalagi_pot_gardan ?? 0.00,
        ],
        'retail' => [
            'live_chicken_rate'              => $dailyRates->live_chicken_rate ?? 0.00,
            'retail_mix_rate'                => $dailyRates->retail_mix_rate ?? 0.00,
            'retail_chest_rate'              => $dailyRates->retail_chest_rate ?? 0.00,
            'retail_thigh_rate'              => $dailyRates->retail_thigh_rate ?? 0.00,
            'retail_piece_rate'              => $dailyRates->retail_piece_rate ?? 0.00,
            'retail_chest_and_leg_pieces'    => $dailyRates->retail_chest_and_leg_pieces ?? 0.00,
            'retail_drum_sticks'             => $dailyRates->retail_drum_sticks ?? 0.00,
            'retail_chest_boneless'          => $dailyRates->retail_chest_boneless ?? 0.00,
            'retail_thigh_boneless'          => $dailyRates->retail_thigh_boneless ?? 0.00,
            'retail_kalagi_pot_gardan'       => $dailyRates->retail_kalagi_pot_gardan ?? 0.00,
        ]
    ];

    // 🟢 Pass $formulas to the view
    return view('pages.sales.index', compact('customers', 'rates', 'formulas'));
}

    // ... (Your store method remains the same) ...
    public function store(Request $request)
    {
        // ... (Keep existing store logic) ...
        // I am omitting it here to save space since it was correct.
        // Let me know if you need the full store method again.
         $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'rate_channel'      => 'required|in:wholesale,retail',
            'cart_items'        => 'required|array|min:1',
            'cart_items.*.category' => 'required|string|max:50',
            'cart_items.*.weight'   => 'required|numeric|min:0.001',
            'cart_items.*.rate'     => 'required|numeric|min:0',
            'total_payable'         => 'required|numeric|min:0',
            'cash_received'         => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 1. Stock Check
            $total_weight_to_sell = 0.00;
            foreach ($validated['cart_items'] as $item) {
                $total_weight_to_sell += (float) $item['weight'];
            }

            $total_purchased = Purchase::sum('net_live_weight');
            $total_sold_past = DB::table('sale_items')->sum('weight_kg');
            $current_net_stock = $total_purchased - $total_sold_past;

            if ($total_weight_to_sell > $current_net_stock) {
                DB::rollBack();
                $available_display = number_format(max(0, $current_net_stock), 2);
                $selling_display = number_format($total_weight_to_sell, 2);
                return response()->json([
                    'message' => "Error: Insufficient stock. Selling $selling_display KG, but only $available_display KG available.",
                ], 400);
            }

            // 2. Prepare Data
            $customer = Customer::findOrFail($validated['customer_id']);
            $totalAmount = $validated['total_payable'];
            $cashReceived = $validated['cash_received'] ?? 0;
            
            // Determine Status
            $paymentStatus = 'credit';
            if ($cashReceived >= $totalAmount) {
                $paymentStatus = 'paid';
            } elseif ($cashReceived > 0) {
                $paymentStatus = 'partial';
            }

            // 3. Create Sale Record
            $sale = Sale::create([
                'customer_id'    => $customer->id,
                'total_amount'   => $totalAmount,
                'paid_amount'    => $cashReceived,
                'payment_status' => $paymentStatus,
                'sale_channel'   => $validated['rate_channel'],
            ]);

            // 4. Save Sale Items
            $saleItemsData = [];
            foreach ($validated['cart_items'] as $item) {
                $line_total = $item['weight'] * $item['rate'];
                $saleItemsData[] = new SaleItem([ 
                    'product_category' => $item['category'],
                    'weight_kg'        => $item['weight'],
                    'rate_pkr'         => $item['rate'],
                    'line_total'       => $line_total,
                ]);
            }
            $sale->items()->saveMany($saleItemsData);

            // 5. Update Balance & Ledger
            
            // A. Record SALE (Debit)
            $customer->current_balance += $totalAmount;
            $customer->save();

            DB::table('transactions')->insert([
                'customer_id' => $customer->id,
                'date'        => now(),
                'type'        => 'sale',
                'description' => "Sale #{$sale->id} (" . count($validated['cart_items']) . " items)",
                'debit'       => $totalAmount,
                'credit'      => 0,
                'balance'     => $customer->current_balance,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // B. Record PAYMENT (Credit)
            if ($cashReceived > 0) {
                $customer->current_balance -= $cashReceived;
                $customer->save();

                DB::table('transactions')->insert([
                    'customer_id' => $customer->id,
                    'date'        => now(),
                    'type'        => 'payment',
                    'description' => "Cash Received for Sale #{$sale->id}",
                    'debit'       => 0,
                    'credit'      => $cashReceived,
                    'balance'     => $customer->current_balance,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'message'         => 'Sale confirmed successfully.',
                'sale_id'         => $sale->id,
                'customer_id'     => $customer->id,
                'customer_name'   => $customer->name,
                'updated_balance' => number_format($customer->current_balance, 2, '.', ''),
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Sales Transaction Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }
}