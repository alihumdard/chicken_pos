@extends('layouts.main')

@section('content')
    <div class="w-full max-w-[100vw] overflow-x-hidden bg-gray-100 min-h-screen flex flex-col">
        <main class="flex-1 w-full max-w-full p-2 sm:p-6 lg:p-8">
            <div class="space-y-6 w-full max-w-full">

                <div class="px-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 tracking-tight">
                        Profit & Loss Analysis
                    </h1>
                </div>

                {{-- Filter Form (Stock report jaisa design) --}}
                <form method="GET" action="{{ route('admin.reports.pnl') }}"
                    class="bg-white p-4 rounded-xl shadow-md grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 w-full">
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>
                    <div class="w-full sm:col-span-2 md:col-span-2 flex items-end">
                        <button type="submit" class="w-full rounded-md bg-blue-600 py-2 px-4 text-sm font-medium text-white hover:bg-blue-700">
                            Apply Filter
                        </button>
                    </div>
                </form>

                {{-- Summary Cards (Stock report jaisa design) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full text-center">
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Revenue</p>
                        <h2 class="text-xl font-bold text-green-600 truncate">{{ number_format($totalRevenue, 0) }}</h2>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-red-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Cost</p>
                        <h2 class="text-xl font-bold text-red-500 truncate">{{ number_format($totalCogs, 0) }}</h2>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-yellow-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Kharch</p>
                        <h2 class="text-xl font-bold text-yellow-600 truncate">{{ number_format($poultryExpenses, 0) }}</h2>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-indigo-600">
                        <p class="text-xs text-gray-500 font-bold uppercase">Net Profit</p>
                        <h2 class="text-xl font-bold {{ $totalNetProfit >= 0 ? 'text-indigo-700' : 'text-red-700' }} truncate">
                            {{ number_format($totalNetProfit, 0) }}
                        </h2>
                    </div>
                </div>

                {{-- Daily Breakdown Table (Stock report jaisa layout) --}}
                <div class="bg-white p-4 shadow rounded-xl w-full flex flex-col">
                    <h3 class="font-semibold mb-4 text-gray-700">Daily Financial Breakdown</h3>
                    <div class="w-full overflow-x-auto ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Date</th>
                                    <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Revenue</th>
                                    <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Cost</th>
                                    <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Kharch</th>
                                    <th class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Net Profit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($dailyReport as $day)
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($day['date'])->format('d/m/y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-emerald-600 font-bold">
                                            {{ number_format($day['revenue']) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-red-500">
                                            ({{ number_format($day['cost']) }})
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-amber-600">
                                            ({{ number_format($day['kharch']) }})
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-black {{ $day['net_profit'] >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                                            {{ number_format($day['net_profit']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="p-4 text-center text-sm text-gray-500">No records found.</td></tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td class="py-3.5 pl-4 pr-3 text-sm text-gray-900">Total</td>
                                    <td class="px-3 py-3.5 text-right text-sm text-emerald-700">{{ number_format($totalRevenue) }}</td>
                                    <td class="px-3 py-3.5 text-right text-sm text-red-600">({{ number_format($totalCogs) }})</td>
                                    <td class="px-3 py-3.5 text-right text-sm text-amber-700">({{ number_format($poultryExpenses) }})</td>
                                    <td class="px-3 py-3.5 text-right text-sm {{ $totalNetProfit >= 0 ? 'text-indigo-700' : 'text-red-700' }}">
                                        {{ number_format($totalNetProfit) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection