@extends('layouts.app')

@section('title', 'Detail Bayar Angsuran')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
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
                    <p class="text-muted fs-3 mb-0">Kode Pinjaman: <strong class="text-primary">{{ $pinjaman->kode_pinjaman }}</strong></p>
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
                <strong>Perhatian!</strong> Pilih baris angsuran yang belum dibayar, lalu klik <strong>"Bayar"</strong> untuk memproses pembayaran
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
        <div class="card-header bg-primary-subtle">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="ti ti-file-text me-2"></i>Detail Pinjaman</h6>
                <button class="btn btn-sm btn-light shadow-sm" data-bs-toggle="collapse" data-bs-target="#detailPinjaman">
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
                            height="150" alt="Foto Anggota" onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
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
                                    <td>{{ $pinjaman->anggota_departement }}</td>
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
                                            {{ $pinjaman->kode_pinjaman }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Pinjam</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->tanggal_pinjam->translatedFormat('d F Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lama Pinjaman</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $pinjaman->lama_pinjaman }} Bulan</span>
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
                                        <strong>{{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Angsuran Pokok</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Biaya & Bunga</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong class="text-info">{{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-muted fw-semibold pt-2">Jumlah Angsuran</td>
                                    <td class="pt-2">:</td>
                                    <td class="text-end pt-2">
                                        <strong class="text-success fs-6">{{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Pembayaran -->
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-md-2 col-6">
                        <p class="text-muted mb-1">Sisa Angsuran</p>
                        <span class="fw-bold fs-6">{{ $pinjaman->jumlah_sisa_angsuran }} Bulan</span>
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
                        <span class="fw-bold text-warning fs-6" id="totalBayar">Rp {{ number_format($pinjaman->total_sisa_tagihan, 0, ',', '.') }}</span>
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

    <!-- Data Jadwal & Pembayaran Angsuran -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-receipt me-2"></i>Jadwal & Pembayaran Angsuran</h6>
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
                        <input type="text" class="form-control form-control-sm" id="searchKode" 
                               placeholder="Cari kode..." style="max-width: 200px;">
                        <button class="btn btn-primary btn-sm" onclick="cariData()">
                            <i class="ti ti-search"></i> Cari
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="clearSearch()">
                            <i class="ti ti-x"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Jadwal Angsuran -->
            <div class="table-responsive">
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Kode Bayar</th>
                            <th class="text-center">Tanggal Bayar</th>
                            <th class="text-center">Tanggal Tempo</th>
                            <th class="text-center">Angsuran Ke</th>
                            <th class="text-end">Jumlah Angsuran</th>
                            <th class="text-end">Jumlah Bayar</th>
                            <th class="text-end">Denda</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">User</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalAngsuran as $item)
                            <tr data-id="{{ $item->id }}" 
                                data-status="{{ $item->status_bayar }}"
                                class="{{ $item->status_bayar == 'Lunas' ? 'table-success-subtle' : ($item->is_terlambat ? 'table-danger-subtle' : '') }}">
                                <td class="text-center">
                                    @if($item->pembayaranTerakhir)
                                        <span class="badge bg-info-subtle text-info fw-semibold px-2 py-1">
                                            {{ $item->pembayaranTerakhir->kode_bayar }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->tanggal_bayar)
                                        <div>{{ $item->tanggal_bayar->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $item->tanggal_bayar->format('H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div>{{ $item->tanggal_jatuh_tempo->format('d M Y') }}</div>
                                    @if($item->status_bayar == 'Belum' && $item->is_terlambat)
                                        <small class="text-danger">
                                            <i class="ti ti-alert-circle"></i> {{ $item->hari_terlambat }} hari
                                        </small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->angsuran_ke }}</span>
                                </td>
                                <td class="text-end">
                                    <strong>Rp {{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    @if($item->status_bayar == 'Lunas')
                                        <strong class="text-success">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($item->denda > 0)
                                        <span class="text-danger fw-bold">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->status_bayar == 'Lunas')
                                        <span class="badge bg-success">
                                            <i class="ti ti-check"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="ti ti-clock"></i> Belum Bayar
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->user)
                                        <span class="badge border border-secondary text-secondary">
                                            {{ $item->user->name ?? '-' }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->status_bayar == 'Lunas' && $item->pembayaranTerakhir)
                                        <button class="btn btn-sm btn-primary" 
                                                onclick="cetakNota({{ $item->pembayaranTerakhir->id }})" 
                                                data-bs-toggle="tooltip" title="Cetak Nota">
                                            <i class="ti ti-printer"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="ti ti-inbox fs-1 text-muted"></i>
                                    <p class="text-muted mb-0">Belum ada data angsuran</p>
                                </td>
                            </tr>
                        @endforelse
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
                                <li class="mb-2">Pilih baris angsuran yang <strong class="text-warning">Belum Bayar</strong></li>
                                <li class="mb-2">Klik tombol <strong class="text-success">Bayar</strong></li>
                                <li class="mb-2">Isi form pembayaran dan pilih kas tujuan</li>
                                <li class="mb-2">Denda otomatis terhitung jika terlambat</li>
                                <li class="mb-2">Kode pembayaran (TBY) otomatis ter-generate</li>
                            </ul>

                            <h6 class="fw-bold mb-3">B. Edit & Hapus Pembayaran</h6>
                            <ul class="mb-0">
                                <li class="mb-2">Pilih baris yang sudah <strong class="text-success">Lunas</strong></li>
                                <li class="mb-2">Klik <strong>Edit</strong> untuk mengubah data pembayaran</li>
                                <li class="mb-2">Klik <strong>Hapus</strong> untuk membatalkan pembayaran</li>
                                <li class="mb-2">Status akan kembali menjadi "Belum Bayar"</li>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                order: [[3, 'asc']], // Order by angsuran_ke
                columnDefs: [
                    { orderable: false, targets: [9] }
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

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Function: Refresh Data
        function refreshData() {
            Swal.fire({
                title: 'Memuat Ulang Data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });
            setTimeout(() => { location.reload(); }, 1000);
        }

        // Function: Show Help
        function showHelp() {
            $('#modalBantuan').modal('show');
        }

        // Function: Bayar Angsuran
        function bayarAngsuran() {
            const selectedRow = table.row('.selected');
            
            if (!selectedRow.data()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih angsuran yang akan dibayar'
                });
                return;
            }

            const rowNode = selectedRow.node();
            const angsuranId = $(rowNode).data('id');
            const status = $(rowNode).data('status');

            if (status === 'Lunas') {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: 'Angsuran ini sudah lunas'
                });
                return;
            }

            // Ambil detail via AJAX
            $.ajax({
                url: '{{ route("pinjaman.bayar.getDetail", ":id") }}'.replace(':id', angsuranId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        showModalBayar(response.data);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Gagal mengambil data'
                    });
                }
            });
        }

        // Function: Show Modal Bayar
        function showModalBayar(angsuran) {
            const jumlahBayar = parseFloat(angsuran.jumlah_angsuran);
            const denda = parseFloat(angsuran.denda_otomatis) || 0;

            Swal.fire({
                title: 'Bayar Angsuran',
                html: `
                    <div class="text-start">
                        <div class="alert alert-info rounded-3">
                             <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Angsuran Ke-${angsuran.angsuran_ke}</strong><br>
                                    <small>Tempo: ${angsuran.tanggal_jatuh_tempo_formatted}</small>
                                </div>
                                ${angsuran.is_terlambat ? `<span class="badge bg-danger">${angsuran.hari_terlambat} hari terlambat</span>` : ''}
                            </div>
                        </div>

                        <input type="hidden" id="angsuranId" value="${angsuran.id}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="tglBayar"
                                value="${new Date().toISOString().slice(0, 16)}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Angsuran</label>
                            <input type="text" class="form-control bg-light" 
                                value="Rp ${jumlahBayar.toLocaleString('id-ID')}" readonly>
                            <input type="hidden" id="jumlahBayar" value="${jumlahBayar}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Denda</label>
                            <input type="number" class="form-control" id="dendaBayar"
                                value="${denda}" min="0" step="1000">
                            <small class="text-muted">Dapat diubah manual</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kas Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select" id="keKasId">
                                <option value="">-- Pilih Kas --</option>
                                @foreach($kasList as $kas)
                                    <option value="{{ $kas->id }}">{{ $kas->nama_kas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" id="keterangan" rows="2"></textarea>
                        </div>

                        <div class="alert alert-success mb-0">
                            <div class="d-flex justify-content-between">
                                <span>Total Bayar:</span>
                                <strong class="fs-5" id="totalDisplay">Rp ${(jumlahBayar + denda).toLocaleString('id-ID')}</strong>
                            </div>
                        </div>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-cash"></i> Bayar Sekarang',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#13a460ff',
                didOpen: () => {
                    // Update total saat denda berubah
                    $('#dendaBayar').on('input', function() {
                        const bayar = parseFloat($('#jumlahBayar').val());
                        const dendaVal = parseFloat($(this).val()) || 0;
                        $('#totalDisplay').text('Rp ' + (bayar + dendaVal).toLocaleString('id-ID'));
                    });
                },
                preConfirm: () => {
                    const tanggalBayar = $('#tglBayar').val();
                    const keKasId = $('#keKasId').val();
                    const jumlahBayar = $('#jumlahBayar').val();
                    const denda = $('#dendaBayar').val();
                    const keterangan = $('#keterangan').val();

                    if (!tanggalBayar) {
                        Swal.showValidationMessage('Tanggal bayar wajib diisi');
                        return false;
                    }

                    if (!keKasId) {
                        Swal.showValidationMessage('Kas tujuan wajib dipilih');
                        return false;
                    }

                    return {
                        angsuran_id: $('#angsuranId').val(),
                        tanggal_bayar: tanggalBayar,
                        jumlah_bayar: jumlahBayar,
                        denda: denda,
                        ke_kas_id: keKasId,
                        keterangan: keterangan
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesPayment(result.value);
                }
            });
        }

        // Function: Proses Payment
        function prosesPayment(data) {
            Swal.fire({
                title: 'Memproses Pembayaran...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("pinjaman.bayar.store") }}',
                type: 'POST',
                data: {
                    ...data,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `
                                Pembayaran berhasil disimpan<br>
                                <strong>Kode Bayar: ${response.kode_bayar}</strong>
                            `,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memproses pembayaran'
                    });
                }
            });
        }

        // Function: Edit Bayar
        function editBayar() {
            const selectedRow = table.row('.selected');
            
            if (!selectedRow.data()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih angsuran yang akan diedit'
                });
                return;
            }

            const rowNode = selectedRow.node();
            const angsuranId = $(rowNode).data('id');
            const status = $(rowNode).data('status');

            if (status !== 'Lunas') {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: 'Hanya angsuran yang sudah lunas yang bisa diedit'
                });
                return;
            }

            // Get detail pembayaran
            $.ajax({
                url: '{{ route("pinjaman.bayar.getPembayaran", ":id") }}'.replace(':id', angsuranId),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        showModalEdit(response.data);
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Gagal mengambil data pembayaran'
                    });
                }
            });
        }

        // Function: Show Modal Edit
        function showModalEdit(pembayaran) {
            const jumlahBayar = parseFloat(pembayaran.jumlah_bayar);
            const denda = parseFloat(pembayaran.denda) || 0;

            Swal.fire({
                title: 'Edit Pembayaran',
                html: `
                    <div class="text-start">
                        <div class="alert alert-info">
                            <strong>Kode Bayar: ${pembayaran.kode_bayar}</strong><br>
                            <small>Angsuran Ke-${pembayaran.angsuran_ke}</small>
                        </div>

                        <input type="hidden" id="detailId" value="${pembayaran.id}">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="tglBayarEdit"
                                value="${pembayaran.tanggal_bayar}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Bayar</label>
                            <input type="number" class="form-control" id="jumlahBayarEdit"
                                value="${jumlahBayar}" min="0" step="1000">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Denda</label>
                            <input type="number" class="form-control" id="dendaBayarEdit"
                                value="${denda}" min="0" step="1000">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kas Tujuan <span class="text-danger">*</span></label>
                            <select class="form-select" id="keKasIdEdit">
                                <option value="">-- Pilih Kas --</option>
                                @foreach($kasList as $kas)
                                    <option value="{{ $kas->id }}" ${pembayaran.ke_kas_id == {{ $kas->id }} ? 'selected' : ''}>
                                        {{ $kas->nama_kas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" id="keteranganEdit" rows="2">${pembayaran.keterangan || ''}</textarea>
                        </div>

                        <div class="alert alert-success mb-0">
                            <div class="d-flex justify-content-between">
                                <span>Total:</span>
                                <strong class="fs-5" id="totalDisplayEdit">Rp ${(jumlahBayar + denda).toLocaleString('id-ID')}</strong>
                            </div>
                        </div>
                    </div>
                `,
                width: '600px',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-check"></i> Update',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#ffc107',
                didOpen: () => {
                    // Update total saat nilai berubah
                    $('#jumlahBayarEdit, #dendaBayarEdit').on('input', function() {
                        const bayar = parseFloat($('#jumlahBayarEdit').val()) || 0;
                        const dendaVal = parseFloat($('#dendaBayarEdit').val()) || 0;
                        $('#totalDisplayEdit').text('Rp ' + (bayar + dendaVal).toLocaleString('id-ID'));
                    });
                },
                preConfirm: () => {
                    const tanggalBayar = $('#tglBayarEdit').val();
                    const keKasId = $('#keKasIdEdit').val();
                    const jumlahBayar = $('#jumlahBayarEdit').val();
                    const denda = $('#dendaBayarEdit').val();
                    const keterangan = $('#keteranganEdit').val();

                    if (!tanggalBayar) {
                        Swal.showValidationMessage('Tanggal bayar wajib diisi');
                        return false;
                    }

                    if (!keKasId) {
                        Swal.showValidationMessage('Kas tujuan wajib dipilih');
                        return false;
                    }

                    return {
                        id: $('#detailId').val(),
                        tanggal_bayar: tanggalBayar,
                        jumlah_bayar: jumlahBayar,
                        denda: denda,
                        ke_kas_id: keKasId,
                        keterangan: keterangan
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesUpdate(result.value);
                }
            });
        }

        // Function: Proses Update
        function prosesUpdate(data) {
            Swal.fire({
                title: 'Mengupdate Data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("pinjaman.bayar.update", ":id") }}'.replace(':id', data.id),
                type: 'PUT',
                data: {
                    ...data,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data pembayaran berhasil diupdate',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupdate data'
                    });
                }
            });
        }

        // Function: Hapus Bayar
        function hapusBayar() {
            const selectedRow = table.row('.selected');
            
            if (!selectedRow.data()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Silakan pilih angsuran yang akan dihapus'
                });
                return;
            }

            const rowNode = selectedRow.node();
            const angsuranId = $(rowNode).data('id');
            const status = $(rowNode).data('status');

            if (status !== 'Lunas') {
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: 'Hanya angsuran yang sudah lunas yang bisa dihapus'
                });
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin membatalkan pembayaran ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesHapus(angsuranId);
                }
            });
        }

        // Function: Proses Hapus
        function prosesHapus(angsuranId) {
            Swal.fire({
                title: 'Menghapus Data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("pinjaman.bayar.destroy", ":id") }}'.replace(':id', angsuranId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran berhasil dibatalkan',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data'
                    });
                }
            });
        }

        // Function: Cetak Nota
        function cetakNota(detailId) {
            window.open('{{ route("pinjaman.bayar.cetak", ":id") }}'.replace(':id', detailId), '_blank');
        }

        // Function: Cari Data
        function cariData() {
            const keyword = $('#searchKode').val();
            table.search(keyword).draw();
        }

        // Function: Clear Search
        function clearSearch() {
            $('#searchKode').val('');
            table.search('').draw();
        }
    </script>
@endpush