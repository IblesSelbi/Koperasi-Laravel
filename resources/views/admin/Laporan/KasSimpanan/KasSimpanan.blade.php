@extends('layouts.app')

@section('title', 'Laporan Data Simpanan')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    
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
                    <h4 class="fw-semibold mb-1">Laporan Data Simpanan</h4>
                    <p class="text-muted fs-3 mb-0">Periode {{ \Carbon\Carbon::parse($tglDari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tglSamp)->format('d M Y') }}</p>
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
            <form id="fmCari" method="GET" action="{{ route('laporan.kas-simpanan') }}">
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

    <!-- Data Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table id="tabelSimpanan" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="width:5%; vertical-align: middle; text-align:center">No</th>
                            <th style="width:35%; vertical-align: middle; text-align:center">Jenis Akun</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Simpanan</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Penarikan</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kasSimpanan as $item)
                            <tr>
                                <td class="h_tengah">{{ $item->no }}</td>
                                <td>{{ $item->jenis_akun }}</td>
                                <td class="h_kanan">{{ number_format($item->simpanan, 0, ',', '.') }}</td>
                                <td class="h_kanan">{{ number_format($item->penarikan, 0, ',', '.') }}</td>
                                <td class="h_kanan">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="ti ti-database-off fs-2"></i>
                                    <p class="mb-0 mt-2">Tidak ada data untuk periode yang dipilih</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light border-bottom">
                        <tr class="header_kolom">
                            <td colspan="2" class="h_tengah"><strong>Jumlah Total</strong></td>
                            <td class="h_kanan"><strong>{{ number_format($totalSimpanan, 0, ',', '.') }}</strong></td>
                            <td class="h_kanan"><strong>{{ number_format($totalPenarikan, 0, ',', '.') }}</strong></td>
                            <td class="h_kanan"><strong>{{ number_format($totalJumlah, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
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
                    'Hari ini': [moment(), moment()],
                    'Kemarin': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    '7 Hari yang lalu': [moment().subtract('days', 6), moment()],
                    '30 Hari yang lalu': [moment().subtract('days', 29), moment()],
                    'Bulan ini': [moment().startOf('month'), moment().endOf('month')],
                    'Bulan kemarin': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')],
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
            window.location.href = '{{ route("laporan.kas-simpanan") }}';
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

            const url = `{{ route('laporan.kas-simpanan.cetak') }}?tgl_dari=${tglDari}&tgl_samp=${tglSamp}`;
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