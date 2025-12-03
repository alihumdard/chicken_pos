<?php
namespace App\Http\Controllers;

use App\Models\DailyRate;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Default rates structure (Initialized)
        $defaultData = [
            'base_effective_cost'           => 0.00,
            'manual_base_cost'              => 0.00, // 🟢 ADDED
            'net_stock_available'           => 0.00,
            'wholesale_rate'                => 0.00,
            'permanent_rate'                => 0.00,
            'retail_mix_rate'               => 0.00,
            'retail_chest_rate'             => 0.00,
            'retail_thigh_rate'             => 0.00,
            'retail_piece_rate'             => 0.00,

            // New fields for rates.index, initialized to 0.00
            'live_chicken_rate'             => 0.00,
            'wholesale_hotel_mix_rate'      => 0.00,
            'wholesale_hotel_chest_rate'    => 0.00,
            'wholesale_hotel_thigh_rate'    => 0.00,
            'wholesale_customer_piece_rate' => 0.00,

            'is_historical'                 => false,
        ];

        // 2. Try to fetch an existing rate for the target date
        $activeRate = DailyRate::whereDate('created_at', $targetDate)
            ->latest()
            ->first();

        if ($activeRate) {
            // Found a SAVED rate for the specific day (historical or today's activated rate)
            $defaultData = [
                'base_effective_cost'           => $activeRate->base_effective_cost,
                'manual_base_cost'              => $activeRate->manual_base_cost ?? 0.00, // 🟢 READ SAVED MANUAL COST
                'wholesale_rate'                => $activeRate->wholesale_rate,
                'permanent_rate'                => $activeRate->permanent_rate,
                'retail_mix_rate'               => $activeRate->retail_mix_rate,
                'retail_chest_rate'             => $activeRate->retail_chest_rate,
                'retail_thigh_rate'             => $activeRate->retail_thigh_rate,
                'retail_piece_rate'             => $activeRate->retail_piece_rate,
                
                'live_chicken_rate'             => $activeRate->live_chicken_rate ?? 0.00,
                'wholesale_hotel_mix_rate'      => $activeRate->wholesale_hotel_mix_rate ?? 0.00,
                'wholesale_hotel_chest_rate'    => $activeRate->wholesale_hotel_chest_rate ?? 0.00,
                'wholesale_hotel_thigh_rate'    => $activeRate->wholesale_hotel_thigh_rate ?? 0.00,
                'wholesale_customer_piece_rate' => $activeRate->wholesale_customer_piece_rate ?? 0.00,

                'is_historical'                 => true, // Marks this as a saved, uneditable view
            ];
            
            // Calculate current stock sum for display purposes
            $stockData = $this->calculateCombinedStock();
            $defaultData['net_stock_available'] = $stockData['net_stock'] ?? 0.00;

        } elseif (now()->toDateString() == $targetDate) {
            // 3. Live calculation: No rate saved for today. Use average cost and summed stock
            
            $combinedData = $this->calculateCombinedRatesAndStock();
            $baseCost     = $combinedData['average_effective_cost'];
            
            $defaultData['base_effective_cost'] = $baseCost;
            // The manual_base_cost stays 0.00 in $defaultData if this block runs
            $defaultData['net_stock_available'] = $combinedData['sum_net_stock'];

            // --- DYNAMIC DEFAULT RATE CALCULATION (Base Cost + Margin) ---
            $defaultData['wholesale_rate']          = $baseCost + 10.00;      
            $defaultData['live_chicken_rate']       = $baseCost + 20.00;      
            $defaultData['wholesale_hotel_mix_rate']      = $baseCost + 25.00;    
            $defaultData['wholesale_hotel_chest_rate']    = $baseCost + 125.00;   
            $defaultData['wholesale_hotel_thigh_rate']    = $baseCost + 75.00;    
            $defaultData['wholesale_customer_piece_rate'] = $baseCost;          
            $defaultData['retail_mix_rate']         = $baseCost + 50.00;      
            $defaultData['retail_chest_rate']       = $baseCost + 150.00;     
            $defaultData['retail_thigh_rate']       = $baseCost + 100.00;     
            $defaultData['retail_piece_rate']       = $baseCost - 10.00;      

        }

        return view('pages.rates.index', compact('suppliers', 'defaultData', 'targetDate'));
    }

    /**
     * Helper method to calculate the AVERAGE effective cost and SUMMED net stock across all suppliers.
     */
    private function calculateCombinedRatesAndStock(): array
    {
        $suppliers = Supplier::pluck('id');
        $totalEffectiveCost = 0.00;
        $supplierCount = 0;
        $totalNetStock = 0.00;
        
        foreach ($suppliers as $supplierId) {
            $latestPurchase = Purchase::where('supplier_id', $supplierId)
                                      ->latest('created_at')
                                      ->first();

            if ($latestPurchase) {
                $totalEffectiveCost += (float) $latestPurchase->effective_cost;
                $supplierCount++;
            }
        }
        
        $averageEffectiveCost = $supplierCount > 0 ? $totalEffectiveCost / $supplierCount : 0.00;
        $netStockAvailable = $this->calculateCombinedStock()['net_stock']; 

        return [
            'average_effective_cost' => $averageEffectiveCost,
            'sum_net_stock'          => $netStockAvailable,
        ];
    }
    
    /**
     * Helper method to calculate the summed net stock across all suppliers.
     */
    private function calculateCombinedStock(): array
    {
         $totalLiveWeight = Purchase::sum('net_live_weight');
         $netStockAvailable = max(0, $totalLiveWeight - 500.00); 

         return [
             'net_stock' => (float) $netStockAvailable,
         ];
    }
    
    /**
     * Handle AJAX request to get cost and stock data for a selected supplier.
     */
    public function getSupplierData(Request $request)
    {
        return response()->json([
            'base_effective_cost' => 0.00,
            'net_stock_available' => 0.00,
        ]);
    }

    /**
     * Store a new set of Daily Rates and activate them.
     */
    public function store(Request $request)
    {
        // 1. Validation 
        $data = $request->validate([
            'supplier_id'                   => ['nullable', 'exists:suppliers,id'],
            'base_effective_cost'           => ['required', 'numeric', 'min:0'],
            'manual_base_cost'              => ['nullable', 'numeric', 'min:0'], // 🟢 VALIDATE NEW FIELD
            'wholesale_rate'                => ['required', 'numeric', 'min:0'],
            'permanent_rate'                => ['required', 'numeric', 'min:0'], 
            'live_chicken_rate'             => ['required', 'numeric', 'min:0'], 
            'wholesale_hotel_mix_rate'      => ['required', 'numeric', 'min:0'], 
            'wholesale_hotel_chest_rate'    => ['required', 'numeric', 'min:0'], 
            'wholesale_hotel_thigh_rate'    => ['required', 'numeric', 'min:0'], 
            'wholesale_customer_piece_rate' => ['required', 'numeric', 'min:0'], 
            'retail_mix_rate'               => ['required', 'numeric', 'min:0'],
            'retail_chest_rate'             => ['required', 'numeric', 'min:0'],
            'retail_thigh_rate'             => ['required', 'numeric', 'min:0'],
            'retail_piece_rate'             => ['required', 'numeric', 'min:0'],
        ]);
        
        if (empty($data['supplier_id'])) {
             $data['supplier_id'] = Supplier::first()->id ?? 1;
        }
        
        // Ensure manual_base_cost is set to 0 if null/missing (based on nullable validation)
        $data['manual_base_cost'] = $data['manual_base_cost'] ?? 0.00;

        // 2. Deactivate previous rates saved TODAY only.
        DailyRate::whereDate('created_at', now()->toDateString())->update(['is_active' => false]);

        // 3. Create the new DailyRate record
        DailyRate::create(array_merge($data, ['is_active' => true]));

        // 4. Redirect with success and a date parameter to show the newly saved rate for today
        return redirect()->route('admin.rates.index', ['target_date' => now()->toDateString()])->with('success', 'New daily rates activated successfully!');
    }
}