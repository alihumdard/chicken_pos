@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="flex-1 p-3 sm:p-6 lg:p-8 bg-gray-100">

        <h1 class="text-xl lg:text-2xl font-bold mb-4 lg:mb-6">Sales Point - Wholesale & Permanent</h1>
        <div id="statusMessage" class="mb-4 hidden p-3 rounded-lg text-sm font-medium" role="alert"></div>
        <hr>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            {{-- Left Column: Customer Selection --}}
            <div class="bg-white p-4 rounded-lg shadow-md overflow-hidden">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="text-lg lg:text-2xl font-bold">Customer Selection</h2>
                    <button onclick="openModal()" 
                        class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2 px-3 rounded flex items-center transition-colors shadow-sm">
                        <i class="fas fa-plus mr-1"></i> New
                    </button>
                </div>

                <div class="relative mb-4">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-gray-500"></i>
                    </span>
                    <input id="customer-search" type="text" placeholder="Search Customer..."
                        class="w-full border rounded px-10 py-2 focus:outline-none focus:ring text-sm lg:text-base">
                </div>

                <ul id="customer-list" class="space-y-2 max-h-48 lg:max-h-[600px] overflow-y-auto">
                    @forelse ($customers as $customer)
                        <li class="customer-item px-3 rounded cursor-pointer text-base lg:text-lg font-bold transition-colors"
                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                            data-balance="{{ number_format($customer->current_balance, 2, '.', '') }}">
                            {{ $customer->name }}
                            <br>
                            <span class="text-xs lg:text-sm text-gray-700 balance-text">
                                (Bal: {{ number_format($customer->current_balance, 0) }} PKR)
                            </span>
                        </li>
                        <hr>
                    @empty
                        <li id="no-customer-placeholder" class="p-3 text-gray-500 text-sm">
                            No customers found. Please add a customer.
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- Right Column: Sale Form --}}
            <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-md flex flex-col justify-between">
                <form id="sale-form" action="{{ route('admin.sales.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" id="selected-customer-id" required>
                    <input type="hidden" name="total_payable" id="final-total-payable" required>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                        <h2 class="text-xl lg:text-2xl font-bold">New Transaction</h2>
                        <p class="text-sm lg:text-base">
                            Selling to: <span class="font-bold text-blue-600" id="current-customer-name">Select Customer</span> 
                            <br class="sm:hidden">
                            | Bal: <span id="current-customer-balance" class="font-bold text-red-600">0.00 PKR</span>
                        </p>
                    </div>

                    {{-- Channel Selection --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 mb-6 p-3 border rounded-lg bg-gray-50">
                        <label class="flex items-center space-x-2 font-medium text-sm lg:text-lg cursor-pointer">
                            <input type="checkbox" id="wholesale-channel-checkbox" name="rate_channel" value="wholesale"
                                class="form-checkbox text-blue-600 h-5 w-5" checked>
                            <span>Wholesale / Permanent</span>
                        </label>

                        <label class="flex items-center space-x-2 font-medium text-sm lg:text-lg cursor-pointer">
                            <input type="checkbox" id="retail-channel-checkbox" name="rate_channel" value="retail"
                                class="form-checkbox text-green-600 h-5 w-5">
                            <span>Shop Retail Rates</span>
                        </label>
                    </div>

                    {{-- Categories --}}
                    @php
                        $allCategories = [
                            'live' => ['rate_field' => 'wholesale_rate', 'image' => asset('assets/images/live.png')],
                            
                            // ðŸŸ¢ THESE KEYS MUST MATCH YOUR DATABASE WHOLESALE KEYS EXACTLY
                            'Mix (No.35)' => ['rate_field' => 'wholesale_chest_rate', 'image' => asset('assets/images/chest.png')],
                            'Mix (No.36)' => ['rate_field' => 'wholesale_thigh_rate', 'image' => asset('assets/images/thigh.png')],
                            'Mix (No.34)' => ['rate_field' => 'wholesale_mix_rate', 'image' => asset('assets/images/mix.png')],
                            
                            // ID 6 in your DB is wholesale_customer_piece_rate
                            'Mix (No.37)' => ['rate_field' => 'wholesale_customer_piece_rate', 'image' => asset('assets/images/piece.png')],
                            
                            'Live' => ['rate_field' => 'live_chicken_rate', 'image' => asset('assets/images/live.png')],

                        ];
                        $wholesaleGroup = ['live', 'Mix (No.34)', 'Mix (No.35)', 'Mix (No.36)', 'Mix (No.37)'];
                        $retailGroup = ['Live', 'Mix (No.34)', 'Mix (No.35)', 'Mix (No.36)', 'Mix (No.37)']; 
                    @endphp

                    <div id="category-tabs-wrapper" class="mb-6">
                        {{-- Wholesale Grid --}}
                        <div id="wholesale-category-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 lg:gap-3">
                            @foreach ($wholesaleGroup as $categoryName)
                                @php 
                                    if(!isset($allCategories[$categoryName])) continue;
                                    $details = $allCategories[$categoryName]; 
                                    $displayName = ($categoryName === 'live' || $categoryName === 'Live') ? 'Live' : strtoupper($categoryName);
                                @endphp
                                <div class="category-tab bg-white border border-gray-300 rounded-lg p-2 lg:p-3 flex flex-col items-center justify-center cursor-pointer transition-all h-28 lg:h-40 hover:border-gray-500"
                                    data-category="{{ $categoryName }}"
                                    data-rate-field="{{ $details['rate_field'] }}">
                                    <div class="icon-wrapper mb-1 lg:mb-2">
                                        <img src="{{ $details['image'] }}" alt="{{ $categoryName }}" class="w-14 h-14 lg:w-20 lg:h-20 object-contain">
                                    </div>
                                    <span class="text-xs lg:text-sm font-bold text-gray-800 tracking-wide">{{ $displayName }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Retail Grid --}}
                        <div id="retail-category-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 lg:gap-3 hidden">
                            @foreach ($retailGroup as $categoryName)
                                @php 
                                    if(!isset($allCategories[$categoryName])) continue;
                                    $details = $allCategories[$categoryName]; 
                                    $displayName = ($categoryName === 'live' || $categoryName === 'Live') ? 'Live' : strtoupper($categoryName);
                                @endphp
                                <div class="category-tab bg-white border border-gray-300 rounded-lg p-2 lg:p-3 flex flex-col items-center justify-center cursor-pointer transition-all h-28 lg:h-40 hover:border-gray-500"
                                    data-category="{{ $categoryName }}"
                                    data-rate-field="{{ $details['rate_field'] }}">
                                    <div class="icon-wrapper mb-1 lg:mb-2">
                                        <img src="{{ $details['image'] }}" alt="{{ $categoryName }}" class="w-14 h-14 lg:w-20 lg:h-20 object-contain">
                                    </div>
                                    <span class="text-xs lg:text-sm font-bold text-gray-800 tracking-wide">{{ $displayName }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Inputs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="weight-input" class="text-sm font-medium">Weight (KG)</label>
                            <input id="weight-input" type="number" value="0" placeholder="0.000"
                                class="w-full border rounded px-3 py-2 mt-1 focus:ring text-lg font-semibold">
                        </div>
                        <div>
                            <label for="rate-input" class="text-sm font-medium">Rate (PKR)</label>
                            <input id="rate-input" type="number" step="0.01" min="0" value="0.00"
                                class="w-full border rounded px-3 py-2 mt-1 focus:ring text-lg font-semibold">
                            <p id="rate-source-display" class="text-xs text-gray-500 mt-1">Rate source: Not Selected</p>
                        </div>
                    </div>

                    <div class="flex justify-between text-lg font-semibold mb-6">
                        <span>Line Total:</span>
                        <span id="line-total-display" class="text-xl font-bold text-red-500">0.00 PKR</span>
                    </div>

                    <button type="button" id="add-item-btn"
                        class="w-full bg-blue-500 text-white py-3 lg:py-2 rounded hover:bg-blue-600 transition-colors mb-4 text-lg font-bold"
                        disabled>
                        + Add Item 
                    </button>

                    <hr class="my-4">

                    <div id="cart-items-container" class="space-y-2 max-h-40 overflow-y-auto mb-4 text-sm lg:text-base">
                        <div class="text-gray-500 text-center py-4">Empty</div>
                    </div>

                    <hr class="my-4">

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-lg lg:text-xl font-bold">Total Bill:</span>
                            <span id="total-payable-display" class="text-2xl lg:text-3xl font-bold text-green-600">0.00 PKR</span>
                        </div>

                        <div class="flex items-center gap-4 mt-3">
                            <div class="flex-1">
                                <label for="cash-received-input" class="block text-sm font-bold text-gray-700">Cash Received:</label>
                                <input type="number" name="cash_received" id="cash-received-input" value="0" min="0" 
                                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:ring-2 focus:ring-green-500 text-xl font-bold text-gray-800">
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Net Balance Change</p>
                                <p id="net-balance-change" class="text-lg font-bold text-red-600">0.00</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="button" id="cancel-sale-btn" class="w-full bg-gray-300 py-3 lg:py-2 rounded hover:bg-gray-400 transition-colors font-semibold">
                            Cancel
                        </button>
                        <button type="submit" id="confirm-sale-btn" class="w-full bg-green-600 text-white py-3 lg:py-2 rounded hover:bg-green-700 transition-colors font-bold shadow-md" disabled>
                            Confirm Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL FOR ADDING CUSTOMER (Same as before) --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-md border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Customer</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" name="type" value="customer"> 
                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none" placeholder="e.g. Hotel Bismillah">
                            <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" id="openingBalance" value="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" id="saveContactBtn" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700">Save Customer</button>
                        <button type="button" onclick="closeModal()" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

   <script>
    const STORE_CONTACT_URL = "{{ route('admin.contacts.store') }}"; 
    
    function openModal() {
        const modal = document.getElementById('contactModal');
        document.getElementById('contactForm').reset();
        document.getElementById('nameError').classList.add('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => document.getElementById('contactName').focus(), 100);
    }
    function closeModal() { document.getElementById('contactModal').classList.add('hidden'); }

    document.addEventListener('DOMContentLoaded', function () {
        
        // Search Logic
        document.getElementById('customer-search').addEventListener('input', function() {
            let filter = this.value.toLowerCase().trim();
            document.querySelectorAll('.customer-item').forEach(function(item) {
                let text = item.textContent.toLowerCase();
                let hr = item.nextElementSibling; 
                if (text.includes(filter)) {
                    item.classList.remove('hidden');
                    if(hr && hr.tagName === 'HR') hr.classList.remove('hidden'); 
                } else {
                    item.classList.add('hidden');
                    if(hr && hr.tagName === 'HR') hr.classList.add('hidden'); 
                }
            });
        });

        let ACTIVE_RATES = @json($rates);
        let selectedCustomerId = null;
        let selectedCategory = null;
        let selectedRateField = null;
        let selectedChannel = 'wholesale'; 
        let cartItems = [];

        // DOM
        const customerIdInput = document.getElementById('selected-customer-id');
        const customerNameDisplay = document.getElementById('current-customer-name');
        const customerBalanceDisplay = document.getElementById('current-customer-balance');
        const categoryTabs = document.querySelectorAll('.category-tab'); 
        const wholesaleGrid = document.getElementById('wholesale-category-grid');
        const retailGrid = document.getElementById('retail-category-grid');
        const weightInput = document.getElementById('weight-input');
        const rateInput = document.getElementById('rate-input');
        const addItemBtn = document.getElementById('add-item-btn');
        const cartContainer = document.getElementById('cart-items-container');
        const totalPayableDisplay = document.getElementById('total-payable-display');
        const finalTotalPayableInput = document.getElementById('final-total-payable');
        const confirmSaleBtn = document.getElementById('confirm-sale-btn');
        const saleForm = document.getElementById('sale-form');
        const wholesaleCheckbox = document.getElementById('wholesale-channel-checkbox');
        const retailCheckbox = document.getElementById('retail-channel-checkbox');
        
        const cashReceivedInput = document.getElementById('cash-received-input');
        const netBalanceChangeDisplay = document.getElementById('net-balance-change');

        const formatCurrency = (value) => parseFloat(value).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' PKR';
        const formatCurrencyNoDecimal = (value) => parseFloat(value).toLocaleString('en-PK', { maximumFractionDigits: 0 });

        const calculateLineTotal = () => {
            const weight = parseFloat(weightInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            document.getElementById('line-total-display').textContent = formatCurrency(weight * rate);
            updateAddItemButton();
        };

        // ðŸŸ¢ 1. FIXED MAPPING (Matches your DB IDs)
        const retailRateMap = {
            'wholesale_mix_rate':            'retail_mix_rate',
            'wholesale_chest_rate':          'retail_chest_rate',
            'wholesale_thigh_rate':          'retail_thigh_rate',      
            'wholesale_customer_piece_rate': 'retail_piece_rate', 
            'wholesale_rate':                'retail_mix_rate',      
        };

        // ðŸŸ¢ 2. FIXED LOOKUP LOGIC
        const getRateByChannel = (channel, wholesaleKey) => {
            console.log(`\n--- ðŸ”„ LOOKUP REQUEST: ${channel} | ${wholesaleKey} ---`);

            let rate = 0.00;
            if (!ACTIVE_RATES || (!ACTIVE_RATES.wholesale && !ACTIVE_RATES.retail)) { 
                console.error("âŒ Critical: ACTIVE_RATES is empty.");
                return 0.00; 
            }

            if (channel === 'wholesale') {
                if (ACTIVE_RATES.wholesale[wholesaleKey] !== undefined) {
                    rate = ACTIVE_RATES.wholesale[wholesaleKey];
                    console.log("âœ… Found Wholesale Direct:", rate);
                } else {
                    console.warn("âšï¸ Wholesale Key NOT found:", wholesaleKey);
                }
            } 
            else if (channel === 'retail') {
                // Special case fallback
                if (wholesaleKey === 'live_chicken_rate' || wholesaleKey === 'wholesale_rate') {
                     rate = ACTIVE_RATES.wholesale.live_chicken_rate ?? 0;
                     console.log("â„¹ï¸ Using Live Fallback:", rate);
                } else {

                    // Check the Map
                    const retailKey = retailRateMap[wholesaleKey];
                    console.log(`ðŸ—ºï¸ Mapped '${wholesaleKey}' to '${retailKey}'`);
                    
                    if (!retailKey) {
                        console.error("âŒ ERROR: No mapping found in retailRateMap for this key!");
                    } else if (ACTIVE_RATES.retail[retailKey] !== undefined) {
                        rate = ACTIVE_RATES.retail[retailKey];
                        console.log("âœ… Found Retail Rate:", rate);
                    } else {
                        console.error(`âŒ ERROR: Key '${retailKey}' exists in Map, but is MISSING from ACTIVE_RATES.retail data!`);
                        console.log("Available Retail Keys:", Object.keys(ACTIVE_RATES.retail));
                    }
                }
            }
            return parseFloat(rate) || 0.00;
        };

        const updateRateInput = () => {
            const channel = wholesaleCheckbox.checked ? 'wholesale' : 'retail';
            if (!selectedCategory || !selectedRateField) {
                rateInput.value = (0.00).toFixed(2);
                document.getElementById('rate-source-display').textContent = 'Rate source: Not Selected';
                return;
            }
            const rate = getRateByChannel(channel, selectedRateField);
            rateInput.value = rate.toFixed(2);
            document.getElementById('rate-source-display').textContent = `Rate source: ${channel.toUpperCase()}`;
            calculateLineTotal();
        };

        const updateAddItemButton = () => {
            const weight = parseFloat(weightInput.value);
            const rate = parseFloat(rateInput.value);
            addItemBtn.disabled = !(selectedCustomerId && selectedCategory && weight > 0 && rate > 0);
        };

        const updateCartDisplay = () => {
            let grandTotal = 0;
            cartContainer.innerHTML = '';
            
            if (cartItems.length === 0) {
                cartContainer.innerHTML = '<div class="text-gray-500 text-center py-4">Empty</div>';
                confirmSaleBtn.disabled = true;
            } else {
                cartItems.forEach((item, index) => {
                    const lineTotal = item.weight * item.rate;
                    grandTotal += lineTotal;
                    
                    const channelBadgeClass = item.channel === 'Wholesale' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800';
                    const channelLabel = `<span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded ${channelBadgeClass}">${item.channel}</span>`;

                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex justify-between text-gray-700 py-2 border-b items-center';
                    itemDiv.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <button type="button" data-index="${index}" class="remove-item-btn text-red-500 hover:text-red-700 text-sm p-1"><i class="fa-solid fa-times-circle text-lg"></i></button>
                            
                            <input type="hidden" name="cart_items[${index}][category]" value="${item.category}">
                            <input type="hidden" name="cart_items[${index}][weight]" value="${item.weight.toFixed(3)}">
                            <input type="hidden" name="cart_items[${index}][rate]" value="${item.rate.toFixed(2)}">
                            
                            <div class="flex flex-col leading-tight">
                                <span class="font-bold text-gray-800 text-sm">
                                    ${item.category} ${channelLabel}
                                </span>
                                <span class="text-xs text-gray-500">
                                    ${item.weight.toFixed(3)}kg @ ${item.rate.toFixed(2)}
                                </span>
                            </div>
                        </div>
                        <span class="font-bold text-gray-800">${formatCurrency(lineTotal)}</span>
                    `;
                    cartContainer.appendChild(itemDiv);
                });
                confirmSaleBtn.disabled = !selectedCustomerId;
            }
            totalPayableDisplay.textContent = formatCurrency(grandTotal);
            finalTotalPayableInput.value = grandTotal.toFixed(2);
            calculateNetBalanceChange();
        };

        const calculateNetBalanceChange = () => {
            const total = parseFloat(finalTotalPayableInput.value) || 0;
            const received = parseFloat(cashReceivedInput.value) || 0;
            const netChange = total - received;

            const displayEl = netBalanceChangeDisplay;
            if(netChange > 0) {
                displayEl.textContent = "+" + formatCurrency(netChange) + " (Added to Debt)";
                displayEl.className = "text-lg font-bold text-red-600";
            } else if (netChange < 0) {
                displayEl.textContent = formatCurrency(netChange) + " (Overpaid)";
                displayEl.className = "text-lg font-bold text-green-600";
            } else {
                displayEl.textContent = "0.00 (Fully Paid)";
                displayEl.className = "text-lg font-bold text-gray-600";
            }
        };

        cashReceivedInput.addEventListener('input', calculateNetBalanceChange);

        const handleChannelChange = (e) => {
            const checked = e.target.checked;
            if (checked) {
                if (e.target.id === 'wholesale-channel-checkbox') { retailCheckbox.checked = false; selectedChannel = 'wholesale'; }
                else { wholesaleCheckbox.checked = false; selectedChannel = 'retail'; }
            } else {
                if (e.target.id === 'wholesale-channel-checkbox') { retailCheckbox.checked = true; selectedChannel = 'retail'; }
                else { wholesaleCheckbox.checked = true; selectedChannel = 'wholesale'; }
            }
            if(selectedChannel === 'wholesale') { wholesaleGrid.classList.remove('hidden'); retailGrid.classList.add('hidden'); }
            else { wholesaleGrid.classList.add('hidden'); retailGrid.classList.remove('hidden'); }
            if (selectedCategory) updateRateInput();
        };

        wholesaleCheckbox.addEventListener('change', handleChannelChange);
        retailCheckbox.addEventListener('change', handleChannelChange);

        document.querySelectorAll('.customer-item').forEach(item => {
            item.addEventListener('click', function () {
                document.querySelectorAll('.customer-item').forEach(i => i.classList.remove('bg-yellow-200'));
                this.classList.add('bg-yellow-200');
                selectedCustomerId = this.dataset.id;
                customerIdInput.value = selectedCustomerId;
                customerNameDisplay.textContent = this.dataset.name;
                customerBalanceDisplay.textContent = formatCurrency(this.dataset.balance);
                updateAddItemButton();
                if (cartItems.length > 0) confirmSaleBtn.disabled = false;
            });
        });

        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                categoryTabs.forEach(t => { t.classList.remove('bg-yellow-400', 'border-black', 'shadow-md'); t.classList.add('bg-white', 'border-gray-300'); });
                this.classList.remove('bg-white', 'border-gray-300'); this.classList.add('bg-yellow-400', 'border-black', 'shadow-md');
                selectedCategory = this.dataset.category;
                selectedRateField = this.dataset.rateField;
                updateRateInput();
                updateAddItemButton();
            });
        });

        weightInput.addEventListener('input', calculateLineTotal);
        rateInput.addEventListener('input', calculateLineTotal);

        addItemBtn.addEventListener('click', function () {
            if (selectedCustomerId && selectedCategory && parseFloat(weightInput.value) > 0 && parseFloat(rateInput.value) > 0) {
                const channelLabel = selectedChannel.charAt(0).toUpperCase() + selectedChannel.slice(1);
                cartItems.unshift({ 
                    category: selectedCategory, 
                    channel: channelLabel, 
                    weight: parseFloat(weightInput.value), 
                    rate: parseFloat(rateInput.value) 
                });

                weightInput.value = 0;
                calculateLineTotal();
                updateCartDisplay();
                weightInput.focus();
            } else {
                Swal.fire({ icon: 'warning', title: 'Missing Information', text: 'Please select customer, category, and valid weight/rate.' });
            }
        });

        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('saveContactBtn');
            btn.disabled = true; btn.textContent = 'Saving...';
            try {
                const res = await fetch(STORE_CONTACT_URL, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(e.target) });
                if (!res.ok) throw new Error('Failed');
                const data = await res.json(); 
                closeModal();
                Swal.fire({ icon: 'success', title: 'Saved!', showConfirmButton: false, timer: 1000 });

                const customerList = document.getElementById('customer-list');
                const noCustomerMsg = document.getElementById('no-customer-placeholder');
                if(noCustomerMsg) noCustomerMsg.remove();

                const balanceVal = parseFloat(data.opening_balance || 0);
                const formattedBal = balanceVal.toFixed(2);
                const displayBal = Math.floor(balanceVal).toLocaleString();

                const li = document.createElement('li');
                li.className = 'customer-item px-3 rounded cursor-pointer text-base lg:text-lg font-bold transition-colors';
                li.setAttribute('data-id', data.id);
                li.setAttribute('data-name', data.name);
                li.setAttribute('data-balance', formattedBal);
                li.innerHTML = `${data.name} <br><span class="text-xs lg:text-sm text-gray-700 balance-text">(Bal: ${displayBal} PKR)</span>`;

                li.addEventListener('click', function () {
                    document.querySelectorAll('.customer-item').forEach(i => i.classList.remove('bg-yellow-200'));
                    this.classList.add('bg-yellow-200');
                    selectedCustomerId = this.dataset.id;
                    customerIdInput.value = selectedCustomerId;
                    customerNameDisplay.textContent = this.dataset.name;
                    customerBalanceDisplay.textContent = formatCurrency(this.dataset.balance);
                    updateAddItemButton();
                    if (cartItems.length > 0) confirmSaleBtn.disabled = false;
                });

                const hr = document.createElement('hr');
                customerList.insertBefore(hr, customerList.firstChild);
                customerList.insertBefore(li, customerList.firstChild);
                li.click(); 
                li.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Failed to save', 'error');
            } finally {
                btn.disabled = false; btn.textContent = 'Save Customer';
            }
        });

        saleForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            confirmSaleBtn.disabled = true; confirmSaleBtn.textContent = 'Processing...';
            try {
                const res = await fetch(saleForm.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' }, body: new FormData(saleForm) });
                const data = await res.json();
                if (res.ok && data.sale_id) {
                    Swal.fire({ icon: 'success', title: 'Sale Completed', text: data.message, timer: 2000, showConfirmButton: false });
                    customerBalanceDisplay.textContent = formatCurrency(data.updated_balance);
                    const listItem = document.querySelector(`.customer-item[data-id="${data.customer_id}"]`);
                    if (listItem) {
                        listItem.dataset.balance = data.updated_balance;
                        listItem.querySelector('.balance-text').textContent = `(Bal: ${formatCurrencyNoDecimal(data.updated_balance)} PKR)`;
                    }
                    cartItems = []; updateCartDisplay(); weightInput.value = 0; cashReceivedInput.value = 0; calculateNetBalanceChange();
                } else {
                    Swal.fire('Error', data.message || 'Transaction Failed', 'error');
                }
            } catch (err) {
                Swal.fire('Error', 'Network Error', 'error');
            } finally {
                confirmSaleBtn.disabled = false; confirmSaleBtn.textContent = 'Confirm Sale';
            }
        });
        
        const init = () => {
            if(selectedChannel === 'wholesale') { wholesaleGrid.classList.remove('hidden'); retailGrid.classList.add('hidden'); }
            const liveTab = document.querySelector('#wholesale-category-grid .category-tab[data-category="live"]') || document.querySelector('#wholesale-category-grid .category-tab');
            if (liveTab) liveTab.click();
        };
        init();
        
        cartContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-item-btn')) {
                cartItems.splice(e.target.closest('.remove-item-btn').dataset.index, 1);
                updateCartDisplay();
            }
        });

        document.getElementById('cancel-sale-btn').addEventListener('click', () => window.location.reload());
    });
    </script>
@endsection