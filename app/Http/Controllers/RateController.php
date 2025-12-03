<?php
namespace App\Http\Controllers;

use App\Models\DailyRate;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class RateController extends Controller
{
    // Define margins centrally for clean calculation
    private const RATE_MARGINS = [
        'wholesale_rate'                => 10.00, // Margin is set to 20.00
        'live_chicken_rate'             => 20.00,
        'wholesale_hotel_mix_rate'      => 25.00,
        'wholesale_hotel_chest_rate'    => 125.00,
        'wholesale_hotel_thigh_rate'    => 75.00,
        'wholesale_customer_piece_rate' => 0.00, // No Margin
        'retail_mix_rate'               => 50.00,
        'retail_chest_rate'             => 150.00,
        'retail_thigh_rate'             => 100.00,
        'retail_piece_rate'             => -10.00, // Loss/Negative Margin
    ];
    
    /**
     * Display the daily rates overview, checking for a specific date or the active rate.
     */
    public function index(Request $request)
    {
        try {
            // ... (rest of the index logic remains the same)
            $targetDate = $request->input('target_date', now()->toDateString());
            $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
            $defaultData = [
                'base_effective_cost'           => 0.00,
                'manual_base_cost'              => 0.00, 
                'net_stock_available'           => 0.00,
                'wholesale_rate'                => 0.00,
                'permanent_rate'                => 0.00, 
                'retail_mix_rate'               => 0.00,
                'retail_chest_rate'             => 0.00,
                'retail_thigh_rate'             => 0.00,
                'retail_piece_rate'             => 0.00,
                'live_chicken_rate'             => 0.00,
                'wholesale_hotel_mix_rate'      => 0.00,
                'wholesale_hotel_chest_rate'    => 0.00,
                'wholesale_hotel_thigh_rate'    => 0.00,
                'wholesale_customer_piece_rate' => 0.00,
                'is_historical'                 => false,
            ];


            $activeRate = DailyRate::whereDate('created_at', $targetDate)
                ->latest()
                ->first();

            if ($activeRate) {
                $savedManualCost = (float)($activeRate->manual_base_cost ?? 0.00);
                $baseForCalculation = $savedManualCost > 0 ? $savedManualCost : (float)$activeRate->base_effective_cost;

                $defaultData['base_effective_cost'] = (float)$activeRate->base_effective_cost;
                $defaultData['manual_base_cost'] = $savedManualCost; 
                
                // Recalculate all individual rates based on the saved base cost.
                $defaultData['wholesale_rate']                = $baseForCalculation + self::RATE_MARGINS['wholesale_rate'];
                $defaultData['live_chicken_rate']             = $baseForCalculation + self::RATE_MARGINS['live_chicken_rate'];
                $defaultData['wholesale_hotel_mix_rate']      = $baseForCalculation + self::RATE_MARGINS['wholesale_hotel_mix_rate'];
                $defaultData['wholesale_hotel_chest_rate']    = $baseForCalculation + self::RATE_MARGINS['wholesale_hotel_chest_rate'];
                $defaultData['wholesale_hotel_thigh_rate']    = $baseForCalculation + self::RATE_MARGINS['wholesale_hotel_thigh_rate'];
                $defaultData['wholesale_customer_piece_rate'] = $baseForCalculation + self::RATE_MARGINS['wholesale_customer_piece_rate'];
                $defaultData['retail_mix_rate']               = $baseForCalculation + self::RATE_MARGINS['retail_mix_rate'];
                $defaultData['retail_chest_rate']             = $baseForCalculation + self::RATE_MARGINS['retail_chest_rate'];
                $defaultData['retail_thigh_rate']             = $baseForCalculation + self::RATE_MARGINS['retail_thigh_rate'];
                $defaultData['retail_piece_rate']             = $baseForCalculation + self::RATE_MARGINS['retail_piece_rate'];
                $defaultData['permanent_rate'] = $activeRate->permanent_rate ?? 0.00;

                $defaultData['is_historical'] = now()->toDateString() != $targetDate;
                
                $stockData = $this->calculateCombinedStock();
                $defaultData['net_stock_available'] = $stockData['net_stock'] ?? 0.00;

            } elseif (now()->toDateString() == $targetDate) {
                
                $combinedData = $this->calculateCombinedRatesAndStock();
                $baseCost     = $combinedData['average_effective_cost'];
                
                $defaultData['base_effective_cost'] = $baseCost;
                $defaultData['net_stock_available'] = $combinedData['sum_net_stock'];

                // DYNAMIC DEFAULT RATE CALCULATION (Base Cost + Margin)
                $defaultData['wholesale_rate']          = $baseCost + self::RATE_MARGINS['wholesale_rate'];
                $defaultData['live_chicken_rate']       = $baseCost + self::RATE_MARGINS['live_chicken_rate'];
                $defaultData['wholesale_hotel_mix_rate']      = $baseCost + self::RATE_MARGINS['wholesale_hotel_mix_rate'];
                $defaultData['wholesale_hotel_chest_rate']    = $baseCost + self::RATE_MARGINS['wholesale_hotel_chest_rate'];
                $defaultData['wholesale_hotel_thigh_rate']    = $baseCost + self::RATE_MARGINS['wholesale_hotel_thigh_rate'];
                $defaultData['wholesale_customer_piece_rate'] = $baseCost + self::RATE_MARGINS['wholesale_customer_piece_rate'];
                $defaultData['retail_mix_rate']         = $baseCost + self::RATE_MARGINS['retail_mix_rate'];
                $defaultData['retail_chest_rate']       = $baseCost + self::RATE_MARGINS['retail_chest_rate'];
                $defaultData['retail_thigh_rate']       = $baseCost + self::RATE_MARGINS['retail_thigh_rate'];
                $defaultData['retail_piece_rate']       = $baseCost + self::RATE_MARGINS['retail_piece_rate'];      

            }

            return view('pages.rates.index', compact('suppliers', 'defaultData', 'targetDate'));
            
        } catch (Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading daily rates data. Please check the database configuration: ' . $e->getMessage());
        }
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
            'manual_base_cost'              => ['nullable', 'numeric', 'min:0'], 
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
        
        $data['manual_base_cost'] = $data['manual_base_cost'] ?? 0.00;

        try {
            // 2. Deactivate previous rates saved TODAY only.
            DailyRate::whereDate('created_at', now()->toDateString())->update(['is_active' => false]);

            // 3. Create the new DailyRate record
            DailyRate::create(array_merge($data, ['is_active' => true]));
            
            // 🟢 Handle AJAX request (from the Override button)
            if ($request->ajax() || $request->wantsJson()) {
                
                // Determine the base cost for recalculating all rates for the display
                $baseCost = (float)($data['manual_base_cost'] ?? $data['base_effective_cost']);

                $updatedRates = [];
                // Recalculate and format all rates based on the saved cost for the frontend update
                foreach (self::RATE_MARGINS as $key => $margin) {
                    $updatedRates[$key] = number_format($baseCost + $margin, 2, '.', '');
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Rates overridden and saved successfully (via AJAX).',
                    'base_effective_cost' => number_format($baseCost, 2, '.', ''),
                    'rates' => $updatedRates,
                ]);
            }

            // 4. Default action (from Activate Today's Rates button)
            return redirect()->route('admin.rates.index', ['target_date' => now()->toDateString()])->with('success', 'New daily rates activated and saved successfully!');

        } catch (Exception $e) {
            // 🛑 FIX: Returning the detailed exception message for debugging
            $errorMessage = 'Database error: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                 return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            return back()->withInput()->with('error', $errorMessage);
        }
    }
    
    // ... (Helper methods remain unchanged)
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
    
    private function calculateCombinedStock(): array
    {
         $totalLiveWeight = Purchase::sum('net_live_weight');
         $netStockAvailable = max(0, $totalLiveWeight - 500.00); 

         return [
             'net_stock' => (float) $netStockAvailable,
         ];
    }
    
    public function getSupplierData(Request $request)
    {
        return response()->json([
            'base_effective_cost' => 0.00,
            'net_stock_available' => 0.00,
        ]);
    }
}