@extends('layouts.main')
@section('title', 'Edit Club')

@section('content')
<div class="w-full p-6 bg-gray-50">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Edit Club</h2>
    <form action="{{ route('clubs.update', $club) }}" method="POST" class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $club->name) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $club->email) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Club Name</label>
                <input type="text" name="club_name" value="{{ old('club_name', $club->club_name) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                <input type="text" name="location" value="{{ old('location', $club->location) }}" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
            </div>
        </div>
        <div class="mt-6 text-right">
            <button type="submit" class="bg-gradient-to-r from-orange-400 to-orange-600 text-white px-6 py-2 rounded-lg font-semibold">Update</button>
        </div>
    </form>
</div>
@endsection