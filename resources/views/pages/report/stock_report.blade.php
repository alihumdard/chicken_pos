@extends('layouts.main')

@section('content')
    <div class="w-full max-w-[100vw] overflow-x-hidden bg-gray-100 min-h-screen flex flex-col">
        <main class="flex-1 w-full max-w-full p-2 sm:p-6 lg:p-8">
            <div class="space-y-6 w-full max-w-full">

                <div class="px-1 flex justify-between items-center">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-800 tracking-tight">Stock Analysis</h1>
                    <div class="bg-indigo-600 text-white px-4 py-2 rounded-xl shadow-lg">
                        <span class="text-xs font-bold uppercase block">Current Net Stock</span>
                        <span class="text-xl font-black">{{ number_format($current_net_stock, 2) }} KG</span>
                    </div>
                </div>

                {{-- Filter --}}
                <form method="GET" action="{{ route('admin.reports.stock') }}" class="bg-white p-4 rounded-xl shadow-md grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-1"><input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-md border-gray-300"></div>
                    <div class="sm:col-span-1"><input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-md border-gray-300"></div>
                    <div class="sm:col-span-2"><button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md font-bold">Update Inventory View</button></div>
                </form>

                {{-- Stock Specific Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Input Weight</p>
                        <h2 class="text-2xl font-black text-blue-600">{{ number_format($total_purchased_weight, 2) }} KG</h2>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Sold (Output)</p>
                        <h2 class="text-2xl font-black text-green-600">{{ number_format($totalSoldWeightToDate, 2) }} KG</h2>
                    </div>
                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-red-500">
                        <p class="text-xs text-gray-500 font-bold uppercase">Total Shrinkage</p>
                        <h2 class="text-2xl font-black text-red-600">{{ number_format($totalShrink, 2) }} KG</h2>
                    </div>
                </div>

                {{-- Chart (Weight Focus) --}}
                <div class="bg-white p-4 shadow rounded-xl w-full">
                    <h3 class="font-bold mb-4 text-gray-700">Weight In vs Weight Out</h3>
                    <div class="relative w-full h-64"><canvas id="yieldChart"></canvas></div>
                </div>

                {{-- Table (Weight Focus) --}}
                <div class="bg-white p-4 shadow rounded-xl overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-[10px] font-bold text-gray-500 uppercase">
                            <tr>
                                <th class="p-3 text-left">Date</th>
                                <th class="p-3 text-right">In-Weight</th>
                                <th class="p-3 text-right">Out-Weight</th>
                                <th class="p-3 text-right">Shrink Loss</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($dailyReport as $day)
                            <tr>
                                <td class="p-3 text-sm font-medium">{{ $day['date'] }}</td>
                                <td class="p-3 text-right text-blue-600 font-bold">{{ number_format($day['input_weight'], 2) }} kg</td>
                                <td class="p-3 text-right text-green-600 font-bold">{{ number_format($day['output_weight'], 2) }} kg</td>
                                <td class="p-3 text-right text-red-500 font-bold">{{ number_format($day['shrink'], 2) }} kg</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Chart(document.getElementById("yieldChart"), {
                type: "bar",
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        { label: "Weight In", backgroundColor: "#3b82f6", data: @json($chartInputData) },
                        { label: "Weight Out", backgroundColor: "#10b981", data: @json($chartOutputData) }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        });
    </script>
    @endpush
@endsection