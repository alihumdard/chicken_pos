@extends('layouts.main')

@section('content')
<div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Supplier & Customer Management</h1>
    </div>
    
    <div id="statusMessage" class="mb-4 hidden p-3 rounded-lg text-sm font-medium" role="alert"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- 🟢 SUPPLIERS SECTION --}}
        <div class="bg-gray-100 rounded-xl p-1">
            <div class="flex flex-col gap-3 p-4 mb-2">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-700">Suppliers (Trucks)</h2>
                    <button onclick="openAddModal('supplier')" 
                            class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Add Supplier
                    </button>
                </div>
                <div class="relative">
                    <input type="text" id="searchSupplier" placeholder="Search suppliers..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div id="supplierList" class="space-y-2 px-2 pb-2 max-h-[600px] overflow-y-auto">
                @forelse($suppliers as $supplier)
                <div id="supplier-{{ $supplier->id }}" 
                     onclick="openLedger({{ $supplier->id }}, '{{ $supplier->name }}', 'supplier')"
                     class="supplier-item group bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow cursor-pointer">
                    <div>
                        <h3 class="font-medium text-gray-800 supplier-name">
                            {{ $supplier->name }}
                            <span class="text-xs text-gray-500 block">Click to view ledger</span>
                        </h3>
                    </div>
                    
                    <div class="relative inline-block text-left" onclick="event.stopPropagation()"> 
                        <button onclick="toggleDropdown(this)" type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                            <div class="py-1" role="menu">
                                <a href="#" onclick="confirmDelete('{{ $supplier->id }}', 'supplier')" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-trash-alt mr-2"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div id="noSuppliersPlaceholder" class="bg-white p-8 rounded-lg border border-dashed border-gray-300 text-center">
                    <p class="text-gray-400 text-sm">No suppliers added yet.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- 🟢 CUSTOMERS SECTION --}}
        <div class="bg-gray-100 rounded-xl p-1">
            <div class="flex flex-col gap-3 p-4 mb-2">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-700">Permanent Customers</h2>
                    <button onclick="openAddModal('customer')" 
                            class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center transition-colors shadow-sm">
                        <i class="fas fa-plus mr-2"></i> Add Customer
                    </button>
                </div>
                <div class="relative">
                    <input type="text" id="searchCustomer" placeholder="Search customers..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all text-sm">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <div id="customerList" class="space-y-2 px-2 pb-2 max-h-[600px] overflow-y-auto">
                @forelse($customers as $customer)
                <div id="customer-{{ $customer->id }}" 
                     onclick="openLedger({{ $customer->id }}, '{{ $customer->name }}', 'customer')"
                     class="customer-item group bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow cursor-pointer">
                    <div>
                        <h3 class="font-medium text-gray-800 customer-name">
                            {{ $customer->name }} 
                            <span class="text-xs text-gray-500 block">Bal: {{ number_format($customer->current_balance ?? 0) }}</span>
                        </h3>
                    </div>

                    <div class="relative inline-block text-left" onclick="event.stopPropagation()">
                        <button onclick="toggleDropdown(this)" type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                            <div class="py-1" role="menu">
                                <a href="#" onclick="confirmDelete('{{ $customer->id }}', 'customer')" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-trash-alt mr-2"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                    <div id="noCustomersPlaceholder" class="bg-white p-8 rounded-lg border border-dashed border-gray-300 text-center">
                    <p class="text-gray-400 text-sm">No customers added yet.</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- 🟢 ADD CONTACT MODAL --}}
