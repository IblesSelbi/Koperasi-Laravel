@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <style>
        .body-wrapper .user-area > .container-fluid {
            padding-top: 100px;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-semibold mb-1">Laporan Pembayaran Angsuran</h4>
            <p class="text-muted fs-3 mb-0">Riwayat pembayaran angsuran pinjaman Anda</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-4">
                <i class="ti ti-file-analytics text-primary me-2"></i>
                Ringkasan Pembayaran
            </h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-info-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-receipt text-info-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Total Pembayaran</small>
                                <h4 class="fw-bold mb-0">{{ $summary['total_pembayaran'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-cash text-success-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Total Dibayar</small>
                                <h5 class="fw-bold mb-0">Rp {{ number_format($summary['total_dibayar'] ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-warning-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-alert-triangle text-warning-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Total Denda</small>
                                <h5 class="fw-bold mb-0 text-warning">Rp {{ number_format($summary['total_denda'] ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-calendar-time text-primary-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Bulan Ini</small>
                                <h5 class="fw-bold mb-0">Rp {{ number_format($summary['bulan_ini'] ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body shadow-sm">
            <div class="table-responsive">
                <table id="tabelPembayaran" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Tanggal</th>
                            <th class="text-center align-middle">Jenis</th>
                            <th class="text-center align-middle">Angsuran Ke</th>
                            <th class="text-end align-middle">Denda</th>
                            <th class="text-end align-middle">Jumlah Bayar</th>
                            <th class="align-middle">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayaran as $item)
                        <tr>
                            <td class="text-center text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success">{{ $item->jenis }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary">Bulan ke-{{ $item->angsuran_ke }}</span>
                            </td>
                            <td class="text-end">
                                @if($item->denda > 0)
                                    <span class="fw-bold text-warning">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">Rp 0</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</span>
                            </td>
                            <td>{{ $item->keterangan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="ti ti-inbox fs-1 d-block mb-2"></i>
                                Belum ada data pembayaran
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
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            table = $('#tabelPembayaran').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: true, targets: '_all' }
                ]
            });
        });
    </script>
@endpush