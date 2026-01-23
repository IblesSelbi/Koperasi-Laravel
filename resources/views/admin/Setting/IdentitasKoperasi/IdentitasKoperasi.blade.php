@extends('layouts.app')

@section('title', 'Identitas Koperasi')

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Identitas Koperasi</h4>
                    <p class="text-muted fs-6 mb-0">Kelola informasi dan profil koperasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light text-white">
            <h5 class="mb-0 fw-semibold">
                <i class="ti ti-building me-2"></i>Update Data Koperasi
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('setting.identitas.update') }}" method="POST" enctype="multipart/form-data"
                id="formIdentitas">
                @csrf
                @method('PUT')

                <!-- Nama Koperasi -->
                <div class="mb-4">
                    <label for="nama_lembaga" class="form-label fw-semibold">
                        <i class="ti ti-building-community text-primary"></i> Nama Koperasi
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                        class="form-control form-control-lg @error('nama_lembaga') is-invalid @enderror" 
                        id="nama_lembaga" 
                        name="nama_lembaga"
                        value="{{ old('nama_lembaga', $identitas->nama_lembaga) }}" 
                        maxlength="255" 
                        required
                        placeholder="Masukkan nama koperasi">
                    @error('nama_lembaga')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Nama lengkap koperasi yang akan ditampilkan pada dokumen</div>
                </div>

                <!-- Nama Pimpinan -->
                <div class="mb-4">
                    <label for="nama_ketua" class="form-label fw-semibold">
                        <i class="ti ti-user-star text-success"></i> Nama Pimpinan
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                        class="form-control @error('nama_ketua') is-invalid @enderror" 
                        id="nama_ketua" 
                        name="nama_ketua"
                        value="{{ old('nama_ketua', $identitas->nama_ketua) }}" 
                        maxlength="255" 
                        required 
                        placeholder="Masukkan nama pimpinan">
                    @error('nama_ketua')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- No HP -->
                <div class="mb-4">
                    <label for="hp_ketua" class="form-label fw-semibold">
                        <i class="ti ti-phone text-info"></i> No HP Pimpinan
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                        class="form-control @error('hp_ketua') is-invalid @enderror" 
                        id="hp_ketua" 
                        name="hp_ketua"
                        value="{{ old('hp_ketua', $identitas->hp_ketua) }}" 
                        maxlength="255" 
                        required 
                        placeholder="Contoh: 081234567890">
                    @error('hp_ketua')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Nomor HP aktif yang dapat dihubungi</div>
                </div>

                <!-- Alamat -->
                <div class="mb-4">
                    <label for="alamat" class="form-label fw-semibold">
                        <i class="ti ti-map-pin text-danger"></i> Alamat Lengkap
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                        id="alamat" 
                        name="alamat" 
                        rows="3" 
                        maxlength="500" 
                        required
                        placeholder="Masukkan alamat lengkap koperasi">{{ old('alamat', $identitas->alamat) }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Telepon -->
                <div class="mb-4">
                    <label for="telepon" class="form-label fw-semibold">
                        <i class="ti ti-phone-call text-warning"></i> Telepon Kantor
                    </label>
                    <input type="text" 
                        class="form-control @error('telepon') is-invalid @enderror" 
                        id="telepon" 
                        name="telepon"
                        value="{{ old('telepon', $identitas->telepon) }}" 
                        maxlength="255" 
                        placeholder="Contoh: 021-1234567">
                    @error('telepon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kota/Kabupaten -->
                <div class="mb-4">
                    <label for="kota" class="form-label fw-semibold">
                        <i class="ti ti-location text-primary"></i> Kota/Kabupaten
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                        class="form-control @error('kota') is-invalid @enderror" 
                        id="kota" 
                        name="kota" 
                        value="{{ old('kota', $identitas->kota) }}"
                        maxlength="255" 
                        required 
                        placeholder="Masukkan nama kota/kabupaten">
                    @error('kota')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">
                        <i class="ti ti-mail text-info"></i> Email
                        <span class="text-danger">*</span>
                    </label>
                    <input type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        id="email" 
                        name="email" 
                        value="{{ old('email', $identitas->email) }}"
                        maxlength="255" 
                        required 
                        placeholder="contoh@email.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Email resmi koperasi untuk korespondensi</div>
                </div>

                <!-- Website -->
                <div class="mb-4">
                    <label for="web" class="form-label fw-semibold">
                        <i class="ti ti-world text-success"></i> Website
                    </label>
                    <input type="text" 
                        class="form-control @error('web') is-invalid @enderror" 
                        id="web" 
                        name="web" 
                        value="{{ old('web', $identitas->web) }}"
                        maxlength="255" 
                        placeholder="www.contoh.com">
                    @error('web')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Alamat website koperasi (opsional)</div>
                </div>

                <!-- Logo Upload -->
                <div class="mb-4">
                    <label for="logo" class="form-label fw-semibold">
                        <i class="ti ti-photo text-warning"></i> Logo Koperasi
                    </label>
                    <input type="file" 
                        class="form-control @error('logo') is-invalid @enderror" 
                        id="logo" 
                        name="logo" 
                        accept="image/*">
                    @error('logo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Format: JPG, PNG, atau GIF. Ukuran maksimal: 2MB
                    </div>

                    <!-- Preview Logo Saat Ini -->
                    @if($identitas->logo)
                        <div class="mt-3">
                            <div class="border rounded p-3 bg-light d-inline-block">
                                <p class="text-muted mb-2 small">Logo Saat Ini:</p>
                                <img src="{{ asset($identitas->logo) }}" 
                                    alt="Logo Koperasi" 
                                    class="img-thumbnail"
                                    style="max-height: 120px;" 
                                    id="previewLogo"
                                    onerror="this.src='{{ asset('assets/images/logos/logo-placeholder.png') }}'">
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-outline-secondary" id="btnReset">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="ti ti-device-floppy"></i> Update Data
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Info Card -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card bg-light shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="ti ti-info-circle fs-4 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1 fw-semibold">Informasi Penting</h6>
                            <p class="mb-0 text-muted small">
                                Data yang diisi di form ini akan digunakan pada laporan, kwitansi, dan dokumen resmi
                                koperasi lainnya.
                                Pastikan semua informasi yang dimasukkan sudah benar dan sesuai.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Preview Logo sebelum upload
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validasi ukuran file (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: 'Ukuran file maksimal 2MB'
                    });
                    e.target.value = '';
                    return;
                }

                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format File Tidak Valid',
                        text: 'Hanya file JPG, PNG, atau GIF yang diperbolehkan'
                    });
                    e.target.value = '';
                    return;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('previewLogo').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Form Submit Handler dengan loading
        document.getElementById('formIdentitas').addEventListener('submit', function(e) {
            const btnSubmit = document.getElementById('btnSubmit');
            const btnText = btnSubmit.innerHTML;
            
            // Disable button & show loading
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
        });

        // Form Reset Handler
        document.getElementById('btnReset').addEventListener('click', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Reset Form?',
                text: 'Semua perubahan yang belum disimpan akan hilang',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formIdentitas').reset();
                    document.getElementById('formIdentitas').classList.remove('was-validated');
                    
                    // Reset preview logo
                    @if($identitas->logo)
                        document.getElementById('previewLogo').src = '{{ asset($identitas->logo) }}';
                    @endif

                    Swal.fire({
                        icon: 'success',
                        title: 'Form Direset',
                        text: 'Form telah dikembalikan ke data awal',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Phone number validation
        document.getElementById('hp_ketua').addEventListener('input', function(e) {
            // Hanya izinkan angka dan beberapa karakter khusus
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });

        document.getElementById('telepon').addEventListener('input', function(e) {
            // Hanya izinkan angka dan beberapa karakter khusus
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endpush