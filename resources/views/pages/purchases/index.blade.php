@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary: #2563eb;
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

        /* 🟢 FIX: Prevent horizontal scroll globally */
        html, body { 
            box-sizing: border-box;
            overflow-x: hidden; 
            width: 100%;
            max-width: 100%;
        } 
        
        /* 🟢 FIX: Ensure all elements respect box model */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        .input-focus { box-shadow: 0 0 0 2px var(--blue-light), 0 0 0 4px var(--blue-primary); }
    </style>

    {{-- 🟢 FIX: Added overflow-x-hidden and max-w-full to main wrapper --}}
    <div class="flex-1 sm:p-6 lg:p-8 bg-gray-100 w-full max-w-full overflow-x-hidden">

        <form method="POST" action="{{ route('admin.purchases.store') }}" id="purchaseForm" class="flex flex-col gap-4 w-full">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="id" id="purchase_id_hidden">

            {{-- HEADER --}}
            <div class="px-3 sm:px-6 py-2 flex flex-col justify-center">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-2">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-[var(--text-dark)] leading-tight">Purchase Entry</h2>
                        <p class="text-[var(--text-gray)] text-sm mt-0.5">Today: {{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-0 sm:px-6 pb-4 flex flex-col gap-4 w-full">

                {{-- 1. TRUCK DETAILS --}}
                <div class="bg-[var(--card-bg)] p-3 sm:p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col w-full">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 border-b border-[var(--border-light)] pb-2 gap-2">
                        <h3 class="font-bold text-[var(--text-dark)] text-lg sm:text-xl">1. Truck Details</h3>
                        <button type="button" onclick="openModal()" class="w-full sm:w-auto bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2 px-3 rounded-lg flex justify-center items-center gap-2 transition-colors shadow-sm">
                            <i class="fas fa-plus"></i> New Supplier
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                        <div class="flex flex-col w-full">
                            <label for="supplier_id" class="text-[var(--text-gray)] text-sm font-medium mb-1">Select Supplier</label>
                            {{-- 🟢 FIX: min-w-0 prevents select from forcing width --}}
                            <select name="supplier_id" id="supplier_id" required class="min-w-0 w-full border border-[var(--border-light)] bg-white rounded-lg p-2 text-sm text-[var(--text-dark)] focus:ring-2 focus:ring-[var(--blue-primary)] transition-all h-[42px]">
                                @if(isset($suppliers))
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-balance="{{ $supplier->current_balance }}">
                                            {{ $supplier->name }} (Bal: {{ number_format($supplier->current_balance) }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="flex flex-col w-full">
                            <label for="driver_no" class="text-[var(--text-gray)] text-sm font-medium mb-1">Driver No <span class="text-xs font-normal text-gray-400">(Optional)</span></label>
                            <input type="text" name="driver_no" id="driver_no" class="min-w-0 w-full border border-[var(--border-light)] rounded-lg p-2 text-sm transition-all h-[42px]" placeholder="Ex: LEA-1234">
                        </div>
                    </div>
                </div>

                {{-- 2. WEIGHT CALCULATION --}}
           <div class="bg-[var(--card-bg)] p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col w-full">
    {{-- Header --}}
    <div class="flex items-center gap-2 h-[30px] mb-4">
        <h3 class="font-bold text-[var(--text-dark)] text-lg lg:text-xl">2. Weight Calculation</h3>
        <span class="text-xs lg:text-md bg-[var(--blue-primary)] text-white px-2 lg:px-3 py-0.5 rounded-full">Live</span>
    </div>

    {{-- 🟢 RESPONSIVE GRID: 1 column on mobile, 3 columns on Large screens (Desktop) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-4 h-full w-full">
        
        {{-- 1. GROSS WEIGHT --}}
        <div class="flex flex-col justify-end h-full">
            <label for="gross_weight" class="text-[var(--text-gray)] text-md mb-2">Gross Weight</label>
            <div class="flex items-end gap-2 lg:gap-3 h-full">
                <div class="relative w-full">
                    {{-- Responsive Text Size: 3xl on mobile, 4xl on desktop --}}
                    <input type="number" name="gross_weight" id="gross_weight" value="0" min="0" required 
                        class="text-3xl lg:text-4xl font-bold w-full p-3 lg:p-4 pr-10 lg:pr-12 border border-[var(--border-light)] rounded-lg text-[var(--text-dark)] focus:border-[var(--blue-primary)] transition-all min-w-0">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--text-gray)] font-semibold">KG</span>
                </div>
                {{-- Hide icon on tiny screens, show on small+ --}}
                <div class="hidden sm:flex w-[40px] h-[40px] rounded-full bg-[var(--text-gray)] items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-minus text-white text-lg"></i>
                </div>
            </div>
        </div>

        {{-- 2. DEDUCTIONS --}}
        <div class="flex flex-col justify-end h-full mt-2 lg:mt-0">
            <h4 class="font-semibold text-[var(--red-primary)] text-sm mb-2">Deductions (Weight)</h4>
            <div class="py-2 px-3 rounded-xl border h-full flex flex-col justify-center lg:justify-between w-full">
                {{-- Used Grid here to ensure perfect 3-column split on mobile too --}}
                <div class="grid grid-cols-3 gap-2 w-full">
                    
                    {{-- Dead Qty --}}
                    <div class="flex flex-col min-w-0">
                        <label for="dead_qty" class="text-[10px] lg:text-xs text-[var(--red-primary)] mb-1 block text-center lg:text-left">Dead Qty</label>
                        <div class="relative w-full">
                            <input type="number" name="dead_qty" id="dead_qty" value="0" min="0" 
                                class="w-full bg-red-100 p-1 lg:p-2 h-9 lg:h-8 border border-[var(--border-light)] rounded-md text-sm lg:text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all text-center lg:text-left min-w-0">
                            <span class="hidden lg:block absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">Pcs</span>
                        </div>
                    </div>

                    {{-- Dead Weight --}}
                    <div class="flex flex-col min-w-0">
                        <label for="dead_weight" class="text-[10px] lg:text-xs text-[var(--red-primary)] mb-1 block text-center lg:text-left">Dead Wgt</label>
                        <div class="relative w-full">
                            <input type="number" name="dead_weight" id="dead_weight" value="0" min="0" 
                                class="w-full bg-red-100 p-1 lg:p-2 h-9 lg:h-8 border border-[var(--border-light)] rounded-md text-sm lg:text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all text-center lg:text-left min-w-0">
                            <span class="hidden lg:block absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">Kg</span>
                        </div>
                    </div>

                    {{-- Shrink Loss --}}
                    <div class="flex flex-col min-w-0">
                        <label for="shrink_loss" class="text-[10px] lg:text-xs text-[var(--red-primary)] mb-1 block text-center lg:text-left">Shrink</label>
                        <div class="relative w-full">
                            <input type="number" name="shrink_loss" id="shrink_loss" value="0" min="0" step="0.01" 
                                class="w-full bg-red-100 p-1 lg:p-2 h-9 lg:h-8 border border-[var(--border-light)] rounded-md text-sm lg:text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all text-center lg:text-left min-w-0">
                            <span class="hidden lg:block absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">Kg</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- 3. NET WEIGHT --}}
        <div class="flex items-end gap-2 lg:gap-4 h-full mt-2 lg:mt-0">
            <div class="hidden sm:flex w-[40px] h-[40px] rounded-full bg-[var(--text-gray)] items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-equals text-white text-lg"></i>
            </div>
            <div class="flex flex-col gap-1 w-full">
                <label class="text-[var(--green-primary)] font-semibold text-sm">Net Live Weight</label>
                <div class="flex items-center gap-2 bg-[var(--green-light)] border border-[var(--green-primary)] rounded-md p-3 lg:p-4 w-full">
                    <input type="number" name="net_live_weight" id="net_live_weight" value="0" readonly 
                        class="bg-transparent outline-none w-full text-3xl lg:text-4xl text-left font-extrabold text-[var(--green-primary)] cursor-not-allowed min-w-0">
                    <span class="text-[var(--green-primary)] font-semibold text-sm">KG</span>
                </div>
            </div>
        </div>
    </div>
</div>

                {{-- 🟢 RATES & PAYMENT SECTION --}}
                <div class="bg-gray-800 p-4 sm:p-5 rounded-2xl shadow-lg flex flex-col w-full">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3">
                        <h2 class="text-white text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-coins text-yellow-400"></i> Rates & Payment
                        </h2>
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <button type="button" id="cancelEditBtn" onclick="resetFormToCreate()" class="hidden flex-1 sm:flex-none justify-center px-4 bg-gray-600 hover:bg-gray-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all h-[40px] items-center gap-2">
                                <i class="fas fa-times text-base"></i> <span>Cancel</span>
                            </button>
                            <button type="submit" id="savePurchaseBtn" class="flex-1 sm:flex-none w-full sm:w-auto px-4 bg-[var(--yellow-primary)] hover:bg-[var(--yellow-dark)] text-black font-bold text-sm rounded-xl shadow-lg transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 h-[40px] min-w-[140px]">
                                <i class="fas fa-plus text-base"></i> <span id="saveBtnText">Add Purchase</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch w-full">
                        {{-- 1. Buying Rate --}}
                        <div class="bg-gray-700 p-3 rounded-xl flex flex-col justify-center w-full">
                            <label for="buying_rate" class="text-gray-300 text-xs mb-1 font-medium">Buying Rate (Per Kg)</label>
                            <div class="relative w-full">
                                <input type="number" name="buying_rate" id="buying_rate" value="0" min="0" required class="min-w-0 w-full p-2.5 pr-10 text-black font-bold rounded-lg focus:outline-none text-sm focus:ring-2 focus:ring-yellow-400 transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs font-bold">Rs</span>
                            </div>
                        </div>

                        {{-- 3. Total kharch --}}
                        <div class="bg-gray-700 p-3 rounded-xl flex flex-col justify-center border border-gray-600 w-full">
                            <label for="total_kharch" class="text-green-300 text-xs mb-1 font-bold">Total Kharch</label>
                            <div class="relative w-full">
                                <input type="number" name="total_kharch" id="total_kharch" value="0" min="0" 
                                    class="min-w-0 w-full p-2.5 pr-10 text-black font-bold rounded-lg focus:outline-none text-sm focus:ring-2 focus:ring-green-400 transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs font-bold">Rs</span>
                            </div>
                        </div>

                        {{-- 3. CASH PAID --}}
                        <div class="bg-gray-700 p-3 rounded-xl flex flex-col justify-center border border-gray-600 w-full">
                            <label for="cash_paid" class="text-green-300 text-xs mb-1 font-bold">Cash Paid</label>
                            <div class="relative w-full">
                                <input type="number" name="cash_paid" id="cash_paid" value="0" min="0" 
                                    class="min-w-0 w-full p-2.5 pr-10 text-black font-bold rounded-lg focus:outline-none text-sm focus:ring-2 focus:ring-green-400 transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs font-bold">Rs</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1 text-right">Remaining: <span id="remaining_balance_display" class="text-red-300">0</span></p>
                        </div>
                        

                        {{-- 2. Total Payable --}}
                        <div class="bg-gray-900 p-3 rounded-xl flex flex-col justify-center border border-gray-700 w-full">
                            <label class="text-gray-400 text-xs mb-1 font-medium">Total Payable Amount</label>
                            <input type="text" name="total_payable_display" id="total_payable_display" value="Rs 0" readonly class="min-w-0 w-full p-2.5 bg-gray-800 text-white font-mono text-lg font-bold rounded-lg focus:outline-none cursor-not-allowed border border-gray-700">
                            <input type="hidden" name="total_payable" id="total_payable" value="0">
                        </div>
                        
                        {{-- 4. Effective Cost --}}
                        @php
                            $defaultFormula = ['multiply' => 1.0, 'divide' => 1.0, 'plus' => 0.0, 'minus' => 0.0];
                            $formulaData = $defaultFormula;
                            try {
                                $purchaseFormula = \App\Models\RateFormula::where('rate_key', 'purchase_effective_cost')->first();
                                if ($purchaseFormula) {
                                    $formulaData['multiply'] = $purchaseFormula->multiply;
                                    $formulaData['divide'] = $purchaseFormula->divide;
                                    $formulaData['plus'] = $purchaseFormula->plus;
                                    $formulaData['minus'] = $purchaseFormula->minus;
                                }
                            } catch (\Throwable $e) {}
                        @endphp
                        <div class="bg-[var(--blue-primary)] p-3 rounded-xl text-center shadow-md flex flex-col justify-center relative overflow-hidden group min-h-[80px] w-full">
                            <i class="fas fa-calculator absolute -right-2 -bottom-2 text-4xl text-white opacity-10 transform rotate-12 group-hover:scale-110 transition-transform"></i>
                            <p class="text-blue-100 text-[20px] uppercase tracking-wider font-semibold">Rate</p>
                            <h1 class="text-2xl font-extrabold text-white leading-none my-1">
                                <span id="effective_cost_display">0</span><span class="text-sm font-medium opacity-80">/kg</span>
                            </h1>
                            <input type="hidden" name="effective_cost" id="effective_cost" value="0">
                           <input type="hidden" id="purchase_formula_data" value='@json($formulaData)'>
                            <div class="mt-2 flex justify-center w-full">
                                <div class=" px-3 py-1.5 text-xs text-white font-mono flex items-center gap-2 shadow-sm backdrop-blur-sm">
                                    
                                    <span class="text-blue-200 uppercase tracking-wider font-bold text-[10px]">value:</span>
                                    
                                    <div class="flex gap-2">
                                        <span class="flex items-center" title="Multiply">
                                            <i class="fas fa-times text-[10px] mr-1 text-yellow-300 opacity-80"></i> 
                                            {{ $formulaData['multiply'] ?? 1 }}
                                        </span>

                                        <span class="flex items-center border-l border-white/20 pl-2" title="Divide">
                                            <i class="fas fa-divide text-[10px] mr-1 text-blue-300 opacity-80"></i> 
                                            {{ $formulaData['divide'] ?? 1 }}
                                        </span>

                                        @if(($formulaData['plus'] ?? 0) > 0)
                                        <span class="flex items-center border-l border-white/20 pl-2" title="Add">
                                            <i class="fas fa-plus text-[10px] mr-1 text-green-300 opacity-80"></i> 
                                            {{ $formulaData['plus'] }}
                                        </span>
                                        @endif

                                        @if(($formulaData['minus'] ?? 0) > 0)
                                        <span class="flex items-center border-l border-white/20 pl-2" title="Subtract">
                                            <i class="fas fa-minus text-[10px] mr-1 text-red-300 opacity-80"></i> 
                                            {{ $formulaData['minus'] }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. HISTORY TABLE --}}
                <div class="bg-[var(--card-bg)] p-3 sm:p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col mt-4 w-full">
                    <h3 class="font-bold text-[var(--text-dark)] text-lg sm:text-xl mb-3">4. Purchase History ({{ now()->format('d M Y') }})</h3>
                    
                    {{-- 🟢 FIX: Constrain table container width to viewport --}}
                    <div class="overflow-x-auto border rounded-lg w-full max-w-[calc(100vw-2.5rem)]">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Supplier</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Gross Wgt</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Net Wgt</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Buying Rate</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Payable</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Cost Rate/Kg</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-table-body" class="bg-white divide-y divide-gray-200">
                                @forelse ($purchases as $purchase)
                                    <tr id="purchase-row-{{ $purchase->id }}">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 whitespace-nowrap">#{{ $purchase->id }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                            <div class="font-semibold">{{ $purchase->supplier_name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $purchase->created_at_human }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-[var(--green-primary)] whitespace-nowrap">{{ number_format($purchase->gross_weight, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold text-[var(--green-primary)] whitespace-nowrap">{{ number_format($purchase->net_live_weight, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700 whitespace-nowrap">{{ number_format($purchase->buying_rate, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-extrabold text-[var(--text-dark)] whitespace-nowrap">{{ number_format($purchase->total_payable, 0) }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-[var(--blue-primary)] font-bold whitespace-nowrap">{{ number_format($purchase->effective_cost, 0) }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium whitespace-nowrap">
                                            <button type="button" onclick='editPurchase(this)' 
                                                data-id="{{ $purchase->id }}" data-supplier="{{ $purchase->supplier_id }}" 
                                                data-gross="{{ $purchase->gross_weight }}" data-dead-qty="{{ $purchase->dead_qty }}" 
                                                data-dead-wgt="{{ $purchase->dead_weight }}" data-shrink="{{ $purchase->shrink_loss }}" 
                                                data-rate="{{ $purchase->buying_rate }}" data-driver="{{ $purchase->driver_no }}"
                                                class="text-blue-600 hover:text-blue-900 mr-3"><i class="fa-solid fa-pen-to-square"></i></button>
                                            <button type="button" onclick="deletePurchase({{ $purchase->id }})" class="text-[var(--red-primary)] hover:text-red-700"><i class="fa-solid fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="no-purchases-row"><td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">No recent purchases found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- MODAL (Width fixed for mobile) --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all w-full max-w-[90vw] sm:max-w-md border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Add New Supplier</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" name="type" value="supplier">
                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" value="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-col-reverse sm:flex-row-reverse gap-2 sm:gap-0">
                        <button type="submit" id="saveContactBtn" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white">Save Supplier</button>
                        <button type="button" onclick="closeModal()" class="w-full sm:w-auto mt-0 sm:mr-3 inline-flex justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 border border-gray-300">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const BASE_URL = "{{ route('admin.purchases.store') }}";
        const STORE_CONTACT_URL = "{{ route('admin.contacts.store') }}";

        function openModal() { document.getElementById('contactModal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('contactModal').classList.add('hidden'); }

        document.getElementById('contactForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('saveContactBtn');
            btn.disabled = true; btn.textContent = 'Saving...';
            try {
                const res = await fetch(STORE_CONTACT_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(e.target) });
                if(res.ok) {
                    const data = await res.json();
                    const select = document.getElementById('supplier_id');
                    select.add(new Option(data.contact.name, data.contact.id, true, true));
                    closeModal(); Swal.fire('Saved', 'Supplier added', 'success');
                }
            } catch (err) { Swal.fire('Error', 'Failed', 'error'); } 
            finally { btn.disabled = false; btn.textContent = 'Save Supplier'; }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const inputs = {
                gross_weight: document.getElementById('gross_weight'),
                dead_weight: document.getElementById('dead_weight'),
                shrink_loss: document.getElementById('shrink_loss'),
                buying_rate: document.getElementById('buying_rate'),
                cash_paid: document.getElementById('cash_paid'),
                total_kharch: document.getElementById('total_kharch'),
            };

            const outputs = {
                net_live_weight: document.getElementById('net_live_weight'),
                total_payable: document.getElementById('total_payable'),
                total_payable_display: document.getElementById('total_payable_display'),
                effective_cost: document.getElementById('effective_cost'),
                effective_cost_display: document.getElementById('effective_cost_display'),
                remaining_balance_display: document.getElementById('remaining_balance_display'),
            };

            const purchaseFormula = JSON.parse(document.getElementById('purchase_formula_data').value || '{"multiply":1,"divide":1,"plus":0,"minus":0}');

            function parseInput(el) { return parseFloat(el ? el.value || 0 : 0) || 0; }
            function formatCurrency(n) { return Math.round(n).toLocaleString(); }

            window.calculateTotals = function () {
                const gross = parseInput(inputs.gross_weight);
                const dead = parseInput(inputs.dead_weight);
                const shrink = parseInput(inputs.shrink_loss);
                const rate = parseInput(inputs.buying_rate);
                const cash = parseInput(inputs.cash_paid);
                const total_kharch = parseInput(inputs.total_kharch);
                

                let net = Math.max(0, gross - dead - shrink);
                const total = gross * rate;
                
                // Effective Cost Formula
                let base = (total+total_kharch)/net;
                let effective = base * purchaseFormula.multiply / purchaseFormula.divide + purchaseFormula.plus - purchaseFormula.minus;

                outputs.net_live_weight.value = Math.max(0, net).toFixed(2);
                outputs.total_payable.value = Math.max(0, total).toFixed(2);
                outputs.total_payable_display.value = `Rs ${formatCurrency(total)}`;
                outputs.effective_cost.value = Math.max(0, effective).toFixed(2);
                outputs.effective_cost_display.textContent = (Math.floor(Math.max(0, effective) * 100) / 100).toFixed(2);
                // REMAINING BALANCE CALCULATION
                const remaining = total - cash;
                outputs.remaining_balance_display.textContent = formatCurrency(remaining);
                
                if (remaining > 0) {
                    outputs.remaining_balance_display.className = "text-red-300 font-bold";
                    outputs.remaining_balance_display.textContent += " (Credit)";
                } else if (remaining < 0) {
                    outputs.remaining_balance_display.className = "text-green-300 font-bold";
                    outputs.remaining_balance_display.textContent = formatCurrency(Math.abs(remaining)) + " (Advance)";
                } else {
                    outputs.remaining_balance_display.className = "text-gray-400";
                    outputs.remaining_balance_display.textContent = "0 (Paid)";
                }
            };

            // Attach listeners
            Object.values(inputs).forEach(input => { if(input) input.addEventListener('input', window.calculateTotals); });
            window.calculateTotals();

            // Form Submit Logic
            document.getElementById('purchaseForm').addEventListener('submit', async function (e) {
                e.preventDefault();
                window.calculateTotals();
                const formData = new FormData(this);
                const btn = document.getElementById('savePurchaseBtn');
                btn.disabled = true; btn.textContent = 'Processing...';

                try {
                    const res = await fetch(this.action, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: formData });
                    const data = await res.json();
                    if(res.ok) {
                        Swal.fire('Success', 'Purchase saved', 'success');
                        this.reset();
                        window.calculateTotals();
                        setTimeout(() => location.reload(), 1000); 
                    } else {
                        Swal.fire('Error', data.message || 'Error', 'error');
                    }
                } catch (err) { Swal.fire('Error', 'Network Error', 'error'); }
                finally { btn.disabled = false; btn.innerHTML = '<i class="fas fa-plus text-base"></i> <span id="saveBtnText">Add Purchase</span>'; }
            });
        });
        
        window.editPurchase = function(btn) {
            document.getElementById('purchaseForm').action = BASE_URL.replace('/store', '') + '/' + btn.getAttribute('data-id');
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('purchase_id_hidden').value = btn.getAttribute('data-id');

            document.getElementById('supplier_id').value = btn.getAttribute('data-supplier');
            document.getElementById('driver_no').value = btn.getAttribute('data-driver');
            document.getElementById('gross_weight').value = btn.getAttribute('data-gross');
            document.getElementById('dead_qty').value = btn.getAttribute('data-dead-qty');
            document.getElementById('dead_weight').value = btn.getAttribute('data-dead-wgt');
            document.getElementById('shrink_loss').value = btn.getAttribute('data-shrink');
            document.getElementById('buying_rate').value = btn.getAttribute('data-rate');
            
            document.getElementById('saveBtnText').textContent = 'Update';
            document.getElementById('cancelEditBtn').classList.remove('hidden');
            document.getElementById('cancelEditBtn').classList.add('flex');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            window.calculateTotals();
        }
        
        window.deletePurchase = async function(id) {
             if(confirm('Delete this purchase?')) {
                 await fetch(BASE_URL.replace('/store', '') + '/' + id, { 
                     method: 'POST', 
                     headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'Content-Type': 'application/json' },
                     body: JSON.stringify({_method: 'DELETE'})
                 });
                 location.reload();
             }
        }
        
        window.resetFormToCreate = function() {
            document.getElementById('purchaseForm').reset();
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('purchaseForm').action = BASE_URL;
            document.getElementById('saveBtnText').textContent = 'Add Purchase';
            document.getElementById('cancelEditBtn').classList.add('hidden');
            document.getElementById('cancelEditBtn').classList.remove('flex');
            window.calculateTotals();
        }
    </script>
@endsection