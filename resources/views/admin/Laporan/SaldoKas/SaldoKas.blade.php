@extends('layouts.app')

@section('title', 'Laporan Saldo Kas')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Bootstrap DateTimePicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Saldo Kas</h4>
                    <p class="text-muted fs-3 mb-0">Periode {{ $periodeDisplay }}</p>
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
            <form id="fmCari" method="GET" action="{{ route('laporan.saldo-kas') }}">
                <input type="hidden" name="periode" id="periode" value="{{ $periode }}" />

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-calendar text-primary"></i> Pilih Bulan & Tahun
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="ti ti-calendar"></i>
                            </span>
                            <input id="txt_periode" class="form-control" type="text" value="{{ $periodeDisplay }}" readonly />
                        </div>
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
                <table id="tabelSaldoKas" class="table table-hover align-middle mb-0" style="width:100%">
                    <thead class="table-primary">
                        <tr class="header_kolom">
                            <th style="width:5%; vertical-align: middle; text-align:center">No</th>
                            <th style="width:35%; vertical-align: middle; text-align:center">Nama Kas</th>
                            <th style="width:20%; vertical-align: middle; text-align:center">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-light">
                            <td class="h_kanan header_kolom" colspan="2"><strong>SALDO PERIODE SEBELUMNYA</strong></td>
                            <td class="h_kanan header_kolom"><strong>{{ number_format($saldoPeriodeSebelumnya, 0, ',', ',') }}</strong></td>
                        </tr>
                        @foreach($saldoKas as $item)
                            <tr>
                                <td class="h_tengah">{{ $item->no }}</td>
                                <td>{{ $item->nama_kas }}</td>
                                <td class="h_kanan">{{ number_format($item->saldo, 0, ',', ',') }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-light">
                            <td colspan="2" class="h_kanan header_kolom"><strong>Jumlah</strong></td>
                            <td class="h_kanan header_kolom"><strong>{{ number_format($jumlahSaldo, 0, ',', ',') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="h_kanan"><strong>Saldo</strong></td>
                            <td class="h_kanan"><strong>{{ number_format($totalSaldo, 0, ',', ',') }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Moment.js -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Moment Locale ID -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/locale/id.min.js"></script>
    <!-- Bootstrap DateTimePicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize DateTimePicker
        $(document).ready(function () {
            moment.locale('id');
            
            $('#txt_periode').datetimepicker({
                format: 'MMMM YYYY',
                viewMode: 'months',
                locale: 'id',
                defaultDate: moment('{{ $periode }}', 'YYYY-MM')
            }).on('dp.change', function (e) {
                var selectedDate = e.date;
                var formattedPeriode = selectedDate.format('YYYY-MM');
                $('#periode').val(formattedPeriode);
                doSearch();
            });
        });

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

        // Function: Clear Search
        function clearSearch() {
            window.location.href = '{{ route("laporan.saldo-kas") }}';
        }

        // Function: Cetak Laporan
        function cetak() {
            const periode = $('#periode').val();

            const url = `{{ route('laporan.saldo-kas.cetak') }}?periode=${periode}`;
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
    </style>
@endpush