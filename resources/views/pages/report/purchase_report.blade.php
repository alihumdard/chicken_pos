@extends('layouts.main')

@section('content')
      <div class="flex">
        <!-- MAIN CONTENT -->
        <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">
            <!-- PAGE CONTENT -->
            <div class="p-4">
                <div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
                    <h2 class="font-semibold text-lg mb-4">Purchase Report</h2>

                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 text-left">Date & Supplier</th>
                                <th class="p-2 text-left">Loaded Wgt (kg)</th>
                                <th class="p-2 text-left">Dead/Shrink</th>
                                <th class="p-2 text-left">Net Live Wgt</th>
                                <th class="p-2 text-left">Rate Paid</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="border-b">
                                <td class="p-2">03/10/2022 – Supplier</td>
                                <td class="p-2">29kg</td>
                                <td class="p-2 text-red-500">-50kg</td>
                                <td class="p-2 text-green-600">232.7kg</td>
                                <td class="p-2">$35</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">03/10/2022 – Supplier</td>
                                <td class="p-2">28kg</td>
                                <td class="p-2 text-red-500">-90kg</td>
                                <td class="p-2 text-green-600">223.5kg</td>
                                <td class="p-2">$34</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">03/10/2022 – Supplier</td>
                                <td class="p-2">20kg</td>
                                <td class="p-2 text-red-500">-90kg</td>
                                <td class="p-2 text-green-600">178.7kg</td>
                                <td class="p-2">$35</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">03/10/2022 – Supplier</td>
                                <td class="p-2">20kg</td>
                                <td class="p-2 text-red-500">-50kg</td>
                                <td class="p-2 text-green-600">188.5kg</td>
                                <td class="p-2">$33</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">03/10/2022 – Supplier</td>
                                <td class="p-2">25kg</td>
                                <td class="p-2 text-red-500">-50kg</td>
                                <td class="p-2 text-green-600">268.2kg</td>
                                <td class="p-2">$30</td>
                            </tr>
                            <tr class="border-b">
                                <td class="p-2">03/10/2022 – Supplier</td>
                                <td class="p-2">22kg</td>
                                <td class="p-2 text-red-500">-50kg</td>
                                <td class="p-2 text-green-600">200.7kg</td>
                                <td class="p-2">$30</td>
                            </tr>
                            <tr class="font-semibold">
                                <td class="p-2">Total</td>
                                <td class="p-2">100kg</td>
                                <td class="p-2 text-red-500">-50kg</td>
                                <td class="p-2 text-green-600">233.5kg</td>
                                <td class="p-2">—</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </main>
    </div>


@endsection