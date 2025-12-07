@extends('layouts.main')

@section('content')
<div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">

    {{-- 🟢 HEADER & ADD BUTTON --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Daily Shop Expenses</h1>
        
        <button onclick="openModal()" 
                class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors shadow-md">
            <i class="fas fa-plus mr-2"></i> Add New Expense
        </button>
    </div>

    {{-- 🟢 DATA TABLE --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Amount (PKR)</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="expenseTableBody">
                    @forelse($expenses as $item)
                        <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($item->date)->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">
                                    {{ $item->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $item->description ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-red-600">
                                {{ number_format($item->amount, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <button onclick="editExpense({{ $item->id }})" class="text-blue-500 hover:text-blue-700 mr-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteExpense({{ $item->id }})" class="text-red-500 hover:text-red-700" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                No expenses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- 🟢 MODAL (ADD & EDIT) --}}
<div id="expenseModal" class="fixed inset-0 z-50 hidden" style="z-index: 60;">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl flex flex-col">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Add Expense</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <form id="expenseForm">
                    @csrf
                    <input type="hidden" name="id" id="editId">

                    <div class="space-y-4">
                        
                        {{-- Date & Amount --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="date" id="date" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 outline-none" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PKR)</label>
                                <input type="number" name="amount" id="amount" placeholder="0" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 outline-none font-bold text-gray-800" required>
                            </div>
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" id="category" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 outline-none bg-white">
                                <option value="Shop Rent">Shop Rent</option>
                                <option value="Electricity Bill">Electricity Bill</option>
                                <option value="Labor Salary">Labor Salary</option>
                                <option value="Bags & Packing">Bags & Packing</option>
                                <option value="Ice">Ice (Barf)</option>
                                <option value="Fuel/Transport">Fuel/Transport</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Food/Tea">Food/Tea (Refreshment)</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-xs text-gray-400">(Optional)</span></label>
                            <textarea name="description" id="description" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 outline-none resize-none" placeholder="Details e.g. Paid to Ali"></textarea>
                        </div>

                    </div>

                    {{-- Footer Buttons --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="submitBtn" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow transition-colors">Save Expense</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 🟢 SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const modal = document.getElementById('expenseModal');
    const form = document.getElementById('expenseForm');
    const submitBtn = document.getElementById('submitBtn');
    const modalTitle = document.getElementById('modalTitle');
    const editIdInput = document.getElementById('editId');

    function openModal() {
        form.reset();
        editIdInput.value = ''; 
        document.getElementById('date').value = new Date().toISOString().split('T')[0]; // Reset date to today
        modalTitle.textContent = 'Add New Expense';
        submitBtn.textContent = 'Save Expense';
        submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        submitBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }

    // Edit Function
    async function editExpense(id) {
        try {
            const response = await fetch(`/admin/expenses/${id}/edit`);
            const result = await response.json();

            if (result.success) {
                const data = result.data;
                
                document.getElementById('date').value = data.date;
                document.getElementById('amount').value = Math.floor(data.amount); // Show integer for editing convenience
                document.getElementById('category').value = data.category;
                document.getElementById('description').value = data.description || '';
                
                editIdInput.value = data.id;

                modalTitle.textContent = 'Edit Expense';
                submitBtn.textContent = 'Update Expense';
                
                // Change button color to blue for edit mode
                submitBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
                submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                
                modal.classList.remove('hidden');
            } else {
                Swal.fire('Error', 'Record not found.', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'Failed to fetch data.', 'error');
        }
    }

    // Handle Form Submit
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = editIdInput.value;
        
        let url = "{{ route('admin.expenses.store') }}";
        
        if (id) {
            url = `/admin/expenses/${id}`; 
            formData.append('_method', 'PUT'); 
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch(url, {
                method: 'POST', 
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok) {
                Swal.fire({
                    icon: 'success',
                    title: id ? 'Updated!' : 'Saved!',
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); 
                });
                closeModal();
            } else {
                Swal.fire('Error', result.message || 'Validation Failed', 'error');
            }

        } catch (error) {
            Swal.fire('Error', 'Network error occurred', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = id ? 'Update Expense' : 'Save Expense';
        }
    });

    // Delete Function
    function deleteExpense(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/expenses/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                }).then(res => {
                    if(res.ok) {
                        document.getElementById(`row-${id}`).remove();
                        Swal.fire('Deleted!', 'Expense has been deleted.', 'success');
                    } else {
                        Swal.fire('Error', 'Could not delete.', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection