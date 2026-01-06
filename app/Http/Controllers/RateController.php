<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DailyRate;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SaleItem;
use App\Models\RateFormula;
use App\Models\Sale;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\DB;

class RateController extends Controller
{
    // 1. Define Class Properties
    private $rateMargins;
    private $rateFriendlyNames;

    /**
     * 2. Constructor: Initialize Defaults & Merge Database Values
     */
    public function __construct()
    {
        $this->rateMargins = [];

        $this->rateFriendlyNames = [];

        // B. Merge Real-Time Database Values
        try {
            // Check active formulas
            $formulas = RateFormula::where('status', true)->get();

            foreach ($formulas as $formula) {
                // Add/Overwrite friendly name
                $this->rateFriendlyNames[$formula->rate_key] = $formula->title;

                // Add/Overwrite margin (using 'plus' column as default margin)
                $this->rateMargins[$formula->rate_key] = $formula->plus;
            }
        } catch (\Throwable $e) {
            // Silently fail if DB is not ready (e.g. during migration)
        }
    }

    public function index(Request $request)
    {
        try {
            $targetDate = $request->input('target_date', now()->toDateString());
            $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

            // Fetch formulas for the view
            $rateFormulas = RateFormula::where('status', true)->get()->keyBy('rate_key');

            $defaultData = [
                'base_effective_cost' => 0.00,
                'manual_base_cost'    => 0.00,
                'net_stock_available' => 0.00,
                'is_historical'       => false,
            ];

            // Initialize all known keys to 0.00
            foreach ($this->rateFriendlyNames as $key => $name) {
                $defaultData[$key] = 0.00;
            }

            $activeRate = DailyRate::whereDate('created_at', $targetDate)->latest()->first();

            if ($activeRate) {
                // --- LOAD SAVED DATA (Historical) ---
                $savedManualCost = (float)($activeRate->manual_base_cost ?? 0.00);

                $defaultData['base_effective_cost'] = (float)$activeRate->base_effective_cost;
                $defaultData['manual_base_cost']    = $savedManualCost;
                $defaultData['is_historical']       = now()->toDateString() != $targetDate;

                $stockData = $this->calculateCombinedStock();
                $defaultData['net_stock_available'] = $stockData['net_stock'] ?? 0.00;

                // Load values dynamically from the saved record
                foreach ($this->rateFriendlyNames as $key => $name) {
                    // Check if column exists on the object before accessing
                    $defaultData[$key] = (float)($activeRate->$key ?? 0.00);
                }
            } elseif (now()->toDateString() == $targetDate) {
                // --- LIVE CALCULATION (Today) ---
                $combinedData = $this->calculateCombinedRatesAndStock();
                $baseCost     = $combinedData['average_effective_cost'];

                $defaultData['base_effective_cost'] = $baseCost;
                $defaultData['net_stock_available'] = $combinedData['sum_net_stock'];

                // Calculate values dynamically
                foreach ($this->rateFriendlyNames as $key => $name) {
                    $formula = $rateFormulas->get($key);

                    // Priority: Use DB Formula if exists, else use Base + Margin array
                    if ($formula) {
                        $defaultData[$key] = $this->applyFormula($baseCost, $formula);
                    } else {
                        $margin = $this->rateMargins[$key] ?? 0.00;
                        $defaultData[$key] = max(0, $baseCost + $margin);
                    }
                }
            }

        $shops = Shop::all(); 
        $stockData = $this->calculateCombinedStock();
        $defaultData['net_stock_available'] = $stockData['net_stock'];
        $defaultData['shop_stock_breakdown'] = $stockData['shop_breakdown'];

        return view('pages.rates.index', compact('suppliers', 'defaultData', 'targetDate', 'rateFormulas', 'shops'));
        } catch (Exception $e) {
            dd($e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        // 1. Static Validation Rules (For the main table columns)
        $rules = [
            'supplier_id'         => ['nullable', 'exists:suppliers,id'],
            'base_effective_cost' => ['required', 'numeric', 'min:0'],
            'manual_base_cost'    => ['nullable', 'numeric', 'min:0'],
        ];

        // 2. Dynamic Validation Rules (For the values going into JSON)
        // We ensure every key defined in our friendly names list is present
        foreach ($this->rateFriendlyNames as $key => $name) {
            $rules[$key] = ['required', 'numeric'];
        }

        $data = $request->validate($rules);

        // Prepare Basic Data
        $supplierId = $data['supplier_id'] ?? (Supplier::first()->id ?? 1);
        $baseCost   = $data['base_effective_cost'];
        $manualCost = $data['manual_base_cost'] ?? 0.00;

        // ðŸŸ¢ 3. PACK DYNAMIC RATES INTO AN ARRAY
        // Instead of saving to individual columns, we bundle them here.
        $rateValues = [];
        foreach ($this->rateFriendlyNames as $key => $title) {
            $rateValues[$key] = $data[$key];
        }

        try {
            // Deactivate previous rates for today
            DailyRate::whereDate('created_at', now()->toDateString())->update(['is_active' => false]);

            // ðŸŸ¢ 4. SAVE TO DATABASE (Using JSON column)
            DailyRate::create([
                'supplier_id'         => $supplierId,
                'base_effective_cost' => $baseCost,
                'manual_base_cost'    => $manualCost,
                'rate_values'         => $rateValues, // Eloquent automatically converts this array to JSON
                'is_active'           => true
            ]);

            // 5. AJAX Response (For the "Override/Calculate" button preview)
            if ($request->ajax() || $request->wantsJson()) {
                $calcBase = (float)($manualCost ?: $baseCost);
                $rateFormulas = RateFormula::all()->keyBy('rate_key');
                $updatedRates = [];

                foreach ($this->rateFriendlyNames as $key => $title) {
                    $formula = $rateFormulas->get($key);
                    if ($formula) {
                        $updatedRates[$key] = number_format($this->applyFormula($calcBase, $formula), 2, '.', '');
                    } else {
                        $margin = $this->rateMargins[$key] ?? 0.00;
                        $updatedRates[$key] = number_format($calcBase + $margin, 2, '.', '');
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Rates calculated and saved successfully.',
                    'base_effective_cost' => number_format($calcBase, 2, '.', ''),
                    'rates' => $updatedRates,
                ]);
            }

            return redirect()->route('admin.rates.index', ['target_date' => now()->toDateString()])
                ->with('success', 'Rates saved successfully!');
        } catch (Exception $e) {
            $errorMessage = 'Database error: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) return response()->json(['success' => false, 'message' => $errorMessage], 500);
            return back()->withInput()->with('error', $errorMessage);
        }
    }

    public function getRateFormulas()
    {
        $formulas = RateFormula::all()->keyBy('rate_key');

        // 1. Refresh dynamic names from DB
        try {
            $dbFormulas = RateFormula::where('status', true)->get();
            foreach ($dbFormulas as $f) {
                $this->rateFriendlyNames[$f->rate_key] = $f->title;
            }
        } catch (\Exception $e) {
        }

        // 🟢 2. SORTING LOGIC (Wholesale -> Retail -> Others)
        // We get all keys and sort them based on channel priority
        $sortedKeys = collect(array_keys($this->rateFriendlyNames))->sortBy(function ($key) use ($formulas) {

            // Determine Channel: Look in DB first, otherwise guess from string
            $formula = $formulas->get($key);

            if ($formula && $formula->channel) {
                $channel = $formula->channel;
            } else {
                // Fallback logic for keys not yet in DB
                if (str_starts_with($key, 'wholesale')) {
                    $channel = 'wholesale';
                } elseif (str_starts_with($key, 'retail') || str_starts_with($key, 'live')) {
                    $channel = 'retail';
                } else {
                    $channel = 'other'; // e.g. permanent_rate
                }
            }

            // Assign Priority (Lower number = Top of list)
            return match ($channel) {
                'wholesale' => 1,
                'retail'    => 2,
                default     => 3,
            };
        });

        $formattedFormulas = [];

        // 🟢 3. Loop through the SORTED keys
        foreach ($sortedKeys as $key) {

            $name = $this->rateFriendlyNames[$key];
            $formula = $formulas->get($key);

            // Handle Icon URL
            $iconFullUrl = null;
            if ($formula && $formula->icon_url) {
                if (str_starts_with($formula->icon_url, 'http')) {
                    $iconFullUrl = $formula->icon_url;
                } else {
                    $path = str_replace('storage/', '', $formula->icon_url);
                    $iconFullUrl = asset('storage/' . $path);
                }
            }

            $formattedFormulas[$key] = [
                'name'     => $name,
                'icon_url' => $iconFullUrl,
                'multiply' => number_format($formula->multiply ?? 1.0, 4, '.', ''),
                'divide'   => number_format($formula->divide ?? 1.0, 4, '.', ''),
                'plus'     => number_format($formula->plus ?? 0.0, 2, '.', ''),
                'minus'    => number_format($formula->minus ?? 0.0, 2, '.', ''),
            ];
        }

        return response()->json([
            'formulas'       => $formattedFormulas,
            // We re-sort the friendly names array to match the order for the dropdown
            'friendly_names' => array_replace(array_flip($sortedKeys->toArray()), $this->rateFriendlyNames)
        ]);
    }

    public function updateRateFormula(Request $request)
    {
        $data = $request->validate([
            'rate_key'  => ['required', 'string'],
            'title'     => ['nullable', 'string', 'max:191'],
            'channel'   => ['nullable', 'in:wholesale,retail'],
            'icon_url'  => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'multiply'  => ['nullable', 'numeric', 'min:0'],
            'divide'    => ['nullable', 'numeric', 'min:0.0001'],
            'plus'      => ['nullable', 'numeric'],
            'minus'     => ['nullable', 'numeric'],
        ]);

        try {
            $existing = $this->rateFriendlyNames[$data['rate_key']] ?? null;
            $titleToSave = $data['title'] ?? ($existing ?? ucfirst(str_replace('_', ' ', $data['rate_key'])));
            $channelToSave = $data['channel'] ?? (str_starts_with($data['rate_key'], 'wholesale') ? 'wholesale' : 'retail');

            $updateData = [
                'title'    => $titleToSave,
                'channel'  => $channelToSave,
                'multiply' => $data['multiply'] ?? 1.0000,
                'divide'   => $data['divide'] ?? 1.0000,
                'plus'     => $data['plus'] ?? 0.0000,
                'minus'    => $data['minus'] ?? 0.0000,
                'status'   => true,
            ];

            // Handle File Upload
            if ($request->hasFile('icon_url')) {
                $path = $request->file('icon_url')->store('formula_icons', 'public');
                $updateData['icon_url'] = 'storage/' . $path;
            }

            RateFormula::updateOrCreate(
                ['rate_key' => $data['rate_key']],
                $updateData
            );

            return response()->json([
                'success' => true,
                'message' => "Formula for **$titleToSave** saved successfully!",
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    private function calculateCombinedRatesAndStock(): array
    {
        try {
            $suppliers = Supplier::pluck('id');
            $totalEffectiveCost = 0.00;
            $supplierCount = 0;
            foreach ($suppliers as $supplierId) {
                $latest = Purchase::where('supplier_id', $supplierId)->latest('created_at')->first();
                if ($latest) {
                    $totalEffectiveCost += (float) $latest->effective_cost;
                    $supplierCount++;
                }
            }
            $avg = $supplierCount > 0 ? $totalEffectiveCost / $supplierCount : 0.00;
            $stock = $this->calculateCombinedStock()['net_stock'] ?? 0.00;
            return ['average_effective_cost' => $avg, 'sum_net_stock' => $stock];
        } catch (\Throwable $e) {
            Log::error("Rate Calc Error: " . $e->getMessage());
            return ['average_effective_cost' => 0.00, 'sum_net_stock' => 0.00];
        }
    }

    private function calculateCombinedStock(): array
    {
        $shops = Shop::all();
        
        $shopStocks = [];
        $netStockTotal = 0.00;

        foreach ($shops as $shop) {
            $currentStock = $shop->current_stock; 

            $shopStocks[] = [
                'id'    => $shop->id,
                'name'  => $shop->name,
                'stock' => $currentStock
            ];

            $netStockTotal += $currentStock;
        }

        return [
            'net_stock'      => (float) max(0, $netStockTotal),
            'shop_breakdown' => $shopStocks
        ];
    }

    private function applyFormula(float $baseRate, ?RateFormula $formula): float
    {
        if (!$formula) return $baseRate;
        $val = $baseRate * ($formula->multiply > 0 ? $formula->multiply : 1.0);
        if ($formula->divide > 0 && $formula->divide != 1) $val /= $formula->divide;
        $val += $formula->plus;
        $val -= $formula->minus;
        return max(0.00, $val);
    }

    public function getSupplierData(Request $request)
    {
        return response()->json(['base_effective_cost' => 0.00, 'net_stock_available' => 0.00]);
    }


    public function shrink(Request $request)
    {
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'weight'  => 'required|numeric|min:0.01',
            'note'    => 'nullable|string|max:255'
        ]);

        try {
            
            DB::beginTransaction();

            $shop = Shop::findOrFail($request->shop_id);
            if ($shop->current_stock < $request->weight) {
                return response()->json(['success' => false, 'message' => "Insufficient stock in {$shop->name}. Available: {$shop->current_stock}"], 400);
            }

            $customer = Customer::firstOrCreate(
                ['name' => 'Stock Shrinkage ('.$shop->name.')'],
                ['phone' => rand('00000000000','9999999999'), 'type'=> 'shop_retail','address' => 'Internal System']
            );

            $sale = Sale::create([
                'shop_id'        => $shop->id,
                'customer_id'    => $customer->id,
                'total_amount'   => 0, 
                'paid_amount'    => 0,
                'payment_status' => 'paid', 
                'sale_channel'   => 'retail',
                'note'           => $request->note ?? 'Manual Stock Shrinkage',
            ]);

            // 4. Create Sale Item
            SaleItem::create([
                'sale_id'          => $sale->id,
                'product_category' => 'shrinkage',
                'weight_kg'        => $request->weight,
                'rate_pkr'         => 0,
                'line_total'       => 0,
            ]);

            DB::commit();

            // Return new stock value
            return response()->json([
                'success' => true,
                'message' => "Stock reduced by {$request->weight} KG from {$shop->name}.",
                'new_stock' => $shop->current_stock
            ]);

        } catch (Exception $e) {
            
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
