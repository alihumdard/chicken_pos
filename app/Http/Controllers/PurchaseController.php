<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
                $purchase->supplier_name    = $purchase->supplier->name ?? 'N/A';
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

    $request->validate([
        'cash_received' => 'nullable|numeric|min:0', 
    ]);

    try {
        DB::beginTransaction();

        $purchaseData = collect($validatedData)->except(['cash_paid'])->map(function ($value, $key) {
            if (in_array($key, ['dead_qty', 'dead_weight', 'shrink_loss', 'total_kharch']) && ($value === null || $value === '')) {
                return 0;
            }
            return $value;
        })->toArray();

        $purchase = Purchase::create($purchaseData);
        $supplier     = Supplier::findOrFail($request->supplier_id);
        $totalPayable = (float) $request->total_payable;
        $cashPaid     = (float) ($request->cash_paid ?? $request->cash_received ?? 0);

        $netEffect = $totalPayable - $cashPaid;
        $supplier->current_balance += $netEffect;
        $supplier->save();

        DB::table('transactions')->insert([
            'supplier_id'     => $supplier->id,
            'date'            => now(),
            'type'            => 'purchase',
            'description'     => "Purchase #{$purchase->id}" . ($purchase->driver_no ? " ({$purchase->driver_no})" : ""),
            'gross_weight'    => $request->gross_weight,
            'dead_weight'     => $request->dead_weight,
            'shrink_loss'     => $request->shrink_loss,
            'net_live_weight' => $request->net_live_weight,
            'total_kharch'    => $request->total_kharch,
            'buying_rate'     => $request->rate,
            'debit'           => $cashPaid,     // Cash paid moke par (DEBIT)
            'credit'          => $totalPayable, // Total Bill (CREDIT)
            'balance'         => $supplier->current_balance,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $purchase->load('supplier:id,name');
        DB::commit();

        return response()->json([
            'message'  => 'Purchase saved successfully in single row!',
            'purchase' => $this->formatPurchaseForJson($purchase),
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
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
            $updateData = collect($validatedData)->map(function ($value, $key) {
                if (in_array($key, ['dead_qty', 'dead_weight', 'shrink_loss', 'total_kharch']) && ($value === null || $value === '')) {
                    return 0;
                }
                return $value;
            })->toArray();

            $purchase->update($updateData);
            $purchase->load('supplier:id,name');

            DB::commit();

            return response()->json([
                'message'  => 'Purchase updated successfully!',
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
            'shop_id'         => 'required|exists:shops,id'
        ]);
    }

    private function formatPurchaseForJson($purchase)
    {
        return [
            'id'               => $purchase->id,
            'created_at_human' => $purchase->created_at->diffForHumans(),
            'supplier_id'      => $purchase->supplier_id,
            'supplier_name'    => optional($purchase->supplier)->name ?? 'N/A',
            'driver_no'        => $purchase->driver_no,
            'gross_weight'     => (float) $purchase->gross_weight,
            'dead_qty'         => (int) $purchase->dead_qty,
            'dead_weight'      => (float) $purchase->dead_weight,
            'shrink_loss'      => (float) $purchase->shrink_loss,
            'net_live_weight'  => (float) $purchase->net_live_weight,
            'buying_rate'      => (float) $purchase->buying_rate,
            'total_kharch'     => (float) $purchase->total_kharch,
            'total_payable'    => (float) $purchase->total_payable,
            'effective_cost'   => (float) $purchase->effective_cost,
        ];
    }
}
