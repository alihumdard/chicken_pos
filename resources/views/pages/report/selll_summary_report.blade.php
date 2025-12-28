@extends('layouts.main')

@section('content')
<div class="flex">
    <main id="mainContent" class="flex-1 bg-gray-100 w-full max-w-full p-3 sm:p-6 lg:p-8">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 sm:p-6">
            
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">
                        Today's Sales Summary
                    </h1>
                    <p class="text-gray-500 text-sm">Date: {{ Carbon\Carbon::parse($date)->format('F d, Y') }}</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <form action="{{ route('admin.reports.sell.summary') }}" method="GET" class="flex items-center gap-2">
                        <input type="date" name="date" value="{{ $date }}" class="border rounded-lg px-2 py-1.5 text-sm outline-none">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg text-sm font-bold transition">Filter</button>
                    </form>

                    <button onclick="openMonthlyModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-bold flex items-center gap-2 transition shadow-md">
                        <i class="fas fa-calendar-alt"></i> Monthly Report
                    </button>
                </div>
            </div>

            @php
                $sections = [
                    ['key' => 'wholesale', 'title' => '1. Wholesale Sales (Brokers)'],
                    ['key' => 'permanent', 'title' => '2. Permanent Customer Sales'],
                    ['key' => 'shop_retail', 'title' => '3. Shop / Retail Sales']
                ];
            @endphp

            @foreach($sections as $section)
                <div class="mb-10">
                    <h2 class="font-bold text-lg text-gray-800 mb-4 px-1 border-l-4 border-indigo-500 pl-3">{{ $section['title'] }}</h2>
                    <div class="overflow-x-auto rounded-xl border">
                        <table class="w-full text-sm border-collapse">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="p-3 text-left">Customer / Time</th>
                                    <th class="p-3 text-left">Items Detail</th>
                                    <th class="p-3 text-right">Weight</th>
                                    <th class="p-3 text-right">Total (PKR)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @php $sWeight = 0; $sRev = 0; @endphp
                                @forelse ($categorizedSales[$section['key']] as $sale)
                                    <tr>
                                        <td class="p-3">
                                            <span class="font-bold text-gray-700">{{ $sale['customer_name'] }}</span>
                                            <div class="text-[10px] text-gray-400">{{ $sale['sale_time'] }}</div>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($sale['items'] as $item)
                                                    <span class="bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 rounded border">
                                                        {{ $item->product_category }}: {{ number_format($item->weight_kg, 2) }}kg
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="p-3 text-right">{{ number_format($sale['total_weight'], 2) }}kg</td>
                                        <td class="p-3 text-right font-black">{{ number_format($sale['total_amount'], 0) }}</td>
                                    </tr>
                                    @php
                                        $sWeight += $sale['total_weight'];
                                        $sRev += $sale['total_amount'];
                                    @endphp
                                @empty
                                    <tr><td colspan="4" class="p-10 text-center text-gray-400">No records found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

            <div class="bg-indigo-600 text-white rounded-2xl p-6 mt-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-center">
                <div>
                    <p class="text-xs opacity-70">TODAY'S TOTAL WEIGHT</p>
                    <p class="text-3xl font-black">{{ number_format($totals['grandTotalWeight'], 2) }}kg</p>
                </div>
                <div>
                    <p class="text-xs opacity-70">TODAY'S TOTAL REVENUE</p>
                    <p class="text-3xl font-black">{{ number_format($totals['grandTotalRevenue'], 0) }} PKR</p>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- --- MONTHLY MODAL --- --}}
<div id="monthlyModal" class="fixed inset-0 z-[60] hidden overflow-y-auto">
    <div class="fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-sm" onclick="closeMonthlyModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-6xl overflow-hidden">
            
            <div class="bg-indigo-700 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-bold"><i class="fas fa-chart-line"></i> Monthly Sales Analysis</h3>
                <button onclick="closeMonthlyModal()" class="text-3xl font-bold">&times;</button>
            </div>

            <div class="p-6 bg-gray-50">
                {{-- Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 bg-white p-5 rounded-2xl border">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">SELECT MONTH</label>
                        <input type="month" id="monthInput" value="{{ date('Y-m') }}" class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">CUSTOMER DROPDOWN</label>
                        <select id="customerFilter" class="w-full border rounded-xl p-2.5 outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer) {{-- Backend se aane wale saare customers yahan loop ho rahe hain --}}
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button onclick="loadMonthlyData()" class="w-full bg-indigo-600 text-white py-2.5 rounded-xl font-bold shadow-md">Search Report</button>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="bg-blue-600 rounded-2xl p-5 text-white shadow-xl">
                        <p class="text-xs opacity-80 font-bold uppercase">Monthly Total Weight</p>
                        <p id="mWeight" class="text-3xl font-black mt-1">0.00kg</p>
                    </div>
                    <div class="bg-emerald-600 rounded-2xl p-5 text-white shadow-xl">
                        <p class="text-xs opacity-80 font-bold uppercase">Monthly Total Revenue</p>
                        <p id="mRevenue" class="text-3xl font-black mt-1">0 PKR</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border overflow-hidden">
                    <div class="max-h-[450px] overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 sticky top-0 z-10">
                                <tr>
                                    <th class="p-4 text-left font-bold">Date / Customer</th>
                                    <th class="p-4 text-left font-bold">Items Detail</th>
                                    <th class="p-4 text-right font-bold">Weight</th>
                                    <th class="p-4 text-right font-bold">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyTableBody" class="divide-y divide-gray-100">
                                <tr><td colspan="4" class="p-20 text-center text-gray-400">Search to load monthly records...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    async function loadMonthlyData() {
        const month = document.getElementById('monthInput').value;
        const customer = document.getElementById('customerFilter').value;
        const tbody = document.getElementById('monthlyTableBody');

        tbody.innerHTML = '<tr><td colspan="4" class="p-20 text-center"><p class="text-gray-500 font-bold animate-pulse">Loading Monthly Records...</p></td></tr>';

        try {
            const response = await fetch(`{{ route('admin.reports.sell.monthly') }}?month=${month}&customer_id=${customer}`);
            const data = await response.json();

            document.getElementById('mWeight').textContent = data.totals.weight + ' kg';
            document.getElementById('mRevenue').textContent = data.totals.revenue + ' PKR';

            let html = '';
            data.sales.forEach(sale => {
                let itemsHtml = '';
                sale.items.forEach(item => {
                    itemsHtml += `<span class="inline-block bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 rounded border mr-1 mb-1">
                                    ${item.product_category}: ${parseFloat(item.weight_kg).toFixed(2)}kg
                                  </span>`;
                });

                html += `
                    <tr class="hover:bg-gray-50 transition border-b">
                        <td class="p-4">
                            <div class="font-black text-gray-700 uppercase text-xs">${sale.customer_name}</div>
                            <div class="text-[10px] text-gray-400 font-bold">${sale.date} @ ${sale.time}</div>
                        </td>
                        <td class="p-4">${itemsHtml}</td>
                        <td class="p-4 text-right font-bold text-gray-600">${parseFloat(sale.total_weight).toFixed(2)}kg</td>
                        <td class="p-4 text-right font-black text-indigo-700">${parseFloat(sale.total_amount).toLocaleString()}</td>
                    </tr>`;
            });

            tbody.innerHTML = html || '<tr><td colspan="4" class="p-20 text-center text-gray-400 font-bold">No records found.</td></tr>';
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="4" class="p-20 text-center text-red-500 font-bold">Failed to load monthly data.</td></tr>';
        }
    }

    function openMonthlyModal() { document.getElementById('monthlyModal').classList.remove('hidden'); }
    function closeMonthlyModal() { document.getElementById('monthlyModal').classList.add('hidden'); }
</script>
@endsection