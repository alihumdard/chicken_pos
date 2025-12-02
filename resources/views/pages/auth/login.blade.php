<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RANA POS - Secure Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- 1. CONFIG: GLOBAL COLOR VARIABLES (Updated to your provided values) --- */
        :root {
            --primary-color: #2C394A; /* Main Blue */
            --secondary-color: color-mix(in srgb, var(--primary-color), white 35%); /* Lighter Blue */
            --secondary-bg-color: color-mix(in srgb, var(--primary-color), white 35%); /* Lighter Blue */
        }

        body { font-family: 'Poppins', sans-serif; }

        /* --- 2. BACKGROUND THEME (Using provided gradient) --- */
        .bg-theme {
            background: linear-gradient(228deg,
                    var(--secondary-bg-color) 0%,
                    var(--primary-color) 50%,
                    #2C394A 100%);
            /* Using hardcoded value from your global CSS where variable was missing */
            box-shadow: 0 0 30px #2C394A; 
        }
        
        /* --- 3. BUTTON STYLES (Using provided classes) --- */
        .btn-theme-primary {
            background: linear-gradient(228deg,
                    var(--secondary-bg-color) 0%,
                    var(--primary-color) 50%,
                    #2C394A 100%);
            background-size: 200% 200%;
            transition: background-position 0.5s ease, background-color 0.5s ease;
            color: #fff;
        }

        .btn-theme-primary:hover {
            background-position: 0 100%;
            transform: translateY(-2px);
            /* 🟢 Adjusted box-shadow to use primary color variable */
            box-shadow: 0 10px 20px -10px color-mix(in srgb, var(--primary-color), transparent 60%);
        }

        /* 🟢 Added hover class for links/icons */
        .hover-theme-primary:hover {
            color: var(--primary-color) !important;
        }

        .theme-text-primary { color: var(--primary-color); }

        /* --- 4. INPUT FOCUS STATES --- */
        .focus-theme:focus {
            outline: none;
            border-color: var(--primary-color);
            /* 🟢 Adjusted box-shadow to use primary color variable */
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--primary-color), transparent 80%);
        }

        /* Role Selection Active State */
        .role-active {
            /* 🟢 Using primary color variable */
            background-color: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
            transform: scale(1.05);
            /* 🟢 Adjusted box-shadow to use primary color variable */
            box-shadow: 0 10px 25px -5px color-mix(in srgb, var(--primary-color), transparent 60%);
        }
        
        /* Role Selection Inactive State */
        .role-inactive {
            background-color: white;
            color: #9CA3AF; /* gray-400 */
            border-color: #F3F4F6; /* gray-100 */
        }
    </style>
</head>

<body class="bg-theme min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="w-full max-w-[450px] bg-white rounded-3xl shadow-2xl px-8 py-10 z-10 relative">
        
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-slate-800 tracking-tight">
                <span class="theme-text-primary">RANA</span> POS
            </h1>
            <p class="text-xs text-gray-500 font-semibold uppercase tracking-widest mt-2">Chicken Management System</p>
        </div>

        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <input type="hidden" name="role" id="roleInput" value="admin">

            <div class="mb-8">
                <h2 class="text-center text-xs font-bold text-gray-400 uppercase mb-4">Select Login Type</h2>
                <div class="flex gap-4">
                    
                    <div id="btn-admin" onclick="selectRole('admin')" 
                          class="cursor-pointer flex-1 rounded-2xl p-4 flex flex-col items-center justify-center border-2 transition-all duration-300 role-active">
                        <i class="fas fa-crown text-2xl mb-2"></i>
                        <span class="font-bold text-sm">Owner</span>
                        <span class="text-[10px] opacity-80 mt-1">Full Access</span>
                    </div>

                    <div id="btn-manager" onclick="selectRole('manager')" 
                          class="cursor-pointer flex-1 rounded-2xl p-4 flex flex-col items-center justify-center border-2 transition-all duration-300 role-inactive hover-theme-primary">
                        {{-- 🟢 Removed hardcoded hover style --}}
                        <i class="fas fa-user-tag text-2xl mb-2"></i>
                        <span class="font-bold text-sm">Manager</span>
                        <span class="text-[10px] opacity-80 mt-1">Sales Only</span>
                    </div>

                </div>
            </div>

            @if(session('status'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm text-center">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-5 relative">
                <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Username / Email</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-envelope"></i></span>
                    <input type="text" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="Enter your ID" 
                            class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-700 font-medium focus-theme transition-all placeholder-gray-300" 
                            required />
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-red-500 font-medium ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8 relative">
                <label class="block text-xs font-bold text-gray-500 mb-1 ml-1">Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-lock"></i></span>
                    <input type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••" 
                            class="w-full pl-12 pr-12 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-700 font-medium focus-theme transition-all placeholder-gray-300" 
                            required />
                    {{-- 🟢 Applied new hover class --}}
                    <span onclick="togglePassword()" class="absolute right-4 top-3.5 text-gray-300 cursor-pointer hover-theme-primary transition-colors">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-red-500 font-medium ml-1">{{ $message }}</p>
                @enderror
                
                <div class="flex justify-end mt-2">
                    {{-- 🟢 Applied new hover class --}}
                    <a href="{{ route('password.request') }}" class="text-xs text-gray-400 hover-theme-primary transition-colors">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" class="w-full btn-theme-primary font-bold py-4 rounded-xl shadow-xl text-lg tracking-wide uppercase">
                Login System
            </button>

        </form>
    </div>

    <div class="absolute bottom-3 text-center text-white/60 text-xs">
        &copy; {{ date('Y') }} Rana Chicken Shop POS. All rights reserved.
    </div>

    <script>
        // 1. Role Selection Logic
        function selectRole(role) {
            // Update Hidden Input Value (For Backend)
            document.getElementById('roleInput').value = role;

            const btnAdmin = document.getElementById('btn-admin');
            const btnManager = document.getElementById('btn-manager');

            // Toggle Classes
            if (role === 'admin') {
                // Activate Admin
                btnAdmin.classList.add('role-active');
                btnAdmin.classList.remove('role-inactive');
                // Deactivate Manager
                btnManager.classList.add('role-inactive');
                btnManager.classList.remove('role-active');
            } else {
                // Activate Manager
                btnManager.classList.add('role-active');
                btnManager.classList.remove('role-inactive');
                // Deactivate Admin
                btnAdmin.classList.add('role-inactive');
                btnAdmin.classList.remove('role-active');
            }
        }

        // 2. Toggle Password Visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>