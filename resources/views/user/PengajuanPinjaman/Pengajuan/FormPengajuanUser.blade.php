@extends('layouts.app')

@section('title', 'Ajukan Pinjaman Baru')

@section('content')
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Ajukan Pinjaman Baru</h4>
                    <p class="text-muted fs-3 mb-0">Isi form pengajuan pinjaman dengan lengkap</p>
                </div>
                <div>
                    <a href="{{ route('user.pengajuan.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Alert -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="ti ti-info-circle me-2"></i>
        <strong>Informasi Penting:</strong>
        <ul class="mb-0 mt-2">
            <li>Pengajuan akan diproses oleh admin dalam 1-2 hari kerja</li>
            <li>Pastikan semua data yang diisi sudah benar sebelum submit</li>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-light text-white">
            <h5 class="mb-0"><i class="ti ti-file-text me-2"></i>Form Pengajuan Pinjaman</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('user.pengajuan.store') }}" method="POST" id="formPengajuan">
                @csrf

                <div class="row">
                    <!-- LEFT COLUMN -->
                    <div class="col-md-8">
                        <!-- Data Anggota -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-primary border-bottom pb-2">
                                <i class="ti ti-user me-2"></i>Data Anggota
                            </h6>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-semibold">ID Anggota</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext fw-bold" readonly
                                        value="{{ $anggota->id_anggota }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-semibold">Nama Lengkap</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext fw-bold" readonly
                                        value="{{ $anggota->nama }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-semibold">Departemen</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext" readonly
                                        value="{{ $anggota->departement }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label fw-semibold">No. Telepon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control-plaintext" readonly
                                        value="{{ $anggota->no_telp ?? '-' }}">
                                </div>
                            </div>
                        </div>

                        <!-- Data Pengajuan -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-primary border-bottom pb-2">
                                <i class="ti ti-file-invoice me-2"></i>Data Pengajuan
                            </h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Jenis Pinjaman <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="jenis" id="jenisPinjaman" required>
                                    <option value="">-- Pilih Jenis Pinjaman --</option>
                                    <option value="Biasa" {{ old('jenis') == 'Biasa' ? 'selected' : '' }}>
                                        Biasa (Pinjaman Regular)
                                    </option>
                                    <option value="Darurat" {{ old('jenis') == 'Darurat' ? 'selected' : '' }}>
                                        Darurat (Kebutuhan Mendesak)
                                    </option>
                                    <option value="Barang" {{ old('jenis') == 'Barang' ? 'selected' : '' }}>
                                        Barang (Pembelian Barang)
                                    </option>
                                </select>
                                <small class="text-muted">Pilih jenis pinjaman sesuai kebutuhan Anda</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nominal Pinjaman <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" name="nominal" id="nominal" placeholder="0"
                                        value="{{ old('nominal') }}" required>
                                </div>
                                <small class="text-muted">Minimal pinjaman Rp 500.000</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Lama Angsuran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="lama_ags" id="lamaAngsuran" required>
                                    <option value="">-- Pilih Lama Angsuran --</option>
                                    @foreach($lama_angsuran as $la)
                                        <option value="{{ $la->id }}" {{ old('lama_ags') == $la->id ? 'selected' : '' }}>
                                            {{ $la->lama_angsuran }} Bulan
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih berapa bulan Anda ingin mengangsur</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Keterangan / Tujuan Pinjaman <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" name="keterangan" id="keterangan" rows="4"
                                    placeholder="Jelaskan tujuan dan kebutuhan pinjaman Anda..."
                                    required>{{ old('keterangan') }}</textarea>
                                <small class="text-muted">Maksimal 500 karakter</small>
                            </div>
                        </div>

                        <!-- Simulasi Angsuran -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-primary border-bottom pb-2">
                                <i class="ti ti-calculator me-2"></i>Simulasi Angsuran
                            </h6>

                            <div class="alert alert-warning" id="simulasiInfo">
                                <i class="ti ti-info-circle me-2"></i>
                                Isi nominal dan lama angsuran untuk melihat simulasi
                            </div>

                            <div class="table-responsive d-none" id="simulasiTable">
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="50%">Total Pinjaman</th>
                                        <td class="fw-bold text-success" id="simTotalPinjaman">Rp 0</td>
                                    </tr>
                                    <tr>
                                        <th>Lama Angsuran</th>
                                        <td class="fw-bold" id="simLamaAngsuran">0 Bulan</td>
                                    </tr>
                                    <tr>
                                        <th>Angsuran Per Bulan</th>
                                        <td class="fw-bold text-primary" id="simAngsuranBulan">Rp 0</td>
                                    </tr>
                                    <tr class="table-light">
                                        <th>Total Pembayaran</th>
                                        <td class="fw-bold text-danger fs-5" id="simTotalBayar">Rp 0</td>
                                    </tr>
                                </table>
                                <small class="text-muted">
                                    <i class="ti ti-alert-circle"></i>
                                    Simulasi ini bersifat estimasi. Angsuran final akan ditentukan oleh admin.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="col-md-4">
                        <!-- Foto Anggota -->
                        <div class="mb-4">
                            <h6 class="fw-semibold mb-3 text-primary border-bottom pb-2">
                                <i class="ti ti-photo me-2"></i>Foto Anggota
                            </h6>
                            <div class="text-center border rounded p-3" style="min-height: 300px;">
                                <img src="{{ asset($anggota->photo_display ?? 'assets/images/profile/user-1.jpg') }}"
                                    alt="Foto {{ $anggota->nama }}" class="img-fluid rounded shadow-sm"
                                    style="max-height: 280px;"
                                    onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h6 class="fw-semibold mb-3">
                                    <i class="ti ti-bulb text-warning me-2"></i>Tips Pengajuan
                                </h6>
                                <ul class="small mb-0 ps-3">
                                    <li class="mb-2">Pastikan nominal sesuai kebutuhan</li>
                                    <li class="mb-2">Pilih tenor yang sesuai kemampuan bayar</li>
                                    <li class="mb-2">Jelaskan tujuan pinjaman dengan jelas</li>
                                    <li class="mb-2">Cek kembali sebelum submit</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex gap-2 justify-content-end border-top pt-3">
                            <a href="{{ route('user.pengajuan.index') }}" class="btn btn-secondary">
                                <i class="ti ti-x"></i> Batal
                            </a>
                            <button type="reset" class="btn btn-warning">
                                <i class="ti ti-refresh"></i> Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnSubmit">
                                <i class="ti ti-send"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Format Rupiah Input
            $('#nominal').on('input', function () {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    $(this).val(parseInt(value).toLocaleString('id-ID'));
                }
                hitungSimulasi();
            });

            // Update simulasi saat lama angsuran berubah
            $('#lamaAngsuran').on('change', function () {
                hitungSimulasi();
            });

            // Hitung Simulasi Angsuran
            function hitungSimulasi() {
                const nominalStr = $('#nominal').val().replace(/[^0-9]/g, '');
                const nominal = parseInt(nominalStr) || 0;
                const lamaAngsuran = parseInt($('#lamaAngsuran option:selected').text()) || 0;

                if (nominal > 0 && lamaAngsuran > 0) {
                    const angsuranBulan = Math.ceil(nominal / lamaAngsuran);
                    const totalBayar = angsuranBulan * lamaAngsuran;

                    // Update simulasi
                    $('#simTotalPinjaman').text('Rp ' + nominal.toLocaleString('id-ID'));
                    $('#simLamaAngsuran').text(lamaAngsuran + ' Bulan');
                    $('#simAngsuranBulan').text('Rp ' + angsuranBulan.toLocaleString('id-ID'));
                    $('#simTotalBayar').text('Rp ' + totalBayar.toLocaleString('id-ID'));

                    // Show table, hide info
                    $('#simulasiInfo').addClass('d-none');
                    $('#simulasiTable').removeClass('d-none');
                } else {
                    // Hide table, show info
                    $('#simulasiInfo').removeClass('d-none');
                    $('#simulasiTable').addClass('d-none');
                }
            }

            // Validasi sebelum submit
            $('#formPengajuan').on('submit', function (e) {
                const nominalStr = $('#nominal').val().replace(/[^0-9]/g, '');
                const nominal = parseInt(nominalStr) || 0;

                if (nominal < 500000) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Nominal Tidak Valid',
                        text: 'Minimal pinjaman adalah Rp 500.000',
                        confirmButtonColor: '#dc3545'
                    });
                    return false;
                }

                // Konfirmasi submit
                e.preventDefault();
                Swal.fire({
                    title: 'Konfirmasi Pengajuan',
                    html: `
                        <div class="text-start">
                            <p class="mb-2">Pastikan data sudah benar:</p>
                            <ul>
                                <li><strong>Jenis:</strong> ${$('#jenisPinjaman option:selected').text()}</li>
                                <li><strong>Nominal:</strong> Rp ${nominal.toLocaleString('id-ID')}</li>
                                <li><strong>Lama:</strong> ${$('#lamaAngsuran option:selected').text()}</li>
                            </ul>
                            <p class="mb-0 text-danger"><small>Data yang sudah dikirim tidak dapat diubah kecuali masih dalam status pending.</small></p>
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
                        // Show loading
                        Swal.fire({
                            title: 'Mengirim Pengajuan...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            willOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        this.submit();
                    }
                });
            });

            // Reset form handler
            $('button[type="reset"]').on('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Reset Form?',
                    text: 'Semua data yang telah diisi akan dihapus',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#ffc107'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#formPengajuan')[0].reset();
                        $('#simulasiInfo').removeClass('d-none');
                        $('#simulasiTable').addClass('d-none');
                        Swal.fire({
                            icon: 'success',
                            title: 'Form Direset',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }
                });
            });
        });
    </script>
@endpush