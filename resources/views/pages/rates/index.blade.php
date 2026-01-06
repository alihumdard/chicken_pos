@extends('layouts.main')

@section('content')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="flex">
        <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">

            <div class="max-w-7xl mx-auto">

                {{-- Display error message --}}
                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            Swal.fire({ icon: 'error', title: 'Error', text: "{{ session('error') }}" });
                        });
                    </script>
                @endif

                <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight">
                    Daily Rates & Pricing
                </h1>

                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            Swal.fire({ icon: 'success', title: 'Success', text: "{{ session('success') }}" });
                        });
                    </script>
                @endif

                <input type="hidden" name="supplier_id" form="daily-rates-form" value="{{ $suppliers->first()->id ?? 1 }}">

                <form id="daily-rates-form" method="POST" action="{{ route('admin.rates.store') }}">
                    @csrf

                    {{-- CONTROL PANEL: Manual Rate + Actions --}}
                    <div class="bg-yellow-50 p-6 rounded-2xl shadow-md border border-yellow-300 mb-8 @if($defaultData['is_historical'] ?? false) hidden @endif">

                        <h2 class="font-bold text-xl text-gray-800 mb-6 flex items-center gap-2 border-b border-yellow-200 pb-2">
                            <i class="fas fa-sliders-h"></i> Pricing Control & Actions
                        </h2>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                            {{-- LEFT SECTION: Manual Override --}}
                            <div class="flex flex-col gap-4 lg:border-r border-yellow-200 lg:pr-8">
                                <h3 class="text-sm font-bold text-yellow-800 uppercase tracking-wide mb-1">
                                    1. Manual add rate (Optional)
                                </h3>

                                <div class="relative w-full">
                                    <input type="number" name="manual_base_cost" id="manual_base_cost"
                                        value="{{ ($defaultData['manual_base_cost'] ?? 0.00) > 0 ? number_format($defaultData['manual_base_cost'], 2, '.', '') : '' }}"
                                        min="0" step="0.01"
                                        class="text-xl font-bold w-full p-3 pr-12 border border-green-500 bg-white rounded-lg text-gray-800 focus:border-green-700 focus:ring-green-700 transition-all shadow-sm"
                                        placeholder="Enter manual rate...">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">PKR</span>
                                </div>

                                <button type="button" id="apply-rate-override"
                                    class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex justify-center items-center gap-2">
                                    <i class="fas fa-check-circle"></i> Apply & Save
                                </button>
                            </div>

                            {{-- RIGHT SECTION: System Actions --}}
                            <div class="flex flex-col justify-between">
                                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wide mb-4 lg:mb-1">
                                    2. Final Actions
                                </h3>

                                <div class="flex flex-col gap-3 h-full justify-center">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex justify-center items-center gap-2 h-full
                                            @if($defaultData['is_historical'] ?? false) opacity-50 cursor-not-allowed @endif"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif>
                                            <i class="fas fa-save"></i> Active Today's Rates
                                        </button>
                                        <a href="{{ route('admin.settings.index') }}"
                                            class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-lg shadow transition-colors flex justify-center items-center gap-2 text-center h-full">
                                            <i class="fas fa-calculator"></i> Set/Change Formula's
                                        </a>
                                    </div>
                                    <p class="text-xs text-gray-500 text-center mt-2">
                                        * Ensure formulas are set before activating.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- END CONTROL PANEL --}}

                    <input type="hidden" name="base_effective_cost" id="hidden-base-cost"
                        value="{{ $defaultData['base_effective_cost'] ?? 0.00 }}">

                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="font-bold text-4xl text-gray-700">
                                Rate
                                <span class="text-blue-600 text-sm block md:inline md:ml-2">
                                    @if($defaultData['is_historical'] ?? false) (Saved Rate) @else (Live Calculation) @endif
                                </span>
                            </h2>
                            <button type="button" id="adjust-stock-btn"
                                class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 font-bold px-3 py-2 rounded-lg text-sm transition flex items-center gap-2">
                                <i class="fas fa-minus-circle"></i> Stock Shrink
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div id="base-cost-box"
                                class="bg-blue-600 text-white text-center p-6 rounded-2xl shadow-md transition-colors">
                                <p class="text-lg font-semibold opacity-90">Basic Rate</p>
                                <span id="base-cost-display" class="block text-3xl font-extrabold mt-2">
                                    {{ number_format($defaultData['base_effective_cost'] ?? 0.00, 2) }} PKR/kg
                                </span>
                            </div>

                        <div class="bg-green-600 text-white p-6 rounded-2xl shadow-md relative overflow-hidden group">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-lg font-semibold opacity-90">Total Net Stock</p>
                                    <div class="block text-4xl font-extrabold mt-1">
                                        <span id="net-stock-value">{{ number_format($defaultData['net_stock_available'] ?? 0.00, 2) }}</span>
                                        <span class="text-xl">KG</span>
                                    </div>
                                </div>
                                {{-- Shop Breakdown Tooltip/Popup --}}
                                <div class="text-right text-xs bg-green-700 bg-opacity-50 p-2 rounded-lg">
                                    @if(isset($defaultData['shop_stock_breakdown']))
                                        @foreach($defaultData['shop_stock_breakdown'] as $shopStock)
                                            <div class="flex justify-between gap-4">
                                                <span class="opacity-80">{{ $shopStock['name'] }}:</span>
                                                <span class="font-bold">{{ number_format($shopStock['stock'], 2) }} kg</span>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons Row --}}
                            <div class="mt-6 flex flex-wrap gap-2">
                                <button type="button" onclick="openStockAdjustmentModal()"
                                    class="bg-white text-green-700 hover:bg-green-50 font-bold px-3 py-2 rounded-lg text-sm shadow transition flex items-center gap-2">
                                    <i class="fas fa-sliders-h"></i> Adjust
                                </button>
                                
                                <button type="button" onclick="openTransferModal()"
                                    class="bg-purple-600 text-white hover:bg-purple-700 border border-purple-500 font-bold px-3 py-2 rounded-lg text-sm shadow transition flex items-center gap-2">
                                    <i class="fas fa-exchange-alt"></i> Transfer
                                </button>
                            </div>
                        </div>
                        </div>

                        @if($defaultData['is_historical'] ?? false)
                            <p class="text-red-600 text-sm mt-4 font-semibold text-center">
                                This view is locked for historical data from {{ $targetDate }}.
                            </p>
                        @endif
                    </div>

                    {{-- Form Inputs (Permanent/Wholesale/Retail) --}}
                    <div class="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div id="permanent_rate_container" class="hidden">
                                <label class="font-semibold text-gray-700 block mb-1">Permanent Rate</label>
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="permanent_rate" id="permanent_rate_input"
                                        value="{{ number_format($defaultData['permanent_rate'] ?? 0.00, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                @php
                                    $pRateFormula = $rateFormulas->get('permanent_rate');
                                    $pRateFormulaText = $pRateFormula ? "×{$pRateFormula->multiply} ÷{$pRateFormula->divide} +{$pRateFormula->plus} -{$pRateFormula->minus}" : 'No Formula';
                                @endphp
                                <p class="text-xs mt-1 text-gray-500" data-key="permanent_rate">
                                    Formula: {{ $pRateFormulaText }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- 🟢 WHOLESALE SECTION (Dynamic) --}}
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10 mt-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6 flex items-center gap-2">
                            <i class="fas fa-chart-line text-gray-600 text-2xl"></i>
                            Wholesale Rates
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- 1. Base Wholesale Rate (Static Input kept for layout) --}}
                            <div id="wholesale_rate_container">
                                <label class="font-semibold text-gray-700 block mb-1">Live</label>
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="wholesale_rate" id="wholesale_rate_input"
                                        value="{{ number_format($defaultData['wholesale_rate'] ?? 0.00, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                @php
                                    $wRateFormula = $rateFormulas->get('wholesale_rate');
                                    $wRateFormulaText = $wRateFormula ? "×{$wRateFormula->multiply} ÷{$wRateFormula->divide} +{$wRateFormula->plus} -{$wRateFormula->minus}" : 'No Formula';
                                @endphp
                                <p class="text-xs mt-1 text-gray-500" data-key="wholesale_rate">
                                    Formula: ( value {{ $wRateFormulaText }} )
                                </p>
                            </div>
                            
                            {{-- 🟢 DYNAMIC LOOP FOR WHOLESALE --}}
                            @foreach($rateFormulas->where('channel', 'wholesale') as $formula)
                                {{-- Skip 'wholesale_rate' if it's in the DB to avoid duplicating the input above --}}
                                @if($formula->rate_key === 'wholesale_rate') @continue @endif

                                <div>
                                    {{-- Title from DB used as Label --}}
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $formula->title }}</label>
                                    
                                    <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                        {{-- Rate Key used as Input Name --}}
                                        <input type="number" name="{{ $formula->rate_key }}" id="{{ $formula->rate_key }}_input"
                                            value="{{ number_format($defaultData[$formula->rate_key] ?? 0.00, 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    
                                    {{-- Dynamic Formula Text --}}
                                    @php
                                        $formulaText = "×".($formula->multiply+0)." ÷".($formula->divide+0)." +".($formula->plus+0)." -".($formula->minus+0);
                                    @endphp
                                    <p class="text-xs mt-1 text-gray-500" data-key="{{ $formula->rate_key }}">
                                        Formula: (value {{ $formulaText }})
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 🟢 RETAIL SECTION (Dynamic) --}}
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10">
                        <h2 class="font-bold text-xl text-gray-700 mb-6 flex items-center gap-2">
                            <i class="fas fa-chart-line text-gray-600 text-2xl pr-2"></i>
                            Shop Retail Rates (Purchun)
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- 🟢 DYNAMIC LOOP FOR RETAIL --}}
                            @foreach($rateFormulas->where('channel', 'retail') as $formula)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $formula->title }}</label>
                                    
                                    <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $formula->rate_key }}" id="{{ $formula->rate_key }}_input"
                                            value="{{ number_format($defaultData[$formula->rate_key] ?? 0.00, 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    
                                    @php
                                        $formulaText = "×".($formula->multiply+0)." ÷".($formula->divide+0)." +".($formula->plus+0)." -".($formula->minus+0);
                                    @endphp
                                    <p class="text-xs mt-1 text-gray-500" data-key="{{ $formula->rate_key }}">
                                        Formula: (value {{ $formulaText }})
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

        {{-- 🟢 STOCK TRANSFER MODAL --}}
    <div id="transferModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 border border-gray-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Transfer Stock</h3>
                <button onclick="document.getElementById('transferModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="{{ route('admin.stock.transfer.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">From (Source)</label>
                        <select name="from_shop_id" class="w-full border p-2 rounded-lg bg-gray-50" required>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-center pt-4">
                        <i class="fas fa-arrow-right text-gray-400"></i>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">To (Destination)</label>
                        <select name="to_shop_id" class="w-full border p-2 rounded-lg bg-gray-50" required>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Weight to Transfer (KG)</label>
                    <input type="number" name="weight" step="0.01" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none font-bold text-purple-700" placeholder="0.00" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-gray-200 outline-none">
                </div>

                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg shadow-lg">
                    Transfer Stock
                </button>
            </form>
        </div>
    </div>


    {{-- 🟢 STOCK ADJUSTMENT / ISSUE MODAL --}}
    <div id="adjustmentModal" class="fixed inset-0 z-50 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 border border-gray-200 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Stock Transfer / Issue</h3>
                <button onclick="document.getElementById('adjustmentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
            </div>
            
            <form action="{{ route('admin.stock.adjustment.store') }}" method="POST" id="adjustmentForm">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">From Shop (Source)</label>
                    {{-- 🟢 UPDATED onchange: added autoToggleShops('source') --}}
                    <select name="shop_id" id="source_shop_select" 
                        onchange="validateStockAvailability(); autoToggleShops('source')" 
                        class="w-full border p-2.5 rounded-lg bg-gray-50 focus:ring-2 focus:ring-red-500 outline-none" required>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" data-stock="{{ $shop->current_stock }}">
                                {{ $shop->name }} (Stock: {{ number_format($shop->current_stock, 2) }} kg)
                            </option>
                        @endforeach
                    </select>
                    <p id="stock_warning_msg" class="text-xs text-red-600 font-bold mt-1 hidden"></p>
                </div>

                {{-- 2. Destination Shop (Where stock goes TO) --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">To Shop (Destination)</label>
                    {{-- 🟢 UPDATED onchange: added autoToggleShops('destination') --}}
                    <select name="to_shop_id" id="to_shop_select" 
                        onchange="filterCustomersByShop(); autoToggleShops('destination')" 
                        class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Customer Selection (Optional) --}}
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Issue to Customer (Optional)</label>
                    <select name="customer_id" id="adj_customer_select" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @php
                            $retailCustomers = \App\Models\Customer::where('type', 'shop_retail')->get();
                        @endphp
                        @foreach($retailCustomers as $customer)
                            <option value="{{ $customer->id }}" data-shop-id="{{ $customer->shop_id }}" @selected($customer->id == 1)  >
                                {{ $customer->name }} ({{ $customer->shop->name ?? 'No Shop' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Select a customer to record this as a sale/credit transaction.</p>
                </div>

          
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Meat Type (Formula)</label>
                    <select id="adj_formula_select" name="formula_key" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" onchange="updateAdjustmentRate()">
                        @foreach($rateFormulas as $formula)
                            @php
                                $base = $defaultData['base_effective_cost'] ?? 0;
                                $rate = $base + $formula->plus * ($formula->multiply ?: 1);
                                $rate = $rate - $formula->minus;
                                if($formula->multiply > 0)$rate = $rate * ($formula->multiply ?: 1);  
                                if($formula->divide > 0) $rate = $rate / $formula->divide;
                            @endphp
                            <option value="{{ $formula->rate_key }}" data-rate="{{ number_format($rate, 2, '.', '') }}">
                                {{ $formula->title }} (Rate: {{ number_format($rate, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    {{-- 5. Weight --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Weight (KG)</label>
                        <input type="number" id="adj_weight" name="weight" step="0.01" 
                            class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none font-bold" 
                            placeholder="0.00" required oninput="calculateAdjTotal(); validateStockAvailability()" value="">
                    </div>
                    {{-- 6. Rate --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rate (PKR)</label>
                        <input type="number" id="adj_rate" name="rate" step="0.01" 
                            class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" 
                            placeholder="0.00" required oninput="calculateAdjTotal()">
                    </div>
                </div>

                {{-- 7. Total Amount --}}
                <div class="mb-4 bg-gray-50 p-3 rounded-lg flex justify-between items-center border">
                    <span class="text-gray-600 font-bold text-sm">Total Value:</span>
                    <span id="adj_total_display" class="text-xl font-black text-blue-700">0.00</span>
                    <input type="hidden" name="total_amount" id="adj_total_input">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Reason / Note</label>
                    <input type="text" name="reason" class="w-full border p-2.5 rounded-lg focus:ring-2 focus:ring-gray-200 outline-none" placeholder="e.g. Daily supply">
                </div>

                <button type="submit" id="adj_submit_btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-lg disabled:bg-gray-400 disabled:cursor-not-allowed">
                    Process Transaction
                </button>
            </form>
        </div>
    </div>

<script>
    document.getElementById('adj_weight').value = ''; 
    document.getElementById('adj_total_display').innerText = '0.00';
    autoToggleShops('source');
    updateAdjustmentRate();
    function autoToggleShops(trigger) {
        const sourceSelect = document.getElementById('source_shop_select');
        const destSelect = document.getElementById('to_shop_select');
        
        const sourceVal = sourceSelect.value;
        const destVal = destSelect.value;

        if (trigger === 'source') {
            if (sourceVal == 1) {
                destSelect.value = 2;
            } else if (sourceVal == 2) {
                destSelect.value = 1;
            }
            filterCustomersByShop();
        } 
        else if (trigger === 'destination') {
            if (destVal == 1) {
                sourceSelect.value = 2;
            } else if (destVal == 2) {
                sourceSelect.value = 1;
            }
            validateStockAvailability();
        }
    }

    function validateStockAvailability() {
        const sourceSelect = document.getElementById('source_shop_select');
        const weightInput = document.getElementById('adj_weight');
        const submitBtn = document.getElementById('adj_submit_btn');
        const warningMsg = document.getElementById('stock_warning_msg');

        // Get available stock from the selected option's data attribute
        const selectedOption = sourceSelect.options[sourceSelect.selectedIndex];
        const availableStock = parseFloat(selectedOption.getAttribute('data-stock')) || 0;
        const enteredWeight = parseFloat(weightInput.value) || 0;

        if (enteredWeight > availableStock) {
            warningMsg.textContent = `Error: Cannot transfer ${enteredWeight}kg. Only ${availableStock.toFixed(2)}kg available.`;
            warningMsg.classList.remove('hidden');
            submitBtn.disabled = true; // Disable submit button
            weightInput.classList.add('border-red-500', 'bg-red-50');
        } else {
            warningMsg.classList.add('hidden');
            submitBtn.disabled = false; // Enable submit button
            weightInput.classList.remove('border-red-500', 'bg-red-50');
        }
    }

    function filterCustomersByShop() {
        const selectedShopId = document.getElementById('to_shop_select').value;
        const customerSelect = document.getElementById('adj_customer_select');
        const options = customerSelect.querySelectorAll('option');

        let firstVisibleValue = null;
        let currentSelectionValid = false;

        options.forEach(option => {
            const customerShopId = option.getAttribute('data-shop-id');
            
            if (customerShopId == selectedShopId || !customerShopId) {
                option.style.display = ''; 
                
                if (firstVisibleValue === null) {
                    firstVisibleValue = option.value;
                }
                
                if (option.value === customerSelect.value) {
                    currentSelectionValid = true;
                }
            } else {
                option.style.display = 'none'; 
            }
        });

        if (!currentSelectionValid && firstVisibleValue !== null) {
            customerSelect.value = firstVisibleValue;
        }
    }

    function updateAdjustmentRate() {
        const select = document.getElementById('adj_formula_select');
        const rateInput = document.getElementById('adj_rate');
        if (select && rateInput) {
            const selectedOption = select.options[select.selectedIndex];
            const rate = selectedOption ? (selectedOption.getAttribute('data-rate') || 0) : 0;
            rateInput.value = rate;
            calculateAdjTotal();
        }
    }

    function calculateAdjTotal() {
        const weight = parseFloat(document.getElementById('adj_weight').value) || 0;
        const rate = parseFloat(document.getElementById('adj_rate').value) || 0;
        const total = weight * rate;

        document.getElementById('adj_total_display').innerText = total.toFixed(2);
        document.getElementById('adj_total_input').value = total.toFixed(2);
    }
    
    function openStockAdjustmentModal() {
        const modal = document.getElementById('adjustmentModal');
        const weightInput = document.getElementById('adj_weight');
        const totalDisplay = document.getElementById('adj_total_display');
        const totalInput = document.getElementById('adj_total_input');

        if (weightInput) weightInput.value = ''; 
        if (totalDisplay) totalDisplay.innerText = '0.00';
        if (totalInput) totalInput.value = '0.00';

        modal.classList.remove('hidden');

        autoToggleShops('source');
        updateAdjustmentRate();
    }
</script>

<script>
    function applyFormulaJS(baseRate, formula) {
        if (!formula) return baseRate;
        const multiply = parseFloat(formula.multiply) || 1.0;
        const divide = parseFloat(formula.divide) || 1.0;
        const plus = parseFloat(formula.plus) || 0.0;
        const minus = parseFloat(formula.minus) || 0.0;
        let finalRate = baseRate;
        finalRate += plus;
        finalRate *= multiply;
        if (divide !== 0 && divide !== 1) { finalRate /= divide; }
        finalRate -= minus;
        return Math.max(0.00, finalRate);
    }


    function openTransferModal() {
        document.getElementById('transferModal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        try {
            // 🟢 1. PREPARE SHOP DATA FOR DROPDOWN
            const availableShops = @json($shops->map(function($shop){ 
                return ['id' => $shop->id, 'name' => $shop->name, 'stock' => $shop->current_stock]; 
            }));

            const colors = { yellowDark: '#EAB308', bluePrimary: '#2563EB', yellowLight: '#FFFBEB' };
            const isHistorical = {{ json_encode($defaultData['is_historical'] ?? false) }};
            if (isHistorical) return;

            const formulaMapBase64 = '{{ base64_encode(json_encode($rateFormulas->map(function ($f) {
                return ["multiply" => $f->multiply, "divide" => $f->divide, "plus" => $f->plus, "minus" => $f->minus]; }))) }}';
            const rateFormulas = JSON.parse(atob(formulaMapBase64));

            const form = document.getElementById('daily-rates-form');
            const hiddenBaseCost = document.getElementById('hidden-base-cost');
            const baseCostDisplay = document.getElementById('base-cost-display');
            const baseCostBox = document.getElementById('base-cost-box');
            const manualBaseCostInput = document.getElementById('manual_base_cost');
            const applyOverrideButton = document.getElementById('apply-rate-override');

            // STOCK ELEMENTS
            const adjustStockBtn = document.getElementById('adjust-stock-btn');
            const netStockValueEl = document.getElementById('net-stock-value');
            const netStockInput = document.getElementById('net_stock_input');

            function formatNumber(number) { return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number); }

            const rateInputs = document.querySelectorAll('input[type="number"]:not(#manual_base_cost):not(#net_stock_input)');
            const rateInputElements = {};
            const userEditedInputs = {};

            rateInputs.forEach(inputElement => {
                const inputName = inputElement.name;
                rateInputElements[inputName] = inputElement;
                inputElement.addEventListener('input', () => {
                    userEditedInputs[inputName] = true;
                    inputElement.style.backgroundColor = colors.yellowLight;
                });
            });

            function calculateAndApplyRatesClient(event) {
                const initialBaseCost = parseFloat(hiddenBaseCost.value) || 0.00;
                const manualBaseOverride = parseFloat(manualBaseCostInput.value) || 0.00;
                let activeBaseCost;
                let isOverrideActive = manualBaseOverride > 0;

                if (isOverrideActive) {
                    activeBaseCost = manualBaseOverride;
                    baseCostBox.style.backgroundColor = colors.yellowDark;
                    baseCostBox.style.color = 'black';
                } else {
                    activeBaseCost = initialBaseCost;
                    baseCostBox.style.backgroundColor = colors.bluePrimary;
                    baseCostBox.style.color = 'white';
                }

                baseCostDisplay.textContent = formatNumber(activeBaseCost) + ' PKR/kg';

                for (const name in rateInputElements) {
                    if (!userEditedInputs[name]) {
                        const baseRateWithMargin = activeBaseCost;
                        const finalRate = applyFormulaJS(baseRateWithMargin, rateFormulas[name]);
                        rateInputElements[name].value = finalRate.toFixed(2);
                        rateInputElements[name].style.backgroundColor = '';
                    }
                }
            }

            manualBaseCostInput.addEventListener('input', calculateAndApplyRatesClient);
            manualBaseCostInput.addEventListener('blur', () => {
                const currentValue = manualBaseCostInput.value.trim();
                if (currentValue === '0.00' || currentValue === '0' || currentValue === '') {
                    manualBaseCostInput.value = '';
                }
                calculateAndApplyRatesClient({});
            });

            calculateAndApplyRatesClient({});

            // 🟢 UPDATED STOCK SHRINK LOGIC WITH SHOP SELECTION
            if (adjustStockBtn) {
                adjustStockBtn.addEventListener('click', async () => {
                    
                    // Build Shop Options
                    let shopOptions = '';
                    availableShops.forEach(shop => {
                        shopOptions += `<option value="${shop.id}">${shop.name} (Avail: ${parseFloat(shop.stock).toFixed(2)} kg)</option>`;
                    });

                    const { value: formValues } = await Swal.fire({
                        title: 'Stock Shrinkage / Loss',
                        html:
                            `<div class="text-left">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Select Shop</label>
                                <select id="swal-shop-id" class="swal2-input w-full m-0 mb-4 h-10 text-base" style="display:block;">
                                    ${shopOptions}
                                </select>

                                <label class="block text-sm font-bold text-gray-700 mb-1">Weight to Remove (KG)</label>
                                <input id="swal-weight" type="number" step="0.01" class="swal2-input w-full m-0 mb-4 h-10" placeholder="0.00">

                                <label class="block text-sm font-bold text-gray-700 mb-1">Reason (Optional)</label>
                                <input id="swal-note" class="swal2-input w-full m-0 h-10" placeholder="e.g. Died, Error, Waste">
                            </div>`,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Shrinkage',
                        confirmButtonColor: '#DC2626',
                        preConfirm: () => {
                            const shopId = document.getElementById('swal-shop-id').value;
                            const weight = document.getElementById('swal-weight').value;
                            const note = document.getElementById('swal-note').value;

                            if (!weight || weight <= 0) {
                                Swal.showValidationMessage('Please enter a valid weight');
                                return false;
                            }
                            return { shop_id: shopId, weight: weight, note: note };
                        }
                    });

                    if (formValues) {
                        Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

                        try {
                            const response = await fetch("{{ route('admin.rates.shrink') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify(formValues)
                            });

                            const result = await response.json();

                            if (response.ok && result.success) {
                                // Update Global UI visually
                                let currentTotal = parseFloat(netStockValueEl.innerText.replace(/,/g, '')) || 0;
                                let newTotal = currentTotal - parseFloat(formValues.weight);
                                netStockValueEl.innerText = formatNumber(newTotal);

                                // Update Local Shop Data Array (to prevent error on next click)
                                const shopIdx = availableShops.findIndex(s => s.id == formValues.shop_id);
                                if(shopIdx !== -1) {
                                    availableShops[shopIdx].stock = parseFloat(availableShops[shopIdx].stock) - parseFloat(formValues.weight);
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Stock Updated',
                                    text: result.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                throw new Error(result.message || 'Unknown error');
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: error.message
                            });
                        }
                    }
                });
            }

            // AJAX SAVE OVERRIDE
            applyOverrideButton.addEventListener('click', async function (e) {
                e.preventDefault();
                const overrideValue = parseFloat(manualBaseCostInput.value) || 0.00;
                const activeBaseCostForSubmit = overrideValue > 0 ? overrideValue : (parseFloat(hiddenBaseCost.value) || 0.00);
                hiddenBaseCost.value = activeBaseCostForSubmit.toFixed(2);
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });
                    const result = await response.json();

                    if (response.ok && result.success) {
                        baseCostDisplay.textContent = formatNumber(result.base_effective_cost) + ' PKR/kg';
                        hiddenBaseCost.value = result.base_effective_cost;
                        manualBaseCostInput.value = overrideValue > 0 ? result.base_effective_cost : '';
                        baseCostBox.style.backgroundColor = colors.yellowDark;
                        baseCostBox.style.color = 'black';

                        for (const [name, value] of Object.entries(result.rates)) {
                            if (rateInputElements[name]) {
                                if (!userEditedInputs[name]) {
                                    rateInputElements[name].value = value;
                                    rateInputElements[name].style.backgroundColor = '';
                                }
                            }
                        }
                        Swal.fire({ icon: 'success', title: 'Updated!', text: result.message, timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: result.message || "An error occurred." });
                    }
                } catch (fetchError) {
                    console.error('Fetch Error:', fetchError);
                    Swal.fire({ icon: 'error', title: 'Network Error', text: "Check your connection." });
                }
            });

            form.addEventListener('submit', function () {
                const overrideValue = parseFloat(manualBaseCostInput.value) || 0.00;
                const initialBaseCost = parseFloat(hiddenBaseCost.value) || 0.00;
                hiddenBaseCost.value = (overrideValue > 0 ? overrideValue : initialBaseCost).toFixed(2);
            });

        } catch (error) {
            console.error("JavaScript Error:", error);
        }
    });
</script>
@endsection