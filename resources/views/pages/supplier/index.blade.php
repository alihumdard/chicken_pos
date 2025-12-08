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

                {{-- 🟢 SUPPLIERS SECTION --}}
                {{-- min-w-0 prevents flex items from overflowing their container --}}
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
                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
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
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-gray-700">Customers</h2>
                            <button onclick="openAddModal('customer')"
                                class="bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-medium py-2 px-3 sm:px-4 rounded-lg flex items-center transition-colors shadow-sm whitespace-nowrap">
                                <i class="fas fa-plus mr-2"></i> Add Customer
                            </button>
                        </div>
                        <div class="relative w-full">
                            <input type="text" id="searchCustomer" placeholder="Search customers..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all text-sm">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <div id="customerList" class="space-y-2 px-1 sm:px-2 pb-2">
                        @forelse($customers as $customer)
                            <div id="customer-{{ $customer->id }}"
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
                                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                                        <div class="py-1" role="menu">
                                            <a href="#"
                                                onclick="openEditModal('{{ $customer->id }}', '{{ addslashes($customer->name) }}', '{{ $customer->phone ?? '' }}', '{{ addslashes($customer->address ?? '') }}', 'customer')"
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
    <div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()">
        </div>
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-sm sm:max-w-md border border-gray-100">
                <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Contact</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors"><i
                            class="fas fa-times"></i></button>
                </div>
                <form id="contactForm">
                    @csrf
                    <input type="hidden" id="editContactId" name="id" value="">

                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" name="name" id="contactName" required
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none"
                                placeholder="e.g. Ali Poultry">
                            <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none"
                                placeholder="e.g. 923001234567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                    class="text-gray-400 font-normal text-xs">(Optional)</span></label>
                            <textarea name="address" rows="2"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 outline-none resize-none"
                                placeholder="e.g. Street 1, Lahore"></textarea>
                        </div>
                        <div id="typeFieldContainer">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <div class="relative">
                                <select name="type" id="typeSelect" required
                                    class="w-full appearance-none px-4 py-2.5 rounded-lg border border-gray-300 bg-white">
                                    <option value="" selected>Select Type</option>
                                    <option value="supplier">Supplier (Truck)</option>
                                    <option value="customer">Permanent Customer (Hotel/Shop)</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <p id="typeError" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                            <input type="number" name="opening_balance" id="openingBalance" value="0"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 outline-none">
                            <p class="text-xs text-gray-400 mt-1">Positive = They owe us. Negative = We owe them.</p>
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
            <div class="relative w-full max-w-7xl bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl flex flex-col h-[95vh] sm:h-[90vh]">

                {{-- Header --}}
                <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 rounded-t-2xl gap-4">
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
                    <div id="paymentFormContainer" class="bg-white p-4 sm:p-5 rounded-xl border border-blue-100 shadow-sm mb-6 sm:mb-8">
                        <h4 class="font-bold text-gray-800 text-md mb-4 flex items-center gap-2">
                            <i class="fas fa-coins text-yellow-500"></i> Add Manual Transaction
                        </h4>
                        
                        {{-- 
                            Responsive Form: 
                            - flex-col on mobile (stack vertically)
                            - lg:flex-row on desktop (side-by-side)
                        --}}
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

                    {{-- Ledger Table --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden w-full">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-700 text-lg">Transaction History</h4>
                        </div>
                        
                        {{-- 
                             overflow-x-auto allows table scrolling internally on mobile 
                             without breaking the page width 
                        --}}
                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        {{-- whitespace-nowrap keeps headers on one line --}}
                                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[15%]">Date</th>
                                        <th class="px-4 sm:px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[35%]">Description</th>
                                        <th id="thDebit" class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[15%]">Debit</th>
                                        <th id="thCredit" class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[15%]">Credit</th>
                                        <th class="px-4 sm:px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap w-[20%]">Balance</th>
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
        document.getElementById('searchSupplier').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('.supplier-item').forEach(function(item) {
                let name = item.querySelector('.supplier-name').textContent.toLowerCase();
                item.style.display = name.includes(filter) ? "" : "none";
            });
        });

        document.getElementById('searchCustomer').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('.customer-item').forEach(function(item) {
                let name = item.querySelector('.customer-name').textContent.toLowerCase();
                item.style.display = name.includes(filter) ? "" : "none";
            });
        });

        // --- ADD / EDIT CONTACT MODAL LOGIC ---
        function openAddModal(type) {
            const modal = document.getElementById('contactModal');
            const title = document.getElementById('modalTitle');
            const select = document.getElementById('typeSelect');
            const form = document.getElementById('contactForm');
            const typeContainer = document.getElementById('typeFieldContainer');

            form.reset();
            document.getElementById('editContactId').value = '';

            document.getElementById('statusMessage').classList.add('hidden');
            document.getElementById('nameError').classList.add('hidden');
            document.getElementById('typeError').classList.add('hidden');
            modal.classList.remove('hidden');

            if (type === 'supplier') {
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

        function openEditModal(id, name, phone, address, type) {
            const modal = document.getElementById('contactModal');
            const title = document.getElementById('modalTitle');
            const typeContainer = document.getElementById('typeFieldContainer');

            document.getElementById('editContactId').value = id;
            document.querySelector('input[name="name"]').value = name;
            document.querySelector('input[name="phone"]').value = phone;
            document.querySelector('textarea[name="address"]').value = address;

            const select = document.getElementById('typeSelect');
            select.value = type;
            typeContainer.classList.add('hidden');

            title.textContent = 'Edit ' + (type.charAt(0).toUpperCase() + type.slice(1));
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

        window.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                document.querySelectorAll('.relative > div.origin-top-right').forEach(d => d.classList.add('hidden'));
            }
        });

        // --- FORM SUBMISSION ---
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const submitBtn = document.getElementById('saveContactBtn');
            const nameError = document.getElementById('nameError');
            const typeError = document.getElementById('typeError');
            const editId = document.getElementById('editContactId').value;

            nameError.classList.add('hidden');
            typeError.classList.add('hidden');
            submitBtn.disabled = true;

            let url = STORE_URL;
            if (editId) {
                url = `/admin/contacts/${editId}`;
                formData.append('_method', 'PUT');
                submitBtn.textContent = 'Updating...';
            } else {
                submitBtn.textContent = 'Saving...';
            }

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const result = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && result.errors) {
                        if (result.errors.name) {
                            nameError.textContent = result.errors.name[0];
                            nameError.classList.remove('hidden');
                        }
                        if (result.errors.type) {
                            typeError.textContent = result.errors.type[0];
                            typeError.classList.remove('hidden');
                        }
                    } else {
                        Swal.fire('Error', result.message || 'Server Error', 'error');
                    }
                    return;
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(() => location.reload(), 1000);

            } catch (error) {
                console.error(error);
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
                    body: JSON.stringify({
                        type: type,
                        _method: 'DELETE'
                    })
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

        function openLedger(id, name, phone, type) {
            ledgerModal.classList.remove('hidden');
            ledgerTitleEl.textContent = name;
            paymentContactIdEl.value = id;
            paymentContactTypeEl.value = type;
            paymentContactPhoneEl.value = phone;

            ledgerTableBody.innerHTML =
                '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i><br>Loading records...</td></tr>';

            const thDebit = document.getElementById('thDebit');
            const thCredit = document.getElementById('thCredit');

            if (type === 'supplier') {
                customerActionsEl.classList.add('hidden');
                thDebit.innerHTML = "Debit <span class='text-[10px] lowercase font-normal'>(Paid)</span>";
                thCredit.innerHTML = "Credit <span class='text-[10px] lowercase font-normal'>(Purchase)</span>";
                paymentTypeSelect.innerHTML = `
                    <option value="payment">Payment (Cash Given to Supplier)</option>
                    <option value="opening_balance">Opening Balance Adjustment</option>
                `;
            } else {
                customerActionsEl.classList.remove('hidden');
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
            try {
                const endpoint = (type === 'supplier') ? 'suppliers' : 'customers';
                const url = `/admin/${endpoint}/${id}/ledger`;

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error("Failed to fetch data");

                const data = await response.json();
                ledgerBalanceEl.textContent = parseFloat(data.current_balance).toLocaleString();

                if (data.transactions.length === 0) {
                    ledgerTableBody.innerHTML =
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No transactions found.</td></tr>';
                    return;
                }

                let html = '';
                data.transactions.forEach(txn => {
                    let debitVal = parseFloat(txn.debit) || 0;
                    let creditVal = parseFloat(txn.credit) || 0;
                    let balanceVal = parseFloat(txn.balance) || 0;

                    let debitDisplay = debitVal > 0 ?
                        `<span class="font-bold text-gray-800">${debitVal.toLocaleString()}</span>` : '-';
                    let creditDisplay = creditVal > 0 ?
                        `<span class="font-bold text-gray-800">${creditVal.toLocaleString()}</span>` : '-';

                    let rowClass = "hover:bg-gray-50";
                    let iconHtml = '<i class="fas fa-exchange-alt text-gray-400 mr-2"></i>';
                    let descHtml = txn.description || 'N/A';

                    if (txn.type === 'purchase') {
                        rowClass = "bg-blue-50 hover:bg-blue-100";
                        iconHtml = '<i class="fas fa-truck text-blue-600 mr-2"></i>';
                        descHtml =
                            `<span class="bg-blue-100 text-blue-800 text-xs font-bold mr-2 px-2 py-0.5 rounded border border-blue-300">PURCHASE</span> ${txn.description}`;
                        creditDisplay = `<span class="text-blue-700 font-bold">${creditVal.toLocaleString()}</span>`;
                    } else if (txn.type === 'payment' && type === 'supplier') {
                        rowClass = "bg-red-50 hover:bg-red-100";
                        iconHtml = '<i class="fas fa-money-bill-wave text-red-600 mr-2"></i>';
                        descHtml =
                            `<span class="bg-red-100 text-red-800 text-xs font-bold mr-2 px-2 py-0.5 rounded border border-red-300">PAID</span> ${txn.description}`;
                        debitDisplay = `<span class="text-red-700 font-bold">${debitVal.toLocaleString()}</span>`;
                    } else if (txn.type === 'sale') {
                        rowClass = "bg-red-50 hover:bg-red-100";
                        iconHtml = '<i class="fas fa-shopping-cart text-red-500 mr-2"></i>';
                        descHtml =
                            `<span class="bg-red-100 text-red-800 text-xs font-bold mr-2 px-2 py-0.5 rounded border border-red-300">SALE</span> ${txn.description}`;
                        debitDisplay = `<span class="text-red-600 font-bold">${debitVal.toLocaleString()}</span>`;
                    } else if (txn.type === 'payment' && type === 'customer') {
                        rowClass = "bg-green-50 hover:bg-green-100";
                        iconHtml = '<i class="fas fa-hand-holding-usd text-green-500 mr-2"></i>';
                        descHtml =
                            `<span class="bg-green-100 text-green-800 text-xs font-bold mr-2 px-2 py-0.5 rounded border border-green-300">RECEIVED</span> ${txn.description}`;
                        creditDisplay =
                            `<span class="text-green-600 font-bold">${creditVal.toLocaleString()}</span>`;
                    }

                    // Added whitespace-nowrap to cells
                    html += `
                    <tr class="${rowClass} border-b border-gray-100 last:border-0 transition-colors">
                        <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-600 text-sm font-medium">${txn.date}</td>
                        <td class="px-4 sm:px-6 py-3 text-gray-800 text-sm flex items-center min-w-[200px]">
                            ${iconHtml}
                            <span class="truncate max-w-xs" title="${txn.description}">${descHtml}</span>
                        </td>
                        <td class="px-4 sm:px-6 py-3 text-right text-sm whitespace-nowrap">${debitDisplay}</td>
                        <td class="px-4 sm:px-6 py-3 text-right text-sm whitespace-nowrap">${creditDisplay}</td>
                        <td class="px-4 sm:px-6 py-3 text-right font-bold text-gray-700 text-sm whitespace-nowrap">${balanceVal.toLocaleString()}</td>
                    </tr>
                `;
                });
                ledgerTableBody.innerHTML = html;

            } catch (error) {
                console.error(error);
                ledgerTableBody.innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data.</td></tr>';
            }
        }

        document.getElementById('addPaymentForm').addEventListener('submit', async function(e) {
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
    </script>
@endsection