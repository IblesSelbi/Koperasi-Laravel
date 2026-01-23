@extends('layouts.app')

@section('title', 'Laporan Simpanan')

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
            <h4 class="fw-semibold mb-1">Laporan Simpanan dan Penarikan</h4>
            <p class="text-muted fs-3 mb-0">Riwayat transaksi simpanan Anda</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-4">
                <i class="ti ti-file-analytics text-primary me-2"></i>
                Ringkasan Data
            </h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-info-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-list text-info-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Jumlah Transaksi</small>
                                <h4 class="fw-bold mb-0">{{ $summary['jumlah_transaksi'] ?? 0 }}</h4>
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
                                    <i class="ti ti-arrow-up text-success-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Total Setoran</small>
                                <h5 class="fw-bold mb-0 text-success">Rp {{ number_format($summary['total_setoran'] ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-danger-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-arrow-down text-danger-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Total Penarikan</small>
                                <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($summary['total_penarikan'] ?? 0, 0, ',', '.') }}</h5>
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
                                    <i class="ti ti-wallet text-primary-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Saldo Akhir</small>
                                <h5 class="fw-bold mb-0 text-primary">Rp {{ number_format($summary['saldo_akhir'] ?? 0, 0, ',', '.') }}</h5>
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
                <table id="tabelSimpanan" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Tanggal</th>
                            <th class="text-center align-middle">Jenis</th>
                            <th class="text-end align-middle">Jumlah</th>
                            <th class="align-middle">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($simpanan as $item)
                        <tr>
                            <td class="text-center text-muted">{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                            <td class="text-center">
                                @if($item->tipe == 'penarikan')
                                    <span class="badge bg-danger-subtle text-danger">{{ $item->jenis }}</span>
                                @else
                                    @if(str_contains(strtolower($item->jenis), 'wajib'))
                                        <span class="badge bg-success-subtle text-success">{{ $item->jenis }}</span>
                                    @else
                                        <span class="badge bg-info-subtle text-info">{{ $item->jenis }}</span>
                                    @endif
                                @endif
                            </td>
                            <td class="text-end">
                                @if($item->tipe == 'penarikan')
                                    <span class="fw-bold text-danger">- Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                @else
                                    <span class="fw-bold text-success">+ Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td>{{ $item->keterangan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="ti ti-inbox fs-1 d-block mb-2"></i>
                                Belum ada data transaksi simpanan
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
            table = $('#tabelSimpanan').DataTable({
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