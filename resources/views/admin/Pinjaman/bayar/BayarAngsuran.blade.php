@extends('layouts.app')

@section('title', 'Pembayaran Angsuran Pinjaman')

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
            <div class="row g-3">
                <!-- Filter Tanggal -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar text-primary"></i> Rentang Tanggal Pinjam
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
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <span class="badge bg-info-subtle text-info rounded-2 border px-3 py-2">
                            <i class="ti ti-info-circle"></i> Klik tombol "Bayar" untuk memproses pembayaran angsuran
                        </span>
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
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Kode</th>
                            <th width="100px" class="text-center align-middle">Tanggal<br>Pinjam</th>
                            <th class="text-center align-middle">ID<br>Anggota</th>
                            <th width="200px" class="align-middle">Nama Anggota</th>
                            <th class="text-end align-middle">Pokok<br>Pinjaman</th>
                            <th class="text-center align-middle">Lama<br>Pinjam</th>
                            <th class="text-end align-middle">Angsuran<br>Pokok</th>
                            <th class="text-end align-middle">Bunga<br>Angsuran</th>
                            <th class="text-end align-middle">Biaya<br>Admin</th>
                            <th class="text-end align-middle">Angsuran<br>Per Bulan</th>
                            <th width="70px" class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pinjaman as $item)
                            <tr class="{{ $item->status_lunas == 'Belum' ? 'table-danger' : '' }}">
                                <td class="text-center">
                                    <span
                                        class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">{{ $item->kode }}</span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $item->anggota_id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ asset($item->anggota_foto) }}" width="40" height="40"
                                            class="rounded-circle me-2">
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
                                    <span class="badge bg-info-subtle text-info">{{ $item->lama_angsuran }} Bulan</span>
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
                                   <a href="{{ route('pinjaman.bayar.detail', $item->id) }}"
                                    class="btn btn-success btn-sm">
                                        <i class="ti ti-cash"></i> Bayar
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="card mt-3 border-start border border-warning">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <i class="ti ti-info-circle fs-8 text-warning me-3"></i>
                <div>
                    <h6 class="mb-1 fw-semibold">Informasi</h6>
                    <p class="mb-0 text-muted">
                        • Baris berwarna <strong class="text-danger">merah muda</strong> menunjukkan pinjaman yang belum
                        lunas atau mendekati jatuh tempo<br>
                        • Klik tombol <strong>"Bayar"</strong> untuk memproses pembayaran angsuran<br>
                        • Pastikan data pembayaran sudah benar sebelum menyimpan
                    </p>
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
            const tableWrapper = $('#tabelAngsuran').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            // Initialize DataTable
            table = $('#tabelAngsuran').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [10] }
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

            // Auto uppercase untuk kode transaksi
            $('#filterKode').on('input', function () {
                $(this).val($(this).val().toUpperCase());
            });

            // Enter key untuk search
            $('#filterKode, #filterNama').on('keypress', function (e) {
                if (e.which === 13) {
                    filterData();
                }
            });

            // Set default tanggal bayar
            const today = new Date().toISOString().split('T')[0];
            $('#tglBayar').val(today);

            // Format currency on input
            $('#jumlahBayar, #denda').on('input', function () {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                }
                $(this).val(value);
            });

            // Auto calculate total
            $('#jumlahBayar, #denda').on('input', calculateTotal);

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Calculate Total
        function calculateTotal() {
            const jumlahBayar = parseInt($('#jumlahBayar').val().replace(/[^0-9]/g, '') || 0);
            const denda = parseInt($('#denda').val().replace(/[^0-9]/g, '') || 0);
            const total = jumlahBayar + denda;

            // Update total pembayaran
            $('#totalPembayaran').text('Rp ' + total.toLocaleString('id-ID'));

            // Calculate sisa (example calculation)
            const sisaAngsuran = 5; // dari data
            const angsuranPerBulan = 983333;
            const sisa = (sisaAngsuran * angsuranPerBulan) - total;
            $('#sisaPembayaran').text('Rp ' + sisa.toLocaleString('id-ID'));
        }

        // Function: Simpan Pembayaran
        function simpanPembayaran() {
            // Validasi form
            const tglBayar = $('#tglBayar').val();
            const angsuranKe = $('#angsuranKe').val();
            const jumlahBayar = $('#jumlahBayar').val();
            const kasId = $('#kasId').val();

            if (!tglBayar) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Tanggal bayar harus diisi',
                    confirmButtonColor: '#3085d6'
                });
                $('#tglBayar').focus();
                return;
            }

            if (!angsuranKe || angsuranKe <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Angsuran ke harus diisi dengan benar',
                    confirmButtonColor: '#3085d6'
                });
                $('#angsuranKe').focus();
                return;
            }

            if (!jumlahBayar || jumlahBayar <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Jumlah bayar harus diisi',
                    confirmButtonColor: '#3085d6'
                });
                $('#jumlahBayar').focus();
                return;
            }

            if (!kasId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Kas harus dipilih',
                    confirmButtonColor: '#3085d6'
                });
                $('#kasId').focus();
                return;
            }

            // Konfirmasi pembayaran
            const totalBayar = parseInt($('#jumlahBayar').val().replace(/[^0-9]/g, ''));
            const denda = parseInt($('#denda').val().replace(/[^0-9]/g, '') || 0);
            const total = totalBayar + denda;

            Swal.fire({
                title: 'Konfirmasi Pembayaran',
                html: `
                        <div class="text-start">
                            <p class="mb-3">Pastikan data pembayaran sudah benar:</p>
                            <table class="table table-sm">
                                <tr>
                                    <td>Angsuran Ke</td>
                                    <td><strong>${angsuranKe}</strong></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Bayar</td>
                                    <td><strong>Rp ${totalBayar.toLocaleString('id-ID')}</strong></td>
                                </tr>
                                <tr>
                                    <td>Denda</td>
                                    <td><strong>Rp ${denda.toLocaleString('id-ID')}</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Total Pembayaran</strong></td>
                                    <td><strong class="text-success">Rp ${total.toLocaleString('id-ID')}</strong></td>
                                </tr>
                            </table>
                        </div>
                    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-check"></i> Ya, Proses',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#198754',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesPembayaran();
                }
            });
        }

        // Function: Proses Pembayaran
        function prosesPembayaran() {
            Swal.fire({
                title: 'Memproses Pembayaran...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // AJAX Request
            const formData = new FormData($('#formBayar')[0]);

            $.ajax({
                url: '{{ route("pinjaman.bayar.proses") }}',
                type: 'POST',
                data: Object.fromEntries(formData),
                success: function (response) {
                    $('#modalBayarAngsuran').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Berhasil!',
                        html: `
                                <div class="text-start">
                                    <p>Pembayaran angsuran telah berhasil diproses.</p>
                                    <div class="alert alert-info mt-3">
                                        <i class="ti ti-info-circle"></i> Silakan cetak bukti pembayaran untuk arsip
                                    </div>
                                </div>
                            `,
                        showCancelButton: true,
                        confirmButtonText: '<i class="ti ti-printer"></i> Cetak Bukti',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#0d6efd'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            cetakBukti($('#pinjamanId').val());
                        }
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

        // Function: Cetak Bukti
        function cetakBukti(id) {
            const url = `{{ url('pinjaman/bayar/cetak-bukti') }}/${id}`;
            window.open(url, '_blank');
        }

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