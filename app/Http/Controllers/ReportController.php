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
    // Placeholder methods for existing routes
    public function index() { 
        // Implement logic to display a reports index page, or redirect to a main report
       return redirect()->route('admin.reports.stock'); 
    }
    
    /**
     * Handles the Profit & Loss report functionality,
     * aggregating data and using the ProfitLossSummary DTO.
     */
public function stock(Request $request) 
    {
        $today = Carbon::today();
        $gauge_max_range = 5000; // Fixed max capacity for visual scale

        // 1. CUMULATIVE DATA (Net Stock, matching Dashboard logic)
        $totalPurchasedWeight = Purchase::sum('net_live_weight'); // Total Purchases to date
        $totalSoldWeightToDate = SaleItem::sum('weight_kg'); // Total Sales to date
        
        // 2. CURRENT NET STOCK (Current Stock on Dashboard)
        $currentNetStock = max(0, $totalPurchasedWeight - $totalSoldWeightToDate);

        // 3. TODAY'S SOLD WEIGHT (Sold Today on Dashboard)
        $todaySoldWeight = SaleItem::whereDate('created_at', $today)->sum('weight_kg');

        // 4. MORNING OPENING STOCK: Total Stock - Sales BEFORE today
        $totalSoldBeforeToday = $totalSoldWeightToDate - $todaySoldWeight;
        $morningOpeningStock = max(0, $totalPurchasedWeight - $totalSoldBeforeToday);
        
        $data = [
            'today_date' => $today->format('d M, Y'),
            'current_net_stock' => $currentNetStock,
            'morning_opening' => $morningOpeningStock,
            'sold_today_weight' => $todaySoldWeight,
            'total_purchased_weight' => $totalPurchasedWeight, 
            'gauge_max_range' => $gauge_max_range, 
        ];

        return view('pages.report.stock_report', $data);
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
            
            // 🚩 NEW: Use the saved sale_channel. Default to 'permanent' for older records.
            $saleChannel = strtolower($sale->sale_channel ?? 'permanent'); 

            foreach ($sale->items as $item) {
                $category = $item->product_category;
                $lineTotal = $item->line_total;
                $weight = $item->weight_kg;

                $grandTotalWeight += $weight;
                $grandTotalRevenue += $lineTotal;

                // 1. Check for Retail Sales (Explicitly selected on the form)
                if ($saleChannel === 'retail') {
                    if (isset($retailSalesAggregation[$category])) {
                        $retailSalesAggregation[$category]['weight'] += $weight;
                        $retailSalesAggregation[$category]['revenue'] += $lineTotal;
                        $retailSalesAggregation[$category]['total_rate'] += $item->rate_pkr;
                        $retailSalesAggregation[$category]['count'] += 1;
                    }
                } 
                // 2. Check for Wholesale Sales (Explicitly selected on the form OR customer name contains 'truck/wholesale')
                elseif ($saleChannel === 'wholesale' || str_contains(strtolower($customerName), 'truck')) {
                    $wholesaleSales[] = [
                        'customer_name' => $customerName,
                        'weight' => $weight,
                        'rate' => $item->rate_pkr,
                        'total' => $lineTotal,
                        'category' => $category,
                    ];
                } 
                // 3. Permanent/Hotel Sales (Any other sale with a customer ID that wasn't retail/wholesale)
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
                    // Aggregate line totals to ensure accurate sale amount
                    $permanentSales[$sale->id]['total_sale_amount'] += $lineTotal; 
                }
            }
        }
        
        $totalRetailWeight = array_sum(array_column($retailSalesAggregation, 'weight'));
        $totalRetailRevenue = array_sum(array_column($retailSalesAggregation, 'revenue'));

        $totalWholesaleRevenue = array_sum(array_column($wholesaleSales, 'total'));
        $totalWholesaleWeight = array_sum(array_column($wholesaleSales, 'weight'));
        
        // ... (Rest of the code remains the same as it correctly builds the reportData array) ...

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