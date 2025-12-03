@extends('layouts.main')

@section('content')
    <div class="flex">
        <div class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">

            <div class="max-w-7xl mx-auto">

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
                                        class="text-xl font-bold w-full p-2.5 pr-12 border border-yellow-500 bg-white rounded-lg text-gray-800 focus:border-yellow-700 focus:ring-yellow-700 transition-all"
                                        placeholder="Enter manual rate to override the calculated average...">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-semibold text-sm">
                                        PKR
                                    </span>
                                </div>
                            </div>
                            {{-- 🟢 NOTE: This button MUST be type="button" to prevent form submission --}}
                            <button type="button" id="apply-rate-override"
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors h-[50px] flex-shrink-0">
                                Apply Override
                            </button>
                        </div>
                        <p class="text-yellow-700 text-xs mt-2">
                            Enter a value and click 'Apply Override'. All rates below will instantly adjust based on this value.
                        </p>
                    </div>
                    {{-- 🟢 END NEW MANUAL OVERRIDE --}}

                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Bulk & Credit Rates</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- 1. Wholesale (Truck) --}}
                            <div id="wholesale_rate_container">
                                <label class="font-semibold text-gray-700 block mb-1">Wholesale (Truck)</label>
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="wholesale_rate" id="wholesale_rate_input"
                                        value="{{ number_format($defaultData['wholesale_rate'] ?? 0.00, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                <p class="text-green-600 text-xs mt-1" data-margin="10.00">+10 PKR Margin</p>
                            </div>

                            {{-- 2. Live Chicken (Updated) --}}
                            <div id="live_chicken_rate_container">
                                <label class="font-semibold text-gray-700 block mb-1">Live Chicken</label>
                                <div class="flex items-center border border-green-500 rounded-xl p-3 shadow-sm">
                                    <input type="number" name="live_chicken_rate" id="live_chicken_rate_input"
                                        value="{{ number_format($defaultData['live_chicken_rate'] ?? 0.00, 2, '.', '') }}"
                                        step="0.01" class="w-full outline-none text-gray-800"
                                        @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                    <span class="ml-2 text-gray-500">PKR</span>
                                </div>
                                <p class="text-green-600 text-xs mt-1" data-margin="20.00">+20 PKR Margin</p>
                            </div>

                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-8">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Wholesale Rates (Hotels & Customers)</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                            @php
                                $wholesales = [
                                    ['label' => 'Hotel Mix', 'name' => 'wholesale_hotel_mix_rate', 'default' => 625, 'margin' => '+25 PKR Margin', 'color' => 'orange', 'data_margin' => '25.00'],
                                    ['label' => 'Hotel Chest', 'name' => 'wholesale_hotel_chest_rate', 'default' => 725, 'margin' => '+125 PKR Margin', 'color' => 'orange', 'data_margin' => '125.00'],
                                    ['label' => 'Hotel Thigh', 'name' => 'wholesale_hotel_thigh_rate', 'default' => 675, 'margin' => '+75 PKR Margin', 'color' => 'orange', 'data_margin' => '75.00'],
                                    ['label' => 'Customer Piece', 'name' => 'wholesale_customer_piece_rate', 'default' => 600, 'margin' => 'No Margin', 'color' => 'blue', 'data_margin' => '0.00'],
                                ];
                            @endphp

                            @foreach($wholesales as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    <div
                                        class="flex items-center border border-{{ $item['color'] }}-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}" id="{{ $item['name'] }}_input"
                                            value="{{ number_format($defaultData[$item['name']] ?? $item['default'], 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    <p class="text-{{ $item['color'] }}-600 text-xs mt-1" data-margin="{{ $item['data_margin'] }}">{{ $item['margin'] }}</p>
                                </div>
                            @endforeach

                        </div>
                    </div>


                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200 mb-10">
                        <h2 class="font-bold text-xl text-gray-700 mb-6">Shop Retail Rates (Purchun)</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                            @php
                                $retails = [
                                    ['label' => 'Mix', 'name' => 'retail_mix_rate', 'default' => 650, 'margin' => '+50 PKR Margin', 'color' => 'green', 'data_margin' => '50.00'],
                                    ['label' => 'Chest', 'name' => 'retail_chest_rate', 'default' => 750, 'margin' => '+150 PKR Margin', 'color' => 'green', 'data_margin' => '150.00'],
                                    ['label' => 'Thigh', 'name' => 'retail_thigh_rate', 'default' => 700, 'margin' => '+100 PKR Margin', 'color' => 'green', 'data_margin' => '100.00'],
                                    ['label' => 'Piece', 'name' => 'retail_piece_rate', 'default' => 590, 'margin' => '-10 PKR Loss', 'color' => 'red', 'data_margin' => '-10.00'],
                                ];
                            @endphp

                            @foreach($retails as $item)
                                <div>
                                    <label class="font-semibold text-gray-700 block mb-1">{{ $item['label'] }}</label>
                                    <div
                                        class="flex items-center border border-{{ $item['color'] }}-500 rounded-xl p-3 shadow-sm">
                                        <input type="number" name="{{ $item['name'] }}" id="{{ $item['name'] }}_input"
                                            value="{{ number_format($defaultData[$item['name']] ?? $item['default'], 2, '.', '') }}"
                                            step="0.01" class="w-full outline-none text-gray-800"
                                            @if($defaultData['is_historical'] ?? false) disabled @endif required>
                                        <span class="ml-2 text-gray-500">PKR</span>
                                    </div>
                                    <p class="text-{{ $item['color'] }}-600 text-xs mt-1" data-margin="{{ $item['data_margin'] }}">{{ $item['margin'] }}</p>
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

 <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Define reliable colors
            const colors = {
                yellowDark: '#EAB308', 
                bluePrimary: '#2563EB', 
                yellowLight: '#FFFBEB'  
            };
            
            const isHistorical = {{ json_encode($defaultData['is_historical'] ?? false) }};
            if (isHistorical) return; 

            // Form elements
            const form = document.getElementById('daily-rates-form');
            const hiddenBaseCost = document.getElementById('hidden-base-cost');
            const baseCostDisplay = document.getElementById('base-cost-display');
            const baseCostBox = document.getElementById('base-cost-box');
            const manualBaseCostInput = document.getElementById('manual_base_cost');
            const applyOverrideButton = document.getElementById('apply-rate-override');
            
            const rateInputs = document.querySelectorAll('input[type="number"][name$="_rate"]:not(#manual_base_cost)');
            const margins = {};
            const userEditedInputs = {};

            // 1. Gather all margins and attach input listeners
            rateInputs.forEach(inputElement => {
                const inputName = inputElement.name;
                
                // Find margin from the sibling p tag
                const pTag = inputElement.closest('div').nextElementSibling;
                if (pTag && pTag.tagName === 'P' && pTag.dataset.margin) {
                    margins[inputName] = parseFloat(pTag.dataset.margin);
                } else {
                    margins[inputName] = 0;
                }
                
                inputElement.addEventListener('input', () => {
                    userEditedInputs[inputName] = true;
                    inputElement.style.backgroundColor = colors.yellowLight;
                });
            });
            
            function formatNumber(number) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(number);
            }
            
            function calculateAndApplyRates(event) {
                // Get the calculated/default base cost from the hidden field (set by controller)
                // Note: hiddenBaseCost.value might already be the overridden value if loaded from session/DB
                const initialBaseCost = parseFloat(hiddenBaseCost.value) || 0.00;
                
                // Get the value currently in the manual override input box
                const manualBaseOverride = parseFloat(manualBaseCostInput.value) || 0.00;
                
                let activeBaseCost;
                let isOverrideActive = manualBaseOverride > 0;

                if (isOverrideActive) {
                    activeBaseCost = manualBaseOverride;
                    
                    // Visual updates for override status
                    baseCostBox.style.backgroundColor = colors.yellowDark;
                    baseCostBox.style.color = 'black';
                    
                    // When global override is applied via button/input, UNLOCK/RESET all individual rate fields
                    if (event && (event.target === applyOverrideButton || event.target === manualBaseCostInput)) {
                        rateInputs.forEach(input => {
                            userEditedInputs[input.name] = false;
                            input.style.backgroundColor = ''; 
                        });
                    }

                } else {
                    // If override is cleared, revert to the initial calculated value loaded by the controller
                    // NOTE: You may need a separate hidden input for the ORIGINAL calculated cost if initialBaseCost 
                    // is being overridden on load. For now, we trust the server loaded the correct starting value.
                    activeBaseCost = initialBaseCost;
                    baseCostBox.style.backgroundColor = colors.bluePrimary;
                    baseCostBox.style.color = 'white';
                }

                // 🚨 CRUCIAL STEP: Update the hidden input that gets SAVED (base_effective_cost)
                hiddenBaseCost.value = activeBaseCost.toFixed(2);
                
                baseCostDisplay.textContent = formatNumber(activeBaseCost) + ' PKR/kg';

                // Update all rate fields based on the active base cost
                rateInputs.forEach(input => {
                    const inputName = input.name;
                    
                    // Skip if the user has manually entered a value into this field
                    if (userEditedInputs[inputName]) {
                        return;
                    }
                    
                    const margin = margins[inputName] || 0;
                    
                    if (!input.disabled) {
                        const newRate = activeBaseCost + margin;
                        input.value = newRate.toFixed(2);
                    }
                });
            }

            // 🟢 INITIALIZATION LOGIC: Check if the server loaded a persistent manual override
            const savedManualOverride = parseFloat(manualBaseCostInput.value);

            if (savedManualOverride > 0) {
                 // If the server loaded a manual value, run calculation immediately based on that value
                 calculateAndApplyRates({target: manualBaseCostInput});
            } else {
                 // Otherwise, run calculation based on the calculated live cost
                 calculateAndApplyRates({}); 
            }

            // Event listeners
            // 🟢 Button listener (Guaranteed to fire logic)
            applyOverrideButton.addEventListener('click', function(e) {
                e.preventDefault(); 
                calculateAndApplyRates(e); 
            });
            
            // Input listener on the manual override field
            manualBaseCostInput.addEventListener('input', calculateAndApplyRates);
            
            // Clear override on blur if the value is zero/blank after input
            manualBaseCostInput.addEventListener('blur', () => {
                const currentValue = manualBaseCostInput.value.trim();
                if (currentValue === '0.00' || currentValue === '0' || currentValue === '') {
                    manualBaseCostInput.value = '';
                    calculateAndApplyRates();
                } else {
                    calculateAndApplyRates(); 
                }
            });
            
            // 🛑 CRITICAL: Add a final listener to the FORM SUBMISSION itself 
            // to guarantee the calculation is run one last time before data is sent.
            form.addEventListener('submit', function() {
                // Ensure the final active base cost is correctly set in the hidden field right before submission
                calculateAndApplyRates({}); 
            });
        });
    </script>


@endsection