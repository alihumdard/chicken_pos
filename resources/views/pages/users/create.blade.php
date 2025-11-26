@extends('layouts.main')
@section('title', 'Add Club')

@section('content')
    <div class="w-full p-6 bg-gray-50">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">Club Registration</h2>
        <form action="{{ route('clubs.store') }}" method="POST" class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Club Name</label>
                    <input type="text" name="club_name" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                    <input type="text" name="location" class="w-full border border-gray-300 rounded-lg px-4 py-2" required />
                </div>
            </div>
            <div class="mt-6 text-right">
                <button type="submit" class="bg-gradient-to-r from-orange-400 to-orange-600 text-white px-6 py-2 rounded-lg font-semibold">Submit</button>
            </div>
        </form>
    </div>    
@endsection