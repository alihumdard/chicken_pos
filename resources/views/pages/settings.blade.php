@extends('layouts.main')

@section('content')

<main class="flex justify-center items-center min-h-screen p-4 sm:p-6 lg:p-10 bg-gray-100">

    <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-10 w-full max-w-5xl border border-gray-200">
        
        <h1 class="text-3xl font-extrabold text-gray-800 mb-8 tracking-tight text-center">
            System Configuration
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">

            <div class="space-y-10">

                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">
                        General Settings
                    </h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                            <input type="text" placeholder="Enter shop name"
                                   class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" placeholder="Enter address"
                                   class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="text" placeholder="Enter phone number"
                                   class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

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

            <div class="space-y-10">

                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">
                        Chicken Formula Settings
                    </h2>
                    
                    <form id="formula-settings-form">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="rate_key" class="block text-sm font-medium text-gray-700 mb-1">Select Rate Category</label>
                            <select id="rate_key" name="rate_key"
                                    class="w-full rounded-lg border-gray-300 shadow-sm p-3 bg-white focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="" disabled selected>-- Select a category to edit --</option>
                                {{-- Options will be populated by JavaScript --}}
                            </select>
                        </div>
                        
                        {{-- Formula Inputs (Initially Hidden) --}}
                        <div id="formula-inputs" class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                            
                            {{-- Multiply --}}
                            <div>
                                <label for="multiply" class="block text-sm font-medium text-gray-700 mb-1">Multiply (×)</label>
                                <input type="number" name="multiply" id="multiply" step="0.0001" value="1.0000"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                            </div>

                            {{-- Divide --}}
                            <div>
                                <label for="divide" class="block text-sm font-medium text-gray-700 mb-1">Divide (÷)</label>
                                <input type="number" name="divide" id="divide" step="0.0001" min="0.0001" value="1.0000"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                            </div>

                            {{-- Plus --}}
                            <div>
                                <label for="plus" class="block text-sm font-medium text-gray-700 mb-1">Plus (+)</label>
                                <input type="number" name="plus" id="plus" step="0.0001" value="0.0000"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                            </div>

                            {{-- Minus --}}
                            <div>
                                <label for="minus" class="block text-sm font-medium text-gray-700 mb-1">Minus (-)</label>
                                <input type="number" name="minus" id="minus" step="0.0001" min="0" value="0.0000"
                                       class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-green-500 focus:border-green-500 text-sm">
                            </div>
                            
                            <div class="col-span-2 mt-2">
                                <button type="submit" id="save-formula-btn"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg py-3 shadow-md transition">
                                    Save Formula
                                </button>
                            </div>
                            
                        </div>
                    </form>
                    
                    <p id="formula-message" class="text-sm mt-3 font-medium hidden"></p>
                    
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-l-4 border-blue-600 pl-3">
                        Data Management
                    </h2>

                    <div class="flex gap-4">
                        <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg py-3 shadow-md transition">
                            Backup Data
                        </button>

                        <button class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg py-3 shadow-md transition">
                            Reset System
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-700 border-l-4 border-blue-600 pl-3">
                            User Management
                        </h2>

                        <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow hover:bg-blue-700">
                            + User
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach (['Admin (Owner)', 'Manager'] as $role)
                            <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-200 shadow">
                                <span class="text-sm font-medium text-gray-700">{{ $role }}</span>
                                <button class="text-xs bg-white border border-gray-300 py-1 px-3 rounded-md shadow hover:bg-gray-100">
                                    Edit
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-10 border-t pt-6 flex justify-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition">
                Save All Changes
            </button>
        </div>

    </div>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rateKeySelect = document.getElementById('rate_key');
        const formulaInputsContainer = document.getElementById('formula-inputs');
        const formulaForm = document.getElementById('formula-settings-form');
        const formulaMessage = document.getElementById('formula-message');
        
        let allFormulas = {};

        /**
         * Fetches all rate keys and existing formulas from the controller.
         */
        async function fetchFormulas() {
            try {
                // Ensure the route is correctly set up: /admin/settings/rates/formulas
                const response = await fetch('{{ route('admin.rates.formulas.get') }}', { 
                    headers: { 'Accept': 'application/json' }
                });
                
                if (!response.ok) throw new Error('Failed to fetch formulas.');
                
                const data = await response.json();
                allFormulas = data.formulas;
                populateDropdown(data.friendly_names);
                
            } catch (error) {
                console.error('Error fetching rate formulas:', error);
                formulaMessage.textContent = 'Could not load rate formulas. Check console.';
                formulaMessage.classList.remove('hidden');
                formulaMessage.classList.add('text-red-600');
            }
        }

        /**
         * Populates the rate category dropdown.
         */
        function populateDropdown(friendlyNames) {
            // Remove existing options except the placeholder
            rateKeySelect.querySelectorAll('option:not([disabled])').forEach(option => option.remove());

            for (const key in friendlyNames) {
                const option = document.createElement('option');
                option.value = key;
                option.textContent = friendlyNames[key];
                rateKeySelect.appendChild(option);
            }
        }

        /**
         * Updates the formula input fields when a category is selected.
         */
        rateKeySelect.addEventListener('change', function() {
            const selectedKey = this.value;
            const formula = allFormulas[selectedKey];

            if (formula) {
                document.getElementById('multiply').value = formula.multiply;
                document.getElementById('divide').value = formula.divide;
                document.getElementById('plus').value = formula.plus;
                document.getElementById('minus').value = formula.minus;
                
                formulaInputsContainer.classList.remove('hidden');
                formulaMessage.classList.add('hidden'); // Clear messages on change
            }
        });

        /**
         * Handles the form submission to save the formula via AJAX.
         */
        formulaForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const rateKey = formData.get('rate_key');
            
            formulaMessage.classList.add('hidden');
            
            try {
                // Ensure the route is correctly set up: /admin/settings/rates/formulas
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
                    // Update the local formula data for next selection
                    allFormulas[rateKey] = {
                        multiply: formData.get('multiply'),
                        divide: formData.get('divide'),
                        plus: formData.get('plus'),
                        minus: formData.get('minus'),
                    };
                    
                    formulaMessage.textContent = result.message;
                    formulaMessage.classList.remove('hidden', 'text-red-600');
                    formulaMessage.classList.add('text-green-600');
                    
                } else {
                    const errorMessage = result.message || "An unknown error occurred on the server.";
                    formulaMessage.textContent = 'Error: ' + errorMessage;
                    formulaMessage.classList.remove('hidden', 'text-green-600');
                    formulaMessage.classList.add('text-red-600');
                }

            } catch (fetchError) {
                console.error('Fetch Error:', fetchError);
                formulaMessage.textContent = "Network or Server error during save. Please check console.";
                formulaMessage.classList.remove('hidden', 'text-green-600');
                formulaMessage.classList.add('text-red-600');
            }
        });

        // Initial fetch
        fetchFormulas();
    });
</script>

@endsection