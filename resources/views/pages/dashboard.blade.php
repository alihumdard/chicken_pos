@extends('layouts.main') 

@section('content')
<style>
    /* Modern Gradient Bar Styles */
    .card-gradient-green { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
    .card-gradient-blue { background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); }
    .card-gradient-purple { background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%); }
    .card-gradient-orange { background: linear-gradient(135deg, #F97316 0%, #EA580C 100%); }
</style>

<div class="p-4 sm:p-6 lg:p-8 bg-gray-100 min-h-screen">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-xl font-bold text-gray-800">Overview - {{ $today_date ?? '' }}</h1>
        <span class="text-sm font-medium text-gray-500 bg-white px-3 py-1 rounded-full shadow-sm border border-gray-100 italic">
            Live System Status
        </span>
    </div>

    {{-- Quick Actions: Single Row, Slim Design --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('admin.sales.index') }}" class="flex items-center justify-center gap-3 p-3 rounded-xl shadow-sm bg-white border border-emerald-100 hover:bg-emerald-50 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center group-hover:bg-emerald-200">
                <i class="fas fa-shopping-cart text-emerald-600 text-sm"></i>
            </div>
            <span class="text-gray-700 text-xs font-bold uppercase tracking-wider">New Sale</span>
        </a>
        
        <a href="{{ route('admin.contacts.index') }}" class="flex items-center justify-center gap-3 p-3 rounded-xl shadow-sm bg-white border border-purple-100 hover:bg-purple-50 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200">
                <i class="fas fa-users text-purple-600 text-sm"></i>
            </div>
            <span class="text-gray-700 text-xs font-bold uppercase tracking-wider">Contacts</span>
        </a>
        
        <a href="{{ route('admin.reports.stock') }}" class="flex items-center justify-center gap-3 p-3 rounded-xl shadow-sm bg-white border border-blue-100 hover:bg-blue-50 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200">
                <i class="fas fa-chart-bar text-blue-600 text-sm"></i>
            </div>
            <span class="text-gray-700 text-xs font-bold uppercase tracking-wider">Stock</span>
        </a>
        
        <a href="{{ route('admin.purchases.index') }}" class="flex items-center justify-center gap-3 p-3 rounded-xl shadow-sm bg-white border border-amber-100 hover:bg-amber-50 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200">
                <i class="fas fa-truck text-amber-600 text-sm"></i>
            </div>
            <span class="text-gray-700 text-xs font-bold uppercase tracking-wider">Purchase</span>
        </a>
    </div>

    {{-- Financial & Stock Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- Card 1: Total Revenue (Synced with P&L) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="card-gradient-green h-1.5 w-full"></div>
            <div class="p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Revenue</p>
                <h2 class="text-2xl font-black text-gray-800">PKR {{ number_format($total_revenue ?? 0, 0) }}</h2>
                <div class="mt-4 flex items-center text-[10px] text-emerald-600 font-bold">
                    <i class="fas fa-sync-alt mr-1"></i> Synced with P&L
                </div>
            </div>
        </div>

        {{-- Card 2: Current Stock --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="card-gradient-blue h-1.5 w-full"></div>
            <div class="p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Current Stock</p>
                <h2 class="text-2xl font-black text-gray-800">{{ number_format($current_stock ?? 0, 0) }} KG</h2>
                <div class="mt-4 flex items-center text-[10px] text-blue-600 font-bold">
                    <i class="fas fa-box mr-1"></i> Available Now
                </div>
            </div>
        </div>

        {{-- Card 3: Purchase Expense --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="card-gradient-purple h-1.5 w-full"></div>
            <div class="p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Purchase Expense</p>
                <h2 class="text-2xl font-black text-gray-800">PKR {{ number_format($purchase_expense ?? 0, 0) }}</h2>
                <div class="mt-4 flex items-center text-[10px] text-purple-600 font-bold">
                    <i class="fas fa-shopping-bag mr-1"></i> Today's Buying
                </div>
            </div>
        </div>

        {{-- Card 4: Internal Expense (Poultry Kharch) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="card-gradient-orange h-1.5 w-full"></div>
            <div class="p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Internal Expense</p>
                <h2 class="text-2xl font-black text-gray-800">PKR {{ number_format($internal_expenses ?? 0, 0) }}</h2>
                <div class="mt-4 flex items-center text-[10px] text-orange-600 font-bold">
                    <i class="fas fa-tools mr-1"></i> Poultry Kharch
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Ledger Activity Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Ledger Activity</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="pb-3">Time</th>
                        <th class="pb-3">Description</th>
                        <th class="pb-3">Type</th>
                        <th class="pb-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($transactions ?? [] as $tx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 text-xs text-gray-500 font-medium">{{ $tx->time }}</td>
                        <td class="py-4 text-sm font-bold text-gray-700">{!! $tx->customer !!}</td>
                        <td class="py-4 text-xs">
                            <span class="px-2 py-1 rounded-md font-bold uppercase {{ strtolower($tx->type) == 'sale' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                {{ $tx->type }}
                            </span>
                        </td>
                        <td class="py-4 text-sm font-black text-right text-gray-900">
                            {{ number_format($tx->amount, 0) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-400 font-medium">
                            No activity recorded today.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection