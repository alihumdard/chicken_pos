<?php
namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Set Date
        $today = Carbon::today();

        // 2. Calculate Sales Metrics
        $todaySales = Sale::whereDate('created_at', $today)->get();

        $totalSalesAmount = $todaySales->sum('total_amount');

                                                 // Note: Actual Cash/Credit split depends on Sale model's payment method/status.
                                                 // Using a mock split here for demonstration.
        $cashSales   = $totalSalesAmount * 0.40; // Mock 40% Cash
        $creditSales = $totalSalesAmount * 0.60; // Mock 60% Credit

        // 3. Calculate Current Live Stock (Total Purchases - Total Sales Weight)
        $totalPurchasedWeight = Purchase::sum('net_live_weight');
        $totalSoldWeight      = SaleItem::sum('weight_kg');
        $currentStock         = max(0, $totalPurchasedWeight - $totalSoldWeight); // Current available stock

        // 4. Calculate Today's Purchases
        $todayPurchases    = Purchase::whereDate('created_at', $today)->get();
        $todayPurchaseNet  = $todayPurchases->sum('net_live_weight');
        $todayPurchaseCost = $todayPurchases->sum('total_payable');

        // 5. Fetch Recent Transactions (Last 5 sales)
        $transactions = Sale::with('customer')
            ->whereDate('created_at', $today)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return (object) [
                    'time'     => $sale->created_at->format('h:i A'),
                    'customer' => $sale->customer->name ?? 'N/A',
                    // Mocking type based on payment status/channel if available, otherwise using Credit/Cash placeholder
                    'type'     => $sale->payment_status === 'paid' ? 'Cash' : 'Credit',
                    'amount'   => (float) $sale->total_amount,
                ];
            });

                                   // 6. Mock Today's Expenses (Since no Expense Model is provided)
        $todayExpenses = 25000.00; // Placeholder value

        $data = [
            'today_date'          => $today->format('d M, Y'),
            'total_sales'         => $totalSalesAmount,
            'cash_sales'          => $cashSales,
            'credit_sales'        => $creditSales,
            'current_stock'       => $currentStock,
            'today_purchase_net'  => $todayPurchaseNet,
            'today_purchase_cost' => $todayPurchaseCost,
            'today_expenses'      => $todayExpenses,
            'transactions'        => $transactions,
        ];

        return view('pages.dashboard', $data);
    }
}
