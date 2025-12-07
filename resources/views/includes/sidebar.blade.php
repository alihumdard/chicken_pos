<style>
    :root {
        --primary-color: #2C394A;
        --secondary-color: color-mix(in srgb, var(--primary-color), white 20%);
        --highlight-color: #F8BC18;
        --text-color-primary: #f0f0f0;
        --sub-link-hover-bg: color-mix(in srgb, var(--primary-color), black 10%);
    }

    /* Custom Scrollbar for Sidebar */
    .sidebar-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .sidebar-scroll::-webkit-scrollbar-track {
        background: var(--primary-color);
    }
    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: var(--secondary-color);
        border-radius: 5px;
    }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover {
        background: var(--highlight-color);
    }

    .bg-sidebar-dark {
        background-color: var(--primary-color);
    }

    .sidebar-link {
        color: var(--text-color-primary);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        width: 100%;
        margin-bottom: 0.25rem;
    }

    .sidebar-link i {
        color: var(--text-color-primary);
        transition: color 0.2s ease;
        width: 1.5rem; /* Fixed width for icons for alignment */
        text-align: center;
        margin-right: 1rem;
    }

    .sidebar-link:hover {
        background-color: color-mix(in srgb, var(--primary-color), black 10%);
        color: var(--text-color-primary) !important;
    }

    .sidebar-link.active {
        background-color: rgba(255, 255, 255, 0.08);
        color: var(--text-color-primary) !important;
        border-left: 4px solid var(--highlight-color);
    }

    .sidebar-link.active i {
        color: var(--highlight-color) !important;
    }

    /* Dropdown Styling */
    .dropdown-container {
        display: block;
    }

    .dropdown-link {
        color: var(--text-color-primary);
        padding: 0.6rem 1rem 0.6rem 3.5rem; /* Indented */
        border-radius: 4px;
        transition: background-color 0.2s ease;
        display: block;
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .dropdown-link:hover {
        background-color: var(--sub-link-hover-bg);
        opacity: 1;
    }

    .dropdown-link.active-sub {
        color: var(--highlight-color);
        font-weight: 600;
        background-color: var(--sub-link-hover-bg);
        opacity: 1;
    }

    .dropdown-content {
        display: none; /* Hidden by default */
        background-color: rgba(0,0,0,0.1); /* Slight darken for submenu */
        border-radius: 0 0 8px 8px;
    }

    .sidebar-link.reports-open {
        background-color: var(--sub-link-hover-bg);
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    /* Mobile specific overrides */
    @media (max-width: 768px) {
        /* Overlay to darken background when menu opens */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }

        .overlay.active {
            display: block;
        }

        /* Sidebar positioning for mobile */
        aside.mobile-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 17rem; /* Slightly wider on mobile for ease of touch */
        }

        aside.mobile-sidebar.open {
            transform: translateX(0);
        }
    }
</style>

{{-- 
    Structure Note: 
    This assumes your main layout wrapper has `flex` or this component sits outside the flow.
    The button is fixed so it's always visible on mobile.
--}}

{{-- 1. Mobile Toggle Button (Visible only on small screens) --}}
<button id="mobile-menu-btn" class="md:hidden fixed top-3 left-3 z-50 bg-gray-800 text-white py-1 px-3 rounded-md shadow-lg hover:bg-gray-700 transition-colors">
    <i class="fas fa-bars text-lg"></i>
</button>

{{-- 2. Overlay (Background dimmer) --}}
<div id="sidebar-overlay" class="overlay"></div>

{{-- 3. Sidebar Container --}}
<aside id="sidebar" class="mobile-sidebar w-64 bg-sidebar-dark border-r border-gray-700 flex flex-col md:sticky md:top-0 shrink-0">
    
    {{-- Close Button (Mobile Only) --}}
    <button id="sidebar-close-btn" class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-white transition-colors">
        <i class="fas fa-times text-2xl"></i>
    </button>

    {{-- Logo / Brand Area --}}
    <div class="p-6 flex items-center justify-start border-b border-gray-700/50 shrink-0 h-20">
        @php
            $settingsModel = $globalSettings ?? null;
            if (!($settingsModel instanceof \App\Models\Setting)) {
                try {
                    $settingsModel = \App\Models\Setting::getGlobalSettings();
                } catch (\Throwable $e) {
                    $settingsModel = (object) ['shop_name' => 'RANA POS', 'logo_url' => null];
                }
            }
            $shopName = $settingsModel->shop_name ?? 'RANA POS';
            $logoUrl = $settingsModel->logo_url ?? null;
        @endphp

        @if($logoUrl)
            <img src="{{ asset($logoUrl) }}" alt="Logo" class="h-12 w-auto rounded-full mr-3">
            <span class="text-white font-bold text-lg tracking-wider">{{ $shopName }}</span>
        @else
            <h2 class="text-2xl font-bold tracking-wide text-white">
                <span style="color: var(--highlight-color);">RANA</span> POS
            </h2>
        @endif
    </div>

    {{-- Navigation Links (Scrollable Area) --}}
    <nav class="flex-1 px-3 py-10 space-y-1 overflow-y-auto sidebar-scroll">

        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <a href="{{ route('admin.sales.index') }}" class="sidebar-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
            <i class="fas fa-shopping-cart"></i>
            <span class="text-sm font-medium">Sales</span>
        </a>

        <a href="{{ route('admin.purchases.index') }}" class="sidebar-link {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
            <i class="fas fa-box-open"></i>
            <span class="text-sm font-medium">Purchase</span>
        </a>

        <a href="{{ route('admin.rates.index') }}" class="sidebar-link {{ request()->routeIs('admin.rates.*') ? 'active' : '' }}">
            <i class="fas fa-percent"></i>
            <span class="text-sm font-medium">Rates & Stock</span>
        </a>

        {{-- Reports Dropdown --}}
        <div class="dropdown-container">
            <a href="#" id="reports-toggle" class="sidebar-link justify-between {{ request()->routeIs('admin.reports.*') ? 'active reports-open' : '' }}">
                <div class="flex items-center">
                    <i class="fas fa-chart-line"></i>
                    <span class="text-sm font-medium">Reports</span>
                </div>
                <i class="fas fa-chevron-down text-xs transition-transform duration-200 {{ request()->routeIs('admin.reports.*') ? 'rotate-180' : '' }}"></i>
            </a>

            <div id="reports-dropdown" class="dropdown-content" style="{{ request()->routeIs('admin.reports.*') ? 'display: block;' : '' }}">
                <a href="{{ route('admin.reports.purchase') }}" class="dropdown-link {{ request()->routeIs('admin.reports.purchase') ? 'active-sub' : '' }}">
                    Purchase Report
                </a>
                <a href="{{ route('admin.reports.sell.summary') }}" class="dropdown-link {{ request()->routeIs('admin.reports.sell.summary') ? 'active-sub' : '' }}">
                    Sales Report
                </a>
                <a href="{{ route('admin.reports.stock') }}" class="dropdown-link {{ request()->routeIs('admin.reports.stock') ? 'active-sub' : '' }}">
                    Stock Report
                </a>
            </div>
        </div>

        <a href="{{ route('admin.contacts.index') }}" class="sidebar-link {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span class="text-sm font-medium">Suppliers & Customers</span>
        </a>

        <a href="{{ route('admin.poultry.index') }}" class="sidebar-link {{ request()->routeIs('admin.poultry.*') ? 'active' : '' }}">
            <i class="fas fa-dove"></i>
            <span class="text-sm font-medium">Poultry Management</span>
        </a>

        <a href="{{ route('admin.expenses.index') }}" class="sidebar-link {{ request()->routeIs('admin.expenses.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i>
            <span class="text-sm font-medium">Expenses</span>
        </a>

        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cogs"></i>
            <span class="text-sm font-medium">Settings</span>
        </a>

    </nav>

    {{-- Footer / Logout --}}
    <div class="p-4 border-t border-gray-700/50 bg-black/10 shrink-0">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link text-red-400 hover:bg-red-900/20 hover:text-red-300 w-full justify-start">
                <i class="fas fa-sign-out-alt text-red-400"></i>
                <span class="text-sm font-medium">Logout</span>
            </button>
        </form>
    </div>

</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Elements
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('sidebar-close-btn');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const reportsToggle = document.getElementById('reports-toggle');
        const reportsDropdown = document.getElementById('reports-dropdown');
        const reportsIcon = reportsToggle.querySelector('.fa-chevron-down');

        // --- Mobile Menu Logic ---
        function openSidebar() {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden'; // Lock body scroll
        }

        function closeSidebar() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = ''; // Unlock body scroll
        }

        // Event Listeners for Mobile Menu
        if(mobileBtn) mobileBtn.addEventListener('click', openSidebar);
        if(closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if(overlay) overlay.addEventListener('click', closeSidebar);

        // Auto-close sidebar on mobile when a link is clicked
        document.querySelectorAll('.sidebar-link, .dropdown-link').forEach(link => {
            link.addEventListener('click', function(e) {
                // If it's the toggle button, don't close, otherwise close
                if (this.id !== 'reports-toggle' && window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        // --- Reports Dropdown Toggle Logic ---
        if(reportsToggle && reportsDropdown) {
            reportsToggle.addEventListener('click', function (e) {
                e.preventDefault();
                
                const isHidden = reportsDropdown.style.display === 'none' || reportsDropdown.style.display === '';
                
                if (isHidden) {
                    // Open
                    reportsDropdown.style.display = 'block';
                    reportsIcon.classList.add('rotate-180');
                    reportsToggle.classList.add('reports-open');
                } else {
                    // Close
                    reportsDropdown.style.display = 'none';
                    reportsIcon.classList.remove('rotate-180');
                    reportsToggle.classList.remove('reports-open');
                }
            });
        }
    });
</script>