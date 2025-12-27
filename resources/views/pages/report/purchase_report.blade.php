@extends('layouts.main')

@section('content')
    <div class="flex">
        <main id="mainContent" class="flex-1 bg-gray-100 w-full max-w-full overflow-x-hidden">
            <div class="p-3 sm:p-6 lg:p-8">

                <div class="bg-white rounded-2xl shadow-lg border border-gray-200">
                    <div class="p-4 sm:p-6">
                        <h2 class="font-bold text-xl sm:text-2xl text-gray-800 mb-6">Purchase Report</h2>

                        {{-- FILTER FORM --}}
                        <form id="purchaseReportFilter" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 sm:gap-6 items-end">
                            @csrf
                            <div class="w-full">
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Select Supplier</label>
                                <select id="supplier_id" name="supplier_id" class="w-full px-3 py-2 sm:px-4 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 text-sm sm:text-base transition">
                                    <option value="">All Suppliers</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="w-full">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Start Date</label>
                                <input type="date" id="start_date" name="start_date" class="w-full px-3 py-2 sm:px-4 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base transition">
                            </div>

                            <div class="w-full">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ now()->format('Y-m-d') }}" class="w-full px-3 py-2 sm:px-4 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm sm:text-base transition">
                            </div>

                            <div class="w-full">
                                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg transition duration-200 text-sm sm:text-base">
                                    Filter Report
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="block w-full md:p-5 pb-3 sm:pb-0 overflow-x-auto">
                        <table class="w-full text-sm border-collapse table-auto">
                            <thead class="hidden md:table-header-group bg-indigo-100 rounded-t-xl">
                                <tr>
                                    <th class="p-3 text-left text-indigo-900 font-semibold rounded-tl-xl">Date & Supplier</th>
                                    <th class="p-3 text-left text-indigo-900 font-semibold">Gross Weight</th>
                                    <th class="p-3 text-left text-indigo-900 font-semibold">Net Live</th>
                                    <th class="p-3 text-left text-indigo-900 font-semibold">Kharch</th>
                                    <th class="p-3 text-left text-indigo-900 font-semibold">Buying (Rate)</th>
                                    <th class="p-3 text-left text-indigo-900 font-semibold rounded-tr-xl">Credit Balance</th>
                                </tr>
                            </thead>

                            <tbody id="purchaseReportTableBody" class="block md:table-row-group bg-white divide-y divide-gray-200">
                                @php
                                    $totalGrossWeight = 0;
                                    $totalNetLiveWeight = 0;
                                    $totalKharchAmount = 0;
                                    $totalBillAmount = 0;
                                @endphp

                                @forelse($purchases as $purchase)
                                    @php
                                        $deadShrink = $purchase->dead_weight + $purchase->shrink_loss;
                                        $netLive = $purchase->gross_weight - $deadShrink;
                                        
                                        $totalGrossWeight += $purchase->gross_weight;
                                        $totalNetLiveWeight += $netLive;
                                        $totalKharchAmount += $purchase->total_kharch;
                                        $totalBillAmount += $purchase->total_payable;
                                    @endphp

                                    <tr class="block md:table-row mb-4 md:mb-0 border md:border-0 rounded-lg shadow-sm md:shadow-none mx-4 md:mx-0 p-4 md:p-0 hover:bg-gray-50 transition">
                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                            <span class="md:hidden font-bold text-gray-500">Supplier:</span>
                                            <span class="text-gray-900 font-medium">
                                                {{ \Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y') }} – {{ $purchase->supplier->name ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                            <span class="md:hidden font-bold text-gray-500">Gross Wgt:</span>
                                            <span>{{ number_format($purchase->gross_weight, 2) }} kg</span>
                                        </td>

                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block text-green-600 font-medium border-b md:border-b-0">
                                            <span class="md:hidden font-bold text-gray-500">Net Wgt:</span>
                                            <span>{{ number_format($netLive, 2) }} kg</span>
                                        </td>

                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                            <span class="md:hidden font-bold text-gray-500">Kharch:</span>
                                            <span class="text-red-500">{{ number_format($purchase->total_kharch, 2) }}</span>
                                        </td>

                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block border-b md:border-b-0">
                                            <span class="md:hidden font-bold text-gray-500">Rate:</span>
                                            <span>{{ number_format($purchase->buying_rate, 2) }}</span>
                                        </td>

                                        <td class="block md:table-cell p-2 md:p-3 flex justify-between md:block font-bold text-indigo-700">
                                            <span class="md:hidden font-bold text-gray-500">Total Bill:</span>
                                            <span>{{ number_format($purchase->total_payable, 2) }} PKR</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-400">No purchase records found.</td>
                                    </tr>
                                @endforelse

                                {{-- TOTAL ROW --}}
                                <tr class="block md:table-row bg-gray-100 font-semibold border-t-2 border-gray-300 md:border-0 mx-4 md:mx-0 rounded-lg md:rounded-none mt-2">
                                    <td class="block md:table-cell p-3 flex justify-between md:block">
                                        <span class="md:hidden font-bold">Total:</span>
                                        <span class="hidden md:inline">GRAND TOTAL</span>
                                    </td>
                                    <td class="block md:table-cell p-3 flex justify-between md:block text-gray-700">
                                        <span class="md:hidden font-bold text-gray-500">Total Gross:</span>
                                        {{ number_format($totalGrossWeight, 2) }} kg
                                    </td>
                                    <td class="block md:table-cell p-3 flex justify-between md:block text-green-600">
                                        <span class="md:hidden font-bold text-gray-500">Total Net:</span>
                                        {{ number_format($totalNetLiveWeight, 2) }} kg
                                    </td>
                                    <td class="block md:table-cell p-3 flex justify-between md:block text-red-500">
                                        <span class="md:hidden font-bold text-gray-500">Total Kharch:</span>
                                        {{ number_format($totalKharchAmount, 2) }}
                                    </td>
                                    <td class="block md:table-cell p-3 hidden md:table-cell">—</td>
                                    <td class="block md:table-cell p-3 flex justify-between md:block text-indigo-800 font-bold">
                                        <span class="md:hidden font-bold text-gray-500">Total Bill:</span>
                                        {{ number_format($totalBillAmount, 2) }} PKR
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @push('scripts')
        <script>
            document.getElementById('purchaseReportFilter').addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                const tableBody = document.getElementById('purchaseReportTableBody');

                tableBody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-gray-400">Loading...</td></tr>';

                fetch('{{ route('admin.reports.purchase.filter') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = data.html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = '<tr><td colspan="6" class="p-4 text-center text-red-500">Error loading report data.</td></tr>';
                });
            });
        </script>
    @endpush
@endsection