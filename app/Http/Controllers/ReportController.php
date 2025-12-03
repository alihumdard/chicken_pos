<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProfitLossSummary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // Keeping the use statement for convention, but using FQCN below

class ReportController extends Controller
{
    // Placeholder methods for existing routespublic function index(Request $request)
    public function index(Request $request)
    {
        // Get the requested date, default to today
        $targetDate = $request->input('target_date', now()->toDateString());

        // 1. Fetch all suppliers for the dropdown
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

        // Default rates structure (Ensure all new fields are initialized)
        $defaultData = [
            'base_effective_cost' => 0.00,
            'net_stock_available' => 0.00,
            'wholesale_rate' => 0.00,
            'permanent_rate' => 0.00,
            'retail_mix_rate' => 0.00,
            'retail_chest_rate' => 0.00,
            'retail_thigh_rate' => 0.00,
            'retail_piece_rate' => 0.00,
            
            // New fields for rates.index, initialized to 0.00
            'live_chicken_rate' => 0.00,
            'wholesale_hotel_mix_rate' => 0.00,
            'wholesale_hotel_chest_rate' => 0.00,
            'wholesale_hotel_thigh_rate' => 0.00,
            'wholesale_customer_piece_rate' => 0.00,

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
                // Assuming these new fields are added to DailyRate model, otherwise they default to 0.00
                'live_chicken_rate' => $historicalRate->live_chicken_rate ?? 0.00,
                'wholesale_hotel_mix_rate' => $historicalRate->wholesale_hotel_mix_rate ?? 0.00,
                'wholesale_hotel_chest_rate' => $historicalRate->wholesale_hotel_chest_rate ?? 0.00,
                'wholesale_hotel_thigh_rate' => $historicalRate->wholesale_hotel_thigh_rate ?? 0.00,
                'wholesale_customer_piece_rate' => $historicalRate->wholesale_customer_piece_rate ?? 0.00,

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
            
            $baseCost = $data['effective_cost'] ?? 0.00;

            $defaultData['base_effective_cost'] = $baseCost;
            $defaultData['net_stock_available'] = $data['net_stock'] ?? 0.00;

            // --- START: NEW DYNAMIC DEFAULT RATE CALCULATION (Base Cost + Margin) ---
            
            // Wholesale & Credit Rates
            $defaultData['wholesale_rate'] = $baseCost + 10.00; // Wholesale (Truck) +10 PKR Margin
            $defaultData['live_chicken_rate'] = $baseCost + 20.00; // Live Chicken +20 PKR Margin
            
            // New Wholesale Rates (Hotels & Customers)
            $defaultData['wholesale_hotel_mix_rate'] = $baseCost + 25.00; // Hotel Mix +25 PKR Margin
            $defaultData['wholesale_hotel_chest_rate'] = $baseCost + 125.00; // Hotel Chest +125 PKR Margin
            $defaultData['wholesale_hotel_thigh_rate'] = $baseCost + 75.00; // Hotel Thigh +75 PKR Margin
            $defaultData['wholesale_customer_piece_rate'] = $baseCost; // Customer Piece No Margin
            
            // Retail Rates (Shop Purchun)
            $defaultData['retail_mix_rate'] = $baseCost + 50.00; // Mix +50 PKR Margin
            $defaultData['retail_chest_rate'] = $baseCost + 150.00; // Chest +150 PKR Margin
            $defaultData['retail_thigh_rate'] = $baseCost + 100.00; // Thigh +100 PKR Margin
            $defaultData['retail_piece_rate'] = $baseCost - 10.00; // Piece -10 PKR Loss (Can be negative)
            // --- END: NEW DYNAMIC DEFAULT RATE CALCULATION ---

        }

        return view('pages.rates.index', compact('suppliers', 'defaultData', 'targetDate'));
    }
    
    /**
     * Handles the Profit & Loss report functionality,
     * aggregating data and using the ProfitLossSummary DTO.
     */
    public function stock(Request $request) // <-- UPDATED: Now contains P&L logic
    {
        // Use \Carbon\Carbon everywhere to avoid namespace confusion
        $startDate = $request->input('start_date', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $endDate   = $request->input('end_date', \Carbon\Carbon::now()->toDateString());
        
        $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        $end   = \Carbon\Carbon::parse($endDate)->endOfDay();
        
        // Mock Expenses
        $dailyExpenses = $this->generateMockExpenses($start, $end);
        
        // 2. Fetch Aggregated Sales (Revenue)
        $salesData = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // 3. Fetch Aggregated Purchases (COGS - Calculated by Effective Cost)
        $purchaseData = Purchase::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_payable) as total_cost'),
                DB::raw('SUM(net_live_weight) as total_weight_in')
            )
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');
            
