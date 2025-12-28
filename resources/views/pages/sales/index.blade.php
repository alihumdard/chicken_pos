@extends('layouts.main')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .category-slider::-webkit-scrollbar { height: 6px; }
        .category-slider::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .category-slider { scroll-behavior: smooth; -ms-overflow-style: none; scrollbar-width: thin; }
        
        .category-tab.active {
            background-color: #facc15 !important;
            border-color: #000 !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        .pinned-badge {
            background: #ef4444;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .live-badge {
            background: #fffb20ff;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .permanent-badge {
            background: #61ef44ff;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
        }
    </style>

    <div class="flex-1 p-3 sm:p-6 lg:p-8 bg-gray-100">

        <div class="flex justify-between items-center mb-4 lg:mb-6">
            <h1 class="text-xl lg:text-2xl font-bold">Sales Point</h1>
            <button onclick="openRatesModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold shadow-md flex items-center gap-2 transition-all text-sm">
                <i class="fas fa-eye"></i> View Rates
            </button>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

            {{-- Left Column: Customer Selection --}}
            <div class="bg-white p-4 rounded-lg shadow-md overflow-hidden border">
                <div class="flex justify-between items-center mb-3">
                    {{-- Click heading to Reset/Show All --}}
                    <h2 class="text-lg font-bold cursor-pointer hover:text-blue-600" onclick="filterCustomers('all')" title="Click to Show All">
                        Customers
                    </h2>
                    
                    <div class="flex gap-1">
                        <button onclick="openModal()" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold py-2 px-3 rounded flex items-center">
                            <i class="fas fa-plus mr-1"></i> New
                        </button>
                        
                        {{-- Filter: Permanent (customer) --}}
                        <button onclick="filterCustomers('customer')" class="bg-red-800 hover:bg-red-900 text-white text-xs font-bold py-2 px-3 rounded flex items-center">
                            Permanent
                        </button>

                        {{-- Filter: Live (broker) --}}
                        <button onclick="filterCustomers('broker')" class="bg-red-800 hover:bg-red-900 text-white text-xs font-bold py-2 px-3 rounded flex items-center">
                            Live
                        </button>
                    </div>
                </div>

                <div class="relative mb-4">
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    {{-- Updated ID for clearer targeting --}}
                    <input id="customer-search" type="text" placeholder="Search Customer..." class="w-full border rounded px-10 py-2 outline-none focus:ring-2 focus:ring-blue-200 text-sm">
                </div>

                <ul id="customer-list" class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                    @php
                        $sortedCustomers = $customers->sortByDesc(function($customer) {
                            return $customer->type === 'shop_retail';
                        });
                    @endphp

                    @forelse ($sortedCustomers as $customer)
                        {{-- ðŸŸ¢ ADDED data-type HERE --}}
                        <li class="customer-item p-3 rounded-lg border cursor-pointer transition-all hover:bg-gray-50 {{ $customer->type === 'shop_retail' ? 'bg-blue-50 border-blue-200' : 'bg-white border-gray-100' }}"
                            data-id="{{ $customer->id }}" 
                            data-type="{{ $customer->type }}" 
                            data-name="{{ $customer->name }}"
                            data-balance="{{ number_format($customer->current_balance, 2, '.', '') }}">
                            
                            <div class="flex justify-between items-start">
                                <span class="font-bold text-gray-800">{{ $customer->name }}</span>
                                @if($customer->type === 'shop_retail')
                                    <span class="pinned-badge"><i class="fas fa-thumbtack"></i> Shop</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-600">Bal: <b>{{ number_format($customer->current_balance, 0) }}</b> PKR</span>
                        </li>
                    @empty
                        <li class="p-3 text-gray-500 text-sm text-center">No customers found.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Right Column: Sale Form --}}
            <div class="lg:col-span-2 bg-white p-4 rounded-lg shadow-md flex flex-col border">
                <form id="sale-form">
                    @csrf
                    <input type="hidden" name="customer_id" id="selected-customer-id" required>
                    <input type="hidden" name="total_payable" id="final-total-payable" required>

                    <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-2 border-b pb-2">
                        <h2 class="text-lg font-bold uppercase tracking-tight">New Transaction</h2>
                        <div class="text-sm">
                            <span class="font-bold text-blue-600" id="current-customer-name">Select Customer</span> 
                            | Bal: <span id="current-customer-balance" class="font-bold text-red-600">0.00</span>
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4 p-2 border rounded-lg bg-gray-50">
                        <label class="flex items-center space-x-2 font-bold text-xs cursor-pointer">
                            <input type="radio" name="rate_channel" value="wholesale" checked> <span>WHOLESALE</span>
                        </label>
                        <label class="flex items-center space-x-2 font-bold text-xs cursor-pointer">
                            <input type="radio" name="rate_channel" value="retail"> <span>RETAIL</span>
                        </label>
                    </div>

                    <div id="category-tabs-wrapper" class="mb-4">
                        
                        {{-- 🟢 DYNAMIC WHOLESALE GRID --}}
                        <div id="wholesale-category-grid" class="category-slider flex overflow-x-auto space-x-3 pb-2">
                            @if(isset($formulas['wholesale']))
                                @foreach ($formulas['wholesale'] as $f)
                                    @php
                                        // 🟢 Handle Icon Path Logic
                                        $iconSrc = $f->icon_url ? asset(str_replace('storage/', '', $f->icon_url) ? 'storage/'.str_replace('storage/', '', $f->icon_url) : $f->icon_url) : asset('assets/images/default.png');
                                        if(str_starts_with($f->icon_url, 'http')) { $iconSrc = $f->icon_url; }
                                    @endphp
                                    <div class="category-tab flex-shrink-0 bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer w-28 h-28 lg:w-32 lg:h-32"
                                         data-category="{{ $f->title }}" data-rate-field="{{ $f->rate_key }}">
                                        {{-- Image Handling --}}
                                        <img src="{{ $iconSrc }}" class="w-10 h-10 object-contain mb-2" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        <span class="text-[10px] font-bold text-center uppercase leading-tight">{{ $f->title }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-400 p-2">No wholesale items configured.</p>
                            @endif
                        </div>

                        {{-- 🟢 DYNAMIC RETAIL GRID --}}
                        <div id="retail-category-grid" class="category-slider flex overflow-x-auto space-x-3 pb-2 hidden">
                            @if(isset($formulas['retail']))
                                @foreach ($formulas['retail'] as $f)
                                    @php
                                        // 🟢 Handle Icon Path Logic
                                        $iconSrc = $f->icon_url ? asset(str_replace('storage/', '', $f->icon_url) ? 'storage/'.str_replace('storage/', '', $f->icon_url) : $f->icon_url) : asset('assets/images/default.png');
                                        if(str_starts_with($f->icon_url, 'http')) { $iconSrc = $f->icon_url; }
                                    @endphp
                                    <div class="category-tab flex-shrink-0 bg-white border border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center cursor-pointer w-28 h-28 lg:w-32 lg:h-32"
                                         data-category="{{ $f->title }}" data-rate-field="{{ $f->rate_key }}">
                                        <img src="{{ $iconSrc }}" class="w-10 h-10 object-contain mb-2" onerror="this.src='{{ asset('assets/images/placeholder.png') }}'">
                                        <span class="text-[10px] font-bold text-center uppercase leading-tight">{{ $f->title }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-400 p-2">No retail items configured.</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Weight (KG)</label>
                            <input id="weight-input" type="number" step="0.001" value="0" class="w-full border-2 rounded-lg px-3 py-2 text-xl font-bold outline-none focus:border-blue-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Rate (PKR)</label>
                            <input id="rate-input" type="number" step="0.01" value="0.00" class="w-full border-2 rounded-lg px-3 py-2 text-xl font-bold outline-none focus:border-blue-500">
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-4 p-2 bg-red-50 rounded-lg">
                        <span class="text-sm font-bold text-red-700">Line Total:</span>
                        <span id="line-total-display" class="text-xl font-black text-red-600">0.00 PKR</span>
                    </div>

                    <button type="button" id="add-item-btn" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 font-bold mb-4 disabled:opacity-50" disabled>+ ADD TO BILL</button>

                    <div id="cart-items-container" class="space-y-2 max-h-40 overflow-y-auto mb-4 border-t pt-4 text-sm">
                        <div class="text-gray-400 text-center py-2">No items added</div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-black text-gray-700">Final Bill:</span>
                            <span id="total-payable-display" class="text-2xl font-black text-blue-700">0.00 PKR</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="text-[10px] font-bold text-gray-500">CASH RECEIVED</label>
                                <input type="number" name="cash_received" id="cash-received-input" value="0" class="w-full border rounded-lg px-2 py-1.5 text-lg font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-blue-500">EXTRA CHARGES (+)</label>
                                <input type="number" name="extra_charges" id="charges-input" value="0" class="w-full border rounded-lg px-2 py-1.5 text-lg font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-red-500">DISCOUNT (-)</label>
                                <input type="number" name="discount" id="discount-input" value="0" class="w-full border rounded-lg px-2 py-1.5 text-lg font-bold">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="text-[10px] font-bold text-gray-500">NOTE / REMARKS</label>
                            <input type="text" name="note" id="note-input" placeholder="Optional notes..." class="w-full border rounded-lg px-3 py-1.5 text-sm">
                        </div>

                        <div class="flex justify-between items-center mt-4 pt-2 border-t">
                            <span class="text-xs font-bold text-gray-500 uppercase">Net Balance Change:</span>
                            <span id="net-balance-change" class="text-lg font-black text-gray-700">0.00</span>
                        </div>
                    </div>

                    <div class="flex gap-4 mt-6">
                        <button type="button" onclick="location.reload()" class="flex-1 bg-gray-200 py-3 rounded-lg font-bold">RESET</button>
                        <button type="submit" id="confirm-sale-btn" class="flex-[2] bg-green-600 text-white py-3 rounded-lg font-black shadow-lg disabled:opacity-50" disabled>CONFIRM SALE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ðŸŸ¢ MODAL: VIEW RATES & FORMULAS (Updated Dynamic Design) --}}
    <div id="ratesModal" class="fixed inset-0 z-[60] hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-sm" onclick="closeRatesModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl border border-gray-100 overflow-hidden transform transition-all">
                
                {{-- Header --}}
                <div class="px-6 py-5 border-b bg-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                            <i class="fas fa-chart-line text-blue-600"></i> 
                            Today's Rate List
                        </h3>
                        <p class="text-sm text-slate-500 font-medium">Live prices based on configured formulas</p>
                    </div>
                    <button onclick="closeRatesModal()" class="bg-white border text-slate-400 hover:text-slate-600 hover:bg-slate-100 h-10 w-10 rounded-full flex items-center justify-center transition-all shadow-sm text-xl">&times;</button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 bg-slate-50/50 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Wholesale Channel Card --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 overflow-hidden">
                            <div class="bg-blue-600 px-5 py-3 flex justify-between items-center">
                                <h4 class="text-white font-bold uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-truck-loading"></i> Wholesale
                                </h4>
                                <span class="bg-blue-400/30 text-white text-[10px] px-2 py-1 rounded-full font-bold border border-blue-300">Channel A</span>
                            </div>
                            <div class="p-4">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-slate-400 text-[11px] uppercase border-b">
                                            <th class="py-2 text-left font-bold">Category</th>
                                            <th class="py-2 text-right font-bold">Calculated Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @if(isset($formulas['wholesale']))
                                            @foreach($formulas['wholesale'] as $f)
                                                <tr class="hover:bg-slate-50 transition-colors">
                                                    <td class="py-3 font-semibold text-slate-700">{{ $f->title }}</td>
                                                    <td class="py-3 text-right">
                                                        <span class="text-blue-700 font-black text-lg">
                                                            {{ number_format($rates['wholesale'][$f->rate_key] ?? 0, 2) }}
                                                        </span>
                                                        <span class="text-[10px] text-slate-400 font-bold ml-1">PKR</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Retail Channel Card --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-green-100 overflow-hidden">
                            <div class="bg-green-600 px-5 py-3 flex justify-between items-center">
                                <h4 class="text-white font-bold uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-store"></i> Shop Retail
                                </h4>
                                <span class="bg-green-400/30 text-white text-[10px] px-2 py-1 rounded-full font-bold border border-green-300">Channel B</span>
                            </div>
                            <div class="p-4">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-slate-400 text-[11px] uppercase border-b">
                                            <th class="py-2 text-left font-bold">Category</th>
                                            <th class="py-2 text-right font-bold">Calculated Price</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @if(isset($formulas['retail']))
                                            @foreach($formulas['retail'] as $f)
                                                <tr class="hover:bg-slate-50 transition-colors">
                                                    <td class="py-3 font-semibold text-slate-700">{{ $f->title }}</td>
                                                    <td class="py-3 text-right">
                                                        <span class="text-green-700 font-black text-lg">
                                                            {{ number_format($rates['retail'][$f->rate_key] ?? 0, 2) }}
                                                        </span>
                                                        <span class="text-[10px] text-slate-400 font-bold ml-1">PKR</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="bg-white px-6 py-4 border-t flex justify-center">
                    <button onclick="closeRatesModal()" class="bg-slate-800 hover:bg-slate-900 text-white px-10 py-2.5 rounded-xl font-bold transition-all shadow-lg hover:scale-105 active:scale-95">
                        Close View
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Contact Modal (Kept same as before) --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 backdrop-blur-sm" onclick="closeModal()"></div>
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative z-10 bg-white rounded-xl shadow-2xl w-full max-w-md border">
                <div class="px-6 py-4 border-b flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Add New Customer</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                
                    <form id="contactForm">
                        @csrf
                        
                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border outline-none focus:ring-2 focus:ring-slate-200">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Category</label>
                                <div class="relative">
                                    <select name="type" id="customerSubtypeSelect"
                                        class="w-full appearance-none px-4 py-2.5 rounded-lg border border-gray-300 bg-white outline-none focus:ring-2 focus:ring-slate-200">
                                        <option value="customer">Permanent Customer</option>
                                        <option value="broker">Whole Sale Live</option>
                                        <option value="shop_retail">Shop Retail</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                                <input type="number" name="opening_balance" value="0" class="w-full px-4 py-2.5 rounded-lg border outline-none focus:ring-2 focus:ring-slate-200">
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                            <button type="submit" id="saveContactBtn" class="bg-slate-800 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-slate-700 transition">Save</button>
                            <button type="button" onclick="closeModal()" class="bg-white border px-6 py-2.5 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let ACTIVE_RATES = @json($rates);
        let selectedCustomerId = null;
        let selectedCategory = null;
        let selectedRateField = null;
        let cartItems = [];

        const weightInput = document.getElementById('weight-input');
        const rateInput = document.getElementById('rate-input');
        const cashReceivedInput = document.getElementById('cash-received-input');
        const chargesInput = document.getElementById('charges-input');
        const discountInput = document.getElementById('discount-input');
        const addItemBtn = document.getElementById('add-item-btn');
        const confirmSaleBtn = document.getElementById('confirm-sale-btn');
        const cartContainer = document.getElementById('cart-items-container');

        const formatCurrency = (val) => parseFloat(val).toLocaleString('en-PK', { minimumFractionDigits: 2 }) + ' PKR';

        const updateRateInput = () => {
            const channel = document.querySelector('input[name="rate_channel"]:checked').value;
            if (!selectedRateField) return;
            // 🟢 Handle Dynamic Key Lookups
            rateInput.value = parseFloat(ACTIVE_RATES[channel][selectedRateField] || 0).toFixed(2);
            calculateLineTotal();
        };

        const calculateLineTotal = () => {
            const total = (parseFloat(weightInput.value) || 0) * (parseFloat(rateInput.value) || 0);
            document.getElementById('line-total-display').textContent = formatCurrency(total);
            addItemBtn.disabled = !(selectedCustomerId && selectedCategory && parseFloat(weightInput.value) > 0);
        };

        const calculateFinalTotals = () => {
            let cartTotal = cartItems.reduce((acc, item) => acc + (item.weight * item.rate), 0);
            const charges = parseFloat(chargesInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const received = parseFloat(cashReceivedInput.value) || 0;

            const finalBill = (cartTotal + charges) - discount;
            const netChange = finalBill - received;

            document.getElementById('total-payable-display').textContent = formatCurrency(finalBill);
            document.getElementById('final-total-payable').value = finalBill.toFixed(2);
            
            const netEl = document.getElementById('net-balance-change');
            netEl.textContent = formatCurrency(netChange);
            netEl.className = netChange > 0 ? 'text-lg font-black text-red-600' : (netChange < 0 ? 'text-lg font-black text-green-600' : 'text-lg font-black text-gray-600');
            
            confirmSaleBtn.disabled = !(selectedCustomerId && cartItems.length > 0);
        };

        document.querySelectorAll('input[name="rate_channel"]').forEach(r => r.addEventListener('change', () => {
            document.getElementById('wholesale-category-grid').classList.toggle('hidden');
            document.getElementById('retail-category-grid').classList.toggle('hidden');
            // Auto select first item in new channel
            const visibleGrid = document.querySelector(r.value === 'wholesale' ? '#wholesale-category-grid' : '#retail-category-grid');
            const firstTab = visibleGrid.querySelector('.category-tab');
            if(firstTab) firstTab.click();
            updateRateInput();
        }));

        document.querySelectorAll('.customer-item').forEach(item => item.addEventListener('click', function () {
            document.querySelectorAll('.customer-item').forEach(i => i.classList.remove('bg-yellow-200', 'border-yellow-400'));
            this.classList.add('bg-yellow-200', 'border-yellow-400');
            selectedCustomerId = this.dataset.id;
            document.getElementById('selected-customer-id').value = selectedCustomerId;
            document.getElementById('current-customer-name').textContent = this.dataset.name;
            document.getElementById('current-customer-balance').textContent = formatCurrency(this.dataset.balance);
            calculateFinalTotals();
        }));

        document.addEventListener('click', function(e) {
            if (e.target.closest('.category-tab')) {
                const tab = e.target.closest('.category-tab');
                document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                selectedCategory = tab.dataset.category;
                selectedRateField = tab.dataset.rateField;
                updateRateInput();
            }
        });

        [weightInput, rateInput, cashReceivedInput, chargesInput, discountInput].forEach(el => 
            el.addEventListener('input', () => { calculateLineTotal(); calculateFinalTotals(); }));

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
        });

        function updateCartDisplay() {
            cartContainer.innerHTML = cartItems.length ? '' : '<div class="text-gray-400 text-center py-2">No items added</div>';
            cartItems.forEach((item, idx) => {
                const div = document.createElement('div');
                div.className = 'flex justify-between border-b pb-2 items-center';
                div.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="window.removeItem(${idx})" class="text-red-400"><i class="fa-solid fa-times-circle"></i></button>
                        <div>
                            <p class="font-bold text-gray-700 uppercase text-[10px]">${item.category} (${item.channel})</p>
                            <p class="text-[10px] text-gray-500">${item.weight.toFixed(3)}kg x ${item.rate.toFixed(2)}</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-700">${formatCurrency(item.weight * item.rate)}</span>
                `;
                cartContainer.appendChild(div);
            });
            calculateFinalTotals();
        }

        window.removeItem = (idx) => { cartItems.splice(idx, 1); updateCartDisplay(); };

        // FORM SUBMIT
        document.getElementById('sale-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (cartItems.length === 0) {
                Swal.fire('Error', 'Please add at least one item to the bill', 'error');
                return;
            }
            confirmSaleBtn.disabled = true;
            confirmSaleBtn.textContent = 'Processing...';

            const payload = {
                _token: document.querySelector('input[name="_token"]').value,
                customer_id: document.getElementById('selected-customer-id').value,
                total_payable: document.getElementById('final-total-payable').value,
                cash_received: document.getElementById('cash-received-input').value,
                extra_charges: document.getElementById('charges-input').value,
                discount: document.getElementById('discount-input').value,
                note: document.getElementById('note-input').value,
                rate_channel: document.querySelector('input[name="rate_channel"]:checked').value,
                cart_items: cartItems 
            };

            try {
                const res = await fetch("{{ route('admin.sales.store') }}", {
                    method: 'POST',
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': payload._token
                    },
                    body: JSON.stringify(payload) 
                });

                const data = await res.json();
                
                if (res.ok) {
                    Swal.fire('Success', data.message, 'success').then(() => location.reload());
                } else {
                    let errorMsg = data.message || 'Something went wrong';
                    if(data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('<br>');
                    }
                    Swal.fire('Error', errorMsg, 'error');
                    confirmSaleBtn.disabled = false;
                    confirmSaleBtn.textContent = 'CONFIRM SALE';
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Network error or Server side problem', 'error');
                confirmSaleBtn.disabled = false;
                confirmSaleBtn.textContent = 'CONFIRM SALE';
            }
        });

        document.getElementById('customer-search').addEventListener('input', function() {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('.customer-item').forEach(li => {
                li.style.display = li.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });

        // Initialize First Tab
        const firstTab = document.querySelector('#wholesale-category-grid .category-tab');
        if (firstTab) firstTab.click();
    });

    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btn = document.getElementById('saveContactBtn');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const formData = new FormData(this);

        try {
            // Send data to the SupplierCustomerController logic
            const response = await fetch("{{ route('admin.contacts.store') }}", {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Customer added successfully',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Optional: Dynamically add the new customer to the list without reload
                if(data.contact) {
                const list = document.getElementById('customer-list');
                // Create new list item logic here if needed, or just reload:
                setTimeout(() => location.reload(), 1000); 
                }
                
                closeModal();
                this.reset();
            } else {
                // Handle Validation Errors
                let msg = data.message || 'Error saving customer';
                if(data.errors) {
                    msg = Object.values(data.errors).flat().join('\n');
                }
                Swal.fire('Error', msg, 'error');
            }

        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Something went wrong on the server.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save';
        }
    });

    // Variable to store the currently active filter (default is 'all')
    let currentFilterType = 'all';

    function filterCustomers(type) {
        currentFilterType = type; // Update global filter state
        
        // Get current search text
        const searchInput = document.getElementById('customer-search');
        const filterText = searchInput ? searchInput.value.toLowerCase() : '';

        document.querySelectorAll('.customer-item').forEach(item => {
            const itemType = item.dataset.type; // e.g., 'customer', 'broker', 'shop_retail'
            const itemContent = item.textContent.toLowerCase();

            // Check Type Condition
            // If type is 'all', match everything. Otherwise match specific type.
            const matchesType = (currentFilterType === 'all') || (itemType === currentFilterType);

            // Check Search Condition
            const matchesSearch = itemContent.includes(filterText);

            // Show if BOTH match
            if (matchesType && matchesSearch) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Update the existing search listener to use this new function
    // (Make sure to remove the old listener if it conflicts, or just use this one)
    document.getElementById('customer-search').addEventListener('input', function() {
        filterCustomers(currentFilterType);
    });

    function openModal() { document.getElementById('contactModal').classList.remove('hidden'); }
    function closeModal() { document.getElementById('contactModal').classList.add('hidden'); }
    function openRatesModal() { document.getElementById('ratesModal').classList.remove('hidden'); }
    function closeRatesModal() { document.getElementById('ratesModal').classList.add('hidden'); }
    </script>
@endsection