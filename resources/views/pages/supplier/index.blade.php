@extends('layouts.main')

@section('content')
    <div class="w-full max-w-[100vw] min-h-screen overflow-x-hidden bg-gray-100 flex flex-col">

        <div class="flex-1 p-3 sm:p-6 lg:p-8">

            <div class="mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Supplier & Customer Management</h1>
            </div>

            <div id="statusMessage" class="mb-4 hidden p-3 rounded-lg text-sm font-medium" role="alert"></div>

            {{-- Grid: Stacks on mobile (grid-cols-1), 2 cols on Large screens --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-8 w-full">
                <div class="bg-gray-100 rounded-xl p-1 min-w-0">
                    <div class="flex flex-col gap-3 p-2 sm:p-4 mb-2">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-700">Suppliers (Trucks)</h2>
                            <button onclick="openAddModal('supplier')"
                                class="bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-medium py-2 px-3 sm:px-4 rounded-lg flex items-center transition-colors shadow-sm whitespace-nowrap">
                                <i class="fas fa-plus mr-2"></i> Add Supplier
                            </button>
                        </div>
                        <div class="relative w-full">
                            <input type="text" id="searchSupplier" placeholder="Search suppliers..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div id="supplierList" class="space-y-2 px-1 sm:px-2 pb-2">
                        @forelse($suppliers as $supplier)
                            <div id="supplier-{{ $supplier->id }}"
                                onclick="openLedger({{ $supplier->id }}, '{{ addslashes($supplier->name) }}', '{{ $supplier->phone ?? '' }}', 'supplier')"
                                class="supplier-item group bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow cursor-pointer">
                                <div class="min-w-0 pr-2"> {{-- min-w-0 allows truncate to work if needed --}}
                                    <h3 class="font-medium text-gray-800 supplier-name truncate">
                                        {{ $supplier->name }}
                                        <span class="text-xs text-gray-500 block font-normal">Click to view ledger</span>
                                    </h3>
                                </div>

                                <div class="relative inline-block text-left flex-shrink-0" onclick="event.stopPropagation()">
                                    <button onclick="toggleDropdown(this)" type="button"
                                        class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div
                                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                                        <div class="py-1" role="menu">
                                            <a href="#"
                                                onclick="openEditModal('{{ $supplier->id }}', '{{ addslashes($supplier->name) }}', '{{ $supplier->phone ?? '' }}', '{{ addslashes($supplier->address ?? '') }}', 'supplier')"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2 text-blue-500"></i> Edit
                                            </a>
                                            <a href="#" onclick="confirmDelete('{{ $supplier->id }}', 'supplier')"
                                                class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-trash-alt mr-2"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div id="noSuppliersPlaceholder"
                                class="bg-white p-6 sm:p-8 rounded-lg border border-dashed border-gray-300 text-center">
                                <p class="text-gray-400 text-sm">No suppliers added yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- 🟢 CUSTOMERS SECTION --}}
                <div class="bg-gray-100 rounded-xl p-1 min-w-0">
                    <div class="flex flex-col gap-3 p-2 sm:p-4 mb-2">
                        <div class="flex flex-col sm:flex-row justify-between items-center gap-2">
                            {{-- Click Title to Reset Filter --}}
                            <h2 class="text-lg font-semibold text-gray-700 cursor-pointer hover:text-blue-600" 
                                onclick="filterCustomers('all')" title="Show All">
                                Customers
                            </h2>
                            
                            <div class="flex flex-wrap gap-1 justify-end">
                                {{-- 🟢 Filter Buttons --}}
                                <button onclick="filterCustomers('customer')" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-[10px] sm:text-xs font-bold py-1.5 px-2.5 rounded shadow-sm transition-colors">
                                    Permanent
                                </button>
                                <button onclick="filterCustomers('broker')" 
                                    class="bg-orange-600 hover:bg-orange-700 text-white text-[10px] sm:text-xs font-bold py-1.5 px-2.5 rounded shadow-sm transition-colors">
                                    Live
                                </button>
                                <button onclick="filterCustomers('shop_retail')" 
                                    class="bg-green-600 hover:bg-green-700 text-white text-[10px] sm:text-xs font-bold py-1.5 px-2.5 rounded shadow-sm transition-colors">
                                    Shop
                                </button>
                                
                                {{-- Add Button --}}
                                <button onclick="openAddModal('customer')"
                                    class="bg-slate-800 hover:bg-slate-900 text-white text-[10px] sm:text-xs font-bold py-1.5 px-2.5 rounded flex items-center shadow-sm whitespace-nowrap ml-1 transition-colors">
                                    <i class="fas fa-plus mr-1"></i> New
                                </button>
                            </div>
                        </div>
                        <div class="relative w-full">
                            <input type="text" id="searchCustomer" placeholder="Search customers..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div id="customerList" class="space-y-2 px-1 sm:px-2 pb-2">
                        @forelse($customers as $customer)
                            <div id="customer-{{ $customer->id }}" data-type="{{ $customer->type ?? 'customer' }}"
                                onclick="openLedger({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ $customer->phone ?? '' }}', 'customer')"
                                class="customer-item group bg-white p-3 sm:p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow cursor-pointer">
                                <div class="min-w-0 pr-2">
                                    <h3 class="font-medium text-gray-800 customer-name truncate">
                                        {{ $customer->name }}
                                        <span class="text-xs text-gray-500 block font-normal">Bal:
                                            {{ number_format($customer->current_balance ?? 0) }}</span>
                                    </h3>
                                </div>

                                <div class="relative inline-block text-left flex-shrink-0" onclick="event.stopPropagation()">
                                    <button onclick="toggleDropdown(this)" type="button"
                                        class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                                        <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div
                                        class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                                        <div class="py-1" role="menu">
                                            <a href="#"
                                                onclick="openEditModal('{{ $customer->id }}', '{{ addslashes($customer->name) }}', '{{ $customer->phone ?? '' }}', '{{ addslashes($customer->address ?? '') }}', '{{ addslashes($customer->type ?? '') }}')"
                                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit mr-2 text-blue-500"></i> Edit
                                            </a>
                                            <a href="#" onclick="confirmDelete('{{ $customer->id }}', 'customer')"
                                                class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                <i class="fas fa-trash-alt mr-2"></i> Delete
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div id="noCustomersPlaceholder"
                                class="bg-white p-6 sm:p-8 rounded-lg border border-dashed border-gray-300 text-center">
                                <p class="text-gray-400 text-sm">No customers added yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- 🟢 ADD/EDIT CONTACT MODAL --}}
    <div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()">
        </div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-sm sm:max-w-md border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Contact</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times"></i></button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" id="editContactId" name="id" value="">
                    <input type="hidden" name="type" id="finalType">

                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none">
                            <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>

                        {{-- 🟢 Customer Sub-types --}}
                        <div id="customerTypeContainer" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Category</label>
                            <div class="relative">
                                <select id="customerSubtypeSelect" name="type"
                                    class="w-full appearance-none px-4 py-2.5 rounded-lg border border-gray-300 bg-white">
                                    <option value="customer">Permanent Customer</option>
                                    <option value="broker">Whole Sale Live</option>
                                    <option value="shop_retail">Shop Retail</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none">
                        </div>

                        <div id="openingBalanceDiv">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" id="openingBalance" value="0"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="address" rows="2"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none resize-none"></textarea>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-2 sm:gap-0">
                        <button type="submit" id="saveContactBtn"
                            class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 transition-colors">Save
                            Contact</button>
                        <button type="button" onclick="closeModal()"
                            class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mr-3 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- 🟢 UNIFIED LEDGER MODAL (Fully Responsive) --}}
    <div id="ledgerModal" class="fixed inset-0 z-50 hidden" style="z-index: 60;">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeLedger()">
        </div>

        <div class="flex min-h-full items-end sm:items-center justify-center p-0 sm:p-4">
            {{-- h-[90vh] ensures modal height is contained on mobile --}}
            <div
                class="relative w-full max-w-7xl bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl flex flex-col h-[95vh] sm:h-[90vh]">

                {{-- Header --}}
                <div
                    class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 rounded-t-2xl gap-4">
                    <div class="flex-1 w-full flex justify-between sm:block">
                        <div>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-800" id="ledgerTitle">Contact Name</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Balance: <span id="ledgerCurrentBalance"
                                    class="font-bold text-blue-600 text-lg sm:text-xl">0</span> PKR
                            </p>
                        </div>

                        {{-- Close button moved here on mobile for easier access --}}
                        <button onclick="closeLedger()"
                            class="sm:hidden text-gray-400 hover:text-gray-600 bg-white p-2 rounded-full shadow border border-gray-100 h-10 w-10">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    {{-- Action Buttons --}}
                    <div id="customerActions" class="hidden flex flex-wrap gap-2 sm:gap-3 w-full sm:w-auto">

                        <button onclick="sendWhatsAppReminder()"
                            class="flex-1 sm:flex-none justify-center bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center shadow-sm transition-colors whitespace-nowrap">
                            <i class="fab fa-whatsapp text-lg mr-2"></i> Reminder
                        </button>
                        <button onclick="focusPaymentInput()"
                            class="flex-1 sm:flex-none justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center shadow-sm transition-colors whitespace-nowrap">
                            <i class="fas fa-hand-holding-usd text-lg mr-2"></i> Receive
                        </button>
                    </div>

                    {{-- Desktop Close Button --}}
                    <button onclick="closeLedger()"
                        class="hidden sm:block text-gray-400 hover:text-gray-600 transition-colors bg-white p-2 rounded-full shadow-sm hover:shadow border border-gray-100 ml-2">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Body (Scrollable) --}}
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 bg-gray-50/50">

                    {{-- Payment Form --}}
                    <div id="paymentFormContainer"
                        class="bg-white p-4 sm:p-5 rounded-xl border border-blue-100 shadow-sm mb-6 sm:mb-8">
                        <h4 class="font-bold text-gray-800 text-md mb-4 flex items-center gap-2">
                            <i class="fas fa-coins text-yellow-500"></i> Add Manual Transaction
                        </h4>
                        <form id="addPaymentForm" class="flex flex-col lg:flex-row gap-3 sm:gap-4 items-end">
                            <input type="hidden" id="paymentContactId" name="contact_id">
                            <input type="hidden" id="paymentContactType" name="contact_type">
                            <input type="hidden" id="paymentContactPhone" value="">

                            <div class="w-full lg:w-1/5">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Date</label>
                                <input type="date" name="date"
                                    class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="w-full lg:w-1/5">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Amount (PKR)</label>
                                <input type="number" name="amount" id="paymentAmountInput" placeholder="0.00"
                                    class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-gray-800"
                                    required>
                            </div>

                            <div class="w-full lg:w-1/4">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Type</label>
                                <select name="type" id="paymentTypeSelect"
                                    class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                    {{-- Options populated via JS --}}
                                </select>
                            </div>

                            <div class="w-full lg:w-1/4">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Description</label>
                                <input type="text" name="description" placeholder="Optional notes..."
                                    class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div class="w-full lg:w-auto">
                                <button type="submit"
                                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-md active:scale-95 flex items-center justify-center whitespace-nowrap">
                                    <i class="fas fa-plus mr-2"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="flex items-center justify-end mb-4 gap-3">
                        <button onclick="exportLedgerToExcel()"
                            class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                            <i class="fas fa-file-excel text-sm"></i>
                            Excel
                        </button>

                        <button onclick="exportLedgerToPDF()"
                            class="flex items-center gap-2 bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                            <i class="fas fa-file-pdf text-sm"></i>
                            PDF
                        </button>
                    </div>

                    {{-- Ledger Table --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">

                        <div
                            class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-700 text-lg">Transaction History</h4>
                        </div>

                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        {{-- whitespace-nowrap keeps headers on one line --}}
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[15%]">
                                            Date</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[35%]">
                                            Description</th>
                                        <th id="thDebit"
                                            class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[15%]">
                                            Debit</th>
                                        <th id="thCredit"
                                            class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[15%]">
                                            Credit</th>
                                        <th
                                            class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[20%]">
                                            Balance</th>
                                    </tr>
                                </thead>
                                <tbody id="ledgerTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">Loading records...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS (Unchanged logic, just ensure they are included) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const STORE_URL = "{{ route('admin.contacts.store') }}";

        // --- SEARCH LOGIC ---
        document.getElementById('searchSupplier').addEventListener('keyup', function () {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('.supplier-item').forEach(function (item) {
                let name = item.querySelector('.supplier-name').textContent.toLowerCase();
                item.style.display = name.includes(filter) ? "" : "none";
            });
        });

        document.getElementById('searchCustomer').addEventListener('keyup', function () {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('.customer-item').forEach(function (item) {
                let name = item.querySelector('.customer-name').textContent.toLowerCase();
                item.style.display = name.includes(filter) ? "" : "none";
            });
        });

        // --- ADD / EDIT CONTACT MODAL LOGIC ---
        function openAddModal(type) {
            const modal = document.getElementById('contactModal');
            const finalTypeInput = document.getElementById('finalType'); // Hidden field jo backend ko data bhejti hai
            const customerTypeContainer = document.getElementById('customerTypeContainer');
            const customerSubtypeSelect = document.getElementById('customerSubtypeSelect');
            const form = document.getElementById('contactForm');

            form.reset();
            document.getElementById('editContactId').value = '';
            modal.classList.remove('hidden');

            if (type === 'supplier') {
                document.getElementById('modalTitle').textContent = 'Add New Supplier';
                finalTypeInput.value = 'supplier'; // Supplier ke liye seedha value set karein
                customerTypeContainer.classList.add('hidden');
            } else {
                document.getElementById('modalTitle').textContent = 'Add New Customer';
                customerTypeContainer.classList.remove('hidden');

                // Default subtype set karein
                finalTypeInput.value = customerSubtypeSelect.value;

                // Jab user dropdown change kare to hidden field update ho
                customerSubtypeSelect.onchange = function () {
                    finalTypeInput.value = this.value;
                };
            }
        }

        function openEditModal(id, name, phone, address, type) {
        const modal = document.getElementById('contactModal');
        const finalTypeInput = document.getElementById('finalType');
        const customerTypeContainer = document.getElementById('customerTypeContainer');
        const customerSubtypeSelect = document.getElementById('customerSubtypeSelect');
        const balanceDiv = document.getElementById('openingBalanceDiv'); 

        document.getElementById('editContactId').value = id;
        document.getElementById('contactName').value = name;
        document.querySelector('input[name="phone"]').value = phone;
        document.querySelector('textarea[name="address"]').value = address;

        // Hide opening balance during edit
        if (balanceDiv) balanceDiv.classList.add('hidden');

        // ✅ FIX 1: Set the hidden input value immediately
        finalTypeInput.value = type;

        if (type === 'supplier') {
            customerTypeContainer.classList.add('hidden');
        } else {
            customerTypeContainer.classList.remove('hidden');
            // ✅ FIX 2: Set the dropdown to match the incoming type
            customerSubtypeSelect.value = type;
        }

        document.getElementById('modalTitle').textContent = 'Edit Contact';
        modal.classList.remove('hidden');
    }

        function closeModal() {
            document.getElementById('contactModal').classList.add('hidden');
        }

        function toggleDropdown(button) {
            event.stopPropagation();
            const dropdown = button.nextElementSibling;
            document.querySelectorAll('.relative > div.origin-top-right').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        window.addEventListener('click', function (e) {
            if (!e.target.closest('button')) {
                document.querySelectorAll('.relative > div.origin-top-right').forEach(d => d.classList.add('hidden'));
            }
        });

        // --- FORM SUBMISSION ---
        document.getElementById('contactForm').addEventListener('submit', async function (e) {
            e.preventDefault(); // Stop the page from reloading

            const submitBtn = document.getElementById('saveContactBtn');
            const nameError = document.getElementById('nameError');
            const editId = document.getElementById('editContactId').value;
            const formData = new FormData(this);

            // Reset UI
            nameError.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';

            // Determine URL (Store vs Update)
            let url = "/admin/contacts"; // Make sure this matches your route
            if (editId) {
                url = `/admin/contacts/${editId}`;
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    // Success! 
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message,
                        timer: 1500
                    });
                    setTimeout(() => location.reload(), 1500); // Reload to show new customer
                } else {
                    // Validation Errors
                    if (response.status === 422) {
                        if (result.errors.name) {
                            nameError.textContent = result.errors.name[0];
                            nameError.classList.remove('hidden');
                        }
                        // Log other errors to console for debugging
                        console.error("Validation Errors:", result.errors);
                    } else {
                        Swal.fire('Error', result.message || 'Something went wrong', 'error');
                    }
                }
            } catch (error) {
                console.error("Fetch Error:", error);
                Swal.fire('Error', 'Server connection failed', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Contact';
            }
        });
        function confirmDelete(id, type) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) deleteContact(id, type);
            });
        }

        async function deleteContact(id, type) {
            try {
                const response = await fetch(`/admin/contacts/${id}`, {
                    method: 'POST', // We use POST with _method spoofing
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        type: type,
                        _method: 'DELETE' // Laravel Method Spoofing
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    document.getElementById(`${type}-${id}`).remove();
                    Swal.fire('Deleted!', data.message, 'success');
                } else {
                    // This will show "Cannot delete: This contact has transaction history"
                    Swal.fire('Error', data.message || 'Delete failed', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Network error.', 'error');
            }
        }

        // ==========================================
        // 🟢 LEDGER LOGIC 
        // ==========================================

        const ledgerModal = document.getElementById('ledgerModal');
        const ledgerTableBody = document.getElementById('ledgerTableBody');
        const ledgerTitleEl = document.getElementById('ledgerTitle');
        const ledgerBalanceEl = document.getElementById('ledgerCurrentBalance');
        const paymentContactIdEl = document.getElementById('paymentContactId');
        const paymentContactTypeEl = document.getElementById('paymentContactType');
        const paymentContactPhoneEl = document.getElementById('paymentContactPhone');
        const paymentTypeSelect = document.getElementById('paymentTypeSelect');
        const customerActionsEl = document.getElementById('customerActions');

        /**
        * 🟢 EXPORT LEDGER TO EXCEL
        */
        /**
         * 🟢 IMPROVED EXCEL EXPORT (with Styles & Formatting)
         */
        async function exportLedgerToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Ledger');
            const name = document.getElementById('ledgerTitle').textContent;
            const balance = document.getElementById('ledgerCurrentBalance').textContent;

            // 1. Add Header Information
            worksheet.mergeCells('A1:E1');
            const titleCell = worksheet.getCell('A1');
            titleCell.value = `Ledger Report: ${name}`;
            titleCell.font = { name: 'Arial Black', size: 14, color: { argb: 'FFFFFFFF' } };
            titleCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF4F46E5' } }; // Indigo
            titleCell.alignment = { vertical: 'middle', horizontal: 'center' };

            worksheet.mergeCells('A2:E2');
            worksheet.getCell('A2').value = `Generated on: ${new Date().toLocaleString()} | Closing Balance: PKR ${balance}`;
            worksheet.getCell('A2').font = { italic: true, size: 10 };

            // 2. Define Columns & Headers
            const table = document.querySelector('#ledgerModal table');
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText).slice(0, -1); // Action column chhor kar

            const headerRow = worksheet.addRow(headers);
            headerRow.eachCell((cell) => {
                cell.font = { bold: true, color: { argb: 'FFFFFFFF' } };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF1F2937' } }; // Dark Gray
                cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };
            });

            // 3. Add Data Rows
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(tr => {
                const rowData = Array.from(tr.querySelectorAll('td')).map(td => td.innerText).slice(0, -1);
                const addedRow = worksheet.addRow(rowData);

                // Conditional formatting for Amounts
                addedRow.eachCell((cell, colNumber) => {
                    cell.border = { top: { style: 'thin' }, left: { style: 'thin' }, bottom: { style: 'thin' }, right: { style: 'thin' } };

                    // Debit (Paid/Sale) formatting
                    if (headers[colNumber - 1].includes('Debit') && cell.value !== '-') {
                        cell.font = { color: { argb: 'FFDC2626' }, bold: true }; // Red
                    }
                    // Credit (Bill/Recv) formatting
                    if (headers[colNumber - 1].includes('Credit') && cell.value !== '-') {
                        cell.font = { color: { argb: 'FF059669' }, bold: true }; // Green
                    }
                });
            });

            // 4. Final Formatting (Auto-width)
            worksheet.columns.forEach(column => {
                column.width = 20;
                column.alignment = { vertical: 'middle', horizontal: 'left' };
            });

            // 5. Download File
            const buffer = await workbook.xlsx.writeBuffer();
            saveAs(new Blob([buffer]), `${name}_Ledger_${new Date().toISOString().slice(0, 10)}.xlsx`);
        }
        /**
         * 🟢 EXPORT LEDGER TO PDF
         */
        function exportLedgerToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = jsPDF({ orientation: 'landscape' }); // Landscape for more columns
            const name = document.getElementById('ledgerTitle').textContent;
            const balance = document.getElementById('ledgerCurrentBalance').textContent;

            // Header Info
            doc.setFontSize(18);
            doc.text("Ledger Report", 14, 15);
            doc.setFontSize(11);
            doc.text(`Contact: ${name}`, 14, 22);
            doc.text(`Closing Balance: PKR ${balance}`, 14, 28);
            doc.text(`Generated on: ${new Date().toLocaleString()}`, 14, 34);

            // AutoTable Logic
            doc.autoTable({
                html: '#ledgerModal table',
                startY: 40,
                theme: 'striped',
                headStyles: { fillColor: [79, 70, 229] }, // Indigo color for header
                styles: { fontSize: 8 },
                columnStyles: {
                    0: { cellWidth: 25 }, // Date
                    1: { cellWidth: 'auto' } // Description
                },
                // We exclude the 'Action' column from the export
                didParseCell: function (data) {
                    if (data.column.index === (data.table.columns.length - 1)) {
                        data.cell.text = ""; // Hide text in Action column
                    }
                }
            });

            doc.save(`${name}_Ledger.pdf`);
        }
        /**
    * 🟢 FIXED: Open Ledger Function
    */
        function openLedger(id, name, phone, type) {
            // Elements ko function ke andar fetch karein taake 'null' error na aaye
            const tableBody = document.getElementById('ledgerTableBody');
            const balanceDisplay = document.getElementById('ledgerCurrentBalance');
            const titleDisplay = document.getElementById('ledgerTitle');
            const modal = document.getElementById('ledgerModal');

            // Check karein ke elements mil gaye hain ya nahi
            if (!tableBody || !modal) {
                console.error("Critical UI elements missing!");
                return;
            }

            // 1. Reset State
            tableBody.innerHTML = '<tr><td colspan="11" class="px-6 py-10 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-2xl mb-2"></i><br>Loading fresh records...</td></tr>';
            if (balanceDisplay) balanceDisplay.textContent = "0";
            if (titleDisplay) titleDisplay.textContent = name;

            // 2. Show Modal
            modal.classList.remove('hidden');

            // 3. Update Hidden Inputs
            document.getElementById('paymentContactId').value = id;
            document.getElementById('paymentContactType').value = type;
            document.getElementById('paymentContactPhone').value = phone;

            // 4. Update Table Headers based on Type
            const thDebit = document.getElementById('thDebit');
            const thCredit = document.getElementById('thCredit');
            const actions = document.getElementById('customerActions');
            const typeSelect = document.getElementById('paymentTypeSelect');

            if (type === 'supplier') {
                if (actions) actions.classList.add('hidden');
                if (thDebit) thDebit.innerHTML = "Debit <span class='text-[10px] lowercase font-normal'>(Paid)</span>";
                if (thCredit) thCredit.innerHTML = "Credit <span class='text-[10px] lowercase font-normal'>(Purchase)</span>";
                typeSelect.innerHTML = `<option value="payment">Payment (Cash Given to Supplier)</option><option value="opening_balance">Opening Balance Adjustment</option>`;
            } else {
                if (actions) actions.classList.remove('hidden');
                if (thDebit) thDebit.innerHTML = "Debit <span class='text-[10px] lowercase font-normal'>(Sale/Due)</span>";
                if (thCredit) thCredit.innerHTML = "Credit <span class='text-[10px] lowercase font-normal'>(Received)</span>";
                typeSelect.innerHTML = `<option value="payment">Payment (Cash Received from Customer)</option><option value="opening_balance">Opening Balance Adjustment</option>`;
            }

            // 5. Data Fetch karein
            fetchLedgerData(id, type);
        }

        function sendWhatsAppReminder() {
            const phone = paymentContactPhoneEl.value;
            const name = ledgerTitleEl.textContent;
            const balance = ledgerBalanceEl.textContent;

            if (!phone) {
                Swal.fire('Info', 'No phone number saved for this customer.', 'info');
                return;
            }
            const cleanPhone = phone.replace(/[^0-9]/g, '');
            const message =
                `Hello ${name}, this is a gentle reminder that your current outstanding balance is PKR ${balance}. Please clear it at your earliest convenience. Thank you.`;
            const url = `https://wa.me/${cleanPhone}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }

        function focusPaymentInput() {
            const formContainer = document.getElementById('paymentFormContainer');
            formContainer.scrollIntoView({
                behavior: 'smooth'
            });
            const typeSelect = document.getElementById('paymentTypeSelect');
            typeSelect.value = 'payment';
            const amountInput = document.getElementById('paymentAmountInput');
            amountInput.focus();
            amountInput.classList.add('ring-4', 'ring-green-200');
            setTimeout(() => {
                amountInput.classList.remove('ring-4', 'ring-green-200');
            }, 1000);
        }

        async function fetchLedgerData(id, type) {
            // 🟢 RE-SELECT ELEMENTS to prevent "Cannot set properties of null" errors
            const tableBody = document.getElementById('ledgerTableBody');
            const balanceDisplay = document.getElementById('ledgerCurrentBalance');
            const thead = document.querySelector('#ledgerModal thead');
            const scrollContainer = document.querySelector('#ledgerModal .overflow-y-auto');

            // Safety check: if elements are missing, don't proceed
            if (!tableBody || !thead) return;

            try {
                const endpoint = (type === 'supplier') ? 'suppliers' : 'customers';
                const url = `/admin/${endpoint}/${id}/ledger`;

                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (!response.ok) throw new Error("Failed to fetch data from server");

                const data = await response.json();

                // Modal balance update
                if (balanceDisplay) {
                    balanceDisplay.textContent = parseFloat(data.current_balance || 0).toLocaleString();
                }

                // 1. Dynamic Headers Logic based on Contact Type
                if (type === 'supplier') {
                    thead.innerHTML = `
                                    <tr class="bg-gray-100 text-[10px] font-bold uppercase text-gray-600">
                                        <th class="px-2 py-3">Date</th>
                                        <th class="px-2 py-3 text-left">Description</th>
                                        <th class="px-2 py-3 text-right">Gross (wht)</th>
                                        <th class="px-2 py-3 text-right text-red-500">Ded (wht)</th>
                                        <th class="px-2 py-3 text-right">Net (wht)</th>
                                        <th class="px-2 py-3 text-right text-blue-600">Kharch</th>
                                        <th class="px-2 py-3 text-right">Rate</th>
                                        <th class="px-2 py-3 text-right text-red-600">Debit (Paid)</th>
                                        <th class="px-2 py-3 text-right text-green-600">Credit (Bill)</th>
                                        <th class="px-2 py-3 text-right">Balance</th>
                                        <th class="px-2 py-3 text-center">Action</th>
                                    </tr>`;
                } else {
                    thead.innerHTML = `
                                    <tr class="bg-gray-100 text-xs font-bold uppercase text-gray-600">
                                        <th class="px-4 py-3 text-left">Date</th>
                                        <th class="px-4 py-3 text-left">Description</th>
                                        <th class="px-4 py-3 text-right text-red-600">Debit (Sale)</th>
                                        <th class="px-4 py-3 text-right text-green-600">Credit (Recv)</th>
                                        <th class="px-4 py-3 text-right">Balance</th>
                                        <th class="px-4 py-3 text-center">Action</th>
                                    </tr>`;
                }

                if (!data.transactions || data.transactions.length === 0) {
                    tableBody.innerHTML = `<tr><td colspan="11" class="px-6 py-10 text-center text-gray-400 font-medium italic">No transaction history found.</td></tr>`;
                    return;
                }

                let html = '';
                let runningBalance = 0;

                data.transactions.forEach(txn => {
                    let debitVal = parseFloat(txn.debit) || 0;
                    let creditVal = parseFloat(txn.credit) || 0;

                    // Running Balance Logic
                    if (type === 'supplier') {
                        runningBalance += (creditVal - debitVal);
                    } else {
                        runningBalance += (debitVal - creditVal);
                    }

                    let descriptionText = txn.description || '-';
                    const rowId = txn.group_key || txn.id;

                    if (type === 'supplier') {
                        // Supplier Formatting
                        let gross = txn.gross_weight || '-';
                        let net = txn.net_live_weight || '-';
                        let rate = txn.buying_rate || '-';
                        let kharch = txn.total_kharch ? parseFloat(txn.total_kharch).toLocaleString() : '-';
                        let ded = (parseFloat(txn.dead_weight || 0) + parseFloat(txn.shrink_loss || 0)).toFixed(net !== '-' ? 2 : 0);

                        html += `
                                    <tr id="ledger-row-${rowId}" class="border-b hover:bg-gray-50 text-[11px] transition-all">
                                        <td class="px-2 py-3 whitespace-nowrap row-date text-gray-500 font-medium">${txn.date}</td>
                                        <td class="px-2 py-3 row-desc font-bold text-gray-700"><span>${descriptionText}</span></td>
                                        <td class="px-2 py-3 text-right text-gray-600">${gross}</td>
                                        <td class="px-2 py-3 text-right text-red-400">${ded == "0" || ded == "0.00" ? '-' : ded}</td>
                                        <td class="px-2 py-3 text-right font-bold text-green-700">${net}</td>
                                        <td class="px-2 py-3 text-right text-blue-600 font-medium">${kharch}</td>
                                        <td class="px-2 py-3 text-right text-gray-600">${rate}</td>
                                        <td class="px-2 py-3 text-right font-black text-red-600 row-debit bg-red-50/20">${debitVal > 0 ? debitVal.toLocaleString() : '-'}</td>
                                        <td class="px-2 py-3 text-right font-black text-green-600 row-credit bg-green-50/20">${creditVal > 0 ? creditVal.toLocaleString() : '-'}</td>
                                        <td class="px-2 py-3 text-right font-extrabold text-blue-800 bg-gray-50/50">${runningBalance.toLocaleString()}</td>
                                        <td class="px-2 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-1">
                                                <button onclick="toggleLedgerEdit('${rowId}', '${type}')" class="p-1.5 text-blue-500 hover:bg-blue-50 rounded"><i class="fas fa-edit"></i></button>
                                                <button onclick="deleteLedgerEntry('${rowId}', '${type}')" class="p-1.5 text-red-400 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>`;
                    } else {
                        // Customer Formatting
                        html += `
                                    <tr id="ledger-row-${rowId}" class="border-b hover:bg-gray-50 text-sm transition-all">
                                        <td class="px-4 py-3 whitespace-nowrap row-date text-gray-500 font-medium">${txn.date}</td>
                                        <td class="px-4 py-3 row-desc font-bold text-gray-700"><span>${descriptionText}</span></td>
                                        <td class="px-4 py-3 text-right font-black text-red-600 row-debit bg-red-50/20">${debitVal > 0 ? debitVal.toLocaleString() : '-'}</td>
                                        <td class="px-4 py-3 text-right font-black text-green-600 row-credit bg-green-50/20">${creditVal > 0 ? creditVal.toLocaleString() : '-'}</td>
                                        <td class="px-4 py-3 text-right font-extrabold text-blue-800 bg-gray-50/50">${runningBalance.toLocaleString()}</td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="toggleLedgerEdit('${rowId}', '${type}')" class="p-2 text-blue-500 hover:bg-blue-50 rounded"><i class="fas fa-edit"></i></button>
                                                <button onclick="deleteLedgerEntry('${rowId}', '${type}')" class="p-2 text-red-400 hover:bg-red-50 rounded"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>`;
                    }
                });

                tableBody.innerHTML = html;

                // Smooth Auto-scroll to latest entry
                if (scrollContainer) {
                    setTimeout(() => {
                        scrollContainer.scrollTo({ top: scrollContainer.scrollHeight, behavior: 'smooth' });
                    }, 100);
                }

            } catch (error) {
                console.error("Ledger Fetch Error:", error);
                if (tableBody) {
                    tableBody.innerHTML = `<tr><td colspan="11" class="px-6 py-10 text-center text-red-500 font-bold bg-red-50 rounded-lg">Error: ${error.message}</td></tr>`;
                }
            }
        }

        // 🟢 FIXED INLINE EDIT LOGIC
        function toggleLedgerEdit(txnId, contactType) {
            const row = document.getElementById(`ledger-row-${txnId}`);

            // Values extract karein aur format clean karein
            const date = row.querySelector('.row-date').textContent.trim();
            const desc = row.querySelector('.row-desc span').textContent.trim();
            const debit = row.querySelector('.row-debit').textContent.replace(/,/g, '').replace('-', '0').trim();
            const credit = row.querySelector('.row-credit').textContent.replace(/,/g, '').replace('-', '0').trim();

            // Row ko inputs mein tabdeel karein
            row.querySelector('.row-date').innerHTML = `<input type="date" id="ed-date-${txnId}" value="${date}" class="w-full text-xs p-1 border rounded focus:ring-1">`;
            row.querySelector('.row-desc').innerHTML = `<input type="text" id="ed-desc-${txnId}" value="${desc}" class="w-full text-xs p-1 border rounded focus:ring-1">`;
            row.querySelector('.row-debit').innerHTML = `<input type="number" step="any" id="ed-debit-${txnId}" value="${debit}" class="w-full p-1 border rounded text-right text-xs">`;
            row.querySelector('.row-credit').innerHTML = `<input type="number" step="any" id="ed-credit-${txnId}" value="${credit}" class="w-full p-1 border rounded text-right text-xs">`;

            // Buttons badlein
            row.querySelector('td:last-child').innerHTML = `
                                                        <div class="flex gap-2 justify-center">
                                                            <button onclick="saveLedgerEdit('${txnId}', '${contactType}')" class="text-green-600 hover:text-green-800"><i class="fas fa-check-circle text-xl"></i></button>
                                                            <button onclick="fetchLedgerData('${paymentContactIdEl.value}', '${contactType}')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times-circle text-xl"></i></button>
                                                        </div>`;
        }

        async function saveLedgerEdit(txnId, contactType) {
            const submitData = {
                date: document.getElementById(`ed-date-${txnId}`).value,
                description: document.getElementById(`ed-desc-${txnId}`).value,
                debit: document.getElementById(`ed-debit-${txnId}`).value,
                credit: document.getElementById(`ed-credit-${txnId}`).value,
                _token: document.querySelector('input[name="_token"]').value
            };

            try {
                const response = await fetch(`/admin/ledger/${txnId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': submitData._token
                    },
                    body: JSON.stringify(submitData)
                });

                const result = await response.json();
                if (response.ok && result.success) {
                    Swal.fire({ icon: 'success', title: 'Updated!', timer: 1000, showConfirmButton: false });
                    fetchLedgerData(paymentContactIdEl.value, contactType);
                } else {
                    Swal.fire('Error', result.message || 'Update failed', 'error');
                }
            } catch (error) {
                Swal.fire('Error', 'Network error', 'error');
            }
        }

        async function deleteLedgerEntry(txnId, contactType) {
            const result = await Swal.fire({
                title: 'Delete Transaction?',
                text: "Are you sure? This will update the contact balance.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(`/admin/ledger/${txnId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();
                    if (response.ok && data.success) {
                        Swal.fire('Deleted!', 'Transaction removed successfully.', 'success');
                        fetchLedgerData(paymentContactIdEl.value, contactType);
                    } else {
                        Swal.fire('Error', data.message || 'Could not delete.', 'error');
                    }
                } catch (error) {
                    Swal.fire('Error', 'Network connection error.', 'error');
                }
            }
        }

        document.getElementById('addPaymentForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const id = paymentContactIdEl.value;
            const type = paymentContactTypeEl.value;
            const phone = paymentContactPhoneEl.value;
            const formData = new FormData(this);

            if (type === 'supplier') {
                formData.append('supplier_id', id);
            } else {
                formData.append('customer_id', id);
            }

            const endpoint = (type === 'supplier') ? 'suppliers' : 'customers';
            const url = `/admin/${endpoint}/payment`;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'Transaction added successfully',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    this.reset();
                    paymentContactIdEl.value = id;
                    paymentContactTypeEl.value = type;
                    paymentContactPhoneEl.value = phone;
                    document.querySelector('input[name="date"]').value = new Date().toISOString().split('T')[0];
                    fetchLedgerData(id, type);
                } else {
                    Swal.fire('Error', result.message || 'Failed to save', 'error');
                }

            } catch (error) {
                Swal.fire('Error', 'Network Error', 'error');
            }
        });

        function closeLedger() {
            ledgerModal.classList.add('hidden');
            // Clear content on close to ensure next open starts fresh
            ledgerTableBody.innerHTML = '';
        }

        let currentFilterType = 'all';

    function filterCustomers(type) {
        currentFilterType = type; // Save selected filter
        
        // Get current search text to apply both filters together
        const searchText = document.getElementById('searchCustomer').value.toLowerCase();

        document.querySelectorAll('.customer-item').forEach(item => {
            // Get the type from the HTML attribute we added
            const itemType = item.getAttribute('data-type');
            const name = item.querySelector('.customer-name').textContent.toLowerCase();

            // 1. Check Type Match (or if 'all' is selected)
            const matchesType = (currentFilterType === 'all' || itemType === currentFilterType);

            // 2. Check Search Text Match
            const matchesSearch = name.includes(searchText);

            // Show only if BOTH match
            if (matchesType && matchesSearch) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Update the search listener to use the new function
    document.getElementById('searchCustomer').addEventListener('keyup', function () {
        filterCustomers(currentFilterType);
    });
    </script>
@endsection