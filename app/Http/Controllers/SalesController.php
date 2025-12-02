<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale; // Import the Sale Model
use App\Models\SaleItem; // Import the SaleItem Model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // For database transaction

class SalesController extends Controller
{
    /**
     * Display a listing of customers and the sales interface.
     */
    public function index()
    {
        // Fetch all customers, ordered by name, for the customer selection list.
        $customers = Customer::orderBy('name')->get();

        // Pass the fetched customers to the view
        return view('pages.sales.index', compact('customers'));
    }

    /**
     * Handle the sale confirmation and save the transaction.
     */
    public function store(Request $request)
    {
        // 1. Validation (Basic Example)
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'cart_items' => 'required|array|min:1',
            'cart_items.*.category' => 'required|string|max:50',
            'cart_items.*.weight' => 'required|numeric|min:0.001',
            'cart_items.*.rate' => 'required|numeric|min:0',
            'total_payable' => 'required|numeric|min:0',
        ]);

        // Use a database transaction to ensure data consistency
        DB::beginTransaction();

        try {
            $customer = Customer::findOrFail($validated['customer_id']);

            // 2. Create the Sale record
            $sale = Sale::create([
                'customer_id' => $customer->id,
                'total_amount' => $validated['total_payable'],
                'payment_status' => 'credit', // Assuming all POS sales start as Credit/Unpaid
            ]);

            // 3. Add Sale Items and calculate total for double-check
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

            // Save all items at once
            $sale->items()->saveMany($saleItemsData);

            // 4. Update Customer Balance (Since this is a Credit Sale)
            $customer->current_balance += $sale->total_amount;
            $customer->save();

            DB::commit();

            // Return a success message (you might redirect in a real app)
            return response()->json([
                'message' => 'Sale confirmed and saved successfully.',
                'sale_id' => $sale->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error and return a detailed message
            \Log::error('Sales Transaction Failed: ' . $e->getMessage());
            return response()->json(['message' => 'Transaction failed. Please try again.'], 500);
        }
    }
}