@extends('layouts.main')

@section('content')
    {{-- 🟢 SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Reduced padding on mobile, original padding on large screens --}}
    <div class="flex-1 p-3 sm:p-6 lg:p-8 bg-gray-100">

        <h1 class="text-xl lg:text-2xl font-bold mb-4 lg:mb-6">Sales Point - Wholesale & Permanent</h1>
        <div id="statusMessage" class="mb-4 hidden p-3 rounded-lg text-sm font-medium" role="alert"></div>
        <hr>
        
        {{-- 
            RESPONSIVE GRID: 
            - Mobile/Tablet: 1 Column (Stacked)
            - Large Screens (lg): 3 Columns (Side by Side - Original Design)
        --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            {{-- Left Column: Customer Selection --}}
            {{-- On mobile, this sits on top. On desktop, it's the left sidebar. --}}
            <div class="bg-white p-4 rounded-lg shadow-md overflow-hidden">
                {{-- Header with Add Button --}}
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

                {{-- 
                    RESPONSIVE LIST HEIGHT:
                    - Mobile: max-h-48 (Short, so user can scroll down to form)
                    - Desktop: max-h-[600px] (Tall, original design)
                --}}
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
            {{-- Spans 2 columns on Large screens, 1 on mobile --}}
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

                    {{-- Channel Selection: Stack on very small screens, row on others --}}
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
                            'Chest' => ['rate_field' => 'wholesale_hotel_chest_rate', 'image' => asset('assets/images/chest.png')],
                            'Thigh' => ['rate_field' => 'wholesale_hotel_thigh_rate', 'image' => asset('assets/images/thigh.png')],
                            'Mix' => ['rate_field' => 'wholesale_hotel_mix_rate', 'image' => asset('assets/images/mix.png')],
                            'Piece' => ['rate_field' => 'wholesale_customer_piece_rate', 'image' => asset('assets/images/piece.png')],
                            'Live' => ['rate_field' => 'live_chicken_rate', 'image' => asset('assets/images/live.png')],
                            'live' => ['rate_field' => 'wholesale_rate', 'image' => asset('assets/images/live.png')],
                        ];
                        $wholesaleGroup = ['live', 'Chest', 'Thigh', 'Mix', 'Piece'];
                        $retailGroup = ['Live', 'Mix', 'Chest', 'Thigh', 'Piece']; 
                    @endphp

                    <div id="category-tabs-wrapper" class="mb-6">

                        {{-- 1. WHOLESALE CATEGORIES --}}
                        <div id="wholesale-category-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2 lg:gap-3">
                            @foreach ($wholesaleGroup as $categoryName)
                                @php 
                                    if(!isset($allCategories[$categoryName])) continue;
                                    $details = $allCategories[$categoryName]; 
                                    $displayName = ($categoryName === 'live' || $categoryName === 'Live') ? 'Live' : strtoupper($categoryName);
                                @endphp

                                {{-- 
                                    RESPONSIVE TAB SIZE:
                                    - Mobile: h-28 (Compact)
                                    - Desktop (lg): h-40 (Original Big Size)
                                --}}
                                <div class="category-tab bg-white border border-gray-300 rounded-lg p-2 lg:p-3 flex flex-col items-center justify-center cursor-pointer transition-all h-28 lg:h-40 hover:border-gray-500"
                                    data-category="{{ $categoryName }}"
                                    data-rate-field="{{ $details['rate_field'] }}">
                                    
                                    {{-- Image Size: w-14 on mobile, w-20 on desktop --}}
                                    <div class="icon-wrapper mb-1 lg:mb-2">
                                        <img src="{{ $details['image'] }}" 
                                             alt="{{ $categoryName }}" 
                                             class="w-14 h-14 lg:w-20 lg:h-20 object-contain">
                                    </div>

                                    <span class="text-xs lg:text-sm font-bold text-gray-800 tracking-wide">
                                        {{ $displayName }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- 2. RETAIL CATEGORIES --}}
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
                                        <img src="{{ $details['image'] }}" 
                                             alt="{{ $categoryName }}" 
                                             class="w-14 h-14 lg:w-20 lg:h-20 object-contain">
                                    </div>

                                    <span class="text-xs lg:text-sm font-bold text-gray-800 tracking-wide">
                                        {{ $displayName }}
                                    </span>
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

                    <div class="flex justify-between items-center mt-4">
                        <span class="text-lg lg:text-xl font-bold">Total Payable:</span>
                        {{-- Responsive Text for Total --}}
                        <span id="total-payable-display" class="text-2xl lg:text-3xl font-bold text-green-600">0.00 PKR</span>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="button" id="cancel-sale-btn"
                            class="w-full bg-gray-300 py-3 lg:py-2 rounded hover:bg-gray-400 transition-colors font-semibold">
                            Cancel
                        </button>

                        <button type="submit" id="confirm-sale-btn"
                            class="w-full bg-green-600 text-white py-3 lg:py-2 rounded hover:bg-green-700 transition-colors font-bold shadow-md"
                            disabled>
                            Confirm Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 🟢 MODAL FOR ADDING CUSTOMER (Hidden by default) --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
    
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-md border border-gray-100">
                
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Customer</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
    
                <form id="contactForm">
                    @csrf
                    {{-- Force Type to Customer --}}
                    <input type="hidden" name="type" value="customer"> 
                    
                    <div class="px-6 py-6 space-y-5">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required 
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300"
                                   placeholder="e.g. Hotel Bismillah">
                            <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" id="openingBalance" value="0"
                                   class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300">
                            <p class="text-xs text-gray-400 mt-1">Positive = They owe us. Negative = We owe them.</p>
                        </div>
    
                    </div>
    
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" id="saveContactBtn" class="inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 transition-colors w-full sm:w-auto">
                            Save Customer
                        </button>
                        <button type="button" onclick="closeModal()" class="mt-3 sm:mt-0 inline-flex justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors w-full sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </form>
    
            </div>
        </div>
    </div>


    <script>
    // 🟢 ROUTE FOR ADDING CONTACT
    const STORE_CONTACT_URL = "{{ route('admin.contacts.store') }}"; 

    // 🟢 MODAL JS LOGIC
    function openModal() {
        const modal = document.getElementById('contactModal');
        const form = document.getElementById('contactForm');
        form.reset();
        document.getElementById('nameError').classList.add('hidden');
        modal.classList.remove('hidden');
        // Focus on name input
        setTimeout(() => document.getElementById('contactName').focus(), 100);
    }

    function closeModal() {
        document.getElementById('contactModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        // 🔍 CUSTOMER SEARCH LOGIC
        document.getElementById('customer-search').addEventListener('input', function() {
            let filter = this.value.toLowerCase().trim();
            let items = document.querySelectorAll('.customer-item');

            items.forEach(function(item) {
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

        // 🟢 RATES DATA LOADED FROM CONTROLLER
        let ACTIVE_RATES = @json($rates);

        // State variables
        let selectedCustomerId = null;
        let selectedCustomerName = null;
        let selectedCategory = null;
        let selectedRateField = null;
        let selectedChannel = 'wholesale'; 
        let cartItems = [];

        // DOM Elements
        const customerIdInput = document.getElementById('selected-customer-id');
        const customerNameDisplay = document.getElementById('current-customer-name');
        const customerBalanceDisplay = document.getElementById('current-customer-balance');
        const categoryTabs = document.querySelectorAll('.category-tab'); 
        const wholesaleGrid = document.getElementById('wholesale-category-grid');
        const retailGrid = document.getElementById('retail-category-grid');
        const weightInput = document.getElementById('weight-input');
        const rateInput = document.getElementById('rate-input');
        const lineTotalDisplay = document.getElementById('line-total-display');
        const addItemBtn = document.getElementById('add-item-btn');
        const cartContainer = document.getElementById('cart-items-container');
        const totalPayableDisplay = document.getElementById('total-payable-display');
        const finalTotalPayableInput = document.getElementById('final-total-payable');
        const confirmSaleBtn = document.getElementById('confirm-sale-btn');
        const saleForm = document.getElementById('sale-form');
        const rateSourceDisplay = document.getElementById('rate-source-display');
        const customerList = document.getElementById('customer-list'); 

        // Checkbox Elements
        const wholesaleCheckbox = document.getElementById('wholesale-channel-checkbox');
        const retailCheckbox = document.getElementById('retail-channel-checkbox');

        const FETCH_RATES_ROUTE = "{{ route('admin.sales.fetch-rates') }}";
        const retailRateMap = {
            'wholesale_hotel_mix_rate': 'retail_mix_rate',
            'wholesale_hotel_chest_rate': 'retail_chest_rate',
            'wholesale_hotel_thigh_rate': 'retail_thigh_rate',
            'wholesale_customer_piece_rate': 'retail_piece_rate',
            'wholesale_rate': 'retail_mix_rate',
        };

        // --- Utility Functions ---

        const formatCurrency = (value) => {
            return parseFloat(value).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' PKR';
        };

        const formatCurrencyNoDecimal = (value) => {
            return parseFloat(value).toLocaleString('en-PK', { maximumFractionDigits: 0 });
        };


        const calculateLineTotal = () => {
            const weight = parseFloat(weightInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const total = weight * rate;
            lineTotalDisplay.textContent = formatCurrency(total);
            updateAddItemButton();
        };

        const getRateByChannel = (channel, wholesaleKey) => {
            let rate = 0.00;
            let displayKey = '';

            if (channel === 'wholesale') {
                if (ACTIVE_RATES.wholesale.hasOwnProperty(wholesaleKey)) {
                    rate = ACTIVE_RATES.wholesale[wholesaleKey];
                    displayKey = wholesaleKey;
                }
            } else if (channel === 'retail') {
                if (wholesaleKey === 'live_chicken_rate') {
                    if (ACTIVE_RATES.wholesale.hasOwnProperty('live_chicken_rate')) {
                        rate = ACTIVE_RATES.wholesale.live_chicken_rate;
                        displayKey = wholesaleKey + ' (Retail)';
                    }
                } else {
                    const retailKey = retailRateMap[wholesaleKey];
                    if (retailKey && ACTIVE_RATES.retail.hasOwnProperty(retailKey)) {
                        rate = ACTIVE_RATES.retail[retailKey];
                        displayKey = retailKey;
                    }
                }
            }
            rate = parseFloat(rate) || 0.00;
            return { rate: rate, displayKey: displayKey.replace(/_/g, ' ') };
        };

        const updateRateInput = () => {
            const currentSelectedRateField = selectedRateField;
            const currentSelectedChannel = wholesaleCheckbox.checked ? 'wholesale' : 'retail';

            if (!selectedCategory || !currentSelectedRateField) {
                rateInput.value = (0.00).toFixed(2);
                rateSourceDisplay.textContent = 'Rate source: No Category Selected';
                return;
            }
            const rateData = getRateByChannel(currentSelectedChannel, currentSelectedRateField);
            rateInput.value = rateData.rate.toFixed(2);
            rateSourceDisplay.textContent = `Rate source: ${currentSelectedChannel.toUpperCase()} - ${rateData.displayKey}`;
            calculateLineTotal();
        };

        const updateAddItemButton = () => {
            const weight = parseFloat(weightInput.value);
            const rate = parseFloat(rateInput.value);
            if (selectedCustomerId && selectedCategory && weight > 0 && rate > 0) {
                addItemBtn.disabled = false;
            } else {
                addItemBtn.disabled = true;
            }
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
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex justify-between text-gray-700 py-1 border-b';
                    itemDiv.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <button type="button" data-index="${index}" class="remove-item-btn text-red-500 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-times-circle"></i>
                            </button>
                            <input type="hidden" name="cart_items[${index}][category]" value="${item.category}">
                            <input type="hidden" name="cart_items[${index}][weight]" value="${item.weight.toFixed(3)}">
                            <input type="hidden" name="cart_items[${index}][rate]" value="${item.rate.toFixed(2)}">
                            <span>${index + 1}. ${item.category} - ${item.weight.toFixed(3)}kg @ ${item.rate.toFixed(2)}</span>
                        </div>
                        <span>${formatCurrency(lineTotal)}</span>
                    `;
                    cartContainer.appendChild(itemDiv);
                });
                confirmSaleBtn.disabled = !selectedCustomerId;
            }
            totalPayableDisplay.textContent = formatCurrency(grandTotal);
            finalTotalPayableInput.value = grandTotal.toFixed(2);
        };

        const fetchLatestRates = async () => {
            try {
                const response = await fetch(FETCH_RATES_ROUTE, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data.success) {
                        ACTIVE_RATES = data.rates;
                        updateRateInput();
                        return true;
                    }
                }
                return false;
            } catch (error) {
                console.error("Failed to fetch latest rates:", error);
                return false;
            }
        };

        // --- Event Handlers ---

        // 1. Channel Selection
        const toggleCategoryGrids = (channel) => {
            if (channel === 'wholesale') {
                wholesaleGrid.classList.remove('hidden');
                retailGrid.classList.add('hidden');
            } else {
                wholesaleGrid.classList.add('hidden');
                retailGrid.classList.remove('hidden');
            }
        };

        const handleChannelChange = (event) => {
            const clickedCheckbox = event.target;
            if (clickedCheckbox.checked) {
                if (clickedCheckbox.id === 'wholesale-channel-checkbox') {
                    retailCheckbox.checked = false;
                    selectedChannel = 'wholesale';
                } else if (clickedCheckbox.id === 'retail-channel-checkbox') {
                    wholesaleCheckbox.checked = false;
                    selectedChannel = 'retail';
                }
            } else {
                if (clickedCheckbox.id === 'wholesale-channel-checkbox') {
                    retailCheckbox.checked = true;
                    selectedChannel = 'retail';
                } else if (clickedCheckbox.id === 'retail-channel-checkbox') {
                    wholesaleCheckbox.checked = true;
                    selectedChannel = 'wholesale';
                }
            }
            toggleCategoryGrids(selectedChannel);
            if (selectedCategory) updateRateInput();
        };

        wholesaleCheckbox.addEventListener('change', handleChannelChange);
        retailCheckbox.addEventListener('change', handleChannelChange);

        // 🟢 CUSTOMER SELECTION LOGIC 
        function attachCustomerListeners() {
            const items = document.querySelectorAll('.customer-item');
            items.forEach(item => {
                const newItem = item.cloneNode(true);
                item.parentNode.replaceChild(newItem, item);
                
                newItem.addEventListener('click', function () {
                    document.querySelectorAll('.customer-item').forEach(i => i.classList.remove('bg-yellow-200'));
                    this.classList.add('bg-yellow-200');

                    selectedCustomerId = this.dataset.id;
                    selectedCustomerName = this.dataset.name;
                    const balance = parseFloat(this.dataset.balance);

                    customerIdInput.value = selectedCustomerId;
                    customerNameDisplay.textContent = selectedCustomerName;
                    customerBalanceDisplay.textContent = formatCurrency(balance);

                    updateAddItemButton();
                    if (cartItems.length > 0) confirmSaleBtn.disabled = false;
                });
            });
        }
        attachCustomerListeners();


        // 3. Category Selection
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function () {
                categoryTabs.forEach(t => {
                    t.classList.remove('bg-yellow-400', 'border-black', 'shadow-md');
                    t.classList.add('bg-white', 'border-gray-300');
                });
                this.classList.remove('bg-white', 'border-gray-300');
                this.classList.add('bg-yellow-400', 'border-black', 'shadow-md');
                selectedCategory = this.dataset.category;
                selectedRateField = this.dataset.rateField;
                updateRateInput();
                updateAddItemButton();
            });
        });

        // 4. Input Changes
        weightInput.addEventListener('input', calculateLineTotal);
        rateInput.addEventListener('input', calculateLineTotal);

        // 5. Add Item to Cart
        addItemBtn.addEventListener('click', function () {
            if (selectedCustomerId && selectedCategory && parseFloat(weightInput.value) > 0 && parseFloat(rateInput.value) > 0) {
                const item = {
                    category: selectedCategory,
                    weight: parseFloat(weightInput.value),
                    rate: parseFloat(rateInput.value),
                };
                cartItems.push(item);
                weightInput.value = 0;
                calculateLineTotal();
                updateRateInput();
                updateCartDisplay();
                weightInput.focus();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please select a customer, category, and enter valid weight/rate.',
                    confirmButtonColor: '#3085d6',
                });
            }
        });

        // 🟢 SUBMIT NEW CUSTOMER (AJAX + PAGE RELOAD)
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorData.message || 'Error occurred',
                        });
                    }
                    return;
                }

                // Close Modal
                closeModal();

                // 🟢 SHOW SUCCESS AND THEN RELOAD PAGE
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Customer added successfully! Reloading...',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    // This forces the page to reload
                    window.location.reload();
                });

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Something went wrong. Please check your connection.',
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Customer';
            }
        });


        // 🟢 Handle Confirm Sale Submission
        saleForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!selectedCustomerId || cartItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Cart',
                    text: 'Please select a customer and add items to the cart.',
                });
                return;
            }
            confirmSaleBtn.disabled = true;
            confirmSaleBtn.textContent = 'Processing...';

            const formData = new FormData(saleForm);

            try {
                const response = await fetch(saleForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.sale_id) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sale Completed!',
                        text: data.message + ` (Sale ID: ${data.sale_id})`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    customerBalanceDisplay.textContent = formatCurrency(data.updated_balance);

                    // Update list item balance
                    const customerListItem = document.querySelector(`.customer-item[data-id="${data.customer_id}"]`);
                    if (customerListItem) {
                        customerListItem.dataset.balance = data.updated_balance;
                        const balanceSpan = customerListItem.querySelector('.balance-text');
                        if (balanceSpan) {
                            balanceSpan.textContent = `(Bal: ${formatCurrencyNoDecimal(data.updated_balance)} PKR)`;
                        }
                        customerListItem.classList.remove('bg-yellow-200');
                        customerListItem.classList.add('bg-green-200');
                        setTimeout(() => {
                            customerListItem.classList.remove('bg-green-200');
                            customerListItem.classList.add('bg-yellow-200');
                        }, 1500);
                    }

                    cartItems = [];
                    updateCartDisplay();
                    weightInput.value = 0;
                    calculateLineTotal();
                } else {
                    let errorMessage = data.message || 'Error occurred.';
                    if (data.errors) errorMessage += "\n" + Object.values(data.errors).flat().join('\n');
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaction Failed',
                        text: errorMessage,
                    });
                }
            } catch (error) {
                console.error('Sale failed:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'Could not complete the sale. Check console for details.',
                });
            } finally {
                confirmSaleBtn.disabled = false;
                confirmSaleBtn.textContent = 'Confirm Sale (Credit)';
            }
        });

        // Final Setup
        const initialSetup = () => {
            toggleCategoryGrids(selectedChannel);
            let liveTab = document.querySelector('#wholesale-category-grid .category-tab[data-category="live"]');
            if(!liveTab) liveTab = document.querySelector('#wholesale-category-grid .category-tab[data-category="Live"]');
            if (liveTab) liveTab.click();
            else {
                const firstTab = document.querySelector('#wholesale-category-grid .category-tab');
                if (firstTab) firstTab.click();
            }
        }
        initialSetup();
        updateCartDisplay();

        cartContainer.addEventListener('click', function (e) {
            if (e.target.closest('.remove-item-btn')) {
                const index = e.target.closest('.remove-item-btn').dataset.index;
                cartItems.splice(index, 1);
                updateCartDisplay();
            }
        });

        document.getElementById('cancel-sale-btn').addEventListener('click', function () {
            cartItems = [];
            updateCartDisplay();
            window.location.reload();
        });

        window.addEventListener('focus', function () {
            const rateUpdateFlag = localStorage.getItem('rates_updated');
            if (rateUpdateFlag === 'true') {
                fetchLatestRates().then(success => {
                    if (success) localStorage.removeItem('rates_updated');
                });
            }
        });
    });
</script>
@endsection