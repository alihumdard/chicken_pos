<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale; // Import the Sale Model
use App\Models\SaleItem; // Import the SaleItem Model
use App\Models\DailyRate; // Import the DailyRate Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For database transaction

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
            $customer = Customer::findOrFail($validated['customer_id']);

            $sale = Sale::create([
                'customer_id' => $customer->id,
                'total_amount' => $validated['total_payable'],
                'payment_status' => 'credit', 
                'sale_channel' => $validated['rate_channel'], // 🚩 SAVING THE SELECTED CHANNEL
            ]);

            $total_check = 0;
            $saleItemsData = [];

            foreach ($validated['cart_items'] as $item) {
                $line_total = $item['weight'] * $item['rate'];
                $total_check += $line_total;

                $saleItemsData[] = new SaleItem([
                    'product_category' => $item['category'],
                    'weight_kg' => $item['weight'],
                    'rate_pkr' => $item['rate'],
                    'line_total' => $line_total,
                ]);
            }

            $sale->items()->saveMany($saleItemsData);

            $customer->current_balance += $sale->total_amount;
            $customer->save();

            DB::commit();

            return response()->json([
                'message' => 'Sale confirmed and saved successfully.',
                'sale_id' => $sale->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Sales Transaction Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Transaction failed. Please try again.'], 500);
        }
    }
}