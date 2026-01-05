@extends('layouts.app')

@section('title', 'Laporan Jatuh Tempo')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <style>
        #tabelJatuhTempo tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px #ffe7e7 !important;
            color: #721c24 !important;
        }

        #tabelJatuhTempo tbody tr:hover {
            cursor: pointer;
            background-color: #fff3cd;
        }

        .badge {
            font-weight: 500;
            padding: 0.35em 0.65em;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Laporan Jatuh Tempo Pembayaran Kredit</h4>
                    <p class="text-muted fs-3 mb-0">Data pinjaman yang akan jatuh tempo per periode</p>
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
                        <span class="badge bg-warning-subtle text-warning shadow-sm px-3 py-2">
                            <i class="ti ti-alert-circle"></i> Total Tagihan:
                            <strong id="totalTagihan">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
                        </span>
                        <span class="badge bg-danger-subtle text-danger shadow-sm px-3 py-2">
                            <i class="ti ti-exclamation-circle"></i> Sisa Tagihan:
                            <strong id="sisaTagihan">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
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
                        <button class="btn btn-warning btn-sm" onclick="kirimNotifikasi()">
                            <i class="ti ti-bell"></i> Kirim Notifikasi
                        </button>

                        <span class="badge bg-info-subtle text-info shadow-sm border-2 px-3 py-2 ms-auto">
                            <i class="ti ti-file-text"></i> Total Data:
                            <strong id="totalData">{{ $jatuhTempo->count() }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Info -->
    <div class="alert alert-primary d-flex align-items-center mb-3" role="alert">
        <i class="ti ti-info-circle fs-5 me-2"></i>
        <div>
            <strong>Periode:</strong> 
            <span id="periodeText">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->isoFormat('MMMM YYYY') }}
            </span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelJatuhTempo" class="table table-hover align-middle rounded-2 border-1 overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="text-center align-middle" style="width: 10%;">Kode Pinjam</th>
                            <th class="align-middle" style="width: 15%;">Nama Anggota</th>
                            <th class="text-center align-middle" style="width: 12%;">Tanggal Pinjam</th>
                            <th class="text-center align-middle" style="width: 12%;">Tanggal Tempo</th>
                            <th class="text-center align-middle" style="width: 10%;">Lama Pinjam</th>
                            <th class="text-end align-middle" style="width: 12%;">Jumlah Tagihan</th>
                            <th class="text-end align-middle" style="width: 12%;">Dibayar</th>
                            <th class="text-end align-middle" style="width: 15%;">Sisa Tagihan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jatuhTempo as $index => $item)
                            <tr>
                                <td class="text-center align-middle">
                                    <span class="fw-semibold">{{ $index + 1 }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode_pinjam }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <strong class="text-dark">{{ $item->nama_anggota }}</strong><br>
                                        <small class="text-muted">ID: {{ $item->id_anggota }}</small>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="text-muted">
                                        {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $tempo = \Carbon\Carbon::parse($item->tanggal_tempo);
                                        $now = \Carbon\Carbon::now();
                                        $diff = $now->diffInDays($tempo, false);
                                    @endphp
                                    @if($diff < 0)
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                            <i class="ti ti-alert-triangle"></i> {{ $tempo->format('d M Y') }}
                                        </span>
                                    @elseif($diff <= 7)
                                        <span class="badge bg-warning-subtle text-warning px-2 py-1">
                                            <i class="ti ti-clock"></i> {{ $tempo->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info px-2 py-1">
                                            <i class="ti ti-calendar"></i> {{ $tempo->format('d M Y') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $item->lama_pinjam }} Bulan
                                    </span>
                                </td>
                                <td class="text-end align-middle">
                                    <strong class="text-primary">Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end align-middle">
                                    <span class="{{ $item->dibayar > 0 ? 'text-success' : 'text-muted' }}">
                                        Rp {{ number_format($item->dibayar, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-end align-middle">
                                    <strong class="text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="6" class="text-center align-middle">
                                <strong>Jumlah Total</strong>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-primary">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong>
                            </td>
                            <td class="text-end align-middle">
                                <strong class="text-danger">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
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

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            // Hide table initially
            const tableWrapper = $('#tabelJatuhTempo').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            table = $('#tabelJatuhTempo').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[4, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [0] }
                ],
                initComplete: function () {
                    tableWrapper.css('opacity', 1);
                }
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Table row selection
            $('#tabelJatuhTempo tbody').on('click', 'tr', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });
        });

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
                text: 'Sedang memuat data jatuh tempo',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                location.href = `{{ route('laporan.jatuh-tempo') }}?periode=${periode}`;
            }, 500);
        }

        // Function: Reset Filter
        function resetFilter() {
            const now = new Date();
            const currentMonth = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
            $('#filterPeriode').val(currentMonth);

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Periode dikembalikan ke bulan ini',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                location.href = `{{ route('laporan.jatuh-tempo') }}?periode=${currentMonth}`;
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
                        <p><strong>Laporan Jatuh Tempo Pembayaran Kredit</strong></p>
                        <hr>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">Periode</td>
                                <td><strong>${periodeText}</strong></td>
                            </tr>
                            <tr>
                                <td>Total Data</td>
                                <td><strong>${$('#totalData').text()} pinjaman</strong></td>
                            </tr>
                            <tr>
                                <td>Total Tagihan</td>
                                <td><strong class="text-primary">${$('#totalTagihan').text()}</strong></td>
                            </tr>
                            <tr>
                                <td>Sisa Tagihan</td>
                                <td><strong class="text-danger">${$('#sisaTagihan').text()}</strong></td>
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
                    const url = `{{ route('laporan.jatuh-tempo.cetak') }}?periode=${periode}`;
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
                        <p>Data jatuh tempo periode <strong>${periodeText}</strong> akan diekspor ke format Excel (.xlsx)</p>
                        <hr>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="includeSummary" checked>
                            <label class="form-check-label" for="includeSummary">
                                Sertakan ringkasan total
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeChart" checked>
                            <label class="form-check-label" for="includeChart">
                                Sertakan grafik status pembayaran
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
                        const includeChart = document.getElementById('includeChart').checked;
                        window.location.href = `{{ route('laporan.jatuh-tempo.export.excel') }}?periode=${periode}&summary=${includeSummary}&chart=${includeChart}`;

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'File Excel berhasil diunduh',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }, 1500);
                }
            });
        }

        // Function: Kirim Notifikasi
        function kirimNotifikasi() {
            const periode = $('#filterPeriode').val();
            const periodeText = $('#periodeText').text();
            const totalData = $('#totalData').text();

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
                title: 'Kirim Notifikasi',
                html: `
                    <div class="text-start">
                        <p>Kirim reminder pembayaran kepada <strong>${totalData}</strong> anggota yang akan jatuh tempo pada periode <strong>${periodeText}</strong></p>
                        <hr>
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle"></i> 
                            Notifikasi akan dikirim melalui:
                            <ul class="mb-0 mt-2">
                                <li>SMS ke nomor telepon terdaftar</li>
                                <li>Email ke alamat email anggota</li>
                                <li>WhatsApp (jika tersedia)</li>
                            </ul>
                        </div>
                        <div class="form-group mt-3">
                            <label class="form-label">Pesan Template:</label>
                            <select class="form-select" id="pesanTemplate">
                                <option value="standard">Standard - Reminder Pembayaran</option>
                                <option value="urgent">Urgent - Segera Jatuh Tempo</option>
                                <option value="friendly">Friendly - Pengingat Ramah</option>
                            </select>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-send"></i> Kirim Notifikasi',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#ffc107',
                customClass: {
                    confirmButton: 'btn btn-warning',
                    cancelButton: 'btn btn-secondary'
                },
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim Notifikasi...',
                        html: `
                            <div class="text-center">
                                <div class="spinner-border text-primary mb-3" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Sedang mengirim notifikasi ke <strong>${totalData}</strong> anggota...</p>
                            </div>
                        `,
                        allowOutsideClick: false,
                        showConfirmButton: false
                    });

                    const template = document.getElementById('pesanTemplate').value;
                    
                    // AJAX Request
                    $.ajax({
                        url: '{{ route("laporan.jatuh-tempo.kirim-notifikasi") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            periode: periode,
                            template: template
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Notifikasi Terkirim!',
                                html: `
                                    <div class="text-start">
                                        <p>Notifikasi berhasil dikirim kepada <strong>${totalData}</strong> anggota</p>
                                        <hr>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><i class="ti ti-check text-success"></i> SMS</td>
                                                <td class="text-end">${response.data.sms} terkirim</td>
                                            </tr>
                                            <tr>
                                                <td><i class="ti ti-check text-success"></i> Email</td>
                                                <td class="text-end">${response.data.email} terkirim</td>
                                            </tr>
                                            <tr>
                                                <td><i class="ti ti-check text-success"></i> WhatsApp</td>
                                                <td class="text-end">${response.data.whatsapp} terkirim</td>
                                            </tr>
                                        </table>
                                    </div>
                                `,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#198754'
                            });
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat mengirim notifikasi'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush