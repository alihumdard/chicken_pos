<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Placeholder methods for existing routes
    public function index() { 
        return redirect()->route('admin.reports.stock_report'); 
    }
    
    /**
     * Handles the STOCK Report AND the Profit & Loss (P&L) report functionality.
     */
    public function stock(Request $request) 
    {
        // --- 1. P&L DATE RANGE SETUP ---
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->toDateString()))->endOfDay();
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();

        // --- 2. STOCK & INVENTORY CALCULATIONS ---
        $today = Carbon::today();
        $gauge_max_range = 5000; 

        $totalPurchasedWeight = Purchase::sum('net_live_weight');
        $totalSoldWeightToDate = SaleItem::sum('weight_kg');
        
        $currentNetStock = max(0, $totalPurchasedWeight - $totalSoldWeightToDate);
        $todaySoldWeight = SaleItem::whereDate('created_at', $today)->sum('weight_kg');
        $totalSoldBeforeToday = $totalSoldWeightToDate - $todaySoldWeight;
        $morningOpeningStock = max(0, $totalPurchasedWeight - $totalSoldBeforeToday);
        
        // --- 3. PROFIT & LOSS CALCULATIONS ---

        // Aggregate Revenue and Cost (COGS) based on the filter dates
        $totalRevenue = SaleItem::whereBetween('created_at', [$startDate, $endDate])->sum('line_total');
        $totalCogs = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_payable');

        // Daily Breakdown for Table and Chart
        $dailyReport = [];
        $chartLabels = [];
        $chartInputData = [];
        $chartOutputData = [];
        
        $currentDate = $startDate->copy();

        // 🟢 REMOVED: Mock Expense Generator
        // $mockExpenses = $this->generateMockExpenses($startDate, $endDate); 

        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            
            // Daily Revenue & Cost
            $dailyRevenue = SaleItem::whereDate('created_at', $currentDate)->sum('line_total');
            $dailyCost = Purchase::whereDate('created_at', $currentDate)->sum('total_payable');
            
            // Daily Weights for Chart
            $dailyInputWeight = Purchase::whereDate('created_at', $currentDate)->sum('net_live_weight');
            $dailyOutputWeight = SaleItem::whereDate('created_at', $currentDate)->sum('weight_kg');
            
            // 🟢 UPDATED: Set Expenses to 0 (Real data logic can be added here later)
            $dailyExpenses = 0; 
            
            $netProfit = $dailyRevenue - $dailyCost - $dailyExpenses;

            $dailyReport[] = [
                'date' => $dateString,
                'revenue' => (float)$dailyRevenue,
                'cost' => (float)$dailyCost,
                'expenses' => (float)$dailyExpenses,
                'net_profit' => (float)$netProfit,
            ];

            // Collect data for Chart JS
            $chartLabels[] = $currentDate->format('M d');
            $chartInputData[] = (float)$dailyInputWeight;
            $chartOutputData[] = (float)$dailyOutputWeight;

            $currentDate->addDay();
        }

        // Final Totals
        $totalExpenses = 0; // Since daily expenses are 0
        $totalNetProfit = $totalRevenue - $totalCogs - $totalExpenses;


        // --- 4. DATA COMPILATION & VIEW RETURN ---
        $data = [
            'today_date' => $today->format('d M, Y'),
            'current_net_stock' => $currentNetStock,
            'morning_opening' => $morningOpeningStock,
            'sold_today_weight' => $todaySoldWeight,
            'total_purchased_weight' => $totalPurchasedWeight, 
            'gauge_max_range' => $gauge_max_range, 
            
            'startDate' => $startDate->toDateString(), 
            'endDate' => $endDate->toDateString(),
            'totalRevenue' => $totalRevenue, 
            'totalCogs' => $totalCogs, 
            'totalExpenses' => $totalExpenses, 
            'totalNetProfit' => $totalNetProfit, 
            'dailyReport' => $dailyReport,
            'chartLabels' => $chartLabels,
            'chartInputData' => $chartInputData,
            'chartOutputData' => $chartOutputData,
        ];

        return view('pages.report.stock_report', $data);
    }

    // --- PURCHASE REPORT METHODS ---
    public function purchaseReport(Request $request)
    {
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get(); 
        $purchases = Purchase::with('supplier')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->get(); 
        return view('pages.report.purchase_report', compact('suppliers', 'purchases')); 
    }

    public function filterPurchaseReport(Request $request)
    {
        $query = Purchase::with('supplier');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $endDate = Carbon::parse($request->input('end_date'))->endOfDay(); 
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

        return response()->json(['html' => $html]);
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
            $html .= '<td class="p-2">' . Carbon::parse($purchase->created_at)->format('d/m/Y') . ' – ' . ($purchase->supplier->name ?? 'N/A') . '</td>';
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
        $date = $request->input('date', Carbon::now()->toDateString());
        $startDate = Carbon::parse($date)->startOfDay();
        $endDate = Carbon::parse($date)->endOfDay();

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
            $saleChannel = strtolower($sale->sale_channel ?? 'permanent'); 

            foreach ($sale->items as $item) {
                $category = $item->product_category;
                $lineTotal = $item->line_total;
                $weight = $item->weight_kg;

                $grandTotalWeight += $weight;
                $grandTotalRevenue += $lineTotal;

                if ($saleChannel === 'retail') {
                    if (isset($retailSalesAggregation[$category])) {
                        $retailSalesAggregation[$category]['weight'] += $weight;
                        $retailSalesAggregation[$category]['revenue'] += $lineTotal;
                        $retailSalesAggregation[$category]['total_rate'] += $item->rate_pkr;
                        $retailSalesAggregation[$category]['count'] += 1;
                    }
                } 
                elseif ($saleChannel === 'wholesale' || str_contains(strtolower($customerName), 'truck')) {
                    $wholesaleSales[] = [
                        'customer_name' => $customerName,
                        'weight' => $weight,
                        'rate' => $item->rate_pkr,
                        'total' => $lineTotal,
                        'category' => $category,
                    ];
                } 
                elseif ($sale->customer_id) {
                    if (!isset($permanentSales[$sale->id])) {
                        $permanentSales[$sale->id] = [
                            'customer_name' => $customerName,
                            'sale_date' => $sale->created_at->format('H:i'),
                            'items' => [],
                            'total_sale_amount' => 0,
                        ];
                    }
                    $permanentSales[$sale->id]['items'][] = $item;
                    $permanentSales[$sale->id]['total_sale_amount'] += $lineTotal; 
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

    public function profitLossReportDynamic(Request $request)
    {
        return redirect()->route('admin.reports.stock', $request->query());
    }
    
    // 🟢 REMOVED: generateMockExpenses function
}