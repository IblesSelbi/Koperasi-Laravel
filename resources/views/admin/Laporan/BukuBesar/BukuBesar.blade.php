@extends('layouts.app')

@section('title', 'Laporan Buku Besar')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Buku Besar</h4>
                    <p class="text-muted fs-3 mb-0">Laporan mutasi kas per akun periode tertentu</p>
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
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter Periode</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Filter Periode -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar text-primary"></i> Pilih Periode
                    </label>
                    <input type="month" class="form-control" id="filterPeriode" value="{{ date('Y-m') }}">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">
                            <i class="ti ti-search"></i> Tampilkan
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetFilter()"
                            data-bs-toggle="tooltip" title="Reset Filter">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>

                <!-- Info Summary -->
                <div class="col-12 col-lg-6">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2 flex-wrap justify-content-lg-end">
                        <span class="badge bg-info-subtle text-info shadow-sm px-3 py-2">
                            <i class="ti ti-books"></i> Total Akun:
                            <strong id="totalAkun">{{ $totalAkun }}</strong>
                        </span>
                        <span class="badge bg-primary-subtle text-primary shadow-sm px-3 py-2">
                            <i class="ti ti-wallet"></i> Total Saldo:
                            <strong id="totalSaldo">Rp {{ number_format($totalSaldo, 0, ',', '.') }}</strong>
                        </span>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Info -->
    <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
        <i class="ti ti-info-circle fs-5 me-2"></i>
        <div>
            <strong>Periode:</strong> <span id="periodeText">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('F Y') }}</span>
        </div>
    </div>

    <!-- Kas Tunai Section -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-success-subtle text-white">
            <h5 class="mb-0"><i class="ti ti-cash"></i> Kas Tunai</h5>
        </div>
        <div class="card-body">
           <div class="table-responsive shadow-sm rounded-2 border">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 10%;">Tanggal</th>
                            <th class="align-middle" style="width: 20%;">Jenis Transaksi</th>
                            <th class="align-middle" style="width: 35%;">Keterangan</th>
                            <th class="text-end align-middle" style="width: 10%;">Debet</th>
                            <th class="text-end align-middle" style="width: 10%;">Kredit</th>
                            <th class="text-end align-middle" style="width: 10%;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasTunai as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->jenis_transaksi }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-end">
                                    @if($item->debet > 0)
                                        <strong class="text-success">Rp {{ number_format($item->debet, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->kredit > 0)
                                        <strong class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $item->saldo < 0 ? 'text-danger' : 'text-primary' }}">
                                        Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-inbox fs-4"></i><br>
                                    Tidak ada transaksi pada periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kas Besar Section -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-warning-subtle text-dark">
            <h5 class="mb-0"><i class="ti ti-building-bank"></i> Kas Besar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 10%;">Tanggal</th>
                            <th class="align-middle" style="width: 20%;">Jenis Transaksi</th>
                            <th class="align-middle" style="width: 35%;">Keterangan</th>
                            <th class="text-end align-middle" style="width: 10%;">Debet</th>
                            <th class="text-end align-middle" style="width: 10%;">Kredit</th>
                            <th class="text-end align-middle" style="width: 10%;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasBesar as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->jenis_transaksi }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-end">
                                    @if($item->debet > 0)
                                        <strong class="text-success">Rp {{ number_format($item->debet, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->kredit > 0)
                                        <strong class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $item->saldo < 0 ? 'text-danger' : 'text-primary' }}">
                                        Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-inbox fs-4"></i><br>
                                    Tidak ada transaksi pada periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Transfer Section -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-info-subtle text-white">
            <h5 class="mb-0"><i class="ti ti-transfer"></i> Transfer</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 10%;">Tanggal</th>
                            <th class="align-middle" style="width: 20%;">Jenis Transaksi</th>
                            <th class="align-middle" style="width: 35%;">Keterangan</th>
                            <th class="text-end align-middle" style="width: 10%;">Debet</th>
                            <th class="text-end align-middle" style="width: 10%;">Kredit</th>
                            <th class="text-end align-middle" style="width: 10%;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfer as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->jenis_transaksi }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="text-end">
                                    @if($item->debet > 0)
                                        <strong class="text-success">Rp {{ number_format($item->debet, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->kredit > 0)
                                        <strong class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</strong>
                                    @else
                                        Rp 0
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $item->saldo < 0 ? 'text-danger' : 'text-primary' }}">
                                        Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="ti ti-inbox fs-4"></i><br>
                                    Tidak ada transaksi pada periode ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Total Saldo Section -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body py-2">
            <div class="table-responsive">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-end align-middle py-2">
                            <strong class="text-primary fs-4">TOTAL SALDO KAS BANK</strong>
                        </td>
                        <td class="text-end align-middle py-2" style="width: 15%;">
                            <strong class="{{ $totalSaldo < 0 ? 'text-danger' : 'text-primary' }} fs-4" id="totalSaldoKasBank">
                                Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize
        $(document).ready(function () {
            updatePeriodeText();

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Function: Update Periode Text
        function updatePeriodeText() {
            const periodeInput = document.getElementById('filterPeriode').value;
            if (periodeInput) {
                const [year, month] = periodeInput.split('-');
                const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                const periodeText = `${monthNames[parseInt(month) - 1]} ${year}`;
                document.getElementById('periodeText').textContent = periodeText;
            }
        }

        // Function: Format Rupiah
        function formatRupiah(angka) {
            const isNegative = angka < 0;
            const absolute = Math.abs(angka);
            const formatted = 'Rp ' + absolute.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return isNegative ? 'Rp -' + absolute.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : formatted;
        }

        // Function: Filter Data
        function filterData() {
            const periode = $('#filterPeriode').val();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang memuat data buku besar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Simulasi load data
            setTimeout(() => {
                updatePeriodeText();

                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data buku besar berhasil dimuat',
                    timer: 1500,
                    showConfirmButton: false
                });

                // TODO: Reload dengan filter
                // location.href = `{{ route('laporan.buku-besar') }}?periode=${periode}`;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            // Reset to current month
            const now = new Date();
            const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
            $('#filterPeriode').val(currentMonth);

            updatePeriodeText();

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Periode dikembalikan ke bulan ini',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Function: Cetak Laporan
        function cetakLaporan() {
            const periode = $('#filterPeriode').val();
            const periodeText = $('#periodeText').text();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Cetak Laporan',
                html: `
                    <div class="text-start">
                        <p><strong>Laporan Buku Besar</strong></p>
                        <hr>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Periode</td>
                                <td><strong>${periodeText}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Akun</td>
                                <td><strong>${$('#totalAkun').text()} akun</strong></td>
                            </tr>
                            <tr>
                                <td>Total Saldo Kas Bank</td>
                                <td><strong class="text-primary">${$('#totalSaldo').text()}</strong></td>
                            </tr>
                        </table>
                        <hr>
                        <p class="text-muted">Laporan akan dibuka di tab baru</p>
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
                    const url = `{{ route('laporan.buku-besar.cetak') }}?periode=${periode}`;
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
            const periode = $('#filterPeriode').val();
            const periodeText = $('#periodeText').text();

            if (!periode) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih periode terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: 'Export ke Excel',
                html: `
                    <div class="text-start">
                        <p>Data buku besar periode <strong>${periodeText}</strong> akan diekspor ke format Excel (.xlsx)</p>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="includeSummary" checked>
                            <label class="form-check-label" for="includeSummary">
                                Sertakan ringkasan total saldo
                            </label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="separateSheets" checked>
                            <label class="form-check-label" for="separateSheets">
                                Pisahkan setiap akun ke sheet berbeda
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeChart">
                            <label class="form-check-label" for="includeChart">
                                Sertakan grafik mutasi kas
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
                        const includeSummary = document.getElementById('includeSummary').checked;
                        const separateSheets = document.getElementById('separateSheets').checked;
                        const includeChart = document.getElementById('includeChart').checked;

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'File Excel berhasil diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // TODO: Implement download
                        // const url = `{{ route('laporan.buku-besar.export.excel') }}?periode=${periode}&summary=${includeSummary}&separate=${separateSheets}&chart=${includeChart}`;
                        // window.location.href = url;
                    }, 1500);
                }
            });
        }

        // Update periode text on change
        document.getElementById('filterPeriode').addEventListener('change', function () {
            updatePeriodeText();
        });
    </script>
@endpush