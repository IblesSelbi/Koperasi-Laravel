@extends('layouts.app')

@section('title', 'Laporan Transaksi Kas')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Transaksi Kas</h4>
                    <p class="text-muted fs-3 mb-0">Data transaksi kas masuk dan keluar per periode</p>
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
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter Rentang Tanggal</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Filter Date Range -->
                <div class="col-md-6 col-lg-4">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar-event text-primary"></i> Pilih Periode Tanggal
                    </label>
                    <input type="text" class="form-control" id="filterDateRange" readonly>
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">
                            <i class="ti ti-search"></i> Tampilkan
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetFilter()" data-bs-toggle="tooltip"
                            title="Reset Filter">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>

                <!-- Info Summary -->
                <div class="col-12 col-lg-5">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                        <span class="badge bg-success-subtle text-success shadow-sm px-3 py-2">
                            <i class="ti ti-arrow-down-circle"></i> Total Debet:
                            <strong id="totalDebet">Rp {{ number_format($totalDebet, 0, ',', '.') }}</strong>
                        </span>
                        <span class="badge bg-danger-subtle text-danger shadow-sm px-3 py-2">
                            <i class="ti ti-arrow-up-circle"></i> Total Kredit:
                            <strong id="totalKredit">Rp {{ number_format($totalKredit, 0, ',', '.') }}</strong>
                        </span>
                        <span class="badge bg-primary-subtle text-primary shadow-sm px-3 py-2">
                            <i class="ti ti-wallet"></i> Saldo:
                            <strong id="saldoAkhir">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Secondary Actions -->
            <div class="row mt-3 pt-3 border-top">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap align-items-center">
                        <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <button class="btn btn-success btn-sm" onclick="exportExcel()">
                            <i class="ti ti-file-spreadsheet"></i> Export Excel
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="exportPDF()">
                            <i class="ti ti-file-text"></i> Export PDF
                        </button>

                        <span class="badge bg-info-subtle text-info shadow-sm border-2 px-3 py-2 ms-auto">
                            <i class="ti ti-file-text"></i> Total Transaksi:
                            <strong id="totalData">{{ $totalData }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Info -->
    <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
        <i class="ti ti-info-circle fs-5 me-2"></i>
        <div>
            <strong>Periode:</strong> <span
                id="periodeText">{{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') }} -
                {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y') }}</span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive shadow-sm">
                <table id="tabelTransaksiKas" class="table table-hover align-middle rounded-2 border-1 overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 10%;">Kode<br>Transaksi</th>
                            <th class="text-center align-middle" style="width: 10%;">Tanggal<br>Transaksi</th>
                            <th class="align-middle" style="width: 25%;">Akun Transaksi</th>
                            <th class="text-center align-middle" style="width: 10%;">Dari Kas</th>
                            <th class="text-center align-middle" style="width: 10%;">Untuk Kas</th>
                            <th class="text-end align-middle" style="width: 13%;">Debet</th>
                            <th class="text-end align-middle" style="width: 13%;">Kredit</th>
                            <th class="text-end align-middle" style="width: 13%;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo Sebelumnya Row -->
                        <tr class="table-light saldo-sebelumnya">
                            <td class="text-center align-middle">-</td>
                            <td class="text-center align-middle">-</td>
                            <td class="text-center align-middle">-</td>
                            <td class="align-middle">
                                <strong class="text-primary">SALDO SEBELUMNYA</strong>
                            </td>
                            <td class="text-center align-middle">-</td>
                            <td class="text-center align-middle">-</td>
                            <td class="text-end align-middle">
                                <span class="text-muted">Rp 0</span>
                            </td>
                            <td class="text-end align-middle">
                                <span class="text-muted">Rp 0</span>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-primary fs-5" id="saldoSebelumnyaText">Rp
                                    {{ number_format($saldoSebelumnya, 0, ',', '.') }}</strong>
                            </td>
                        </tr>

                        @foreach($transaksiKas as $index => $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-semibold">{{ $index + 1 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span
                                        class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">{{ $item->kode_transaksi }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span
                                        class="text-muted">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->locale('id')->translatedFormat('d M Y') }}</span>
                                </td>
                                <td class="align-middle">
                                    <strong class="text-dark">{{ $item->akun_transaksi }}</strong><br>
                                    <small class="text-muted">{{ $item->keterangan }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    @if($item->dari_kas && $item->dari_kas != '-')
                                        <span class="badge bg-success-subtle text-success">{{ $item->dari_kas }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($item->untuk_kas && $item->untuk_kas != '-')
                                        <span class="badge bg-danger-subtle text-danger">{{ $item->untuk_kas }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end align-middle">
                                    @if($item->debet > 0)
                                        <strong class="text-success">Rp {{ number_format($item->debet, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-end align-middle">
                                    @if($item->kredit > 0)
                                        <strong class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-end align-middle text-nowrap">
                                    <strong class="text-primary">Rp {{ number_format($item->saldo, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="6" class="text-center align-middle">
                                <strong class="text-primary">TOTAL TRANSAKSI</strong>
                            </td>
                            <td class="text-end align-middle text-nowrap">
                                <strong class="text-success fs-5">Rp {{ number_format($totalDebet, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end align-middle text-nowrap">
                                <strong class="text-danger fs-5">Rp {{ number_format($totalKredit, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end align-middle text-nowrap">
                                <strong class="text-primary fs-5">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    </tfoot>
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

    <!-- Moment.js & Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            // Simpan nilai original dari server
            const originalTotalDebet = {{ $totalDebet }};
            const originalTotalKredit = {{ $totalKredit }};
            const originalSaldoAkhir = {{ $saldoAkhir }};
            const originalSaldoSebelumnya = {{ $saldoSebelumnya }};
            const originalTotalData = {{ $totalData }};

            table = $('#tabelTransaksiKas').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 25,
                order: [[2, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0] }
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();

                    // Helper function to parse currency
                    var intVal = function (i) {
                        if (typeof i === 'string') {
                            // Remove "Rp", spaces, and dots, then convert to number
                            return parseFloat(i.replace(/Rp\s?/g, '').replace(/\./g, '').replace(/,/g, '.')) || 0;
                        }
                        return typeof i === 'number' ? i : 0;
                    };

                    // Calculate totals (exclude saldo sebelumnya row)
                    var totalDebet = 0;
                    var totalKredit = 0;
                    var saldoSebelumnya = originalSaldoSebelumnya;

                    // Loop through visible rows only
                    api.rows({ search: 'applied' }).every(function () {
                        var rowNode = this.node();

                        // Skip saldo sebelumnya row
                        if ($(rowNode).hasClass('saldo-sebelumnya')) {
                            return;
                        }

                        // Get the cell values using jQuery (more reliable)
                        var debetCell = $(rowNode).find('td').eq(6).text().trim();
                        var kreditCell = $(rowNode).find('td').eq(7).text().trim();

                        totalDebet += intVal(debetCell);
                        totalKredit += intVal(kreditCell);
                    });

                    // Calculate saldo akhir with saldo sebelumnya
                    var saldoAkhir = saldoSebelumnya + totalDebet - totalKredit;

                    // Update footer
                    $(api.column(6).footer()).html(
                        '<strong class="text-success fs-5">' + formatRupiah(totalDebet) + '</strong>'
                    );
                    $(api.column(7).footer()).html(
                        '<strong class="text-danger fs-5">' + formatRupiah(totalKredit) + '</strong>'
                    );
                    $(api.column(8).footer()).html(
                        '<strong class="text-primary fs-5">' + formatRupiah(saldoAkhir) + '</strong>'
                    );

                    // Update summary badges
                    $('#totalDebet').text(formatRupiah(totalDebet));
                    $('#totalKredit').text(formatRupiah(totalKredit));
                    $('#saldoAkhir').text(formatRupiah(saldoAkhir));

                    // Update total data count (exclude saldo sebelumnya)
                    var countData = api.rows({ search: 'applied' }).count();
                    if (api.rows({ search: 'applied' }).nodes().to$().hasClass('saldo-sebelumnya').length > 0) {
                        countData = countData - 1;
                    }
                    $('#totalData').text(countData);
                },
                drawCallback: function () {
                    // Re-calculate on every draw (including page change, search, etc)
                    var api = this.api();

                    // Trigger footer callback
                    var settings = api.settings()[0];
                    if (settings.oInit.footerCallback) {
                        settings.oInit.footerCallback.call(this,
                            settings.aoFooter[0],
                            api.rows({ page: 'current' }).data(),
                            0,
                            0,
                            api.rows({ search: 'applied' }).indexes()
                        );
                    }
                },
                initComplete: function () {
                    // Set nilai awal dari server
                    $('#totalDebet').text(formatRupiah(originalTotalDebet));
                    $('#totalKredit').text(formatRupiah(originalTotalKredit));
                    $('#saldoAkhir').text(formatRupiah(originalSaldoAkhir));
                    $('#totalData').text(originalTotalData);

                    // Update footer dengan nilai dari server
                    var api = this.api();
                    $(api.column(6).footer()).html(
                        '<strong class="text-success fs-5">' + formatRupiah(originalTotalDebet) + '</strong>'
                    );
                    $(api.column(7).footer()).html(
                        '<strong class="text-danger fs-5">' + formatRupiah(originalTotalKredit) + '</strong>'
                    );
                    $(api.column(8).footer()).html(
                        '<strong class="text-primary fs-5">' + formatRupiah(originalSaldoAkhir) + '</strong>'
                    );
                }
            });

            // Initialize Daterangepicker
            const startDate = moment('{{ $startDate }}');
            const endDate = moment('{{ $endDate }}');

            $('#filterDateRange').daterangepicker({
                startDate: startDate,
                endDate: endDate,
                locale: {
                    format: 'DD MMM YYYY',
                    separator: ' - ',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Sampai',
                    customRangeLabel: 'Custom',
                    weekLabel: 'W',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    firstDay: 1
                },
                ranges: {
                    'Hari Ini': [moment(), moment()],
                    'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                    '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                    'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan Lalu': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                    'Tahun Lalu': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            });

            // Update periode text on change
            $('#filterDateRange').on('apply.daterangepicker', function (ev, picker) {
                updatePeriodeText();
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Format Rupiah
        function formatRupiah(angka) {
            var number_string = angka.toString().replace(/[^,\d]/g, '');
            var split = number_string.split(',');
            var sisa = split[0].length % 3;
            var rupiah = split[0].substr(0, sisa);
            var ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return 'Rp ' + rupiah;
        }

        // Function: Update Periode Text
        function updatePeriodeText() {
            const dateRange = $('#filterDateRange').val();
            if (dateRange) {
                document.getElementById('periodeText').textContent = dateRange;
            }
        }

        // Function: Filter Data
        function filterData() {
            const dateRange = $('#filterDateRange').val();

            if (!dateRange) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih rentang tanggal terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengambil data transaksi kas',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            const dates = dateRange.split(' - ');
            const startDate = moment(dates[0], 'DD MMM YYYY').format('YYYY-MM-DD');
            const endDate = moment(dates[1], 'DD MMM YYYY').format('YYYY-MM-DD');

            // Redirect dengan parameter filter
            window.location.href = `{{ route('laporan.transaksi-kas') }}?start_date=${startDate}&end_date=${endDate}`;
        }

        // Function: Reset Filter
        function resetFilter() {
            const today = new Date();
            const startOfYear = new Date(today.getFullYear(), 0, 1);
            const endOfYear = new Date(today.getFullYear(), 11, 31);

            $('#filterDateRange').data('daterangepicker').setStartDate(startOfYear);
            $('#filterDateRange').data('daterangepicker').setEndDate(endOfYear);

            updatePeriodeText();

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Filter dikembalikan ke tahun ini',
                timer: 1500,
                showConfirmButton: false
            });

            // Reload halaman tanpa parameter
            setTimeout(() => {
                window.location.href = `{{ route('laporan.transaksi-kas') }}`;
            }, 1500);
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const dateRange = $('#filterDateRange').val();

            if (!dateRange) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih rentang tanggal terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Pilih Format Laporan',
                html: `
                                <div class="text-start">
                                    <p class="mb-3">Pilih format laporan yang akan dicetak:</p>
                                    <div class="list-group">
                                        <a href="#" class="list-group-item list-group-item-action" onclick="cetakLaporanFormat('ringkas')">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="ti ti-file-text text-info"></i> Laporan Ringkas</h6>
                                            </div>
                                            <p class="mb-1 text-muted small">Tampilan sederhana dengan informasi penting</p>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" onclick="cetakLaporanFormat('lengkap')">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="ti ti-file-description text-primary"></i> Laporan Lengkap</h6>
                                            </div>
                                            <p class="mb-1 text-muted small">Tampilan detail dengan semua informasi transaksi</p>
                                        </a>
                                        <a href="#" class="list-group-item list-group-item-action" onclick="cetakLaporanFormat('summary')">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h6 class="mb-1"><i class="ti ti-chart-bar text-success"></i> Laporan Summary</h6>
                                            </div>
                                            <p class="mb-1 text-muted small">Ringkasan dengan grafik dan statistik</p>
                                        </a>
                                    </div>
                                </div>`,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                width: '600px',
                customClass: {
                    cancelButton: 'btn btn-secondary'
                }
            });
        }

        // Function: Cetak Laporan Format
        function cetakLaporanFormat(format) {
            Swal.close();
            const dateRange = $('#filterDateRange').val();
            const dates = dateRange.split(' - ');
            const startDate = moment(dates[0], 'DD MMM YYYY').format('YYYY-MM-DD');
            const endDate = moment(dates[1], 'DD MMM YYYY').format('YYYY-MM-DD');

            Swal.fire({
                icon: 'info',
                title: 'Membuka Jendela Cetak',
                text: 'Laporan akan dibuka di tab baru',
                timer: 1500,
                showConfirmButton: false
            });

            const url = `{{ route('laporan.transaksi-kas.cetak') }}?start_date=${startDate}&end_date=${endDate}&format=${format}`;
            window.open(url, '_blank');
        }

        // Function: Export Excel
        function exportExcel() {
            const dateRange = $('#filterDateRange').val();

            if (!dateRange) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih rentang tanggal terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Export ke Excel',
                html: `
                                <div class="text-start">
                                    <p class="mb-3">Pilih format export:</p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Format File</label>
                                        <select id="swal-format" class="form-select">
                                            <option value="xlsx">Excel (.xlsx)</option>
                                            <option value="xls">Excel 97-2003 (.xls)</option>
                                            <option value="csv">CSV (.csv)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Template</label>
                                        <select id="swal-template" class="form-select">
                                            <option value="standard">Standard</option>
                                            <option value="detailed">Detailed</option>
                                            <option value="summary">Summary Only</option>
                                        </select>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="swal-include-chart" checked>
                                        <label class="form-check-label" for="swal-include-chart">Sertakan grafik</label>
                                    </div>
                                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-download"></i> Export',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#198754',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return {
                        format: document.getElementById('swal-format').value,
                        template: document.getElementById('swal-template').value,
                        includeChart: document.getElementById('swal-include-chart').checked
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Membuat File...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Berhasil!',
                            text: 'File akan segera diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const dates = $('#filterDateRange').val().split(' - ');
                        const startDate = moment(dates[0], 'DD MMM YYYY').format('YYYY-MM-DD');
                        const endDate = moment(dates[1], 'DD MMM YYYY').format('YYYY-MM-DD');
                        window.location.href = `{{ route('laporan.transaksi-kas.export.excel') }}?start_date=${startDate}&end_date=${endDate}&format=${result.value.format}&template=${result.value.template}`;
                    }, 1500);
                }
            });
        }

        // Function: Export PDF
        function exportPDF() {
            const dateRange = $('#filterDateRange').val();

            if (!dateRange) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih rentang tanggal terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Export ke PDF',
                html: `
                                <div class="text-start">
                                    <p class="mb-3">Konfigurasi export PDF:</p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Orientasi Halaman</label>
                                        <select id="swal-orientasi" class="form-select">
                                            <option value="portrait">Portrait (Tegak)</option>
                                            <option value="landscape">Landscape (Mendatar)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Ukuran Kertas</label>
                                        <select id="swal-paper" class="form-select">
                                            <option value="A4">A4</option>
                                            <option value="Letter">Letter</option>
                                            <option value="Legal">Legal</option>
                                            <option value="F4">F4</option>
                                        </select>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="swal-include-header" checked>
                                        <label class="form-check-label" for="swal-include-header">Sertakan header koperasi</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="swal-include-footer" checked>
                                        <label class="form-check-label" for="swal-include-footer">Sertakan footer dan tanda tangan</label>
                                    </div>
                                </div>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-file-type-pdf"></i> Export PDF',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                preConfirm: () => {
                    return {
                        orientasi: document.getElementById('swal-orientasi').value,
                        paper: document.getElementById('swal-paper').value,
                        includeHeader: document.getElementById('swal-include-header').checked,
                        includeFooter: document.getElementById('swal-include-footer').checked
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Membuat PDF...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Export Berhasil!',
                            text: 'File PDF akan segera diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const dates = $('#filterDateRange').val().split(' - ');
                        const startDate = moment(dates[0], 'DD MMM YYYY').format('YYYY-MM-DD');
                        const endDate = moment(dates[1], 'DD MMM YYYY').format('YYYY-MM-DD');
                        window.location.href = `{{ route('laporan.transaksi-kas.export.pdf') }}?start_date=${startDate}&end_date=${endDate}&orientasi=${result.value.orientasi}&paper=${result.value.paper}`;
                    }, 1500);
                }
            });
        }
    </script>
@endpush