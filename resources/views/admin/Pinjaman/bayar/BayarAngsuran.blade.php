@extends('layouts.app')

@section('title', 'Pembayaran Angsuran Pinjaman')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #tabelAngsuran td {
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
                    <h4 class="fw-semibold mb-1">Pembayaran Angsuran Pinjaman</h4>
                    <p class="text-muted fs-3 mb-0">Kelola pembayaran angsuran pinjaman anggota</p>
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

    <!-- Toolbar Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter & Pencarian Data</h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('pinjaman.bayar') }}" method="GET">
                <div class="row g-3">
                    <!-- Filter Tanggal -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-calendar text-primary"></i> Rentang Tanggal Pinjam
                        </label>
                        <input type="text" class="form-control" name="tanggal" id="filterTanggal"
                            value="{{ request('tanggal') }}" placeholder="Pilih tanggal..." readonly>
                    </div>

                    <!-- Kode Transaksi -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-barcode text-info"></i> Kode Transaksi
                        </label>
                        <input type="text" class="form-control" name="kode" id="filterKode" value="{{ request('kode') }}"
                            placeholder="Masukkan kode...">
                    </div>

                    <!-- Nama Anggota -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-user text-warning"></i> Nama Anggota
                        </label>
                        <input type="text" class="form-control" name="nama" id="filterNama" value="{{ request('nama') }}"
                            placeholder="Nama anggota...">
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i> Cari
                            </button>
                            <a href="{{ route('pinjaman.bayar') }}" class="btn btn-outline-secondary"
                                data-bs-toggle="tooltip" title="Reset Filter">
                                <i class="ti ti-refresh"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Secondary Actions -->
            <div class="row mt-3 pt-3 border-top">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span class="badge bg-info-subtle text-info rounded-2 border px-3 py-2">
                            <i class="ti ti-info-circle"></i> Klik tombol "Detail" untuk melihat dan memproses pembayaran
                            angsuran
                        </span>
                        <div class="ms-auto">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="ti ti-file-text"></i> Total Data: <strong>{{ $pinjaman->count() }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Kode</th>
                            <th width="100px" class="text-center align-middle">Tanggal<br>Pinjam</th>
                            <th class="text-center align-middle">ID<br>Anggota</th>
                            <th class="align-middle">Nama Anggota</th>
                            <th class="text-center align-middle">Pokok<br>Pinjaman</th>
                            <th class="text-center align-middle">Lama<br>Angsuran</th>
                            <th class="text-center align-middle">Angsuran<br>Pokok</th>
                            <th class="text-center align-middle">Bunga<br>Angsuran</th>
                            <th class="text-center align-middle">Biaya<br>Admin</th>
                            <th class="text-center align-middle">Angsuran<br>Per Bulan</th>
                            <th class="text-center align-middle">Sisa<br>Angsuran</th>
                            <th width="70px" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- ✅ GANTI @forelse JADI @foreach --}}
                        @foreach($pinjaman as $item)
                            <tr class="{{ $item->ada_terlambat ? 'table-danger' : '' }}">
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode_pinjaman }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ $item->tanggal_pinjam->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge fw-semibold bg-secondary-subtle text-secondary">{{ $item->anggota_id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($item->anggota_foto) }}" width="40" height="40"
                                            class="rounded-circle me-2"
                                            onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                                        <div>
                                            <strong>{{ $item->anggota_nama }}</strong><br>
                                            <small class="text-muted">{{ $item->anggota_kota }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">Rp {{ number_format($item->pokok_pinjaman, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge fw-semibold bg-info-subtle text-info">{{ $item->lama_angsuran }} Bulan</span>
                                </td>
                                <td class="text-end fw-bold">Rp {{ number_format($item->angsuran_pokok, 0, ',', '.') }}</td>
                                <td class="text-end text-info fw-bold">Rp
                                    {{ number_format($item->bunga_angsuran, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-warning">Rp
                                    {{ number_format($item->biaya_admin, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <span class="fw-bold text-success fs-5">Rp
                                        {{ number_format($item->angsuran_per_bulan, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item->sisa_angsuran > 0 ? 'bg-warning' : 'bg-success' }}">
                                        {{ $item->sisa_angsuran }} / {{ $item->lama_angsuran }}
                                    </span>
                                    @if($item->ada_terlambat)
                                        <br><small class="text-danger"><i class="ti ti-alert-circle"></i> Terlambat</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pinjaman.bayar.detail', $item->id) }}" class="btn btn-success btn-sm"
                                        data-bs-toggle="tooltip" title="Lihat Detail & Bayar">
                                        <i class="ti ti-eye"></i> Bayar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($notifications->isNotEmpty())
        <!-- Notification Card -->
        <div class="card mt-3 border-start border-1 border-danger">
            <div class="card-header bg-danger-subtle">
                <h6 class="mb-0 text-danger"><i class="ti ti-bell-ringing me-2"></i>Pengingat Jatuh Tempo (7 Hari Ke Depan)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Nama Anggota</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th class="text-end">Jumlah Angsuran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notif)
                                <tr>
                                    <td><strong>{{ $notif->nama }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($notif->tanggal_jatuh_tempo)->translatedFormat('d F Y') }}</td>
                                    <td class="text-end"><strong>Rp {{ number_format($notif->sisa_tagihan, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let table;

        $(document).ready(function () {
            // ✅ Initialize DataTable dengan custom empty message
            table = $('#tabelAngsuran').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
                    // ✅ TAMBAHKAN INI - Custom message untuk empty table
                    emptyTable: `
                        <div class="py-5">
                            <div class="text-center">
                                <i class="ti ti-inbox" style="font-size: 4rem; color: #adb5bd;"></i>
                                <p class="text-muted mt-3 mb-0">Tidak ada data pinjaman yang belum lunas</p>
                            </div>
                        </div>
                    `,
                    // ✅ Custom message untuk search result empty
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
                    { orderable: false, targets: [11] }
                ]
            });

            // Initialize Daterangepicker
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#filterTanggal').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('#filterTanggal').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            // Auto uppercase untuk kode transaksi
            $('#filterKode').on('input', function () {
                $(this).val($(this).val().toUpperCase());
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush