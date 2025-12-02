<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>RANA POS - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- 1. SHARED THEME VARIABLES --- */
        :root {
            --primary-color: #2C394A;
            --secondary-color: color-mix(in srgb, var(--primary-color), white 35%);
            --secondary-bg-color: color-mix(in srgb, var(--primary-color), white 35%);
        }

        body { font-family: 'Poppins', sans-serif; }

        /* --- 2. BACKGROUND ANIMATION --- */
        .bg-theme {
            background: linear-gradient(228deg,
                    var(--secondary-bg-color) 0%,
                    var(--primary-color) 50%,
                    #2C394A 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* --- 3. BUTTONS & TEXT --- */
        .btn-theme-primary {
            background: linear-gradient(228deg,
                    var(--secondary-bg-color) 0%,
                    var(--primary-color) 50%,
                    #2C394A 100%);
            background-size: 200% 200%;
            transition: all 0.5s ease;
            color: #fff;
        }

        .btn-theme-primary:hover {
            background-position: 0 100%;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px #2C394A;
        }

        .theme-text-primary { color: var(--primary-color); }

        /* --- 4. INPUTS --- */
        .focus-theme:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px #2C394A;
        }

        /* WhatsApp Pulse Animation */
        @keyframes pulsing {
            to { box-shadow: 0 0 0 30px rgba(66, 219, 135, 0); }
        }
    </style>
</head>

<body class="bg-theme min-h-screen flex items-center justify-center p-4 relative">

    <div class="w-full max-w-[450px] bg-white rounded-3xl shadow-2xl px-8 py-10 z-10 relative">

        <div class="text-center mb-6">
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">
                <span class="theme-text-primary">RANA</span> POS
            </h1>
        </div>

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4 text-[#2C394A]">
                <i class="fas fa-lock-open text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Forgot Password?</h2>
            <p class="text-sm text-gray-500 mt-2 px-4 leading-relaxed">
                Enter your registered email address and we'll send you a secure link to reset your password.
            </p>
        </div>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-6 relative">
                <label for="email" class="block text-xs font-bold text-gray-500 mb-1 ml-1">Email Address</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400"><i class="fas fa-envelope"></i></span>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-700 font-medium focus-theme transition-all placeholder-gray-300" 
                           placeholder="you@example.com"
                           required>
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-red-500 font-medium ml-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full btn-theme-primary font-bold py-4 rounded-xl shadow-xl text-lg tracking-wide uppercase mb-6">
                Send Reset Link
            </button>
        </form>

        <div class="text-center border-t border-gray-100 pt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-semibold text-gray-400 hover:text-[#EF681A] transition-colors group">
                <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                Back to Login
            </a>
        </div>

    </div>

    <div style="position: fixed; bottom: 30px; right: 30px; width: 100px; height: 100px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1000;">
        <a target="_blank" href="https://wa.me/917845667204" style="text-decoration: none;">
            <div style="background-color: #42db87; color: #fff; width: 60px; height: 60px; font-size: 30px; border-radius: 50%; text-align: center; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 0 #42db87; animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1); transition: all 300ms ease-in-out;">
                <i class="fab fa-whatsapp"></i>
            </div>
        </a>
        <p style="margin-top: 8px; color: #ffff; font-size: 13px; font-weight: 500; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">Talk to us?</p>
    </div>

</body>

</html>