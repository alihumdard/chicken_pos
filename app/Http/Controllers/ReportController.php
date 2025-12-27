<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // Placeholder methods for existing routes
    public function index()
    {
        return redirect()->route('admin.reports.stock_report');
    }

    /**
     * Handles the STOCK Report AND the Profit & Loss (P&L) report functionality.
     */
    public function stock(Request $request) 
    {
        $endDate = Carbon::parse($request->input('end_date', Carbon::now()->toDateString()))->endOfDay();
        $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();

        // 1. Overall Inventory Status
        $totalPurchasedWeight = Purchase::sum('net_live_weight');
        $totalSoldWeightToDate = SaleItem::sum('weight_kg');
        $currentNetStock = max(0, $totalPurchasedWeight - $totalSoldWeightToDate);
        
        // 2. Range Specific Totals
        $totalShrink = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('shrink_loss');
        $rangePurchasedWeight = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('net_live_weight');
        $rangeSoldWeight = SaleItem::whereBetween('created_at', [$startDate, $endDate])->sum('weight_kg');

        // 3. Daily Breakdown for Table & Chart
        $dailyReport = [];
        $chartLabels = [];
        $chartInputData = [];
        $chartOutputData = [];
        
        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dateString = $currentDate->toDateString();
            
            $inputWeight = Purchase::whereDate('created_at', $currentDate)->sum('net_live_weight');
            $outputWeight = SaleItem::whereDate('created_at', $currentDate)->sum('weight_kg');
            $shrinkLoss = Purchase::whereDate('created_at', $currentDate)->sum('shrink_loss');
            
            $dailyReport[] = [
                'date' => $currentDate->format('d M'),
                'input_weight' => (float)$inputWeight,
                'output_weight' => (float)$outputWeight,
                'shrink' => (float)$shrinkLoss,
            ];

            $chartLabels[] = $currentDate->format('M d');
            $chartInputData[] = (float)$inputWeight;
            $chartOutputData[] = (float)$outputWeight;

            $currentDate->addDay();
        }

        return view('pages.report.stock_report', [
            'startDate' => $startDate->toDateString(), 
            'endDate' => $endDate->toDateString(),
            'current_net_stock' => $currentNetStock,
            'total_purchased_weight' => $rangePurchasedWeight,
            'totalSoldWeightToDate' => $rangeSoldWeight,
            'totalShrink' => $totalShrink,
            'dailyReport' => $dailyReport,
            'chartLabels' => $chartLabels,
            'chartInputData' => $chartInputData,
            'chartOutputData' => $chartOutputData,
        ]);
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
                $endDate,
            ]);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->input('supplier_id'));
        }

        $purchases = $query->orderBy('created_at', 'desc')->get();
        $html      = $this->renderPurchaseTableRows($purchases);

        return response()->json(['html' => $html]);
    }

    protected function renderPurchaseTableRows($purchases)
    {
        $html                  = '';
        $totalGrossWeight      = 0;
        $totalDeadShrinkWeight = 0;
        $totalNetLiveWeight    = 0;

        if ($purchases->isEmpty()) {
            return '<tr><td colspan="5" class="p-4 text-center text-gray-500">No purchase records found for the selected criteria.</td></tr>';
        }

        foreach ($purchases as $purchase) {
            $deadShrink = $purchase->dead_weight + $purchase->shrink_loss;
            $netLive    = $purchase->gross_weight - $deadShrink;

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

        // 1. Fetch all customers for the Modal Dropdown
        $allCustomers = \App\Models\Customer::orderBy('name')->get();

        $sales = Sale::with(['customer', 'items'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'asc')
            ->get();

        $categorizedSales = [
            'wholesale' => [],
            'permanent' => [],
            'shop_retail' => [],
        ];

        $grandTotalWeight = 0;
        $grandTotalRevenue = 0;

        foreach ($sales as $sale) {
            $customer = $sale->customer;
            $type = $customer->type ?? 'permanent';
            
            $group = 'permanent';
            if ($type === 'broker') $group = 'wholesale';
            if ($type === 'shop_retail') $group = 'shop_retail';

            $saleWeight = $sale->items->sum('weight_kg');
            $grandTotalWeight += $saleWeight;
            $grandTotalRevenue += $sale->total_amount;

            $categorizedSales[$group][$sale->id] = [
                'customer_name' => $customer->name ?? 'Retail/Walk-in',
                'sale_time' => $sale->created_at->format('H:i'),
                'items' => $sale->items,
                'total_weight' => $saleWeight,
                'total_amount' => $sale->total_amount,
            ];
        }

        return view('pages.report.selll_summary_report', [
            'categorizedSales' => $categorizedSales,
            'date' => $date,
            'customers' => $allCustomers,
            'totals' => [
                'grandTotalWeight' => $grandTotalWeight,
                'grandTotalRevenue' => $grandTotalRevenue,
            ]
        ]);
    }

    public function monthlySalesReport(Request $request)
{
    $month = $request->input('month', Carbon::now()->format('Y-m'));
    $customerId = $request->input('customer_id'); // Optional Customer Filter

    $startDate = Carbon::parse($month)->startOfMonth()->startOfDay();
    $endDate = Carbon::parse($month)->endOfMonth()->endOfDay();

    $query = Sale::with(['customer', 'items'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    // Optional Customer Filter Logic
    if ($customerId) {
        $query->where('customer_id', $customerId);
    }

    $sales = $query->orderBy('created_at', 'asc')->get();

    $monthlySales = [];
    $totalRevenue = 0;
    $totalWeight = 0;

    foreach ($sales as $sale) {
        $saleWeight = $sale->items->sum('weight_kg');
        
        $monthlySales[] = [
            'customer_name' => $sale->customer->name ?? 'Retail/Walk-in',
            'date' => $sale->created_at->format('d M, Y'),
            'time' => $sale->created_at->format('H:i'),
            'items' => $sale->items, // Items detail pass ki ja rahi hai
            'total_weight' => $saleWeight,
            'total_amount' => $sale->total_amount,
        ];

        $totalRevenue += $sale->total_amount;
        $totalWeight += $saleWeight;
    }

    return response()->json([
        'sales' => $monthlySales,
        'totals' => [
            'revenue' => number_format($totalRevenue, 0),
            'weight' => number_format($totalWeight, 2),
        ]
    ]);
    }
   public function profitLossReport(Request $request)
{
    $endDate = Carbon::parse($request->input('end_date', Carbon::now()->toDateString()))->endOfDay();
    $startDate = Carbon::parse($request->input('start_date', Carbon::now()->startOfMonth()->toDateString()))->startOfDay();

    // 1. Total Financial Calculations
    $totalRevenue = SaleItem::whereBetween('created_at', [$startDate, $endDate])->sum('line_total');
    $totalCogs = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_payable');
    $poultryExpenses = Purchase::whereBetween('created_at', [$startDate, $endDate])->sum('total_kharch');
    $externalExpenses = 0; // Future use ke liye placeholder

    $totalNetProfit = $totalRevenue - ($totalCogs + $poultryExpenses + $externalExpenses);

    // 2. Daily Financial Breakdown
    $dailyReport = [];
    $currentDate = $startDate->copy();

    while ($currentDate->lte($endDate)) {
        $dateString = $currentDate->toDateString();
        
        $dailyRevenue = SaleItem::whereDate('created_at', $currentDate)->sum('line_total');
        $dailyCost = Purchase::whereDate('created_at', $currentDate)->sum('total_payable');
        $dailyKharch = Purchase::whereDate('created_at', $currentDate)->sum('total_kharch');
        
        $net = $dailyRevenue - ($dailyCost + $dailyKharch);

        $dailyReport[] = [
            'date' => $dateString,
            'revenue' => (float)$dailyRevenue,
            'cost' => (float)$dailyCost,
            'kharch' => (float)$dailyKharch,
            'net_profit' => (float)$net,
        ];

        $currentDate->addDay();
    }

    return view('pages.report.pnl_report', [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
        'totalRevenue' => $totalRevenue,
        'totalCogs' => $totalCogs,
        'poultryExpenses' => $poultryExpenses,
        'externalExpenses' => $externalExpenses,
        'totalNetProfit' => $totalNetProfit,
        'dailyReport' => $dailyReport
    ]);
}
    
}
