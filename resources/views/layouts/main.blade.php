<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title' , 'Urban Media')</title>
    
    <style>
        /* 🟢 FIX: Scroll wapis lane ke liye overflow: auto kar diya */
        html, body {
            height: 100%;
            margin: 0;
            overflow-y: auto !important; /* Force Scroll Enabled */
        }

        /* Loader Container */
        #page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 999999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            will-change: opacity; 
            transition: opacity 0.5s ease-out;
        }

        /* Spinner Animation */
        .beautiful-spinner {
            width: 80px;
            height: 80px;
            transform: translateZ(0);
        }

        .beautiful-spinner circle {
            fill: none;
            stroke-width: 4;
            stroke-linecap: round;
        }

        .circle-1 { stroke: #e5e7eb; }

        .circle-2 {
            stroke: #2563eb;
            stroke-dasharray: 90, 150;
            stroke-dashoffset: 0;
            transform-origin: center;
            animation: beautifulSpin 1.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        
        .circle-3 {
            stroke: #06b6d4;
            stroke-dasharray: 60, 180;
            stroke-dashoffset: -40;
            transform-origin: center;
            animation: beautifulSpin 2s cubic-bezier(0.4, 0, 0.2, 1) infinite reverse;
            opacity: 0.7;
        }

        .loader-text {
            margin-top: 20px;
            background: linear-gradient(to right, #2563eb, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 3px;
            animation: pulseText 2s ease-in-out infinite;
        }

        #main-wrapper {
            opacity: 0;
            will-change: opacity;
            transition: opacity 0.7s ease-in;
        }

        @keyframes beautifulSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes pulseText {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.98); }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/css/auth-style.css') }}">
    @include('includes.head')
</head>

<body>

    {{-- LOADER --}}
    <div id="page-loader">
        <svg class="beautiful-spinner" viewBox="0 0 50 50">
            <circle class="circle-1" cx="25" cy="25" r="20"></circle>
            <circle class="circle-2" cx="25" cy="25" r="20"></circle>
             <circle class="circle-3" cx="25" cy="25" r="16"></circle>
        </svg>
        <div class="loader-text">PLEASE WAIT...</div>
    </div>

    {{-- MAIN CONTENT --}}
    <div id="main-wrapper">
        <div class="flex min-h-screen"> 
            @include('includes.sidebar')
            <div class="flex-1 flex flex-col">
                @include('includes.header') 
                @yield('content')
                @include('includes.footer')
            </div>
        </div>
        
        <div style="position: fixed; bottom: 30px; right: 30px; width: 100px; height: 100px; display: flex; flex-direction: column; align-items: center; justify-content: center; z-index: 1000;">
            <a target="_blank" href="https://wa.me/917845667204" style="text-decoration: none;">
                <div style=" background-color: #42db87; color: #fff; width: 60px; height: 60px; font-size: 30px; border-radius: 50%; text-align: center; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 0 #42db87; animation: pulsing 1.25s infinite cubic-bezier(0.66, 0, 0, 1); transition: all 300ms ease-in-out;">
                    <i class="fab fa-whatsapp"></i>
                </div>
            </a>
            <p style="margin-top: 8px; color: #ffff; font-size: 13px;">Talk to us?</p>
        </div>
    </div>

    @include('includes.script')
    @stack('scripts')
    
    <style>
    @keyframes pulsing { to { box-shadow: 0 0 0 30px rgba(66, 219, 135, 0); } }
    </style>

    {{-- 🟢 SCRIPT (Ensure Scroll is Enabled) --}}
    <script>
        (function() {
            function hideLoader() {
                const loader = document.getElementById('page-loader');
                const content = document.getElementById('main-wrapper');

                if (loader && content) {
                    loader.style.opacity = '0';
                    content.style.opacity = '1';
                    
                    // 🟢 FORCE SCROLL ENABLED
                    document.body.style.overflow = 'auto';
                    document.documentElement.style.overflow = 'auto';

                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500); 
                }
            }

            window.addEventListener('load', hideLoader);
            setTimeout(hideLoader, 3000); // Safety check: 3 second baad loader hata do

            document.addEventListener('click', function(e) {
                const target = e.target.closest('a');
                if (target) {
                    const href = target.getAttribute('href');
                    const targetAttr = target.getAttribute('target');
                    if (href && !href.startsWith('#') && !href.startsWith('javascript') && targetAttr !== '_blank') {
                        const loader = document.getElementById('page-loader');
                        if (loader) {
                            loader.style.display = 'flex';
                            requestAnimationFrame(() => {
                                loader.style.opacity = '1';
                            });
                        }
                    }
                }
            });
        })();
    </script>

</body>
</html>