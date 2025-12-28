@extends('layouts.main')

@section('content')

<main class="flex justify-center items-center min-h-screen p-4 sm:p-6 lg:p-10 bg-gray-100">

    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-10 w-full max-w-5xl border border-gray-200 relative">

        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight text-center">
            System Configuration
        </h1>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            {{-- LEFT COLUMN --}}
            <div class="space-y-10">

                {{-- General Settings Form --}}
                <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" id="general-settings-form">
                    @csrf
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">
                            General Settings
                        </h2>
                        <div class="space-y-5">
                            {{-- Shop Name --}}
                            <div>
                                <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                                <input type="text" name="shop_name" id="shop_name" placeholder="Enter shop name"
                                       value="{{ old('shop_name', $settings->shop_name ?? '') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            {{-- Address --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" name="address" id="address" placeholder="Enter address"
                                       value="{{ old('address', $settings->address ?? '') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            {{-- Phone Number --}}
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" placeholder="Enter phone number"
                                       value="{{ old('phone_number', $settings->phone_number ?? '') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            {{-- Logo --}}
                            <div>
                                <label for="logo_file" class="block text-sm font-medium text-gray-700 mb-2">Shop Logo (Max 2MB)</label>
                                <input type="file" name="logo_file" id="logo_file"
                                       class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 p-2">
                                @if (isset($settings->logo_url) && $settings->logo_url)
                                    <div class="mt-3 flex items-center space-x-3">
                                        <p class="text-sm text-gray-500">Current Logo:</p>
                                        <img src="{{ asset($settings->logo_url) }}" alt="Current Logo" class="h-10 w-auto object-contain border p-1 rounded">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="hidden"><button type="submit" id="save-general-btn"></button></div>
                </form>

                {{-- User Config Placeholder --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">
                        User Configuration
                    </h2>
                    <select class="w-full rounded-lg border-gray-300 shadow-sm p-3 bg-white focus:ring-blue-500 focus:border-blue-500">
                        <option>Admin (Owner)</option>
                        <option>Manager</option>
                        <option>Staff</option>
                    </select>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="space-y-10">

                {{-- Formula Settings --}}
                <div>
                    <div class="flex justify-between items-center mb-4 border-l-4 border-blue-600 pl-3">
                        <h2 class="text-xl font-semibold text-gray-700">
                            Chicken Formula Settings
                        </h2>
                        {{-- 🟢 NEW: Add Formula Button --}}
                        <button id="open-create-modal-btn" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold px-3 py-2 rounded-lg shadow transition flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Formula
                        </button>
                    </div>
                    
                    <form id="formula-settings-form" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="rate_key" class="block text-sm font-medium text-gray-700 mb-1">Select Rate Category</label>
                            <select id="rate_key" name="rate_key"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-3 bg-white focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="" disabled selected>-- Select a category to edit --</option>
                            </select>
                        </div>
                        
                        {{-- Edit Formula Inputs --}}
                        <div id="formula-inputs" class="bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                            
                           <div class="mb-4 flex items-center gap-4 border-b border-gray-200 pb-4">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden border border-gray-300 relative">
                                    <img id="edit-icon-preview" src="" alt="Icon" class="w-full h-full object-cover hidden">
                                    <i id="edit-icon-placeholder" class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Update Icon</label>
                                    {{-- 🟢 Input name is icon_url --}}
                                    <input type="file" name="icon_url" accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Multiply (×)</label>
                                    <input type="number" name="multiply" id="multiply" step="0.0001" value="1.0000"
                                           class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Divide (÷)</label>
                                    <input type="number" name="divide" id="divide" step="0.0001" min="0.0001" value="1.0000"
                                           class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Plus (+)</label>
                                    <input type="number" name="plus" id="plus" step="0.0001" value="0.0000"
                                           class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Minus (-)</label>
                                    <input type="number" name="minus" id="minus" step="0.0001" min="0" value="0.0000"
                                           class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <button type="submit" id="save-formula-btn"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg py-3 shadow-md transition">
                                    Update Formula
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <p id="formula-message" class="text-sm mt-3 font-medium hidden"></p>
                </div>

                {{-- Data Management & User Mgmt (Existing) --}}
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">Data Management</h2>
                    <div class="flex gap-4">
                        <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg py-3 shadow-md transition">Backup Data</button>
                        <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg py-3 shadow-md transition">Reset System</button>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-4 border-l-4 border-blue-600 pl-3">
                        <h2 class="text-xl font-semibold text-gray-700">User Management</h2>
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow hover:bg-blue-700">+ User</button>
                    </div>
                    <div class="space-y-3">
                        @foreach (['Admin (Owner)', 'Manager'] as $role)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 shadow">
                                <span class="text-sm font-medium text-gray-700">{{ $role }}</span>
                                <button class="text-xs bg-white border border-gray-300 py-1 px-3 rounded-md shadow hover:bg-gray-100">Edit</button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-10 border-t pt-6 flex justify-end">
            <button id="main-save-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition">
                Save All Changes
            </button>
        </div>

    </div>

    {{-- 🟢 START: CREATE FORMULA MODAL --}}
    <div id="create-formula-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" id="modal-backdrop"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                        Add New Rate Formula
                    </h3>
                    
                    <form id="create-formula-form" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            
                            {{-- 🟢 NEW: Channel Selection --}}
                            <div>
                                <label for="new_channel" class="block text-sm font-medium text-gray-700">Channel <span class="text-red-500">*</span></label>
                                <select id="new_channel" name="channel" required 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                                    <option value="wholesale" selected>Wholesale</option>
                                    <option value="retail">Retail</option>
                                </select>
                            </div>
                    
                            {{-- Title --}}
                            <div>
                                <label for="new_title" class="block text-sm font-medium text-gray-700">Title / Name <span class="text-red-500">*</span></label>
                                <input type="text" id="new_title" name="title" required placeholder="e.g. Special Mix"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm p-2 border">
                            </div>
                    
                            {{-- Key (Auto-generated) --}}
                            <div>
                                <label for="new_rate_key" class="block text-sm font-medium text-gray-700">System Key <span class="text-xs text-gray-500">(Auto-generated)</span></label>
                                <input type="text" id="new_rate_key" name="rate_key" required readonly
                                       class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 shadow-sm sm:text-sm p-2 border cursor-not-allowed">
                            </div>
                    
                            {{-- 🟢 UPDATED: File Input for Custom Icon --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Custom Icon (Image)</label>
                                {{-- 🟢 Input name is icon_url --}}
                                <input type="file" id="new_icon_file" name="icon_url" accept="image/*"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border rounded-md p-1">
                                <p class="text-xs text-gray-500 mt-1">Formats: JPG, PNG, SVG (Max 2MB)</p>
                            </div>
                            
                            {{-- Math Values --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Multiply</label>
                                    <input type="number" name="multiply" step="0.0001" value="1.0000" class="w-full border p-2 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Divide</label>
                                    <input type="number" name="divide" step="0.0001" value="1.0000" class="w-full border p-2 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Plus</label>
                                    <input type="number" name="plus" step="0.0001" value="0.0000" class="w-full border p-2 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 uppercase">Minus</label>
                                    <input type="number" name="minus" step="0.0001" value="0.0000" class="w-full border p-2 rounded text-sm">
                                </div>
                            </div>
                    
                        </div>
                    </form>
           <p id="create-error-msg" class="text-red-500 text-sm mt-2 hidden"></p>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="submit-create-formula" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Create
                    </button>
                    <button type="button" id="close-create-modal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- 🟢 END MODAL --}}

</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const rateKeySelect = document.getElementById('rate_key');
        const formulaInputsContainer = document.getElementById('formula-inputs');
        const formulaForm = document.getElementById('formula-settings-form');
        const formulaMessage = document.getElementById('formula-message');
        
        // 🟢 NEW: Preview Elements
        const editIconPreview = document.getElementById('edit-icon-preview');
        const editIconPlaceholder = document.getElementById('edit-icon-placeholder');

        // Modal Elements
        const openModalBtn = document.getElementById('open-create-modal-btn');
        const closeModalBtn = document.getElementById('close-create-modal');
        const modal = document.getElementById('create-formula-modal');
        const backdrop = document.getElementById('modal-backdrop');
        const submitCreateBtn = document.getElementById('submit-create-formula');
        const createForm = document.getElementById('create-formula-form');
        const newChannelSelect = document.getElementById('new_channel'); 
        const newTitleInput = document.getElementById('new_title');
        const newKeyInput = document.getElementById('new_rate_key');
        const createErrorMsg = document.getElementById('create-error-msg');

        // Main Save Button Proxy
        document.getElementById('main-save-btn').addEventListener('click', function() {
            document.getElementById('save-general-btn').click(); 
        });

        let allFormulas = {};

        // --- FETCH FORMULAS ---
        async function fetchFormulas() {
            try {
                const response = await fetch('{{ route('admin.rates.formulas.get') }}', { 
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Failed to fetch formulas.');
                const data = await response.json();
                allFormulas = data.formulas;
                populateDropdown(data.friendly_names);
            } catch (error) {
                console.error(error);
                formulaMessage.textContent = 'Could not load formulas.';
                formulaMessage.classList.remove('hidden', 'text-green-600');
                formulaMessage.classList.add('text-red-600');
            }
        }

        function populateDropdown(friendlyNames) {
            rateKeySelect.querySelectorAll('option:not([disabled])').forEach(option => option.remove());
            for (const key in friendlyNames) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = friendlyNames[key];
                rateKeySelect.appendChild(option);
            }
        }

        // --- 🟢 EDIT FORMULA LOGIC (With Image Preview) ---
  // --- EDIT FORMULA LOGIC ---
        rateKeySelect.addEventListener('change', function() {
            const selectedKey = this.value;
            const formula = allFormulas[selectedKey];
            
            if (formula) {
                document.getElementById('multiply').value = formula.multiply;
                document.getElementById('divide').value = formula.divide;
                document.getElementById('plus').value = formula.plus;
                document.getElementById('minus').value = formula.minus;
                
                // 🟢 Simplified Preview Logic
                if (formula.icon_url) {
                    // Controller now sends the FULL accessible URL
                    editIconPreview.src = formula.icon_url; 
                    editIconPreview.classList.remove('hidden');
                    editIconPlaceholder.classList.add('hidden');
                } else {
                    editIconPreview.src = '';
                    editIconPreview.classList.add('hidden');
                    editIconPlaceholder.classList.remove('hidden');
                }

                formulaInputsContainer.classList.remove('hidden');
            }
        });

        // --- SUBMIT EDIT FORM ---
        formulaForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this); // Automatically handles files
            const rateKey = formData.get('rate_key');
            formulaMessage.classList.add('hidden');
            
            try {
                const response = await fetch('{{ route('admin.rates.formulas.update') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                const result = await response.json();

                if (response.ok && result.success) {
                    // Update Local Data
                    allFormulas[rateKey].multiply = formData.get('multiply');
                    allFormulas[rateKey].divide = formData.get('divide');
                    allFormulas[rateKey].plus = formData.get('plus');
                    allFormulas[rateKey].minus = formData.get('minus');
                    
                    // Update icon url locally if returned
                    if(result.new_icon_url) {
                         // Extract relative path if needed, or just refresh the fetch
                         fetchFormulas(); // Simplest way to ensure paths are correct
                    }

                    formulaMessage.textContent = result.message;
                    formulaMessage.classList.remove('hidden', 'text-red-600');
                    formulaMessage.classList.add('text-green-600');
                } else {
                    throw new Error(result.message || "Unknown error");
                }
            } catch (error) {
                formulaMessage.textContent = "Error: " + error.message;
                formulaMessage.classList.remove('hidden', 'text-green-600');
                formulaMessage.classList.add('text-red-600');
            }
        });

        // --- MODAL LOGIC (Create) ---
        function toggleModal(show) {
            if(show) { modal.classList.remove('hidden'); } 
            else { 
                modal.classList.add('hidden'); 
                createForm.reset(); 
                createErrorMsg.classList.add('hidden'); 
            }
        }

        openModalBtn.addEventListener('click', () => toggleModal(true));
        closeModalBtn.addEventListener('click', () => toggleModal(false));
        backdrop.addEventListener('click', () => toggleModal(false));

        function generateKey() {
            const channel = newChannelSelect ? newChannelSelect.value : 'wholesale';
            const titleVal = newTitleInput.value.toLowerCase().trim();
            if(titleVal) {
                const slug = titleVal.replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '_');
                newKeyInput.value = `${channel}_${slug}_rate`;
            } else {
                newKeyInput.value = '';
            }
        }

        newTitleInput.addEventListener('input', generateKey);
        if(newChannelSelect) newChannelSelect.addEventListener('change', generateKey);

        submitCreateBtn.addEventListener('click', async function() {
            createErrorMsg.classList.add('hidden');
            const formData = new FormData(createForm); // Automatically handles files

            if(!formData.get('title')) {
                createErrorMsg.textContent = "Title is required.";
                createErrorMsg.classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch('{{ route('admin.rates.formulas.update') }}', { 
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData 
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    toggleModal(false);
                    fetchFormulas(); // Refresh dropdown
                    alert("Formula Created Successfully!");
                } else {
                    throw new Error(result.message || "Failed to create.");
                }
            } catch (error) {
                createErrorMsg.textContent = error.message;
                createErrorMsg.classList.remove('hidden');
            }
        });

        // Initial Fetch
        fetchFormulas();
    });
</script>
@endsection