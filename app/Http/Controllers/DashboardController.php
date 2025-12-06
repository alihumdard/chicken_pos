<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Transaction; // The ledger model we created
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Calculate Total Sales Today
        $total_sales = Sale::whereDate('created_at', $today)->sum('total_amount');

        // 2. Calculate Current Live Stock (Total Purchased - Total Sold)
        $total_purchased_weight = Purchase::sum('net_live_weight');
        $total_sold_weight = SaleItem::sum('weight_kg');
        $current_stock = max(0, $total_purchased_weight - $total_sold_weight);

        // 3. Today's Purchase Data
        $today_purchases = Purchase::whereDate('created_at', $today)->get();
        $today_purchase_net = $today_purchases->sum('net_live_weight');
        $today_purchase_cost = $today_purchases->sum('total_payable');

        // 4. Today's Expenses 
        // (Set to 0 for now until you create an Expense Table, removing mock random numbers)
        $today_expenses = 0; 

        // 5. Recent Transactions (From Ledger)
        // Mapping the data to match your View's variable names
        $transactions = Transaction::latest()
            ->take(10) // Show last 10
            ->get()
            ->map(function ($tx) {
                // Determine Amount (Debit or Credit)
                $amount = ($tx->debit > 0) ? $tx->debit : $tx->credit;
                
                // Determine Label Color logic based on type
                $type = ucfirst(str_replace('_', ' ', $tx->type)); // e.g., "Sale", "Payment"

                return (object) [
                    'time' => Carbon::parse($tx->created_at)->format('h:i A'),
                    'customer' => $tx->description, // Description contains Name + Badge info
                    'type' => $type,
                    'amount' => $amount
                ];
            });

        return view('pages.dashboard', [
            'today_date' => $today->format('d M, Y'),
            'total_sales' => $total_sales,
            'current_stock' => $current_stock,
            'today_purchase_net' => $today_purchase_net,
            'today_purchase_cost' => $today_purchase_cost,
            'today_expenses' => $today_expenses,
            'transactions' => $transactions
        ]);
    }
}