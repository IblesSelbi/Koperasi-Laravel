@extends('layouts.app')

@section('title', 'Laporan Data Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Data Anggota</h4>
                    <p class="text-muted fs-3 mb-0">Laporan lengkap data anggota koperasi</p>
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
                <!-- Filter Status -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-toggle-left text-success"></i> Status Anggota
                    </label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
                    </select>
                </div>

                <!-- Filter Gender -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-user text-info"></i> Jenis Kelamin
                    </label>
                    <select class="form-select" id="filterGender">
                        <option value="">Semua</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <!-- Filter Jabatan -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-briefcase text-warning"></i> Jabatan
                    </label>
                    <select class="form-select" id="filterJabatan">
                        <option value="">Semua Jabatan</option>
                        <option value="Produksi BOPP">Produksi BOPP</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Accounting">Accounting</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-search text-primary"></i> Cari Nama/ID
                    </label>
                    <input type="text" class="form-control" id="filterSearch" placeholder="Ketik nama...">
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
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="ti ti-file-spreadsheet"></i> Export Excel
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="exportPDF()">
                            <i class="ti ti-file-type-pdf"></i> Export PDF
                        </button>
                        <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <div class="ms-auto">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="ti ti-users"></i> Total Anggota: <strong id="totalData">{{ $anggota->count() }}</strong>
                            </span>
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
                <table id="tabelAnggota" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th width="50px" class="text-center align-middle">No.</th>
                            <th width="100px" class="text-center align-middle">ID Anggota</th>
                            <th width="250px" class="align-middle">Nama Anggota</th>
                            <th width="50px" class="text-center align-middle">L/P</th>
                            <th width="150px" class="text-center align-middle">Jabatan</th>
                            <th width="200px" class="align-middle">Alamat</th>
                            <th width="100px" class="text-center align-middle">Status</th>
                            <th width="120px" class="text-center align-middle">Tgl Registrasi</th>
                            <th width="80px" class="text-center align-middle">Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($anggota as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $item->id_anggota }}</span>
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block">{{ $item->nama }}</strong>
                                        <small class="text-muted">
                                            {{ $item->tempat_lahir }}, {{ \Carbon\Carbon::parse($item->tanggal_lahir)->format('d M Y') }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info">{{ $item->jenis_kelamin }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $item->jabatan }}</span><br>
                                    <small class="text-muted">{{ $item->departemen }}</small>
                                </td>
                                <td>
                                    {{ $item->alamat }}<br>
                                    <small class="text-muted"><i class="ti ti-phone"></i> {{ $item->no_telepon }}</small>
                                </td>
                                <td class="text-center">
                                    @if($item->status == 'Aktif')
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="ti ti-check"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="ti ti-x"></i> Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_registrasi)->format('d M Y') }}
                                </td>
                                <td class="text-center">
                                    <img src="{{ asset($item->foto) }}" alt="Photo" class="rounded" width="45" height="60" style="object-fit: cover;">
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

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            // Hide table initially
            const tableWrapper = $('#tabelAnggota').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            table = $('#tabelAnggota').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [8] }
                ],
                initComplete: function () {
                    tableWrapper.css('opacity', 1);
                }
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Filter Data
        function filterData() {
            const status = $('#filterStatus').val();
            const gender = $('#filterGender').val();
            const jabatan = $('#filterJabatan').val();
            const search = $('#filterSearch').val();

            console.log('Filter:', { status, gender, jabatan, search });

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
                // location.href = `{{ route('laporan.anggota') }}?status=${status}&gender=${gender}&jabatan=${jabatan}&search=${search}`;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            $('#filterStatus').val('');
            $('#filterGender').val('');
            $('#filterJabatan').val('');
            $('#filterSearch').val('');

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

        // Function: Export Excel
        function exportExcel() {
            Swal.fire({
                icon: 'success',
                title: 'Export Excel',
                text: 'File akan segera diunduh...',
                timer: 1500,
                showConfirmButton: false
            });
            window.location.href = '{{ route("laporan.anggota.export.excel") }}';
        }

        // Function: Export PDF
        function exportPDF() {
            Swal.fire({
                icon: 'success',
                title: 'Export PDF',
                text: 'File akan segera diunduh...',
                timer: 1500,
                showConfirmButton: false
            });
            window.location.href = '{{ route("laporan.anggota.export.pdf") }}';
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const status = $('#filterStatus').val() || '';
            const gender = $('#filterGender').val() || '';
            const jabatan = $('#filterJabatan').val() || '';

            const url = `{{ route('laporan.anggota.cetak') }}?status=${status}&gender=${gender}&jabatan=${jabatan}`;
            window.open(url, '_blank');
        }
    </script>
@endpush