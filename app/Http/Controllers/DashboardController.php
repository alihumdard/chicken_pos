<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        /**
         * 🟢 REVENUE SYNC WITH P&L REPORT
         * Profit & Loss report mein revenue 'SaleItem::sum(line_total)' se calculate hoti hai.
         * Humne dashboard par bhi wahi logic apply kar di hai.
         */
        $total_revenue = SaleItem::whereDate('created_at', $today)->sum('line_total');

        // 2. Current Live Stock
        $total_purchased_weight = Purchase::sum('net_live_weight');
        $total_sold_weight = SaleItem::sum('weight_kg');
        $current_stock = max(0, $total_purchased_weight - $total_sold_weight);

        // 3. Today's Purchase Expense (Total Payable)
        $today_purchase_cost = Purchase::whereDate('created_at', $today)->sum('total_payable');

        // 4. Internal Expense (Poultry Kharch)
        $internal_expenses = Purchase::whereDate('created_at', $today)->sum('total_kharch');

        // 5. Recent Transactions mapping
        $transactions = Transaction::latest()
            ->take(8)
            ->get()
            ->map(function ($tx) {
                $amount = ($tx->debit > 0) ? $tx->debit : $tx->credit;
                $type = ucfirst(str_replace('_', ' ', $tx->type));
                return (object) [
                    'time' => Carbon::parse($tx->created_at)->format('h:i A'),
                    'customer' => $tx->description,
                    'type' => $type,
                    'amount' => $amount
                ];
            });

        return view('pages.dashboard', [
            'today_date' => $today->format('d M, Y'),
            'total_revenue' => $total_revenue,
            'current_stock' => $current_stock,
            'purchase_expense' => $today_purchase_cost,
            'internal_expenses' => $internal_expenses,
            'transactions' => $transactions
        ]);
    }
}