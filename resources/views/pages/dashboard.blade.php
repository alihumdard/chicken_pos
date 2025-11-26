@extends('layouts.main')
@section('title', 'Dashboard')

@section('content')
<div class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-100">
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 tracking-tight">Welcome, {{ auth()->user()->name ?? 'User' }}!</h1>
        <p class="text-base sm:text-lg text-gray-600 mt-1">Here's a quick look at your upcoming events.</p>
    </div>

    <div>
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-700 mb-4">Recent Events</h2>
            <div class="bg-white p-6 sm:p-8 rounded-xl shadow-lg text-center">
                <p class="text-gray-600">You have no upcoming events.</p>
                <a href="#" class="mt-4 inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 sm:px-6 sm:py-2 rounded-lg transition duration-200 text-sm sm:text-base">
                    Create an Event
                </a>
            </div>
            <div class="grid mt-5 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden transform hover:-translate-y-1 transition-transform duration-300">
                    <div class="p-4 sm:p-5">
                        <div class="flex items-center mb-3">
                            <div class="bg-orange-100 p-2 rounded-full">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="ml-3 text-xs sm:text-sm font-medium text-gray-500">
                            </p>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-800 truncate">###</h3>
                        <p class="text-xs sm:text-sm text-gray-600 mt-1">Client: ##</p>
                    </div>
                    <div class="bg-gray-50 px-4 sm:px-5 py-3">
                        <a href="#" class="text-xs sm:text-sm font-semibold text-orange-600 hover:text-orange-700">View Details &rarr;</a>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection