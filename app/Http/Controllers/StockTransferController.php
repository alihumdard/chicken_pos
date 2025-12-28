<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shop;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request) {}

    public function store(Request $request)
    {
        $request->validate([
            'from_shop_id' => 'required',
            'to_shop_id' => 'required|different:from_shop_id',
            'weight' => 'required|numeric'
        ]);

        // Check availability
        $shop = Shop::find($request->from_shop_id);
        if ($shop->current_stock < $request->weight) {
            return back()->withErrors(['msg' => 'Not enough stock!']);
        }

        StockTransfer::create($request->all());
        return back()->with('success', 'Transfer complete');
    }

    public function create(Request $request) {}

    public function destroy(Request $request) {}

public function storeAdjustment(Request $request)
{
    // 1. Validation (Cleaned up to allow simultaneous Transfer + Sale)
    $request->validate([
        'shop_id'      => 'required|exists:shops,id', // Source (From)
        'to_shop_id'   => 'required|exists:shops,id|different:shop_id', // Destination (To)
        'weight'       => 'required|numeric|min:0.01',
        'rate'         => 'required|numeric|min:0',
        'customer_id'  => 'nullable|exists:customers,id', // Optional Customer
        'formula_key'  => 'nullable|string',
        'reason'       => 'nullable|string'
    ]);

    DB::transaction(function () use ($request) {
        

        // 🟢 STEP 2: GENERATE SALE (Deduct from Destination)
        // This ensures the stock is recorded as 'Sold' from the destination shop.
        $totalAmount = $request->weight * $request->rate;

        $sale = \App\Models\Sale::create([
            'shop_id'      => $request->to_shop_id, // Sale happens at the DESTINATION shop
            'customer_id'  => $request->customer_id, 
            'total_amount' => $totalAmount,
            'note'         => $request->reason ?? 'Stock Issue via Transfer',
        ]);

        // 🟢 STEP 3: GENERATE SALE ITEMS
        \App\Models\SaleItem::create([
            'sale_id'          => $sale->id,
            'product_category' => $request->formula_key ?? 'Issue',
            'weight_kg'        => $request->weight,
            'rate_pkr'         => $request->rate,
            'line_total'       => $totalAmount
        ]);

        // 🟢 STEP 4: GENERATE LEDGER TRANSACTION (If Customer Selected)
        if ($request->customer_id) {
            DB::table('transactions')->insert([
                'shop_id'     => $request->to_shop_id,
                'customer_id' => $request->customer_id,
                'date'        => now(),
                'type'        => 'sale',
                'description' => "Stock Issue #{$sale->id}",
                'debit'       => $totalAmount,
                'credit'      => 0,
                'balance'     => 0, // Helper recalculates this later
                'created_at'  => now(),
                'updated_at'  => now()
            ]);
            
            \App\Models\StockTransfer::create([
                'from_shop_id' => $request->shop_id,
                'to_shop_id'   => $request->to_shop_id,
                'weight'       => $request->weight,
                'date'         => now(),
                'description'  => "Transfer for Issue: " . ($request->reason ?? 'Manual'),
            ]);
            // Optional: Call your recalculateBalance helper here if available
            // $this->recalculateBalance($request->customer_id, null);
        }
    });

    return back()->with('success', 'Stock Transfer and Sale generated successfully.');
}

}
