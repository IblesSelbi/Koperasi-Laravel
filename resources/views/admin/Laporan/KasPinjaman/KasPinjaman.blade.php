@extends('layouts.app')

@section('title', 'Laporan Data Kas Pinjaman')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Data Kas Pinjaman</h4>
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
            <form id="fmCari" method="GET" action="{{ route('laporan.kas-pinjaman') }}">
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

    <!-- Summary Statistics Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h5 class="fw-semibold mb-4">
                <i class="ti ti-file-analytics text-primary me-2"></i>Ringkasan Data
            </h5>

            <div class="row g-3">
                <!-- Jumlah Peminjam -->
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100 shadow-sm bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="icon-circle bg-info-subtle">
                                    <i class="ti ti-users text-info-emphasis fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Jumlah Peminjam</small>
                                <h4 class="fw-bold mb-0">{{ $summary->jumlah_peminjam }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Peminjam Lunas -->
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100 shadow-sm bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="icon-circle bg-success-subtle">
                                    <i class="ti ti-circle-check text-success-emphasis fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Peminjam Lunas</small>
                                <h4 class="fw-bold mb-0">{{ $summary->peminjam_lunas }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Belum Lunas -->
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100 shadow-sm bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="icon-circle bg-warning-subtle">
                                    <i class="ti ti-clock-hour-3 text-warning-emphasis fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted">Belum Lunas</small>
                                <h4 class="fw-bold mb-0">{{ $summary->belum_lunas }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table id="tabelPinjaman" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="width:5%; vertical-align: middle; text-align:center">No</th>
                            <th style="width:35%; vertical-align: middle; text-align:center">Keterangan</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kasPinjaman as $index => $item)
                            @if($item->no <= 3)
                                <tr>
                                    <td class="h_tengah">{{ $item->no }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td class="h_kanan">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        
                        <tr class="table-light">
                            <td class="h_tengah header_kolom"></td>
                            <td class="header_kolom">Jumlah Tagihan + Denda</td>
                            <td class="h_kanan header_kolom">{{ number_format($jumlahTagihanDenda, 0, ',', '.') }}</td>
                        </tr>
                        
                        @foreach($kasPinjaman as $index => $item)
                            @if($item->no == 4)
                                <tr>
                                    <td class="h_tengah">{{ $item->no }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td class="h_kanan">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        @endforeach
                        
                        @foreach($kasPinjaman as $index => $item)
                            @if($item->no == 5)
                                <tr style="background-color: #d1f2dd;">
                                    <td class="h_tengah">{{ $item->no }}</td>
                                    <td><strong>{{ $item->keterangan }}</strong></td>
                                    <td class="h_kanan"><strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
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
            window.location.href = '{{ route("laporan.kas-pinjaman") }}';
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

            const url = `{{ route('laporan.kas-pinjaman.cetak') }}?tgl_dari=${tglDari}&tgl_samp=${tglSamp}`;
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

        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush