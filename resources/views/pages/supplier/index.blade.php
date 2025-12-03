@extends('layouts.main')

@section('content')
<div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Supplier & Customer Management</h1>
    </div>
    
    <div id="statusMessage" class="mb-4 hidden p-3 rounded-lg text-sm font-medium" role="alert"></div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="bg-gray-100 rounded-xl p-1">
            <div class="flex justify-between items-center p-4 mb-2">
                <h2 class="text-lg font-semibold text-gray-700">Suppliers (Trucks)</h2>
                <button onclick="openModal('supplier')" 
                        class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Add Supplier
                </button>
            </div>

            <div id="supplierList" class="space-y-2 px-2 pb-2">
                @forelse($suppliers as $supplier)
                <div id="supplier-{{ $supplier->id }}" class="group bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow">
                    <div>
                        <h3 class="font-medium text-gray-800">{{ $supplier->name }}</h3>
                    </div>
                    
                    <div class="relative inline-block text-left">
                        <button onclick="toggleDropdown(this)" type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="#" 
                                    onclick="confirmDelete('{{ $supplier->id }}', 'supplier')"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">
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

        <div class="bg-gray-100 rounded-xl p-1">
            <div class="flex justify-between items-center p-4 mb-2">
                <h2 class="text-lg font-semibold text-gray-700">Permanent Customers (Hotels/Shops)</h2>
                <button onclick="openModal('customer')" 
                        class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center transition-colors shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Add Customer
                </button>
            </div>

            <div id="customerList" class="space-y-2 px-2 pb-2">
                @forelse($customers as $customer)
                <div id="customer-{{ $customer->id }}" class="group bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow">
                    <div>
                        <h3 class="font-medium text-gray-800">
                            {{ $customer->name }} 
                            <span class="text-gray-500 font-normal text-sm ml-1">(Bal: {{ number_format($customer->current_balance ?? 0) }})</span>
                        </h3>
                    </div>

                    <div class="relative inline-block text-left">
                        <button onclick="toggleDropdown(this)" type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="#" 
                                    onclick="confirmDelete('{{ $customer->id }}', 'customer')"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">
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

<div id="contactModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-40 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
            
            <div class="bg-white px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800" id="modalTitle">Add New Contact</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="contactForm">
                @csrf
                <div class="px-6 py-6 space-y-5">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="contactName" required 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300"
                                placeholder="e.g. Ali Poultry">
                        <p id="nameError" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>

                    <div id="typeFieldContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <div class="relative">
                            <select name="type" id="typeSelect" required
                                    class="w-full appearance-none px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all bg-white text-gray-700">
                                <option value="" selected>Select Type</option>
                                <option value="supplier">Supplier (Truck)</option>
                                <option value="customer">Permanent Customer (Hotel/Shop)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                        <p id="typeError" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                        <input type="number" name="opening_balance" id="openingBalance" value="0"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-slate-200 focus:border-slate-400 outline-none transition-all placeholder-gray-300">
                        <p class="text-xs text-gray-400 mt-1">Positive = They owe us. Negative = We owe them.</p>
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse">
                    <button type="submit" id="saveContactBtn" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-slate-800 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600 transition-colors">
                        Save Contact
                    </button>
                    <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full sm:w-auto justify-center rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:mr-3 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const STORE_URL = "{{ route('admin.contacts.store') }}"; 
    
    // --- BASIC MODAL/UI FUNCTIONS ---

    function openModal(type) {
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

    function closeModal() {
        document.getElementById('contactModal').classList.add('hidden');
    }
    
    function toggleDropdown(button) {
        const dropdown = button.nextElementSibling;
        document.querySelectorAll('.relative > div').forEach(d => {
            if (d !== dropdown) {
                d.classList.add('hidden');
            }
        });
        dropdown.classList.toggle('hidden');
    }

    // --- CRUD FUNCTIONS ---

    // 1. CREATE (AJAX Submit)
    document.getElementById('contactForm').addEventListener('submit', async function(e) {
        // 🛑 CRITICAL: Prevents the default browser submission (which causes the reload)
        e.preventDefault(); 

        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = document.getElementById('saveContactBtn');
        const statusMessage = document.getElementById('statusMessage');
        const nameError = document.getElementById('nameError');
        const typeError = document.getElementById('typeError');

        nameError.classList.add('hidden');
        typeError.classList.add('hidden'); 
        statusMessage.classList.add('hidden');

        const typeValue = document.getElementById('typeSelect').value;
        if (!typeValue || (typeValue !== 'supplier' && typeValue !== 'customer')) {
             typeError.textContent = 'Contact type must be selected.';
             typeError.classList.remove('hidden');
             return; 
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch(STORE_URL, {
                method: 'POST',
                headers: {
                    // Tell Laravel to treat this as an AJAX request
                    'X-Requested-With': 'XMLHttpRequest', 
                },
                body: formData
            });

            // If the server returns anything other than 2xx, handle it as an error
            if (!response.ok) {
                const contentType = response.headers.get("content-type");
                let errorData = { message: `Server error (${response.status}).` };
                
                if (contentType && contentType.indexOf("application/json") !== -1) {
                    errorData = await response.json();
                } else {
                    // If the server returns HTML (like a redirect), this block is crucial.
                    // It alerts you that the server is not sending JSON.
                    errorData.message = `Unexpected server response. Check the Network tab (F12) for the actual response.`;
                }

                
                if (response.status === 422 && errorData.errors) {
                    if (errorData.errors.name) {
                        nameError.textContent = errorData.errors.name[0];
                        nameError.classList.remove('hidden');
                    }
                    if (errorData.errors.type) {
                        typeError.textContent = errorData.errors.type[0];
                        typeError.classList.remove('hidden');
                    }
                    statusMessage.textContent = 'Validation failed. Check the form.';
                } else {
                    statusMessage.textContent = errorData.message || 'An unexpected server error occurred.';
                }
                
                statusMessage.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
                statusMessage.classList.remove('hidden');
                return; 
            }

            // 🟢 SUCCESS: Response is OK (200-299)
            const data = await response.json(); 
            
            // 1. Append new contact to the list
            appendContactToList(data.contact); 

            // 2. Show success message
            statusMessage.textContent = data.message || 'Contact added successfully!';
            statusMessage.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-green-100 text-green-700';
            statusMessage.classList.remove('hidden');
            
            // 3. Close the modal and reset the form immediately
            closeModal(); 
            form.reset(); 

        } catch (error) {
            // Network errors or errors during response.json() parsing
            console.error('Error:', error);
            statusMessage.textContent = 'Network error occurred. Please try again.';
            statusMessage.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
            statusMessage.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Save Contact';
        }
    });
    
    // 2. DELETE (AJAX Delete)

    function confirmDelete(id, type) {
        if (!confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.`)) {
            return;
        }

        const openDropdown = document.querySelector('.relative > div:not(.hidden)');
        if(openDropdown) {
             openDropdown.classList.add('hidden');
        }
        
        deleteContact(id, type);
    }

    async function deleteContact(id, type) {
        const statusMessage = document.getElementById('statusMessage');
        statusMessage.classList.add('hidden');
        const DELETE_URL = `/admin/contacts/${id}`; 

        try {
            const response = await fetch(DELETE_URL, {
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
                
                // 1. Remove the contact item from the DOM
                const elementToRemove = document.getElementById(`${type}-${id}`);
                if (elementToRemove) {
                    elementToRemove.remove();
                }
                
                // 2. Check if the list is now empty and show the placeholder
                const listId = type === 'supplier' ? 'supplierList' : 'customerList';
                const listContainer = document.getElementById(listId);
                
                if (listContainer.children.length === 0) {
                    const placeholderId = type === 'supplier' ? 'noSuppliersPlaceholder' : 'noCustomersPlaceholder';
                    const listName = type === 'supplier' ? 'suppliers' : 'customers';
                    const placeholderHtml = `<div id="${placeholderId}" class="bg-white p-8 rounded-lg border border-dashed border-gray-300 text-center"><p class="text-gray-400 text-sm">No ${listName} added yet.</p></div>`;
                    listContainer.insertAdjacentHTML('afterbegin', placeholderHtml);
                }


                // 3. Show success message
                statusMessage.textContent = data.message || `${type} deleted successfully.`;
                statusMessage.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-green-100 text-green-700';
                statusMessage.classList.remove('hidden');

            } else {
                const errorData = await response.json();
                statusMessage.textContent = errorData.message || 'Error deleting contact.';
                statusMessage.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
                statusMessage.classList.remove('hidden');
            }

        } catch (error) {
            console.error('Error during deletion:', error);
            statusMessage.textContent = 'Network error during deletion. Please try again.';
            statusMessage.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
            statusMessage.classList.remove('hidden');
        }
    }


    /** 3. HELPER FUNCTION: Appends new contact to the correct list */
    function appendContactToList(contact) {
        const contactType = (contact && contact.type) ? contact.type.trim().toLowerCase() : null;

        if (!contactType) {
            console.error('Cannot append contact: Type is missing in the server response.', contact);
            return; 
        }

        const listId = contactType === 'supplier' ? 'supplierList' : 'customerList';
        const listContainer = document.getElementById(listId);
        
        // 1. Remove the "No contacts added yet" placeholder if it exists
        const placeholderId = contactType === 'supplier' ? 'noSuppliersPlaceholder' : 'noCustomersPlaceholder';
        const placeholder = document.getElementById(placeholderId);
        if (placeholder) {
            placeholder.remove();
        }

        // 2. Create the HTML structure for the new contact
        let balanceHtml = '';
        if (contactType === 'customer') {
            const balance = contact.current_balance ? parseFloat(contact.current_balance).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",") : '0';
            balanceHtml = `<span class="text-gray-500 font-normal text-sm ml-1">(Bal: ${balance})</span>`;
        }

        const newContactHtml = `
            <div id="${contactType}-${contact.id}" class="group bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex justify-between items-center hover:shadow-md transition-shadow">
                <div>
                    <h3 class="font-medium text-gray-800">
                        ${contact.name} 
                        ${balanceHtml}
                    </h3>
                </div>
                <div class="relative inline-block text-left">
                    <button onclick="toggleDropdown(this)" type="button" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-50">
                        <i class="fas fa-ellipsis-h"></i>
                    </button>
                    <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden z-10">
                        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            <a href="#" 
                               onclick="confirmDelete('${contact.id}', '${contactType}')"
                               class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50" role="menuitem">
                                  <i class="fas fa-trash-alt mr-2"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // 3. Insert the new contact at the top of the correct list
        listContainer.insertAdjacentHTML('afterbegin', newContactHtml);
        
        // VISUAL FIX: Force browser redraw/reflow for immediate visibility
        listContainer.style.display = 'none';
        // A minimal timeout of 1ms is enough to trigger a reflow
        setTimeout(() => {
            listContainer.style.display = 'block'; 
        }, 1); 
    }
</script>
@endsection