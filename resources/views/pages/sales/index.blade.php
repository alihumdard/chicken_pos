@extends('layouts.main')

@section('content')
<div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100 ">

    <h1 class="text-2xl font-bold mb-6">Sales Point - Wholesale & Permanent</h1>
    <hr>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-[83vh]">

        <!-- CUSTOMER LIST -->
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
                    <li class="customer-item p-3 hover:bg-gray-100 rounded cursor-pointer text-lg font-bold transition-colors"
                        data-id="{{ $customer->id }}"
                        data-name="{{ $customer->name }}"
                        data-balance="{{ number_format($customer->current_balance, 2, '.', '') }}"
                    >
                        {{ $customer->name }}
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

        <!-- TRANSACTION PANEL -->
        <div class="md:col-span-2 bg-white p-4 rounded-lg shadow-md flex flex-col justify-between">
            <form id="sale-form" action="{{ route('admin.sales.store') }}" method="POST">
                @csrf
                <input type="hidden" name="customer_id" id="selected-customer-id" required>
                <input type="hidden" name="total_payable" id="final-total-payable" required>
                
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-2xl font-bold">New Transaction</h2>
                    <p>
                        Selling to: <span class="font-bold text-blue-600" id="current-customer-name">Please Select Customer</span> | Current Bal:
                        <span id="current-customer-balance" class="font-bold text-red-600">0.00 PKR</span>
                    </p>
                </div>

                <!-- PRODUCT TABS -->
                <div id="category-tabs" class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
                    @php
                        $categories = [
                            ['name' => 'Whole', 'icon' => 'fa-brands fa-the-red-yeti'],
                            ['name' => 'Chest', 'icon' => 'fa-solid fa-drumstick-bite'],
                            ['name' => 'Thigh', 'icon' => 'fa-solid fa-drumstick-bite'],
                            ['name' => 'Mix', 'icon' => 'fa-solid fa-layer-group'],
                            ['name' => 'Piece', 'icon' => 'fa-solid fa-bone'],
                        ];
                    @endphp
                    @foreach ($categories as $category)
                        <div class="category-tab bg-gray-100 rounded-lg p-4 flex flex-col items-center cursor-pointer hover:bg-gray-200 transition-all"
                            data-category="{{ $category['name'] }}">
                            <i class="{{ $category['icon'] }} text-3xl mb-2 text-gray-700"></i>
                            <span class="text-sm font-medium">{{ $category['name'] }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- INPUT AREA -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="weight-input" class="text-sm font-medium">Weight (KG)</label>
                        <input id="weight-input" type="number" value="0" placeholder="0.000"
                            class="w-full border rounded px-3 py-2 mt-1 focus:ring">
                    </div>

                    <div>
                        <label for="rate-input" class="text-sm font-medium">Rate (PKR)</label>
                        <input id="rate-input" type="number" step="0.01" min="0" value="750.00"
                            class="w-full border rounded px-3 py-2 mt-1 focus:ring">
                    </div>
                </div>

                <div class="flex justify-between text-lg font-semibold mb-6">
                    <span>Line Total:</span>
                    <span id="line-total-display" class="text-xl font-bold text-red-500">0.00 PKR</span>
                </div>

                <button type="button" id="add-item-btn" class="w-full bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition-colors mb-4" disabled>
                    + Add Item to Cart
                </button>
                
                <hr class="my-4">

                <!-- CART ITEMS -->
                <div id="cart-items-container" class="space-y-2 max-h-40 overflow-y-auto mb-4">
                    <!-- Dynamic items will be inserted here -->
                    <div class="text-gray-500 text-center py-4">Cart is Empty</div>
                </div>
                
                <hr class="my-4">
                
                <div class="flex justify-between text-xl font-bold mt-4">
                    <span>Total Payable:</span>
                    <span id="total-payable-display" class="text-3xl text-green-600">0.00 PKR</span>
                </div>
                
                <div class="flex gap-4 mt-6">
                    <button type="button" id="cancel-sale-btn" class="w-full bg-gray-300 py-2 rounded hover:bg-gray-400 transition-colors">
                        Cancel
                    </button>
                    
                    <button type="submit" id="confirm-sale-btn" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition-colors" disabled>
                        Confirm Sale (Credit)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // State variables
        let selectedCustomerId = null;
        let selectedCustomerName = '';
        let selectedCategory = null;
        let cartItems = [];

        // DOM Elements
        const customerItems = document.querySelectorAll('.customer-item');
        const customerIdInput = document.getElementById('selected-customer-id');
        const customerNameDisplay = document.getElementById('current-customer-name');
        const customerBalanceDisplay = document.getElementById('current-customer-balance');
        const categoryTabs = document.querySelectorAll('.category-tab');
        const weightInput = document.getElementById('weight-input');
        const rateInput = document.getElementById('rate-input');
        const lineTotalDisplay = document.getElementById('line-total-display');
        const addItemBtn = document.getElementById('add-item-btn');
        const cartContainer = document.getElementById('cart-items-container');
        const totalPayableDisplay = document.getElementById('total-payable-display');
        const finalTotalPayableInput = document.getElementById('final-total-payable');
        const confirmSaleBtn = document.getElementById('confirm-sale-btn');
        const saleForm = document.getElementById('sale-form');

        // --- Utility Functions ---

        const formatCurrency = (value) => {
            return parseFloat(value).toLocaleString('en-PK', { minimumFractionDigits: 2 }) + ' PKR';
        };

        const calculateLineTotal = () => {
            const weight = parseFloat(weightInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const total = weight * rate;
            lineTotalDisplay.textContent = formatCurrency(total);
            updateAddItemButton();
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
            cartContainer.innerHTML = ''; // Clear existing items

            if (cartItems.length === 0) {
                cartContainer.innerHTML = '<div class="text-gray-500 text-center py-4">Cart is Empty</div>';
                confirmSaleBtn.disabled = true;
            } else {
                cartItems.forEach((item, index) => {
                    const lineTotal = item.weight * item.rate;
                    grandTotal += lineTotal;
                    
                    // Create line item HTML
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'flex justify-between text-gray-700 py-1 border-b';
                    itemDiv.innerHTML = `
                        <div class="flex items-center space-x-2">
                            <button type="button" data-index="${index}" class="remove-item-btn text-red-500 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-times-circle"></i>
                            </button>
                            <span>${index + 1}. ${item.category} - ${item.weight.toFixed(3)}kg @ ${item.rate.toFixed(2)}</span>
                        </div>
                        <span>${formatCurrency(lineTotal)}</span>
                    `;
                    cartContainer.appendChild(itemDiv);
                });
                confirmSaleBtn.disabled = !selectedCustomerId; // Enable if customer is selected and cart is not empty
            }

            // Update totals
            totalPayableDisplay.textContent = formatCurrency(grandTotal);
            finalTotalPayableInput.value = grandTotal.toFixed(2);
        };
        
        // --- Event Handlers ---

        // 1. Customer Selection
        customerItems.forEach(item => {
            item.addEventListener('click', function() {
                // Clear active state from all
                customerItems.forEach(i => i.classList.remove('bg-yellow-200'));

                // Set active state on clicked item
                this.classList.add('bg-yellow-200');

                // Update state and displays
                selectedCustomerId = this.dataset.id;
                selectedCustomerName = this.dataset.name;
                const balance = parseFloat(this.dataset.balance);
                
                customerIdInput.value = selectedCustomerId;
                customerNameDisplay.textContent = selectedCustomerName;
                customerBalanceDisplay.textContent = formatCurrency(balance);
                
                updateAddItemButton(); // Re-check button status
                if (cartItems.length > 0) {
                     confirmSaleBtn.disabled = false;
                }
            });
            // Initial selection of the first customer (simulate behavior)
            if (customerItems.length > 0 && customerItems[0].dataset.id === @json($customers->first()->id ?? null)) {
                customerItems[0].click();
            }
        });
        
        // 2. Category Selection
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Clear active state from all
                categoryTabs.forEach(t => t.classList.remove('bg-yellow-300', 'hover:bg-yellow-400'));
                categoryTabs.forEach(t => t.classList.add('bg-gray-100', 'hover:bg-gray-200'));
                
                // Set active state on clicked tab
                this.classList.remove('bg-gray-100', 'hover:bg-gray-200');
                this.classList.add('bg-yellow-300', 'hover:bg-yellow-400');

                // Update state
                selectedCategory = this.dataset.category;
                updateAddItemButton();
            });
            // Initial selection of the first category
            if (categoryTabs.length > 0) {
                 categoryTabs[1].click(); // Chest is index 1, mimicking the original file's active state
            }
        });

        // 3. Weight/Rate Input Changes
        weightInput.addEventListener('input', calculateLineTotal);
        rateInput.addEventListener('input', calculateLineTotal);

        // **NEW FIX: Prevent form submission on Enter key press**
        saleForm.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Stop the form from submitting (reloading the page)
                
                // If the user presses Enter on the Rate input, simulate clicking "Add Item"
                if (e.target === rateInput) {
                    addItemBtn.click();
                }
                // If the user presses Enter on the Weight input, move focus to Rate input
                else if (e.target === weightInput) {
                    rateInput.focus();
                }
            }
        });

        // 4. Add Item to Cart
        addItemBtn.addEventListener('click', function() {
            if (selectedCustomerId && selectedCategory && parseFloat(weightInput.value) > 0 && parseFloat(rateInput.value) > 0) {
                const item = {
                    category: selectedCategory,
                    weight: parseFloat(weightInput.value),
                    rate: parseFloat(rateInput.value),
                };
                cartItems.push(item);

                // Reset inputs and category selection after adding
                weightInput.value = 0;
                rateInput.value = 750.00; // Reset rate to a default
                calculateLineTotal(); // Recalculate and reset line total display

                // Re-click the currently selected category to maintain its active state visually
                const activeTab = document.querySelector('.category-tab.bg-yellow-300');
                if (activeTab) activeTab.click();

                updateCartDisplay();
            } else {
                alert('Please select a customer, category, and enter valid weight/rate.');
            }
        });

        // 5. Remove Item from Cart (Delegated Event)
        cartContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                const btn = e.target.closest('.remove-item-btn');
                const indexToRemove = parseInt(btn.dataset.index);
                cartItems.splice(indexToRemove, 1);
                updateCartDisplay();
            }
        });

        // 6. Form Submission (AJAX)
        saleForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!selectedCustomerId || cartItems.length === 0) {
                alert('Please select a customer and add items to the cart.');
                return;
            }

            confirmSaleBtn.disabled = true;
            confirmSaleBtn.textContent = 'Processing...';

            // Prepare the data to send
            const data = {
                _token: document.querySelector('input[name="_token"]').value,
                customer_id: selectedCustomerId,
                cart_items: cartItems.map(item => ({
                    category: item.category,
                    weight: item.weight,
                    rate: item.rate,
                })),
                total_payable: parseFloat(finalTotalPayableInput.value),
            };

            try {
                const response = await fetch(saleForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(data),
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Sale successful! Transaction ID: ' + result.sale_id);
                    // Reset the form and state
                    cartItems = [];
                    updateCartDisplay();
                    document.getElementById('cancel-sale-btn').click(); // Triggers full reset logic
                } else {
                    alert('Sale failed: ' + (result.message || 'Server Error'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An unexpected error occurred during the sale.');
            } finally {
                confirmSaleBtn.disabled = false;
                confirmSaleBtn.textContent = 'Confirm Sale (Credit)';
            }
        });
        
        // 7. Cancel/Reset Logic
        document.getElementById('cancel-sale-btn').addEventListener('click', function() {
            cartItems = [];
            updateCartDisplay();
            selectedCustomerId = null;
            selectedCategory = null;
            
            // Clear customer selection UI
            customerItems.forEach(i => i.classList.remove('bg-yellow-200'));
            customerNameDisplay.textContent = 'Please Select Customer';
            customerBalanceDisplay.textContent = '0.00 PKR';
            customerIdInput.value = '';

            // Reset category selection UI
            categoryTabs.forEach(t => t.classList.remove('bg-yellow-300', 'hover:bg-yellow-400'));
            categoryTabs.forEach(t => t.classList.add('bg-gray-100', 'hover:bg-gray-200'));
            if (categoryTabs.length > 0) {
                 categoryTabs[1].click(); // Reset to the default selected tab
            }

            // Reset inputs
            weightInput.value = 0;
            rateInput.value = 750.00;
            calculateLineTotal();
        });


        // Initial setup calls
        calculateLineTotal();
        updateCartDisplay();

    });
</script>
@endsection