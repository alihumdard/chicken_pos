<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Purchase; 
use App\Models\DailyRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Validation\Rule;

class RateController extends Controller
{
    /**
     * Display the daily rates overview, checking for a specific date or the active rate.
     */
    public function index(Request $request)
    {
        // Get the requested date, default to today
        $targetDate = $request->input('target_date', now()->toDateString());

        // 1. Fetch all suppliers for the dropdown
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        // Default rates structure
        $defaultData = [
            'base_effective_cost' => 0.00,
            'net_stock_available' => 0.00,
            'wholesale_rate' => 0.00,
            'permanent_rate' => 0.00,
            'retail_mix_rate' => 0.00,
            'retail_chest_rate' => 0.00,
            'retail_thigh_rate' => 0.00,
            'retail_piece_rate' => 0.00,
            'is_historical' => false,
        ];

        // 2. Try to fetch an existing rate for the target date
        $historicalRate = DailyRate::whereDate('created_at', $targetDate)
            ->latest() // Get the most recently activated rate for that day
            ->first();

        if ($historicalRate) {
            // Found a rate for the specific day (historical or today's activated rate)
            $defaultData = [
                'base_effective_cost' => $historicalRate->base_effective_cost,
                'wholesale_rate' => $historicalRate->wholesale_rate,
                'permanent_rate' => $historicalRate->permanent_rate,
                'retail_mix_rate' => $historicalRate->retail_mix_rate,
                'retail_chest_rate' => $historicalRate->retail_chest_rate,
                'retail_thigh_rate' => $historicalRate->retail_thigh_rate,
                'retail_piece_rate' => $historicalRate->retail_piece_rate,
                'is_historical' => true,
            ];
            
            // Fetch Net Stock available using the supplier linked to the historical rate
            if ($historicalRate->supplier_id) {
                 $data = $this->calculateSupplierData($historicalRate->supplier_id);
                 $defaultData['net_stock_available'] = $data['net_stock'] ?? 0.00;
            } else {
                 $defaultData['net_stock_available'] = 0.00;
            }

        } elseif ($suppliers->isNotEmpty() && now()->toDateString() == $targetDate) {
            // 3. Fallback to live calculation for the current day if no rate is set yet
            $defaultSupplierId = $suppliers->first()->id;
            $data = $this->calculateSupplierData($defaultSupplierId);
            
            $defaultData['base_effective_cost'] = $data['effective_cost'] ?? 0.00;
            $defaultData['net_stock_available'] = $data['net_stock'] ?? 0.00;
        }

        return view('pages.rates.index', compact('suppliers', 'defaultData', 'targetDate'));
    }

    /**
     * Handle AJAX request to get cost and stock data for a selected supplier.
     */
    public function getSupplierData(Request $request)
    {
        $supplierId = $request->input('supplier_id');

        if (!$supplierId) {
            return response()->json(['error' => 'Supplier ID is required.'], 400);
        }

        $data = $this->calculateSupplierData($supplierId);

        if ($data) {
            return response()->json([
                'base_effective_cost' => $data['effective_cost'], 
                'net_stock_available' => $data['net_stock'],
            ]);
        }

        return response()->json([
            'base_effective_cost' => 0.00,
            'net_stock_available' => 0.00,
        ]);
    }

    /**
     * Helper method to calculate the effective cost and net stock for a supplier.
     */
    private function calculateSupplierData(int $supplierId): array
    {
        // 1. Get the latest effective cost (from the most recent purchase)
        $latestPurchase = Purchase::where('supplier_id', $supplierId)
                                  ->latest('created_at')
                                  ->first();

        $effectiveCost = $latestPurchase ? $latestPurchase->effective_cost : 0.00;

        // 2. Calculate Net Stock Available (Placeholder Logic)
        $totalLiveWeight = Purchase::where('supplier_id', $supplierId)
                                   ->sum('net_live_weight');
        
        $netStockAvailable = max(0, $totalLiveWeight - 500.00); 

        return [
            'effective_cost' => (float) $effectiveCost,
            'net_stock' => (float) $netStockAvailable,
        ];
    }
    
    /**
     * Store a new set of Daily Rates and activate them.
     */
    public function store(Request $request)
    {
        // 1. Validation
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'base_effective_cost' => ['required', 'numeric', 'min:0'],
            'wholesale_rate' => ['required', 'numeric', 'min:0'],
            'permanent_rate' => ['required', 'numeric', 'min:0'],
            'retail_mix_rate' => ['required', 'numeric', 'min:0'],
            'retail_chest_rate' => ['required', 'numeric', 'min:0'],
            'retail_thigh_rate' => ['required', 'numeric', 'min:0'],
            'retail_piece_rate' => ['required', 'numeric', 'min:0'],
        ]);

        // 2. Deactivate previous rates saved TODAY only.
        // We only deactivate rates created on the same day to allow viewing historical rates
        DailyRate::whereDate('created_at', now()->toDateString())->update(['is_active' => false]);

        // 3. Create the new DailyRate record
        DailyRate::create(array_merge($data, ['is_active' => true]));

        // 4. Redirect with success and a date parameter to show the newly saved rate for today
        return redirect()->route('admin.rates.index', ['target_date' => now()->toDateString()])->with('success', 'New daily rates activated successfully!');
    }

    // ... other methods
}