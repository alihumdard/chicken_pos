<?php
namespace App\Http\Controllers;

use App\Models\DailyRate;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SaleItem;
use App\Models\RateFormula;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Added for logging in try/catch
use Exception;

class RateController extends Controller
{
    // 🟢 RESTORING ORIGINAL FIXED MARGINS (This ensures the base rates behave as they did originally)
    private const RATE_MARGINS = [
        'wholesale_rate'                => 10.00, 
        'live_chicken_rate'             => 20.00,
        'wholesale_hotel_mix_rate'      => 25.00,
        'wholesale_hotel_chest_rate'    => 125.00,
        'wholesale_hotel_thigh_rate'    => 75.00,
        'wholesale_customer_piece_rate' => 0.00,
        'retail_mix_rate'               => 50.00,
        'retail_chest_rate'             => 150.00,
        'retail_thigh_rate'             => 100.00,
        'retail_piece_rate'             => -10.00,
        // 🟢 NEW: Key for Purchase Effective Cost
        'purchase_effective_cost'       => 0.00, 
    ];
    
    // 🟢 Define friendly names for the settings page dropdown
    private const RATE_FRIENDLY_NAMES = [
        'wholesale_rate'                => 'Wholesale Live',
        'live_chicken_rate'             => 'Retail Live Chicken',
        'wholesale_hotel_mix_rate'      => 'Wholesale Hotel Mix',
        'wholesale_hotel_chest_rate'    => 'Wholesale Hotel Chest',
        'wholesale_hotel_thigh_rate'    => 'Wholesale Hotel Thigh',
        'wholesale_customer_piece_rate' => 'Wholesale Customer Piece',
        'retail_mix_rate'               => 'Retail Mix',
        'retail_chest_rate'             => 'Retail Chest',
        'retail_thigh_rate'             => 'Retail Thigh',
        'retail_piece_rate'             => 'Retail Piece',
        'purchase_effective_cost'       => 'Purchase Effective Cost', 
    ];
    
