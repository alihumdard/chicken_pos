@extends('layouts.main') 

@section('content')
<style>
    /* Custom Color Mapping to Tailwind classes */
    .card-green { background-color: #10B981; } 
    .card-blue { background-color: #3B82F6; } 
    .card-purple { background-color: #A855F7; } 
    .card-orange { background-color: #F97316; } 
    
    .text-green-icon { color: #10B981; }
    .text-red-icon { color: #EF4444; }
    .text-blue-icon { color: #3B82F6; }
    .text-yellow-icon { color: #F59E0B; }
    
    .bg-green-icon { background-color: #10B981; }
    .bg-red-icon { background-color: #EF4444; }
    .bg-blue-icon { background-color: #3B82F6; }
    .bg-yellow-icon { background-color: #F59E0B; }
</style>

{{-- Reduced padding on mobile (p-4), kept original large padding on desktop (lg:p-8) --}}
<div class="p-4 sm:p-6 lg:p-8 bg-gray-100 min-h-screen">
    
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">Today's Overview - {{ $today_date ?? 'N/A' }}</h1>
    </div>

    {{-- Grid Layout: 1 col mobile, 2 cols tablet, 4 cols desktop --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        
        {{-- Card 1: Total Sales Today (GREEN) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-green text-white p-4 font-semibold"> 
                Total Sales Today
            </div>
            <div class="p-4 flex flex-col justify-between items-center text-center"> 
                {{-- Responsive Text: 3xl on mobile, 4xl on desktop --}}
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-2"> 
                    PKR {{ number_format($total_sales ?? 0, 0) }}
                </h2>
                <p class="pt-4 lg:pt-6 text-gray-500">Gross Revenue</p>
            </div>
        </div>

        {{-- Card 2: Current Live Stock (BLUE) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-blue text-white px-4 py-3 font-semibold"> 
                Current Live Stock
            </div>
            <div class="p-4 flex flex-col justify-between items-center text-center">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 mt-2"> 
                    {{ number_format($current_stock ?? 0, 0) }} KG
                </h2>
                <div class="text-sm text-gray-700 w-full mt-4"> 
                    Available for Sale
                </div>
            </div>
        </div>

        {{-- Card 3: Today's Purchase (PURPLE) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="bg-purple-600 text-white px-4 py-3 font-semibold"> 
                Today's Purchase
            </div>
            <div class="p-4 flex flex-col justify-start ">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 mt-2"> 
                    Net: {{ number_format($today_purchase_net ?? 0, 0) }} KG
                </h2>
                <div class="text-sm text-gray-700 w-full mt-4"> 
                    Cost: PKR <span class="font-bold">{{ number_format($today_purchase_cost ?? 0, 0) }}</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Today's Expenses (ORANGE) --}}
        <div class="bg-white rounded-xl shadow-lg overflow-hidden justify-between items-center text-center">
            <div class="card-orange text-white px-4 py-3 font-semibold"> 
                Today's Expenses
            </div>
            <div class="p-4 flex flex-col justify-between items-start justify-between items-center text-center">
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-6">
                    PKR {{ number_format($today_expenses ?? 0, 0) }}
                </h2>
                <div class="text-sm text-gray-700">
                    Daily Operational Costs
                </div>
            </div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Left Panel: Recent Transactions --}}
        <div class="lg:col-span-1 bg-white p-4 lg:p-6 rounded-xl shadow-lg"> 
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Ledger Transactions</h3>
            
            {{-- overflow-x-auto allows table to scroll horizontally on small screens --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions ?? [] as $tx)
                        <tr>
                            <td class="px-3 py-3 whitespace-nowrap text-xs text-gray-500">{{ $tx->time }}</td>
                            
                            <td class="px-3 py-3 text-sm font-medium text-gray-800">
                                {!! $tx->customer !!} 
                            </td>
                            
                            <td class="px-3 py-3 whitespace-nowrap text-sm">
                                @if(strtolower($tx->type) == 'sale' || strtolower($tx->type) == 'debit')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $tx->type }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $tx->type }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-3 whitespace-nowrap text-sm font-bold text-right text-gray-900">
                                {{ number_format($tx->amount, 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-3 text-center text-sm text-gray-500">No transactions recorded today.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right Panel: Quick Actions --}}
        <div class="lg:col-span-1 bg-white p-4 lg:p-6 rounded-xl shadow-lg"> 
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            
            <div class="grid grid-cols-2 gap-4">
                
                {{-- Action 1: New Sale --}}
                <a href="{{ route('admin.sales.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-green-icon hover:bg-green-600 transition-colors h-24">
                    <i class="fas fa-shopping-cart text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">New Sale</span>
                </a>
                
                {{-- Action 2: Suppliers / Ledger --}}
                <a href="{{ route('admin.contacts.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-purple-600 hover:bg-purple-700 transition-colors h-24">
                    <i class="fas fa-users text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">Suppliers & Customers</span>
                </a>
                
                {{-- Action 3: View Stock Monitor --}}
                <a href="{{ route('admin.reports.stock') }}" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-blue-icon hover:bg-blue-600 transition-colors h-24">
                    <i class="fas fa-chart-bar text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">View Stock Monitor</span>
                </a>
                
                {{-- Action 4: Purchases --}}
                <a href="{{ route('admin.purchases.index') }}" class="flex flex-col items-center justify-center p-4 rounded-xl shadow-md bg-yellow-icon hover:bg-yellow-600 transition-colors h-24">
                    <i class="fas fa-truck text-white text-2xl mb-1"></i>
                    <span class="text-white text-xs font-medium text-center">Manage Purchases</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection