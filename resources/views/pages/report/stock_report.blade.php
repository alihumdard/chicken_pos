@extends('layouts.main')

@section('content')

    <div class="flex">

        <!-- MAIN PAGE -->
        <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">
            <!-- PAGE CONTENT -->
            <div class="p-4 space-y-6">

                <h1 class="text-3xl font-extrabold text-gray-800 mb-6 tracking-tight">
                    Profit & Loss Analysis
                </h1>

                {{-- Date Range Filter Form --}}
                <form method="GET" action="{{ route('admin.reports.pnl') }}"
                    class="mb-8 p-4 bg-white rounded-xl shadow-md grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="col-span-1">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="start_date" name="start_date"
                            value="{{ $startDate ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>

                    <div class="col-span-1">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ $endDate ?? \Carbon\Carbon::now()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm">
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <button type="submit"
                            class="w-full justify-center rounded-md border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Apply Filter
                        </button>
                    </div>
                </form>

                <!-- TOP CARDS (Dynamic Data) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- 1. Total Revenue --}}
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-500">
                        <p class="text-gray-600">Total Revenue</p>
                        <h2 class="text-2xl font-bold text-green-600">{{ number_format($totalRevenue, 0) }} PKR</h2>
                    </div>

                    {{-- 2. Cost of Goods Sold (COGS) --}}
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-red-500">
                        <p class="text-gray-600">Total Cost (COGS - Simplified)</p>
                        <h2 class="text-2xl font-bold text-red-500">{{ number_format($totalCogs, 0) }} PKR</h2>
                    </div>

                    {{-- 3. Total Expenses --}}
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-yellow-500">
                        <p class="text-gray-600">Total Expenses (Mock)</p>
                        <h2 class="text-2xl font-bold text-yellow-600">{{ number_format($totalExpenses, 0) }} PKR</h2>
                    </div>

                    {{-- 4. NET PROFIT --}}
                    @php $netProfitColor = $totalNetProfit >= 0 ? 'text-green-700' : 'text-red-700'; @endphp
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-600">
                        <p class="text-gray-600">NET PROFIT / LOSS</p>
                        <h2 class="text-2xl font-bold {{ $netProfitColor }}">{{ number_format($totalNetProfit, 0) }} PKR
                        </h2>
                    </div>

                </div>

                <!-- CHART (Dynamic Data) -->
                <div class="bg-white p-5 shadow rounded-xl">
                    <h3 class="font-semibold mb-3">Total Input vs Output Weight Analysis</h3>
                    <div class="h-64">
                        <canvas id="yieldChart"></canvas>
                    </div>
                </div>

                <!-- DAILY TABLE (Dynamic Data) -->
                <div class="bg-white p-5 shadow rounded-xl overflow-x-auto">
                    <h3 class="font-semibold mb-3">Daily Breakdown ({{ $startDate }} to {{ $endDate }})</h3>
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="p-2 text-left">Date</th>
                                <th class="p-2 text-right">Total Revenue (PKR)</th>
                                <th class="p-2 text-right">Total Cost (PKR)</th>
                                <th class="p-2 text-right">Expenses (PKR)</th>
                                <th class="p-2 text-right">Net Profit / Loss (PKR)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($dailyReport as $day)
                                @php $rowProfitColor = $day['net_profit'] >= 0 ? 'text-green-600' : 'text-red-500'; @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="p-2">{{ \Carbon\Carbon::parse($day['date'])->format('d/m/Y') }}</td>
                                    <td class="p-2 text-right">{{ number_format($day['revenue'], 0) }}</td>
                                    <td class="p-2 text-right text-red-500">({{ number_format($day['cost'], 0) }})</td>
                                    <td class="p-2 text-right text-yellow-600">({{ number_format($day['expenses'], 0) }})</td>
                                    <td class="p-2 text-right font-bold {{ $rowProfitColor }}">
                                        {{ number_format($day['net_profit'], 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-500">No data found for the selected period.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="font-bold bg-gray-100 border-t-2">
                                <td class="p-2">Grand Totals</td>
                                <td class="p-2 text-right text-blue-600">{{ number_format($totalRevenue, 0) }}</td>
                                <td class="p-2 text-right text-red-600">({{ number_format($totalCogs, 0) }})</td>
                                <td class="p-2 text-right text-yellow-700">({{ number_format($totalExpenses, 0) }})</td>
                                @php $footerProfitColor = $totalNetProfit >= 0 ? 'text-green-700' : 'text-red-700'; @endphp
                                <td class="p-2 text-right {{ $footerProfitColor }} text-lg">
                                    {{ number_format($totalNetProfit, 0) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>

        </main>

    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chartLabels = @json($chartLabels);
                const chartInputData = @json($chartInputData);
                const chartOutputData = @json($chartOutputData);

                new Chart(document.getElementById("yieldChart"), {
                    type: "bar",
                    data: {
                        labels: chartLabels,
                        datasets: [
                            {
                                label: "Input Weight (Purchased)",
                                backgroundColor: "#3b82f6", // Blue
                                data: chartInputData
                            },
                            {
                                label: "Output Weight (Estimated Sold)",
                                backgroundColor: "#ef4444", // Red
                                data: chartOutputData
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Weight (KG)'
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection