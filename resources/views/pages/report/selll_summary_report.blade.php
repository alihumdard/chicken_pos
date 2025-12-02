@extends('layouts.main')

@section('content')
 <div class="flex">

        <!-- MAIN PAGE -->
        <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">
            <!-- PAGE CONTENT -->
            <div class="p-4 space-y-6">

                <!-- 1. WHOLESALE -->
                <div class="bg-white rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3">Wholesale Sales (Truck-to-Truck)</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Customer Name</th>
                                <th class="p-2 text-left">Weight (KG)</th>
                                <th class="p-2 text-left">Rate</th>
                                <th class="p-2 text-left">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b">
                                <td class="p-2">Chicken Shop</td>
                                <td class="p-2">10kg</td>
                                <td class="p-2">$50.00</td>
                                <td class="p-2">$300.00</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">KaniKittu Customers</td>
                                <td class="p-2">5kg</td>
                                <td class="p-2">$60.00</td>
                                <td class="p-2">$300.00</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">Chicken Part</td>
                                <td class="p-2">25kg</td>
                                <td class="p-2">$20.00</td>
                                <td class="p-2">$750.00</td>
                            </tr>
                            <tr class="font-semibold">
                                <td class="p-2">Total</td>
                                <td class="p-2">10kg</td>
                                <td class="p-2">$25.00</td>
                                <td class="p-2">$1,774.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. HOTEL/PERMANENT SALES -->
                <div class="bg-white rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3">Hotel/Permanent Sales</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Hotel Name</th>
                                <th class="p-2 text-left">Items Detail</th>
                                <th class="p-2 text-left">Weight</th>
                                <th class="p-2 text-left">Rate</th>
                                <th class="p-2 text-left">Total</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr class="border-b">
                                <td class="p-2">Hotel Name</td>
                                <td class="p-2">
                                    <span class="bg-blue-200 text-blue-800 px-2 py-0.5 rounded">Chest: 10kg</span>
                                    <span class="bg-orange-200 text-orange-800 px-2 py-0.5 rounded">Thigh: 5kg</span>
                                </td>
                                <td class="p-2">10kg</td>
                                <td class="p-2">$70.00</td>
                                <td class="p-2">$350.00</td>
                            </tr>

                            <tr class="border-b">
                                <td class="p-2">Permanent Hotel</td>
                                <td class="p-2">
                                    <span class="bg-blue-200 text-blue-800 px-2 py-0.5 rounded">Chest: 10kg</span>
                                    <span class="bg-orange-200 text-orange-800 px-2 py-0.5 rounded">Thigh: 5kg</span>
                                </td>
                                <td class="p-2">10kg</td>
                                <td class="p-2">$70.00</td>
                                <td class="p-2">$150.00</td>
                            </tr>

                            <tr class="font-semibold">
                                <td class="p-2">Total</td>
                                <td></td>
                                <td class="p-2">10kg</td>
                                <td class="p-2">$20.00</td>
                                <td class="p-2">$655.00</td>
                            </tr>

                        </tbody>
                    </table>

                </div>

                <!-- 3. RETAIL SALES -->
                <div class="bg-white rounded-xl shadow p-4">
                    <h2 class="font-semibold mb-3">Shop/Retail Sales (Aggregated)</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Category</th>
                                <th class="p-2 text-left">Est. Weight</th>
                                <th class="p-2 text-left">Avg Rate</th>
                                <th class="p-2 text-left">Cash Collected</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="border-b">
                                <td class="p-2">Shop Mix</td>
                                <td class="p-2">30kg</td>
                                <td class="p-2">$25.00</td>
                                <td class="p-2">$250.00</td>
                            </tr>

                            <tr class="border-b">
                                <td class="p-2">Shop Chest</td>
                                <td class="p-2">10kg</td>
                                <td class="p-2">$25.00</td>
                                <td class="p-2">$250.00</td>
                            </tr>

                            <tr class="font-semibold">
                                <td class="p-2">Total</td>
                                <td class="p-2">20kg</td>
                                <td></td>
                                <td class="p-2">$670.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- FOOTER TOTALS -->
                <div class="bg-white rounded-xl shadow p-4 font-semibold">
                    <div class="flex justify-between">
                        <div>Grand Total</div>
                        <div>Total Weight Sold 30kg</div>
                        <div>Total Revenue</div>
                    </div>
                </div>

            </div>
        </main>
    </div>


@endsection