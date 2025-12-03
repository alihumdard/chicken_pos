@extends('layouts.main')

@section('content')
<div class="flex">
    <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">
        <div class="p-4">

            {{-- Card Container --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 overflow-x-auto border border-gray-200">
                {{-- Page Title --}}
                <h2 class="font-bold text-2xl text-gray-800 mb-6">Purchase Report</h2>

                {{-- FILTER FORM --}}
                <form id="purchaseReportFilter" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    @csrf

                    {{-- Supplier Dropdown --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Select Supplier</label>
                        <select id="supplier_id" name="supplier_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-gray-700 transition">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Start Date --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" id="start_date" name="start_date"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" id="end_date" name="end_date" value="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm focus:ring-indigo-500 focus:border-indigo-500 transition">
                    </div>

                    {{-- Filter Button --}}
                    <div>
                        <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl shadow-lg transition duration-200">
                            Filter Report
                        </button>
                    </div>
                </form>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead class="bg-indigo-100 rounded-t-xl">
                            <tr>
                                <th class="p-3 text-left text-indigo-900 font-semibold rounded-tl-xl">Date & Supplier</th>
                                <th class="p-3 text-left text-indigo-900 font-semibold">Gross Weight (kg)</th>
                                <th class="p-3 text-left text-indigo-900 font-semibold">Dead/Shrink</th>
                                <th class="p-3 text-left text-indigo-900 font-semibold">Net Live Wgt</th>
                                <th class="p-3 text-left text-indigo-900 font-semibold rounded-tr-xl">Rate per kg (PKR)</th>
                            </tr>
                        </thead>

                        <tbody id="purchaseReportTableBody" class="bg-white divide-y divide-gray-200">
                            @php
                                $totalGrossWeight = 0;
                                $totalDeadShrinkWeight = 0;
                                $totalNetLiveWeight = 0;
                            @endphp

                            @forelse($purchases as $purchase)
                                @php
                                    $deadShrink = $purchase->dead_weight + $purchase->shrink_loss;
                                    $netLive = $purchase->gross_weight - $deadShrink;
                                    $totalGrossWeight += $purchase->gross_weight;
                                    $totalDeadShrinkWeight += $deadShrink;
                                    $totalNetLiveWeight += $netLive;
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-3">{{ \Carbon\Carbon::parse($purchase->created_at)->format('d/m/Y') }} – {{ $purchase->supplier->name ?? 'N/A' }}</td>
                                    <td class="p-3">{{ number_format($purchase->gross_weight, 2) }}kg</td>
                                    <td class="p-3 text-red-500 font-medium">-{{ number_format($deadShrink, 2) }}kg</td>
                                    <td class="p-3 text-green-600 font-medium">{{ number_format($netLive, 2) }}kg</td>
                                    <td class="p-3">{{ number_format($purchase->buying_rate, 2) }} PKR</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-gray-400">No purchase records found for the selected period.</td>
                                </tr>
                            @endforelse

                            {{-- TOTAL ROW --}}
                            <tr class="bg-gray-100 font-semibold">
                                <td class="p-3">Total</td>
                                <td class="p-3">{{ number_format($totalGrossWeight, 2) }}kg</td>
                                <td class="p-3 text-red-500">-{{ number_format($totalDeadShrinkWeight, 2) }}kg</td>
                                <td class="p-3 text-green-600">{{ number_format($totalNetLiveWeight, 2) }}kg</td>
                                <td class="p-3">—</td>
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
document.getElementById('purchaseReportFilter').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const tableBody = document.getElementById('purchaseReportTableBody');

    tableBody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-gray-400">Loading...</td></tr>';

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
        console.error('Error fetching data:', error);
        tableBody.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-red-500">Error loading report data.</td></tr>';
    });
});
</script>
@endpush
@endsection