    /**
     * Display the daily rates overview, checking for a specific date or the active rate.
     */
    public function index(Request $request)
    {
        try {
            $targetDate = $request->input('target_date', now()->toDateString());
            $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
            // Fetch all active formulas once
            $rateFormulas = RateFormula::all()->keyBy('rate_key');

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
                
                // Load SAVED rates from the database record instead of recalculating
                $baseForCalculation = $savedManualCost > 0 ? $savedManualCost : (float)$activeRate->base_effective_cost;

                $defaultData['base_effective_cost'] = (float)$activeRate->base_effective_cost;
                $defaultData['manual_base_cost'] = $savedManualCost; 
                
                // Load ALL individual rates directly from the saved activeRate object
                $defaultData['wholesale_rate']                = (float)$activeRate->wholesale_rate;
                $defaultData['live_chicken_rate']             = (float)$activeRate->live_chicken_rate;
                $defaultData['wholesale_hotel_mix_rate']      = (float)$activeRate->wholesale_hotel_mix_rate;
                $defaultData['wholesale_hotel_chest_rate']    = (float)$activeRate->wholesale_hotel_chest_rate;
                $defaultData['wholesale_hotel_thigh_rate']    = (float)$activeRate->wholesale_hotel_thigh_rate;
                $defaultData['wholesale_customer_piece_rate'] = (float)$activeRate->wholesale_customer_piece_rate;
                $defaultData['retail_mix_rate']               = (float)$activeRate->retail_mix_rate;
                $defaultData['retail_chest_rate']             = (float)$activeRate->retail_chest_rate;
                $defaultData['retail_thigh_rate']             = (float)$activeRate->retail_thigh_rate;
                $defaultData['retail_piece_rate']             = (float)$activeRate->retail_piece_rate;
                $defaultData['permanent_rate']                = (float)$activeRate->permanent_rate;

                $defaultData['is_historical'] = now()->toDateString() != $targetDate;
                
                $stockData = $this->calculateCombinedStock();
                $defaultData['net_stock_available'] = $stockData['net_stock'] ?? 0.00;

            } elseif (now()->toDateString() == $targetDate) {
                
                // Default live calculation logic 
                $combinedData = $this->calculateCombinedRatesAndStock();
                $baseCost     = $combinedData['average_effective_cost'];
                
                $defaultData['base_effective_cost'] = $baseCost;
                $defaultData['net_stock_available'] = $combinedData['sum_net_stock'];

                // DYNAMIC DEFAULT RATE CALCULATION (Base Cost + Fixed Margin + Formula)
                foreach (self::RATE_MARGINS as $key => $margin) {
                    $baseRate = $baseCost + $margin; 
                    if ($key !== 'purchase_effective_cost') {
                        $defaultData[$key] = $this->applyFormula($baseRate, $rateFormulas->get($key));
                    }
                }
                
                // Permanent rate also starts at Base Cost + 0 margin
                $baseRate = $baseCost + 0.00;
                $defaultData['permanent_rate'] = $this->applyFormula($baseRate, $rateFormulas->get('permanent_rate'));
            }

            // Pass formulas and friendly names to the view
            return view('pages.rates.index', compact('suppliers', 'defaultData', 'targetDate', 'rateFormulas'));
            
        } catch (Exception $e) {
            return redirect()->route('admin.dashboard')->with('error', 'Error loading daily rates data. Please check the database configuration: ' . $e->getMessage());
        }
    }

    /**
     * Store a new set of Daily Rates and activate them. 
     */
    public function store(Request $request)
    {
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
            
            // Handle AJAX request (from the Override button)
            if ($request->ajax() || $request->wantsJson()) {
                
                // Determine the base cost for recalculating all rates for the display
                $baseCost = (float)($data['manual_base_cost'] ?? $data['base_effective_cost']);
                // Fetch active formulas for recalculation
                $rateFormulas = RateFormula::all()->keyBy('rate_key');

                $updatedRates = [];
                // Recalculate and format all rates based on the saved cost for the frontend update
                foreach (self::RATE_MARGINS as $key => $margin) {
                    // Only calculate for keys that are displayed on the Rates page
                    if ($key !== 'purchase_effective_cost') {
                        $baseRate = $baseCost + $margin; 
                        // Apply formula logic here
                        $updatedRates[$key] = number_format($this->applyFormula($baseRate, $rateFormulas->get($key)), 2, '.', '');
                    }
                }
                
                // Also handle permanent_rate
                $baseRate = $baseCost + 0.00;
                $updatedRates['permanent_rate'] = number_format($this->applyFormula($baseRate, $rateFormulas->get('permanent_rate')), 2, '.', '');

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
            $errorMessage = 'Database error: ' . $e->getMessage();
            
            if ($request->ajax() || $request->wantsJson()) {
                 return response()->json(['success' => false, 'message' => $errorMessage], 500);
            }
            return back()->withInput()->with('error', $errorMessage);
        }
    }
    
    /**
     * Ensures an array is always returned.
     */
    private function calculateCombinedRatesAndStock(): array
    {
        try {
            $suppliers = Supplier::pluck('id');
            $totalEffectiveCost = 0.00;
            $supplierCount = 0;
            
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
            
            try {
                $netStockAvailable = $this->calculateCombinedStock()['net_stock']; 
            } catch (\Throwable $th) {
                $netStockAvailable = 0.00;
            }


            return [
                'average_effective_cost' => $averageEffectiveCost,
                'sum_net_stock'          => $netStockAvailable,
            ];
        } catch (\Throwable $e) {
            Log::error("Error in calculateCombinedRatesAndStock: " . $e->getMessage());
            return [
                'average_effective_cost' => 0.00,
                'sum_net_stock'          => 0.00,
            ];
        }
    }
    
    private function calculateCombinedStock(): array
    {
         $totalPurchasedWeight = Purchase::sum('net_live_weight');
         $totalSoldWeight = SaleItem::sum('weight_kg'); 
         
         $netStockAvailable = max(0, $totalPurchasedWeight - $totalSoldWeight); 

         return [
             'net_stock' => (float) $netStockAvailable,
         ];
    }
    
    // Formula application logic
    private function applyFormula(float $baseRate, ?RateFormula $formula): float
    {
        if (!$formula) {
            return $baseRate;
        }

        $multiply = $formula->multiply > 0 ? $formula->multiply : 1.0;
        $divide   = $formula->divide > 0 ? $formula->divide : 1.0;
        $plus     = $formula->plus;
        $minus    = $formula->minus;
        
        $finalRate = $baseRate;
        
        $finalRate *= $multiply;
        
        if ($divide != 0 && $divide != 1) { 
            $finalRate /= $divide;
        }

        $finalRate += $plus;
        $finalRate -= $minus;
        
        return max(0.00, $finalRate);
    }
    
    /**
     * Endpoint to get rate formulas (for settings page population)
     */
    public function getRateFormulas()
    {
        $formulas = RateFormula::all()->keyBy('rate_key');
        
        $formattedFormulas = [];
        // Iterate through all friendly names (including the new purchase effective cost)
        foreach (self::RATE_FRIENDLY_NAMES as $key => $name) {
            $formula = $formulas->get($key);
            $formattedFormulas[$key] = [
                'name'     => $name,
                'multiply' => number_format($formula->multiply ?? 1.0, 1, '.', ''),
                'divide'   => number_format($formula->divide ?? 1.0, 1, '.', ''),
                'plus'     => number_format($formula->plus ?? 0.0, 1, '.', ''),
                'minus'    => number_format($formula->minus ?? 0.0, 1, '.', ''),
            ];
        }

        return response()->json([
            'formulas' => $formattedFormulas,
            'friendly_names' => self::RATE_FRIENDLY_NAMES
        ]);
    }
    
    /**
     * Endpoint to save rate formulas from the settings page
     */
    public function updateRateFormula(Request $request)
    {
        $data = $request->validate([
            // Validate the key against ALL keys, including the new one
            'rate_key' => ['required', 'string', 'in:' . implode(',', array_keys(self::RATE_MARGINS))], 
            'multiply' => ['nullable', 'numeric', 'min:0'],
            'divide'   => ['nullable', 'numeric', 'min:0.0001'], 
            'plus'     => ['nullable', 'numeric'],
            'minus'    => ['nullable', 'numeric', 'min:0'],
        ]);
        
        try {
            $data['multiply'] = $data['multiply'] ?? 1.0000;
            $data['divide']   = $data['divide'] ?? 1.0000;
            $data['plus']     = $data['plus'] ?? 0.0000;
            $data['minus']    = $data['minus'] ?? 0.0000;
            
            RateFormula::updateOrCreate(
                ['rate_key' => $data['rate_key']],
                $data
            );
            
            $friendlyName = self::RATE_FRIENDLY_NAMES[$data['rate_key']];

            return response()->json([
                'success' => true,
                'message' => "Formula for **$friendlyName** saved successfully!",
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving formula: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function getSupplierData(Request $request)
    {
        return response()->json([
            'base_effective_cost' => 0.00,
            'net_stock_available' => 0.00,
        ]);
    }
}