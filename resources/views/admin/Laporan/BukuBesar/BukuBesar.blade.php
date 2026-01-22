@extends('layouts.app')

@section('title', 'Laporan Buku Besar')

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
                    <input type="month" class="form-control" id="filterPeriode" value="{{ $periode }}">
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
            <strong>Periode:</strong> <span
                id="periodeText">{{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('F Y') }}</span>
        </div>
    </div>

    @forelse($bukuBesarData as $index => $data)
        <!-- Kas Account Section -->
        <div class="card mb-3 shadow-sm">
            <div
                class="card-header {{ $index % 3 == 0 ? 'bg-success-subtle' : ($index % 3 == 1 ? 'bg-warning-subtle' : 'bg-info-subtle') }}">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="ti ti-{{ $index % 3 == 0 ? 'cash' : ($index % 3 == 1 ? 'building-bank' : 'transfer') }}"></i>
                        {{ $data['kas']->nama_kas }}
                    </h5>
                    <div class="d-flex gap-3">
                        <span class="badge bg-white text-dark">
                            Saldo Awal: <strong>Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}</strong>
                        </span>
                        <span class="badge bg-white {{ $data['saldo_akhir'] < 0 ? 'text-danger' : 'text-primary' }}">
                            Saldo Akhir: <strong>Rp {{ number_format($data['saldo_akhir'], 0, ',', '.') }}</strong>
                        </span>
                    </div>
                </div>
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
                            <!-- Saldo Awal Row -->
                            <tr class="table-light">
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td colspan="2"><strong class="text-primary">SALDO AWAL PERIODE</strong></td>
                                <td class="text-end">Rp 0</td>
                                <td class="text-end">Rp 0</td>
                                <td class="text-end">
                                    <strong class="text-primary">Rp
                                        {{ number_format($data['saldo_awal'], 0, ',', '.') }}</strong>
                                </td>
                            </tr>

                            @foreach($data['transaksi'] as $idx => $item)
                                <tr>
                                    <td class="text-center">{{ $idx + 1 }}</td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}</td>
                                    <td>{{ $item->jenis_transaksi }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td class="text-end">
                                        @if($item->debet > 0)
                                            <strong class="text-success">Rp {{ number_format($item->debet, 0, ',', '.') }}</strong>
                                        @else
                                            <span class="text-muted">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($item->kredit > 0)
                                            <strong class="text-danger">Rp {{ number_format($item->kredit, 0, ',', '.') }}</strong>
                                        @else
                                            <span class="text-muted">Rp 0</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <strong class="{{ $item->saldo < 0 ? 'text-danger' : 'text-primary' }}">
                                            Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            @endforeach

                            <!-- Total Row -->
                            <tr class="table-light">
                                <td colspan="4" class="text-center">
                                    <strong class="text-primary">TOTAL MUTASI PERIODE</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp
                                        {{ number_format($data['total_debet'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">Rp
                                        {{ number_format($data['total_kredit'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $data['saldo_akhir'] < 0 ? 'text-danger' : 'text-primary' }}">
                                        Rp {{ number_format($data['saldo_akhir'], 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body py-5 text-center">
                <i class="ti ti-inbox fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">Tidak Ada Data</h5>
                <p class="text-muted">Tidak ada transaksi pada periode yang dipilih</p>
            </div>
        </div>
    @endforelse

    <!-- Total Saldo Section -->
    @if(count($bukuBesarData) > 0)
        <div class="card mb-3 shadow-sm border-primary">
            <div class="card-body py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 text-primary fw-semibold">
                        <i class="ti ti-wallet"></i> TOTAL SALDO KAS BANK
                    </h6>
                    <h5 class="mb-0 fw-semibold {{ $totalSaldo < 0 ? 'text-danger' : 'text-primary' }}" id="totalSaldoKasBank">
                        Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                    </h5>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

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

            // Redirect dengan parameter filter
            window.location.href = `{{ route('laporan.buku-besar') }}?periode=${periode}`;
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

            // Reload halaman tanpa parameter
            setTimeout(() => {
                window.location.href = `{{ route('laporan.buku-besar') }}`;
            }, 1500);
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
                                    <td>Total Saldo</td>
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
                            Sertakan ringkasan total
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="separateSheet" checked>
                        <label class="form-check-label" for="separateSheet">
                            Pisahkan per akun kas (sheet terpisah)
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="includeChart">
                        <label class="form-check-label" for="includeChart">
                            Sertakan grafik mutasi
                        </label>
                    </div>
                    <hr>
                    <p class="text-muted small"><i class="ti ti-info-circle"></i> File akan otomatis terunduh</p>
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
                },
                preConfirm: () => {
                    return {
                        summary: document.getElementById('includeSummary').checked,
                        separate: document.getElementById('separateSheet').checked,
                        chart: document.getElementById('includeChart').checked
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const options = result.value;

                    Swal.fire({
                        title: 'Mengekspor...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Build URL with parameters - sesuai route buku-besar.export.excel
                    const url = `{{ route('laporan.buku-besar.export.excel') }}?periode=${periode}&summary=${options.summary ? 1 : 0}&separate=${options.separate ? 1 : 0}&chart=${options.chart ? 1 : 0}`;

                    // Create temporary link and trigger download
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = `Laporan_Buku_Besar_${periode}.xlsx`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Show success message after short delay
                    setTimeout(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'File Excel berhasil diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 500);
                }
            });
        }

        // Event listener for periode change
        $('#filterPeriode').on('change', function () {
            updatePeriodeText();
        });
    </script>
@endpush