<section>
    <header class="mb-4">
        <h5 class="fw-semibold mb-1">
            <i class="ti ti-user text-primary"></i> Informasi Profil
        </h5>
        <p class="text-muted small mb-0">
            Perbarui nama dan alamat email akun Anda.
        </p>
    </header>

    {{-- Resend email verification --}}
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('user.profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- NAMA --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">
                <i class="ti ti-user"></i> Nama Lengkap
                <span class="text-danger">*</span>
            </label>
            <input
                type="text"
                name="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}"
                placeholder="Masukkan nama lengkap"
                required
            >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- EMAIL --}}
        <div class="mb-4">
            <label class="form-label fw-semibold">
                <i class="ti ti-mail"></i> Email
                <span class="text-danger">*</span>
            </label>
            <input
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}"
                placeholder="user@example.com"
                required
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2 py-2 small">
                    <i class="ti ti-alert-circle"></i>
                    Email belum diverifikasi.
                    <button form="send-verification" class="btn btn-link btn-sm p-0">
                        Kirim ulang verifikasi
                    </button>
                </div>

                @if (session('status') === 'verification-link-sent')
                    <div class="alert alert-success mt-2 py-2 small">
                        <i class="ti ti-check"></i>
                        Link verifikasi sudah dikirim ke email Anda.
                    </div>
                @endif
            @endif
        </div>

        {{-- Divider --}}
        <hr class="my-4">

        {{-- BUTTON --}}
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success mb-0 py-2 px-3 d-inline-flex align-items-center">
                        <i class="ti ti-check me-2"></i>
                        <span class="small">Profil berhasil diperbarui</span>
                    </div>
                @endif
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="ti ti-device-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</section>

@push('scripts')
<script>
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