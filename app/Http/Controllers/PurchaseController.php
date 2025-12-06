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
        // 1. Fetch Suppliers for the dropdown
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();

        // 2. Fetch Purchases for TODAY only
        $currentDate = Carbon::now()->toDateString();

        $purchases = Purchase::with('supplier:id,name')
            ->whereDate('created_at', $currentDate)
            ->latest()
            ->get()
            ->map(function ($purchase) {
                // We map this to ensure the view has easy access to formatted data 
                // AND raw data for the Edit button attributes.
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
        $validatedData = $this->validatePurchase($request);

        try {
            DB::beginTransaction();

            $purchase = Purchase::create($validatedData);
            
            // Load supplier for the response
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
     * Update the specified purchase (Used by Edit Mode).
     */
    public function update(Request $request, $id)
    {
        $validatedData = $this->validatePurchase($request);

        try {
            $purchase = Purchase::findOrFail($id);
            
            DB::beginTransaction();
            
            $purchase->update($validatedData);
            
            // Reload supplier in case it changed
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

    /**
     * Remove the specified purchase.
     */
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

    /**
     * Helper: Validate Request Data
     */
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
            // Calculated fields (Validated to ensure frontend logic matches backend)
            'net_live_weight' => 'required|numeric|min:0',
            'total_payable'   => 'required|numeric|min:0',
            'effective_cost'  => 'required|numeric|min:0',
        ]);
    }

    /**
     * Helper: Format data for JSON response (AJAX)
     */
    private function formatPurchaseForJson($purchase)
    {
        // This structure must match what your JS function renderPurchaseRow expects
        return [
            'id'              => $purchase->id,
            'created_at_human'=> $purchase->created_at->diffForHumans(), // For display
            'supplier_id'     => $purchase->supplier_id,                 // For Edit Logic
            'supplier_name'   => optional($purchase->supplier)->name ?? 'N/A',
            'driver_no'       => $purchase->driver_no,
            'gross_weight'    => (float)$purchase->gross_weight,         // For Edit Logic
            'dead_qty'        => (int)$purchase->dead_qty,               // For Edit Logic
            'dead_weight'     => (float)$purchase->dead_weight,          // For Edit Logic
            'shrink_loss'     => (float)$purchase->shrink_loss,          // For Edit Logic
            'net_live_weight' => (float)$purchase->net_live_weight,
            'buying_rate'     => (float)$purchase->buying_rate,
            'total_payable'   => (float)$purchase->total_payable,
            'effective_cost'  => (float)$purchase->effective_cost,
        ];
    }
}