@extends('layouts.main')

@section('content')

    <div class="flex">
        <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">

            <div class="max-w-7xl mx-auto">
                
                {{-- Display error message if the Controller caught an exception --}}
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-5 py-4 rounded-xl mb-6 shadow">
                        {{ session('error') }}
                    </div>
                @endif
                
                <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight">
                    Daily Rates & Pricing
                </h1>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-xl mb-6 shadow">
                        {{ session('success') }}
                    </div>
                @endif
                
                {{-- Add hidden supplier ID for form submission validation --}}
                <input type="hidden" name="supplier_id" form="daily-rates-form" value="{{ $suppliers->first()->id ?? 1 }}">

                <form id="daily-rates-form" method="POST" action="{{ route('admin.rates.store') }}">
                    @csrf

                    {{-- This holds the final active or calculated base cost. This is the value your controller should save. --}}
                    <input type="hidden" name="base_effective_cost" id="hidden-base-cost"
                        value="{{ $defaultData['base_effective_cost'] ?? 0.00 }}">

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

                            <div id="base-cost-box" class="bg-blue-600 text-white text-center p-6 rounded-2xl shadow-md transition-colors">
                                <p class="text-lg font-semibold opacity-90">Base Effective Cost</p>
                                <span id="base-cost-display" class="block text-3xl font-extrabold mt-2">
                                    {{ number_format($defaultData['base_effective_cost'] ?? 0.00, 2) }} PKR/kg
                                </span>
                            </div>

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
                    
                    {{-- 🟢 MANUAL BASE COST OVERRIDE FOR RATES --}}
                    <div class="bg-yellow-50 p-6 rounded-2xl shadow-md border border-yellow-300 mb-8 @if($defaultData['is_historical'] ?? false) hidden @endif">
                        <h2 class="font-bold text-xl text-gray-700 mb-4">Manual Base Cost Override</h2>
                        <div class="flex items-end space-x-4">
                            <div class="flex flex-col flex-1">
                                <label for="manual_base_cost" class="text-gray-700 text-sm mb-1 font-semibold">
                                    Override Base Cost (PKR/kg)
                                </label>
                                <div class="relative w-full">
                                    {{-- 🟢 THIS INPUT SUBMITS THE MANUAL OVERRIDE VALUE TO THE SERVER --}}
                                    <input type="number" name="manual_base_cost" id="manual_base_cost" 
                                        {{-- 🟢 LOAD SAVED MANUAL COST HERE --}}
                                        value="{{ ($defaultData['manual_base_cost'] ?? 0.00) > 0 ? number_format($defaultData['manual_base_cost'], 2, '.', '') : '' }}" 
                                        min="0" step="0.01"
                                        {{-- FIX: Border and Focus classes changed to green --}}
                                        class="text-xl font-bold w-full p-2.5 pr-12 border border-green-500 bg-white rounded-lg text-gray-800 focus:border-green-700 focus:ring-green-700 transition-all"
                                        placeholder="Enter manual rate to override the calculated average...">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">
                                        PKR
                                    </span>
                                </div>
                            </div>
                            {{-- Button type is 'button' to trigger custom JavaScript fetch --}}
                            <button type="button" id="apply-rate-override"
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors h-[50px] flex-shrink-0">
                                Apply & Save Override
                            </button>
                        </div>
                        <p class="text-yellow-700 text-xs mt-2">
                            Enter a value and click 'Apply & Save Override'. All rates below will instantly adjust and save to the database.
                        </p>
                    </div>
                    {{-- 🟢 END NEW MANUAL OVERRIDE --}}

                    <div class="">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- 🟢 Permanent Rate (Hidden) --}}
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
                                    // Formula is considered SET if any component is not the default (1.0000, 1.0000, 0.0000, 0.0000)
                                    $pRateFormulaSet = $pRateFormula && (
                                        $pRateFormula->multiply != 1.0000 || 
                                        $pRateFormula->divide != 1.0000 || 
                                        $pRateFormula->plus != 0.0000 || 
                                        $pRateFormula->minus != 0.0000
                                    );
                                    $pRateFormulaText = $pRateFormula ? "×{$pRateFormula->multiply} ÷{$pRateFormula->divide} +{$pRateFormula->plus} -{$pRateFormula->minus}" : 'No Formula';
                                    $pTextColor = $pRateFormulaSet ? 'text-green-600' : 'text-red-600';
                                @endphp
                                <p class="{{ $pTextColor }} text-xs mt-1" data-margin="0.00" data-formula="{{ htmlspecialchars($pRateFormulaText) }}" data-key="permanent_rate">
                                    Formula: {{ $pRateFormulaText }}
                                </p>
                            </div>
                            {{-- END Permanent Rate --}}

                        </div>
                    </div>

                    {{-- Wholesale Rates (Hotels & Customers) --}}
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Wholesale Rates (Hotels & Customers)</h2>

                        {{-- LAYOUT: md:grid-cols-3 for 5 items (3 + 2) --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- 🟢 1. Wholesale Live (wholesale_rate) --}}
                            <div id="wholesale_rate_container">
                                <label class="font-semibold text-gray-700 block mb-1">Wholesale Live</label>
                                {{-- FIX: Border changed to green --}}
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="wholesale_rate" id="wholesale_rate_input"
                                        value="{{ number_format($defaultData['wholesale_rate'] ?? 0.00, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                @php
                                    $wRateFormula = $rateFormulas->get('wholesale_rate');
                                    $wRateFormulaSet = $wRateFormula && (
                                        $wRateFormula->multiply != 1.0000 || 
                                        $wRateFormula->divide != 1.0000 || 
                                        $wRateFormula->plus != 0.0000 || 
                                        $wRateFormula->minus != 0.0000
                                    );
                                    $wRateFormulaText = $wRateFormula ? "×{$wRateFormula->multiply} ÷{$wRateFormula->divide} +{$wRateFormula->plus} -{$wRateFormula->minus}" : 'No Formula';
                                    $wTextColor = $wRateFormulaSet ? 'text-green-600' : 'text-red-600';
                                @endphp
                                <p class="{{ $wTextColor }} text-xs mt-1" data-margin="0.00" data-formula="{{ htmlspecialchars($wRateFormulaText) }}" data-key="wholesale_rate">
                                    Formula: {{ $wRateFormulaText }}
                                </p>
                            </div>
                            {{-- END Wholesale Live --}}

                            @php
                                $wholesales = [
                                    ['label' => 'Hotel Mix', 'name' => 'wholesale_hotel_mix_rate', 'data_margin' => '0.00'],
                                    ['label' => 'Hotel Chest', 'name' => 'wholesale_hotel_chest_rate', 'data_margin' => '0.00'],
                                    ['label' => 'Hotel Thigh', 'name' => 'wholesale_hotel_thigh_rate', 'data_margin' => '0.00'],
                                    ['label' => 'Customer Piece', 'name' => 'wholesale_customer_piece_rate', 'data_margin' => '0.00'],
                                ];
                            @endphp

                            @foreach($wholesales as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    {{-- FIX: Border changed to green --}}
                                    <div
                                        class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}" id="{{ $item['name'] }}_input"
                                            value="{{ number_format($defaultData[$item['name']] ?? 0.00, 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    @php
                                        $formula = $rateFormulas->get($item['name']);
                                        $formulaSet = $formula && (
                                            $formula->multiply != 1.0000 || 
                                            $formula->divide != 1.0000 || 
                                            $formula->plus != 0.0000 || 
                                            $formula->minus != 0.0000
                                        );
                                        $formulaText = $formula ? "×{$formula->multiply} ÷{$formula->divide} +{$formula->plus} -{$formula->minus}" : 'No Formula';
                                        $textColor = $formulaSet ? 'text-green-600' : 'text-red-600';
                                    @endphp
                                    <p class="{{ $textColor }} text-xs mt-1" data-margin="{{ $item['data_margin'] }}" data-formula="{{ htmlspecialchars($formulaText) }}" data-key="{{ $item['name'] }}">
                                        Formula: {{ $formulaText }}
                                    </p>
                                </div>
                            @endforeach

                        </div>
                    </div>

                    {{-- Shop Retail Rates (Purchun) --}}
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Shop Retail Rates (Purchun)</h2>

                        {{-- LAYOUT: md:grid-cols-3 for 5 items (3 + 2) --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            {{-- 🟢 Retail Live Chicken Rate - CORRECTED (using live_chicken_rate field) --}}
                            <div>
                                <label class="font-semibold text-gray-700 block mb-1">Retail Live</label>
                                {{-- FIX: Border changed to green --}}
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="live_chicken_rate" id="live_chicken_rate_input"
                                        value="{{ number_format($defaultData['live_chicken_rate'] ?? 0.00, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                @php
                                    $lcRateFormula = $rateFormulas->get('live_chicken_rate');
                                    $lcRateFormulaSet = $lcRateFormula && (
                                        $lcRateFormula->multiply != 1.0000 || 
                                        $lcRateFormula->divide != 1.0000 || 
                                        $lcRateFormula->plus != 0.0000 || 
                                        $lcRateFormula->minus != 0.0000
                                    );
                                    $lcRateFormulaText = $lcRateFormula ? "×{$lcRateFormula->multiply} ÷{$lcRateFormula->divide} +{$lcRateFormula->plus} -{$lcRateFormula->minus}" : 'No Formula';
                                    $lcTextColor = $lcRateFormulaSet ? 'text-green-600' : 'text-red-600';
                                @endphp
                                <p class="{{ $lcTextColor }} text-xs mt-1" data-margin="0.00" data-formula="{{ htmlspecialchars($lcRateFormulaText) }}" data-key="live_chicken_rate">
                                    Formula: {{ $lcRateFormulaText }}
                                </p>
                            </div>
                            {{-- END Retail Live --}}

                            @php
                                $retails = [
                                    ['label' => 'Mix', 'name' => 'retail_mix_rate', 'data_margin' => '0.00'],
                                    ['label' => 'Chest', 'name' => 'retail_chest_rate', 'data_margin' => '0.00'],
                                    ['label' => 'Thigh', 'name' => 'retail_thigh_rate', 'data_margin' => '0.00'],
                                    ['label' => 'Piece', 'name' => 'retail_piece_rate', 'data_margin' => '0.00'],
                                ];
                            @endphp

                            @foreach($retails as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    {{-- FIX: Border changed to green --}}
                                    <div
                                        class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}" id="{{ $item['name'] }}_input"
                                            value="{{ number_format($defaultData[$item['name']] ?? 0.00, 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    @php
                                        $formula = $rateFormulas->get($item['name']);
                                        $formulaSet = $formula && (
                                            $formula->multiply != 1.0000 || 
                                            $formula->divide != 1.0000 || 
                                            $formula->plus != 0.0000 || 
                                            $formula->minus != 0.0000
                                        );
                                        $formulaText = $formula ? "×{$formula->multiply} ÷{$formula->divide} +{$formula->plus} -{$formula->minus}" : 'No Formula';
                                        $textColor = $formulaSet ? 'text-green-600' : 'text-red-600';
                                    @endphp
                                    <p class="{{ $textColor }} text-xs mt-1" data-margin="{{ $item['data_margin'] }}" data-formula="{{ htmlspecialchars($formulaText) }}" data-key="{{ $item['name'] }}">
                                        Formula: {{ $formulaText }}
                                    </p>
                                </div>
                            @endforeach

                        </div>
                    </div>

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

    {{-- JavaScript needs to be updated to apply the formulas --}}
    <script>
        // 🟢 NEW HELPER FUNCTION to apply the formula
        function applyFormulaJS(baseRate, formula) {
            if (!formula) return baseRate;
            
            // NOTE: Formulas from PHP are passed as strings, need to parse them safely
            const multiply = parseFloat(formula.multiply) || 1.0;
            const divide   = parseFloat(formula.divide) || 1.0;
            const plus     = parseFloat(formula.plus) || 0.0;
            const minus    = parseFloat(formula.minus) || 0.0;
            
            let finalRate = baseRate;
            
            finalRate *= multiply;
            
            if (divide !== 0 && divide !== 1) {
                finalRate /= divide;
            }

            finalRate += plus;
            finalRate -= minus;
            
            return Math.max(0.00, finalRate);
        }
        
        document.addEventListener('DOMContentLoaded', function () {
            try { // Start of try block for client-side logic

                // Define reliable colors
                const colors = {
                    yellowDark: '#EAB308', 
                    bluePrimary: '#2563EB', 
                    yellowLight: '#FFFBEB'  
                };
                
                const isHistorical = {{ json_encode($defaultData['is_historical'] ?? false) }};
                if (isHistorical) return; 
                
                // 🟢 NEW: Pass the formulas from the blade to the JS
                const formulaMapBase64 = '{{ base64_encode(json_encode($rateFormulas->map(function($f) { return ['multiply' => $f->multiply, 'divide' => $f->divide, 'plus' => $f->plus, 'minus' => $f->minus]; }))) }}';
                const rateFormulas = JSON.parse(atob(formulaMapBase64));

                // Form elements
                const form = document.getElementById('daily-rates-form');
                const hiddenBaseCost = document.getElementById('hidden-base-cost');
                const baseCostDisplay = document.getElementById('base-cost-display');
                const baseCostBox = document.getElementById('base-cost-box');
                const manualBaseCostInput = document.getElementById('manual_base_cost');
                const applyOverrideButton = document.getElementById('apply-rate-override');

                // Helper function to format numbers for display
                function formatNumber(number) {
                    return new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(number);
                }
                
                // Get all rate inputs, including permanent_rate_input (if present)
                const rateInputs = document.querySelectorAll('input[type="number"][name$="_rate"]:not(#manual_base_cost)');
                
                // Maps input name to DOM element for easy access
                const rateInputElements = {};
                const margins = {};
                const userEditedInputs = {}; // Tracks which fields the user has manually changed

                // 1. Gather all margins and attach input listeners
                rateInputs.forEach(inputElement => {
                    const inputName = inputElement.name;
                    rateInputElements[inputName] = inputElement;

                    // Find margin from the sibling p tag
                    const pTag = inputElement.closest('div').nextElementSibling;
                    if (pTag && pTag.tagName === 'P' && pTag.dataset.margin) {
                        // All margins are now 0.00
                        margins[inputName] = parseFloat(pTag.dataset.margin);
                    } else {
                        margins[inputName] = 0; 
                    }

                    // Listener to mark a field as manually edited
                    inputElement.addEventListener('input', () => {
                        userEditedInputs[inputName] = true; // Mark as edited
                        inputElement.style.backgroundColor = colors.yellowLight;
                    });
                });

                /**
                 * Client-side calculation function (for input/blur events).
                 */
                function calculateAndApplyRatesClient(event) {
                    // This function is for dynamic visual update (not AJAX)
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

                    // Update base cost display immediately
                    baseCostDisplay.textContent = formatNumber(activeBaseCost) + ' PKR/kg';
                    
                    // 🟢 Re-calculate and update rates based on new manual cost (if field not user-edited)
                    for (const name in rateInputElements) {
                         if (!userEditedInputs[name]) {
                             // baseRateWithMargin is now just activeBaseCost + 0.00 (margins[name])
                             const baseRateWithMargin = activeBaseCost + (margins[name] || 0); 
                             const finalRate = applyFormulaJS(baseRateWithMargin, rateFormulas[name]); // 🟢 Apply Formula
                             rateInputElements[name].value = finalRate.toFixed(2);
                             rateInputElements[name].style.backgroundColor = ''; // Clear color for auto-updated field
                         }
                    }
                }
                
                // Input listener on the manual override field (only for visual update)
                manualBaseCostInput.addEventListener('input', calculateAndApplyRatesClient);

                // Clear override on blur if the value is zero/blank after input
                manualBaseCostInput.addEventListener('blur', () => {
                    const currentValue = manualBaseCostInput.value.trim();
                    if (currentValue === '0.00' || currentValue === '0' || currentValue === '') {
                        manualBaseCostInput.value = '';
                    }
                    calculateAndApplyRatesClient({}); 
                });

                // Initial calculation call to ensure rates reflect base cost + formula on load
                calculateAndApplyRatesClient({});


                /**
                 * Fetches and saves the rates via AJAX, then updates the DOM.
                 */
                applyOverrideButton.addEventListener('click', async function(e) {
                    e.preventDefault(); // Stop default form submission

                    const overrideValue = parseFloat(manualBaseCostInput.value) || 0.00;
                    
                    // Client-side calculation to set the correct hidden value for submission
                    const activeBaseCostForSubmit = overrideValue > 0 ? overrideValue : (parseFloat(hiddenBaseCost.value) || 0.00);
                    hiddenBaseCost.value = activeBaseCostForSubmit.toFixed(2);
                    
                    // Create FormData object to submit all necessary form fields
                    const formData = new FormData(form);
                    
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                // CSRF Token is needed for security
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 
                                'Accept': 'application/json', // Requesting JSON response
                                'X-Requested-With': 'XMLHttpRequest' // Standard AJAX header
                            },
                            body: formData
                        });
                        
                        const result = await response.json();

                        if (response.ok && result.success) {
                            // 1. Update Base Cost Display
                            baseCostDisplay.textContent = formatNumber(result.base_effective_cost) + ' PKR/kg';
                            hiddenBaseCost.value = result.base_effective_cost; // Update hidden field with the new base cost
                            manualBaseCostInput.value = overrideValue > 0 ? result.base_effective_cost : ''; // Re-set input value from saved base
                            baseCostBox.style.backgroundColor = colors.yellowDark;
                            baseCostBox.style.color = 'black';
                            
                            // 2. Update all rate inputs with the freshly calculated/saved rates from the backend
                            for (const [name, value] of Object.entries(result.rates)) {
                                if (rateInputElements[name]) {
                                    // ONLY update if the field was NOT manually edited by the user.
                                    if (!userEditedInputs[name]) {
                                        rateInputElements[name].value = value;
                                        rateInputElements[name].style.backgroundColor = ''; // Clear color for auto-updated field
                                    } 
                                }
                            }
                            
                            // 3. Display success message
                            alert(result.message); 

                        } else {
                            // Handle backend errors or failure message
                            alert("Error saving rates: " + (result.message || "An unknown error occurred on the server."));
                        }

                    } catch (fetchError) {
                        console.error('Fetch Error:', fetchError);
                        alert("Network or Server error during save. Please check console.");
                    }
                });


                // 🛑 CRITICAL: Add a final listener to the FORM SUBMISSION (Activate Today's Rates)
                form.addEventListener('submit', function() {
                    const overrideValue = parseFloat(manualBaseCostInput.value) || 0.00;
                    const initialBaseCost = parseFloat(hiddenBaseCost.value) || 0.00;
                    // Ensure the final base cost reflects the manual override if present
                    hiddenBaseCost.value = (overrideValue > 0 ? overrideValue : initialBaseCost).toFixed(2);
                });

            } catch (error) { // Catch block for client-side logic
                console.error("A critical JavaScript error occurred:", error);
                // Inform the user about the client-side error.
                alert("A critical error occurred while loading the pricing page calculations. Please refresh the page or contact support.");
            }
        });
    </script>


@endsection