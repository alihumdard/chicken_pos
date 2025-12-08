@extends('layouts.main')

@section('content')

    {{-- 
        CRITICAL FIXES:
        1. max-w-[100vw]: Forces the container never to exceed the viewport width.
        2. overflow-x-hidden: Hides any accidental spillover.
    --}}
    <div class="w-full max-w-[100vw] overflow-x-hidden bg-gray-100 min-h-screen flex flex-col">

        {{-- Reduced padding on mobile (p-2) to give more room for content --}}
        <main class="flex-1 w-full max-w-full p-2 sm:p-6 lg:p-8">
            
            <div class="space-y-6 w-full max-w-full">

                {{-- Page Title --}}
                <div class="px-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 tracking-tight">
                        Profit & Loss Analysis
                    </h1>
                </div>

                {{-- Filter Form --}}
                <form method="GET" action="{{ route('admin.reports.pnl') }}"
                    class="bg-white p-4 rounded-xl shadow-md grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 w-full">

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date"
                            value="{{ $startDate ?? \Carbon\Carbon::now()->startOfMonth()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date"
                            value="{{ $endDate ?? \Carbon\Carbon::now()->toDateString() }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm sm:text-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="w-full sm:col-span-2 md:col-span-2 flex items-end">
                        <button type="submit"
                            class="w-full rounded-md bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            Apply Filter
                        </button>
                    </div>
                </form>

                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                    {{-- Card 1 --}}
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Revenue</p>
                        <h2 class="text-xl font-bold text-green-600 truncate">{{ number_format($totalRevenue, 0) }}</h2>
                    </div>
                    {{-- Card 2 --}}
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-red-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Cost</p>
                        <h2 class="text-xl font-bold text-red-500 truncate">{{ number_format($totalCogs, 0) }}</h2>
                    </div>
                    {{-- Card 3 --}}
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-yellow-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Expenses</p>
                        <h2 class="text-xl font-bold text-yellow-600 truncate">{{ number_format($totalExpenses, 0) }}</h2>
                    </div>
                    {{-- Card 4 --}}
                    @php $netProfitColor = $totalNetProfit >= 0 ? 'text-green-700' : 'text-red-700'; @endphp
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-600">
                        <p class="text-xs text-gray-500 font-bold uppercase">Net Profit</p>
                        <h2 class="text-xl font-bold {{ $netProfitColor }} truncate">
                            {{ number_format($totalNetProfit, 0) }}
                        </h2>
                    </div>
                </div>

                {{-- Chart Section --}}
                {{-- overflow-hidden here prevents the canvas from pushing width out --}}
                <div class="bg-white p-4 shadow rounded-xl w-full overflow-hidden">
                    <h3 class="font-semibold mb-4 text-gray-700 text-sm sm:text-base">Weight Analysis</h3>
                    <div class="relative w-full h-64">
                        <canvas id="yieldChart"></canvas>
                    </div>
                </div>

                {{-- Table Section --}}
                <div class="bg-white p-4 shadow rounded-xl w-full flex flex-col">
                    <h3 class="font-semibold mb-4 text-gray-700 text-sm sm:text-base">Daily Breakdown</h3>

                    {{-- 
                        THE FIX:
                        - ring-1 ring-black ring-opacity-5: Adds a subtle border without adding width.
                        - overflow-x-auto: Allows scrolling INSIDE this div.
                        - max-w-full: Ensures it fits in the parent.
                    --}}
                    <div class="w-full overflow-x-auto ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 whitespace-nowrap">Date</th>
                                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 whitespace-nowrap">Revenue</th>
                                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 whitespace-nowrap">Cost</th>
                                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 whitespace-nowrap">Exp.</th>
                                    <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900 whitespace-nowrap">Net</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse($dailyReport as $day)
                                    @php $rowProfitColor = $day['net_profit'] >= 0 ? 'text-green-600' : 'text-red-500'; @endphp
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($day['date'])->format('d/m/y') }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-500">
                                            {{ number_format($day['revenue']) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-red-500">
                                            ({{ number_format($day['cost']) }})
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-yellow-600">
                                            ({{ number_format($day['expenses']) }})
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-bold {{ $rowProfitColor }}">
                                            {{ number_format($day['net_profit']) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-sm text-gray-500">No data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-50 font-bold">
                                <tr>
                                    <td class="py-3.5 pl-4 pr-3 text-sm text-gray-900 whitespace-nowrap">Total</td>
                                    <td class="px-3 py-3.5 text-right text-sm text-blue-600 whitespace-nowrap">{{ number_format($totalRevenue) }}</td>
                                    <td class="px-3 py-3.5 text-right text-sm text-red-600 whitespace-nowrap">({{ number_format($totalCogs) }})</td>
                                    <td class="px-3 py-3.5 text-right text-sm text-yellow-700 whitespace-nowrap">({{ number_format($totalExpenses) }})</td>
                                    @php $footerProfitColor = $totalNetProfit >= 0 ? 'text-green-700' : 'text-red-700'; @endphp
                                    <td class="px-3 py-3.5 text-right text-sm {{ $footerProfitColor }} whitespace-nowrap">
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
                                label: "Input",
                                backgroundColor: "#3b82f6",
                                data: chartInputData,
                                barPercentage: 0.7,
                            },
                            {
                                label: "Output",
                                backgroundColor: "#ef4444",
                                data: chartOutputData,
                                barPercentage: 0.7,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                position: 'top',
                                labels: { boxWidth: 10, font: { size: 10 } } // Smaller legend for mobile
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { font: { size: 10 } }
                            },
                            x: {
                                ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 45 }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush

@endsection