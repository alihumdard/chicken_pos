<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale; // Import the Sale Model
use App\Models\SaleItem; // Import the SaleItem Model
use App\Models\DailyRate; // Import the DailyRate Model
use App\Models\Purchase; // 🟢 ADDED: Import Purchase Model for stock calculation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For database transaction
use Exception; // For error handling

class SalesController extends Controller
{
    /**
     * Display a listing of customers and the sales interface.
     */
    public function index()
    {
        // 1. Fetch all customers
        $customers = Customer::orderBy('name')->get();

        // 2. Fetch the latest active daily rates
        $dailyRates = DailyRate::latest()->first();

        // Prepare the rates data structure for JavaScript
        $rates = [
            'wholesale' => [
                'wholesale_rate' => $dailyRates->wholesale_rate ?? 0.00, // Wholesale (Truck)
                'live_chicken_rate' => $dailyRates->live_chicken_rate ?? 0.00, // Live Chicken
                'wholesale_hotel_mix_rate' => $dailyRates->wholesale_hotel_mix_rate ?? 0.00, // Hotel Mix
                'wholesale_hotel_chest_rate' => $dailyRates->wholesale_hotel_chest_rate ?? 0.00, // Hotel Chest
                'wholesale_hotel_thigh_rate' => $dailyRates->wholesale_hotel_thigh_rate ?? 0.00, // Hotel Thigh
                'wholesale_customer_piece_rate' => $dailyRates->wholesale_customer_piece_rate ?? 0.00, // Customer Piece
            ],
            'retail' => [
                'retail_mix_rate' => $dailyRates->retail_mix_rate ?? 0.00,
                'retail_chest_rate' => $dailyRates->retail_chest_rate ?? 0.00,
                'retail_thigh_rate' => $dailyRates->retail_thigh_rate ?? 0.00,
                'retail_piece_rate' => $dailyRates->retail_piece_rate ?? 0.00,
            ]
        ];

        // Pass customers and rates to the view
        return view('pages.sales.index', compact('customers', 'rates'));
    }

    /**
     * Handle the sale confirmation and save the transaction.
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'rate_channel' => 'required|in:wholesale,retail', // 🚩 ADDED VALIDATION
            'cart_items' => 'required|array|min:1',
            'cart_items.*.category' => 'required|string|max:50',
            'cart_items.*.weight' => 'required|numeric|min:0.001',
            'cart_items.*.rate' => 'required|numeric|min:0',
            'total_payable' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // 1. Calculate total weight being sold in this transaction
            $total_weight_to_sell = 0.00;
            foreach ($validated['cart_items'] as $item) {
                $total_weight_to_sell += (float) $item['weight'];
            }

            // 2. Calculate current available stock (Total Purchases - Total Past Sales)
            // Note: This requires the SaleItem model to be defined with weight_kg field
            $total_purchased = Purchase::sum('net_live_weight');
            $total_sold_past = DB::table('sale_items')->sum('weight_kg'); // Using DB query for SaleItem sum
            $current_net_stock = $total_purchased - $total_sold_past;

            // 3. Stock Check: If selling more than available, block the sale.
            if ($total_weight_to_sell > $current_net_stock) {
                DB::rollBack();
                $available_display = number_format(max(0, $current_net_stock), 2);
                $selling_display = number_format($total_weight_to_sell, 2);

                return response()->json([
                    'message' => "Error: Insufficient stock. You are attempting to sell $selling_display KG, but only $available_display KG stock is available.",
                ], 400);
            }
            
            // --- Proceed with sale if stock is sufficient ---

            $customer = Customer::findOrFail($validated['customer_id']);

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'total_amount' => $validated['total_payable'],
                'payment_status' => 'credit', 
                'sale_channel' => $validated['rate_channel'], 
            ]);

            $saleItemsData = [];

            foreach ($validated['cart_items'] as $item) {
                $line_total = $item['weight'] * $item['rate'];

                // SaleItem model is assumed to be correctly defined
                $saleItemsData[] = new SaleItem([ 
                    'product_category' => $item['category'],
                    'weight_kg' => $item['weight'],
                    'rate_pkr' => $item['rate'],
                    'line_total' => $line_total,
                ]);
            }

            $sale->items()->saveMany($saleItemsData);

            // Update customer balance in DB
            $customer->current_balance += $sale->total_amount;
            $customer->save();

            DB::commit();

            // Return success response with updated balance
            // Stock will now be automatically reduced for subsequent transactions/rate checks 
            return response()->json([
                'message' => 'Sale confirmed and saved successfully. Stock updated.',
                'sale_id' => $sale->id,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'updated_balance' => number_format($customer->current_balance, 2, '.', ''), // Send the new balance
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Sales Transaction Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Transaction failed. Please try again. Error: ' . $e->getMessage()], 500);
        }
    }
}