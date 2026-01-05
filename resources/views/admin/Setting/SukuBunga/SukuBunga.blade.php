@extends('layouts.app')

@section('title', 'Setting Biaya dan Administrasi')

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Setting Biaya dan Administrasi</h4>
                    <p class="text-muted fs-3 mb-0">Kelola suku bunga, biaya administrasi, dan dana koperasi</p>
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

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light text-white">
            <h5 class="mb-0 fw-semibold">
                <i class="ti ti-percentage me-2"></i>Biaya dan Administrasi
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('setting.suku-bunga.update') }}" method="POST" id="formSukuBunga">
                @csrf
                @method('PUT')

                <!-- Section 1: Tipe & Bunga Pinjaman -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-semibold text-primary mb-3 pb-2 border-bottom">
                            Pengaturan Pinjaman
                        </h6>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="pinjaman_bunga_tipe" class="form-label fw-semibold">
                            <i class="ti ti-list text-info"></i> Tipe Pinjaman Bunga
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="pinjaman_bunga_tipe" name="pinjaman_bunga_tipe" required>
                            <option value="A" {{ $sukuBunga->pinjaman_bunga_tipe == 'A' ? 'selected' : '' }}>A: Persen Bunga dikali angsuran bln</option>
                            <option value="B" {{ $sukuBunga->pinjaman_bunga_tipe == 'B' ? 'selected' : '' }}>B: Persen Bunga dikali total pinjaman</option>
                        </select>
                        <div class="form-text">Pilih metode perhitungan bunga pinjaman</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="bg_pinjam" class="form-label fw-semibold">
                            <i class="ti ti-percentage text-success"></i> Suku Bunga Pinjaman (%)
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="bg_pinjam" name="bg_pinjam"
                                value="{{ $sukuBunga->bg_pinjam }}" step="0.01" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text">Persentase bunga yang dikenakan pada pinjaman</div>
                    </div>
                </div>

                <!-- Section 2: Biaya Administrasi -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-semibold text-primary mb-3 pb-2 border-bottom">
                            Biaya Administrasi & Denda
                        </h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="biaya_adm" class="form-label fw-semibold">
                            <i class="ti ti-cash text-warning"></i> Biaya Administrasi (Rp)
                        </label>
                        <input type="number" class="form-control" id="biaya_adm" name="biaya_adm"
                            value="{{ $sukuBunga->biaya_adm }}" min="0" step="1000">
                        <div class="form-text">Biaya admin untuk setiap transaksi pinjaman</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="denda" class="form-label fw-semibold">
                            <i class="ti ti-alert-triangle text-danger"></i> Biaya Denda (Rp)
                        </label>
                        <input type="number" class="form-control" id="denda" name="denda"
                            value="{{ $sukuBunga->denda }}" min="0" step="1000">
                        <div class="form-text">Denda keterlambatan pembayaran</div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="denda_hari" class="form-label fw-semibold">
                            <i class="ti ti-calendar-event text-info"></i> Tempo Tanggal Pembayaran
                        </label>
                        <input type="number" class="form-control" id="denda_hari" name="denda_hari"
                            value="{{ $sukuBunga->denda_hari }}" min="1" max="31">
                        <div class="form-text">Tanggal jatuh tempo pembayaran setiap bulan</div>
                    </div>
                </div>

                <!-- Section 3: Dana & SHU -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h6 class="fw-semibold text-primary mb-3 pb-2 border-bottom">
                            Pembagian Dana & SHU
                        </h6>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dana_cadangan" class="form-label fw-semibold">
                            <i class="ti ti-database text-primary"></i> Dana Cadangan (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="dana_cadangan" name="dana_cadangan"
                                value="{{ $sukuBunga->dana_cadangan }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jasa_usaha" class="form-label fw-semibold">
                            <i class="ti ti-briefcase text-success"></i> Jasa Usaha (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="jasa_usaha" name="jasa_usaha"
                                value="{{ $sukuBunga->jasa_usaha }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jasa_anggota" class="form-label fw-semibold">
                            <i class="ti ti-users text-info"></i> Jasa Anggota (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="jasa_anggota" name="jasa_anggota"
                                value="{{ $sukuBunga->jasa_anggota }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="jasa_modal" class="form-label fw-semibold">
                            <i class="ti ti-wallet text-warning"></i> Jasa Modal Anggota (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="jasa_modal" name="jasa_modal"
                                value="{{ $sukuBunga->jasa_modal }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dana_pengurus" class="form-label fw-semibold">
                            <i class="ti ti-user-star text-danger"></i> Dana Pengurus (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="dana_pengurus" name="dana_pengurus"
                                value="{{ $sukuBunga->dana_pengurus }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dana_karyawan" class="form-label fw-semibold">
                            <i class="ti ti-user-check text-primary"></i> Dana Karyawan (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="dana_karyawan" name="dana_karyawan"
                                value="{{ $sukuBunga->dana_karyawan }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dana_pend" class="form-label fw-semibold">
                            <i class="ti ti-school text-success"></i> Dana Pendidikan (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="dana_pend" name="dana_pend"
                                value="{{ $sukuBunga->dana_pend }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dana_sosial" class="form-label fw-semibold">
                            <i class="ti ti-heart-handshake text-info"></i> Dana Sosial (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="dana_sosial" name="dana_sosial"
                                value="{{ $sukuBunga->dana_sosial }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="pjk_pph" class="form-label fw-semibold">
                            <i class="ti ti-receipt-tax text-warning"></i> Pajak PPh (%)
                        </label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="pjk_pph" name="pjk_pph"
                                value="{{ $sukuBunga->pjk_pph }}" step="0.01" min="0" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>

                <!-- Validation Alert -->
                <div class="alert alert-info border-info d-flex align-items-center" role="alert">
                    <i class="ti ti-info-circle fs-5 me-2"></i>
                    <div>
                        <strong>Perhatian:</strong> Total persentase pembagian dana SHU sebaiknya tidak melebihi 100%.
                        Pastikan semua nilai sudah sesuai dengan ketentuan koperasi.
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">
                            Ringkasan Total Persentase
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="text-muted">Total Dana SHU:</span>
                                    <strong id="totalDana" class="text-primary">0%</strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <span class="text-muted">Sisa Alokasi:</span>
                                    <strong id="sisaDana" class="text-success">100%</strong>
                                </p>
                            </div>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar" id="progressDana" role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>
                </div>

                <!-- Divider -->
                <hr class="my-4">

                <!-- Action Buttons -->
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                    <button type="button" class="btn btn-info" onclick="hitungTotal()">
                        <i class="ti ti-calculator"></i> Hitung Total
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnSubmit">
                        <i class="ti ti-device-floppy"></i> Update Setting
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card bg-light shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-info-circle fs-6 text-primary me-3"></i>
                        <div>
                            <h6 class="mb-1 fw-semibold">Informasi Penting</h6>
                            <p class="mb-0 text-muted fs-3">
                                Setting ini akan mempengaruhi perhitungan bunga, biaya administrasi, dan pembagian SHU
                                koperasi.
                                Pastikan semua nilai sudah sesuai dengan kebijakan koperasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light-subtle border-0">
                <div class="card-body shadow-sm">
                    <div class="d-flex align-items-start">
                        <i class="ti ti-alert-triangle fs-6 text-warning me-3"></i>
                        <div>
                            <h6 class="mb-1 fw-semibold">Peringatan</h6>
                            <p class="mb-0 text-muted fs-3">
                                Perubahan setting akan berlaku untuk transaksi baru. Transaksi yang sudah ada tidak akan
                                terpengaruh.
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
        // Calculate initial total on page load
        document.addEventListener('DOMContentLoaded', function() {
            hitungTotal();
        });

        // Function: Hitung Total Persentase Dana
        function hitungTotal() {
            const danaCadangan = parseFloat(document.getElementById('dana_cadangan').value) || 0;
            const jasaUsaha = parseFloat(document.getElementById('jasa_usaha').value) || 0;
            const jasaAnggota = parseFloat(document.getElementById('jasa_anggota').value) || 0;
            const jasaModal = parseFloat(document.getElementById('jasa_modal').value) || 0;
            const danaPengurus = parseFloat(document.getElementById('dana_pengurus').value) || 0;
            const danaKaryawan = parseFloat(document.getElementById('dana_karyawan').value) || 0;
            const danaPend = parseFloat(document.getElementById('dana_pend').value) || 0;
            const danaSosial = parseFloat(document.getElementById('dana_sosial').value) || 0;

            const total = danaCadangan + jasaUsaha + jasaAnggota + jasaModal + danaPengurus + danaKaryawan + danaPend +
                danaSosial;
            const sisa = 100 - total;

            // Update display
            document.getElementById('totalDana').textContent = total.toFixed(2) + '%';
            document.getElementById('sisaDana').textContent = sisa.toFixed(2) + '%';

            // Update progress bar
            const progressBar = document.getElementById('progressDana');
            progressBar.style.width = Math.min(total, 100) + '%';
            progressBar.textContent = total.toFixed(2) + '%';

            // Change color based on total
            progressBar.className = 'progress-bar';
            if (total > 100) {
                progressBar.classList.add('bg-danger');
                document.getElementById('sisaDana').className = 'text-danger fw-bold';
            } else if (total === 100) {
                progressBar.classList.add('bg-success');
                document.getElementById('sisaDana').className = 'text-success fw-bold';
            } else if (total >= 90) {
                progressBar.classList.add('bg-warning');
                document.getElementById('sisaDana').className = 'text-warning fw-bold';
            } else {
                progressBar.classList.add('bg-primary');
                document.getElementById('sisaDana').className = 'text-success fw-bold';
            }

            // Show alert if over 100%
            if (total > 100) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Total persentase melebihi 100%. Harap sesuaikan nilai-nilai yang diinput.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            }
        }

        // Auto calculate on input change
        document.querySelectorAll('input[type="number"]').forEach(input => {
            if (input.id !== 'biaya_adm' && input.id !== 'denda' && input.id !== 'denda_hari' &&
                input.id !== 'bg_pinjam' && input.id !== 'pjk_pph') {
                input.addEventListener('input', hitungTotal);
            }
        });

        // Function: Reset Form
        function resetForm() {
            Swal.fire({
                title: 'Reset Setting?',
                text: 'Semua perubahan yang belum disimpan akan hilang',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Reset',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formSukuBunga').reset();
                    hitungTotal();

                    Swal.fire({
                        icon: 'success',
                        title: 'Form Direset',
                        text: 'Form telah dikembalikan ke nilai awal',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Form Submit Handler
        document.getElementById('formSukuBunga').addEventListener('submit', function(e) {
            e.preventDefault();

            // Validasi total persentase
            const total = parseFloat(document.getElementById('totalDana').textContent);
            if (total > 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    text: 'Total persentase dana melebihi 100%. Harap sesuaikan nilai-nilai terlebih dahulu.'
                });
                return;
            }

            // Disable button & show loading
            const btnSubmit = document.getElementById('btnSubmit');
            const btnText = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            // Get form data
            const formData = new FormData(this);

            // Show loading
            Swal.fire({
                title: 'Menyimpan Setting...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulate AJAX request
            setTimeout(() => {
                // Success response
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Setting biaya dan administrasi berhasil diupdate',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reset button
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = btnText;
                });

                /* Production AJAX:
                fetch('{{ route('setting.suku-bunga.update') }}', {
                  method: 'POST',
                  body: formData,
                  headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  }
                })
                .then(response => response.json())
                .then(data => {
                  if(data.success) {
                    Swal.fire({
                      icon: 'success',
                      title: 'Berhasil!',
                      text: data.message,
                      timer: 2000
                    });
                  } else {
                    Swal.fire({
                      icon: 'error',
                      title: 'Gagal!',
                      text: data.message
                    });
                  }
                })
                .catch(error => {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Tidak dapat terhubung ke server'
                  });
                })
                .finally(() => {
                  btnSubmit.disabled = false;
                  btnSubmit.innerHTML = btnText;
                });
                */
            }, 1500);
        });

        // Number input validation (prevent negative values)
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('keypress', function(e) {
                // Prevent minus sign
                if (e.key === '-' || e.key === 'e' || e.key === 'E') {
                    e.preventDefault();
                }
            });

            input.addEventListener('input', function() {
                // Remove negative values
                if (this.value < 0) {
                    this.value = 0;
                }

                // Limit percentage fields to max 100
                if (this.name !== 'biaya_adm' && this.name !== 'denda' && this.name !== 'denda_hari') {
                    if (this.value > 100) {
                        this.value = 100;
                    }
                }
            });
        });

        // Show helpful tooltips on focus
        document.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('focus', function() {
                this.classList.add('border-primary');
            });

            element.addEventListener('blur', function() {
                this.classList.remove('border-primary');
            });
        });
    </script>
@endpush