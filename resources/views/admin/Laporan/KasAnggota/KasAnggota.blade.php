@extends('layouts.app')

@section('title', 'Laporan Kas Per Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <style>
        #tabelKasAnggota tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px #e7f1ff !important;
            color: #004085 !important;
        }

        #tabelKasAnggota tbody tr.selected>* strong,
        #tabelKasAnggota tbody tr.selected>* .text-muted,
        #tabelKasAnggota tbody tr.selected>* .text-info,
        #tabelKasAnggota tbody tr.selected>* .text-warning,
        #tabelKasAnggota tbody tr.selected>* .text-success,
        #tabelKasAnggota tbody tr.selected>* .text-danger {
            color: inherit !important;
        }

        #tabelKasAnggota tbody tr:hover {
            cursor: pointer;
            background-color: #f8f9fa;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
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
            <div class="row g-3">
                <!-- Pilih Anggota -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-user text-primary"></i> Pilih ID Anggota
                    </label>
                    <select class="form-select" id="filterAnggota">
                        <option value="">Semua Anggota</option>
                        <option value="member1">member1 - FAISAL</option>
                        <option value="member2">member2 - GEO HALOMOAN SIMANJUNTAK</option>
                        <option value="anggota">anggota - WIDI ALJATSIYAH</option>
                        <option value="fulan">fulan - FULAN</option>
                        <option value="pengguna">pengguna - PENGGUNA</option>
                    </select>
                </div>

                <!-- Filter Status Pembayaran -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-list-check text-success"></i> Status Pembayaran
                    </label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Lancar">Lancar</option>
                        <option value="Macet">Macet</option>
                    </select>
                </div>

                <!-- Filter Jabatan -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-briefcase text-info"></i> Jabatan
                    </label>
                    <select class="form-select" id="filterJabatan">
                        <option value="">Semua Jabatan</option>
                        <option value="Produksi BOPP">Anggota - Produksi BOPP</option>
                        <option value="Engineering">Anggota - Engineering</option>
                        <option value="Accounting">Anggota - Accounting</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6 col-lg-2">
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
                            <th class="text-center align-middle" style="width: 5%;">No.</th>
                            <th class="text-center align-middle" style="width: 5%;">Photo</th>
                            <th class="align-middle" style="width: 23%;">Identitas</th>
                            <th class="align-middle" style="width: 20%;">Saldo Simpanan</th>
                            <th class="align-middle" style="width: 20%;">Tagihan Kredit</th>
                            <th class="align-middle" style="width: 23%;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kasAnggota as $index => $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-semibold">{{ $index + 1 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <img src="{{ $item->foto }}" alt="Photo" width="60" height="80" class="rounded border" />
                                </td>
                                <td class="align-middle">
                                    <div class="mb-1">
                                        <small class="text-muted">ID Anggota:</small>
                                        <span class="badge bg-info-subtle text-info">{{ $item->id_anggota }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Nama:</small>
                                        <strong class="text-dark">{{ $item->nama }}</strong>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Jenis Kelamin:</small>
                                        <span>{{ $item->jenis_kelamin }}</span>
                                    </div>
                                    <div class="mb-1">
                                        <small class="text-muted">Jabatan:</small>
                                        <span>{{ $item->jabatan }} - {{ $item->departemen }}</span>
                                    </div>
                                    <div>
                                        <small class="text-muted">Alamat:</small>
                                        <span>{{ $item->alamat }}</span><br>
                                        <small class="text-muted">Telp:</small>
                                        <span>{{ $item->no_telepon }}</span>
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
                                                <span class="badge bg-warning-subtle text-warning">{{ $item->keterangan['tanggal_tempo'] }}</span>
                                            @endif
                                        </div>
                                    </div>
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
            const tableWrapper = $('#tabelKasAnggota').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            table = $('#tabelKasAnggota').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [1] }
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

            // Table row selection
            $('#tabelKasAnggota tbody').on('click', 'tr', function () {
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
            const anggota = $('#filterAnggota').val();
            const status = $('#filterStatus').val();
            const jabatan = $('#filterJabatan').val();

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
                // location.href = `{{ route('laporan.kas-anggota') }}?anggota=${anggota}&status=${status}&jabatan=${jabatan}`;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            $('#filterAnggota').val('');
            $('#filterStatus').val('');
            $('#filterJabatan').val('');

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

        // Function: Cetak Laporan
        function cetakLaporan() {
            const anggota = $('#filterAnggota').val() || '';
            const status = $('#filterStatus').val() || '';
            const jabatan = $('#filterJabatan').val() || '';

            let filterInfo = 'Semua Data';
            if (anggota) filterInfo = `Anggota: ${anggota}`;
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
                    const url = `{{ route('laporan.kas-anggota.cetak') }}?anggota=${anggota}&status=${status}&jabatan=${jabatan}`;
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