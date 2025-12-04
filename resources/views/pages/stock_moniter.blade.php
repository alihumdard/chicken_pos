@extends('layouts.main')

@section('content')
    <main class="flex justify-center items-center min-h-screen p-4 sm:p-6 lg:p-10 bg-gray-100">

        <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-3xl text-center">

            <h2 class="text-xl md:text-2xl font-semibold mb-4">
                Current Shop Stock
            </h2>

            <!-- GAUGE WRAPPER -->
            <div class="relative flex justify-center">

                <svg class="w-full max-w-md" viewBox="0 0 200 100">
                    <!-- Background arc -->
                    <path d="M20 100 A80 80 0 0 1 180 100" stroke="#e5e7eb" stroke-width="18" fill="none" />

                    <!-- Green arc -->
                    <path d="M20 100 A80 80 0 0 1 180 100" stroke="#22c55e" stroke-width="18" fill="none" />

                    <!-- Needle -->
                    <line x1="100" y1="100" x2="160" y2="50" stroke="#374151" stroke-width="6"
                        transform="rotate(20 100 100)" stroke-linecap="round" />

                    <circle cx="100" cy="100" r="10" fill="#374151"></circle>



                </svg>

                <!-- Gauge value text -->
                <div class="absolute top-[55%] w-full text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-green-600"> {{ number_format($current_stock ?? 0, 0) }} KG</h1>
                    <div class="bg-gray-200 text-gray-700 px-3 py-1 mt-16 rounded-lg text-sm inline-block">
                        Available for Walk-in (Purchun)
                    </div>
                </div>

            </div>

            <!-- Bottom Text -->
            <p class="mt-10 text-gray-700 text-sm md:text-base">
                Morning Opening: <span class="font-bold">2000 KG</span> |
                Sold Today (Wholesale/Hotel): <span class="font-bold">1150 KG</span>
            </p>

        </div>

    </main>

@endsection