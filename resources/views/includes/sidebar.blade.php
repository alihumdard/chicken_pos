@include('includes.head')
@include('includes.script')

<style>
    /* --- 1. CORE THEME VARIABLES (Based on your definitions) --- */
    :root {
        --primary-color: #2C394A; /* Dark Blue/Grey Background */
        --secondary-color: color-mix(in srgb, var(--primary-color), white 20%); /* Lighter for hover/header */
        --highlight-color: #F8BC18; /* Gold/Yellow for active state */
        --text-color-primary: #f0f0f0; /* Off-White Text */
        --sub-link-hover-bg: color-mix(in srgb, var(--primary-color), black 10%);
    }
    
    /* 🟢 Set the ASIDE background to the primary color */
    .bg-sidebar-dark {
        background-color: var(--primary-color);
    }

    /* --- 2. SIDEBAR LINK STYLING --- */
    
    /* Default link style */
    .sidebar-link {
        color: var(--text-color-primary);
        padding: 0.75rem 1rem;
        border-radius: 8px; /* Rounded corners */
        transition: all 0.2s ease;
    }
    
    .sidebar-link i {
        color: var(--text-color-primary);
    }
    
    /* Hover state (Darker hover for subtle effect) */
    .sidebar-link:hover {
        background-color: color-mix(in srgb, var(--primary-color), black 10%);
        color: var(--text-color-primary) !important;
    }
    
    /* 🟢 Active/Selected link style */
    .sidebar-link.active {
        background-color: rgba(255, 255, 255, 0.08); /* Subtle white highlight */
        color: var(--text-color-primary) !important; 
        font-weight: 600;
        /* Left Border for visual indicator (like the image) */
        border-left: 5px solid var(--highlight-color); 
        padding-left: 0.75rem; /* Adjust padding due to border */
    }

    .sidebar-link.active i {
        color: var(--highlight-color) !important; /* Gold Icon */
    }

    /* --- 3. DROPDOWN SPECIFIC STYLING --- */
    
    /* Dropdown container for visual grouping */
    .dropdown-container {
        display: block;
    }

    /* Sub-links within the dropdown */
    .dropdown-link {
        color: var(--text-color-primary);
        padding: 0.5rem 1rem 0.5rem 3rem; /* Increased left padding for indentation */
        border-radius: 4px;
        transition: background-color 0.2s ease;
        display: block; /* Make the whole area clickable */
        font-size: 0.875rem; /* text-sm */
    }

    .dropdown-link:hover {
        background-color: var(--sub-link-hover-bg);
    }
    
    /* Active sub-link style */
    .dropdown-link.active-sub {
        color: var(--highlight-color);
        font-weight: 600;
        background-color: var(--sub-link-hover-bg); /* Highlight background */
    }

    /* Hide the dropdown content by default */
    .dropdown-content {
        display: none;
        padding-top: 0.25rem;
        padding-bottom: 0.25rem;
    }

    /* Style for the Reports link when it's open (to look active) */
    .sidebar-link.reports-open {
        background-color: var(--sub-link-hover-bg);
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    /* 🟢 LOGO TEXT STYLING */
    .logo-text {
        color: var(--highlight-color);
        font-weight: 700;
    }

    /* 🟢 MOBILE BUTTON STYLING (Adjusting to dark theme) */
    .mobile-menu-button {
        display: none;
        position: absolute;
        top: 1rem;
        left: 1rem;
        z-index: 60;
        padding: 0.5rem;
        border-radius: 0.375rem;
        background-color: var(--secondary-color);
        color: white;
        transition: all 0.3s ease;
    }

    .sidebar-close-button {
        display: none;
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 70;
        padding: 0.5rem;
        border-radius: 0.375rem;
        background-color: var(--secondary-color);
        color: white;
        transition: all 0.3s ease;
    }

    .sidebar-close-button:hover {
        background-color: var(--highlight-color);
        color: var(--primary-color);
    }
    
    /* 🟢 MEDIA QUERIES (Retained for mobile responsiveness) */
    @media (max-width: 768px) {
        .mobile-menu-button { display: block; }
        aside {
            position: fixed; z-index: 50; transform: translateX(-100%); transition: transform 0.3s ease-in-out; height: 100vh; top: 0; left: 0;
        }
        aside.open { transform: translateX(0); }
        aside.open .sidebar-close-button { display: block; }
        .overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 40;
        }
        .overlay.open { display: block; }
    }
</style>

