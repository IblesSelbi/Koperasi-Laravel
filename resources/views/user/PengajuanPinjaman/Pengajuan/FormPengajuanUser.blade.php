@extends('layouts.app')

@section('title', 'Tambah Pengajuan Pinjaman')

@push('styles')
    <style>
        .body-wrapper .beranda-user > .container-fluid {
            padding-top: 100px;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Formulir Pengajuan Pinjaman</h4>
                    <p class="text-muted fs-3 mb-0">Lengkapi form di bawah ini untuk mengajukan pinjaman</p>
                </div>
                <div>
                    <a href="{{ route('user.pengajuan.index') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Form Section -->
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="ti ti-forms me-2"></i>Form Pengajuan</h5>
                </div>
                <div class="card-body">
                    <form id="formPengajuan" method="POST" action="{{ route('user.pengajuan.store') }}">
                        @csrf

                        <!-- Jenis Pinjaman -->
                        <div class="mb-4">
                            <label for="jenis" class="form-label fw-semibold">
                                <i class="ti ti-category text-primary me-1"></i> Jenis Pinjaman
                                <span class="text-danger">*</span>
                            </label>
                            <select name="jenis" id="jenis" class="form-select" required>
                                <option value="Biasa" selected>Biasa</option>
                                <option value="Darurat">Darurat</option>
                                <option value="Barang">Barang</option>
                            </select>
                            <div class="form-text">
                                <small>
                                    <strong>Biasa:</strong> Pinjaman reguler dengan jangka waktu fleksibel<br>
                                    <strong>Darurat:</strong> Pinjaman khusus untuk kebutuhan mendesak (max 1 bulan)<br>
                                    <strong>Barang:</strong> Pinjaman untuk pembelian barang tertentu
                                </small>
                            </div>
                        </div>

                        <!-- Nominal -->
                        <div class="mb-4">
                            <label for="nominal" class="form-label fw-semibold">
                                <i class="ti ti-currency-dollar text-success me-1"></i> Nominal Pinjaman
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nominal" id="nominal" class="form-control" value="0" maxlength="20" required />
                            <div class="form-text">
                                <small>Masukkan jumlah pinjaman yang dibutuhkan (min. Rp 500.000)</small>
                            </div>
                        </div>

                        <!-- Lama Angsuran -->
                        <div class="mb-4">
                            <label for="lama_ags" class="form-label fw-semibold">
                                <i class="ti ti-calendar-time text-warning me-1"></i> Lama Angsuran
                                <span class="text-danger">*</span>
                            </label>
                            <select name="lama_ags" id="lama_ags" class="form-select" required>
                                <option value="1">1 bulan</option>
                                <option value="3">3 bulan</option>
                                <option value="6" selected>6 bulan</option>
                                <option value="12">12 bulan</option>
                                <option value="24">24 bulan</option>
                                <option value="36">36 bulan</option>
                            </select>
                            <div id="div_lama_ags"></div>
                            <div class="form-text">
                                <small>Pilih jangka waktu angsuran sesuai kemampuan Anda</small>
                            </div>
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-4">
                            <label for="keterangan" class="form-label fw-semibold">
                                <i class="ti ti-message-2 text-info me-1"></i> Keterangan / Tujuan Pinjaman
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="4" maxlength="255"
                                placeholder="Contoh: Untuk renovasi rumah, biaya pendidikan, modal usaha, dll" required></textarea>
                            <div class="form-text">
                                <small>Jelaskan tujuan penggunaan pinjaman (max. 255 karakter)</small>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex gap-2">
                            <button type="submit" id="btnSubmit" class="btn btn-primary flex-fill">
                                <i class="ti ti-send me-2"></i> Kirim Pengajuan
                            </button>
                            <button type="reset" class="btn btn-outline-secondary flex-fill">
                                <i class="ti ti-refresh me-2"></i> Reset Form
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Simulasi Section -->
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h6 class="mb-0 fw-semibold">
                        <i class="ti ti-calculator text-primary me-2"></i> Simulasi Angsuran
                    </h6>
                </div>
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <small class="text-muted">Total Pinjaman</small>
                        <h6 class="mb-0 fw-semibold" id="simTotal">Rp 0</h6>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <small class="text-muted">Jangka Waktu</small>
                        <h6 class="mb-0 fw-semibold" id="simJangka">0 Bulan</h6>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <small class="text-muted">Bunga per Bulan</small>
                        <h6 class="mb-0 fw-semibold text-warning" id="simBunga">0%</h6>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <small class="text-muted">Angsuran per Bulan</small>
                        <h6 class="mb-0 fw-semibold text-success" id="simAngsuran">Rp 0</h6>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 bg-light shadow-sm rounded-2 mt-2 px-3">
                        <span class="fw-semibold">Total yang Harus Dibayar</span>
                        <h5 class="mb-0 fw-semibold text-primary" id="simTotalBayar">Rp 0</h5>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="ti ti-info-circle me-1"></i>
                            Simulasi ini bersifat perkiraan. Perhitungan final akan ditentukan setelah pengajuan disetujui.
                        </small>
                    </div>

                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-3 shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="ti ti-info-circle text-primary me-2"></i> Informasi Penting
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="ti ti-circle-check text-success me-2"></i>
                            <small>Pastikan data yang diisi sudah benar</small>
                        </li>
                        <li class="mb-2">
                            <i class="ti ti-circle-check text-success me-2"></i>
                            <small>Pengajuan akan diproses maksimal 3 hari kerja</small>
                        </li>
                        <li class="mb-2">
                            <i class="ti ti-circle-check text-success me-2"></i>
                            <small>Anda dapat mengubah pengajuan sebelum disetujui</small>
                        </li>
                        <li class="mb-0">
                            <i class="ti ti-circle-check text-success me-2"></i>
                            <small>Notifikasi akan dikirim melalui email</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function () {
            // Format nominal saat input
            $('#nominal').on('change keyup paste', function () {
                let n = parseInt($(this).val().replace(/\D/g, ''), 10);
                if (isNaN(n)) n = 0;
                $(this).val(numberFormat(n, 0, '', '.'));
            });

            // Handle jenis pinjaman change
            $('#jenis').on('change', function () {
                handleLamaAngsuran();
                simulasikan();
            });

            // Trigger simulasi saat ada perubahan
            $('#jenis, #nominal, #lama_ags').on('change', function () {
                simulasikan();
            });

            // Initial setup
            handleLamaAngsuran();
            simulasikan();

            // Form submission
            $('#formPengajuan').on('submit', function (e) {
                e.preventDefault();

                const nominal = parseInt($('#nominal').val().replace(/\D/g, ''));
                const keterangan = $('#keterangan').val().trim();

                // Validasi
                if (nominal < 500000) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nominal Terlalu Kecil',
                        text: 'Minimal pinjaman adalah Rp 500.000',
                    });
                    return;
                }

                if (!keterangan) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Keterangan Kosong',
                        text: 'Silakan isi keterangan/tujuan pinjaman',
                    });
                    return;
                }

                // Konfirmasi
                Swal.fire({
                    title: 'Kirim Pengajuan?',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">Pastikan data sudah benar:</p>
                            <ul class="list-unstyled">
                                <li><strong>Jenis:</strong> ${$('#jenis').val()}</li>
                                <li><strong>Nominal:</strong> Rp ${$('#nominal').val()}</li>
                                <li><strong>Lama Angsuran:</strong> ${$('#lama_ags option:selected').text()}</li>
                                <li><strong>Keterangan:</strong> ${keterangan}</li>
                            </ul>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-send"></i> Ya, Kirim',
                    cancelButtonText: '<i class="ti ti-x"></i> Batal',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        confirmButton: 'btn btn-primary',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosesKirim();
                    }
                });
            });
        });

        // Number Format Function
        function numberFormat(number, decimals, decPoint, thousandsSep) {
            decimals = decimals || 0;
            number = parseFloat(number);

            if (!decPoint) decPoint = '.';
            if (!thousandsSep) thousandsSep = ',';

            const sign = number < 0 ? '-' : '';
            const intPart = String(parseInt(number = Math.abs(Number(number) || 0).toFixed(decimals)));
            const j = intPart.length > 3 ? intPart.length % 3 : 0;

            return sign +
                (j ? intPart.substr(0, j) + thousandsSep : '') +
                intPart.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousandsSep) +
                (decimals ? decPoint + Math.abs(number - intPart).toFixed(decimals).slice(2) : '');
        }

        // Handle Lama Angsuran based on Jenis
        function handleLamaAngsuran() {
            const jenis = $('#jenis').val();
            if (jenis == 'Darurat') {
                $('#lama_ags').hide();
                $('#div_lama_ags').html('<input value="1 bulan" disabled class="form-control" style="width: 150px;">');
                $('#div_lama_ags').show();
            } else {
                $('#div_lama_ags').html('');
                $('#div_lama_ags').hide();
                $('#lama_ags').show();
            }
        }

        // Simulasi Perhitungan
        function simulasikan() {
            const jenis = $('#jenis').val();
            const nominalStr = $('#nominal').val();
            const nominal = parseInt(nominalStr.replace(/\D/g, ''));
            let lamaAgs = parseInt($('#lama_ags').val());

            if (jenis == 'Darurat') {
                lamaAgs = 1;
            }

            if (isNaN(nominal) || nominal <= 0) {
                resetSimulasi();
                return;
            }

            // Perhitungan bunga
            let bungaPerBulan = 0.01; // 1%
            if (jenis == 'Darurat') {
                bungaPerBulan = 0.005; // 0.5%
            } else if (jenis == 'Barang') {
                bungaPerBulan = 0.015; // 1.5%
            }

            // Hitung total bunga
            const totalBunga = nominal * bungaPerBulan * lamaAgs;
            const totalBayar = nominal + totalBunga;
            const angsuranPerBulan = totalBayar / lamaAgs;

            // Update tampilan simulasi
            $('#simTotal').text('Rp ' + numberFormat(nominal, 0, '', '.'));
            $('#simJangka').text(lamaAgs + ' Bulan');
            $('#simBunga').text((bungaPerBulan * 100).toFixed(2) + '%');
            $('#simAngsuran').text('Rp ' + numberFormat(Math.ceil(angsuranPerBulan), 0, '', '.'));
            $('#simTotalBayar').text('Rp ' + numberFormat(Math.ceil(totalBayar), 0, '', '.'));
        }

        // Reset Simulasi
        function resetSimulasi() {
            $('#simTotal').text('Rp 0');
            $('#simJangka').text('0 Bulan');
            $('#simBunga').text('0%');
            $('#simAngsuran').text('Rp 0');
            $('#simTotalBayar').text('Rp 0');
        }

        // Proses Kirim Pengajuan
        function prosesKirim() {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu, pengajuan sedang diproses',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form
            setTimeout(() => {
                $('#formPengajuan')[0].submit();
            }, 500);
        }
    </script>
@endpush