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

    <!-- Statistik Cards -->
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary-subtle border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-users fs-8 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Anggota</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $totalAnggota }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success-subtle border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-check fs-8 text-success"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Anggota Aktif</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $anggotaAktif }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger-subtle border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-x fs-8 text-danger"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Non Aktif</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ $anggotaNonAktif }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info-subtle border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="ti ti-gender-bigender fs-8 text-info"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">L / P</h6>
                            @if(request()->routeIs('laporan.anggota'))
                                <h3>{{ $anggotaLakiLaki }} / {{ $anggotaPerempuan }}</h3>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toolbar Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter & Pencarian Data</h6>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('laporan.anggota') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Filter Status -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-toggle-left text-success"></i> Status Anggota
                        </label>
                        <select class="form-select" name="status" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non Aktif" {{ request('status') == 'Non Aktif' ? 'selected' : '' }}>Non Aktif</option>
                        </select>
                    </div>

                    <!-- Filter Gender -->
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-user text-info"></i> Jenis Kelamin
                        </label>
                        <select class="form-select" name="gender" id="filterGender">
                            <option value="">Semua</option>
                            <option value="Laki-laki" {{ request('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ request('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <!-- Filter Jabatan -->
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-briefcase text-warning"></i> Jabatan
                        </label>
                        <select class="form-select" name="jabatan" id="filterJabatan">
                            <option value="">Semua Jabatan</option>
                            <option value="Anggota" {{ request('jabatan') == 'Anggota' ? 'selected' : '' }}>Anggota</option>
                            <option value="Pengurus" {{ request('jabatan') == 'Pengurus' ? 'selected' : '' }}>Pengurus</option>
                        </select>
                    </div>

                    <!-- Filter Departemen -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-building text-primary"></i> Departemen
                        </label>
                        <select class="form-select" name="departemen" id="filterDepartemen">
                            <option value="">Semua Departemen</option>
                            <option value="Produksi BOPP" {{ request('departemen') == 'Produksi BOPP' ? 'selected' : '' }}>Produksi BOPP</option>
                            <option value="Produksi Slitting" {{ request('departemen') == 'Produksi Slitting' ? 'selected' : '' }}>Produksi Slitting</option>
                            <option value="WH" {{ request('departemen') == 'WH' ? 'selected' : '' }}>WH</option>
                            <option value="QA" {{ request('departemen') == 'QA' ? 'selected' : '' }}>QA</option>
                            <option value="HRD" {{ request('departemen') == 'HRD' ? 'selected' : '' }}>HRD</option>
                            <option value="GA" {{ request('departemen') == 'GA' ? 'selected' : '' }}>GA</option>
                            <option value="Purchasing" {{ request('departemen') == 'Purchasing' ? 'selected' : '' }}>Purchasing</option>
                            <option value="Accounting" {{ request('departemen') == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                            <option value="Engineering" {{ request('departemen') == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                        </select>
                    </div>

                    <!-- Search -->
                    <div class="col-md-12 col-lg-2">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-search text-primary"></i> Cari
                        </label>
                        <input type="text" class="form-control" name="search" id="filterSearch" 
                               placeholder="Nama/ID..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap align-items-center">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="ti ti-filter"></i> Terapkan Filter
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetFilter()">
                                <i class="ti ti-refresh"></i> Reset
                            </button>
                            <div class="vr"></div>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportExcel()">
                                <i class="ti ti-file-spreadsheet"></i> Export Excel
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="exportPDF()">
                                <i class="ti ti-file-type-pdf"></i> Export PDF
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="cetakLaporan()">
                                <i class="ti ti-printer"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
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
                            <th width="80px" class="text-center align-middle">Photo</th>
                            <th width="100px" class="text-center align-middle">ID Anggota</th>
                            <th width="150px" class="align-middle">Username</th>
                            <th width="250px" class="align-middle">Nama Anggota</th>
                            <th width="50px" class="text-center align-middle">L/P</th>
                            <th width="150px" class="text-center align-middle">Jabatan</th>
                            <th width="150px" class="text-center align-middle">Departemen</th>
                            <th width="200px" class="align-middle">Alamat</th>
                            <th width="100px" class="text-center align-middle">Status</th>
                            <th width="120px" class="text-center align-middle">Tgl Registrasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($anggota as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <img src="{{ $item->photo_url }}" alt="Photo" class="rounded-circle" width="45" height="45" style="object-fit: cover;">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $item->id_anggota }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->username }}</div>
                                </td>
                                <td>
                                    <div>
                                        <strong class="d-block">{{ $item->nama }}</strong>
                                        <small class="text-muted">
                                            {{ $item->tempat_tgl_lahir }}
                                        </small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->jenis_kelamin == 'Laki-laki' ? 'info' : 'warning' }}-subtle text-{{ $item->jenis_kelamin == 'Laki-laki' ? 'info' : 'warning' }}">
                                        {{ $item->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark">{{ $item->jabatan }}</span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">{{ $item->departement ?: '-' }}</small>
                                </td>
                                <td>
                                    {{ $item->alamat }}, {{ $item->kota }}<br>
                                    <small class="text-muted"><i class="ti ti-phone"></i> {{ $item->no_telp ?: '-' }}</small>
                                </td>
                                <td class="text-center">
                                    @if($item->aktif == 'Aktif')
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="ti ti-check"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                            <i class="ti ti-x"></i> Non Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-muted">
                                    {{ $item->tanggal_registrasi->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="ti ti-inbox fs-6"></i>
                                        <p class="mt-2">Tidak ada data anggota</p>
                                    </div>
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
                    { orderable: false, targets: [1, 8, 9] }
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

        // Function: Reset Filter
        function resetFilter() {
            window.location.href = "{{ route('laporan.anggota') }}";
        }

        // Function: Export Excel
        function exportExcel() {
            const params = new URLSearchParams(window.location.search);
            const url = "{{ route('laporan.anggota.export.excel') }}?" + params.toString();
            
            Swal.fire({
                icon: 'success',
                title: 'Export Excel',
                text: 'File akan segera diunduh...',
                timer: 1500,
                showConfirmButton: false
            });
            
            window.location.href = url;
        }

        // Function: Export PDF
        function exportPDF() {
            const params = new URLSearchParams(window.location.search);
            const url = "{{ route('laporan.anggota.export.pdf') }}?" + params.toString();
            
            Swal.fire({
                icon: 'success',
                title: 'Export PDF',
                text: 'File akan segera diunduh...',
                timer: 1500,
                showConfirmButton: false
            });
            
            window.open(url, '_blank');
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const params = new URLSearchParams(window.location.search);
            const url = "{{ route('laporan.anggota.cetak') }}?" + params.toString();
            
            window.open(url, '_blank');
        }
    </script>
@endpush