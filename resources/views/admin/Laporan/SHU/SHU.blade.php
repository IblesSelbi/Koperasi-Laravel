@extends('layouts.app')

@section('title', 'Laporan Sisa Hasil Usaha (SHU)')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Daterangepicker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Sisa Hasil Usaha (SHU)</h4>
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
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter Laporan</h6>
        </div>
        <div class="card-body p-4">
            <form id="fmCari" method="POST" action="{{ route('laporan.shu') }}">
                @csrf
                <input type="hidden" name="tgl_dari" id="tgl_dari" value="{{ $tglDari }}">
                <input type="hidden" name="tgl_samp" id="tgl_samp" value="{{ $tglSamp }}">
                <input type="hidden" id="js_usaha" name="js_usaha" value="{{ $jasaUsaha }}">
                <input type="hidden" id="js_modal" name="js_modal" value="{{ $jasaModal }}">
                <input type="hidden" id="tot_pendpatan" name="tot_pendpatan" value="{{ $totalPendapatanAnggota }}">
                <input type="hidden" id="tot_simpanan" name="tot_simpanan" value="{{ $totalSimpananAnggota }}">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-calendar text-primary"></i> Rentang Tanggal
                        </label>
                        <button class="form-control text-start" type="button" id="daterange-btn">
                            <i class="ti ti-calendar me-2"></i>
                            <span id="reportrange">{{ \Carbon\Carbon::parse($tglDari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($tglSamp)->format('d M Y') }}</span>
                            <i class="ti ti-chevron-down float-end"></i>
                        </button>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-2">
                            <i class="ti ti-user text-info"></i> Pilih ID Anggota
                        </label>
                        <select id="anggota_id" name="anggota_id" class="form-select">
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($anggotaList as $anggota)
                                <option value="{{ $anggota->id }}" {{ $anggotaId == $anggota->id ? 'selected' : '' }}>
                                    {{ $anggota->id_anggota }} - {{ $anggota->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
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
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <div class="table-responsive shadow-sm rounded-2 border">
                <table id="tabelSHU" class="table table-hover align-middle mb-0" style="width:100%">
                    <tbody>
                        <tr class="table-light">
                            <td class="h_kiri header_kolom" colspan="2"><strong>SHU Sebelum Pajak</strong></td>
                            <td class="h_kanan header_kolom"><strong>{{ number_format($shuSebelumPajak, 0, ',', ',') }}</strong></td>
                        </tr>
                        <tr class="table-light">
                            <td class="h_kiri header_kolom" colspan="2"><strong>Pajak PPh (5%)</strong></td>
                            <td class="h_kanan header_kolom"><strong>{{ number_format($pajakPPh, 0, ',', ',') }}</strong></td>
                        </tr>
                        <tr class="table-light">
                            <td class="h_kiri header_kolom" colspan="2"><strong>SHU Setelah Pajak</strong></td>
                            <td class="h_kanan header_kolom"><strong>{{ number_format($shuSetelahPajak, 0, ',', ',') }}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="pt-4"><strong>PEMBAGIAN SHU UNTUK DANA-DANA</strong></td>
                        </tr>
                        <tr>
                            <td>Dana Cadangan</td>
                            <td class="h_kanan">40 %</td>
                            <td class="h_kanan">{{ number_format($danaCadangan, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Jasa Anggota</td>
                            <td class="h_kanan">40 %</td>
                            <td class="h_kanan">{{ number_format($jasaAnggota, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Dana Pengurus</td>
                            <td class="h_kanan">5 %</td>
                            <td class="h_kanan">{{ number_format($danaPengurus, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Dana Karyawan</td>
                            <td class="h_kanan">5 %</td>
                            <td class="h_kanan">{{ number_format($danaKaryawan, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Dana Pendidikan</td>
                            <td class="h_kanan">5 %</td>
                            <td class="h_kanan">{{ number_format($danaPendidikan, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Dana Sosial</td>
                            <td class="h_kanan">5 %</td>
                            <td class="h_kanan">{{ number_format($danaSosial, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="pt-3"><strong>PEMBAGIAN SHU ANGGOTA</strong></td>
                        </tr>
                        <tr>
                            <td>Jasa Usaha</td>
                            <td class="h_kanan">70 %</td>
                            <td class="h_kanan">{{ number_format($jasaUsaha, 0, ',', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Jasa Modal</td>
                            <td class="h_kanan">30 %</td>
                            <td class="h_kanan">{{ number_format($jasaModal, 0, ',', ',') }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="header_kolom"><strong>Total Pendapatan Anggota</strong></td>
                            <td colspan="2" class="h_kanan header_kolom"><strong>{{ number_format($totalPendapatanAnggota, 0, ',', ',') }}</strong></td>
                        </tr>
                        <tr class="table-light">
                            <td class="header_kolom"><strong>Total Simpanan Anggota</strong></td>
                            <td colspan="2" class="h_kanan header_kolom"><strong>{{ number_format($totalSimpananAnggota, 0, ',', ',') }}</strong></td>
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
    <!-- Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize Select2 and Daterangepicker
        $(document).ready(function () {
            // Initialize Select2
            $('#anggota_id').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Anggota --',
                allowClear: true
            });

            // Initialize Daterangepicker
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
            window.location.href = '{{ route("laporan.shu") }}';
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
            const anggotaId = $('#anggota_id').val();
            const tglDari = $('#tgl_dari').val();
            const tglSamp = $('#tgl_samp').val();
            const jsModal = $('#js_modal').val();
            const jsUsaha = $('#js_usaha').val();
            const totPendapatan = $('#tot_pendpatan').val();
            const totSimpanan = $('#tot_simpanan').val();

            const url = `{{ route('laporan.shu.cetak') }}?anggota_id=${anggotaId}&tgl_dari=${tglDari}&tgl_samp=${tglSamp}&js_modal=${jsModal}&js_usaha=${jsUsaha}&tot_pendpatan=${totPendapatan}&tot_simpanan=${totSimpanan}`;
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

        .h_kiri {
            text-align: left;
        }

        .header_kolom {
            font-weight: 600;
        }
    </style>
@endpush