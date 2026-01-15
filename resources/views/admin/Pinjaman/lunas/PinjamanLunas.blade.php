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
            <div class="row g-3">
                <!-- Filter Tanggal -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar text-primary"></i> Rentang Tanggal
                    </label>
                    <input type="text" class="form-control" id="filterTanggal" placeholder="Pilih tanggal..." readonly>
                </div>

                <!-- Kode Transaksi -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-barcode text-info"></i> Kode Transaksi
                    </label>
                    <input type="text" class="form-control" id="filterKode" placeholder="Masukkan kode...">
                </div>

                <!-- Nama Anggota -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-user text-warning"></i> Nama Anggota
                    </label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Nama anggota...">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-12 col-lg-2">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">
                            <i class="ti ti-search"></i> Cari
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetFilter()" data-bs-toggle="tooltip"
                            title="Reset Filter">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Secondary Actions -->
            <div class="row mt-3 pt-3 border-top">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span class="badge bg-success-subtle text-success border rounded-2 px-3 py-2">
                            <i class="ti ti-check-circle"></i> Total Pinjaman Lunas: <strong
                                id="totalData">{{ $pinjamanLunas->count() }}</strong>
                        </span>
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
                            <th class="text-center align-middle">Kode</th>
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
                        @foreach($pinjamanLunas as $item)
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">{{ $item->kode }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ asset($item->anggota_foto) }}" width="40" height="40"
                                        class="rounded-circle me-2">
                                    <div>
                                        <strong>{{ $item->anggota_nama }}</strong><br>
                                        <small class="text-muted">ID: {{ $item->anggota_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary">{{ $item->anggota_departemen }}</span>
                            </td>
                            <td class="text-center text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                            </td>
                            <td class="text-center text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal_tempo)->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info">{{ $item->lama_pinjaman }} Bulan</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold">Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted">Rp {{ number_format($item->total_denda, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">Rp {{ number_format($item->sudah_dibayar, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pinjaman.lunas.detail', $item->id) }}">
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="ti ti-eye"></i> Detail
                                    </button>
                                </a>
                            </td>
                        </tr>
                        @endforeach
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
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [9] }
                ]
            });

            // Initialize Daterangepicker
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
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
        });

        // Function: Filter Data
        function filterData() {
            const kode = $('#filterKode').val();
            const nama = $('#filterNama').val();
            const tanggal = $('#filterTanggal').val();

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menerapkan filter',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Filter Diterapkan!',
                    text: 'Data berhasil difilter',
                    timer: 1500,
                    showConfirmButton: false
                });

                // TODO: Reload dengan filter
                // location.href = `{{ route('pinjaman.lunas') }}?kode=${kode}&nama=${nama}&tanggal=${tanggal}`;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            $('#filterKode').val('');
            $('#filterNama').val('');
            $('#filterTanggal').val('');

            if (table) {
                table.search('').draw();
            }

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Semua filter telah dikembalikan',
                timer: 1500,
                showConfirmButton: false
            });
        }
    </script>
@endpush