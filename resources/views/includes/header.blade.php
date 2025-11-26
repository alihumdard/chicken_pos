<nav class="bg-white shadow-md px-4 sm:px-6 py-3 sm:py-4 flex flex-row items-center justify-between">
    <!-- Logo - centered on mobile, left-aligned on larger screens -->
    <div class="order-1 sm:order-none mb-2 sm:mb-0 w-full sm:w-auto text-center sm:text-left">
        <img src="/assets/images/logo.webp" alt="Logo" class="h-10 sm:h-14 w-auto mx-auto sm:mx-0" />
    </div>

    <!-- User menu - right-aligned on all screens -->
    <div class="order-2 sm:order-none relative">
        @auth
            <button id="user-menu-button" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-3 py-1 sm:py-2 rounded-lg transition duration-200">
                <i class="fas fa-user text-sm sm:text-base"></i>
            </button>

            <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border z-10">
                <div class="px-4 py-3 border-b">
                    <p class="text-sm font-semibold text-gray-800">Hello, {{ Auth::user()->name }}</p>
                </div>
                <div class="py-1">
                    <a href="#" id="logout-button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Logout
                    </a>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-3 py-1 sm:py-2 rounded-lg transition duration-200">
                <i class="fas fa-user text-sm sm:text-base"></i>
            </a>
        @endauth
    </div>
</nav>

@auth
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        const logoutButton = document.getElementById('logout-button');

        // Toggle dropdown menu
        if (userMenuButton && userMenu) {
            userMenuButton.addEventListener('click', function (event) {
                event.stopPropagation();
                userMenu.classList.toggle('hidden');
            });
        }

        // Close dropdown if clicked outside
        document.addEventListener('click', function (event) {
            if (userMenu && !userMenu.classList.contains('hidden') && 
                !userMenu.contains(event.target) && 
                (!userMenuButton || !userMenuButton.contains(event.target))) {
                userMenu.classList.add('hidden');
            }
        });

        // Handle AJAX logout
        if (logoutButton) {
            logoutButton.addEventListener('click', function (event) {
                event.preventDefault();

                fetch("{{ route('logout') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/login';
                    } else {
                        alert('Logout failed. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred during logout.');
                });
            });
        }
    });
</script>
@endauth