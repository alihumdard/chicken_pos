<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\RateFormula;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DailyRate;
use App\Models\Purchase;
use App\Models\Shop; // 🟢 Import Shop Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('name')->get();
        $shops = Shop::all(); // 🟢 Fetch all shops for the dropdown
        
        // ... (Existing Rate Logic) ...
        $formulas = RateFormula::where('status', true)->get()->groupBy('channel');
        $dailyRatesRecord = DailyRate::latest()->first();
        $rateValues = $dailyRatesRecord ? $dailyRatesRecord->rate_values : [];

        $rates = [
            'wholesale' => [],
            'retail'    => []
        ];

        if(isset($formulas['wholesale'])) {
            foreach($formulas['wholesale'] as $f) {
                $rates['wholesale'][$f->rate_key] = $rateValues[$f->rate_key] ?? 0.00;
            }
        }

        if(isset($formulas['retail'])) {
            foreach($formulas['retail'] as $f) {
                $rates['retail'][$f->rate_key] = $rateValues[$f->rate_key] ?? 0.00;
            }
        }

        return view('pages.sales.index', compact('customers', 'rates', 'formulas', 'shops')); // 🟢 Pass 'shops'
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'shop_id'        => 'required|exists:shops,id', // 🟢 Validate Shop ID
            'rate_channel'   => 'required|in:wholesale,retail',
            'cart_items'     => 'required|array|min:1',
            'cart_items.*.category' => 'required|string',
            'cart_items.*.weight'   => 'required|numeric',
            'cart_items.*.rate'     => 'required|numeric',
            'total_payable'  => 'required|numeric',
            'cash_received'  => 'nullable|numeric',
            'extra_charges'  => 'nullable|numeric',
            'discount'       => 'nullable|numeric',
            'note'           => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // 1. Stock Check (Shop-Specific)
            $shopId = $validated['shop_id'];
            $shop = Shop::find($shopId);

            $total_weight_to_sell = 0.00;
            foreach ($validated['cart_items'] as $item) {
                $total_weight_to_sell += (float) $item['weight'];
            }

            // Using the Shop model's helper to get live stock
            $current_net_stock = $shop->current_stock;

            if ($total_weight_to_sell > $current_net_stock) {
                DB::rollBack();
                $available_display = number_format(max(0, $current_net_stock), 2);
                $selling_display = number_format($total_weight_to_sell, 2);
                return response()->json([
                    'message' => "Error: Insufficient stock in {$shop->name}. Selling $selling_display KG, but only $available_display KG available.",
                ], 400);
            }

            // 2. Prepare Data
            $customer = Customer::findOrFail($validated['customer_id']);
            $totalAmount = $validated['total_payable'];
            $cashReceived = $validated['cash_received'] ?? 0;
            
            $paymentStatus = 'credit';
            if ($cashReceived >= $totalAmount) {
                $paymentStatus = 'paid';
            } elseif ($cashReceived > 0) {
                $paymentStatus = 'partial';
            }

            // 3. Create Sale Record (Linked to Shop)
            $sale = Sale::create([
                'shop_id'        => $shopId, // 🟢 Save Shop ID
                'customer_id'    => $customer->id,
                'total_amount'   => $totalAmount,
                'paid_amount'    => $cashReceived,
                'payment_status' => $paymentStatus,
                'sale_channel'   => $validated['rate_channel'],
                'note'           => $validated['note'],
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

            // 5. Update Ledger
            $customer->current_balance += $totalAmount;
            $customer->save();

            DB::table('transactions')->insert([
                'shop_id'     => $shopId, // 🟢 Record Shop in Transaction
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

            if ($cashReceived > 0) {
                $customer->current_balance -= $cashReceived;
                $customer->save();

                DB::table('transactions')->insert([
                    'shop_id'     => $shopId, // 🟢 Record Shop in Payment
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