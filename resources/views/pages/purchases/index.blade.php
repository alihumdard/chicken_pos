@extends('layouts.main')

@section('content')
    {{-- 🟢 SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* ... (CSS styles remain the same) ... */
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

        .input-focus {
            box-shadow: 0 0 0 2px var(--blue-light), 0 0 0 4px var(--blue-primary);
        }
    </style>

    <div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">

        {{-- 🟢 Form Action is set dynamically by JS for Edit/Create --}}
        <form method="POST" action="{{ route('admin.purchases.store') }}" id="purchaseForm" class="flex flex-col h-full">
            @csrf
            {{-- 🟢 Hidden Method Input for switching between POST and PUT --}}
            <input type="hidden" name="_method" id="formMethod" value="POST">
            {{-- 🟢 Hidden ID for Update --}}
            <input type="hidden" name="id" id="purchase_id_hidden">

            <div class="h-[80px] px-6 flex flex-col justify-center">
                <div class="flex justify-between items-end">
                    <div>
                        <h2 class="text-2xl font-bold text-[var(--text-dark)] leading-tight">Purchase Entry</h2>
                        <p class="text-[var(--text-gray)] text-sm mt-0.5">Today: {{ now()->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-6 pb-4 flex flex-col gap-4 overflow-y-auto">

                <div class="bg-[var(--card-bg)] p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col">

                    {{-- 🟢 ROW 1: Header Title + Action Button --}}
                    <div class="flex justify-between items-center mb-4 border-b border-[var(--border-light)] pb-2">
                        <h3 class="font-bold text-[var(--text-dark)] text-xl">
                            1. Truck Details
                        </h3>

                        {{-- Add Supplier Button --}}
                        <button type="button" onclick="openModal()"
                            class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-1.5 px-3 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                            <i class="fas fa-plus"></i> New Supplier
                        </button>
                    </div>

                    {{-- 🟢 ROW 2: Inputs (Supplier & Driver) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                        {{-- Column 1: Supplier Select --}}
                        <div class="flex flex-col">
                            <label for="supplier_id" class="text-[var(--text-gray)] text-sm font-medium mb-1">
                                Select Supplier
                            </label>
                            <select name="supplier_id" id="supplier_id" required
                                class="w-full border border-[var(--border-light)] bg-white rounded-lg p-2.5 text-sm text-[var(--text-dark)] focus:ring-2 focus:ring-[var(--blue-primary)] focus:border-[var(--blue-primary)] transition-all h-[42px]">
                                @if(isset($suppliers))
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" data-name="{{ $supplier->name }}">
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- Column 2: Driver Number --}}
                        <div class="flex flex-col">
                            <label for="driver_no" class="text-[var(--text-gray)] text-sm font-medium mb-1">
                                Driver No <span class="text-xs font-normal text-gray-400">(Optional)</span>
                            </label>
                            <input type="text" name="driver_no" id="driver_no"
                                class="w-full border border-[var(--border-light)] rounded-lg p-2.5 text-sm text-[var(--text-dark)] focus:ring-2 focus:ring-[var(--blue-primary)] focus:border-[var(--blue-primary)] transition-all h-[42px]"
                                placeholder="Ex: LEA-1234">
                        </div>

                    </div>
                </div>

                <div class="bg-[var(--card-bg)] p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col">

                    <div class="flex items-center gap-2 h-[30px] mb-2">
                        <h3 class="font-bold text-[var(--text-dark)] text-xl">2. Weight Calculation</h3>
                        <span class="text-md bg-[var(--blue-primary)] text-white px-3 rounded-full">
                            Live
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
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-[var(--text-gray)] font-semibold">KG</span>
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
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">Pcs</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <label for="dead_weight" class="text-xs text-[var(--red-primary)] mb-1 block">Dead
                                        Wgt</label>
                                    <div class="relative">
                                        <input type="number" name="dead_weight" id="dead_weight" value="0" min="0"
                                            class="w-full bg-red-100 p-2 h-8 pr-10 border border-[var(--border-light)] rounded-md text-lg font-bold text-[var(--red-primary)] focus:border-[var(--red-primary)] transition-all" />
                                        <span
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">Kg</span>
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
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-md text-[var(--red-primary)]">Kg</span>
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
                                <label class="text-[var(--green-primary)] font-semibold text-sm">Net Live Weight</label>
                                <div
                                    class="flex items-center gap-2 bg-[var(--green-light)] border border-[var(--green-primary)] rounded-md p-2">
                                    <input type="number" name="net_live_weight" id="net_live_weight" value="0" readonly
                                        class="bg-transparent outline-none w-full text-4xl text-center font-extrabold text-[var(--green-primary)] cursor-not-allowed">
                                    <span class="text-[var(--green-primary)] font-semibold text-sm flex-shrink-0">KG</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800 p-5 rounded-2xl shadow-lg flex flex-col">

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-white text-lg font-semibold flex items-center gap-2">
                            <i class="fas fa-coins text-yellow-400"></i> Weight, Rates & Action
                        </h2>

                        {{-- 🟢 BUTTONS GROUP: Cancel + Save --}}
                        <div class="flex items-center gap-3">
                            {{-- 1. Cancel Edit Button --}}
                            <button type="button" id="cancelEditBtn" onclick="resetFormToCreate()"
                                class="hidden px-4 bg-gray-600 hover:bg-gray-500 text-white font-bold text-sm rounded-xl shadow-lg transition-all h-[40px] items-center gap-2">
                                <i class="fas fa-times text-base"></i>
                                <span>Cancel</span>
                            </button>

                            {{-- 2. Save Button --}}
                            <button type="submit" id="savePurchaseBtn"
                                class="px-4 bg-[var(--yellow-primary)] hover:bg-[var(--yellow-dark)] text-black font-bold text-sm rounded-xl shadow-lg transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 h-[40px] min-w-[140px]">
                                {{-- Changed icon from fa-save to fa-plus --}}
                                <i class="fas fa-plus text-base"></i>
                                <span id="saveBtnText">Add Purchase</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-stretch">

                        {{-- 1. Buying Rate --}}
                        <div class="bg-gray-700 p-3 rounded-xl flex flex-col justify-center">
                            <label for="buying_rate" class="text-gray-300 text-xs mb-1 font-medium">Buying Rate (Per
                                Kg)</label>
                            <div class="relative w-full">
                                <input type="number" name="buying_rate" id="buying_rate" value="0" min="0" required
                                    class="w-full p-2.5 pr-10 text-black font-bold rounded-lg focus:outline-none text-sm focus:ring-2 focus:ring-yellow-400 transition-all">
                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 text-xs font-bold">Rs</span>
                            </div>
                        </div>

                        {{-- 2. Total Payable --}}
                        <div class="bg-gray-900 p-3 rounded-xl flex flex-col justify-center border border-gray-700">
                            <label class="text-gray-400 text-xs mb-1 font-medium">Total Payable Amount</label>
                            <input type="text" name="total_payable_display" id="total_payable_display" value="Rs 0" readonly
                                class="w-full p-2.5 bg-gray-800 text-white font-mono text-lg font-bold rounded-lg focus:outline-none cursor-not-allowed border border-gray-700">
                            <input type="hidden" name="total_payable" id="total_payable" value="0">
                        </div>

                        {{-- 3. Effective Cost (Dynamic Formula) --}}
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
                            } catch (\Throwable $e) {
                            }
                            $pFormulaText = "×{$formulaData['multiply']} ÷{$formulaData['divide']} +{$formulaData['plus']} -{$formulaData['minus']}";
                        @endphp

                        <div
                            class="bg-[var(--blue-primary)] p-3 rounded-xl text-center shadow-md flex flex-col justify-center relative overflow-hidden group min-h-[80px]">
                            <i
                                class="fas fa-calculator absolute -right-2 -bottom-2 text-4xl text-white opacity-10 transform rotate-12 group-hover:scale-110 transition-transform"></i>

                            <p class="text-blue-100 text-[14px] uppercase tracking-wider font-semibold">Effective Cost</p>
                            <h1 class="text-2xl font-extrabold text-white leading-none my-1">
                                <span id="effective_cost_display">0</span><span
                                    class="text-sm font-medium opacity-80">/kg</span>
                            </h1>
                            <p class="text-[14px] text-blue-200 opacity-80" id="effective_cost_formula_display">
                                ( value {{ $pFormulaText }} )
                            </p>

                            <input type="hidden" name="effective_cost" id="effective_cost" value="0">
                            <input type="hidden" id="purchase_formula_data" value='@json($formulaData)'>
                        </div>

                    </div>
                </div>

                {{-- 🟢 PURCHASE HISTORY TABLE --}}
                <div
                    class="bg-[var(--card-bg)] p-4 rounded-xl shadow-sm border border-[var(--border-light)] flex flex-col mt-4">
                    <h3 class="font-bold text-[var(--text-dark)] text-xl mb-3">4. Purchase History
                        ({{ now()->format('d M Y') }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                        ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">
                                        Date / Supplier</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">
                                        Net Wgt (Kg)</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">
                                        Rate (Rs)</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">
                                        Payable (Rs)</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                        Cost/Kg</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody id="purchase-table-body" class="bg-white divide-y divide-gray-200">
                                @forelse (isset($purchases) ? $purchases : [] as $purchase)
                                    <tr id="purchase-row-{{ $purchase->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            #{{ $purchase->id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{-- Use ?? '' to prevent crashes if name is missing --}}
                                            <div class="font-semibold">{{ $purchase->supplier_name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">Driver: {{ $purchase->driver_no ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">
                                                {{ \Carbon\Carbon::parse($purchase->created_at)->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-[var(--green-primary)]">
                                            {{ number_format($purchase->net_live_weight ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700">
                                            {{ number_format($purchase->buying_rate ?? 0, 2) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-extrabold text-[var(--text-dark)]">
                                            {{ number_format($purchase->total_payable ?? 0, 0) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-center text-[var(--blue-primary)] font-bold">
                                            {{ number_format($purchase->effective_cost ?? 0, 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">

                                            {{-- 🟢 FIXED EDIT BUTTON: Added ?? '' to all data attributes --}}
                                            <button type="button" onclick='editPurchase(this)' data-id="{{ $purchase->id }}"
                                                data-supplier="{{ $purchase->supplier_id ?? '' }}"
                                                data-driver="{{ $purchase->driver_no ?? '' }}"
                                                data-gross="{{ $purchase->gross_weight ?? 0 }}"
                                                data-dead-qty="{{ $purchase->dead_qty ?? 0 }}"
                                                data-dead-wgt="{{ $purchase->dead_weight ?? 0 }}"
                                                data-shrink="{{ $purchase->shrink_loss ?? 0 }}"
                                                data-rate="{{ $purchase->buying_rate ?? 0 }}"
                                                class="text-blue-600 hover:text-blue-900 transition-colors mr-3">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            <button type="button" onclick="deletePurchase({{ $purchase->id }})"
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

    {{-- 🟢 MODAL FOR ADDING SUPPLIER (Updated with Phone & Address) --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()">
        </div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Supplier</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" name="type" value="supplier">

                    <div class="px-6 py-6 space-y-5">
                        {{-- Name --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300"
                                placeholder="e.g. Ali Poultry (Truck)">
                            <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>

                        {{-- 🟢 NEW: Phone Number (Optional) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span
                                    class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                            <input type="text" name="phone" id="contactPhone"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300"
                                placeholder="e.g. 0300-1234567">
                        </div>

                        {{-- 🟢 NEW: Address (Optional) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                    class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                            <textarea name="address" id="contactAddress" rows="2"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300 resize-none"
                                placeholder="e.g. Street 1, Lahore"></textarea>
                        </div>

                        {{-- Opening Balance --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" id="openingBalance" value="0"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300">
                            <p class="text-xs text-gray-400 mt-1">Positive = They owe us. Negative = We owe them.</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse">
                        <button type="submit" id="saveContactBtn"
                            class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 transition-colors">
                            Save Supplier
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="mt-3 inline-flex w-full sm:w-auto justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:mr-3 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global URLs
        const BASE_URL = "{{ route('admin.purchases.store') }}"; // Base URL for Store
        // NOTE: For update, we will append ID to this URL or use a dedicated route in logic below
        const STORE_CONTACT_URL = "{{ route('admin.contacts.store') }}";

        // 🟢 MODAL FUNCTIONS
        function openModal() {
            const modal = document.getElementById('contactModal');
            const form = document.getElementById('contactForm');
            form.reset();
            document.getElementById('nameError').classList.add('hidden');
            modal.classList.remove('hidden');
            setTimeout(() => document.getElementById('contactName').focus(), 100);
        }

        function closeModal() {
            document.getElementById('contactModal').classList.add('hidden');
        }

        // 🟢 SUBMIT NEW SUPPLIER (AJAX)
        document.getElementById('contactForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('saveContactBtn');
            const nameError = document.getElementById('nameError');

            nameError.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            try {
                const response = await fetch(STORE_CONTACT_URL, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    if (response.status === 422 && errorData.errors?.name) {
                        nameError.textContent = errorData.errors.name[0];
                        nameError.classList.remove('hidden');
                    } else {
                        Swal.fire('Error', errorData.message || 'Error occurred', 'error');
                    }
                    return;
                }

                const data = await response.json();

                // 🟢 ADD NEW SUPPLIER TO DROPDOWN & SELECT IT
                const selectElement = document.getElementById('supplier_id');
                const newOption = new Option(data.contact.name, data.contact.id, true, true);
                selectElement.add(newOption, undefined); // Add to end of list

                closeModal();
                Swal.fire({
                    icon: 'success',
                    title: 'Supplier Added',
                    text: 'Supplier has been added and selected.',
                    timer: 1500,
                    showConfirmButton: false
                });

            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Network error.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Supplier';
            }
        });

        document.addEventListener('DOMContentLoaded', function () {

            if (!document.getElementById('gross_weight') || !document.getElementById('net_live_weight')) {
                console.error("Critical: Purchase view inputs are missing.");
                return;
            }

            const form = document.getElementById('purchaseForm');
            const saveBtn = document.getElementById('savePurchaseBtn');
            const saveBtnText = document.getElementById('saveBtnText');
            const purchaseTableBody = document.getElementById('purchase-table-body');
            const formMethodInput = document.getElementById('formMethod');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const purchaseIdHidden = document.getElementById('purchase_id_hidden');

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

            const csrfToken = document.querySelector('input[name="_token"]').value;

            // Formula Logic
            let purchaseFormula = null;
            const formulaDataElement = document.getElementById('purchase_formula_data');
            const nonModifyingDefault = { multiply: 1.0, divide: 1.0, plus: 0.0, minus: 0.0 };

            if (formulaDataElement && formulaDataElement.value) {
                try {
                    const raw = formulaDataElement.value.trim();
                    purchaseFormula = (raw === "" || raw === "{") ? nonModifyingDefault : JSON.parse(raw);
                } catch (e) {
                    purchaseFormula = nonModifyingDefault;
                }
            } else {
                purchaseFormula = nonModifyingDefault;
            }

            function parseInput(element) { return parseFloat(element ? element.value || 0 : 0) || 0; }

            function formatCurrency(number) {
                return new Intl.NumberFormat('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(Math.round(number));
            }

            // --- CALCULATION LOGIC ---
            window.calculateTotals = function () {
                const grossWeight = parseInput(inputs.gross_weight);
                const deadWeight = parseInput(inputs.dead_weight);
                const shrinkLoss = parseInput(inputs.shrink_loss);
                const buyingRate = parseInput(inputs.buying_rate);

                let netLiveWeight = grossWeight - deadWeight - shrinkLoss;
                if (netLiveWeight < 0) netLiveWeight = 0;

                const totalPayable = netLiveWeight * buyingRate;

                // Formula Application
                let baseCostForFormula = 0;
                if (netLiveWeight > 0) {
                    baseCostForFormula = (totalPayable / netLiveWeight);
                }

                // Formula: (Base * Mul / Div) + Plus - Minus
                let finalEffectiveCost = baseCostForFormula;
                finalEffectiveCost *= (parseFloat(purchaseFormula.multiply) || 1.0);
                const div = parseFloat(purchaseFormula.divide) || 1.0;
                if (div !== 0) finalEffectiveCost /= div;
                finalEffectiveCost += (parseFloat(purchaseFormula.plus) || 0);
                finalEffectiveCost -= (parseFloat(purchaseFormula.minus) || 0);
                finalEffectiveCost = Math.max(0, finalEffectiveCost);

                outputs.net_live_weight.value = netLiveWeight.toFixed(2);
                outputs.total_payable.value = totalPayable.toFixed(2);
                outputs.total_payable_display.value = `Rs ${formatCurrency(totalPayable)}`;
                outputs.effective_cost.value = finalEffectiveCost.toFixed(2);
                outputs.effective_cost_display.textContent = finalEffectiveCost.toFixed(0);

                // Visual feedback
                outputs.effective_cost_display.closest('div').style.backgroundColor = 'var(--blue-primary)';
            }

            // --- 🟢 RENDER TABLE ROW (Includes Edit Button) ---
            function renderPurchaseRow(purchase, isUpdate = false) {
                const supplierName = purchase.supplier_name || 'N/A';
                const netWgt = parseFloat(purchase.net_live_weight).toFixed(2);
                const buyingRate = parseFloat(purchase.buying_rate).toFixed(2);
                const totalPayable = formatCurrency(purchase.total_payable);
                const effectiveCost = formatCurrency(purchase.effective_cost);
                const timeDisplay = purchase.created_at_human || 'just now'; // Assume controller sends formatted date or handle JS date

                const rowHtml = `
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
                                    onclick='editPurchase(this)'
                                    data-id="${purchase.id}"
                                    data-supplier="${purchase.supplier_id}"
                                    data-driver="${purchase.driver_no || ''}"
                                    data-gross="${purchase.gross_weight}"
                                    data-dead-qty="${purchase.dead_qty}"
                                    data-dead-wgt="${purchase.dead_weight}"
                                    data-shrink="${purchase.shrink_loss}"
                                    data-rate="${purchase.buying_rate}"
                                    class="text-blue-600 hover:text-blue-900 transition-colors mr-3">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button" onclick="deletePurchase(${purchase.id})" class="text-[var(--red-primary)] hover:text-red-700 transition-colors">
                                    <i class="fa-solid fa-trash-alt"></i>
                                </button>
                            </td>
                        `;

                if (isUpdate) {
                    const existingRow = document.getElementById(`purchase-row-${purchase.id}`);
                    if (existingRow) existingRow.innerHTML = rowHtml;
                } else {
                    const newRow = document.createElement('tr');
                    newRow.id = `purchase-row-${purchase.id}`;
                    newRow.innerHTML = rowHtml;
                    const placeholderRow = document.getElementById('no-purchases-row');
                    if (placeholderRow) placeholderRow.remove();
                    purchaseTableBody.prepend(newRow);
                }
            }

            // --- 🟢 EDIT LOGIC ---
            window.editPurchase = function (btn) {
                // 1. Get data from data attributes
                const id = btn.getAttribute('data-id');
                const supplierId = btn.getAttribute('data-supplier');
                const driver = btn.getAttribute('data-driver');
                const gross = btn.getAttribute('data-gross');
                const deadQty = btn.getAttribute('data-dead-qty');
                const deadWgt = btn.getAttribute('data-dead-wgt');
                const shrink = btn.getAttribute('data-shrink');
                const rate = btn.getAttribute('data-rate');

                // 2. Populate Form
                inputs.supplier_id.value = supplierId;
                inputs.driver_no.value = driver;
                inputs.gross_weight.value = gross;
                inputs.dead_qty.value = deadQty;
                inputs.dead_weight.value = deadWgt;
                inputs.shrink_loss.value = shrink;
                inputs.buying_rate.value = rate;
                purchaseIdHidden.value = id;

                // 3. Recalculate based on filled data
                window.calculateTotals();

                // 4. Change UI to Edit Mode
                form.action = BASE_URL.replace('/store', '') + '/' + id; // e.g., admin/purchases/5
                formMethodInput.value = 'PUT'; // Laravel treats this as update
                saveBtnText.textContent = "Update Purchase";
                saveBtn.classList.remove('bg-[var(--yellow-primary)]', 'hover:bg-[var(--yellow-dark)]');
                saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'text-white');

                // Show Cancel Button (handle Flex properly)
                cancelEditBtn.classList.remove('hidden');
                cancelEditBtn.classList.add('flex');

                // 5. Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });

                // Highlight inputs temporarily
                inputs.gross_weight.classList.add('input-focus');
                setTimeout(() => inputs.gross_weight.classList.remove('input-focus'), 1000);
            }

            // --- 🟢 RESET FORM (Cancel Edit) ---
            window.resetFormToCreate = function () {
                form.reset();
                form.action = BASE_URL; // Reset to Store URL
                formMethodInput.value = 'POST';
                purchaseIdHidden.value = '';

                // UI Reset
                saveBtnText.textContent = "Save Purchase";
                saveBtn.classList.add('bg-[var(--yellow-primary)]', 'hover:bg-[var(--yellow-dark)]');
                saveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'text-white');

                // Hide Cancel Button
                cancelEditBtn.classList.add('hidden');
                cancelEditBtn.classList.remove('flex');

                // Reset calculations
                inputs.gross_weight.value = 0;
                inputs.dead_weight.value = 0;
                inputs.dead_qty.value = 0;
                inputs.shrink_loss.value = 0;
                inputs.buying_rate.value = 0;
                inputs.driver_no.value = '';
                window.calculateTotals();
            }

            // --- DELETE PURCHASE (SweetAlert) ---
            window.deletePurchase = async function (id) {
                const result = await Swal.fire({
                    title: 'Are you sure?',
                    text: `Delete Purchase #${id}?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                });

                if (!result.isConfirmed) return;

                const deleteUrl = BASE_URL.replace('/store', '') + '/' + id;

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
                        const rowElement = document.getElementById(`purchase-row-${id}`);
                        if (rowElement) rowElement.remove();
                        if (purchaseTableBody.children.length === 0) {
                            purchaseTableBody.innerHTML = `<tr id="no-purchases-row"><td colspan="7" class="px-6 py-10 text-center text-gray-500 text-sm italic">No recent purchases found.</td></tr>`;
                        }

                        // If we were editing this specific item, reset the form
                        if (purchaseIdHidden.value == id) {
                            resetFormToCreate();
                        }

                        Swal.fire('Deleted!', responseData.message || 'Purchase deleted.', 'success');
                    } else {
                        Swal.fire('Error!', responseData.message || 'Failed to delete.', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error!', 'Network error during deletion.', 'error');
                }
            }

            // --- SAVE PURCHASE SUBMISSION ---
            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (parseInput(outputs.net_live_weight) <= 0) {
                    Swal.fire('Invalid Weight', 'Net Live Weight must be greater than zero.', 'warning');
                    return;
                }

                window.calculateTotals(); // Ensure latest calculation
                const formData = new FormData(form);
                formData.delete('purchase_formula_data'); // Don't send formula config

                saveBtn.disabled = true;
                const originalText = saveBtnText.textContent;
                saveBtnText.textContent = 'Processing...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST', // The _method hidden field handles PUT if editing
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });

                    const responseData = await response.json();

                    if (response.ok) {
                        const isUpdate = (formMethodInput.value === 'PUT');

                        renderPurchaseRow(responseData.purchase, isUpdate);

                        Swal.fire({
                            icon: 'success',
                            title: isUpdate ? 'Updated!' : 'Saved!',
                            text: isUpdate ? 'Purchase updated successfully.' : 'Purchase entry saved successfully.',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        resetFormToCreate(); // This clears form and resets mode to Create

                    } else if (response.status === 422) {
                        let errorMsg = 'Validation Failed: ';
                        if (responseData.errors) errorMsg += Object.values(responseData.errors).flat().join(' | ');
                        Swal.fire('Validation Error', errorMsg, 'error');
                    } else {
                        Swal.fire('Error', responseData.message || 'Server error', 'error');
                    }
                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'Fatal Server Error. Check logs.', 'error');
                } finally {
                    saveBtn.disabled = false;
                    saveBtnText.textContent = originalText;
                }
            });

            // Listeners
            [inputs.gross_weight, inputs.dead_weight, inputs.shrink_loss, inputs.buying_rate, inputs.dead_qty].forEach(input => {
                if (input) {
                    input.addEventListener('input', window.calculateTotals);
                    input.addEventListener('focus', () => input.classList.add('input-focus'));
                    input.addEventListener('blur', () => input.classList.remove('input-focus'));
                }
            });

            window.calculateTotals();
        });
    </script>
@endsection