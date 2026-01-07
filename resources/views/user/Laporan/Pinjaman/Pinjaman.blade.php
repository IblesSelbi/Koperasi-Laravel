@extends('layouts.app')

@section('title', 'Laporan Pinjaman')

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
            <h4 class="fw-semibold mb-1">Laporan Pinjaman</h4>
            <p class="text-muted fs-3 mb-0">Riwayat pinjaman dan angsuran Anda</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-4">
                <i class="ti ti-file-analytics text-primary me-2"></i>
                Ringkasan Data Pinjaman
            </h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="border shadow-sm rounded-3 p-3 h-100 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <span class="bg-info-subtle rounded-circle d-inline-flex align-items-center justify-content-center"
                                    style="width:44px;height:44px;">
                                    <i class="ti ti-file-text text-info-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Total Pinjaman</small>
                                <h4 class="fw-bold mb-0">{{ $summary['total_pinjaman'] ?? 0 }}</h4>
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
                                    <i class="ti ti-circle-check text-success-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Sudah Lunas</small>
                                <h4 class="fw-bold mb-0">{{ $summary['sudah_lunas'] ?? 0 }}</h4>
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
                                    <i class="ti ti-clock-hour-3 text-warning-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Belum Lunas</small>
                                <h4 class="fw-bold mb-0">{{ $summary['belum_lunas'] ?? 0 }}</h4>
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
                                    <i class="ti ti-alert-circle text-danger-emphasis fs-5"></i>
                                </span>
                            </div>
                            <div>
                                <small class="text-muted">Sisa Tagihan</small>
                                <h5 class="fw-bold mb-0 text-danger">Rp {{ number_format($summary['sisa_tagihan'] ?? 0, 0, ',', '.') }}</h5>
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
                <table id="tabelPinjaman" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Tanggal</th>
                            <th class="text-center align-middle">Lama<br>Angsuran</th>
                            <th class="text-end align-middle">Jumlah<br>Pinjaman</th>
                            <th class="text-end align-middle">Bunga</th>
                            <th class="text-end align-middle">Biaya<br>Admin</th>
                            <th class="text-end align-middle">Angsuran<br>Per Bulan</th>
                            <th class="text-end align-middle">Total<br>Tagihan</th>
                            <th class="text-center align-middle">Jatuh<br>Tempo</th>
                            <th class="text-center align-middle">Status<br>Lunas</th>
                            <th class="align-middle">Keterangan</th>
                            <th class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjaman as $item)
                        <tr>
                            <td class="text-center text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}<br>
                                <small>{{ \Carbon\Carbon::parse($item->tanggal)->format('H:i') }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info">{{ $item->lama_angsuran }} Bulan</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-semibold">Rp {{ number_format($item->jumlah_pinjaman, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="text-info">Rp {{ number_format($item->bunga, 0, ',', '.') }}</span><br>
                                <small class="text-muted">({{ $item->persen_bunga }}%)</small>
                            </td>
                            <td class="text-end">
                                <span class="text-warning">Rp {{ number_format($item->biaya_admin, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-primary">Rp {{ number_format($item->angsuran_per_bulan, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-5">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</span><br>
                                <small class="text-muted">Sisa: Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning">{{ \Carbon\Carbon::parse($item->jatuh_tempo)->format('d M Y') }}</span>
                            </td>
                            <td class="text-center">
                                @if($item->status_lunas)
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="ti ti-check"></i> Lunas
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                        <i class="ti ti-x"></i> Belum Lunas
                                    </span>
                                @endif
                            </td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="text-center">
                                <a href="{{ route('user.laporan.pinjaman.detail', $item->id) }}" class="btn btn-outline-info btn-sm">
                                    <i class="ti ti-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                <i class="ti ti-inbox fs-1 d-block mb-2"></i>
                                Belum ada data pinjaman
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
            table = $('#tabelPinjaman').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: true, targets: '_all' },
                    { orderable: false, targets: [10] }
                ]
            });
        });
    </script>
@endpush