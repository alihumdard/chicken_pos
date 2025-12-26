<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display the form and list previous purchases.
     */
    public function index()
    {
        $suppliers = Supplier::select('id', 'name', 'current_balance')->orderBy('name')->get();

        $currentDate = Carbon::now()->toDateString();

        $purchases = Purchase::with('supplier:id,name')
            ->whereDate('created_at', $currentDate)
            ->latest()
            ->get()
            ->map(function ($purchase) {
                $purchase->supplier_name = $purchase->supplier->name ?? 'N/A';
                $purchase->created_at_human = $purchase->created_at->diffForHumans();
                return $purchase;
            });

        return view('pages.purchases.index', compact('suppliers', 'purchases'));
    }

    /**
     * Store a newly created purchase.
     */
    public function store(Request $request)
    {
        // 1. Validate
        $validatedData = $this->validatePurchase($request);
        
        // Add specific validation for cash_paid
        $request->validate([
            'cash_paid' => 'nullable|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // 2. Create Purchase Record
            // Exclude cash_paid from Purchase model creation if it's not in the table
            $purchaseData = collect($validatedData)->except(['cash_paid'])->toArray();
            $purchase = Purchase::create($purchaseData);
            
            $supplier = Supplier::findOrFail($request->supplier_id);
            $totalPayable = $request->total_payable;
            $cashPaid = $request->input('cash_paid', 0);

            // 3. Update Supplier Balance & Ledger (Purchase Entry)
            // Logic: We bought goods, so we owe the supplier (Credit Increase)
            $supplier->current_balance += $totalPayable;
            $supplier->save();

            DB::table('transactions')->insert([
                'supplier_id' => $supplier->id,
                'date' => now(),
                'type' => 'purchase', // Matches your ledger logic
                'description' => "Purchase #{$purchase->id} (Driver: {$purchase->driver_no})",
                'debit' => 0,
                'credit' => $totalPayable,
                'balance' => $supplier->current_balance,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Update Supplier Balance & Ledger (Payment Entry)
            // Logic: We paid cash, so we owe less (Debit Increase)
            if ($cashPaid > 0) {
                $supplier->current_balance -= $cashPaid;
                $supplier->save();

                DB::table('transactions')->insert([
                    'supplier_id' => $supplier->id,
                    'date' => now(),
                    'type' => 'payment', // Matches your ledger logic
                    'description' => "Cash Paid for Purchase #{$purchase->id}",
                    'debit' => $cashPaid,
                    'credit' => 0,
                    'balance' => $supplier->current_balance,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $purchase->load('supplier:id,name');

            DB::commit();

            return response()->json([
                'message' => 'Purchase saved successfully!',
                'purchase' => $this->formatPurchaseForJson($purchase),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error saving purchase: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified purchase.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $this->validatePurchase($request);

        try {
            $purchase = Purchase::findOrFail($id);
            
            DB::beginTransaction();
            
            // Note: Editing accounting entries is complex. 
            // Currently, this updates the Purchase record but does NOT auto-adjust 
            // the historical ledger to avoid data corruption.
            
            $purchase->update($validatedData);
            $purchase->load('supplier:id,name');

            DB::commit();

            return response()->json([
                'message' => 'Purchase updated successfully!',
                'purchase' => $this->formatPurchaseForJson($purchase),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating purchase: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $purchase->delete();

            return response()->json(['message' => 'Purchase deleted successfully!'], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete purchase.'], 500);
        }
    }

    private function validatePurchase(Request $request)
    {
        return $request->validate([
            'supplier_id'     => 'required|numeric|exists:suppliers,id',
            'driver_no'       => 'nullable|string|max:50',
            'gross_weight'    => 'required|numeric|min:0',
            'dead_qty'        => 'nullable|integer|min:0',
            'dead_weight'     => 'nullable|numeric|min:0',
            'shrink_loss'     => 'nullable|numeric|min:0',
            'buying_rate'     => 'required|numeric|min:0',
            'total_kharch'    => 'nullable|numeric|min:0',
            'net_live_weight' => 'required|numeric|min:0',
            'total_payable'   => 'required|numeric|min:0',
            'effective_cost'  => 'required|numeric|min:0',
        ]);
    }

    private function formatPurchaseForJson($purchase)
    {
        return [
            'id'              => $purchase->id,
            'created_at_human'=> $purchase->created_at->diffForHumans(),
            'supplier_id'     => $purchase->supplier_id,
            'supplier_name'   => optional($purchase->supplier)->name ?? 'N/A',
            'driver_no'       => $purchase->driver_no,
            'gross_weight'    => (float)$purchase->gross_weight,
            'dead_qty'        => (int)$purchase->dead_qty,
            'dead_weight'     => (float)$purchase->dead_weight,
            'shrink_loss'     => (float)$purchase->shrink_loss,
            'net_live_weight' => (float)$purchase->net_live_weight,
            'buying_rate'     => (float)$purchase->buying_rate,
            'total_kharch'     => (float)$purchase->total_kharch,
            'total_payable'   => (float)$purchase->total_payable,
            'effective_cost'  => (float)$purchase->effective_cost,
        ];
    }
}