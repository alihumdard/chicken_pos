<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/auth-style.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

</head>

<body class="bg-theme min-h-screen flex items-center justify-center font-sans">
    <div class="w-full max-w-md p-8 bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Set New Password</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="otp" value="{{ $otp }}">
            <div class="mb-5">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                @error('password')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full btn-theme-primary text-white font-semibold py-3 rounded-lg transition duration-200">
                Reset Password
            </button>
        </form>
    </div>
    <!-- Floating WhatsApp Button-->
    <style>
        @keyframes pulsing {
            to {
                box-shadow: 0 0 0 30px rgba(66, 219, 135, 0);
            }
        }
    </style>
    <div style="position: fixed; bottom: 30px; right: 30px; width: 100px; height: 100px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1000;">
        <a target="_blank" href="https://wa.me/917845667204" style="text-decoration: none;">
            <div style=" background-color: #42db87; color: #fff; width: 60px; height: 60px; font-size: 30px; border-radius: 50%; text-align: center; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 0 #42db87; animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1); transition: all 300ms ease-in-out;">
                <i class="fab fa-whatsapp"></i>
            </div>
        </a>
        <p style="margin-top: 8px; color: #ffff; font-size: 13px;">Talk to us?</p>
    </div>


</body>

</html>