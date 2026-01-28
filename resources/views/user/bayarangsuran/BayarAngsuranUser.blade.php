@extends('layouts.app')

@section('title', 'Bayar Angsuran')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tabelAngsuran td,
        th {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Bayar Angsuran Pinjaman</h4>
                    <p class="text-muted fs-3 mb-0">Kelola pembayaran angsuran pinjaman Anda</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Alert Pending Payment Global -->
    @if($has_pending)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ti ti-clock-hour-4 me-2"></i>
            <strong>Pembayaran Sedang Diverifikasi</strong>
            <p class="mb-0 mt-2">Anda memiliki pembayaran yang sedang menunggu verifikasi admin. Mohon tunggu hingga verifikasi
                selesai sebelum melakukan pembayaran berikutnya.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-4">
                <i class="ti ti-file-analytics text-primary me-2"></i>
                Ringkasan Data Pinjaman
            </h5>

            <div class="row g-3">
                <!-- Total Pinjaman -->
                <div class="col-md-4">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <span
                                class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                style="width:44px;height:44px;">
                                <i class="ti ti-wallet text-primary"></i>
                            </span>
                            <div>
                                <small class="text-muted d-block">Total Pinjaman</small>
                                <h5 class="fw-bold mb-0">
                                    {{ $pinjaman->count() }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Verifikasi -->
                <div class="col-md-4">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <span
                                class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                style="width:44px;height:44px;">
                                <i class="ti ti-clock-hour-4 text-warning"></i>
                            </span>
                            <div>
                                <small class="text-muted d-block">Pending Verifikasi</small>
                                <h5 class="fw-bold mb-0">
                                    {{ $pinjaman->where('has_pending_payment', true)->count() }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Dibayar -->
                <div class="col-md-4">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <span
                                class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                style="width:44px;height:44px;">
                                <i class="ti ti-check-circle text-success"></i>
                            </span>
                            <div>
                                <small class="text-muted d-block">Total Dibayar</small>
                                <h5 class="fw-bold mb-0">
                                    Rp {{ number_format($pinjaman->sum('total_dibayar'), 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <!-- Info Alert -->
    <!-- <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Cara Pembayaran:</strong>
                    <ul class="mb-0 mt-2 ps-3">
                        <li>Pilih pinjaman dari tabel di bawah</li>
                        <li>Klik tombol "Detail & Bayar"</li>
                        <li>Pilih angsuran yang akan dibayar</li>
                        <li>Upload bukti transfer</li>
                        <li>Tunggu verifikasi admin (1-2 hari kerja)</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div> -->

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-file-invoice me-2"></i>Daftar Pinjaman Aktif</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Kode</th>
                            <th width="100px" class="text-center align-middle">Tanggal Pinjam</th>
                            <th class="align-middle">Nama Anggota</th>
                            <th class="text-center align-middle">Pokok Pinjaman</th>
                            <th class="text-center align-middle">Total Pinjaman</th>
                            <th class="text-center align-middle">Sudah Dibayar</th>
                            <th class="text-center align-middle">Sisa Pinjaman</th>
                            <th class="text-center align-middle">Angsuran Ke</th>
                            <th class="text-center align-middle">Status</th>
                            <th width="100px" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode_pinjaman }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ $item->tanggal_pinjam->format('d M Y') }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($item->anggota->photo_display) }}" width="40" height="40"
                                            class="rounded-circle me-2"
                                            onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                                        <div>
                                            <strong>{{ $item->anggota->nama }}</strong><br>
                                            <small class="text-muted">{{ $item->anggota->id_anggota }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">Rp {{ number_format($item->pokok_pinjaman, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-success">Rp
                                        {{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-success">Rp
                                        {{ number_format($item->total_dibayar, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-danger">Rp
                                        {{ number_format($item->sisa_pinjaman_calculated, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $item->angsuran_berikutnya }} / {{ $item->lamaAngsuran->lama_angsuran }}
                                    </span>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: {{ $item->progress_percentage }}%">
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ number_format($item->progress_percentage, 1) }}%</small>
                                </td>
                                <td class="text-center">
                                    @if($item->has_pending_payment)
                                        <span class="badge bg-warning">
                                            <i class="ti ti-clock"></i> Pending
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <i class="ti ti-check"></i> Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('user.bayar.show', $item->id) }}" class="btn btn-success btn-sm"
                                        data-bs-toggle="tooltip" title="Detail & Bayar">
                                        <i class="ti ti-cash"></i> Bayar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="ti ti-inbox display-1 text-muted"></i>
                                    <p class="text-muted mt-3 mb-0">Tidak ada pinjaman aktif</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            $('#tabelAngsuran').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    emptyTable: `
                                        <div class="py-5">
                                            <div class="text-center">
                                                <i class="ti ti-inbox" style="font-size: 4rem; color: #adb5bd;"></i>
                                                <p class="text-muted mt-3 mb-0">Tidak ada pinjaman aktif</p>
                                            </div>
                                        </div>
                                    `,
                    zeroRecords: `
                                        <div class="py-5">
                                            <div class="text-center">
                                                <i class="ti ti-search-off" style="font-size: 4rem; color: #adb5bd;"></i>
                                                <p class="text-muted mt-3 mb-0">Tidak ada data yang cocok dengan pencarian</p>
                                            </div>
                                        </div>
                                    `
                },
                pageLength: 10,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [9] }
                ]
            });

            // Initialize Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush