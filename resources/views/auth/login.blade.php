<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Koperasi Akeno</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/logoAkeno-no-name.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body shadow-sm">
                                <a href="#" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="{{ asset('assets/images/logos/logoAkeno-name.png') }}" alt="Logo"
                                        style="width: auto; height: 80px;">
                                </a>

                                @php
                                    $selectedRole = old('switch_role', 'admin');
                                @endphp

                                <!-- Role Switcher -->
                                <div class="text-center mb-0">
                                    <div class="role-switch">
                                        <div class="role-slider {{ $selectedRole === 'user' ? 'user-active' : '' }}" id="roleSlider"></div>
                                        <button type="button" class="role-btn {{ $selectedRole === 'admin' ? 'active' : '' }}" id="adminBtn"
                                            onclick="switchRole('admin')">
                                            Admin / Operator
                                        </button>
                                        <button type="button" class="role-btn {{ $selectedRole === 'user' ? 'active' : '' }}" id="userBtn"
                                            onclick="switchRole('user')">
                                            User / Member
                                        </button>
                                    </div>
                                </div>

                                <!-- Login Form -->
                                <div class="login-content" id="loginContent">
                                    <p class="role-label" id="roleLabel">
                                        {{ $selectedRole === 'user' ? 'User / Member Login' : 'Admin / Operator Login' }}
                                    </p>
                                    
                                    <!-- Session Status -->
                                    <x-auth-session-status class="mb-4" :status="session('status')" />

                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf

                                        <!-- Hidden Role Field -->
                                        <input type="hidden" name="switch_role" id="switchRoleInput" value="{{ $selectedRole }}">

                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                id="email" name="email" value="{{ old('email') }}" required autofocus>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="mb-4">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input primary" type="checkbox" 
                                                    id="remember_me" name="remember">
                                                <label class="form-check-label text-dark" for="remember_me">
                                                    Remember this Device
                                                </label>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <a class="text-primary fw-bold" href="{{ route('password.request') }}">
                                                    Forgot Password?
                                                </a>
                                            @endif
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                            Sign In
                                        </button>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

    <script>
        let currentRole = "{{ $selectedRole }}";

        function switchRole(role) {
            if (role === currentRole) return;

            const content = document.getElementById('loginContent');
            const slider = document.getElementById('roleSlider');
            const adminBtn = document.getElementById('adminBtn');
            const userBtn = document.getElementById('userBtn');
            const roleLabel = document.getElementById('roleLabel');
            const switchInput = document.getElementById('switchRoleInput');

            content.classList.add('hiding');

            setTimeout(() => {
                currentRole = role;

                if (role === 'user') {
                    slider.classList.add('user-active');
                    adminBtn.classList.remove('active');
                    userBtn.classList.add('active');
                    roleLabel.textContent = 'User / Member Login';
                    switchInput.value = 'user';
                } else {
                    slider.classList.remove('user-active');
                    adminBtn.classList.add('active');
                    userBtn.classList.remove('active');
                    roleLabel.textContent = 'Admin / Operator Login';
                    switchInput.value = 'admin';
                }

                content.classList.remove('hiding');
            }, 200);
        }
    </script>

</body>

</html>
