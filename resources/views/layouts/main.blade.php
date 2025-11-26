<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title' , 'Urban Media')</title>
    @include('includes.head')
</head>

<body>

    @include('includes.header')
    <div class="flex">
        @include('includes.sidebar')
        @yield('content')
    </div>
    @include('includes.footer')

    <!-- Import Js Files -->
    @include('includes.script')

    @stack('scripts')
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