        // 4. Combine data day-by-day
        $allDates = $salesData->keys()->merge($purchaseData->keys())->unique()->sort();

        $dailyReport      = [];
        $totalRevenue     = 0;
        $totalCogs        = 0;
        $totalExpenses    = 0;
        $totalInputWeight = 0;
        
        foreach ($allDates as $date) {
            $sales    = $salesData->get($date);
            $purchases = $purchaseData->get($date);
            $expenses = $dailyExpenses[$date] ?? 0;

            $revenue  = $sales->total_sales ?? 0;
            $cost     = $purchases->total_cost ?? 0;
            $weightIn = $purchases->total_weight_in ?? 0;
            
            $cogsForDisplay = $cost; 
            
            $netProfit = $revenue - $cogsForDisplay - $expenses;
            
            $dailyReport[] = [
                'date'       => $date,
                'revenue'    => $revenue,
                'cost'       => $cogsForDisplay,
                'expenses'   => $expenses,
                'net_profit' => $netProfit,
            ];

            $totalRevenue     += $revenue;
            $totalCogs        += $cogsForDisplay;
            $totalExpenses    += $expenses;
            $totalInputWeight += $weightIn;
        }

        // 5. Instantiate DTO to aggregate final totals (Net Profit, Output Weight)
        $pnlSummary = new ProfitLossSummary([
            'totalRevenue'     => $totalRevenue,
            'totalCogs'        => $totalCogs,
            'totalExpenses'    => $totalExpenses,
            'totalInputWeight' => $totalInputWeight,
            'dailyReport'      => $dailyReport,
        ]);
        
        // 5b. Prepare chart data using DTO's calculated output weight
        $chartLabels     = ['Input vs Output'];
        $chartInputData  = [$pnlSummary->totalInputWeight];
        $chartOutputData = [$pnlSummary->totalOutputWeight]; // Calculated in DTO

