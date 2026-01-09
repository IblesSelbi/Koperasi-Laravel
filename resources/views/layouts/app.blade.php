@php
    $user = auth()->user();
    $isAdmin = $user?->role?->nama === 'admin';
@endphp

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Koperasi Akeno</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/logoAkeno-no-name.png') }}" />

    <!-- CSS Lokal -->
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}">

    <!-- CSS Lokal DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/datatables/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/datatables/css/dataTables.buttons.bootstrap5.min.css') }}">

    <!-- Styles tambahan dari halaman lain -->
    @stack('styles')

    @if(!$isAdmin)
        <style>
            .body-wrapper.beranda-user {
                overflow-y: auto !important;
                height: 100vh !important; 
            }
            
            .body-wrapper-inner.beranda-user {
                padding-top: 20px;
            }
            
            .body-wrapper.beranda-user .container-fluid {
                max-width: 95%;
                padding-left: 32px;
                padding-right: 32px;
                padding-top: 113px;
            }
            
            /* Force header full width */
            .app-header {
                width: 100% !important;
                max-width: 100% !important;
                position: sticky !important;
                top: 65px !important;
                z-index: 100;
            }
            
            .app-header .navbar,
            .app-header .container-fluid,
            .app-header .container-sm,
            .app-header .container-md,
            .app-header .container-lg,
            .app-header .container-xl,
            .app-header .container-xxl {
                max-width: 100% !important;
            }
        </style>
    @endif
</head>

<body>

    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" 
         data-sidebartype="{{ $isAdmin ? 'full' : 'none' }}"
         data-sidebar-position="fixed" data-header-position="fixed">

        {{-- App Topstrip --}}
        <div class="app-topstrip bg-dark py-6 px-3 w-100 d-lg-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3 ms-2">
                @auth
                <a href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}"
                   class="d-flex align-items-center gap-2 text-white text-decoration-none">
                    <img src="{{ asset('assets/images/logos/akeno-white.png') }}" alt="Akeno Logo" height="37">
                    <span class="fw-semibold fs-6 lh-1">
                        Akeno <span class="fw-bold">Multimedia Solution</span>
                    </span>
                </a>
                @endauth
            </div>
        </div>

        {{-- Sidebar (hanya untuk admin) --}}
        @if($isAdmin)
            @include('layouts.sidebar')
        @endif

        {{-- Main wrapper --}}
        <div class="body-wrapper {{ !$isAdmin ? 'beranda-user' : '' }}">

            {{-- Header untuk Admin --}}
            @if($isAdmin)
                @include('layouts.header')
            @endif

            {{-- Header untuk User --}}
            @if(!$isAdmin)
                @include('layouts.UserHeader')
            @endif

            {{-- Content --}}
            <div class="body-wrapper-inner {{ !$isAdmin ? 'beranda-user' : '' }}">
                <div class="container-fluid">
                    @yield('content')

                    {{-- Footer --}}
                    <div class="py-6 px-6 text-center mt-4">
                        <p class="mb-0 fs-4">Sistem Koperasi <strong>Akeno Multimedia Solution</strong> &copy; {{ date('Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Lokal -->
    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    @if($isAdmin)
        <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
        <script src="{{ asset('assets/js/app.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    @endif

    <!-- DataTables -->
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/buttons.bootstrap5.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/sweetalert/sweetalert2.min.js') }}"></script>

    <script>
        // Update Jam Real-time
        function updateJam() {
            const now = new Date();
            const jam = now.getHours().toString().padStart(2, '0');
            const menit = now.getMinutes().toString().padStart(2, '0');
            const detik = now.getSeconds().toString().padStart(2, '0');
            const jamElement = document.getElementById('jam');
            if (jamElement) {
                jamElement.textContent = `${jam}:${menit}:${detik}`;
            }
        }
        setInterval(updateJam, 1000);
        updateJam();
    </script>

    <!-- Scripts tambahan dari halaman lain -->
    @stack('scripts')
</body>

</html>
