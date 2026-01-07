@extends('layouts.app')

@section('title', 'Detail Pinjaman')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Detail Pinjaman</h4>
                    <p class="text-muted fs-3 mb-0">Rincian angsuran pinjaman</p>
                </div>
                <div>
                    <a href="{{ route('user.laporan.pinjaman') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Pinjaman Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-4">
                <i class="ti ti-info-circle text-primary me-2"></i>
                Informasi Pinjaman
            </h5>

            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Tanggal Pinjaman</small>
                    <h6 class="fw-semibold mb-0">{{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d M Y') }}</h6>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Jumlah Pinjaman</small>
                    <h6 class="fw-semibold mb-0 text-primary">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</h6>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Lama Angsuran</small>
                    <h6 class="fw-semibold mb-0">{{ $pinjaman->lama_angsuran }} Bulan</h6>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Status</small>
                    @if($pinjaman->status_lunas)
                        <span class="badge bg-success-subtle text-success px-3 py-2">
                            <i class="ti ti-check"></i> Lunas
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                            <i class="ti ti-x"></i> Belum Lunas
                        </span>
                    @endif
                </div>
            </div>

            <hr class="my-3">

            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Bunga ({{ $pinjaman->persen_bunga }}%)</small>
                    <h6 class="fw-semibold mb-0 text-warning">Rp {{ number_format($pinjaman->bunga, 0, ',', '.') }}</h6>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Biaya Admin</small>
                    <h6 class="fw-semibold mb-0">Rp {{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</h6>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Total Tagihan</small>
                    <h6 class="fw-semibold mb-0 text-success">Rp {{ number_format($pinjaman->total_tagihan, 0, ',', '.') }}</h6>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block mb-1">Sisa Tagihan</small>
                    <h6 class="fw-semibold mb-0 text-danger">Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</h6>
                </div>
            </div>

            @if($pinjaman->keterangan)
            <hr class="my-3">
            <div>
                <small class="text-muted d-block mb-1">Keterangan</small>
                <p class="mb-0">{{ $pinjaman->keterangan }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body shadow-sm">
            <h5 class="fw-semibold mb-3">
                <i class="ti ti-list text-primary me-2"></i>
                Rincian Angsuran
            </h5>
            <div class="table-responsive">
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Bulan Ke</th>
                            <th class="text-end align-middle">Angsuran Pokok</th>
                            <th class="text-end align-middle">Angsuran Bunga</th>
                            <th class="text-end align-middle">Biaya Admin</th>
                            <th class="text-end align-middle">Jumlah Angsuran</th>
                            <th class="text-center align-middle">Tanggal Tempo</th>
                            <th class="text-center align-middle">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($angsuran as $item)
                        <tr>
                            <td class="text-center text-muted">{{ $item->bulan_ke }}</td>
                            <td class="text-end">Rp {{ number_format($item->angsuran_pokok, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->angsuran_bunga, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->biaya_admin, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_tempo)->format('d M Y') }}</td>
                            <td class="text-center">
                                @if($item->status == 'Lunas')
                                    <span class="badge bg-success-subtle text-success">Lunas</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Belum Bayar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="ti ti-inbox fs-1 d-block mb-2"></i>
                                Belum ada data angsuran
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($angsuran->isNotEmpty())
                    <tfoot class="table-light">
                        <tr>
                            <td class="text-center fw-bold">JUMLAH</td>
                            <td class="text-end fw-bold">Rp {{ number_format($total->angsuran_pokok, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($total->angsuran_bunga, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">Rp {{ number_format($total->biaya_admin, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-success">Rp {{ number_format($total->jumlah_angsuran, 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
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
            table = $('#tabelAngsuran').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: true, targets: '_all' }
                ]
            });
        });
    </script>
@endpush