@extends('layouts.app')

@section('title', 'Laporan Neraca Saldo')

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
                    <h4 class="fw-semibold mb-1">Laporan Neraca Saldo</h4>
                    <p class="text-muted fs-3 mb-0" id="periodeTeks">Periode 01 Jan {{ date('Y') }} - 31 Des {{ date('Y') }}</p>
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
            <form id="fmCari" method="GET" action="{{ route('laporan.neraca-saldo') }}">
                <input type="hidden" name="tgl_dari" id="tgl_dari">
                <input type="hidden" name="tgl_samp" id="tgl_samp">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-calendar text-primary"></i> Rentang Tanggal
                        </label>
                        <button class="form-control text-start" type="button" id="daterange-btn">
                            <i class="ti ti-calendar me-2"></i>
                            <span id="reportrange">01 Jan {{ date('Y') }} - 31 Des {{ date('Y') }}</span>
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
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="text-align:center; width:5%"></th>
                            <th style="text-align:center; width:55%">Nama Akun</th>
                            <th style="text-align:center; width:20%">Debet</th>
                            <th style="text-align:center; width:20%">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentKategori = '';
                        @endphp

                        @foreach($neracaSaldo as $item)
                            @if($item->is_header)
                                @php
                                    $currentKategori = $item->kategori;
                                @endphp
                                <tr>
                                    <td class="h_tengah">&nbsp; <i class="ti ti-folder-open"></i></td>
                                    <td><strong>{{ $item->kategori }}</strong></td>
                                    <td class="h_kanan">{{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '' }}</td>
                                    <td class="h_kanan">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '' }}</td>
                                </tr>
                            @else
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>{{ $item->kode_akun ?? '' }}{{ $item->kode_akun ? '. ' : '' }}{{ $item->nama_akun }}</td>
                                    <td class="h_kanan">{{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '0' }}</td>
                                    <td class="h_kanan">{{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '0' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="header_kolom">
                            <td class="text-center" colspan="2"><strong>JUMLAH</strong></td>
                            <td class="h_kanan"><strong>{{ number_format($totalDebet, 0, ',', '.') }}</strong></td>
                            <td class="h_kanan"><strong>{{ number_format($totalKredit, 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Moment.js & Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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
                showDropdowns: true,
                startDate: moment().startOf('year'),
                endDate: moment().endOf('year')
            },
            function (start, end) {
                $('#reportrange').html(start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
                $('#periodeTeks').html('Periode ' + start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
            });

            // Set initial display
            const start = moment().startOf('year');
            const end = moment().endOf('year');
            $('#reportrange').html(start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
            $('#periodeTeks').html('Periode ' + start.format('DD MMM YYYY') + ' - ' + end.format('DD MMM YYYY'));
        });

        function clearSearch() {
            location.href = '{{ route("laporan.neraca-saldo") }}';
        }

        function doSearch() {
            const picker = $('#daterange-btn').data('daterangepicker');
            const tgl_dari = picker.startDate.format('YYYY-MM-DD');
            const tgl_samp = picker.endDate.format('YYYY-MM-DD');
            
            $('input[name=tgl_dari]').val(tgl_dari);
            $('input[name=tgl_samp]').val(tgl_samp);
            
            $('#fmCari').submit();
        }

        function cetak() {
            const picker = $('#daterange-btn').data('daterangepicker');
            const tgl_dari = picker.startDate.format('YYYY-MM-DD');
            const tgl_samp = picker.endDate.format('YYYY-MM-DD');

            const url = '{{ route("laporan.neraca-saldo.cetak") }}?tgl_dari=' + tgl_dari + '&tgl_samp=' + tgl_samp;
            const win = window.open(url, '_blank');
            
            if (win) {
                win.focus();
            } else {
                alert('Popup jangan di block. Silakan aktifkan popup di browser Anda.');
            }
        }
    </script>
@endpush