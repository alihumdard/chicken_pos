@extends('layouts.main')

@section('content')
<style>
    /* Custom Color Mapping to Tailwind classes based on the image */
    .card-green { background-color: #10B981; } /* Emerald-500 equivalent */
    .card-blue { background-color: #3B82F6; }  /* Blue-500 equivalent */
    .card-purple { background-color: #A855F7; } /* Violet-500 equivalent */
    .card-orange { background-color: #F97316; } /* Orange-500 equivalent */
    
    .text-green-icon { color: #10B981; }
    .text-red-icon { color: #EF4444; }
    .text-blue-icon { color: #3B82F6; }
    .text-yellow-icon { color: #F59E0B; }
    
    .bg-green-icon { background-color: #10B981; }
    .bg-red-icon { background-color: #EF4444; }
    .bg-blue-icon { background-color: #3B82F6; }
    .bg-yellow-icon { background-color: #F59E0B; }
</style>

<div class="p-4 sm:p-6 lg:p-8 bg-gray-100 min-h-screen">
    
    <!-- HEADER -->
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">Today's Overview - {{ $today_date ?? 'N/A' }}</h1>
    </div>

    <!-- 1. TOP CARDS ROW (4 Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- Card 1: Total Sales Today (GREEN) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-green text-white px-4 py-3 font-semibold"> {{-- Adjusted padding for header --}}
                Total Sales Today
            </div>
            <div class="p-4 flex flex-col justify-start"> {{-- Removed h-full as it conflicts with content flow --}}
                <h2 class="text-4xl font-extrabold text-gray-900 mb-2"> {{-- Adjusted bottom margin --}}
                    PKR {{ number_format($total_sales ?? 0, 0) }}
                </h2>
                {{-- 🟢 FIX: Used flex justify-between to align text left/right as in image --}}
                <div class="flex justify-between text-sm text-gray-900 mt-2"> 
                    <div class="flex flex-col items-start">
                        <span class="text-gray-700">Cash:</span> 
                        <span class="font-bold">{{ number_format($cash_sales ?? 0, 0) }}</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-gray-700">Credit:</span> 
                        <span class="font-bold">{{ number_format($credit_sales ?? 0, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Current Live Stock (BLUE) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-blue text-white px-4 py-3 font-semibold"> {{-- Adjusted padding for header --}}
                Current Live Stock
            </div>
            <div class="p-4 flex flex-col justify-between items-center text-center">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4 mt-2"> {{-- Reduced margin, added top margin --}}
                    {{ number_format($current_stock ?? 0, 0) }} KG
                </h2>
                <div class="text-sm text-gray-700 w-full mt-4"> {{-- Added top margin --}}
                    Available for Sale
                </div>
            </div>
        </div>

        {{-- Card 3: Today's Purchase (PURPLE) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-purple text-white px-4 py-3 font-semibold"> {{-- Adjusted padding for header --}}
                Today's Purchase
            </div>
            <div class="p-4 flex flex-col justify-start ">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4 mt-2"> {{-- Reduced margin, added top margin --}}
                    Net: {{ number_format($today_purchase_net ?? 0, 0) }} KG
                </h2>
                <div class="text-sm text-gray-700 w-full mt-4"> {{-- Added top margin --}}
                    Cost: PKR <span class="font-bold">{{ number_format($today_purchase_cost ?? 0, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Today's Expenses (ORANGE) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-orange text-white px-4 py-3 font-semibold"> {{-- Adjusted padding for header --}}
                Today's Expenses
            </div>
            <div class="p-4 flex flex-col justify-between items-start justify-between items-center text-center">
                <h2 class="text-4xl font-extrabold text-gray-900 mb-6">
                    PKR {{ number_format($today_expenses ?? 0, 0) }}
                </h2>
                <div class="text-sm text-gray-700">
                    Ice, Bags, etc.
                </div>
            </div>
        </div>

    </div>

    <!-- 2. BOTTOM PANELS ROW (Transactions and Actions) -->
    {{-- FIX APPLIED: Retaining 50/50 split on large screens --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Left Panel: Recent Transactions (50% width on large screens) --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg"> 
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Transactions</h3>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($transactions ?? [] as $tx)
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-sm text-gray-500">{{ $tx->time }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800">{{ $tx->customer }}</td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($tx->type == 'Credit') bg-red-100 text-red-800 @else bg-green-100 text-green-800 @endif">
                                    {{ $tx->type }}
                                </span>
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                                {{ number_format($tx->amount, 0) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right Panel: Quick Actions (50% width on large screens) --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg"> 
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            
            <div class="grid grid-cols-2 gap-4">
                
                {{-- Action 1: New Wholesale Sale (Green) --}}
                <a href="{{ route('admin.sales.create') }}" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-green-icon hover:bg-green-600 transition-colors h-24">
                    <i class="fas fa-shopping-cart text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">New Wholesale Sale</span>
                </a>
                
                {{-- Action 2: Record Expense (Red) --}}
                <a href="#" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-red-icon hover:bg-red-600 transition-colors h-24">
                    <i class="fas fa-file-invoice-dollar text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">Record Expense</span>
                </a>
                
                {{-- Action 3: View Stock Monitor (Blue) --}}
                <a href="{{ route('admin.reports.stock') }}" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-blue-icon hover:bg-blue-600 transition-colors h-24">
                    <i class="fas fa-chart-bar text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">View Stock Monitor</span>
                </a>
                
                {{-- Action 4: Start Day End Closing (Yellow/Orange) --}}
                <a href="#" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-yellow-icon hover:bg-yellow-600 transition-colors h-24">
                    <i class="fas fa-sign-out-alt text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">Start Day End Closing</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection