@extends('layouts.main')

@section('content')
 <div class="flex">

        <!-- MAIN PAGE -->
        <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">
            <!-- PAGE CONTENT -->
            <div class="p-4 space-y-6">

                <h1 class="text-3xl font-extrabold text-gray-800 mb-6 tracking-tight">
                    Sales Summary Report 
                    <span class="text-gray-500 text-base font-normal ml-2">({{ Carbon\Carbon::parse($date)->format('F d, Y') }})</span>
                </h1>
                
                {{-- Date Filter --}}
                <form class="mb-6 flex space-x-4 items-end" method="GET" action="{{ route('admin.reports.sell.summary') }}">
                    <div class="flex-1 max-w-xs">
                        <label for="date_filter" class="block text-sm font-medium text-gray-700">Select Date</label>
                        <input type="date" id="date_filter" name="date" value="{{ $date }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Load Report
                        </button>
                    </div>
                </form>

                {{-- Initialize local totals for Permanent Sales --}}
                @php
                    $totalPermanentWeight = 0;
                    $totalPermanentRevenue = 0;
                @endphp

                <!-- 1. WHOLESALE SALES -->
                <div class="bg-white rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3 text-lg">1. Wholesale Sales (Truck-to-Truck)</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Customer Name</th>
                                <th class="p-2 text-left">Category</th>
                                <th class="p-2 text-left">Weight (KG)</th>
                                <th class="p-2 text-right">Rate (PKR)</th>
                                <th class="p-2 text-right">Total (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($wholesaleSales as $sale)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-medium text-gray-700">{{ $sale['customer_name'] }}</td>
                                    <td class="p-2 text-xs text-gray-600">{{ $sale['category'] }}</td>
                                    <td class="p-2">{{ number_format($sale['weight'], 2) }}kg</td>
                                    <td class="p-2 text-right text-green-600">{{ number_format($sale['rate'], 2) }}</td>
                                    <td class="p-2 text-right font-semibold">{{ number_format($sale['total'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-2 text-center text-gray-400">No wholesale sales recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-100 border-t-2">
                                <td class="p-2">Total Wholesale</td>
                                <td></td>
                                <td class="p-2">{{ number_format($totals['totalWholesaleWeight'], 2) }}kg</td>
                                <td></td>
                                <td class="p-2 text-right text-xl text-indigo-600">{{ number_format($totals['totalWholesaleRevenue'], 0) }} PKR</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 2. HOTEL/PERMANENT SALES -->
                <div class="bg-white rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3 text-lg">2. Hotel/Permanent Sales</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Customer Name</th>
                                <th class="p-2 text-left">Items Detail</th>
                                <th class="p-2 text-left">Total Weight (KG)</th>
                                <th class="p-2 text-right">Total Amount (PKR)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($permanentSales as $saleId => $sale)
                                @php
                                    $currentSaleWeight = 0;
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2 font-medium text-gray-700">{{ $sale['customer_name'] }} <span class="text-xs text-gray-500 ml-1">({{ $sale['sale_date'] }})</span></td>
                                    <td class="p-2 space-x-2">
                                        @foreach($sale['items'] as $item)
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded inline-block my-0.5">
                                                {{ $item->product_category }}: {{ number_format($item->weight_kg, 2) }}kg @ {{ number_format($item->rate_pkr, 0) }}
                                            </span>
                                            @php
                                                $currentSaleWeight += $item->weight_kg;
                                            @endphp
                                        @endforeach
                                    </td>
                                    <td class="p-2">{{ number_format($currentSaleWeight, 2) }}kg</td>
                                    <td class="p-2 text-right font-semibold">{{ number_format($sale['total_sale_amount'], 0) }}</td>
                                </tr>
                                @php
                                    $totalPermanentWeight += $currentSaleWeight;
                                    $totalPermanentRevenue += $sale['total_sale_amount'];
                                @endphp
                            @empty
                                <tr>
                                    <td colspan="4" class="p-2 text-center text-gray-400">No hotel/permanent sales recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-100 border-t-2">
                                <td class="p-2">Total Permanent Sales</td>
                                <td></td>
                                <td class="p-2">{{ number_format($totalPermanentWeight, 2) }}kg</td>
                                <td class="p-2 text-right text-xl text-indigo-600">{{ number_format($totalPermanentRevenue, 0) }} PKR</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- 3. RETAIL SALES (Aggregated) -->
                <div class="bg-white rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3 text-lg">3. Shop/Retail Sales (Aggregated)</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Category</th>
                                <th class="p-2 text-left">Total Weight (KG)</th>
                                <th class="p-2 text-left">Avg Rate (PKR)</th>
                                <th class="p-2 text-right">Total Revenue (PKR)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($retailSalesAggregation as $category => $data)
                                @if ($data['weight'] > 0)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-2 font-medium text-gray-700">{{ $category }}</td>
                                        <td class="p-2">{{ number_format($data['weight'], 2) }}kg</td>
                                        <td class="p-2 text-gray-600">
                                            @if ($data['count'] > 0)
                                                {{ number_format($data['total_rate'] / $data['count'], 2) }}
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                        <td class="p-2 text-right font-semibold">{{ number_format($data['revenue'], 0) }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="4" class="p-2 text-center text-gray-400">No retail sales recorded.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-100 border-t-2">
                                <td class="p-2">Total Retail</td>
                                <td class="p-2">{{ number_format($totals['totalRetailWeight'], 2) }}kg</td>
                                <td></td>
                                <td class="p-2 text-right text-xl text-indigo-600">{{ number_format($totals['totalRetailRevenue'], 0) }} PKR</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- FOOTER TOTALS -->
                <div class="bg-indigo-600 text-white rounded-xl shadow p-6 font-semibold">
                    <div class="flex flex-col md:flex-row justify-between space-y-3 md:space-y-0 text-lg">
                        <div class="font-bold">Grand Total for {{ Carbon\Carbon::parse($date)->format('F d, Y') }}</div>
                        <div class="text-center">
                            Total Weight Sold: <span class="font-extrabold text-2xl ml-2">{{ number_format($totals['grandTotalWeight'], 2) }}kg</span>
                        </div>
                        <div class="text-right">
                            Total Revenue: <span class="font-extrabold text-2xl ml-2">{{ number_format($totals['grandTotalRevenue'], 0) }} PKR</span>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>


@endsection