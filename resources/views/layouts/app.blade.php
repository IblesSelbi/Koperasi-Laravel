<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sistem Koperasi Akeno</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/logoAkeno-no-name.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    @stack('styles')
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        {{-- App Topstrip --}}
        <div class="app-topstrip bg-dark py-6 px-3 w-100 d-lg-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3 ms-2">
                <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-white text-decoration-none">
                    <img src="{{ asset('assets/images/logos/akeno-white.png') }}" alt="Akeno Logo" height="37">
                    <span class="fw-semibold fs-6 lh-1">
                        Akeno <span class="fw-bold">Multimedia Solution</span>
                    </span>
                </a>
            </div>
        </div>

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Main wrapper --}}
        <div class="body-wrapper">
            {{-- Header (Navbar dengan Notifikasi & Profile) --}}
            @include('layouts.header')

            {{-- Content --}}
            <div class="body-wrapper-inner">
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

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    
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
    
    @stack('scripts')
</body>

</html>