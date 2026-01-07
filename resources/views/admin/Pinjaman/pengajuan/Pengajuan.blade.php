@extends('layouts.app')

@section('title', 'Data Pengajuan Pinjaman')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Data Pengajuan Pinjaman</h4>
                    <p class="text-muted fs-3 mb-0">Kelola pengajuan pinjaman anggota</p>
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
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar text-primary"></i> Rentang Tanggal
                    </label>
                    <input type="text" class="form-control" id="filterTanggal" placeholder="Pilih tanggal..." readonly>
                </div>

                <!-- Filter Jenis -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-category text-info"></i> Jenis Pinjaman
                    </label>
                    <select class="form-select" id="filterJenis">
                        <option value="">Semua Jenis</option>
                        <option value="Biasa">Biasa</option>
                        <option value="Darurat">Darurat</option>
                        <option value="Barang">Barang</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-list-check text-success"></i> Status Pengajuan
                    </label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="0">Menunggu Konfirmasi</option>
                        <option value="1">Disetujui</option>
                        <option value="2">Ditolak</option>
                        <option value="3">Sudah Terlaksana</option>
                        <option value="4">Batal</option>
                    </select>
                </div>

                <!-- Filter Bulan -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar-month text-warning"></i> Per Bulan
                    </label>
                    <input type="text" class="form-control" id="filterBulan" placeholder="YYYY-MM" readonly
                        title="Filter Untuk Per Tanggal 21 Sampai 20">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-12 col-lg-2">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">
                            <i class="ti ti-search"></i> Terapkan
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
                                <i class="ti ti-file-text"></i> Total Data: <strong id="totalData">{{ $total_pengajuan }}</strong>
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
                <table id="tabelPengajuan" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th width="70px" class="text-center align-middle">ID Ajuan</th>
                            <th class="text-center align-middle">Tanggal<br>Pengajuan</th>
                            <th width="150px" class="align-middle">Anggota</th>
                            <th class="text-center align-middle">Jenis</th>
                            <th width="120px" class="text-end align-middle">Jumlah</th>
                            <th class="text-center align-middle" title="Jumlah Angsuran">Bln</th>
                            <th width="150px" class="align-middle">Keterangan</th>
                            <th width="130px" class="text-center align-middle">Status</th>
                            <th width="170px" class="align-middle">Sisa Pinjaman</th>
                            <th class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->id_ajuan }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}
                                </td>
                                <td>
                                    <a href="javascript:void(0)" title="Lihat History Pinjaman Anggota"
                                        class="text-decoration-none">
                                        {{ $item->anggota_id }}
                                    </a><br>
                                    <strong>{{ $item->anggota_nama }}</strong><br>
                                    <small class="text-muted">{{ $item->anggota_departemen }}</small>
                                </td>
                                <td class="text-center">
                                    @if($item->jenis_pinjaman == 'Biasa')
                                        <span class="badge bg-info-subtle text-info">Biasa</span>
                                    @elseif($item->jenis_pinjaman == 'Darurat')
                                        <span class="badge bg-warning-subtle text-warning">Darurat</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success">Barang</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success fs-4">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->jumlah_angsuran }}</span>
                                </td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-center">
                                    @if($item->status == 0)
                                        <span class="text-primary">
                                            <i class="ti ti-clock"></i> Menunggu Konfirmasi
                                        </span>
                                    @elseif($item->status == 1)
                                        <span class="text-success">
                                            <i class="ti ti-check"></i> Disetujui<br>
                                            @if($item->tanggal_cair)
                                                <small>Cair: {{ \Carbon\Carbon::parse($item->tanggal_cair)->format('d M Y') }}</small>
                                            @endif
                                        </span>
                                    @elseif($item->status == 2)
                                        <span class="text-danger">
                                            <i class="ti ti-x"></i> Ditolak
                                        </span>
                                    @elseif($item->status == 3)
                                        <span class="text-info">
                                            <i class="ti ti-rocket"></i> Terlaksana
                                        </span>
                                    @else
                                        <span class="text-secondary">
                                            <i class="ti ti-ban"></i> Batal
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">Sisa Jml Pinjaman:</small> {{ $item->sisa_pinjaman }}<br>
                                    <small class="text-muted">Sisa Jml Angsuran:</small> {{ $item->sisa_angsuran }}<br>
                                    <small class="text-muted">Sisa Tagihan:</small> Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="text-center" style="min-width: 200px;">
                                    @if($item->status == 0)
                                        <!-- Pending/Menunggu Konfirmasi -->
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success btn-sm" onclick="setujuiPengajuan({{ $item->id }})">
                                                <i class="ti ti-check"></i> Setujui
                                            </button>
                                            <button class="btn btn-warning btn-sm" onclick="tolakPengajuan({{ $item->id }})">
                                                <i class="ti ti-x"></i> Tolak
                                            </button>
                                        </div>
                                        <div class="btn-group mt-1" role="group">
                                            <button class="btn btn-danger btn-sm" onclick="batalkanPengajuan({{ $item->id }})">
                                                <i class="ti ti-ban"></i> Batal
                                            </button>
                                            <button class="btn btn-secondary btn-sm" onclick="cetakPengajuan({{ $item->id }})">
                                                <i class="ti ti-printer"></i> Cetak
                                            </button>
                                        </div>
                                    @elseif($item->status == 1)
                                        <!-- Disetujui -->
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm" onclick="tolakPengajuan({{ $item->id }})">
                                                <i class="ti ti-x"></i> Tolak
                                            </button>
                                            <button class="btn btn-primary btn-sm" onclick="pendingPengajuan({{ $item->id }})">
                                                <i class="ti ti-clock"></i> Pending
                                            </button>
                                        </div>
                                        <div class="btn-group mt-1" role="group">
                                            <button class="btn btn-info btn-sm" onclick="terlaksanaPengajuan({{ $item->id }})">
                                                <i class="ti ti-rocket"></i> Terlaksana
                                            </button>
                                        </div>
                                        <div class="btn-group mt-1" role="group">
                                            <button class="btn btn-danger btn-sm" onclick="hapusPengajuan({{ $item->id }})">
                                                <i class="ti ti-trash"></i> Hapus
                                            </button>
                                            <button class="btn btn-secondary btn-sm" onclick="cetakPengajuan({{ $item->id }})">
                                                <i class="ti ti-printer"></i> Cetak
                                            </button>
                                        </div>
                                    @elseif($item->status == 3)
                                        <!-- Terlaksana -->
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-secondary btn-sm" onclick="belumTerlaksana({{ $item->id }})">
                                                <i class="ti ti-arrow-back"></i> Kembalikan
                                            </button>
                                        </div>
                                        <div class="btn-group mt-1" role="group">
                                            <button class="btn btn-danger btn-sm" onclick="hapusPengajuan({{ $item->id }})">
                                                <i class="ti ti-trash"></i> Hapus
                                            </button>
                                            <button class="btn btn-info btn-sm" onclick="cetakPengajuan({{ $item->id }})">
                                                <i class="ti ti-printer"></i> Cetak
                                            </button>
                                        </div>
                                    @else
                                        <!-- Ditolak/Batal -->
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-danger btn-sm" onclick="hapusPengajuan({{ $item->id }})">
                                                <i class="ti ti-trash"></i> Hapus
                                            </button>
                                            <button class="btn btn-secondary btn-sm" onclick="cetakPengajuan({{ $item->id }})">
                                                <i class="ti ti-printer"></i> Cetak
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="ti ti-database-off fs-1 mb-2"></i>
                                    <p class="mb-0">Tidak ada data pengajuan</p>
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

    <!-- Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(document).ready(function () {
            // Hide table initially
            const tableWrapper = $('#tabelPengajuan').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            // Initialize DataTable
            var table = $('#tabelPengajuan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [9] }
                ],
                initComplete: function () {
                    tableWrapper.css('opacity', 1);
                }
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

            // Initialize Bulan Picker
            $('#filterBulan').on('click', function () {
                const currentMonth = new Date().toISOString().slice(0, 7);
                Swal.fire({
                    title: 'Pilih Bulan',
                    html: `<input type="month" id="swal-month" class="form-control" value="${currentMonth}">`,
                    showCancelButton: true,
                    confirmButtonText: 'Pilih',
                    cancelButtonText: 'Batal',
                    preConfirm: () => {
                        return document.getElementById('swal-month').value;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        $('#filterBulan').val(result.value);
                    }
                });
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Setujui Pengajuan
        function setujuiPengajuan(id) {
            const today = new Date().toISOString().split('T')[0];

            Swal.fire({
                title: 'Setujui Pengajuan',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin <strong class="text-success">MENYETUJUI</strong> pengajuan ini?</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pencairan <span class="text-danger">*</span></label>
                            <input type="date" id="swal-tglcair" class="form-control" value="${today}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan / Catatan</label>
                            <textarea id="swal-alasan" class="form-control" rows="3" placeholder="Opsional - Tambahkan catatan jika diperlukan"></textarea>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-check"></i> Ya, Setujui',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#198754',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const tglCair = document.getElementById('swal-tglcair').value;
                    const alasan = document.getElementById('swal-alasan').value;

                    if (!tglCair) {
                        Swal.showValidationMessage('Tanggal pencairan wajib diisi!');
                        return false;
                    }

                    return { tglCair, alasan };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('setujui', id, result.value.alasan, result.value.tglCair);
                }
            });
        }

        // Function: Tolak Pengajuan
        function tolakPengajuan(id) {
            Swal.fire({
                title: 'Tolak Pengajuan',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin <strong class="text-danger">MENOLAK</strong> pengajuan ini?</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea id="swal-alasan" class="form-control" rows="3" placeholder="Wajib diisi - Jelaskan alasan penolakan" required></textarea>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-x"></i> Ya, Tolak',
                cancelButtonText: '<i class="ti ti-arrow-back"></i> Batal',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const alasan = document.getElementById('swal-alasan').value.trim();

                    if (!alasan) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi!');
                        return false;
                    }

                    return { alasan };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('tolak', id, result.value.alasan);
                }
            });
        }

        // Function: Pending Pengajuan
        function pendingPengajuan(id) {
            Swal.fire({
                title: 'Pending Pengajuan',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Pengajuan akan ditandai sebagai <strong class="text-primary">PENDING</strong></p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alasan / Catatan</label>
                            <textarea id="swal-alasan" class="form-control" rows="3" placeholder="Opsional - Jelaskan alasan pending"></textarea>
                        </div>
                    </div>
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-clock"></i> Ya, Pending',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#0d6efd',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    const alasan = document.getElementById('swal-alasan').value;
                    return { alasan };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('pending', id, result.value.alasan);
                }
            });
        }

        // Function: Batalkan Pengajuan
        function batalkanPengajuan(id) {
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: 'Pengajuan akan dibatalkan secara permanen',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-ban"></i> Ya, Batalkan',
                cancelButtonText: '<i class="ti ti-x"></i> Tidak',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('batal', id);
                }
            });
        }

        // Function: Hapus Pengajuan
        function hapusPengajuan(id) {
            Swal.fire({
                title: 'Hapus Pengajuan?',
                text: 'Data akan dihapus secara permanen dan tidak dapat dikembalikan',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-trash"></i> Ya, Hapus',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('hapus', id);
                }
            });
        }

        // Function: Terlaksana Pengajuan
        function terlaksanaPengajuan(id) {
            Swal.fire({
                title: 'Tandai Terlaksana?',
                text: 'Pengajuan akan ditandai sebagai sudah dilaksanakan',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-rocket"></i> Ya, Terlaksana',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#198754',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('terlaksana', id);
                }
            });
        }

        // Function: Belum Terlaksana
        function belumTerlaksana(id) {
            Swal.fire({
                title: 'Kembalikan Status?',
                text: 'Pengajuan akan dikembalikan ke status belum dilaksanakan',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-arrow-back"></i> Ya, Kembalikan',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-secondary',
                    cancelButton: 'btn btn-outline-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesAksi('belum', id);
                }
            });
        }

        // Function: Proses Aksi
        function prosesAksi(aksi, id, alasan = '', tglCair = '') {
            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX Request
            $.ajax({
                url: '{{ route("pinjaman.pengajuan.aksi") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    aksi: aksi,
                    alasan: alasan,
                    tgl_cair: tglCair
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Pengajuan berhasil diproses',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan, silakan coba lagi'
                    });
                }
            });
        }

        // Function: Cetak Pengajuan
        function cetakPengajuan(id) {
            const url = `{{ url('pinjaman/pengajuan/cetak') }}/${id}`;
            window.open(url, '_blank');
        }

        // Function: Filter Data
        function filterData() {
            const jenis = $('#filterJenis').val();
            const status = $('#filterStatus').val();
            const bulan = $('#filterBulan').val();
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

                // Reload dengan filter
                location.href = `{{ route('pinjaman.pengajuan') }}?jenis=${jenis}&status=${status}&bulan=${bulan}&tanggal=${tanggal}`;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            $('#filterJenis').val('');
            $('#filterStatus').val('');
            $('#filterBulan').val('');
            $('#filterTanggal').val('');

            $('#tabelPengajuan').DataTable().search('').draw();

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
            window.location.href = '{{ route("pinjaman.pengajuan.export.excel") }}';
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
            window.location.href = '{{ route("pinjaman.pengajuan.export.pdf") }}';
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const jenis = $('#filterJenis').val() || '';
            const status = $('#filterStatus').val() || '';
            const bulan = $('#filterBulan').val() || '';
            const tanggal = $('#filterTanggal').val() || '';

            const url = `{{ route('pinjaman.pengajuan.cetak') }}?jenis=${jenis}&status=${status}&bulan=${bulan}&tanggal=${tanggal}`;
            window.open(url, '_blank');
        }
    </script>
@endpush