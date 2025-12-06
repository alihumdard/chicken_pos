@extends('layouts.main')

@section('content')
    <div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100 ">

        <h1 class="text-2xl font-bold mb-6">Sales Point - Wholesale & Permanent</h1>
        <hr>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Left Column: Customer Selection --}}
            <div class="bg-white p-4 rounded-lg shadow-md overflow-y-auto">
                <h2 class="text-2xl font-bold mb-3">Customer Selection</h2>

                <div class="relative mb-4">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-gray-500"></i>
                    </span>

                    <input id="customer-search" type="text" placeholder="Search Customer..."
                        class="w-full border rounded px-10 py-2 focus:outline-none focus:ring">
                </div>


                <ul id="customer-list" class="space-y-2">
                    @forelse ($customers as $customer)
                        <li class="customer-item px-3 hover:bg-gray-100 rounded cursor-pointer text-lg font-bold transition-colors"
                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}"
                            data-balance="{{ number_format($customer->current_balance, 2, '.', '') }}">
                            {{ $customer->name }}
                            <br>
                            <span class="text-sm text-gray-700">
                                (Bal: {{ number_format($customer->current_balance, 0) }} PKR)
                            </span>
                        </li>
                        <hr>
                    @empty
                        <li class="p-3 text-gray-500">
                            No customers found. Please add a customer to begin a sale.
                        </li>
                    @endforelse
                </ul>
            </div>

            {{-- Right Column: Sale Form --}}
            <div class="md:col-span-2 bg-white p-4 rounded-lg shadow-md flex flex-col justify-between">
                <form id="sale-form" action="{{ route('admin.sales.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="customer_id" id="selected-customer-id" required>
                    <input type="hidden" name="total_payable" id="final-total-payable" required>

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">New Transaction</h2>
                        <p>
                            Selling to: <span class="font-bold text-blue-600" id="current-customer-name">Please Select
                                Customer</span> | Current Bal:
                            <span id="current-customer-balance" class="font-bold text-red-600">0.00 PKR</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-6 mb-6 p-3 border rounded-lg bg-gray-50">
                        <label class="flex items-center space-x-2 font-medium text-lg cursor-pointer">
                            {{-- Set Wholesale as default checked --}}
                            <input type="checkbox" id="wholesale-channel-checkbox" name="rate_channel" value="wholesale"
                                class="form-checkbox text-blue-600 h-5 w-5" checked>
                            <span>Wholesale / Permanent Rates</span>
                        </label>

                        <label class="flex items-center space-x-2 font-medium text-lg cursor-pointer">
                            <input type="checkbox" id="retail-channel-checkbox" name="rate_channel" value="retail"
                                class="form-checkbox text-green-600 h-5 w-5">
                            <span>Shop Retail Rates</span>
                        </label>
                    </div>

                    {{-- 🟢 REFACTOR START: Categories with Images --}}
                    @php
                        // Define a single source of truth for rate fields and images
                        $allCategories = [
                            'Chest' => [
                                'rate_field' => 'wholesale_hotel_chest_rate',
                                'image' => asset('assets/images/chest.png')
                            ],
                            'Thigh' => [
                                'rate_field' => 'wholesale_hotel_thigh_rate',
                                'image' => asset('assets/images/thigh.png')
                            ],
                            'Mix' => [
                                'rate_field' => 'wholesale_hotel_mix_rate',
                                'image' => asset('assets/images/mix.png')
                            ],
                            'Piece' => [
                                'rate_field' => 'wholesale_customer_piece_rate',
                                'image' => asset('assets/images/piece.png')
                            ],
                            // 1. Capital 'Live'
                            'Live' => [
                                'rate_field' => 'live_chicken_rate',
                                'image' => asset('assets/images/live.png')
                            ],
                            // 2. Lowercase 'live' (for loop matching)
                            'live' => [
                                'rate_field' => 'wholesale_rate', 
                                'image' => asset('assets/images/live.png')
                            ],
                        ];
                        
                        // Define the keys for each display group
                        $wholesaleGroup = ['live', 'Chest', 'Thigh', 'Mix', 'Piece'];
                        $retailGroup = ['Live', 'Mix', 'Chest', 'Thigh', 'Piece']; 
                    @endphp

                    <div id="category-tabs-wrapper" class="mb-6">

                        {{-- 1. WHOLESALE CATEGORIES --}}
                        <div id="wholesale-category-grid" class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            @foreach ($wholesaleGroup as $categoryName)
                                @php 
                                    if(!isset($allCategories[$categoryName])) continue;
                                    $details = $allCategories[$categoryName]; 
                                    $displayName = ($categoryName === 'live' || $categoryName === 'Live') ? 'Live' : strtoupper($categoryName);
                                @endphp

                                <div class="category-tab bg-white border border-gray-300 rounded-lg p-3 flex flex-col items-center justify-center cursor-pointer transition-all h-40 hover:border-gray-500"
                                    data-category="{{ $categoryName }}"
                                    data-rate-field="{{ $details['rate_field'] }}">
                                    
                                    {{-- 🟢 WHOLESALE IMAGE --}}
                                    <div class="icon-wrapper mb-2">
                                        {{-- Size increased to w-20 h-20 --}}
                                        <img src="{{ $details['image'] }}" 
                                             alt="{{ $categoryName }}" 
                                             class="w-20 h-20 object-contain">
                                    </div>

                                    <span class="text-sm font-bold text-gray-800 tracking-wide">
                                        {{ $displayName }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        {{-- 2. RETAIL CATEGORIES (Hidden by default) --}}
                        <div id="retail-category-grid" class="grid grid-cols-2 md:grid-cols-5 gap-3 hidden">
                            @foreach ($retailGroup as $categoryName)
                                @php 
                                    if(!isset($allCategories[$categoryName])) continue;
                                    $details = $allCategories[$categoryName]; 
                                    $displayName = ($categoryName === 'live' || $categoryName === 'Live') ? 'Live' : strtoupper($categoryName);
                                @endphp
                                
                                <div class="category-tab bg-white border border-gray-300 rounded-lg p-3 flex flex-col items-center justify-center cursor-pointer transition-all h-40 hover:border-gray-500"
                                    data-category="{{ $categoryName }}"
                                    data-rate-field="{{ $details['rate_field'] }}">
                                    
                                    {{-- 🟢 RETAIL IMAGE (FIXED) --}}
                                    <div class="icon-wrapper mb-2">
                                        {{-- Size increased to w-20 h-20 --}}
                                        <img src="{{ $details['image'] }}" 
                                             alt="{{ $categoryName }}" 
                                             class="w-20 h-20 object-contain">
                                    </div>

                                    <span class="text-sm font-bold text-gray-800 tracking-wide">
                                        {{ $displayName }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                    </div>
                    {{-- 🟢 REFACTOR END --}}


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="weight-input" class="text-sm font-medium">Weight (KG)</label>
                            <input id="weight-input" type="number" value="0" placeholder="0.000"
                                class="w-full border rounded px-3 py-2 mt-1 focus:ring">
                        </div>

                        <div>
                            <label for="rate-input" class="text-sm font-medium">Rate (PKR)</label>
                            <input id="rate-input" type="number" step="0.01" min="0" value="0.00"
                                class="w-full border rounded px-3 py-2 mt-1 focus:ring">
                            <p id="rate-source-display" class="text-xs text-gray-500 mt-1">Rate source: Not Selected</p>
                        </div>
                    </div>

                    <div class="flex justify-between text-lg font-semibold mb-6">
                        <span>Line Total:</span>
                        <span id="line-total-display" class="text-xl font-bold text-red-500">0.00 PKR</span>
                    </div>

                    <button type="button" id="add-item-btn"
                        class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors mb-4"
                        disabled>
                        + Add Item to Cart
                    </button>

                    <hr class="my-4">

                    <div id="cart-items-container" class="space-y-2 max-h-40 overflow-y-auto mb-4">
                        <div class="text-gray-500 text-center py-4">Cart is Empty</div>
                    </div>

                    <hr class="my-4">

                    <div class="flex justify-between text-xl font-bold mt-4">
                        <span>Total Payable:</span>
                        <span id="total-payable-display" class="text-3xl text-green-600">0.00 PKR</span>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="button" id="cancel-sale-btn"
                            class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400 transition-colors">
                            Cancel
                        </button>

                        <button type="submit" id="confirm-sale-btn"
                            class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition-colors"
                            disabled>
                            Confirm Sale (Credit)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 🟢 RATES DATA LOADED FROM CONTROLLER
            let ACTIVE_RATES = @json($rates);

            // State variables
            let selectedCustomerId = null;
            let selectedCustomerName = null;
            let selectedCategory = null;
            let selectedRateField = null;
            let selectedChannel = 'wholesale'; // Default to Wholesale
            let cartItems = [];

            // DOM Elements
            const customerItems = document.querySelectorAll('.customer-item');
            const customerIdInput = document.getElementById('selected-customer-id');
            const customerNameDisplay = document.getElementById('current-customer-name');
            const customerBalanceDisplay = document.getElementById('current-customer-balance');
            const categoryTabs = document.querySelectorAll('.category-tab'); // Selects ALL buttons from both grids
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

            // Checkbox Elements
            const wholesaleCheckbox = document.getElementById('wholesale-channel-checkbox');
            const retailCheckbox = document.getElementById('retail-channel-checkbox');

            const FETCH_RATES_ROUTE = "{{ route('admin.sales.fetch-rates') }}";

            // --- Rate Field Mapping (Maps Wholesale keys to their Retail equivalent) ---
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

            /**
             * Fetches the correct rate value based on the selected channel and category key.
             */
            const getRateByChannel = (channel, wholesaleKey) => {
                let rate = 0.00;
                let displayKey = '';

                if (channel === 'wholesale') {
                    // For ALL wholesale/live categories, pull from the wholesale object
                    if (ACTIVE_RATES.wholesale.hasOwnProperty(wholesaleKey)) {
                        rate = ACTIVE_RATES.wholesale[wholesaleKey];
                        displayKey = wholesaleKey;
                    }
                } else if (channel === 'retail') {

                    // 🛑 FIX START: Correctly pull Live Chicken Rate and map others
                    if (wholesaleKey === 'live_chicken_rate') {
                        // Pull Live Chicken Rate from ACTIVE_RATES.wholesale
                        if (ACTIVE_RATES.wholesale.hasOwnProperty('live_chicken_rate')) {
                            rate = ACTIVE_RATES.wholesale.live_chicken_rate;
                            displayKey = wholesaleKey + ' (Retail)';
                        }
                    } else {
                        // Handle all other retail-mapped items (Truck, Mix, Chest, Thigh, Piece)
                        const retailKey = retailRateMap[wholesaleKey];
                        if (retailKey && ACTIVE_RATES.retail.hasOwnProperty(retailKey)) {
                            rate = ACTIVE_RATES.retail[retailKey];
                            displayKey = retailKey;
                        }
                    }
                    // 🛑 FIX END
                }

                // Ensure rate is a number
                rate = parseFloat(rate) || 0.00;

                return { rate: rate, displayKey: displayKey.replace(/_/g, ' ') };
            };

            // 🟢 Function to update the rate input based on selected category and channel
            const updateRateInput = () => {
                const currentSelectedRateField = selectedRateField;
                const currentSelectedChannel = wholesaleCheckbox.checked ? 'wholesale' : 'retail';

                if (!selectedCategory || !currentSelectedRateField) {
                    rateInput.value = (0.00).toFixed(2);
                    rateSourceDisplay.textContent = 'Rate source: No Category Selected';
                    return;
                }

                // 1. Fetch the default saved rate for the current channel and category
                const rateData = getRateByChannel(currentSelectedChannel, currentSelectedRateField);

                // 2. Update UI (This is the core function for showing the saved rate)
                rateInput.value = rateData.rate.toFixed(2);
                rateSourceDisplay.textContent = `Rate source: ${currentSelectedChannel.toUpperCase()} - ${rateData.displayKey}`;

                calculateLineTotal(); // Also update line total whenever rate changes
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
                    cartContainer.innerHTML = '<div class="text-gray-500 text-center py-4">Cart is Empty</div>';
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
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data.success) {
                            ACTIVE_RATES = data.rates;
                            updateRateInput();
                            console.log('Rates successfully synchronized from backend.');
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

            // 1. Channel Selection (Checkboxes)
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
                        retailCheckbox.checked = true; // Force check retail
                        selectedChannel = 'retail';
                    } else if (clickedCheckbox.id === 'retail-channel-checkbox') {
                        wholesaleCheckbox.checked = true; // Force check wholesale
                        selectedChannel = 'wholesale';
                    }
                }

                // Toggle visibility of the two category grids
                toggleCategoryGrids(selectedChannel);

                // Re-run setup to use the newly selected channel for rate display
                if (selectedCategory) {
                    updateRateInput();
                }
            };

            wholesaleCheckbox.addEventListener('change', handleChannelChange);
            retailCheckbox.addEventListener('change', handleChannelChange);

            // 2. Customer Selection 
            customerItems.forEach(item => {
                item.addEventListener('click', function () {
                    customerItems.forEach(i => i.classList.remove('bg-yellow-200'));
                    this.classList.add('bg-yellow-200');

                    selectedCustomerId = this.dataset.id;
                    selectedCustomerName = this.dataset.name;
                    const balance = parseFloat(this.dataset.balance);

                    customerIdInput.value = selectedCustomerId;
                    document.getElementById('current-customer-name').textContent = selectedCustomerName;
                    document.getElementById('current-customer-balance').textContent = formatCurrency(balance);

                    updateAddItemButton();
                    if (cartItems.length > 0) {
                        confirmSaleBtn.disabled = false;
                    }
                });
            });

            // 3. Category Selection (Event listener uses the shared class '.category-tab')
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    // Clear active state from all
                    categoryTabs.forEach(t => {
                        t.classList.remove('bg-yellow-400', 'border-black', 'shadow-md');
                        t.classList.add('bg-white', 'border-gray-300');
                    });

                    // Set active state on clicked tab (Yellow Background like image)
                    this.classList.remove('bg-white', 'border-gray-300');
                    this.classList.add('bg-yellow-400', 'border-black', 'shadow-md');

                    selectedCategory = this.dataset.category;
                    selectedRateField = this.dataset.rateField;

                    updateRateInput(); // 🟢 Load the new default rate
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
                    alert('Please select a customer, category, and enter valid weight/rate.');
                }
            });

            // 🟢 Handle Confirm Sale Submission (AJAX)
            saleForm.addEventListener('submit', async function (e) {
                e.preventDefault(); // Prevent default form submission (page reload)

                if (!selectedCustomerId || cartItems.length === 0) {
                    alert('Please select a customer and add items to the cart.');
                    return;
                }

                // Disable button during submission to prevent double click
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
                        // Success: Show message and update customer balance on the fly

                        alert('Success: ' + data.message + ` (Sale ID: ${data.sale_id})`);

                        // 1. Update the Balance Display (Current Transaction View)
                        customerBalanceDisplay.textContent = formatCurrency(data.updated_balance);

                        // 2. Update the data attribute on the relevant list item (Customer Selection List)
                        const customerListItem = document.querySelector(`.customer-item[data-id="${data.customer_id}"]`);
                        if (customerListItem) {
                            customerListItem.dataset.balance = data.updated_balance;

                            // 3. Update the small balance text inside the list item
                            const balanceSpan = customerListItem.querySelector('.text-sm.text-gray-700');
                            if (balanceSpan) {
                                balanceSpan.textContent = `(Bal: ${formatCurrencyNoDecimal(data.updated_balance)} PKR)`;
                            }

                            // Optional: Highlight the customer list item for confirmation
                            customerListItem.classList.remove('bg-yellow-200');
                            customerListItem.classList.add('bg-green-200');
                            setTimeout(() => {
                                customerListItem.classList.remove('bg-green-200');
                                customerListItem.classList.add('bg-yellow-200'); // Re-highlight current selection
                            }, 1500);
                        }

                        // 4. Reset POS for next sale
                        cartItems = [];
                        updateCartDisplay();
                        weightInput.value = 0;
                        calculateLineTotal();

                    } else {
                        // Handle Validation/Server Errors
                        let errorMessage = data.message || 'An unknown error occurred.';
                        if (data.errors) {
                            errorMessage += "\nValidation Errors:\n" + Object.values(data.errors).flat().join('\n');
                        }
                        alert('Error: ' + errorMessage);
                    }

                } catch (error) {
                    console.error('Sale confirmation failed:', error);
                    alert('A network or critical error occurred. Check the console.');
                } finally {
                    // Re-enable button
                    confirmSaleBtn.disabled = false;
                    confirmSaleBtn.textContent = 'Confirm Sale (Credit)';
                }
            });

            // 6. Final State Setup (Default Selections)
            const initialSetup = () => {
                // 1. Select first customer
                if (customerItems.length > 0) {
                    customerItems[0].click();
                }

                // 2. Ensure Wholesale Grid is shown by default
                toggleCategoryGrids(selectedChannel);

                // 3. Select the 'Live/Whole' category by default for wholesale selling
                // Try live (lowercase) first
                let liveTab = document.querySelector('#wholesale-category-grid .category-tab[data-category="live"]');
                if(!liveTab) {
                     // Try Live (Uppercase)
                     liveTab = document.querySelector('#wholesale-category-grid .category-tab[data-category="Live"]');
                }

                if (liveTab) {
                    // Manually simulate a click event to trigger category selection and rate update logic
                    liveTab.click();
                } else {
                    // Fallback to the first category
                    const firstTab = document.querySelector('#wholesale-category-grid .category-tab');
                    if (firstTab) firstTab.click();
                }
            }

            // Run initial setup immediately after DOM is ready
            initialSetup();

            // 7. Final state cleanup
            updateCartDisplay();

            // 8. Handle removal of cart item
            cartContainer.addEventListener('click', function (e) {
                if (e.target.closest('.remove-item-btn')) {
                    const index = e.target.closest('.remove-item-btn').dataset.index;
                    cartItems.splice(index, 1);
                    updateCartDisplay();
                }
            });

            // 9. Handle Cancel button
            document.getElementById('cancel-sale-btn').addEventListener('click', function () {
                cartItems = [];
                updateCartDisplay();
                window.location.reload();
            });

            // 10. Listener to check for rate updates when the tab becomes active (if any)
            window.addEventListener('focus', function () {
                const rateUpdateFlag = localStorage.getItem('rates_updated');

                if (rateUpdateFlag === 'true') {
                    fetchLatestRates().then(success => {
                        if (success) {
                            localStorage.removeItem('rates_updated');
                        }
                    });
                }
            });

        });
    </script>
@endsection