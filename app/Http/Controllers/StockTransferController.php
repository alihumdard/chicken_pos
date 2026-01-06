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
        // 1. Validation
        $request->validate([
            'shop_id'      => 'required|exists:shops,id', // Source (From)
            'to_shop_id'   => 'required|exists:shops,id|different:shop_id', // 🟢 CHECK 1: Prevent same shop
            'weight'       => 'required|numeric|min:0.01',
            'rate'         => 'required|numeric|min:0',
            'customer_id'  => 'nullable|exists:customers,id',
            'formula_key'  => 'nullable|string',
            'reason'       => 'nullable|string'
        ]);

        // 🟢 CHECK 2: Validate Stock Availability
        $sourceShop = \App\Models\Shop::findOrFail($request->shop_id);
        
        // Ensure we don't transfer more than what is available
        if ($sourceShop->current_stock < $request->weight) {
            return back()
                ->withInput()
                ->withErrors(['weight' => "Error: Insufficient stock in {$sourceShop->name}. Available: " . number_format($sourceShop->current_stock, 2) . " KG"]);
        }

        DB::transaction(function () use ($request) {

            // 1. Create the Stock Transfer FIRST
            $transfer = \App\Models\StockTransfer::create([
                'from_shop_id' => $request->shop_id,
                'to_shop_id'   => $request->to_shop_id,
                'weight'       => $request->weight,
                'date'         => now(),
                'description'  => "Transfer for Issue: " . ($request->reason ?? 'Manual'),
            ]);

            // 2. Create the Sale and link it to the Transfer
            $totalAmount = $request->weight * $request->rate;

            $sale = \App\Models\Sale::create([
                'shop_id'           => $request->to_shop_id,
                'customer_id'       => $request->customer_id,
                'stock_transfer_id' => $transfer->id, // Linked
                'total_amount'      => $totalAmount,
                'note'              => $request->reason ?? 'Stock Issue via Transfer',
            ]);

            // 3. Create Sale Items
            \App\Models\SaleItem::create([
                'sale_id'          => $sale->id,
                'product_category' => $request->formula_key ?? 'Issue',
                'weight_kg'        => $request->weight,
                'rate_pkr'         => $request->rate,
                'line_total'       => $totalAmount
            ]);

            // 4. Ledger Transaction
            if ($request->customer_id) {
                DB::table('transactions')->insert([
                    'shop_id'     => $request->to_shop_id,
                    'customer_id' => $request->customer_id,
                    'date'        => now(),
                    'type'        => 'sale',
                    'description' => "Stock Issue #{$sale->id}",
                    'debit'       => $totalAmount,
                    'credit'      => 0,
                    'balance'     => 0, // Recalculated by helpers usually
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
        });

        return back()->with('success', 'Stock Adjustment Created Successfully.');
    }
    
}
