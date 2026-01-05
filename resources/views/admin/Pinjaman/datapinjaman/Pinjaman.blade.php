@extends('layouts.app')

@section('title', 'Data Pinjaman Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        #tabelPinjaman tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px #dfe2e5 !important;
            color: #777e89 !important;
        }

        #tabelPinjaman tbody tr.selected>* strong,
        #tabelPinjaman tbody tr.selected>* .text-muted,
        #tabelPinjaman tbody tr.selected>* .text-info,
        #tabelPinjaman tbody tr.selected>* .text-warning,
        #tabelPinjaman tbody tr.selected>* .text-success {
            color: inherit !important;
        }

        #tabelPinjaman tbody tr:hover {
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
                    <h4 class="fw-semibold mb-1">Data Pinjaman Anggota</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data pinjaman anggota koperasi</p>
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

                <!-- Filter Status -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-list-check text-success"></i> Status Lunas
                    </label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Belum">Belum Lunas</option>
                        <option value="Lunas">Sudah Lunas</option>
                    </select>
                </div>

                <!-- Kode Transaksi -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-barcode text-info"></i> Kode Transaksi
                    </label>
                    <input type="text" class="form-control" id="filterKode" placeholder="Masukkan kode...">
                </div>

                <!-- Nama Anggota -->
                <div class="col-md-6 col-lg-2">
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
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success btn-sm" onclick="tambahData()">
                            <i class="ti ti-plus"></i> Tambah Pinjaman
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editData()">
                            <i class="ti ti-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="hapusData()">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                        <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <div class="ms-auto">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="ti ti-file-text"></i> Total Data: <strong
                                    id="totalData">{{ $pinjaman->count() }}</strong>
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
                <table id="tabelPinjaman" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Kode</th>
                            <th width="70px" class="text-center align-middle">Tanggal<br>Pinjam</th>
                            <th width="200px" class="align-middle">Nama Anggota</th>
                            <th width="350px" class="align-middle">Hitungan</th>
                            <th width="250px" class="align-middle">Total Tagihan</th>
                            <th class="text-center align-middle">Status<br>Lunas</th>
                            <th class="text-center align-middle">User</th>
                            <th width="20px" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">PJ001</span>
                            </td>
                            <td class="text-center text-muted">15 Des 2025<br><small>10:30</small></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="./assets/images/profile/user-2.jpg" width="40" height="40"
                                        class="rounded-circle me-2">
                                    <div>
                                        <strong>Budi Santoso</strong><br>
                                        <small class="text-muted">ID: 001234 • Jakarta</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Nama Barang</small>
                                        <strong> Pinjaman Dana Tunai </strong>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Harga Barang</small>
                                        <span>Rp 2.600.000</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Lama Angsuran</small>
                                        <span>6 Bulan</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Pokok Angsuran</small>
                                        <span>Rp 433.333</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Bunga Pinjaman</small>
                                        <span class="text-info">Rp 130.000</span>
                                    </div>

                                    <div class="d-flex justify-content-between pt-1">
                                        <small class="text-muted">Biaya Admin</small>
                                        <span class="text-warning">Rp 0</span>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Jumlah Angsuran</small>
                                        <span>Rp 1.575.000</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Jumlah Denda</small>
                                        <span>Rp 0</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Sudah Dibayar</small>
                                        <span>Rp 0</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Sisa Angsuran</small>
                                        <span>1x</span>
                                    </div>

                                    <div class="d-flex justify-content-between pt-2">
                                        <small class="text-muted fw-semibold">Sisa Tagihan</small>
                                        <span class="fw-bold text-success fs-6">Rp 1.575.000</span>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                    <i class="ti ti-x"></i> Belum Lunas
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge border border-secondary text-secondary px-3 py-1">Admin</span>
                            </td>
                            <td class="text-center" style="min-width: 150px;">
                                <div class="btn-group" role="group">
                                    <div class="btn-group mt-1" role="group">
                                        <a href="{{ route('pinjaman.pinjaman.detail', 1) }}">
                                            <button class="btn btn-outline-info btn-sm">
                                                <i class="ti ti-eye"></i> Detail
                                            </button>
                                        </a>
                                        <button class="btn btn-primary btn-sm" onclick="cetakNota(1)">
                                            <i class="ti ti-printer"></i> Nota
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Sample Data Row 2 -->
                        <tr>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">PJ001</span>
                            </td>
                            <td class="text-center text-muted">15 Des 2025<br><small>10:30</small></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="./assets/images/profile/user-2.jpg" width="40" height="40"
                                        class="rounded-circle me-2">
                                    <div>
                                        <strong>Budi Santoso</strong><br>
                                        <small class="text-muted">ID: 001234 • Jakarta</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Nama Barang</small>
                                        <strong> Pinjaman Dana Tunai </strong>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Harga Barang</small>
                                        <span>Rp 2.600.000</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Lama Angsuran</small>
                                        <span>6 Bulan</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Pokok Angsuran</small>
                                        <span>Rp 433.333</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Bunga Pinjaman</small>
                                        <span class="text-info">Rp 130.000</span>
                                    </div>

                                    <div class="d-flex justify-content-between pt-1">
                                        <small class="text-muted">Biaya Admin</small>
                                        <span class="text-warning">Rp 0</span>
                                    </div>

                                </div>
                            </td>

                            <td>
                                <div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Jumlah Angsuran</small>
                                        <span>Rp 1.575.000</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Jumlah Denda</small>
                                        <span>Rp 0</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Sudah Dibayar</small>
                                        <span>Rp 0</span>
                                    </div>

                                    <div class="d-flex justify-content-between border-bottom py-1">
                                        <small class="text-muted">Sisa Angsuran</small>
                                        <span>1x</span>
                                    </div>

                                    <div class="d-flex justify-content-between pt-2">
                                        <small class="text-muted fw-semibold">Sisa Tagihan</small>
                                        <span class="fw-bold text-success fs-6">Rp 1.575.000</span>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center">
                                <span class="badge bg-danger-subtle text-danger px-3 py-2">
                                    <i class="ti ti-x"></i> Belum Lunas
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge border border-secondary text-secondary px-3 py-1">Admin</span>
                            </td>
                            <td class="text-center" style="min-width: 150px;">
                                <div class="btn-group" role="group">
                                    <div class="btn-group mt-1" role="group">
                                        <a href="{{ route('pinjaman.pinjaman.detail', 2) }}">
                                            <button class="btn btn-outline-info btn-sm">
                                                <i class="ti ti-eye"></i> Detail
                                            </button>
                                        </a>
                                        <button class="btn btn-primary btn-sm" onclick="cetakNota(2)">
                                            <i class="ti ti-printer"></i> Nota
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit Pinjaman -->
    <div class="modal fade" id="modalFormPinjaman" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="ti ti-plus"></i> Tambah Data Pinjaman
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPinjaman">
                        @csrf
                        <input type="hidden" id="pinjamanId" name="id">

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Tanggal Pinjam <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control" id="tglPinjam" name="tanggal_pinjam"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Anggota <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="anggotaId" name="anggota_id" required>
                                        <option value="">-- Pilih Anggota --</option>
                                        <option value="001234">001234 - Budi Santoso (Jakarta)</option>
                                        <option value="001235">001235 - Siti Aminah (Bandung)</option>
                                        <option value="001236">001236 - Ahmad Hidayat (Surabaya)</option>
                                        <option value="001237">001237 - Dewi Lestari (Semarang)</option>
                                        <option value="001238">001238 - Eko Prasetyo (Yogyakarta)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Barang <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="barangId" name="barang_id" required>
                                        <option value="">-- Pilih Barang --</option>
                                        <option value="1">HP Infinix Note 30 8/256 GB - Rp 2.600.000</option>
                                        <option value="2">Pinjaman Dana Tunai - Rp 0</option>
                                        <option value="3">Pinjaman Uang - Rp 10.000.000</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Harga Barang <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0"
                                            required>
                                    </div>
                                    <small class="text-muted">Masukkan jumlah pinjaman atau harga barang</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Lama Angsuran <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="lamaAngsuran" name="lama_angsuran" required>
                                        <option value="">-- Pilih Angsuran --</option>
                                        <option value="1">1 Bulan</option>
                                        <option value="3">3 Bulan</option>
                                        <option value="6">6 Bulan</option>
                                        <option value="12">12 Bulan</option>
                                        <option value="24">24 Bulan</option>
                                        <option value="36">36 Bulan</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Bunga</label>
                                    <input type="text" class="form-control bg-light" id="bunga" value="5%" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Biaya Admin</label>
                                    <input type="text" class="form-control bg-light" id="biayaAdm" value="Rp 0" readonly>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ambil Dari Kas <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="kasId" name="kas_id" required>
                                        <option value="">-- Pilih Kas --</option>
                                        <option value="1">Kas Tunai</option>
                                        <option value="2">Kas Besar</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Keterangan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                        placeholder="Tambahkan keterangan (opsional)"></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-center">
                                    <label class="form-label fw-semibold d-block mb-2">Foto Anggota</label>
                                    <div id="anggotaPhoto" class="border rounded p-2"
                                        style="height: 180px; width: 135px; margin: 0 auto;">
                                        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                            <i class="ti ti-user" style="font-size: 48px;"></i>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2">Foto akan muncul setelah memilih anggota</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" onclick="simpanPinjaman()">
                        <i class="ti ti-check"></i> Simpan
                    </button>
                </div>
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
        let table;

        $(document).ready(function () {
            // Hide table initially
            const tableWrapper = $('#tabelPinjaman').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            // Initialize DataTable
            table = $('#tabelPinjaman').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [7] }
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

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Table row selection
            $('#tabelPinjaman tbody').on('click', 'tr', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });
        });

        // Function: Tambah Data
        function tambahData() {
            $('#modalTitle').html('<i class="ti ti-plus"></i> Tambah Data Pinjaman');
            $('#formPinjaman')[0].reset();
            $('#pinjamanId').val('');
            $('#anggotaPhoto').html('<div class="d-flex align-items-center justify-content-center h-100 text-muted"><i class="ti ti-user" style="font-size: 48px;"></i></div>');
            $('#modalFormPinjaman').modal('show');
        }

        // Function: Edit Data
        function editData() {
            var row = table.row('.selected').data();
            if (!row) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih data yang akan diedit terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            $('#modalTitle').html('<i class="ti ti-edit"></i> Edit Data Pinjaman');
            // TODO: Load data ke form
            $('#modalFormPinjaman').modal('show');
        }

        // Function: Hapus Data
        function hapusData() {
            var row = table.row('.selected').data();
            if (!row) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih data yang akan dihapus terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Data Pinjaman?',
                text: 'Data pinjaman dan seluruh angsurannya akan dihapus permanen!',
                icon: 'warning',
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
                    // TODO: Process deletion via AJAX
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data pinjaman berhasil dihapus',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        // Function: Simpan Pinjaman
        function simpanPinjaman() {
            const form = $('#formPinjaman');
            const formData = new FormData(form[0]);

            // Basic validation
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            const pinjamanId = $('#pinjamanId').val();
            const url = pinjamanId ?
                `{{ url('pinjaman/pinjaman') }}/${pinjamanId}` :
                '{{ route("pinjaman.pinjaman.store") }}';

            const method = pinjamanId ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: Object.fromEntries(formData),
                success: function (response) {
                    $('#modalFormPinjaman').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Data pinjaman berhasil disimpan',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan, silakan coba lagi'
                    });
                }
            });
        }

        // Function: Cetak Nota
        function cetakNota(id) {
            const url = `{{ url('pinjaman/pinjaman/cetak') }}/${id}`;
            window.open(url, '_blank');
        }

        // Function: Filter Data
        function filterData() {
            const status = $('#filterStatus').val();
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
                // location.href = `{{ route('pinjaman.pinjaman') }}?status=${status}&kode=${kode}&nama=${nama}&tanggal=${tanggal}`;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            $('#filterStatus').val('');
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

        // Function: Cetak Laporan
        function cetakLaporan() {
            const status = $('#filterStatus').val() || '';
            const kode = $('#filterKode').val() || '';
            const nama = $('#filterNama').val() || '';
            const tanggal = $('#filterTanggal').val() || '';

            window.open(url, '_blank');
        }
    </script>
@endpush