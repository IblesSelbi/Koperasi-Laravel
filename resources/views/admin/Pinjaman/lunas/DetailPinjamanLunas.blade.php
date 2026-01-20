@extends('layouts.app')

@section('title', 'Detail Pinjaman Lunas')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .table-borderless td {
            padding: 0.35rem 0.5rem;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-semibold mb-1">Detail Pinjaman Lunas</h4>
                    <p class="text-muted fs-3 mb-0">Kode Lunas: <strong class="text-success">{{ $pinjaman->kode }}</strong>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('pinjaman.lunas') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-primary" onclick="cetakDetail({{ $pinjaman->id }})">
                        <i class="ti ti-printer"></i> Cetak Detail
                    </button>

                    @if(auth()->user()->role_id == 1)
                        <button class="btn btn-danger" onclick="batalkanLunas({{ $pinjaman->id }})">
                            <i class="ti ti-x-circle"></i> Batalkan Pelunasan
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check-circle me-2"></i>
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

    <!-- Alert Info -->
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ti ti-check-circle me-2"></i>
        <strong>Status: LUNAS</strong> - Pinjaman ini sudah dilunasi seluruhnya
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Detail Pinjaman Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary-subtle">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-dark"><i class="ti ti-file-text me-2"></i>Detail Pinjaman</h6>
                <button class="btn btn-sm btn-light shadow-sm" data-bs-toggle="collapse" data-bs-target="#detailPinjaman">
                    <i class="ti ti-minus"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="detailPinjaman">
            <div class="card-body p-4">
                <div class="row">
                    <!-- Foto Anggota -->
                    <div class="col-md-2 text-center mb-3 mt-4 mb-md-0">
                        <img src="{{ asset($pinjaman->anggota_foto) }}" class="rounded border shadow-sm" width="130"
                            height="150" alt="Foto Anggota"
                            onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                    </div>

                    <!-- Data Anggota -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="p-3">
                            <h6 class="text-success fw-semibold mb-3 pb-2 border-bottom border-success">
                                <i class="ti ti-user-circle me-1"></i>Data Anggota
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">ID Anggota</td>
                                    <td style="width: 5%;">:</td>
                                    <td><strong>{{ $pinjaman->anggota_id }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->anggota_nama }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dept</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota_departemen }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">TTL</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota_ttl }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kota Tinggal</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota_kota }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Data Pinjaman -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-3">
                            <h6 class="text-success fw-semibold mb-3 pb-2 border-bottom border-success">
                                <i class="ti ti-wallet me-1"></i>Data Pinjaman
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 45%;">Kode Lunas</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success fw-semibold px-2 py-1">
                                            {{ $pinjaman->kode }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Pinjam</td>
                                    <td>:</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->translatedFormat('d F Y') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Tempo</td>
                                    <td>:</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_tempo)->translatedFormat('d F Y') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lama Pinjaman</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $pinjaman->lama_pinjaman }}
                                            Bulan</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Rincian Angsuran -->
                    <div class="col-md-3">
                        <div class="p-3">
                            <h6 class="text-success fw-semibold mb-3 pb-2 border-bottom border-success">
                                <i class="ti ti-calculator me-1"></i>Rincian Angsuran
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Pokok Pinjaman</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Angsuran Pokok</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Biaya & Bunga</td>
                                    <td>:</td>
                                    <td class="text-end"><strong
                                            class="text-info">{{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-muted fw-semibold pt-2">Jumlah Angsuran</td>
                                    <td class="pt-2">:</td>
                                    <td class="text-end pt-2">
                                        <strong
                                            class="text-success fs-6">{{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Pembayaran -->
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-coins"></i> Total Dibayar
                        </p>
                        <span class="fw-semibold fs-5 text-success">
                            Rp {{ number_format($pinjaman->sudah_dibayar, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-alert-triangle"></i> Denda
                        </p>
                        <span class="fw-semibold fs-5 text-danger">
                            Rp {{ number_format($pinjaman->jumlah_denda, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-cash"></i> Sisa Tagihan
                        </p>
                        <span class="fw-semibold fs-5 text-warning">
                            Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-md-3 col-12">
                        <p class="text-muted mb-1">
                            <i class="ti ti-check-circle"></i> Status Pelunasan
                        </p>
                        @if($pinjaman->status_lunas === 'Lunas')
                            <span class="badge bg-success px-3 py-2">
                                <i class="ti ti-check"></i> Lunas
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-2">
                                <i class="ti ti-x"></i> Belum Lunas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulasi Tagihan -->
    <div class="card mb-3 mt-4 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold text-success"><i class="ti ti-calendar-stats me-2"></i>Simulasi Tagihan</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Bln Ke</th>
                            <th class="text-end">Angsuran Pokok</th>
                            <th class="text-end">Angsuran Bunga</th>
                            <th class="text-end">Biaya Admin</th>
                            <th class="text-end">Jumlah Angsuran</th>
                            <th class="text-center">Tanggal Tempo</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($simulasi as $index => $item)
                            <tr>
                                <td class="text-center">{{ $item->bulan_ke }}</td>
                                <td class="text-end">Rp {{ number_format($item->angsuran_pokok, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->angsuran_bunga, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->biaya_admin, 0, ',', '.') }}</td>
                                <td class="text-end"><strong>Rp
                                        {{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</strong></td>
                                <td class="text-center">{{ $item->tanggal_tempo }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success">
                                        <i class="ti ti-check"></i> {{ $item->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="text-center fw-semibold">Jumlah</th>
                            <th class="text-end fw-semibold">Rp {{ number_format($simulasi->sum('angsuran_pokok'), 0, ',', '.') }}</th>
                            <th class="text-end fw-semibold">Rp {{ number_format($simulasi->sum('angsuran_bunga'), 0, ',', '.') }}</th>
                            <th class="text-end fw-semibold">Rp {{ number_format($simulasi->sum('biaya_admin'), 0, ',', '.') }}</th>
                            <th class="text-end text-success fw-semibold fs-4">Rp
                                {{ number_format($simulasi->sum('jumlah_angsuran'), 0, ',', '.') }}
                            </th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Transaksi Pembayaran -->
    <div class="card mb-3 mt-4 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-success"><i class="ti ti-receipt me-2"></i>Riwayat Transaksi Pembayaran
                </h6>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabelTransaksi" class="table table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Kode Bayar</th>
                            <th class="text-center">Tanggal Bayar</th>
                            <th class="text-center">Angsuran Ke</th>
                            <th class="text-center">Jenis Pembayaran</th>
                            <th class="text-end">Jumlah Bayar</th>
                            <th class="text-end">Denda</th>
                            <th class="text-center">User</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                            <tr>
                                <td class="text-center">{{ $item->no }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode_bayar }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    {{ $item->tanggal_bayar }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->angsuran_ke }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->jenis_pembayaran == 'Pelunasan')
                                        <span class="badge bg-success-subtle text-success fw-semibold">{{ $item->jenis_pembayaran }}</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info fw-semibold">{{ $item->jenis_pembayaran }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp
                                        {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    @if($item->denda > 0)
                                        <span class="text-danger fw-bold">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary">{{ $item->user }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                        onclick="cetakNota('{{ $item->kode_bayar }}')" data-bs-toggle="tooltip"
                                        title="Cetak Nota">
                                        <i class="ti ti-printer"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="ti ti-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mb-0">Tidak ada data transaksi pembayaran</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="text-center fs-4" colspan="5">Total</th>
                            <th class="text-end text-success fw-semibold fs-4">
                                Rp {{ number_format($transaksi->sum('jumlah_bayar'), 0, ',', '.') }}
                            </th>
                            <th class="text-end fs-4">
                                Rp {{ number_format($transaksi->sum('denda'), 0, ',', '.') }}
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3 border-start border-1 border-success">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <i class="ti ti-info-circle fs-8 text-success me-3"></i>
                <div>
                    <h6 class="mb-1 fw-semibold text-success">Informasi Pinjaman Lunas</h6>
                    <p class="mb-0 text-muted">
                        • Pinjaman ini telah dilunasi seluruhnya pada
                        <strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_tempo)->translatedFormat('d F Y') }}</strong><br>
                        • Total pembayaran yang dilakukan: <strong>{{ $transaksi->count() }} transaksi</strong><br>
                        • Semua angsuran sudah dibayar tepat waktu<br>
                        • Data ini bersifat <strong>Read-Only</strong> dan tidak dapat diubah
                    </p>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable untuk tabel transaksi
            $('#tabelTransaksi').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'asc']], // Order by No
                columnDefs: [
                    { orderable: false, targets: [8] } // Kolom aksi tidak bisa di-sort
                ]
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Cetak Detail
        function cetakDetail(id) {
            const url = `{{ route('pinjaman.lunas.cetak-detail', ':id') }}`.replace(':id', id);
            window.open(url, '_blank');
        }

        // Function: Cetak Nota
        function cetakNota(kodeBayar) {
            Swal.fire({
                icon: 'info',
                title: 'Cetak Nota',
                text: 'Fitur cetak nota pembayaran sedang dalam pengembangan',
                confirmButtonText: 'OK'
            });
        }

        // ✅ FIXED: Function Batalkan Pelunasan
        function batalkanLunas(id) {
            Swal.fire({
                title: 'Batalkan Validasi Pelunasan?',
                html: `
                    <div class="text-start">
                        <div class="alert alert-danger mb-3">
                            <i class="ti ti-alert-triangle me-2"></i>
                            <strong> PERINGATAN! </strong>
                        </div>
                        
                        <p class="mb-2"><strong>Tindakan ini akan:</strong></p>
                        <ul class="text-muted">
                            <li>Mengembalikan status pinjaman menjadi <strong class="text-danger">BELUM LUNAS</strong></li>
                            <li>Menghapus validasi pelunasan dari sistem</li>
                            <li>Data transaksi pembayaran tetap tersimpan (tidak terhapus)</li>
                            <li>Data validasi dipindahkan ke <strong>Riwayat Pembatalan</strong></li>
                        </ul>

                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="ti ti-info-circle me-2"></i>
                            <small>Pastikan Anda memiliki alasan yang <strong>valid</strong> untuk membatalkan pelunasan ini!</small>
                        </div>
                    </div>
                `,
                icon: 'warning',
                width: '600px',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="ti ti-x-circle me-1"></i> Ya, Batalkan',
                cancelButtonText: '<i class="ti ti-arrow-left me-1"></i> Kembali',
                input: 'textarea',
                inputLabel: 'Alasan Pembatalan (Wajib Diisi)',
                inputPlaceholder: 'Jelaskan alasan pembatalan pelunasan ini (minimal 10 karakter)...',
                inputAttributes: {
                    'aria-label': 'Alasan pembatalan',
                    'rows': 4,
                    'style': 'resize: vertical;'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Alasan pembatalan wajib diisi!';
                    }
                    if (value.trim().length < 10) {
                        return 'Alasan minimal 10 karakter!';
                    }
                    if (value.trim().length > 500) {
                        return 'Alasan maksimal 500 karakter!';
                    }
                },
                preConfirm: (alasan) => {
                    if (!alasan || alasan.trim().length < 10) {
                        Swal.showValidationMessage('Alasan minimal 10 karakter');
                        return false;
                    }
                    return alasan.trim();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses Pembatalan...',
                        html: `
                            <div class="text-center">
                                <div class="spinner-border text-danger mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mb-0">Mohon tunggu sebentar...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX Request
                    $.ajax({
                        url: '{{ route("pinjaman.lunas.batalkan", ":id") }}'.replace(':id', id),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            alasan: result.value
                        },
                        timeout: 15000,
                        success: function(response) {
                            console.log('Success response:', response);
                            
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil Dibatalkan!',
                                    html: `
                                        <div class="alert alert-success text-start">
                                            <i class="ti ti-check-circle me-2"></i>
                                            ${response.message}
                                        </div>
                                        <p class="text-muted mb-0">Anda akan diarahkan ke halaman detail pinjaman...</p>
                                    `,
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                }).then(() => {
                                    window.location.href = response.redirect;
                                });
                            } else {
                                throw new Error(response.message || 'Gagal membatalkan pelunasan');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', {
                                status: xhr.status,
                                statusText: xhr.statusText,
                                responseText: xhr.responseText,
                                error: error
                            });
                            
                            let errorMessage = 'Terjadi kesalahan saat membatalkan pelunasan';
                            
                            if (xhr.status === 403) {
                                errorMessage = xhr.responseJSON?.message || 'Akses ditolak. Hanya admin yang dapat membatalkan pelunasan.';
                            } else if (xhr.status === 404) {
                                errorMessage = 'Data pinjaman lunas tidak ditemukan';
                            } else if (xhr.status === 422) {
                                const errors = xhr.responseJSON?.errors;
                                if (errors && errors.alasan) {
                                    errorMessage = errors.alasan[0];
                                } else if (xhr.responseJSON?.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                            } else if (xhr.status === 500) {
                                errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan server. Silakan coba lagi.';
                            } else if (status === 'timeout') {
                                errorMessage = 'Request timeout. Silakan coba lagi.';
                            } else if (xhr.responseJSON?.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Membatalkan!',
                                html: `
                                    <div class="alert alert-danger text-start">
                                        <i class="ti ti-alert-circle me-2"></i>
                                        ${errorMessage}
                                    </div>
                                    <p class="text-muted mb-0">Silakan hubungi administrator jika masalah berlanjut.</p>
                                `,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush