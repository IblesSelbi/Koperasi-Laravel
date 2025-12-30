<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - Sistem Koperasi Akeno</title>
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

                                <!-- Role Switcher -->
                                <div class="text-center mb-0">
                                    <div class="role-switch">
                                        <div class="role-slider" id="roleSlider"></div>
                                        <button type="button" class="role-btn active" id="adminBtn"
                                            onclick="switchRole('admin')">
                                            Admin / Operator
                                        </button>
                                        <button type="button" class="role-btn" id="userBtn"
                                            onclick="switchRole('user')">
                                            User / Member
                                        </button>
                                    </div>
                                </div>

                                <!-- Register Form -->
                                <div class="register-content" id="registerContent">
                                    <p class="role-label" id="roleLabel">Register Admin</p>
                                    
                                    <!-- Session Status -->
                                    <x-auth-session-status class="mb-4" :status="session('status')" />

                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf

                                        <!-- Hidden Role Field -->
                                        <input type="hidden" name="role" id="roleInput" value="admin">

                                        <!-- Name -->
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                id="name" name="name" value="{{ old('name') }}" required autofocus>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Email -->
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Password -->
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Confirm Password -->
                                        <div class="mb-4">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" class="form-control" 
                                                id="password_confirmation" name="password_confirmation" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                            Sign Up
                                        </button>

                                        <div class="d-flex align-items-center justify-content-center">
                                            <p class="fs-4 mb-0 fw-bold">Already have an Account?</p>
                                            <a class="text-primary fw-bold ms-2" href="{{ route('login') }}">Sign In</a>
                                        </div>
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
        let currentRole = 'admin';

        function switchRole(role) {
            if (role === currentRole) return;

            const content = document.getElementById('registerContent');
            const slider = document.getElementById('roleSlider');
            const adminBtn = document.getElementById('adminBtn');
            const userBtn = document.getElementById('userBtn');
            const roleLabel = document.getElementById('roleLabel');
            const roleInput = document.getElementById('roleInput');

            content.classList.add('hiding');

            setTimeout(() => {
                currentRole = role;

                if (role === 'user') {
                    slider.classList.add('user-active');
                    adminBtn.classList.remove('active');
                    userBtn.classList.add('active');
                    roleLabel.textContent = 'Register User';
                    roleInput.value = 'user';
                } else {
                    slider.classList.remove('user-active');
                    adminBtn.classList.add('active');
                    userBtn.classList.remove('active');
                    roleLabel.textContent = 'Register Admin';
                    roleInput.value = 'admin';
                }

                content.classList.remove('hiding');
            }, 200);
        }
    </script>
</body>

</html>