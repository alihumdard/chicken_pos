  <?php
  include 'includes/head.php';
  ?>

  <?php
  include 'includes/header.php';
  ?>

  <!-- Main Content -->
  <main class="container mx-auto pt-40 p-6 md:p-8 lg:p-10 md:mt-20">
    <h2 class="text-3xl font-bold text-gray-800 mb-2">Overview</h2>
    <p class="text-gray-500 mb-8">
      Real-time insights into your chicken shop operations
    </p>

    <!-- Dashboard Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <a href="/truckArival.html">
        <div
          class="bg-blue-500 rounded-2xl p-6 flex justify-between items-center shadow-lg transition-transform hover:scale-105 hover:shadow-2xl cursor-pointer">
          <div>
            <p class="text-sm text-white">Trucks Arrived</p>
            <p class="text-4xl font-bold text-white">0</p>
            <p class="text-xs text-white">+2 from yesterday</p>
          </div>
          <i class="fas fa-truck text-4xl text-white"></i>
        </div>
      </a>

      <a href="/sales.html">
        <div
          class="bg-amber-500 rounded-2xl p-6 flex justify-between items-center shadow-lg transition-transform hover:scale-105 hover:shadow-2xl cursor-pointer">
          <div>
            <p class="text-sm text-white">Sales</p>
            <p class="text-4xl font-bold text-white">0</p>
            <p class="text-xs text-white">0 deliveries</p>
          </div>
          <i class="fas fa-box-open text-4xl text-white"></i>
        </div>
      </a>

      <a href="/customer.html">
        <div
          class="bg-green-500 rounded-2xl p-6 flex justify-between items-center shadow-lg transition-transform hover:scale-105 hover:shadow-2xl cursor-pointer">
          <div>
            <p class="text-sm text-white">Customers</p>
            <p class="text-4xl font-bold text-white">0</p>
            <p class="text-xs text-white">0 transactions</p>
          </div>
          <i class="fas fa-dolly text-4xl text-white"></i>
        </div>
      </a>

      <a href="/reports.html">
        <div
          class="bg-yellow-500 rounded-2xl p-6 flex justify-between items-center shadow-lg transition-transform hover:scale-105 hover:shadow-2xl cursor-pointer">
          <div>
            <p class="text-sm text-white">Reports</p>
            <p class="text-4xl font-bold text-white">0</p>
            <p class="text-xs text-white">0 transactions</p>
          </div>
          <i class="fas fa-store text-4xl text-white"></i>
        </div>
    </div>
    </a>
    <!-- Inventory Section -->
    <div class="bg-white rounded-2xl p-8 shadow-md mb-10">
      <h3 class="text-xl font-bold text-gray-800 mb-6">
        Current Inventory Status
      </h3>
      <div class="bg-slate-50 rounded-xl p-6 flex justify-between items-center text-lg mb-6">
        <div class="flex items-center gap-2 font-semibold text-gray-700">
          <i class="fas fa-boxes"></i> Available Stock
        </div>
        <span class="text-2xl font-bold text-emerald-600">0 kg</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-blue-100 text-blue-800 p-6 rounded-xl text-center">
          <p class="text-sm font-semibold">Received Today</p>
          <p class="text-2xl font-bold mt-2">0 kg</p>
        </div>
        <div class="bg-orange-100 text-orange-800 p-6 rounded-xl text-center">
          <p class="text-sm font-semibold">Sold Today</p>
          <p class="text-2xl font-bold mt-2">0 kg</p>
        </div>
        <div class="bg-green-100 text-green-800 p-6 rounded-xl text-center">
          <p class="text-sm font-semibold">Remaining</p>
          <p class="text-2xl font-bold mt-2">0 kg</p>
        </div>
      </div>
    </div>

    <!-- Info Boxes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex items-center gap-2 text-xl font-semibold text-gray-800 mb-4">
          <i class="fas fa-truck text-blue-500"></i> Recent Truck Arrivals
        </div>
        <p class="text-gray-500 text-sm">No truck arrivals today.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="flex items-center gap-2 text-xl font-semibold text-gray-800 mb-4">
          <i class="fas fa-user-friends text-purple-500"></i> Permanent
          Customers
        </div>
        <p class="text-gray-500 text-sm">
          No permanent customers registered.
        </p>
      </div>
    </div>
  </main>


  <?php
  include 'includes/footer.php';
  ?>