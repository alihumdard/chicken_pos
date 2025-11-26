@extends('layouts.main')
@section('title', 'Users')

@section('content')
<div class="w-full p-4 sm:p-6 bg-gray-50">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Registered Users</h2>
    </div>

    <div class="w-full overflow-x-auto bg-white rounded-xl shadow-lg border border-gray-200">
        <table class="min-w-full text-sm text-left text-gray-700 whitespace-nowrap">
            <thead class="text-xs uppercase text-black bg-orange-100">
                <tr>
                    <th class="px-6 py-4">Full Name</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Club</th>
                    <th class="px-6 py-4">Location</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                {{-- Style the row differently if user is soft-deleted --}}
                <tr class="border-t hover:bg-gray-50 {{ $user->trashed() ? 'bg-red-50 opacity-60' : '' }}">
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->com_name }}</td>
                    <td class="px-6 py-4">{{ $user->address }}</td>
                    <td class="px-6 py-4">
                        {{-- Show user status --}}
                        @if ($user->trashed())
                            <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Deleted</span>
                        @elseif ($user->status == 1)
                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Active</span>
                        @elseif ($user->status == 2)
                            <span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">Pending</span>
                        @else
                            <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">Deactivated</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center space-x-2">
                        @if ($user->trashed())
                            {{-- RESTORE BUTTON --}}
                            <form action="{{ route('admin.users.restore', $user->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 font-medium rounded-full">
                                    <i class="fas fa-undo"></i> <span>Restore</span>
                                </button>
                            </form>
                        @else
                            {{-- ACTIVATE/DEACTIVATE BUTTON --}}
                            <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                @if ($user->status == 1)
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 hover:bg-yellow-200 font-medium rounded-full">
                                        <i class="fas fa-times-circle"></i> <span>Deactivate</span>
                                    </button>
                                @else
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 hover:bg-green-200 font-medium rounded-full">
                                        <i class="fas fa-check-circle"></i> <span>Activate</span>
                                    </button>
                                @endif
                            </form>

                            {{-- DELETE BUTTON --}}
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 hover:bg-red-200 font-medium rounded-full">
                                    <i class="fas fa-trash-alt"></i> <span>Delete</span>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection