<section>
    <header class="mb-4">
        <h5 class="fw-semibold mb-1">
            <i class="ti ti-lock text-warning"></i> Ubah Password
        </h5>
        <p class="text-muted small mb-0">
            Gunakan password yang kuat dan tidak mudah ditebak untuk keamanan akun Anda.
        </p>
    </header>

    <form method="POST" action="{{ route('admin.profile.password.update') }}" id="formPassword">
        @csrf
        @method('PUT')

        {{-- PASSWORD LAMA --}}
        <div class="mb-4">
            <label for="current_password" class="form-label fw-semibold">
                <i class="ti ti-key text-secondary"></i> Password Lama
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="ti ti-lock"></i>
                </span>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password"
                    placeholder="Masukkan password lama"
                    required
                >
                <button class="btn btn-outline-primary" type="button" onclick="togglePassword('current_password')">
                    <i class="ti ti-eye" id="icon-current_password"></i>
                </button>
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        {{-- PASSWORD BARU --}}
        <div class="mb-4">
            <label for="password" class="form-label fw-semibold">
                <i class="ti ti-lock-plus text-primary"></i> Password Baru
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="ti ti-lock"></i>
                </span>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password"
                    placeholder="Masukkan password baru"
                    required
                    minlength="8"
                >
                <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password')">
                    <i class="ti ti-eye" id="icon-password"></i>
                </button>
                @error('password', 'updatePassword')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div class="form-text">
                <i class="ti ti-info-circle"></i> Minimal 8 karakter, gunakan kombinasi huruf, angka, dan simbol
            </div>
            
            {{-- Password Strength Indicator --}}
            <div class="mt-2" id="passwordStrength" style="display: none;">
                <small class="text-muted">Kekuatan Password:</small>
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%"></div>
                </div>
                <small id="strengthText" class="text-muted"></small>
            </div>
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label fw-semibold">
                <i class="ti ti-lock-check text-success"></i> Konfirmasi Password
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light">
                    <i class="ti ti-lock"></i>
                </span>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password"
                    placeholder="Ulangi password baru"
                    required
                >
                <button class="btn btn-outline-primary" type="button" onclick="togglePassword('password_confirmation')">
                    <i class="ti ti-eye" id="icon-password_confirmation"></i>
                </button>
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <div id="passwordMatch" class="form-text"></div>
        </div>

        {{-- Divider --}}
        <hr class="my-4">

        {{-- BUTTON --}}
        <div class="d-flex align-items-center justify-content-between gap-3">
            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                <i class="ti ti-refresh"></i> Reset
            </button>
            
            <div class="d-flex align-items-center gap-3">
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success mb-0 py-2 px-3 d-flex align-items-center" role="alert">
                        <i class="ti ti-check me-2"></i>
                        <span class="small">Password berhasil diperbarui</span>
                    </div>
                @endif
                
                <button type="submit" class="btn btn-primary" id="btnSubmitPassword">
                    <i class="ti ti-device-floppy"></i> Update Password
                </button>
            </div>
        </div>
    </form>
</section>

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById('icon-' + fieldId);
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
        } else {
            field.type = 'password';
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
        }
    }

    // Password strength checker
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('passwordStrength');
        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');
        
        if (password.length === 0) {
            strengthDiv.style.display = 'none';
            return;
        }
        
        strengthDiv.style.display = 'block';
        
        let strength = 0;
        let text = '';
        let color = '';
        
        // Check password strength
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        switch(strength) {
            case 0:
            case 1:
                text = 'Lemah';
                color = 'bg-danger';
                break;
            case 2:
                text = 'Sedang';
                color = 'bg-warning';
                break;
            case 3:
                text = 'Kuat';
                color = 'bg-info';
                break;
            case 4:
                text = 'Sangat Kuat';
                color = 'bg-success';
                break;
        }
        
        strengthBar.style.width = (strength * 25) + '%';
        strengthBar.className = 'progress-bar ' + color;
        strengthText.textContent = text;
    });

    // Password match checker
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmation = this.value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (confirmation.length === 0) {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmation) {
            matchDiv.innerHTML = '<i class="ti ti-check text-success"></i> <span class="text-success">Password cocok</span>';
        } else {
            matchDiv.innerHTML = '<i class="ti ti-x text-danger"></i> <span class="text-danger">Password tidak cocok</span>';
        }
    });

    // Form submit handler
    document.getElementById('formPassword').addEventListener('submit', function(e) {
        const btnSubmit = document.getElementById('btnSubmitPassword');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
    });

    // Reset form
    function resetForm() {
        if (confirm('Reset form? Semua input akan dikosongkan.')) {
            document.getElementById('formPassword').reset();
            document.getElementById('passwordStrength').style.display = 'none';
            document.getElementById('passwordMatch').innerHTML = '';
        }
    }

    // Auto hide success message
    setTimeout(function() {
        const alert = document.querySelector('.alert-success');
        if (alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }
    }, 5000);
</script>
@endpush