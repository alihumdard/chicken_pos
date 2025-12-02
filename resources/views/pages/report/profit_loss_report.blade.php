@extends('layouts.main')

@section('content')
    <div class="flex">

        <!-- MAIN PAGE -->
        <main id="mainContent" class="flex-1 p-4 sm:p-6 lg:p-10 bg-gray-50">
            <!-- PAGE CONTENT -->
            <div class="p-4 space-y-6">

                <!-- TOP CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-500">
                        <p class="text-gray-600">Total Revenue</p>
                        <h2 class="text-2xl font-bold text-green-600">$141,500.00</h2>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-red-500">
                        <p class="text-gray-600">Cost of Goods Sold</p>
                        <h2 class="text-2xl font-bold text-red-500">$183,440.00</h2>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-yellow-500">
                        <p class="text-gray-600">Daily Expenses</p>
                        <h2 class="text-2xl font-bold text-yellow-600">$80.00</h2>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow border-l-4 border-green-600">
                        <p class="text-gray-600">NET PROFIT</p>
                        <h2 class="text-2xl font-bold text-green-700">+$10,589.73</h2>
                    </div>

                </div>

                <!-- CHART -->
                <div class="bg-white p-5 shadow rounded-xl">
                    <h3 class="font-semibold mb-3">Daily Yield Analysis (Input vs Output Weight)</h3>
                    <div class="h-64">
                        <canvas id="yieldChart"></canvas>
                    </div>
                </div>

                <!-- TABLE -->
                <div class="bg-white p-5 shadow rounded-xl overflow-x-auto">
                    <table class="w-full text-sm border">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="p-2 text-left">Date</th>
                                <th class="p-2 text-left">Total Sales</th>
                                <th class="p-2 text-left">Total Cost</th>
                                <th class="p-2 text-left">Expenses</th>
                                <th class="p-2 text-left">Net Profit</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr class="border-b">
                                <td class="p-2">07/07/2023</td>
                                <td>$6000.00</td>
                                <td class="text-red-500">-$379.00</td>
                                <td>0kg</td>
                                <td class="text-green-600">+$279.00</td>
                            </tr>

                            <tr class="border-b">
                                <td class="p-2">27/05/2023</td>
                                <td>$288.00</td>
                                <td class="text-red-500">-$60.00</td>
                                <td>-20kg</td>
                                <td class="text-green-600">+$85.53</td>
                            </tr>

                            <tr class="border-b">
                                <td class="p-2">27/05/2023</td>
                                <td>$175.00</td>
                                <td class="text-red-500">-$80.00</td>
                                <td>-10kg</td>
                                <td class="text-green-600">+$32.31</td>
                            </tr>

                        </tbody>
                    </table>
                </div>

            </div>

        </main>

    </div>

    <script>
        new Chart(document.getElementById("yieldChart"), {
            type: "bar",
            data: {
                labels: ["Input Weight", "Output Weight"],
                datasets: [
                    {
                        label: "Input Weight",
                        backgroundColor: "#3b82f6",
                        data: [1200, 0]
                    },
                    {
                        label: "Output Weight",
                        backgroundColor: "#ef4444",
                        data: [0, 1100]
                    }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // SIDEBAR TOGGLE LOGIC
        const sidebar = document.getElementById("sidebar");
        const content = document.getElementById("mainContent");
        const toggleBtn = document.getElementById("toggleSidebar");

        let sidebarOpen = true;

        toggleBtn.onclick = () => {
            if (sidebarOpen) {
                sidebar.style.left = "-220px";
                content.style.marginLeft = "0px";
            } else {
                sidebar.style.left = "0px";
                content.style.marginLeft = "220px";
            }
            sidebarOpen = !sidebarOpen;
        };

    </script>
@endsection