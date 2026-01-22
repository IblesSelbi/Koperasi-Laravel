@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@push('styles')
     <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        .h_tengah {
            text-align: center;
        }

        .h_kanan {
            text-align: right;
        }

        .header_kolom {
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Laba Rugi</h4>
                    <p class="text-muted fs-3 mb-0">{{ \Carbon\Carbon::parse($tglDari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tglSamp)->format('d M Y') }}</p>
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

    <!-- Filter Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter Periode</h6>
        </div>
        <div class="card-body p-4">
            <form id="fmCari" method="GET" action="{{ route('laporan.laba-rugi') }}">
                <input type="hidden" name="tgl_dari" id="tgl_dari" value="{{ $tglDari }}">
                <input type="hidden" name="tgl_samp" id="tgl_samp" value="{{ $tglSamp }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-calendar text-primary"></i> Rentang Tanggal
                        </label>
                        <button class="form-control text-start" type="button" id="daterange-btn">
                            <i class="ti ti-calendar me-2"></i>
                            <span id="reportrange">{{ \Carbon\Carbon::parse($tglDari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tglSamp)->format('d M Y') }}</span>
                            <i class="ti ti-chevron-down float-end"></i>
                        </button>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2 d-none d-md-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary" onclick="doSearch()">
                                <i class="ti ti-search"></i> Tampilkan
                            </button>
                            <button type="button" class="btn btn-info" onclick="cetak()">
                                <i class="ti ti-printer"></i> Cetak
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                                <i class="ti ti-refresh"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Estimasi Data Pinjaman -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary-subtle">
            <h5 class="mb-0 fw-semibold">
                <i class="ti ti-chart-line me-2"></i>Estimasi Data Pinjaman
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="width:5%; vertical-align: middle; text-align:center">No.</th>
                            <th style="width:75%; vertical-align: middle; text-align:center">Keterangan</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estimasiPinjaman as $item)
                            <tr>
                                <td class="h_tengah">{{ $item->no }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="h_kanan">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">Tidak ada data pinjaman</td>
                            </tr>
                        @endforelse
                        @if($estimasiPinjaman->isNotEmpty())
                            <tr class="table-light">
                                <td colspan="2" class="h_kanan header_kolom">Jumlah Tagihan</td>
                                <td class="h_kanan header_kolom">{{ number_format($jumlahTagihan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="h_kanan"><strong>Estimasi Pendapatan Pinjaman</strong></td>
                                <td class="h_kanan"><strong>{{ number_format($estimasiPendapatanPinjaman, 0, ',', '.') }}</strong></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pendapatan -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-success-subtle">
            <h5 class="mb-0 fw-semibold">
                <i class="ti ti-trending-up me-2"></i>Pendapatan
            </h5>
        </div>
        <div class="card-body">
           <div class="table-responsive shadow-sm rounded-2 border">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="width:5%; vertical-align: middle; text-align:center">No.</th>
                            <th style="width:75%; vertical-align: middle; text-align:center">Keterangan</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendapatanList as $item)
                            <tr>
                                <td class="h_tengah">{{ $item->no }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="h_kanan">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">Tidak ada pendapatan</td>
                            </tr>
                        @endforelse
                        @if($pendapatanList->isNotEmpty())
                            <tr class="table-light">
                                <td colspan="2" class="h_kanan header_kolom">Jumlah Pendapatan</td>
                                <td class="h_kanan header_kolom">{{ number_format($jumlahPendapatan, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Biaya-biaya -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-danger-subtle">
            <h5 class="mb-0 fw-semibold">
                <i class="ti ti-trending-down me-2"></i>Biaya-biaya
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="width:5%; vertical-align: middle; text-align:center">No.</th>
                            <th style="width:75%; vertical-align: middle; text-align:center">Keterangan</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($biayaList as $item)
                            <tr>
                                <td class="h_tengah">{{ $item->no }}</td>
                                <td>{{ $item->keterangan }}</td>
                                <td class="h_kanan">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">Tidak ada biaya</td>
                            </tr>
                        @endforelse
                        @if($biayaList->isNotEmpty())
                            <tr class="table-light">
                                <td colspan="2" class="h_kanan header_kolom">Jumlah Biaya</td>
                                <td class="h_kanan header_kolom">{{ number_format($jumlahBiaya, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Laba Rugi Summary -->
    <div class="card shadow-sm">
        <div class="card-body py-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="ti ti-calculator me-3 fs-4"></i>
                    <span class="fw-semibold fs-4">LABA RUGI</span>
                </div>

                <span class="fw-semibold {{ $labaRugi < 0 ? 'text-danger' : 'text-success' }} fs-5">
                    {{ $labaRugi < 0 ? '(' : '' }}{{ number_format(abs($labaRugi), 0, ',', '.') }}{{ $labaRugi < 0 ? ')' : '' }}
                </span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize Daterangepicker
        $(document).ready(function () {
            $('#daterange-btn').daterangepicker({
                ranges: {
                    'Tahun ini': [moment().startOf('year').startOf('month'), moment().endOf('year').endOf('month')],
                    'Tahun kemarin': [moment().subtract('year', 1).startOf('year').startOf('month'), moment().subtract('year', 1).endOf('year').endOf('month')]
                },
                locale: {
                    format: 'DD MMM YYYY',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    fromLabel: 'Dari',
                    toLabel: 'Sampai',
                    customRangeLabel: 'Kustom',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                    firstDay: 1
                },
                showDropdowns: true,
                startDate: '{{ $tglDari }}',
                endDate: '{{ $tglSamp }}'
            }, function (start, end) {
                $('#reportrange').html(start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
                $('#tgl_dari').val(start.format('YYYY-MM-DD'));
                $('#tgl_samp').val(end.format('YYYY-MM-DD'));
            });
        });

        // Function: Clear Search
        function clearSearch() {
            window.location.href = '{{ route("laporan.laba-rugi") }}';
        }

        // Function: Do Search
        function doSearch() {
            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang mengambil data',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                $('#fmCari').submit();
            }, 500);
        }

        // Function: Cetak Laporan
        function cetak() {
            const tglDari = $('#tgl_dari').val();
            const tglSamp = $('#tgl_samp').val();

            const url = `{{ route('laporan.laba-rugi.cetak') }}?tgl_dari=${tglDari}&tgl_samp=${tglSamp}`;
            const win = window.open(url, '_blank');
            
            if (win) {
                win.focus();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Popup Diblokir',
                    text: 'Mohon izinkan popup untuk mencetak laporan',
                });
            }
        }
    </script>

@endpush