        // 6. Return to view, merging DTO's array output with other required variables
        return view('pages.report.stock_report', array_merge( // DTO is converted to array to pass data
            $pnlSummary->toArray(),
            compact(
                'startDate', 
                'endDate', 
                'chartLabels',
                'chartInputData',
                'chartOutputData'
            )
        ));
    }

    // --- PURCHASE REPORT METHODS ---

    public function purchaseReport(Request $request)
    {
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get(); 
        $purchases = Purchase::with('supplier')
            ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get(); 
        return view('pages.report.purchase_report', compact('suppliers', 'purchases')); 
    }

    public function filterPurchaseReport(Request $request)
    {
        $query = Purchase::with('supplier');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay(); 
            $query->whereBetween('created_at', [
                $request->input('start_date'), 
                $endDate
            ]);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        $purchases = $query->orderBy('created_at', 'desc')->get();

        $html = $this->renderPurchaseTableRows($purchases);

        return response()->json([
            'html' => $html,
        ]);
    }
    
    protected function renderPurchaseTableRows($purchases)
    {
        $html = '';
        $totalGrossWeight = 0;
        $totalDeadShrinkWeight = 0;
        $totalNetLiveWeight = 0;

        if ($purchases->isEmpty()) {
            return '<tr><td colspan="5" class="p-4 text-center text-gray-500">No purchase records found for the selected criteria.</td></tr>';
        }

        foreach ($purchases as $purchase) {
            $deadShrink = $purchase->dead_weight + $purchase->shrink_loss;
            $netLive = $purchase->gross_weight - $deadShrink;

            $totalGrossWeight += $purchase->gross_weight;
            $totalDeadShrinkWeight += $deadShrink;
            $totalNetLiveWeight += $netLive;

            $html .= '<tr class="border-b">';
            $html .= '<td class="p-2">' . \Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y') . ' – ' . ($purchase->supplier->name ?? 'N/A') . '</td>';
            $html .= '<td class="p-2">' . number_format($purchase->gross_weight, 2) . 'kg</td>';
            $html .= '<td class="p-2 text-red-500">-' . number_format($deadShrink, 2) . 'kg</td>';
            $html .= '<td class="p-2 text-green-600">' . number_format($netLive, 2) . 'kg</td>';
            $html .= '<td class="p-2">' . number_format($purchase->buying_rate, 2) . ' PKR</td>';
            $html .= '</tr>';
        }

        $html .= '<tr class="font-semibold">';
        $html .= '<td class="p-2">Total</td>';
        $html .= '<td class="p-2">' . number_format($totalGrossWeight, 2) . 'kg</td>';
        $html .= '<td class="p-2 text-red-500">-' . number_format($totalDeadShrinkWeight, 2) . 'kg</td>';
        $html .= '<td class="p-2 text-green-600">' . number_format($totalNetLiveWeight, 2) . 'kg</td>';
            $html .= '<td class="p-2">—</td>';
        $html .= '</tr>';

        return $html;
    }


    // --- SELL SUMMARY REPORT METHOD ---

    public function sellSummaryReport(Request $request)
    {
        $date = $request->input('date', \Carbon\Carbon::now()->toDateString());
        $startDate = \Carbon\Carbon::parse($date)->startOfDay();
        $endDate = \Carbon\Carbon::parse($date)->endOfDay();

        $sales = Sale::with(['customer', 'items'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $wholesaleSales = [];
        $permanentSales = [];
        
        $retailSalesAggregation = [
            'Mix' => ['weight' => 0, 'revenue' => 0, 'total_rate' => 0, 'count' => 0],
            'Chest' => ['weight' => 0, 'revenue' => 0, 'total_rate' => 0, 'count' => 0],
            'Thigh' => ['weight' => 0, 'revenue' => 0, 'total_rate' => 0, 'count' => 0],
            'Piece' => ['weight' => 0, 'revenue' => 0, 'total_rate' => 0, 'count' => 0],
        ];

        $grandTotalWeight = 0;
        $grandTotalRevenue = 0;

        foreach ($sales as $sale) {
            $customerName = $sale->customer->name ?? 'Retail/Walk-in'; 

            foreach ($sale->items as $item) {
                $category = $item->product_category;
                $lineTotal = $item->line_total;
                $weight = $item->weight_kg;

                $grandTotalWeight += $weight;
                $grandTotalRevenue += $lineTotal;

                if (str_contains(strtolower($customerName), 'wholesale') || str_contains(strtolower($customerName), 'truck')) {
                    $wholesaleSales[] = [
                        'customer_name' => $customerName,
                        'weight' => $weight,
                        'rate' => $item->rate_pkr,
                        'total' => $lineTotal,
                        'category' => $category,
                    ];
                } elseif ($sale->customer_id) {
                    $permanentSales[$sale->id]['customer_name'] = $customerName;
                    $permanentSales[$sale->id]['sale_date'] = $sale->created_at->format('H:i');
                    $permanentSales[$sale->id]['items'][] = $item;
                    $permanentSales[$sale->id]['total_sale_amount'] = $sale->total_amount;
                } else {
                    if (isset($retailSalesAggregation[$category])) {
                        $retailSalesAggregation[$category]['weight'] += $weight;
                        $retailSalesAggregation[$category]['revenue'] += $lineTotal;
                        $retailSalesAggregation[$category]['total_rate'] += $item->rate_pkr;
                        $retailSalesAggregation[$category]['count'] += 1;
                    }
                }
            }
        }
        
        $totalRetailWeight = array_sum(array_column($retailSalesAggregation, 'weight'));
        $totalRetailRevenue = array_sum(array_column($retailSalesAggregation, 'revenue'));

        $totalWholesaleRevenue = array_sum(array_column($wholesaleSales, 'total'));
        $totalWholesaleWeight = array_sum(array_column($wholesaleSales, 'weight'));
        
        $reportData = [
            'wholesaleSales' => $wholesaleSales,
            'permanentSales' => $permanentSales,
            'retailSalesAggregation' => $retailSalesAggregation,
            'date' => $date,
            'totals' => [
                'grandTotalWeight' => $grandTotalWeight,
                'grandTotalRevenue' => $grandTotalRevenue,
                'totalRetailWeight' => $totalRetailWeight,
                'totalRetailRevenue' => $totalRetailRevenue,
                'totalWholesaleWeight' => $totalWholesaleWeight,
                'totalWholesaleRevenue' => $totalWholesaleRevenue,
            ],
        ];

        return view('pages.report.selll_summary_report', $reportData);
    }


    // --- PROFIT & LOSS REPORT METHOD (Now redirected) ---

    public function profitLossReportDynamic(Request $request) // <-- Logic moved to stock()
    {
        // P&L logic has been moved to the stock() method.
        return redirect()->route('admin.reports.stock', $request->query());
    }
    
    // --- HELPER METHOD FOR MOCK EXPENSES ---
    private function generateMockExpenses(\Carbon\Carbon $start, \Carbon\Carbon $end): array
    {
        $expenses = [];
        $currentDate = $start->copy();
        
        while ($currentDate->lte($end)) {
            $expenses[$currentDate->toDateString()] = rand(50, 500); 
            $currentDate->addDay();
        }
        
        return $expenses;
    }
}