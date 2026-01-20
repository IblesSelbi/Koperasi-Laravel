@extends('layouts.app')

@section('title', 'Pinjaman Lunas')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        #tabelPinjamanLunas tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px #dfe2e5 !important;
            color: #777e89 !important;
        }

        #tabelPinjamanLunas tbody tr.selected>* strong,
        #tabelPinjamanLunas tbody tr.selected>* .text-muted,
        #tabelPinjamanLunas tbody tr.selected>* .text-success {
            color: inherit !important;
        }

        #tabelPinjamanLunas tbody tr:hover {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Pinjaman Lunas</h4>
                    <p class="text-muted fs-3 mb-0">Data pinjaman yang sudah dilunasi</p>
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
            <form action="{{ route('pinjaman.lunas') }}" method="GET">
                <div class="row g-3">
                    <!-- Filter Tanggal -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-calendar text-primary"></i> Rentang Tanggal Lunas
                        </label>
                        <input type="text" class="form-control" name="tanggal" id="filterTanggal"
                            value="{{ request('tanggal') }}" placeholder="Pilih tanggal..." readonly>
                    </div>

                    <!-- Kode Transaksi -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-barcode text-info"></i> Kode Lunas
                        </label>
                        <input type="text" class="form-control" name="kode" id="filterKode" value="{{ request('kode') }}"
                            placeholder="TPJ00001...">
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
                            <a href="{{ route('pinjaman.lunas') }}" class="btn btn-outline-secondary"
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
                    <div class="d-flex gap-2 flex-wrap align-items-center justify-content-between">
                        <div class="d-flex gap-2">
                            <span class="badge bg-success-subtle text-success border rounded-2 px-3 py-2">
                                <i class="ti ti-check-circle"></i> Total Pinjaman Lunas:
                                <strong>{{ $pinjamanLunas->count() }}</strong>
                            </span>
                            @if(request()->hasAny(['kode', 'nama', 'tanggal']))
                                <span class="badge bg-info-subtle text-info border rounded-2 px-3 py-2">
                                    <i class="ti ti-filter"></i> Filter Aktif
                                </span>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            @if(auth()->user()->role_id == 1)
                                <button class="btn btn-secondary btn-sm" onclick="lihatRiwayatBatal()">
                                    <i class="ti ti-history"></i> Riwayat Batal
                                </button>
                            @endif
                            <button class="btn btn-sm btn-primary" onclick="cetakLaporan()">
                                <i class="ti ti-printer"></i> Cetak Laporan
                            </button>
                            <button class="btn btn-sm btn-success" onclick="exportExcel()">
                                <i class="ti ti-file-spreadsheet"></i> Export Excel
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="exportPDF()">
                                <i class="ti ti-file-pdf"></i> Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelPinjamanLunas" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Kode Lunas</th>
                            <th width="200px" class="align-middle">Nama Anggota</th>
                            <th class="text-center align-middle">Dept</th>
                            <th width="170px" class="text-center align-middle">Tanggal Pinjam</th>
                            <th width="170px" class="text-center align-middle">Tanggal Tempo</th>
                            <th class="text-center align-middle">Lama Pinjam</th>
                            <th width="150px" class="text-end align-middle">Total Tagihan</th>
                            <th width="150px" class="text-end align-middle">Total Denda</th>
                            <th width="150px" class="text-end align-middle">Dibayar</th>
                            <th width="150px" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjamanLunas as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($item->anggota_foto) }}" width="40" height="40"
                                            class="rounded-circle me-2"
                                            onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                                        <div>
                                            <strong>{{ $item->anggota_nama }}</strong><br>
                                            <small class="text-muted">ID: {{ $item->anggota_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-secondary-subtle text-secondary fw-semibold">{{ $item->anggota_departemen }}</span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                                </td>
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_tempo)->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info fw-semibold">{{ $item->lama_pinjaman }} Bulan</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold text-success">Rp
                                        {{ number_format($item->sudah_dibayar, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pinjaman.lunas.detail', $item->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="ti ti-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="ti ti-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mb-0">Tidak ada data pinjaman lunas</p>
                                    @if(request()->hasAny(['kode', 'nama', 'tanggal']))
                                        <small class="text-muted">Coba ubah filter pencarian</small>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    @if($notifications->isNotEmpty())
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
                                    <td>{{ $notif->tanggal_jatuh_tempo }}</td>
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
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            table = $('#tabelPinjamanLunas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[3, 'desc']], // Order by tanggal pinjam
                columnDefs: [
                    { orderable: false, targets: [9] } // Kolom aksi tidak bisa di-sort
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

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Table row selection
            $('#tabelPinjamanLunas tbody').on('click', 'tr', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });

            // Auto uppercase untuk kode
            $('#filterKode').on('input', function () {
                $(this).val($(this).val().toUpperCase());
            });
        });

        // Function: Lihat Riwayat Batal (Admin Only)
        function lihatRiwayatBatal() {
            window.location.href = '{{ route("pinjaman.lunas.riwayat-batal") }}';
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const kode = $('#filterKode').val();
            const nama = $('#filterNama').val();
            const tanggal = $('#filterTanggal').val();

            let url = '{{ route("pinjaman.lunas.cetak-laporan") }}';
            const params = new URLSearchParams();

            if (kode) params.append('kode', kode);
            if (nama) params.append('nama', nama);
            if (tanggal) params.append('tanggal', tanggal);

            if (params.toString()) {
                url += '?' + params.toString();
            }

            window.open(url, '_blank');
        }

        // Function: Export Excel
        function exportExcel() {
            Swal.fire({
                icon: 'info',
                title: 'Export Excel',
                text: 'Fitur export Excel sedang dalam pengembangan',
                confirmButtonText: 'OK'
            });

            // TODO: Uncomment jika sudah implement
            // window.location.href = '{{ route("pinjaman.lunas.export.excel") }}';
        }

        // Function: Export PDF
        function exportPDF() {
            Swal.fire({
                icon: 'info',
                title: 'Export PDF',
                text: 'Fitur export PDF sedang dalam pengembangan',
                confirmButtonText: 'OK'
            });

            // TODO: Uncomment jika sudah implement
            // window.location.href = '{{ route("pinjaman.lunas.export.pdf") }}';
        }
    </script>
@endpush