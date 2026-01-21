@extends('layouts.app')

@section('title', 'Laporan Kas Per Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Kas Per Anggota</h4>
                    <p class="text-muted fs-3 mb-0">Data kas simpanan dan tagihan kredit anggota koperasi</p>
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
            <form action="{{ route('laporan.kas-anggota') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Pilih Anggota -->
                    <div class="col-md-6 col-lg-4">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-user text-primary"></i> Pilih ID Anggota
                        </label>
                        <select class="form-select" id="filterAnggota" name="anggota">
                            <option value="">Semua Anggota</option>
                            @php
                                $allAnggota = \App\Models\Admin\DataMaster\DataAnggota::where('aktif', 'Aktif')
                                    ->orderBy('nama', 'asc')
                                    ->get();
                            @endphp
                            @foreach($allAnggota as $ang)
                                <option value="{{ $ang->id_anggota }}" 
                                    {{ request('anggota') == $ang->id_anggota ? 'selected' : '' }}>
                                    {{ $ang->id_anggota }} - {{ $ang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Status Pembayaran -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-list-check text-success"></i> Status Pembayaran
                        </label>
                        <select class="form-select" id="filterStatus" name="status">
                            <option value="">Semua Status</option>
                            <option value="Lancar" {{ request('status') == 'Lancar' ? 'selected' : '' }}>Lancar</option>
                            <option value="Macet" {{ request('status') == 'Macet' ? 'selected' : '' }}>Macet</option>
                        </select>
                    </div>

                    <!-- Filter Jabatan -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-briefcase text-info"></i> Jabatan
                        </label>
                        <select class="form-select" id="filterJabatan" name="jabatan">
                            <option value="">Semua Jabatan</option>
                            <option value="Anggota" {{ request('jabatan') == 'Anggota' ? 'selected' : '' }}>Anggota</option>
                            <option value="Pengurus" {{ request('jabatan') == 'Pengurus' ? 'selected' : '' }}>Pengurus</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-6 col-lg-2">
                        <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ti ti-search"></i> Cari
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetFilter()" 
                                data-bs-toggle="tooltip" title="Reset Filter">
                                <i class="ti ti-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Secondary Actions -->
            <div class="row mt-3 pt-3 border-top">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="ti ti-file-spreadsheet"></i> Export Excel
                        </button>
                        <div class="ms-auto">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="ti ti-users"></i> Total Anggota: <strong id="totalData">{{ $kasAnggota->count() }}</strong>
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
                <table id="tabelKasAnggota" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 6%;">Photo</th>
                            <th class="align-middle" style="width: 23%;">Identitas</th>
                            <th class="align-middle" style="width: 20%;">Saldo Simpanan</th>
                            <th class="align-middle" style="width: 20%;">Tagihan Kredit</th>
                            <th class="align-middle" style="width: 20%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasAnggota as $index => $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-semibold">{{ $index + 1 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <img src="{{ $item->foto }}" alt="Photo" width="70" height="85" 
                                        class="rounded border" 
                                        onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'" />
                                </td>
                                <td class="align-middle">
                                    <div class="small">
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <span class="text-muted">ID Anggota</span>
                                            <span class="badge bg-primary-subtle fw-semibold text-primary">{{ $item->id_anggota }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <span class="text-muted">Nama</span>
                                           <strong class="text-dark">{{ $item->nama }}</strong>
                                        </div>

                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <span class="text-muted">Jenis Kelamin</span>
                                            <span class="{{ $item->jenis_kelamin == 'Laki-laki' }}
                                                text-{{ $item->jenis_kelamin == 'Laki-laki' ? 'info' : 'danger' }} 
                                                fw-semibold">
                                                {{ $item->jenis_kelamin }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <span class="text-muted">Jabatan</span>
                                            <span class="text-end">{{ $item->jabatan }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <span class="text-muted">Departemen</span>
                                            <span class="text-end">{{ $item->departemen ?? '-' }}</span>
                                        </div>

                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <span class="text-muted">Alamat</span>
                                            <div>
                                                <i class="ti ti-map-pin text-danger"></i>
                                                <span class="text-end text-wrap text-break" style="max-width: 220px">
                                                    {{ $item->alamat }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between pt-1">
                                            <span class="text-muted">Telepon</span>
                                            <div>
                                                <i class="ti ti-phone text-success"></i>
                                                <span class="text-end">{{ $item->no_telepon ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Simpanan Sukarela</small>
                                            <span class="text-end">Rp {{ number_format($item->simpanan['sukarela'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Simpanan Pokok</small>
                                            <span class="text-end {{ $item->simpanan['pokok'] < 0 ? 'text-danger' : '' }}">
                                                Rp {{ number_format($item->simpanan['pokok'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Simpanan Wajib</small>
                                            <span class="text-end">Rp {{ number_format($item->simpanan['wajib'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Lainnya</small>
                                            <span class="text-end">Rp {{ number_format($item->simpanan['lainnya'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between pt-1">
                                            <strong class="text-primary">Jumlah Simpanan</strong>
                                            @php
                                                $totalSimpanan = array_sum($item->simpanan);
                                            @endphp
                                            <strong class="{{ $totalSimpanan < 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($totalSimpanan, 0, ',', '.') }}
                                            </strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Pokok Pinjaman</small>
                                            <span class="text-end">Rp {{ number_format($item->kredit['pokok_pinjaman'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Tagihan + Denda</small>
                                            <span class="text-end text-warning">Rp {{ number_format($item->kredit['tagihan_denda'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Dibayar</small>
                                            <span class="text-end">Rp {{ number_format($item->kredit['dibayar'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between pt-1">
                                            <strong class="text-primary">Sisa Tagihan</strong>
                                            <strong class="{{ $item->kredit['sisa_tagihan'] > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($item->kredit['sisa_tagihan'], 0, ',', '.') }}
                                            </strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Jumlah Pinjaman</small>
                                            <span class="text-end">{{ $item->keterangan['jumlah_pinjaman'] }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Pinjaman Lunas</small>
                                            <span class="text-end">{{ $item->keterangan['pinjaman_lunas'] }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom pb-1 mb-1">
                                            <small class="text-muted">Pembayaran</small>
                                            @if($item->keterangan['status_pembayaran'] == 'Macet')
                                                <span class="badge bg-danger-subtle text-danger">Macet</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success">Lancar</span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-between pt-1">
                                            <small class="text-muted">Tanggal Tempo</small>
                                            @if($item->keterangan['tanggal_tempo'] == '-')
                                                <span class="badge bg-secondary-subtle text-secondary">-</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">{{ $item->keterangan['tanggal_tempo'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="ti ti-file-x fs-1 text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada data untuk ditampilkan</p>
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

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize Select2 for Anggota Dropdown
        $(document).ready(function() {
            $('#filterAnggota').select2({
                theme: 'bootstrap-5',
                placeholder: 'Ketik nama atau ID anggota...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "Anggota tidak ditemukan";
                    },
                    searching: function() {
                        return "Mencari...";
                    }
                }
            });
        });

        // Initialize DataTable
        let table;
        $(document).ready(function () {
            const tableWrapper = $('#tabelKasAnggota').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            table = $('#tabelKasAnggota').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[2, 'asc']], // Sort by nama
                columnDefs: [
                    { orderable: false, targets: [1] }
                ],
                initComplete: function () {
                    tableWrapper.css('opacity', 1);
                }
            });
        });

        // Function: Reset Filter
        function resetFilter() {
            // Clear Select2
            $('#filterAnggota').val(null).trigger('change');
            $('#filterStatus').val('');
            $('#filterJabatan').val('');
            
            // Redirect to clean URL
            window.location.href = '{{ route("laporan.kas-anggota") }}';
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const params = new URLSearchParams();
            
            const anggota = $('#filterAnggota').val();
            const status = $('#filterStatus').val();
            const jabatan = $('#filterJabatan').val();

            if (anggota) params.append('anggota', anggota);
            if (status) params.append('status', status);
            if (jabatan) params.append('jabatan', jabatan);

            let filterInfo = 'Semua Data';
            if (anggota) {
                const selectedText = $('#filterAnggota option:selected').text();
                filterInfo = `Anggota: ${selectedText}`;
            }
            if (status) filterInfo += ` | Status: ${status}`;
            if (jabatan) filterInfo += ` | Jabatan: ${jabatan}`;

            Swal.fire({
                title: 'Cetak Laporan',
                html: `
                    <div class="text-start">
                        <p><strong>Filter yang diterapkan:</strong></p>
                        <p class="text-muted">${filterInfo}</p>
                        <hr>
                        <p>Laporan akan dibuka di tab baru</p>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-printer"></i> Cetak',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#0d6efd',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const url = `{{ route('laporan.kas-anggota.cetak') }}?${params.toString()}`;
                    window.open(url, '_blank');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Laporan dibuka di tab baru',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Function: Export Excel
        function exportExcel() {
            Swal.fire({
                title: 'Export ke Excel',
                html: `
                    <div class="text-start">
                        <p>Data kas anggota akan diekspor ke format Excel (.xlsx)</p>
                        <hr>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includePhoto" checked>
                            <label class="form-check-label" for="includePhoto">
                                Sertakan foto anggota
                            </label>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-file-spreadsheet"></i> Export',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#198754',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengekspor Data...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    setTimeout(() => {
                        const includePhoto = document.getElementById('includePhoto').checked;
                        window.location.href = `{{ route('laporan.kas-anggota.export.excel') }}?photo=${includePhoto}`;

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'File Excel berhasil diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 1500);
                }
            });
        }
    </script>
@endpush