<div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Contact</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times"></i></button>
            </div>
            <form id="contactForm">
                @csrf
                <div class="px-6 py-6 space-y-5">
                    
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="contactName" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none" placeholder="e.g. Ali Poultry">
                        <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                        <input type="text" name="phone" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none" placeholder="e.g. 0300-1234567">
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                        <textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none resize-none" placeholder="e.g. Street 1, Lahore"></textarea>
                    </div>

                    {{-- Type (Hidden based on context) --}}
                    <div id="typeFieldContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <div class="relative">
                            <select name="type" id="typeSelect" required class="w-full appearance-none px-4 py-2.5 rounded-lg border border-gray-300 bg-white">
                                <option value="" selected>Select Type</option>
                                <option value="supplier">Supplier (Truck)</option>
                                <option value="customer">Permanent Customer (Hotel/Shop)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500"><i class="fas fa-chevron-down text-xs"></i></div>
                        </div>
                        <p id="typeError" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>

                    {{-- Opening Balance --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                        <input type="number" name="opening_balance" id="openingBalance" value="0" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none">
                        <p class="text-xs text-gray-400 mt-1">Positive = They owe us. Negative = We owe them.</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse">
                    <button type="submit" id="saveContactBtn" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 transition-colors">Save Contact</button>
                    <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full sm:w-auto justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:mr-3 transition-colors">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- 🟢 UNIFIED LEDGER MODAL --}}
<div id="ledgerModal" class="fixed inset-0 z-50 hidden" style="z-index: 60;">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeLedger()"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-7xl bg-white rounded-2xl shadow-2xl flex flex-col h-[90vh]">
            
            {{-- Header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800" id="ledgerTitle">Contact Name</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Current Balance: <span id="ledgerCurrentBalance" class="font-bold text-blue-600 text-xl">0</span> PKR
                    </p>
                </div>
                <button onclick="closeLedger()" class="text-gray-400 hover:text-gray-600 transition-colors bg-white p-2 rounded-full shadow-sm hover:shadow border border-gray-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
                
                {{-- Payment Form --}}
                <div class="bg-white p-5 rounded-xl border border-blue-100 shadow-sm mb-8">
                    <h4 class="font-bold text-gray-800 text-md mb-4 flex items-center gap-2">
                        <i class="fas fa-coins text-yellow-500"></i> Add New Transaction
                    </h4>
                    <form id="addPaymentForm" class="flex flex-col lg:flex-row gap-4 items-end">
                        
                        <input type="hidden" id="paymentContactId" name="contact_id">
                        <input type="hidden" id="paymentContactType" name="contact_type">
                        
                        <div class="w-full lg:w-1/5">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Date</label>
                            <input type="date" name="date" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="w-full lg:w-1/5">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Amount (PKR)</label>
                            <input type="number" name="amount" placeholder="0.00" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none font-semibold text-gray-800" required>
                        </div>

                        <div class="w-full lg:w-1/4">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Transaction Type</label>
                            <select name="type" id="paymentTypeSelect" class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                {{-- Options populated via JS --}}
                            </select>
                        </div>

                        <div class="w-full lg:w-1/4">
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Description</label>
                            <input type="text" name="description" placeholder="Optional notes..." class="w-full border border-gray-300 rounded-lg text-sm px-3 py-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <div class="w-full lg:w-auto">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg text-sm transition-all shadow-md active:scale-95 flex items-center justify-center whitespace-nowrap">
                                <i class="fas fa-plus mr-2"></i> Add Entry
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Ledger Table --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h4 class="font-bold text-gray-700 text-lg">Transaction History</h4>
                        {{-- <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded">Last 50 Records</span> --}}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[15%]">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-[35%]">Description</th>
                                    
                                    {{-- 🟢 UPDATED HEADERS WITH ID --}}
                                    <th id="thDebit" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-[15%]">
                                        Debit
                                    </th>
                                    <th id="thCredit" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-[15%]">
                                        Credit
                                    </th>
                                    
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider w-[20%]">Balance</th>
                                </tr>
                            </thead>
                            <tbody id="ledgerTableBody" class="bg-white divide-y divide-gray-200 text-sm">
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Loading records...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const STORE_URL = "{{ route('admin.contacts.store') }}"; 
    
    // --- SEARCH LOGIC (Unchanged) ---
    document.getElementById('searchSupplier').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.supplier-item');
        items.forEach(function(item) {
            let name = item.querySelector('.supplier-name').textContent.toLowerCase();
            item.style.display = name.includes(filter) ? "" : "none";
        });
    });

    document.getElementById('searchCustomer').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let items = document.querySelectorAll('.customer-item');
        items.forEach(function(item) {
            let name = item.querySelector('.customer-name').textContent.toLowerCase();
            item.style.display = name.includes(filter) ? "" : "none";
        });
    });

    // --- ADD CONTACT MODAL LOGIC ---
    function openAddModal(type) {
        const modal = document.getElementById('contactModal');
        const title = document.getElementById('modalTitle');
        const select = document.getElementById('typeSelect');
        const form = document.getElementById('contactForm');
        const typeContainer = document.getElementById('typeFieldContainer'); 
        form.reset();
        document.getElementById('statusMessage').classList.add('hidden');
        document.getElementById('nameError').classList.add('hidden');
        document.getElementById('typeError').classList.add('hidden'); 
        modal.classList.remove('hidden');
        if(type === 'supplier') {
            title.textContent = 'Add New Supplier';
            select.value = 'supplier';
            typeContainer.classList.add('hidden'); 
        } else if (type === 'customer') {
            title.textContent = 'Add New Customer';
            select.value = 'customer';
            typeContainer.classList.add('hidden'); 
        } else {
             title.textContent = 'Add New Contact';
             select.value = ''; 
             typeContainer.classList.remove('hidden'); 
        }
    }

    function closeModal() { document.getElementById('contactModal').classList.add('hidden'); }
    
    function toggleDropdown(button) {
        const dropdown = button.nextElementSibling;
        document.querySelectorAll('.relative > div').forEach(d => { if (d !== dropdown) d.classList.add('hidden'); });
        dropdown.classList.toggle('hidden');
    }

    // --- ADD CONTACT SUBMIT ---
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        e.preventDefault(); 
        const formData = new FormData(e.target);
        const submitBtn = document.getElementById('saveContactBtn');
        const nameError = document.getElementById('nameError');
        const typeError = document.getElementById('typeError');

        nameError.classList.add('hidden');
        typeError.classList.add('hidden'); 
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch(STORE_URL, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            if (!response.ok) {
                const errorData = await response.json();
                if (response.status === 422 && errorData.errors) {
                    if (errorData.errors.name) { nameError.textContent = errorData.errors.name[0]; nameError.classList.remove('hidden'); }
                    if (errorData.errors.type) { typeError.textContent = errorData.errors.type[0]; typeError.classList.remove('hidden'); }
                } else {
                    Swal.fire('Error', errorData.message || 'Server Error', 'error');
                }
                return; 
            }
            location.reload(); 
        } catch (error) {
            Swal.fire('Error', 'Network error.', 'error');
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
                method: 'POST', 
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-HTTP-Method-Override': 'DELETE' 
                },
                body: JSON.stringify({ type: type, _method: 'DELETE' }) 
            });

            if (response.ok) {
                const data = await response.json();
                document.getElementById(`${type}-${id}`).remove();
                Swal.fire('Deleted!', data.message, 'success');
            }
        } catch (error) {
            Swal.fire('Error', 'Failed to delete.', 'error');
        }
    }

    // ==========================================
    // 🟢 UNIFIED LEDGER LOGIC (Supplier & Customer)
    // ==========================================

    const ledgerModal = document.getElementById('ledgerModal');
    const ledgerTableBody = document.getElementById('ledgerTableBody');
    const ledgerTitleEl = document.getElementById('ledgerTitle');
    const ledgerBalanceEl = document.getElementById('ledgerCurrentBalance');
    const paymentContactIdEl = document.getElementById('paymentContactId');
    const paymentContactTypeEl = document.getElementById('paymentContactType');
    const paymentTypeSelect = document.getElementById('paymentTypeSelect');

    // 🟢 Generic Function to open Ledger for Supplier OR Customer
    function openLedger(id, name, type) {
        ledgerModal.classList.remove('hidden');
        ledgerTitleEl.textContent = name;
        paymentContactIdEl.value = id;
        paymentContactTypeEl.value = type;
        
        // Reset Table
        ledgerTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i><br>Loading records...</td></tr>';
        
        // 🟢 DYNAMIC HEADERS & DROPDOWN LOGIC
        const thDebit = document.getElementById('thDebit');
        const thCredit = document.getElementById('thCredit');

        if (type === 'supplier') {
            // Supplier Case
            thDebit.innerHTML = "Debit <span class='text-[10px] lowercase font-normal'>(Paid)</span>";
            thCredit.innerHTML = "Credit <span class='text-[10px] lowercase font-normal'>(Purchase)</span>";

            paymentTypeSelect.innerHTML = `
                <option value="payment">Payment (Cash Given to Supplier)</option>
                <option value="opening_balance">Opening Balance Adjustment</option>
            `;
        } else {
            // Customer Case
            thDebit.innerHTML = "Debit <span class='text-[10px] lowercase font-normal'>(Sale/Due)</span>";
            thCredit.innerHTML = "Credit <span class='text-[10px] lowercase font-normal'>(Received)</span>"; 

            paymentTypeSelect.innerHTML = `
                <option value="payment">Payment (Cash Received from Customer)</option>
                <option value="opening_balance">Opening Balance Adjustment</option>
            `;
        }

        fetchLedgerData(id, type);
    }

    function closeLedger() {
        ledgerModal.classList.add('hidden');
    }

    // 🟢 Fetch Data Dynamic URL (UPDATED FOR SALES HIGHLIGHT)
    async function fetchLedgerData(id, type) {
        try {
            const endpoint = (type === 'supplier') ? 'suppliers' : 'customers';
            const url = `/admin/${endpoint}/${id}/ledger`;

            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            if(!response.ok) throw new Error("Failed to fetch data");

            const data = await response.json();
            
            // Update Balance
            ledgerBalanceEl.textContent = parseFloat(data.current_balance).toLocaleString();

            // Render Table
            if(data.transactions.length === 0) {
                ledgerTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No transactions found.</td></tr>';
                return;
            }

            let html = '';
            data.transactions.forEach(txn => {
                // Ensure data is numeric
                let debitVal = parseFloat(txn.debit) || 0;
                let creditVal = parseFloat(txn.credit) || 0;
                let balanceVal = parseFloat(txn.balance) || 0;

                // 🟢 VISUAL LOGIC FOR SALES VS PAYMENTS
                let debitDisplay = '-';
                let creditDisplay = '-';
                let rowClass = "hover:bg-gray-50"; 
                let iconHtml = '<i class="fas fa-exchange-alt text-gray-400 mr-2"></i>'; // Default

                // 1. IS SALE (Customer owes money)
                if (txn.type === 'sale') {
                    debitDisplay = `<span class="text-red-600 font-bold">${debitVal.toLocaleString()}</span>`;
                    rowClass = "bg-red-50 hover:bg-red-100"; // Red highlight
                    iconHtml = '<i class="fas fa-shopping-cart text-red-500 mr-2"></i>'; // Cart Icon
                    // Add Badge to Description
                    txn.description = `<span class="bg-red-100 text-red-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded border border-red-400">SALE</span> ` + txn.description;
                } 
                // 2. IS PAYMENT RECEIVED (Customer paid us)
                else if (creditVal > 0 && type === 'customer') {
                    creditDisplay = `<span class="text-green-600 font-bold">${creditVal.toLocaleString()}</span>`;
                    rowClass = "bg-green-50 hover:bg-green-100"; // Green highlight
                    iconHtml = '<i class="fas fa-hand-holding-usd text-green-500 mr-2"></i>';
                }
                // 3. IS PAYMENT GIVEN (We paid Supplier)
                else if (debitVal > 0 && type === 'supplier') {
                    debitDisplay = `<span class="text-red-600 font-bold">${debitVal.toLocaleString()}</span>`;
                    iconHtml = '<i class="fas fa-paper-plane text-red-500 mr-2"></i>';
                }
                // 4. Manual Opening Balance (Generic)
                else {
                    if(debitVal > 0) debitDisplay = `<span class="text-gray-800 font-bold">${debitVal.toLocaleString()}</span>`;
                    if(creditVal > 0) creditDisplay = `<span class="text-gray-800 font-bold">${creditVal.toLocaleString()}</span>`;
                }

                html += `
                    <tr class="${rowClass} border-b border-gray-100 last:border-0 transition-colors">
                        <td class="px-6 py-3 whitespace-nowrap text-gray-600 text-sm font-medium">${txn.date}</td>
                        <td class="px-6 py-3 text-gray-800 text-sm flex items-center">
                            ${iconHtml}
                            <span>${txn.description || 'N/A'}</span>
                        </td>
                        <td class="px-6 py-3 text-right text-sm">${debitDisplay}</td>
                        <td class="px-6 py-3 text-right text-sm">${creditDisplay}</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-700 text-sm">${balanceVal.toLocaleString()}</td>
                    </tr>
                `;
            });
            ledgerTableBody.innerHTML = html;

        } catch (error) {
            console.error(error);
            ledgerTableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data.</td></tr>';
        }
    }

    // 🟢 Handle Payment Submit (Dynamic)
    document.getElementById('addPaymentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const id = paymentContactIdEl.value;
        const type = paymentContactTypeEl.value;
        const formData = new FormData(this);
        
        // Dynamically add correct ID key for backend validation
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

            if(response.ok) {
                Swal.fire({ icon: 'success', title: 'Saved', text: 'Transaction added successfully', timer: 1500, showConfirmButton: false });
                this.reset();
                // Restore hidden values lost on reset
                paymentContactIdEl.value = id;
                paymentContactTypeEl.value = type;
                document.querySelector('input[name="date"]').value = new Date().toISOString().split('T')[0];
                fetchLedgerData(id, type); // Reload table
            } else {
                Swal.fire('Error', result.message || 'Failed to save', 'error');
            }

        } catch (error) {
            Swal.fire('Error', 'Network Error', 'error');
        }
    });

</script>
@endsection