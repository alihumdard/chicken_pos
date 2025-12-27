@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* 🟢 Custom Scrollbar for the Slider */
        .category-slider::-webkit-scrollbar { height: 6px; }
        .category-slider::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .category-slider { scroll-behavior: smooth; -ms-overflow-style: none; scrollbar-width: thin; }
        
        .category-tab.active {
            background-color: #facc15 !important; /* bg-yellow-400 */
            border-color: #000 !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
    </style>

    <div class="flex-1 p-3 sm:p-6 lg:p-8 bg-gray-100">

        {{-- 🟢 Header Row with View Rates Button --}}
        <div class="flex justify-between items-center mb-4 lg:mb-6">
            <h1 class="text-xl lg:text-2xl font-bold">Sales Point</h1>
            <button onclick="openRatesModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold shadow-md flex items-center gap-2 transition-all">
                <i class="fas fa-eye"></i> View Current Rates
            </button>
        </div>
        
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
                            No customers found.
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
                            | Bal: <span id="current-customer-balance" class="font-bold text-red-600">0.00 PKR</span>
                        </p>
                    </div>

                    {{-- Channel Selection --}}
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6 mb-6 p-3 border rounded-lg bg-gray-50">
                        <label class="flex items-center space-x-2 font-medium text-sm lg:text-lg cursor-pointer">
                            <input type="radio" name="rate_channel" value="wholesale" id="wholesale-channel-checkbox"
                                class="form-radio text-blue-600 h-5 w-5" checked>
                            <span>Wholesale Rates</span>
                        </label>

                        <label class="flex items-center space-x-2 font-medium text-sm lg:text-lg cursor-pointer">
                            <input type="radio" name="rate_channel" value="retail" id="retail-channel-checkbox"
                                class="form-radio text-green-600 h-5 w-5">
                            <span>Shop Retail Rates</span>
                        </label>
                    </div>

                    {{-- 🟢 Categories with Slider --}}
                    @php
                        $wholesaleProducts = [
                            ['label' => 'Live', 'field' => 'wholesale_rate', 'img' => 'live.png'],
                            ['label' => 'Mix (34)', 'field' => 'wholesale_mix_rate', 'img' => 'mix.png'],
                            ['label' => 'Mix (35)', 'field' => 'wholesale_chest_rate', 'img' => 'chest.png'],
                            ['label' => 'Mix (36)', 'field' => 'wholesale_thigh_rate', 'img' => 'thigh.png'],
                            ['label' => 'Mix (37)', 'field' => 'wholesale_customer_piece_rate', 'img' => 'piece.png'],
                            ['label' => 'Chest & Leg (38)', 'field' => 'wholesale_chest_and_leg_pieces', 'img' => 'live.png'], 
                            ['label' => 'Drum Sticks', 'field' => 'wholesale_drum_sticks', 'img' => 'mix.png'], 
                            ['label' => 'Chest Boneless', 'field' => 'wholesale_chest_boneless', 'img' => 'chest.png'], 
                            ['label' => 'Thigh Boneless', 'field' => 'wholesale_thigh_boneless', 'img' => 'thigh.png'], 
                            ['label' => 'Kalagi Pot', 'field' => 'wholesale_kalagi_pot_gardan', 'img' => 'piece.png']  
                        ];

                        $retailProducts = [
                            ['label' => 'Retail Live', 'field' => 'live_chicken_rate', 'img' => 'live.png'],
                            ['label' => 'Mix (34)', 'field' => 'retail_mix_rate', 'img' => 'mix.png'],
                            ['label' => 'Mix (35)', 'field' => 'retail_chest_rate', 'img' => 'chest.png'],
                            ['label' => 'Mix (36)', 'field' => 'retail_thigh_rate', 'img' => 'thigh.png'],
                            ['label' => 'Mix (37)', 'field' => 'retail_piece_rate', 'img' => 'piece.png'],
                            ['label' => 'Chest & Leg (38)', 'field' => 'retail_chest_and_leg_pieces', 'img' => 'live.png'],
                            ['label' => 'Drum Sticks', 'field' => 'retail_drum_sticks', 'img' => 'mix.png'],
                            ['label' => 'Chest Boneless', 'field' => 'retail_chest_boneless', 'img' => 'chest.png'],
                            ['label' => 'Thigh Boneless', 'field' => 'retail_thigh_boneless', 'img' => 'thigh.png'],
                            ['label' => 'Kalagi Pot', 'field' => 'retail_kalagi_pot_gardan', 'img' => 'piece.png']
                        ];
                    @endphp

                    <div id="category-tabs-wrapper" class="mb-6">
                        <div id="wholesale-category-grid" class="category-slider flex overflow-x-auto space-x-3 pb-4">
                            @foreach ($wholesaleProducts as $p)
                                <div class="category-tab flex-shrink-0 bg-white border border-gray-300 rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer transition-all w-32 h-32 lg:w-40 lg:h-40 hover:border-blue-500 shadow-sm"
                                    data-category="{{ $p['label'] }}" data-rate-field="{{ $p['field'] }}">
                                    <img src="{{ asset('assets/images/' . $p['img']) }}" class="w-12 h-12 lg:w-16 lg:h-16 object-contain mb-2">
                                    <span class="text-[10px] lg:text-xs font-bold text-center uppercase">{{ $p['label'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div id="retail-category-grid" class="category-slider flex overflow-x-auto space-x-3 pb-4 hidden">
                            @foreach ($retailProducts as $p)
                                <div class="category-tab flex-shrink-0 bg-white border border-gray-300 rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer transition-all w-32 h-32 lg:w-40 lg:h-40 hover:border-green-500 shadow-sm"
                                    data-category="{{ $p['label'] }}" data-rate-field="{{ $p['field'] }}">
                                    <img src="{{ asset('assets/images/' . $p['img']) }}" class="w-12 h-12 lg:w-16 lg:h-16 object-contain mb-2">
                                    <span class="text-[10px] lg:text-xs font-bold text-center uppercase">{{ $p['label'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Inputs --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="weight-input" class="text-sm font-medium">Weight (KG)</label>
                            <input id="weight-input" type="number" step="0.001" value="0"
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
                        class="w-full bg-blue-500 text-white py-3 rounded hover:bg-blue-600 transition-colors mb-4 text-lg font-bold disabled:opacity-50"
                        disabled>
                        + Add Item 
                    </button>

                    <div id="cart-items-container" class="space-y-2 max-h-40 overflow-y-auto mb-4 border-t pt-4">
                        <div class="text-gray-500 text-center py-4">Empty</div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-lg lg:text-xl font-bold">Total Bill:</span>
                            <span id="total-payable-display" class="text-2xl lg:text-3xl font-bold text-green-600">0.00 PKR</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-bold text-gray-700">Cash Received:</label>
                                <input type="number" name="cash_received" id="cash-received-input" value="0" 
                                    class="w-full border rounded px-3 py-2 mt-1 text-xl font-bold text-gray-800">
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Net Balance Change</p>
                                <p id="net-balance-change" class="text-lg font-bold text-gray-600">0.00</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="button" id="cancel-sale-btn" class="w-full bg-gray-300 py-3 rounded hover:bg-gray-400 font-semibold">Cancel</button>
                        <button type="submit" id="confirm-sale-btn" class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 font-bold shadow-md disabled:opacity-50" disabled>Confirm Sale</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- 🟢 MODAL: VIEW RATES & FORMULAS --}}
    <div id="ratesModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm" onclick="closeRatesModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-4xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-800">Today's Formulas & Rates</h3>
                    <button onclick="closeRatesModal()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                </div>
                <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Wholesale Section --}}
                        <div>
                            <h4 class="font-bold text-blue-600 border-b pb-2 mb-3 uppercase tracking-wider text-sm">Wholesale Channel</h4>
                            <table class="w-full text-sm text-left">
                                <thead class="text-gray-500 border-b">
                                    <tr>
                                        <th class="py-2">Category</th>
                                        <th class="py-2">Formula</th>
                                        <th class="py-2 text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($wholesaleProducts as $p)
                                        @php $f = $formulas->get($p['field']); @endphp
                                        <tr>
                                            <td class="py-3 font-semibold">{{ $p['label'] }}</td>
                                            <td class="py-3 text-xs text-gray-500 font-mono">
                                                @if($f)
                                                    x{{ $f->multiply }} /{{ $f->divide }} +{{ $f->plus }} -{{ $f->minus }}
                                                @else
                                                    Default
                                                @endif
                                            </td>
                                            <td class="py-3 text-right font-bold text-blue-700">PKR {{ number_format($rates['wholesale'][$p['field']] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Retail Section --}}
                        <div>
                            <h4 class="font-bold text-green-600 border-b pb-2 mb-3 uppercase tracking-wider text-sm">Retail Channel</h4>
                            <table class="w-full text-sm text-left">
                                <thead class="text-gray-500 border-b">
                                    <tr>
                                        <th class="py-2">Category</th>
                                        <th class="py-2">Formula</th>
                                        <th class="py-2 text-right">Price</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($retailProducts as $p)
                                        @php $f = $formulas->get($p['field']); @endphp
                                        <tr>
                                            <td class="py-3 font-semibold">{{ $p['label'] }}</td>
                                            <td class="py-3 text-xs text-gray-500 font-mono">
                                                @if($f)
                                                    x{{ $f->multiply }} /{{ $f->divide }} +{{ $f->plus }} -{{ $f->minus }}
                                                @else
                                                    Default
                                                @endif
                                            </td>
                                            <td class="py-3 text-right font-bold text-green-700">PKR {{ number_format($rates['retail'][$p['field']] ?? 0, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end">
                    <button onclick="closeRatesModal()" class="bg-gray-800 text-white px-6 py-2 rounded-lg font-semibold">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL FOR ADDING CUSTOMER --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md border border-gray-100">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Add New Customer</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" name="type" value="customer"> 
                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" id="openingBalance" value="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none">
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" id="saveContactBtn" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg font-semibold">Save</button>
                        <button type="button" onclick="closeModal()" class="bg-white border px-6 py-2.5 rounded-lg">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openRatesModal() { document.getElementById('ratesModal').classList.remove('hidden'); }
    function closeRatesModal() { document.getElementById('ratesModal').classList.add('hidden'); }
    
    document.addEventListener('DOMContentLoaded', function () {
        let ACTIVE_RATES = @json($rates);
        let selectedCustomerId = null;
        let selectedCategory = null;
        let selectedRateField = null;
        let cartItems = [];

        // DOM Elements
        const customerIdInput = document.getElementById('selected-customer-id');
        const customerNameDisplay = document.getElementById('current-customer-name');
        const customerBalanceDisplay = document.getElementById('current-customer-balance');
        const wholesaleGrid = document.getElementById('wholesale-category-grid');
        const retailGrid = document.getElementById('retail-category-grid');
        const weightInput = document.getElementById('weight-input');
        const rateInput = document.getElementById('rate-input');
        const addItemBtn = document.getElementById('add-item-btn');
        const cartContainer = document.getElementById('cart-items-container');
        const totalPayableDisplay = document.getElementById('total-payable-display');
        const finalTotalPayableInput = document.getElementById('final-total-payable');
        const confirmSaleBtn = document.getElementById('confirm-sale-btn');
        const cashReceivedInput = document.getElementById('cash-received-input');
        const netBalanceChangeDisplay = document.getElementById('net-balance-change');

        const formatCurrency = (val) => parseFloat(val).toLocaleString('en-PK', { minimumFractionDigits: 2 }) + ' PKR';

        const updateRateInput = () => {
            const channel = document.querySelector('input[name="rate_channel"]:checked').value;
            if (!selectedRateField) return;
            const rate = parseFloat(ACTIVE_RATES[channel][selectedRateField] || 0);
            rateInput.value = rate.toFixed(2);
            document.getElementById('rate-source-display').textContent = `Rate source: ${channel.toUpperCase()}`;
            calculateLineTotal();
        };

        const calculateLineTotal = () => {
            const weight = parseFloat(weightInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const total = weight * rate;
            document.getElementById('line-total-display').textContent = formatCurrency(total);
            addItemBtn.disabled = !(selectedCustomerId && selectedCategory && weight > 0);
        };

        document.querySelectorAll('input[name="rate_channel"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'wholesale') {
                    wholesaleGrid.classList.remove('hidden'); retailGrid.classList.add('hidden');
                } else {
                    wholesaleGrid.classList.add('hidden'); retailGrid.classList.remove('hidden');
                }
                updateRateInput();
            });
        });

        document.querySelectorAll('.customer-item').forEach(item => {
            item.addEventListener('click', function () {
                document.querySelectorAll('.customer-item').forEach(i => i.classList.remove('bg-yellow-200'));
                this.classList.add('bg-yellow-200');
                selectedCustomerId = this.dataset.id;
                customerIdInput.value = selectedCustomerId;
                customerNameDisplay.textContent = this.dataset.name;
                customerBalanceDisplay.textContent = formatCurrency(this.dataset.balance);
                calculateLineTotal();
            });
        });

        document.querySelectorAll('.category-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                selectedCategory = this.dataset.category;
                selectedRateField = this.dataset.rateField;
                updateRateInput();
            });
        });

        weightInput.addEventListener('input', calculateLineTotal);
        rateInput.addEventListener('input', calculateLineTotal);

        addItemBtn.addEventListener('click', function () {
            const channel = document.querySelector('input[name="rate_channel"]:checked').value;
            cartItems.unshift({ 
                category: selectedCategory, 
                channel: channel.toUpperCase(), 
                weight: parseFloat(weightInput.value), 
                rate: parseFloat(rateInput.value) 
            });
            weightInput.value = 0;
            updateCartDisplay();
            weightInput.focus();
        });

        const updateCartDisplay = () => {
            let total = 0;
            cartContainer.innerHTML = cartItems.length ? '' : '<div class="text-gray-500 text-center py-4">Empty</div>';
            cartItems.forEach((item, idx) => {
                const line = item.weight * item.rate;
                total += line;
                const div = document.createElement('div');
                div.className = 'flex justify-between border-b py-2 items-center';
                div.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="removeItem(${idx})" class="text-red-500 hover:text-red-700 transition"><i class="fa-solid fa-times-circle"></i></button>
                        <div class="text-sm">
                            <p class="font-bold text-gray-800">${item.category} (${item.channel})</p>
                            <p class="text-gray-500 font-medium">${item.weight.toFixed(3)}kg @ ${item.rate.toFixed(2)}</p>
                        </div>
                    </div>
                    <span class="font-bold text-blue-600">${formatCurrency(line)}</span>
                `;
                cartContainer.appendChild(div);
            });
            totalPayableDisplay.textContent = formatCurrency(total);
            finalTotalPayableInput.value = total.toFixed(2);
            confirmSaleBtn.disabled = !(selectedCustomerId && cartItems.length > 0);
            calculateNetBalanceChange();
        };

        window.removeItem = (idx) => { cartItems.splice(idx, 1); updateCartDisplay(); };

        const calculateNetBalanceChange = () => {
            const total = parseFloat(finalTotalPayableInput.value) || 0;
            const received = parseFloat(cashReceivedInput.value) || 0;
            const net = total - received;
            netBalanceChangeDisplay.textContent = formatCurrency(net);
            netBalanceChangeDisplay.className = net > 0 ? 'text-lg font-bold text-red-600' : (net < 0 ? 'text-lg font-bold text-green-600' : 'text-lg font-bold text-gray-600');
        };

        cashReceivedInput.addEventListener('input', calculateNetBalanceChange);
        const firstTab = document.querySelector('#wholesale-category-grid .category-tab');
        if (firstTab) firstTab.click();

        document.getElementById('customer-search').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.customer-item').forEach(li => {
                li.style.display = li.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    });

    function closeModal() { document.getElementById('contactModal').classList.add('hidden'); }
    function openModal() { document.getElementById('contactModal').classList.remove('hidden'); }
    </script>
@endsection