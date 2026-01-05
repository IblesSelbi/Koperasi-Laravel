@extends('layouts.app')

@section('title', 'Detail Bayar Angsuran')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .table-borderless td {
            padding: 0.35rem 0.5rem;
        }

        #tabelAngsuran tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px #dfe2e5 !important;
            color: #777e89 !important;
        }

        #tabelAngsuran tbody tr.selected>* strong,
        #tabelAngsuran tbody tr.selected>* .text-success,
        #tabelAngsuran tbody tr.selected>* .text-danger,
        #tabelAngsuran tbody tr.selected>* .text-muted {
            color: inherit !important;
        }

        #tabelAngsuran tbody tr:hover {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-semibold mb-1">Bayar Angsuran</h4>
                    <p class="text-muted fs-3 mb-0">Kode Pinjaman: <strong
                            class="text-primary">{{ $pinjaman->kode }}</strong></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('pinjaman.bayar') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-secondary" onclick="refreshData()">
                        <i class="ti ti-refresh"></i> Refresh
                    </button>
                    <button class="btn btn-info" onclick="showHelp()">
                        <i class="ti ti-help"></i> Bantuan
                    </button>
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

    <!-- Alert Info -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-center">
            <i class="ti ti-alert-triangle fs-5 me-2"></i>
            <div>
                <strong>Perhatian!</strong> Klik <strong>"Validasi Lunas"</strong> untuk melakukan Pelunasan dan Pembayaran
                Tagihan Denda
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Action Buttons -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-success" onclick="validasiLunas({{ $pinjaman->id }})">
                    <i class="ti ti-check-circle"></i> Validasi Lunas
                </button>
                <a href="#" class="btn btn-primary">
                    <i class="ti ti-file-text"></i> Detail
                </a>
            </div>
        </div>
    </div>

    <!-- Detail Pinjaman Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-primary-subtle text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="ti ti-file-text me-2"></i>Detail Pinjaman</h6>
                <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#detailPinjaman">
                    <i class="ti ti-minus"></i>
                </button>
            </div>
        </div>
        <div class="collapse show" id="detailPinjaman">
            <div class="card-body p-4">
                <div class="row">
                    <!-- Foto Anggota -->
                    <div class="col-md-2 text-center mb-3 mt-4 mb-md-0">
                        <img src="{{ asset($pinjaman->anggota_foto) }}" class="rounded border shadow-sm" width="130"
                            height="150" alt="Foto Anggota">
                    </div>

                    <!-- Data Anggota -->
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="p-3">
                            <h6 class="text-success fw-semibold mb-3 pb-2 border-bottom border-success">
                                <i class="ti ti-user-circle me-1"></i>Data Anggota
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">ID Anggota</td>
                                    <td style="width: 5%;">:</td>
                                    <td><strong>{{ $pinjaman->anggota_id }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->anggota_nama }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dept</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota_departemen }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">TTL</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota_ttl }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kota Tinggal</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota_kota }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Data Pinjaman -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-3">
                            <h6 class="text-success fw-semibold mb-3 pb-2 border-bottom border-success">
                                <i class="ti ti-wallet me-1"></i>Data Pinjaman
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 45%;">Kode Pinjam</td>
                                    <td style="width: 5%;">:</td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                            {{ $pinjaman->kode }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Pinjam</td>
                                    <td>:</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->format('d F Y') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Tempo</td>
                                    <td>:</td>
                                    <td><strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_tempo)->format('d F Y') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lama Pinjaman</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $pinjaman->lama_pinjaman }}
                                            Bulan</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Rincian Angsuran -->
                    <div class="col-md-3">
                        <div class="p-3">
                            <h6 class="text-success fw-semibold mb-3 pb-2 border-bottom border-success">
                                <i class="ti ti-calculator me-1"></i>Rincian Angsuran
                            </h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Pokok Pinjaman</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Angsuran Pokok</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Biaya & Bunga</td>
                                    <td>:</td>
                                    <td class="text-end"><strong
                                            class="text-info">{{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-muted fw-semibold pt-2">Jumlah Angsuran</td>
                                    <td class="pt-2">:</td>
                                    <td class="text-end pt-2">
                                        <strong
                                            class="text-success fs-6">{{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Pembayaran -->
            <div class="card-footer bg-light text-dark">
                <div class="row text-center">
                    <div class="col-md-2 col-6">
                        <p class="text-muted mb-1">Sisa Angsuran</p>
                        <span class="fw-bold fs-6">{{ $pinjaman->sisa_angsuran }} Bulan</span>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted mb-1">Dibayar</p>
                        <span class="fw-bold fs-6">Rp {{ number_format($pinjaman->sudah_dibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-md-2 col-6">
                        <p class="text-muted mb-1">Denda</p>
                        <span class="fw-bold fs-6">Rp {{ number_format($pinjaman->jumlah_denda, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-md-3 col-6">
                        <p class="text-muted mb-1">Sisa Tagihan</p>
                        <span class="fw-bold text-warning fs-6"
                            id="totalBayar">{{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-md-3 col-12 mt-2">
                        <p class="text-muted mb-1">Status Pelunasan</p>
                        @if($pinjaman->status_lunas == 'Lunas')
                            <span class="badge bg-success" id="statusLunas">Lunas</span>
                        @else
                            <span class="badge bg-danger" id="statusLunas">Belum Lunas</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Pembayaran Angsuran -->
    <div class="card mb-3 mt-4 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-2"></i>Data Pembayaran Angsuran</h6>
        </div>
        <div class="card-body p-3">
            <!-- Toolbar -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <button class="btn btn-success btn-sm" onclick="bayarAngsuran()">
                        <i class="ti ti-cash"></i> Bayar
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="editBayar()">
                        <i class="ti ti-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="hapusBayar()">
                        <i class="ti ti-trash"></i> Hapus
                    </button>
                </div>
                <div class="col-md-8">
                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                        <div class="input-group" style="max-width: 150px;">
                            <button class="btn btn-outline-secondary" id="daterangeBtn">
                                <i class="ti ti-calendar"></i> <span id="daterangeText">Pilih Tanggal</span>
                            </button>
                        </div>
                        <input type="text" class="form-control form-control-sm" id="searchKode" placeholder="Kode Transaksi"
                            style="max-width: 200px;">
                        <button class="btn btn-primary btn-sm" onclick="cariData()">
                            <i class="ti ti-search"></i> Cari
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="clearSearch()">
                            <i class="ti ti-x"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border-1 overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Kode</th>
                            <th class="text-center">Tanggal Bayar</th>
                            <th class="text-center">Tanggal Tempo</th>
                            <th class="text-center">Angsuran Ke</th>
                            <th class="text-end">Jumlah Bayar</th>
                            <th class="text-end">Denda</th>
                            <th class="text-center">Terlambat</th>
                            <th class="text-center">User</th>
                            <th class="text-center">Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pembayaran as $item)
                            <tr>
                                <td class="text-center">
                                    <span
                                        class="badge bg-info-subtle text-info fw-semibold px-2 py-1">{{ $item->kode_bayar }}</span>
                                </td>
                                <td class="text-center">
                                    <div>{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $item->waktu_bayar }}</small>
                                </td>
                                <td class="text-center">
                                    <div>{{ \Carbon\Carbon::parse($item->tanggal_tempo)->format('d M Y') }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->angsuran_ke }}</span>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp
                                        {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    <span class="text-danger">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->status_keterlambatan == 'Tepat Waktu')
                                        <span class="badge bg-success-subtle text-success">Tepat Waktu</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">{{ $item->status_keterlambatan }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary">{{ $item->user }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary" onclick="cetakNota({{ $item->id }})">
                                        <i class="ti ti-printer"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Bantuan -->
    <div class="modal fade" id="modalBantuan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="ti ti-help"></i> Cara Pembayaran Angsuran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">A. Pembayaran Angsuran</h6>
                            <ul class="mb-4">
                                <li class="mb-2">Admin mencatat <strong class="text-primary">Pembayaran Angsuran</strong>
                                    sesuai Jumlah Angsuran setiap anggota</li>
                                <li class="mb-2">Anggota akan dikenakan <strong class="text-danger">Denda</strong> apabila
                                    terlambat melakukan pembayaran sesuai jatuh tempo</li>
                                <li class="mb-2">Batas maksimal pembayaran adalah pada tanggal 15 (Lima Belas) setiap bulan.
                                    <em class="text-muted">(Tanggal dapat diubah pada menu Setting » Suku Bunga)</em></li>
                            </ul>

                            <h6 class="fw-bold mb-3">B. Pelunasan Cepat</h6>
                            <ul class="mb-0">
                                <li class="mb-2">Anggota dinyatakan <strong class="text-success">LUNAS</strong> apabila
                                    telah membayar sejumlah tagihan yang dibebankan dan tidak memiliki tagihan <strong
                                        class="text-danger">Denda</strong> atau tagihan lainnya</li>
                                <li class="mb-2">Pelunasan dapat dilakukan walau Anggota masih memiliki kewajiban angsuran
                                    atau kurang dari tanggal jatuh tempo</li>
                                <li class="mb-2">Jika Anggota telah menyelesaikan angsuran, Admin diharuskan melakukan
                                    <strong class="text-primary">Validasi Pelunasan</strong> untuk menghitung sisa
                                    pembayaran dan denda yang dibebankan kepada anggota</li>
                                <li class="mb-2">Anggota dapat melakukan peminjaman selanjutnya jika tidak mempunyai tagihan
                                    di pinjaman sebelumnya</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
                </div>
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

    <!-- Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let table;

        $(document).ready(function () {
            // Initialize DataTable
            table = $('#tabelAngsuran').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [8] }
                ]
            });

            // Table row selection
            $('#tabelAngsuran tbody').on('click', 'tr', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });

            // Initialize Daterangepicker
            $('#daterangeBtn').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    format: 'DD/MM/YYYY'
                }
            });

            $('#daterangeBtn').on('apply.daterangepicker', function (ev, picker) {
                $('#daterangeText').text(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('#daterangeBtn').on('cancel.daterangepicker', function (ev, picker) {
                $('#daterangeText').text('Pilih Tanggal');
            });
        });

        // Function: Refresh Data
        function refreshData() {
            Swal.fire({
                title: 'Memuat Ulang Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil dimuat ulang',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }, 1000);
        }

        // Function: Show Help
        function showHelp() {
            $('#modalBantuan').modal('show');
        }

        // Function: Validasi Lunas
        function validasiLunas(id) {
            const sisaTagihan = $('#totalBayar').text();

            Swal.fire({
                title: 'Validasi Pelunasan Pinjaman',
                html: `
                        <div class="text-start">
                            <p class="mb-3">Apakah Anda yakin akan melakukan validasi pelunasan untuk pinjaman ini?</p>
                            <div class="alert alert-info">
                                <strong>Sisa Tagihan:</strong> Rp ${sisaTagihan}<br>
                                <small class="text-muted">Pastikan semua angsuran telah dibayarkan</small>
                            </div>
                        </div>
                    `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-check"></i> Ya, Validasi Lunas',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#198754',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang memvalidasi pelunasan',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // AJAX Request
                    $.ajax({
                        url: `{{ url('pinjaman/pinjaman') }}/${id}/validasi-lunas`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Pinjaman telah divalidasi sebagai lunas',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan, silakan coba lagi'
                            });
                        }
                    });
                }
            });
        }

        // Function: Bayar Angsuran
        function bayarAngsuran() {
            Swal.fire({
                title: 'Bayar Angsuran',
                html: `
                <div class="text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Angsuran Ke <span class="text-danger">*</span></label>
                        <select class="form-select" id="angsuranKe">
                            <option value="">-- Pilih Angsuran --</option>
                            <option value="4">Angsuran ke-4 (Rp 346.700)</option>
                            <option value="5">Angsuran ke-5 (Rp 346.700)</option>
                            <option value="6">Angsuran ke-6 (Rp 346.700)</option>
                            <option value="7">Angsuran ke-7 (Rp 346.700)</option>
                            <option value="8">Angsuran ke-8 (Rp 346.700)</option>
                            <option value="9">Angsuran ke-9 (Rp 346.700)</option>
                            <option value="10">Angsuran ke-10 (Rp 346.700)</option>
                            <option value="11">Angsuran ke-11 (Rp 346.700)</option>
                            <option value="12">Angsuran ke-12 (Rp 346.700)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tanggal Bayar <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="tglBayar"
                            value="${new Date().toISOString().slice(0, 16)}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jumlah Bayar</label>
                        <input type="text" class="form-control bg-light" id="jumlahBayar"
                            value="Rp 346.700" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Denda</label>
                        <input type="text" class="form-control bg-light" id="dendaBayar"
                            value="Rp 0" readonly>
                    </div>

                    <div class="alert alert-warning">
                        <small>
                            <i class="ti ti-info-circle"></i>
                            Pastikan data yang diinput sudah benar
                        </small>
                    </div>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Bayar',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                focusConfirm: false,

                preConfirm: () => {
                    const angsuranKe = document.getElementById('angsuranKe').value;
                    const tglBayar = document.getElementById('tglBayar').value;

                    if (!angsuranKe) {
                        Swal.showValidationMessage('Angsuran wajib dipilih');
                        return false;
                    }

                    if (!tglBayar) {
                        Swal.showValidationMessage('Tanggal bayar wajib diisi');
                        return false;
                    }

                    return {
                        angsuran_ke: angsuranKe,
                        tanggal_bayar: tglBayar,
                        jumlah_bayar: 346700,
                        denda: 0
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = result.value;

                    // 🔥 CONTOH AKSI (pilih salah satu)

                    // 1️⃣ Debug dulu
                    console.log('Data bayar angsuran:', data);

                    // 2️⃣ Contoh AJAX (Laravel)
                    /*
                    $.ajax({
                        url: '/angsuran/bayar',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ...data
                        },
                        success: function (res) {
                            Swal.fire('Berhasil', 'Angsuran berhasil dibayar', 'success');
                        },
                        error: function () {
                            Swal.fire('Gagal', 'Terjadi kesalahan', 'error');
                        }
                    });
                    */

                    // 3️⃣ Notifikasi sukses (dummy)
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pembayaran angsuran berhasil disimpan'
                    });
                }
            });
        }
    </script>
@endpush