@extends('layouts.main')

@section('content')
<div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">

    {{-- 🟢 HEADER & ADD BUTTON --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Poultry Management</h1>
        
        <button onclick="openModal()" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg flex items-center transition-colors shadow-md">
            <i class="fas fa-plus mr-2"></i> Add Poultry Detail
        </button>
    </div>

    {{-- 🟢 DATA TABLE --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Weight</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Avg Weight</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Cost</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="poultryTableBody">
                    @forelse($poultries as $item)
                        <tr id="row-{{ $item->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($item->entry_date)->format('d M, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $item->batch_no ?? '-' }}
                                @if($item->description)
                                    <span class="block text-xs text-gray-400 truncate w-32">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                {{ number_format($item->total_weight, 2) }} KG
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">
                                @if($item->quantity > 0)
                                    {{ number_format($item->total_weight / $item->quantity, 2) }} KG
                                @else
                                    0
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-green-600">
                                {{ number_format($item->cost_price, 0) }} PKR
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                {{-- 🟢 EDIT BUTTON --}}
                                <button onclick="editPoultry({{ $item->id }})" class="text-blue-500 hover:text-blue-700 mr-3" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                {{-- 🔴 DELETE BUTTON --}}
                                <button onclick="deletePoultry({{ $item->id }})" class="text-red-500 hover:text-red-700" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- 🟢 MODAL (ADD & EDIT) --}}
<div id="poultryModal" class="fixed inset-0 z-50 hidden" style="z-index: 60;">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
    
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-2xl flex flex-col">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-800" id="modalTitle">Add Poultry Detail</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <form id="poultryForm">
                    @csrf
                    {{-- Hidden ID for Edit Mode --}}
                    <input type="hidden" name="id" id="editId">

                    <div class="space-y-4">
                        
                        {{-- Date --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date</label>
                            <input type="date" name="entry_date" id="entry_date" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" value="{{ date('Y-m-d') }}" required>
                        </div>

                        {{-- Batch No & Quantity --}}
                        <div class=" gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buyer Name</label>
                                <input type="text" name="batch_no" id="batch_no" placeholder="Enter buyer name" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        {{-- Weight & Cost --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Weight (KG)</label>
                                <input type="number" step="0.01" name="total_weight" id="total_weight" placeholder="0.00" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Cost (PKR)</label>
                                <input type="number" step="0.01" name="cost_price" id="cost_price" placeholder="0.00" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none font-bold text-gray-800" required>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description/Notes</label>
                            <textarea name="description" id="description" rows="2" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none resize-none" placeholder="Optional details..."></textarea>
                        </div>

                    </div>

                    {{-- Footer Buttons --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" id="submitBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition-colors">Save Details</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 🟢 SCRIPTS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const modal = document.getElementById('poultryModal');
    const form = document.getElementById('poultryForm');
    const submitBtn = document.getElementById('submitBtn');
    const modalTitle = document.getElementById('modalTitle');
    const editIdInput = document.getElementById('editId');

    // Open Modal for NEW Record
    function openModal() {
        form.reset();
        editIdInput.value = ''; // Clear ID
        modalTitle.textContent = 'Add Poultry Detail';
        submitBtn.textContent = 'Save Details';
        modal.classList.remove('hidden');
    }

    // Open Modal for EDITING Record
    async function editPoultry(id) {
        try {
            const response = await fetch(`/admin/poultry/${id}/edit`);
            const result = await response.json();

            if (result.success) {
                const data = result.data;
                
                // Populate fields
                document.getElementById('entry_date').value = data.entry_date;
                document.getElementById('batch_no').value = data.batch_no || '';
                document.getElementById('quantity').value = data.quantity;
                document.getElementById('total_weight').value = data.total_weight;
                document.getElementById('cost_price').value = data.cost_price;
                document.getElementById('description').value = data.description || '';
                
                // Set Hidden ID
                editIdInput.value = data.id;

                // Change UI Text
                modalTitle.textContent = 'Edit Poultry Detail';
                submitBtn.textContent = 'Update Details';
                
                modal.classList.remove('hidden');
            } else {
                Swal.fire('Error', 'Record not found.', 'error');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Failed to fetch data.', 'error');
        }
    }

    function closeModal() {
        modal.classList.add('hidden');
        form.reset();
    }

    // Handle Form Submit (Add OR Edit logic)
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = editIdInput.value;
        
        // Determine URL and Method based on whether we have an ID
        let url = "{{ route('admin.poultry.store') }}";
        let method = 'POST';

        if (id) {
            url = `/admin/poultry/${id}`; // Update URL
            formData.append('_method', 'PUT'); // Laravel requires this for PUT requests via FormData
        }

        submitBtn.disabled = true;
        submitBtn.textContent = id ? 'Updating...' : 'Saving...';

        try {
            const response = await fetch(url, {
                method: 'POST', // Always POST when using FormData with _method override
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
            submitBtn.textContent = id ? 'Update Details' : 'Save Details';
        }
    });

    // Delete Function (Same as before)
    function deletePoultry(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/admin/poultry/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Content-Type': 'application/json'
                    }
                }).then(res => {
                    if(res.ok) {
                        document.getElementById(`row-${id}`).remove();
                        Swal.fire('Deleted!', 'Record has been deleted.', 'success');
                    } else {
                        Swal.fire('Error', 'Could not delete.', 'error');
                    }
                });
            }
        });
    }
</script>
@endsection