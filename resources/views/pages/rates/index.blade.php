@extends('layouts.main')

@section('content')
    <div class="flex">
        <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">

            <div class="max-w-7xl mx-auto">

                <!-- PAGE TITLE -->
                <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight">
                    Daily Rates & Pricing
                </h1>

                <!-- SUCCESS MESSAGE -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-xl mb-6 shadow">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- FORM -->
                <form id="daily-rates-form" method="POST" action="{{ route('admin.rates.store') }}">
                    @csrf

                    <!-- HIDDEN FIELDS -->
                    <input type="hidden" name="supplier_id" id="hidden-supplier-id"
                        value="{{ $suppliers->first()->id ?? '' }}">
                    <input type="hidden" name="base_effective_cost" id="hidden-base-cost"
                        value="{{ $defaultData['base_effective_cost'] ?? 0.00 }}">

                    <!-- SUPPLIER SELECT -->
                  <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8
    @if($defaultData['is_historical'] ?? false) hidden @endif">

    <h2 class="font-bold text-xl text-gray-700 mb-5">Select Supplier & Date</h2>

    <!-- GRID ROW -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Supplier Dropdown -->
        <div>
            <label class="font-semibold text-gray-700 block mb-2">Supplier</label>
            <select id="supplier-dropdown"
                class="w-full border border-gray-300 p-3 rounded-xl text-gray-700
                focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @if ($loop->first) selected @endif>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date Picker -->
        <div>
            <label class="font-semibold text-gray-700 block mb-2">Select Date</label>
            <input type="date" id="rate-date"
                value="{{ $targetDate ?? now()->toDateString() }}"
                class="w-full border border-gray-300 p-3 rounded-xl text-gray-700
                focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm">
        </div>

    </div>
</div>


                    <!-- COST REFERENCE -->
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">
                            Cost Reference
                            <span class="text-blue-600 text-sm">
                                @if($defaultData['is_historical'] ?? false)
                                    (Saved Rate)
                                @else
                                    (Live Calculation)
                                @endif
                            </span>
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- BASE COST -->
                            <div class="bg-blue-600 text-white text-center p-6 rounded-2xl shadow-md">
                                <p class="text-lg font-semibold opacity-90">Base Effective Cost</p>
                                <span id="base-cost" class="block text-3xl font-extrabold mt-2">
                                    {{ number_format($defaultData['base_effective_cost'] ?? 0.00, 2) }} PKR/kg
                                </span>
                            </div>

                            <!-- NET STOCK -->
                            <div class="bg-green-600 text-white text-center p-6 rounded-2xl shadow-md">
                                <p class="text-lg font-semibold opacity-90">Net Stock Available</p>
                                <span id="net-stock" class="block text-3xl font-extrabold mt-2">
                                    {{ number_format($defaultData['net_stock_available'] ?? 0.00, 2) }} KG
                                </span>
                            </div>

                        </div>

                        @if($defaultData['is_historical'] ?? false)
                            <p class="text-red-600 text-sm mt-4 font-semibold">
                                This view is locked for historical data from {{ $targetDate }}.
                            </p>
                        @endif
                    </div>

                    <!-- BULK & CREDIT RATES -->
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Bulk & Credit Rates</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="font-semibold text-gray-700 block mb-1">Wholesale (Truck)</label>
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="wholesale_rate"
                                        value="{{ number_format($defaultData['wholesale_rate'] ?? 610, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                <p class="text-green-600 text-xs mt-1">+10 PKR Margin</p>
                            </div>

                            <div>
                                <label class="font-semibold text-gray-700 block mb-1">Permanent (Hotels)</label>
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="permanent_rate"
                                        value="{{ number_format($defaultData['permanent_rate'] ?? 630, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                </div>
                                <p class="text-green-600 text-xs mt-1">+30 PKR Margin</p>
                            </div>

                        </div>
                    </div>

                    <!-- RETAIL RATES -->
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Shop Retail Rates (Purchun)</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                            @php
                                $retails = [
                                    ['label' => 'Mix', 'name' => 'retail_mix_rate', 'default' => 650, 'margin' => '+50 PKR Margin', 'color' => 'green'],
                                    ['label' => 'Chest', 'name' => 'retail_chest_rate', 'default' => 750, 'margin' => '+150 PKR Margin', 'color' => 'green'],
                                    ['label' => 'Thigh', 'name' => 'retail_thigh_rate', 'default' => 700, 'margin' => '+100 PKR Margin', 'color' => 'green'],
                                    ['label' => 'Piece', 'name' => 'retail_piece_rate', 'default' => 590, 'margin' => '-10 PKR Loss', 'color' => 'red'],
                                ];
                            @endphp

                            @foreach($retails as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    <div
                                        class="flex items-center border border-{{ $item['color'] }}-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}"
                                            value="{{ number_format($defaultData[$item['name']] ?? $item['default'], 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    </div>
                                    <p class="text-{{ $item['color'] }}-600 text-xs mt-1">{{ $item['margin'] }}</p>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    <!-- SUBMIT BUTTON -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold px-8 py-3
                                           rounded-xl shadow-md transition transform hover:scale-[1.02]
                                           @if($defaultData['is_historical'] ?? false) opacity-50 cursor-not-allowed @endif"
                            @if($defaultData['is_historical'] ?? false) disabled @endif>
                            Activate Today’s Rates
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const supplierDropdown = document.getElementById('supplier-dropdown');
            const baseCostSpan = document.getElementById('base-cost');
            const netStockSpan = document.getElementById('net-stock');
            const hiddenSupplierId = document.getElementById('hidden-supplier-id');
            const hiddenBaseCost = document.getElementById('hidden-base-cost');
            const rateDateInput = document.getElementById('rate-date');

            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Format number function
            const formatNumber = (num) => {
                let f = parseFloat(num);
                return f.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            // -------- 1. DATE CHANGE LISTENER --------
            if (rateDateInput) {
                rateDateInput.addEventListener('change', function () {
                    const url = new URL(window.location.href);
                    url.searchParams.set('target_date', this.value);
                    url.searchParams.delete('supplier_id');
                    window.location.href = url.toString();
                });
            }

            // -------- 2. SUPPLIER CHANGE LISTENER --------
            if (supplierDropdown) {
                supplierDropdown.addEventListener('change', function () {

                    const supplierId = this.value;
                    if (!supplierId) return;

                    hiddenSupplierId.value = supplierId;

                    baseCostSpan.innerHTML = '<span class="animate-pulse">Loading...</span>';
                    netStockSpan.innerHTML = '<span class="animate-pulse">Loading...</span>';

                    fetch(`{{ route('admin.rates.supplier.data') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({ supplier_id: supplierId })
                    })
                        .then(res => res.json())
                        .then(data => {
                            hiddenBaseCost.value = data.base_effective_cost;
                            baseCostSpan.textContent = formatNumber(data.base_effective_cost) + " PKR/kg";
                            netStockSpan.textContent = formatNumber(data.net_stock_available) + " KG";
                        })
                        .catch((error) => {
                            console.error("Error fetching supplier data:", error);
                            baseCostSpan.textContent = "0.00 PKR/kg";
                            netStockSpan.textContent = "0.00 KG";
                        });
                });
            }

        });
    </script>


@endsection