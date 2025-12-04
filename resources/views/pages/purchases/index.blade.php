@extends('layouts.main') 

@section('content')
    <style>
        /* ... (CSS variables and basic styles remain the same) ... */
        :root {
            --primary: #2563eb;
            --sidebar-bg: #0f172acb;

            --sidebar-text: #afb1b4cb;
            --bg-main: #F2F4F7;
            --card-bg: #FFFFFF;
            --border-light: #E5E7EB;

            --text-dark: #111827;
            --text-gray: #6B7280;

            --blue-primary: #2563EB;
            --green-primary: #16A34A;
            --red-primary: #DC2626;
            --yellow-primary: #FACC15;
            --yellow-dark: #EAB308;

            --blue-light: #EFF6FF;
            --green-light: #DCFCE7;
            --red-light: #FEE2E2;
        }

        html,
        body {
            height: 100%;
        }

        #sidebar {
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
        }

        #center i {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Utility class for input focus */
        .input-focus {
            box-shadow: 0 0 0 2px var(--blue-light), 0 0 0 4px var(--blue-primary);
        }
    </style>
    <div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">

        {{-- Success/Error Message Display Area --}}
        <div id="statusMessage" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"></span>
        </div>
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc ml-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.purchases.store') }}" id="purchaseForm" class="flex flex-col h-full">
            @csrf

            <div class="h-[80px] px-6 flex flex-col justify-center">
                <h2 class="text-2xl font-bold text-[var(--text-dark)] leading-tight">Purchase Entry</h2>
                <p class="text-[var(--text-gray)] text-sm mt-0.5">Today: {{ now()->format('d M Y') }}</p>
            </div>

            <div class="flex-1 px-6 pb-4 flex flex-col gap-4 overflow-y-auto">

                <div class="bg-[var(--card-bg)] p-3 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col">
                    <h3 class="font-bold text-[var(--text-dark)] text-xl mb-3">1. Truck Details</h3>

                    <div class="flex items-start space-x-6">

                        <div class="flex flex-col">
                            <label for="supplier_id" class="text-[var(--text-gray)] text-sm mb-1">Select Supplier</label>
                            <select name="supplier_id" id="supplier_id" required
                                class="border border-[var(--border-light)] rounded-md p-2 text-sm w-[220px] focus:ring-2 focus:ring-[var(--blue-primary)] focus:border-[var(--blue-primary)]">
                                <option value="">--- Select Supplier ---</option>
                                {{-- 🟢 DYNAMIC SUPPLIER LIST --}}
                                @if(isset($suppliers))
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}">{{ $supplier->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label for="driver_no" class="text-[var(--text-gray)] text-sm mb-1">Driver No</label>
                            <input type="text" name="driver_no" id="driver_no"
                                class="border border-[var(--border-light)] rounded-md p-2 text-sm w-[200px] focus:ring-2 focus:ring-[var(--blue-primary)] focus:border-[var(--blue-primary)]"
                                placeholder="Truck/Driver Plate">
                        </div>

                    </div>
                </div>

                <div class="bg-[var(--card-bg)] p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col">

                    <div class="flex items-center gap-2 h-[30px] mb-2">
                        <h3 class="font-bold text-[var(--text-dark)] text-xl">2. Weight Calculation</h3>
                        <span class="text-xs bg-[var(--blue-primary)] text-white px-2 py-1 rounded-full">
                            Automated Math
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-4 flex-1 items-stretch">

                        <div class="flex flex-col justify-end">

                            <label for="gross_weight" class="text-[var(--text-gray)] text-md mb-2">Gross Weight</label>

                            <div class="flex items-end gap-3 h-full">

                                <div class="relative w-full">
                                    <input type="number" name="gross_weight" id="gross_weight" value="0" min="0" required
                                        class="text-4xl font-bold w-full p-2.5 pr-12 border border-[var(--border-light)] rounded-lg text-[var(--text-dark)] focus:border-[var(--blue-primary)] transition-all">

                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--text-gray)] font-semibold">
                                        KG
                                    </span>
                                </div>

                                <div
                                    class="w-[40px] h-[40px] rounded-full bg-[var(--text-gray)] flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-minus text-white text-lg"></i>
                                </div>

                            </div>

                        </div>


                        <div class="p-3 rounded-xl border flex flex-col justify-end h-full">
                            <h4 class="font-semibold text-[var(--red-primary)] text-sm mb-1">Deductions (Loss)</h4>

                            <div class="flex gap-2">

                                <div class="flex-1">
                                    <label for="dead_qty" class="text-xs text-[var(--red-primary)] mb-1 block">Dead
                                        Qty</label>

                                    <div class="relative">
                                        <input type="number" name="dead_qty" id="dead_qty" value="0" min="0"
                                            class="w-full bg-red-100 p-2 h-8 pr-10 border border-[var(--border-light)] rounded-md text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all" />

                                        <span
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">
                                            Pcs
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <label for="dead_weight" class="text-xs text-[var(--red-primary)] mb-1 block">Dead
                                        Wgt</label>

                                    <div class="relative">
                                        <input type="number" name="dead_weight" id="dead_weight" value="0" min="0"
                                            class="w-full bg-red-100 p-2 h-8 pr-10 border border-[var(--border-light)] rounded-md text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all" />

                                        <span
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">
                                            Kg
                                        </span>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <label for="shrink_loss" class="text-xs text-[var(--red-primary)] mb-1 block">Shrink
                                        Loss</label>

                                    <div class="relative">
                                        <input type="number" name="shrink_loss" id="shrink_loss" value="0" min="0"
                                            step="0.01"
                                            class="w-full h-8 bg-red-100 p-2 pr-10 border border-[var(--border-light)] rounded-md text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all" />

                                        <span
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">
                                            Kg
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <div class="flex items-end gap-4 h-full">

                            <div
                                class="w-[40px] h-[40px] rounded-full bg-[var(--text-gray)] flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-equals text-white text-lg"></i>
                            </div>


                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-[var(--green-primary)] font-semibold text-sm">
                                    Net Live Weight
                                </label>

                                <div
                                    class="flex items-center gap-2 bg-[var(--green-light)] border border-[var(--green-primary)] rounded-md p-2">
                                    {{-- NOTE: This input holds the calculated value and should be sent to the backend --}}
                                    <input type="number" name="net_live_weight" id="net_live_weight" value="0" readonly
                                        class="bg-transparent outline-none w-full text-4xl text-center font-extrabold text-[var(--green-primary)] cursor-not-allowed">

                                    <span class="text-[var(--green-primary)] font-semibold text-sm flex-shrink-0">KG</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-4 rounded-2xl shadow flex flex-col">

                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-white text-lg font-semibold">
                            Weight & Rates
                        </h2>
                    </div>

                    <div class="grid grid-cols-3 gap-4 flex-1">

                        <div class=" text-white p-3 flex flex-col bg-gray-700 rounded-xl">
                            <label for="buying_rate" class="text-gray-300 text-xs mb-1">Buying Rate (Per Kg)</label>

                            <div class="relative">
                                <input type="number" name="buying_rate" id="buying_rate" value="0" min="0" required
                                    class="w-full p-2.5 pr-10 text-black rounded-lg focus:outline-none text-sm focus:ring-2 focus:ring-yellow-400">

                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-700 text-xs font-semibold">
                                    Rs
                                </span>
                            </div>
                        </div>



                        <div class="bg-gray-900 text-white p-3 rounded-xl shadow flex flex-col justify-center">
                            <label class="text-gray-300 text-xs mb-1">Total Payable Amount</label>
                            {{-- NOTE: This value is read-only, calculated, and sent to the backend --}}
                            <input type="text" name="total_payable_display" id="total_payable_display" value="Rs 0" readonly
                                class="w-full p-2 rounded-lg text-black focus:outline-none text-sm cursor-not-allowed">
                            <input type="hidden" name="total_payable" id="total_payable" value="0">
                        </div>

                        {{-- 🟢 DYNAMIC EFFECTIVE COST BLOCK (PHP logic added to fetch formula data safely) --}}
                        @php
                            // Ensure the RateFormula model is available in the controller that renders this view.
                            $purchaseFormula = null;
                            if (class_exists(\App\Models\RateFormula::class)) {
                                $purchaseFormula = \App\Models\RateFormula::where('rate_key', 'purchase_effective_cost')->first();
                            }
                            
                            // 🟢 Generate default or saved formula string
                            $pFormula = $purchaseFormula ?? (object)['multiply' => 1.0, 'divide' => 1.0, 'plus' => 0.0, 'minus' => 0.0];
                            $pFormulaText = "×{$pFormula->multiply} ÷{$pFormula->divide} +{$pFormula->plus} -{$pFormula->minus}";
                        @endphp
                        <div
                            class="bg-[var(--blue-primary)] p-3 rounded-xl text-center shadow flex flex-col justify-center">
                            
                            {{-- Display the dynamic formula status text --}}
                            <p class="text-blue-100 text-xs">Effective Cost (Dynamic)</p>
                            
                            <h1 class="text-2xl font-bold text-white leading-none mt-1">
                                <span id="effective_cost_display">0</span><span class="text-sm">/kg</span>
                            </h1>
                            
                            <p class="text-xs text-blue-100 mt-1" id="effective_cost_formula_display">
                                Formula: {{ $pFormulaText }}
                            </p>

                            <input type="hidden" name="effective_cost" id="effective_cost" value="0">
                            
                            {{-- 🟢 PASS FORMULA DATA TO JS (CRITICAL FIX) --}}
                            <input type="hidden" id="purchase_formula_data" 
                                value="{{ htmlspecialchars(json_encode([
                                    'multiply' => $pFormula->multiply, 
                                    'divide' => $pFormula->divide, 
                                    'plus' => $pFormula->plus, 
                                    'minus' => $pFormula->minus
                                ])) }}">
                        </div>

                    </div>
                </div>
            

                <div class="h-[70px] flex items-center justify-end px-6 border-t border-[var(--border-light)]">
                    <button type="submit" id="savePurchaseBtn"
                        class="bg-[var(--yellow-primary)] hover:bg-[var(--yellow-dark)] text-black font-bold w-[200px] py-3 rounded-xl shadow transition-colors">
                        Save Purchase
                    </button>
                </div>


                
                {{-- 🟢 PURCHASE HISTORY TABLE (Unchanged) --}}
                <div class="bg-[var(--card-bg)] p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col mt-4">
                    <h3 class="font-bold text-[var(--text-dark)] text-xl mb-3">4. Recent Purchase History (Today)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Date / Supplier</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Net Wgt (Kg)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Rate (Rs)</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Payable (Rs)</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Cost/Kg</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-table-body" class="bg-white divide-y divide-gray-200">
                                @forelse (isset($purchases) ? $purchases : [] as $purchase)
                                    <tr id="purchase-row-{{ $purchase->id }}"> {{-- Added ID for JavaScript removal --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $purchase->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            <div class="font-semibold">{{ $purchase->supplier_name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">Driver: {{ $purchase->driver_no ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-[var(--green-primary)]">
                                            {{ number_format($purchase->net_live_weight ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                                            {{ number_format($purchase->buying_rate ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-extrabold text-[var(--text-dark)]">
                                            {{ number_format($purchase->total_payable ?? 0, 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-[var(--blue-primary)] font-bold">
                                            {{ number_format($purchase->effective_cost ?? 0, 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            {{-- 🟢 UPDATED: Added onclick event --}}
                                            <button type="button" 
                                                    onclick="deletePurchase({{ $purchase->id }})"
                                                    class="text-[var(--red-primary)] hover:text-red-700 transition-colors">
                                                <i class="fa-solid fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-purchases-row">
                                        <td colspan="7" class="px-6 py-10 text-center text-gray-500 text-sm italic">
                                            No recent purchases found. Save a new one using the form above.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>


    </div>

   <script>
    // Global delete URL template (used in deletePurchase function)
    const DELETE_URL_BASE = "{{ route('admin.purchases.store') }}".replace('/store', ''); 

    document.addEventListener('DOMContentLoaded', function () {
        
        // Safety check for core elements
        if (!document.getElementById('gross_weight') || !document.getElementById('net_live_weight')) {
            console.error("Critical: Purchase view inputs are missing. Calculation cannot proceed.");
            return;
        }

        const form = document.getElementById('purchaseForm');
        const saveBtn = document.getElementById('savePurchaseBtn');
        const statusMessage = document.getElementById('statusMessage');
        const purchaseTableBody = document.getElementById('purchase-table-body');
        
        // --- Input/Output Mappings ---
        const inputs = {
            gross_weight: document.getElementById('gross_weight'),
            dead_weight: document.getElementById('dead_weight'),
            dead_qty: document.getElementById('dead_qty'),
            shrink_loss: document.getElementById('shrink_loss'),
            buying_rate: document.getElementById('buying_rate'),
            supplier_id: document.getElementById('supplier_id'),
            driver_no: document.getElementById('driver_no'),
        };

        const outputs = {
            net_live_weight: document.getElementById('net_live_weight'),
            total_payable: document.getElementById('total_payable'),
            total_payable_display: document.getElementById('total_payable_display'),
            effective_cost: document.getElementById('effective_cost'),
            effective_cost_display: document.getElementById('effective_cost_display'),
        };

        const EFFECTIVE_COST_FACTOR = 1.2;
        const csrfToken = document.querySelector('input[name="_token"]').value;
        
        // 🟢 1. CRITICAL FIX: Initialize purchaseFormula based on settings data or fallback
        let purchaseFormula = null;
        const formulaDataElement = document.getElementById('purchase_formula_data');
        const defaultBusinessRule = { multiply: EFFECTIVE_COST_FACTOR, divide: 1.0, plus: 0.0, minus: 0.0 };
        const genericDefault = { multiply: 1.0, divide: 1.0, plus: 0.0, minus: 0.0 };

        if (formulaDataElement && formulaDataElement.value) {
            try {
                purchaseFormula = JSON.parse(formulaDataElement.value);
            } catch (e) {
                console.error("Error parsing purchase formula JSON:", e);
                // Fallback 1: Parsing failed -> Use the business rule (x 1.2)
                purchaseFormula = defaultBusinessRule; 
            }
        }
        
        // 🟢 2. Check if the retrieved formula is the non-modifying default (1.0, 1.0, 0.0, 0.0)
        // This ensures that when the user hasn't touched the formula, we enforce the x 1.2 rule.
        if (purchaseFormula && 
            purchaseFormula.multiply == 1.0 && 
            purchaseFormula.divide == 1.0 && 
            purchaseFormula.plus == 0.0 && 
            purchaseFormula.minus == 0.0) 
        {
            purchaseFormula = defaultBusinessRule; // Use the business rule (x 1.2)
        } else if (purchaseFormula === null) {
            purchaseFormula = defaultBusinessRule; // Use the business rule if no data element existed
        }


        function parseInput(element) {
            return parseFloat(element ? element.value || 0 : 0) || 0;
        }

        function formatCurrency(number) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(Math.round(number));
        }

        function timeAgo() {
            return 'just now'; 
        }
        
        // HELPER FUNCTION to apply the formula
        function applyFormulaJS(baseRate, formula) {
            if (!formula) return baseRate;
            
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
        
        // --- CORE CALCULATION FUNCTION ---
        function calculateTotals() {
            const grossWeight = parseInput(inputs.gross_weight);
            const deadWeight = parseInput(inputs.dead_weight);
            const shrinkLoss = parseInput(inputs.shrink_loss);
            const buyingRate = parseInput(inputs.buying_rate);

            // 1. Net Live Weight Calculation (Original Logic)
            let netLiveWeight = grossWeight - deadWeight - shrinkLoss;
            if (netLiveWeight < 0) netLiveWeight = 0; 

            // 2. Total Payable Calculation (Original Logic)
            const totalPayable = netLiveWeight * buyingRate;

            // 3. Base Cost for Formula Application (Cost / Wgt, WITHOUT static 1.2 factor)
            let baseCostForFormula = 0;
            if (netLiveWeight > 0) {
                // Raw cost per kg
                baseCostForFormula = (totalPayable / netLiveWeight);
            }
            
            // 4. Apply the Formula from Settings page (Formula rules handle multiplication/addition/etc.)
            let finalEffectiveCost = applyFormulaJS(baseCostForFormula, purchaseFormula); 

            // --- Update Outputs ---
            outputs.net_live_weight.value = netLiveWeight.toFixed(2);
            outputs.total_payable.value = totalPayable.toFixed(2);
            outputs.total_payable_display.value = `Rs ${formatCurrency(totalPayable)}`;
            outputs.effective_cost.value = finalEffectiveCost.toFixed(2);
            outputs.effective_cost_display.textContent = finalEffectiveCost.toFixed(0);
            
            // Set cost box style
            outputs.effective_cost_display.closest('div').style.backgroundColor = 'var(--blue-primary)'; 
            outputs.effective_cost_display.closest('div').style.color = 'white'; 
        }

        // --- RENDER TABLE ROW (Unchanged) ---
        function renderPurchaseRow(purchase) {
            const supplierName = purchase.supplier_name;
            
            const netWgt = parseFloat(purchase.net_live_weight).toFixed(2);
            const buyingRate = parseFloat(purchase.buying_rate).toFixed(2);
            const totalPayable = formatCurrency(purchase.total_payable);
            const effectiveCost = formatCurrency(purchase.effective_cost);
            
            const timeDisplay = purchase.created_at || timeAgo();

            const newRow = document.createElement('tr');
            newRow.id = `purchase-row-${purchase.id}`; // Set ID for removal
            newRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${purchase.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                    <div class="font-semibold">${supplierName}</div>
                    <div class="text-xs text-gray-500">Driver: ${purchase.driver_no || 'N/A'}</div>
                    <div class="text-xs text-gray-400 mt-0.5">${timeDisplay}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-[var(--green-primary)]">${netWgt}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">${buyingRate}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-extrabold text-[var(--text-dark)]">${totalPayable}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-[var(--blue-primary)] font-bold">${effectiveCost}</td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button type="button" 
                            onclick="deletePurchase(${purchase.id})"
                            class="text-[var(--red-primary)] hover:text-red-700 transition-colors">
                        <i class="fa-solid fa-trash-alt"></i>
                    </button>
                </td>
            `;

            const placeholderRow = document.getElementById('no-purchases-row');
            if (placeholderRow) {
                placeholderRow.remove();
            }

            purchaseTableBody.prepend(newRow);
        }
        
        // --- DELETE PURCHASE (Unchanged) ---
        window.deletePurchase = async function(id) {
            if (!confirm(`Are you sure you want to delete Purchase #${id}? This action cannot be undone.`)) {
                return;
            }

            const deleteUrl = `${DELETE_URL_BASE}/${id}`;
            const rowId = `purchase-row-${id}`;
            const rowElement = document.getElementById(rowId);

            if (rowElement) {
                rowElement.style.opacity = 0.5;
            }

            try {
                const response = await fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-HTTP-Method-Override': 'DELETE', 
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                const responseData = await response.json();

                if (response.ok) {
                    if (rowElement) {
                        rowElement.remove();
                    }
                    
                    if (purchaseTableBody.children.length === 0) {
                        purchaseTableBody.innerHTML = `
                            <tr id="no-purchases-row">
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 text-sm italic">
                                    No recent purchases found. Save a new one using the form above.
                                </td>
                            </tr>`;
                    }

                    statusMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4';
                    statusMessage.querySelector('span').textContent = responseData.message || `Purchase #${id} deleted successfully.`;
                    statusMessage.classList.remove('hidden');

                } else {
                    if (rowElement) {
                        rowElement.style.opacity = 1.0; 
                    }
                    statusMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                    statusMessage.querySelector('span').textContent = responseData.message || `Failed to delete purchase #${id}.`;
                    statusMessage.classList.remove('hidden');
                }

            } catch (error) {
                if (rowElement) {
                    rowElement.style.opacity = 1.0; 
                }
                console.error('Network Error during deletion:', error);
                statusMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                statusMessage.querySelector('span').textContent = 'Network error during deletion.';
                statusMessage.classList.remove('hidden');
            }
        }


        // --- AJAX FORM SUBMISSION (Unchanged) ---
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // 1. Basic Validation Check
            if (parseInput(outputs.net_live_weight) <= 0) {
                 statusMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                 statusMessage.querySelector('span').textContent = 'Net Live Weight must be greater than zero. Please check Gross Weight and deductions.';
                 statusMessage.classList.remove('hidden');
                 return;
            }
            
            // 🟢 Recalculate one final time to ensure the effective_cost hidden field is updated
            calculateTotals(); 
            
            const formData = new FormData(form);
            
            // IMPORTANT: Remove the temporary purchase_formula_data field from form data before sending
            formData.delete('purchase_formula_data'); 

            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';
            statusMessage.classList.add('hidden');

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                     const responseData = await response.json();
                    
                    if (response.ok) {
                        // SUCCESS
                        renderPurchaseRow(responseData.purchase); 
                        
                        statusMessage.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4';
                        statusMessage.querySelector('span').textContent = responseData.message || 'Purchase saved successfully!';
                        statusMessage.classList.remove('hidden');

                        // Reset input fields
                        inputs.gross_weight.value = 0;
                        inputs.dead_weight.value = 0;
                        inputs.dead_qty.value = 0;
                        inputs.shrink_loss.value = 0;
                        inputs.buying_rate.value = 0;
                        inputs.driver_no.value = '';
                        
                        calculateTotals(); // Recalculate and reset display

                    } else if (response.status === 422) {
                        let errorMsg = 'Validation Failed: ';
                        if (responseData.errors) {
                            errorMsg += Object.values(responseData.errors).flat().join(' | ');
                        }
                        statusMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                        statusMessage.querySelector('span').textContent = errorMsg;
                        statusMessage.classList.remove('hidden');

                    } else {
                        statusMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                        statusMessage.querySelector('span').textContent = responseData.message || 'An unexpected server error occurred.';
                        statusMessage.classList.remove('hidden');
                    }
                } else {
                     statusMessage.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
                     statusMessage.querySelector('span').textContent = 'Fatal Server Error (Non-JSON Response). Check server logs.';
                     statusMessage.classList.remove('hidden');
                }


            } 
            finally {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Purchase';
            }
        });
        
        // --- Event Listeners (Triggers calculateTotals) ---
        const calculationInputs = [
            inputs.gross_weight, inputs.dead_weight, inputs.shrink_loss, inputs.buying_rate, inputs.dead_qty
        ];

        calculationInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', calculateTotals);
                input.addEventListener('focus', () => input.classList.add('input-focus'));
                input.addEventListener('blur', () => input.classList.remove('input-focus'));
            }
        });
        
        calculateTotals();
    });
</script>
@endsection