{{-- 2. HTML STRUCTURE (Applying Theme Classes) --}}
<div class="flex">
    <button class="mobile-menu-button md:hidden">
        <i class="fas fa-bars"></i>
    </button>

    <div class="overlay"></div>

    {{-- Main Sidebar element: Using the dark background class --}}
    <aside class="w-64 bg-sidebar-dark border-r border-gray-200 fixed md:relative z-50 flex flex-col">
        <button class="sidebar-close-button">
            <i class="fas fa-times"></i>
        </button>

        {{-- Logo Area (RANA POS) --}}
        <div class="p-4 md:p-6 shadow-inner shrink-0" style="padding-bottom: 2rem;">
             <h2 class="text-2xl font-bold tracking-wide text-white">
                 <span class="logo-text">RANA</span> POS
             </h2>
        </div>

        {{-- Navigation Links --}}
        <nav class="mt-4 px-2 md:px-4 space-y-2 flex-1 overflow-y-auto">
            
            {{-- Example: Dashboard --}}
            <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center w-full group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home mr-4 text-base w-5 text-center"></i>
                <span class="text-sm font-medium">Dashboard</span>
            </a>
            
            {{-- Suppliers Link --}}
            <a href="{{ route('admin.contacts.index') }}" class="sidebar-link flex items-center w-full group {{ request()->routeIs('admin.contacts.*') ? 'active' : '' }}">
                <i class="fas fa-users mr-4 text-base w-5 text-center"></i>
                <span class="text-sm font-medium">Suppliers & Customers</span>
            </a>

            {{-- Purchase Link --}}
            <a href="{{ route('admin.purchases.index') }}" class="sidebar-link flex items-center w-full group {{ request()->routeIs('admin.purchases.*') ? 'active' : '' }}">
                <i class="fas fa-box-open mr-4 text-base w-5 text-center"></i> {{-- Changed icon for relevance --}}
                <span class="text-sm font-medium">Purchase & Stock</span>
            </a>

            {{-- Rates Link --}}
             <a href="{{ route('admin.rates.index') }}" class="sidebar-link flex items-center w-full group {{ request()->routeIs('admin.rates.*') ? 'active' : '' }}">
                 <i class="fas fa-percent mr-4 text-base w-5 text-center"></i> {{-- Changed icon for relevance --}}
                 <span class="text-sm font-medium">Rates</span>
             </a>

            {{-- Sales Link --}}
             <a href="{{ route('admin.sales.index') }}" class="sidebar-link flex items-center w-full group {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                 <i class="fas fa-shopping-cart mr-4 text-base w-5 text-center"></i> {{-- Changed icon for relevance --}}
                 <span class="text-sm font-medium">Sales</span>
             </a>

            <div class="dropdown-container">
                <a href="#" id="reports-toggle" class="sidebar-link flex items-center justify-between w-full group 
                    {{ request()->routeIs('admin.reports.*') ? 'active reports-open' : '' }}">
                    <div class="flex items-center">
                        <i class="fas fa-chart-line mr-4 text-base w-5 text-center"></i>
                        <span class="text-sm font-medium">Reports</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs ml-2 transition-transform duration-200 
                        {{ request()->routeIs('admin.reports.*') ? 'rotate-180' : '' }}"></i>
                </a>

                <div class="dropdown-content {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" id="reports-dropdown">
                    {{-- Sub-Link 1: Purchase Report --}}
                    <a href="" class="dropdown-link {{ request()->routeIs('admin.reports.purchases') ? 'active-sub' : '' }}">
                        Purchase Report
                    </a>
                    
                    {{-- Sub-Link 2: Sales Report --}}
                    <a href="" class="dropdown-link {{ request()->routeIs('admin.reports.sales') ? 'active-sub' : '' }}">
                        Sales Report
                    </a>
                    
                    {{-- Sub-Link 3: Stock Report --}}
                    <a href="" class="dropdown-link {{ request()->routeIs('admin.reports.stock') ? 'active-sub' : '' }}">
                        Stock Report
                    </a>
                </div>
            </div>
            </nav>
        
        {{-- Logout/Footer Area --}}
        <div class="p-4 border-t" style="border-color: rgba(255, 255, 255, 0.1);">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link flex items-center w-full text-red-400 hover:bg-red-900/20 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt mr-4 w-5 text-center"></i>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>

    </aside>
</div>

{{-- 3. JavaScript (Updated for Reports Dropdown) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.querySelector('.mobile-menu-button');
        const sidebar = document.querySelector('aside');
        const overlay = document.querySelector('.overlay');
        const closeButton = document.querySelector('.sidebar-close-button');
        
        // --- Mobile Menu Toggle Functionality ---
        function toggleSidebar() {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('open');
            menuButton.classList.toggle('hide');

            if (sidebar.classList.contains('open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        menuButton.addEventListener('click', toggleSidebar);
        closeButton.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        });

        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    // This logic is already present and good for closing on link click
                    // sidebar.classList.remove('open');
                    // overlay.classList.remove('open');
                    // document.body.style.overflow = '';
                }
            });
        });

        // --- Reports Dropdown Functionality ---
        const reportsToggle = document.getElementById('reports-toggle');
        const reportsDropdown = document.getElementById('reports-dropdown');
        const reportsIcon = reportsToggle.querySelector('.fa-chevron-down');

        // Initial state check for page load (if a report sub-link is active)
        if (reportsDropdown.classList.contains('active')) {
            reportsDropdown.style.display = 'block';
            reportsToggle.classList.add('reports-open');
        }

        reportsToggle.addEventListener('click', function(e) {
            e.preventDefault(); // Stop the link from navigating

            // Check if it's currently active (i.e., a report page is open)
            const is_active_route = reportsToggle.classList.contains('active');

            if (reportsDropdown.style.display === 'block' && !is_active_route) {
                // If open and not an active route, close it
                reportsDropdown.style.display = 'none';
                reportsIcon.classList.remove('rotate-180');
                reportsToggle.classList.remove('reports-open');
            } else {
                // If closed or an active route, open it
                reportsDropdown.style.display = 'block';
                reportsIcon.classList.add('rotate-180');
                reportsToggle.classList.add('reports-open');
            }
        });

        // Close mobile sidebar on any link click (including dropdowns)
        document.querySelectorAll('.sidebar-link, .dropdown-link').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    // Add a small delay to allow navigation before closing
                    setTimeout(() => {
                        sidebar.classList.remove('open');
                        overlay.classList.remove('open');
                        document.body.style.overflow = '';
                    }, 100); 
                }
            });
        });
    });
</script>