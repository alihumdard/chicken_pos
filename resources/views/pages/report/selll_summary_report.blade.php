@extends('layouts.main')

@section('content')
<div class="flex">

    <main id="mainContent" class="flex-1 bg-gray-100 w-full max-w-full overflow-x-hidden">
        <div class="p-3 sm:p-6 lg:p-8">

            <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
                <div class="p-4 sm:p-6 space-y-6">

                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 tracking-tight">
                            Sales Summary Report
                            <span class="block sm:inline text-gray-500 text-sm sm:text-base font-normal sm:ml-2 mt-1 sm:mt-0">
                                ({{ Carbon\Carbon::parse($date)->format('F d, Y') }})
                            </span>
                        </h1>
                    </div>

                    {{-- DATE FILTER FORM --}}
                    <form class="mb-6 grid grid-cols-1 sm:grid-cols-12 gap-4 items-end" method="GET" action="{{ route('admin.reports.sell.summary') }}">
                        <div class="sm:col-span-4 lg:col-span-3">
                            <label for="date_filter" class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>
                            <input type="date" id="date_filter" name="date" value="{{ $date }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm transition">
                        </div>
                        <div class="sm:col-span-3">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                Load Report
                            </button>
                        </div>
                    </form>

                    {{-- Initialize local totals --}}
                    @php
                        $totalPermanentWeight = 0;
                        $totalPermanentRevenue = 0;
                    @endphp

                    <div>
                        <h2 class="font-bold text-lg sm:text-xl text-gray-800 mb-4 px-1">1. Wholesale Sales (Broker)</h2>
                        
                        <div class="block w-full">
                            <table class="w-full text-sm border-collapse table-auto">
                                {{-- Header: Hidden on mobile --}}
                                <thead class="hidden md:table-header-group bg-gray-100 rounded-t-xl">
                                    <tr>
                                        <th class="p-3 text-left text-gray-700 font-semibold rounded-tl-xl">Customer Name</th>
                                        <th class="p-3 text-left text-gray-700 font-semibold">Category</th>
                                        <th class="p-3 text-left text-gray-700 font-semibold">Weight (KG)</th>
                                        <th class="p-3 text-right text-gray-700 font-semibold">Rate (PKR)</th>
                                        <th class="p-3 text-right text-gray-700 font-semibold rounded-tr-xl">Total (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody class="block md:table-row-group bg-white divide-y divide-gray-200">
                                    @forelse ($wholesaleSales as $sale)
                                        {{-- Row: Card on mobile, Table row on desktop --}}
                                        <tr class="block md:table-row mb-4 md:mb-0 border md:border-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0 hover:bg-gray-50 transition border-gray-200">
                                            
                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                <span class="md:hidden font-bold text-gray-500">Customer:</span>
                                                <span class="font-medium text-gray-700">{{ $sale['customer_name'] }}</span>
                                            </td>
                                            
                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                <span class="md:hidden font-bold text-gray-500">Category:</span>
                                                <span class="text-xs text-gray-600 bg-gray-100 px-2 py-1 rounded">{{ $sale['category'] }}</span>
                                            </td>

                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                <span class="md:hidden font-bold text-gray-500">Weight:</span>
                                                <span>{{ number_format($sale['weight'], 2) }}kg</span>
                                            </td>

                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                <span class="md:hidden font-bold text-gray-500">Rate:</span>
                                                <span class="text-green-600">{{ number_format($sale['rate'], 2) }}</span>
                                            </td>

                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                                <span class="md:hidden font-bold text-gray-500">Total:</span>
                                                <span class="font-semibold">{{ number_format($sale['total'], 0) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="p-4 text-center text-gray-400 block md:table-cell">No wholesale sales recorded.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                {{-- Footer Total Row --}}
                                <tfoot class="block md:table-footer-group mt-2 md:mt-0">
                                    <tr class="block md:table-row bg-gray-100 font-bold border-t-2 border-gray-300 md:border-0 rounded-lg md:rounded-none p-2 md:p-0">
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                            <span class="md:hidden font-bold">Total:</span>
                                            <span>Total Wholesale</span>
                                        </td>
                                        <td class="hidden md:table-cell"></td>
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                            <span class="md:hidden font-bold text-gray-500">Total Weight:</span>
                                            <span>{{ number_format($totals['totalWholesaleWeight'], 2) }}kg</span>
                                        </td>
                                        <td class="hidden md:table-cell"></td>
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block text-indigo-600 text-lg">
                                            <span class="md:hidden font-bold text-gray-500">Revenue:</span>
                                            <span>{{ number_format($totals['totalWholesaleRevenue'], 0) }} PKR</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 my-6"></div>

                    <div>
                        <h2 class="font-bold text-lg sm:text-xl text-gray-800 mb-4 px-1">2. Permanent Customer Sales</h2>

                        <div class="block w-full">
                            <table class="w-full text-sm border-collapse table-auto">
                                <thead class="hidden md:table-header-group bg-gray-100 rounded-t-xl">
                                    <tr>
                                        <th class="p-3 text-left text-gray-700 font-semibold rounded-tl-xl">Customer Name</th>
                                        <th class="p-3 text-left text-gray-700 font-semibold">Items Detail</th>
                                        <th class="p-3 text-left text-gray-700 font-semibold">Total Weight (KG)</th>
                                        <th class="p-3 text-right text-gray-700 font-semibold rounded-tr-xl">Total Amount (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody class="block md:table-row-group bg-white divide-y divide-gray-200">
                                    @forelse ($permanentSales as $saleId => $sale)
                                        @php
                                            $currentSaleWeight = 0;
                                        @endphp
                                        <tr class="block md:table-row mb-4 md:mb-0 border md:border-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0 hover:bg-gray-50 transition border-gray-200">
                                            
                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0 align-top">
                                                <span class="md:hidden font-bold text-gray-500">Customer:</span>
                                                <div>
                                                    <div class="font-medium text-gray-700">{{ $sale['customer_name'] }}</div>
                                                    <div class="text-xs text-gray-500">({{ $sale['sale_date'] }})</div>
                                                </div>
                                            </td>

                                            <td class="block md:table-cell p-2 md:p-3 md:block border-b md:border-b-0">
                                                <div class="md:hidden font-bold text-gray-500 mb-1">Items:</div>
                                                <div class="flex flex-wrap gap-1 justify-end md:justify-start">
                                                    @foreach($sale['items'] as $item)
                                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded inline-block">
                                                            {{ $item->product_category }}: {{ number_format($item->weight_kg, 2) }}kg @ {{ number_format($item->rate_pkr, 0) }}
                                                        </span>
                                                        @php $currentSaleWeight += $item->weight_kg; @endphp
                                                    @endforeach
                                                </div>
                                            </td>

                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0 align-top">
                                                <span class="md:hidden font-bold text-gray-500">Total Weight:</span>
                                                <span>{{ number_format($currentSaleWeight, 2) }}kg</span>
                                            </td>

                                            <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block align-top">
                                                <span class="md:hidden font-bold text-gray-500">Amount:</span>
                                                <span class="font-semibold">{{ number_format($sale['total_sale_amount'], 0) }}</span>
                                            </td>
                                        </tr>
                                        @php
                                            $totalPermanentWeight += $currentSaleWeight;
                                            $totalPermanentRevenue += $sale['total_sale_amount'];
                                        @endphp
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-gray-400 block md:table-cell">No hotel/permanent sales recorded.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="block md:table-footer-group mt-2 md:mt-0">
                                    <tr class="block md:table-row bg-gray-100 font-bold border-t-2 border-gray-300 md:border-0 rounded-lg md:rounded-none p-2 md:p-0">
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                            <span class="md:hidden font-bold">Total:</span>
                                            <span>Total Permanent</span>
                                        </td>
                                        <td class="hidden md:table-cell"></td>
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                            <span class="md:hidden font-bold text-gray-500">Total Weight:</span>
                                            <span>{{ number_format($totalPermanentWeight, 2) }}kg</span>
                                        </td>
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block text-indigo-600 text-lg">
                                            <span class="md:hidden font-bold text-gray-500">Revenue:</span>
                                            <span>{{ number_format($totalPermanentRevenue, 0) }} PKR</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 my-6"></div>

                    <div>
                        <h2 class="font-bold text-lg sm:text-xl text-gray-800 mb-4 px-1">3. Shop/Retail Sales (Aggregated)</h2>

                        <div class="block w-full">
                            <table class="w-full text-sm border-collapse table-auto">
                                <thead class="hidden md:table-header-group bg-gray-100 rounded-t-xl">
                                    <tr>
                                        <th class="p-3 text-left text-gray-700 font-semibold rounded-tl-xl">Category</th>
                                        <th class="p-3 text-left text-gray-700 font-semibold">Total Weight (KG)</th>
                                        <th class="p-3 text-left text-gray-700 font-semibold">Avg Rate (PKR)</th>
                                        <th class="p-3 text-right text-gray-700 font-semibold rounded-tr-xl">Total Revenue (PKR)</th>
                                    </tr>
                                </thead>
                                <tbody class="block md:table-row-group bg-white divide-y divide-gray-200">
                                    @forelse($retailSalesAggregation as $category => $data)
                                        @if ($data['weight'] > 0)
                                            <tr class="block md:table-row mb-4 md:mb-0 border md:border-0 rounded-lg md:rounded-none shadow-sm md:shadow-none p-4 md:p-0 hover:bg-gray-50 transition border-gray-200">
                                                
                                                <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                    <span class="md:hidden font-bold text-gray-500">Category:</span>
                                                    <span class="font-medium text-gray-700">{{ $category }}</span>
                                                </td>

                                                <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                    <span class="md:hidden font-bold text-gray-500">Weight:</span>
                                                    <span>{{ number_format($data['weight'], 2) }}kg</span>
                                                </td>

                                                <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                                    <span class="md:hidden font-bold text-gray-500">Avg Rate:</span>
                                                    <span class="text-gray-600">
                                                        @if ($data['count'] > 0)
                                                            {{ number_format($data['total_rate'] / $data['count'], 2) }}
                                                        @else
                                                            0.00
                                                        @endif
                                                    </span>
                                                </td>

                                                <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                                    <span class="md:hidden font-bold text-gray-500">Revenue:</span>
                                                    <span class="font-semibold">{{ number_format($data['revenue'], 0) }}</span>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-gray-400 block md:table-cell">No retail sales recorded.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="block md:table-footer-group mt-2 md:mt-0">
                                    <tr class="block md:table-row bg-gray-100 font-bold border-t-2 border-gray-300 md:border-0 rounded-lg md:rounded-none p-2 md:p-0">
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                            <span class="md:hidden font-bold">Total:</span>
                                            <span>Total Retail</span>
                                        </td>
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block">
                                            <span class="md:hidden font-bold text-gray-500">Total Weight:</span>
                                            <span>{{ number_format($totals['totalRetailWeight'], 2) }}kg</span>
                                        </td>
                                        <td class="hidden md:table-cell"></td>
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block text-indigo-600 text-lg">
                                            <span class="md:hidden font-bold text-gray-500">Revenue:</span>
                                            <span>{{ number_format($totals['totalRetailRevenue'], 0) }} PKR</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="bg-indigo-600 text-white rounded-xl shadow p-4 sm:p-6 font-semibold mt-8">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-base sm:text-lg">
                            <div class="font-bold text-center md:text-left w-full md:w-auto border-b md:border-0 border-indigo-500 pb-2 md:pb-0">
                                Grand Total for <span class="whitespace-nowrap">{{ Carbon\Carbon::parse($date)->format('F d, Y') }}</span>
                            </div>
                            <div class="flex flex-col sm:flex-row w-full md:w-auto space-y-2 sm:space-y-0 sm:space-x-8 justify-center">
                                <div class="flex justify-between sm:block text-center w-full sm:w-auto px-4 sm:px-0">
                                    <span class="sm:hidden font-light opacity-80">Total Weight:</span>
                                    <span>Weight: <span class="font-extrabold text-xl ml-1">{{ number_format($totals['grandTotalWeight'], 2) }}kg</span></span>
                                </div>
                                <div class="flex flex-col sm:flex-row w-full md:w-auto space-y-2 sm:space-y-0 sm:space-x-8 pl-5 justify-center">
                                    <span>Revenue: <span class="font-extrabold text-xl ml-1">{{ number_format($totals['grandTotalRevenue'], 0) }} PKR</span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>
@endsection