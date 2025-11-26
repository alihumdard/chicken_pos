@include('includes.head')
@include('includes.script')

<style>
  .sidebar-link.active {
    background-color: #FFEDD5;
    color: #EA580C;
    font-weight: 600;
  }

  .sidebar-link.active i {
    color: #EA580C;
  }

  .mobile-menu-button.hide {
    display: none !important;
  }

  /* Mobile menu toggle */
  .mobile-menu-button {
    display: none;
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 60;
    padding: 0.5rem;
    border-radius: 0.375rem;
    background-color: #F97316;
    color: white;
    transition: all 0.3s ease;
  }

  .mobile-menu-button i {
    transition: transform 0.3s ease;
  }

  /* Close button inside sidebar */
  .sidebar-close-button {
    display: none;
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 70;
    padding: 0.5rem;
    border-radius: 0.375rem;
    background-color: #F97316;
    color: white;
    transition: all 0.3s ease;
  }

  .sidebar-close-button:hover {
    background-color: #EA580C;
  }

  @media (max-width: 768px) {
    .mobile-menu-button {
      display: block;
    }

    aside {
      position: fixed;
      z-index: 50;
      transform: translateX(-100%);
      transition: transform 0.3s ease-in-out;
      height: 100vh;
      top: 0;
      left: 0;
    }

    aside.open {
      transform: translateX(0);
    }

    aside.open .sidebar-close-button {
      display: block;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 40;
    }

    .overlay.open {
      display: block;
    }
  }
</style>

<div class="flex">
  <!-- Mobile menu button (hamburger only) -->
  <button class="mobile-menu-button md:hidden">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Overlay for mobile -->
  <div class="overlay"></div>

  <!-- Sidebar -->
  <aside class="w-64 bg-white border-r border-gray-200 fixed md:relative h-screen z-50">
    <!-- Close button inside sidebar (visible only when sidebar is open) -->
    <button class="sidebar-close-button">
      <i class="fas fa-times"></i>
    </button>

    <div class="bg-gradient-to-br from-orange-400 to-orange-600 text-white p-4 md:p-6 rounded-b-2xl shadow-inner">
      <h2 class="text-xl md:text-2xl font-bold tracking-wide">Urban Media</h2>
    </div>

    <nav class="mt-6 md:mt-8 px-2 md:px-4 space-y-1 md:space-y-2">
      @if(view_permission('index'))
      <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center w-full px-3 py-2 md:px-4 md:py-3 rounded-lg text-gray-700 hover:bg-orange-100 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fas fa-tachometer-alt mr-2 md:mr-3 text-orange-500 group-hover:text-orange-600 text-sm md:text-base"></i>
        <span class="text-sm md:text-base">Dashboard</span>
      </a>
      @endif
      @if(view_permission('users'))
      <a href="{{ route('admin.users.index') }}" class="sidebar-link flex items-center w-full px-3 py-2 md:px-4 md:py-3 rounded-lg text-gray-700 hover:bg-orange-100 hover:text-orange-600 transition-all duration-200 group {{ request()->routeIs('clubs.*') ? 'active' : '' }}">
        <i class="fas fa-users mr-2 md:mr-3 text-orange-500 group-hover:text-orange-600 text-sm md:text-base"></i>
        <span class="text-sm md:text-base">Clubs</span>
      </a>
      @endif
    </nav>
  </aside>
</div>

<script>
  // Mobile menu toggle functionality
  document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.querySelector('.mobile-menu-button');
    const sidebar = document.querySelector('aside');
    const overlay = document.querySelector('.overlay');
    const closeButton = document.querySelector('.sidebar-close-button');

    function toggleSidebar() {
      // Toggle sidebar and overlay
      sidebar.classList.toggle('open');
      overlay.classList.toggle('open');

      // Toggle hamburger visibility
      menuButton.classList.toggle('hide');

      // Prevent body scrolling when sidebar is open
      if (sidebar.classList.contains('open')) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }


    menuButton.addEventListener('click', toggleSidebar);

    closeButton.addEventListener('click', toggleSidebar);

    overlay.addEventListener('click', function() {
      // Close sidebar and overlay
      sidebar.classList.remove('open');
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    });

    // Close sidebar when clicking on links (for mobile)
    document.querySelectorAll('.sidebar-link').forEach(link => {
      link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
          sidebar.classList.remove('open');
          overlay.classList.remove('open');
          document.body.style.overflow = '';
        }
      });
    });
  });
</script>