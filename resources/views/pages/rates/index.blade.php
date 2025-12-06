@extends('layouts.main')

@section('content')
    {{-- 🟢 SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="flex">
        <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">

            <div class="max-w-7xl mx-auto">

                {{-- Display error message --}}
                @if (session('error'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({ icon: 'error', title: 'Error', text: "{{ session('error') }}" });
                        });
                    </script>
                @endif

                <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight">
                    Daily Rates & Pricing
                </h1>

                @if (session('success'))
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({ icon: 'success', title: 'Success', text: "{{ session('success') }}" });
                        });
                    </script>
                @endif

                <input type="hidden" name="supplier_id" form="daily-rates-form" value="{{ $suppliers->first()->id ?? 1 }}">

                <form id="daily-rates-form" method="POST" action="{{ route('admin.rates.store') }}">
                    @csrf
                    
                    {{-- 🟢 CONTROL PANEL: Manual Rate + Actions --}}
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
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">
                                        PKR
                                    </span>
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
                            <h2 class="font-bold text-xl text-gray-700">
                                Cost Reference
                                <span class="text-blue-600 text-sm block md:inline md:ml-2">
                                    @if($defaultData['is_historical'] ?? false) (Saved Rate) @else (Live Calculation) @endif
                                </span>
                            </h2>

                            {{-- 🟢 ADJUST STOCK BUTTON --}}
                            <button type="button" id="adjust-stock-btn"
                                class="bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 font-bold px-3 py-2 rounded-lg text-sm transition flex items-center gap-2">
                                <i class="fas fa-minus-circle"></i> Stock Adjustment
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

                            <div class="bg-green-600 text-white text-center p-6 rounded-2xl shadow-md">
                                <p class="text-lg font-semibold opacity-90">Net Stock Available</p>
                                <div class="block text-3xl font-extrabold mt-2">
                                    {{-- 🟢 Wrapped number in a span for JS targeting --}}
                                    <span id="net-stock-value">{{ number_format($defaultData['net_stock_available'] ?? 0.00, 2) }}</span> 
                                    <span class="text-xl">KG</span>
                                </div>
                                {{-- Hidden Input to send updated stock to server if needed --}}
                                <input type="hidden" name="net_stock_available" id="net_stock_input" value="{{ $defaultData['net_stock_available'] ?? 0.00 }}">
                            </div>
                        </div>

                        @if($defaultData['is_historical'] ?? false)
                            <p class="text-red-600 text-sm mt-4 font-semibold text-center">
                                This view is locked for historical data from {{ $targetDate }}.
                            </p>
                        @endif
                    </div>

                    {{-- Form Inputs (Permanent/Wholesale/Retail) - Keeping them exactly as previous --}}
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
                                    $pRateFormulaSet = $pRateFormula && ($pRateFormula->multiply != 1.0 || $pRateFormula->divide != 1.0 || $pRateFormula->plus != 0.0 || $pRateFormula->minus != 0.0);
                                    $pRateFormulaText = $pRateFormula ? "×{$pRateFormula->multiply} ÷{$pRateFormula->divide} +{$pRateFormula->plus} -{$pRateFormula->minus}" : 'No Formula';
                                    $pTextColor = $pRateFormulaSet ? 'text-green-600' : 'text-red-600';
                                @endphp
                                <p class="{{ $pTextColor }} text-xs mt-1" data-margin="0.00" data-formula="{{ htmlspecialchars($pRateFormulaText) }}" data-key="permanent_rate">
                                    Formula: {{ $pRateFormulaText }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10 mt-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Wholesale Rates</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div id="wholesale_rate_container">
                                <label class="font-semibold text-gray-700 block mb-1">Wholesale Live</label>
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
                                <p class="text-xs mt-1 text-gray-500" data-margin="0.00" data-formula="{{ htmlspecialchars($wRateFormulaText) }}" data-key="wholesale_rate">
                                    Formula: ( value {{ $wRateFormulaText }} )
                                </p>
                            </div>
                            @php
                                $wholesales = [
                                    ['label' => 'Hotel Mix', 'name' => 'wholesale_hotel_mix_rate'],
                                    ['label' => 'Hotel Chest', 'name' => 'wholesale_hotel_chest_rate'],
                                    ['label' => 'Hotel Thigh', 'name' => 'wholesale_hotel_thigh_rate'],
                                    ['label' => 'Customer Piece', 'name' => 'wholesale_customer_piece_rate'],
                                ];
                            @endphp
                            @foreach($wholesales as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}" id="{{ $item['name'] }}_input"
                                            value="{{ number_format($defaultData[$item['name']] ?? 0.00, 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    @php
                                        $formula = $rateFormulas->get($item['name']);
                                        $formulaText = $formula ? "×{$formula->multiply} ÷{$formula->divide} +{$formula->plus} -{$formula->minus}" : 'No Formula';
                                    @endphp
                                    <p class="text-xs mt-1 text-gray-500" data-margin="0.00" data-formula="{{ htmlspecialchars($formulaText) }}" data-key="{{ $item['name'] }}">
                                        Formula: (value {{ $formulaText }})
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Shop Retail Rates (Purchun)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @php
                                $retails = [
                                    ['label' => 'Retail Live', 'name' => 'live_chicken_rate'],
                                    ['label' => 'Mix', 'name' => 'retail_mix_rate'],
                                    ['label' => 'Chest', 'name' => 'retail_chest_rate'],
                                    ['label' => 'Thigh', 'name' => 'retail_thigh_rate'],
                                    ['label' => 'Piece', 'name' => 'retail_piece_rate'],
                                ];
                            @endphp
                            @foreach($retails as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}" id="{{ $item['name'] }}_input"
                                            value="{{ number_format($defaultData[$item['name']] ?? 0.00, 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    @php
                                        $formula = $rateFormulas->get($item['name']);
                                        $formulaText = $formula ? "×{$formula->multiply} ÷{$formula->divide} +{$formula->plus} -{$formula->minus}" : 'No Formula';
                                    @endphp
                                    <p class="text-xs mt-1 text-gray-500" data-margin="0.00" data-formula="{{ htmlspecialchars($formulaText) }}" data-key="{{ $item['name'] }}">
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

    <script>
        function applyFormulaJS(baseRate, formula) {
            if (!formula) return baseRate;
            const multiply = parseFloat(formula.multiply) || 1.0;
            const divide = parseFloat(formula.divide) || 1.0;
            const plus = parseFloat(formula.plus) || 0.0;
            const minus = parseFloat(formula.minus) || 0.0;
            let finalRate = baseRate;
            finalRate *= multiply;
            if (divide !== 0 && divide !== 1) { finalRate /= divide; }
            finalRate += plus;
            finalRate -= minus;
            return Math.max(0.00, finalRate);
        }

        document.addEventListener('DOMContentLoaded', function () {
            try {
                const colors = { yellowDark: '#EAB308', bluePrimary: '#2563EB', yellowLight: '#FFFBEB' };
                const isHistorical = {{ json_encode($defaultData['is_historical'] ?? false) }};
                if (isHistorical) return;

                const formulaMapBase64 = '{{ base64_encode(json_encode($rateFormulas->map(function ($f) { return ["multiply" => $f->multiply, "divide" => $f->divide, "plus" => $f->plus, "minus" => $f->minus]; }))) }}';
                const rateFormulas = JSON.parse(atob(formulaMapBase64));

                const form = document.getElementById('daily-rates-form');
                const hiddenBaseCost = document.getElementById('hidden-base-cost');
                const baseCostDisplay = document.getElementById('base-cost-display');
                const baseCostBox = document.getElementById('base-cost-box');
                const manualBaseCostInput = document.getElementById('manual_base_cost');
                const applyOverrideButton = document.getElementById('apply-rate-override');
                
                // 🟢 STOCK ELEMENTS
                const adjustStockBtn = document.getElementById('adjust-stock-btn');
                const netStockValueEl = document.getElementById('net-stock-value');
                const netStockInput = document.getElementById('net_stock_input');

                function formatNumber(number) { return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(number); }

                const rateInputs = document.querySelectorAll('input[type="number"][name$="_rate"]:not(#manual_base_cost)');
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

                // 🟢 STOCK DEDUCTION LOGIC
                if(adjustStockBtn) {
                    adjustStockBtn.addEventListener('click', async () => {
                        const { value: weightToRemove } = await Swal.fire({
                            title: 'Add Shrink Stock',
                            input: 'number',
                            inputLabel: 'Enter weight to remove (KG)',
                            inputPlaceholder: 'e.g. 50',
                            showCancelButton: true,
                            confirmButtonText: 'Save',
                            confirmButtonColor: '#DC2626',
                            inputValidator: (value) => {
                                if (!value || value <= 0) {
                                    return 'Please enter a valid weight!';
                                }
                            }
                        });

                        if (weightToRemove) {
                            // 1. Get current values
                            let currentStock = parseFloat(netStockValueEl.innerText.replace(/,/g, '')) || 0;
                            let removeAmount = parseFloat(weightToRemove);

                            // 2. Calculation
                            let newStock = currentStock - removeAmount;
                            
                            // Prevent negative stock visually (optional)
                            if(newStock < 0) newStock = 0;

                            // 3. Update UI
                            netStockValueEl.innerText = formatNumber(newStock);
                            netStockInput.value = newStock; // Update hidden input if you want to save it on form submit

                            // 4. Success Alert
                            Swal.fire({
                                icon: 'success',
                                title: 'Stock Updated',
                                text: `${removeAmount} KG deducted. New Stock: ${formatNumber(newStock)} KG`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }

                // 🟢 AJAX SAVE OVERRIDE
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