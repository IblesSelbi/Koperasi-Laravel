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

        /* Highlight row dengan pembayaran pending */
        .table-warning-custom {
            background-color: #fff3cd !important;
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
                            class="text-primary">{{ $pinjaman->kode_pinjaman }}</strong></p>
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
                <strong>Perhatian!</strong> Pilih baris angsuran yang belum dibayar, lalu klik <strong>"Bayar"</strong>
                untuk memproses pembayaran
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Alert Pending Verification -->
    @php
        $hasPendingPayments = $jadwalAngsuran->filter(function ($item) {
            return $item->detailPembayaran->where('status_verifikasi', 'pending')->count() > 0;
        })->count();
    @endphp

    @if($hasPendingPayments > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="ti ti-clock-hour-4 fs-5 me-2"></i>
                <div>
                    <strong>Pembayaran Menunggu Verifikasi!</strong><br>
                    Ada <strong>{{ $hasPendingPayments }}</strong> pembayaran transfer yang perlu diverifikasi. Lihat baris
                    berwarna kuning di tabel.
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-success" onclick="validasiLunas({{ $pinjaman->id }})">
                    <i class="ti ti-check-circle"></i> Validasi Lunas
                </button>
                <a href="{{ route('pinjaman.pinjaman.detail', $pinjaman->id) }}" class="btn btn-primary">
                    <i class="ti ti-file-text"></i> Detail Pinjaman
                </a>
                <button class="btn btn-info" onclick="cetakKartuAngsuran({{ $pinjaman->id }})">
                    <i class="ti ti-printer"></i> Cetak Kartu Angsuran
                </button>
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
                            height="150" alt="Foto Anggota"
                            onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
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
                                        <strong
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
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-calendar-stats"></i> Sisa Angsuran
                        </p>
                        <span class="fw-semibold fs-5 text-primary">
                            {{ $pinjaman->jumlah_sisa_angsuran }} Bulan
                        </span>
                    </div>

                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-coins"></i> Dibayar
                        </p>
                        <span class="fw-semibold fs-5 text-success">
                            Rp {{ number_format($pinjaman->sudah_dibayar, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-alert-triangle"></i> Denda
                        </p>
                        <span class="fw-semibold fs-5 text-danger">
                            Rp {{ number_format($pinjaman->jumlah_denda, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1">
                            <i class="ti ti-cash"></i> Sisa Tagihan
                        </p>
                        <span class="fw-semibold fs-5 text-warning" id="totalBayar">
                            Rp {{ number_format($pinjaman->total_sisa_tagihan, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="col-md-3 col-12">
                        <p class="text-muted mb-1">
                            <i class="ti ti-check-circle"></i> Status Pelunasan
                        </p>
                        @if($pinjaman->status_lunas == 'Lunas')
                            <span class="badge bg-success-subtle text-success shadow-sm fw-semibold px-3 py-2" id="statusLunas">
                                <i class="ti ti-check"></i> Lunas
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger shadow-sm fw-semibold px-3 py-2" id="statusLunas">
                                <i class="ti ti-x"></i> Belum Lunas
                            </span>
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
                    <button class="btn btn-secondary btn-sm" onclick="lihatRiwayatHapus()">
                        <i class="ti ti-history"></i> Riwayat Hapus
                    </button>
                </div>
                <div class="col-md-8">
                    <div class="d-flex gap-2 justify-content-end flex-wrap">
                        <input type="text" class="form-control form-control-sm" id="searchKode" placeholder="Cari kode..."
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

            <!-- Table Jadwal Angsuran -->
            <div class="table-responsive">
                <table id="tabelAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
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
                            <th width="100px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwalAngsuran as $item)
                            @php
                                // Cek apakah ada pembayaran pending untuk jadwal ini
                                $pendingPayment = $item->detailPembayaran->where('status_verifikasi', 'pending')->first();
                                $isPending = $pendingPayment !== null;
                            @endphp

                            <tr data-id="{{ $item->id }}" data-status="{{ $item->status_bayar }}"
                                class="{{ $item->status_bayar == 'Lunas' ? 'table-success-subtle' : ($item->is_terlambat ? 'table-danger-subtle' : '') }} {{ $isPending ? 'table-warning-custom' : '' }}">
                                <td class="text-center">
                                    @if($item->pembayaranTerakhir)
                                        <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                            {{ $item->pembayaranTerakhir->kode_bayar }}
                                        </span>
                                        @if($isPending)
                                            <br>
                                            <span class="badge bg-warning-subtle text-warning fw-semibold shadow-sm mt-1">
                                                <i class="ti ti-clock"></i> Pending
                                            </span>
                                        @endif
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
                                    <div>{{ $item->tanggal_jatuh_tempo->translatedFormat('d F Y') }}</div>
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
                                        <strong class="text-success">Rp
                                            {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                                    @elseif($isPending)
                                        <strong class="text-warning">Rp
                                            {{ number_format($pendingPayment->jumlah_bayar, 0, ',', '.') }}</strong>
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
                                        <span class="badge bg-success-subtle text-success fw-semibold shadow-sm">
                                            <i class="ti ti-check"></i> Lunas
                                        </span>
                                    @elseif($isPending)
                                        <span class="badge bg-warning-subtle text-warning fw-semibold shadow-sm">
                                            <i class="ti ti-clock-hour-4"></i> Pending Verifikasi
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger shadow-sm">
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
                                    @if($isPending)
                                        <button class="btn btn-sm btn-info" onclick="showVerifikasiModal({{ $pendingPayment->id }})"
                                            data-bs-toggle="tooltip" title="Verifikasi Pembayaran">
                                            <i class="ti ti-eye"></i> Verifikasi
                                        </button>
                                    @elseif($item->status_bayar == 'Lunas' && $item->pembayaranTerakhir)
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button class="btn btn-sm btn-primary"
                                                onclick="cetakNota({{ $item->pembayaranTerakhir->id }})" data-bs-toggle="tooltip"
                                                title="Cetak Nota">
                                                <i class="ti ti-printer"></i>
                                            </button>

                                            @if($item->pembayaranTerakhir->bukti_transfer)
                                                <button class="btn btn-sm btn-info"
                                                    onclick="lihatBuktiTransfer({{ $item->pembayaranTerakhir->id }})"
                                                    data-bs-toggle="tooltip" title="Lihat Bukti Transfer">
                                                    <i class="ti ti-photo"></i>
                                                </button>
                                            @endif
                                        </div>
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

    <!-- Modal Verifikasi Pembayaran Transfer -->
    <div class="modal fade" id="modalVerifikasi" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-dark">
                    <h5 class="modal-title"><i class="ti ti-file-check"></i> Verifikasi Pembayaran Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="verifikasiContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bukti Transfer (View Full Image) -->
    <div class="modal fade" id="modalBuktiTransfer" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light text-white">
                    <h5 class="modal-title"><i class="ti ti-photo"></i> Bukti Transfer</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="imgBuktiFull" src="" alt="Bukti Transfer" class="img-fluid" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bantuan -->
    <div class="modal fade" id="modalBantuan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-white">
                    <h5 class="modal-title"><i class="ti ti-help"></i> Cara Pembayaran Angsuran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">A. Pembayaran Angsuran Tunai</h6>
                            <ul class="mb-4">
                                <li class="mb-2">Pilih baris angsuran yang <strong class="text-danger">Belum Bayar</strong>
                                </li>
                                <li class="mb-2">Klik tombol <strong class="text-success">Bayar</strong></li>
                                <li class="mb-2">Isi form pembayaran dan pilih kas tunai</li>
                                <li class="mb-2">Denda otomatis terhitung jika terlambat</li>
                                <li class="mb-2">Pembayaran langsung diproses</li>
                            </ul>

                            <h6 class="fw-bold mb-3">B. Verifikasi Pembayaran Transfer</h6>
                            <ul class="mb-4">
                                <li class="mb-2">Baris berwarna <strong class="text-warning">kuning</strong> = ada
                                    pembayaran pending</li>
                                <li class="mb-2">Klik tombol <strong class="text-warning">"Lihat"</strong> di kolom Aksi
                                </li>
                                <li class="mb-2">Periksa detail pembayaran dan bukti transfer</li>
                                <li class="mb-2">Tambahkan denda jika ada keterlambatan</li>
                                <li class="mb-2">Klik <strong class="text-success">Approve</strong> untuk menyetujui</li>
                                <li class="mb-2">Klik <strong class="text-danger">Tolak</strong> jika bukti tidak valid</li>
                            </ul>

                            <h6 class="fw-bold mb-3">C. Edit & Hapus Pembayaran</h6>
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

        // ============================================
        // PERBAIKAN FINAL: Function lihatBuktiTransfer
        // Ganti function yang error dengan kode ini
        // ============================================

        function lihatBuktiTransfer(detailBayarId) {
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            // ✅ URL YANG BENAR dengan prefix /admin/
            const url = '/admin/bayar/get-bukti-transfer/' + detailBayarId;

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        Swal.close();
                        showBuktiTransferModal(response.data);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Gagal mengambil data pembayaran'
                    });
                }
            });
        }

        // Function showBuktiTransferModal tetap sama (tidak perlu diubah)
        function showBuktiTransferModal(data) {
            const content = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-header bg-success-subtle">
                                                <h6 class="mb-0"><i class="ti ti-file-check"></i> Detail Pembayaran</h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-sm table-borderless">
                                                    <tr>
                                                        <td class="fw-semibold" style="width: 40%;">Kode Bayar</td>
                                                        <td style="width: 5%;">:</td>
                                                        <td><span class="badge bg-primary-subtle text-primary fw-semibold">${data.kode_bayar}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Status</td>
                                                        <td>:</td>
                                                        <td><span class="badge bg-success-subtle text-success fw-semibold">
                                                            <i class="ti ti-check"></i> ${data.status_verifikasi_text}
                                                        </span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Nama Anggota</td>
                                                        <td>:</td>
                                                        <td>${data.anggota_nama}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Angsuran Ke</td>
                                                        <td>:</td>
                                                        <td><span class="badge bg-secondary">Ke-${data.angsuran_ke}</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Tanggal Bayar</td>
                                                        <td>:</td>
                                                        <td>${data.tanggal_bayar_formatted}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Jumlah Bayar</td>
                                                        <td>:</td>
                                                        <td class="text-success fw-bold">Rp ${data.jumlah_bayar_formatted}</td>
                                                    </tr>
                                                    ${data.denda > 0 ? `
                                                    <tr>
                                                        <td class="fw-semibold">Denda</td>
                                                        <td>:</td>
                                                        <td class="text-danger fw-bold">Rp ${data.denda_formatted}</td>
                                                    </tr>
                                                    ` : ''}
                                                    <tr class="border-top">
                                                        <td class="fw-semibold pt-2">Total Dibayar</td>
                                                        <td class="pt-2">:</td>
                                                        <td class="pt-2"><strong class="text-success fs-5">Rp ${data.total_bayar_formatted}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="fw-semibold">Bank Transfer</td>
                                                        <td>:</td>
                                                        <td><span class="badge bg-info">${data.bank_nama}</span></td>
                                                    </tr>
                                                    ${data.keterangan ? `
                                                    <tr>
                                                        <td class="fw-semibold">Keterangan</td>
                                                        <td>:</td>
                                                        <td>${data.keterangan}</td>
                                                    </tr>
                                                    ` : ''}
                                                </table>
                                                ${data.verified_by ? `
                                                <div class="alert alert-success mt-3 mb-0">
                                                    <small>
                                                        <i class="ti ti-user-check"></i> Diverifikasi oleh: <strong>${data.verified_by}</strong><br>
                                                        <i class="ti ti-calendar"></i> Pada: ${data.verified_at_formatted}
                                                    </small>
                                                </div>
                                                ` : ''}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-info">
                                            <div class="card-header bg-info-subtle">
                                                <h6 class="mb-0"><i class="ti ti-photo"></i> Bukti Transfer</h6>
                                            </div>
                                            <div class="card-body text-center">
                                                <img src="${data.bukti_url}" 
                                                     alt="Bukti Transfer" 
                                                     class="img-fluid rounded border shadow-sm" 
                                                     style="max-height: 500px; cursor: pointer;"
                                                     onclick="showFullImage('${data.bukti_url}')">
                                                <p class="text-muted small mt-2 mb-0">
                                                    <i class="ti ti-info-circle"></i> Klik gambar untuk memperbesar
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;

            Swal.fire({
                title: 'Bukti Transfer Pembayaran',
                html: content,
                width: '900px',
                showConfirmButton: true,
                confirmButtonText: '<i class="ti ti-x"></i> Tutup',
                confirmButtonColor: '#6c757d'
            });
        }

        // ============================================
        // VERIFIKASI PEMBAYARAN TRANSFER
        // ============================================

        // Function: Show Modal Verifikasi
        function showVerifikasiModal(detailBayarId) {
            Swal.fire({
                title: 'Memuat Data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            // Get detail pembayaran via AJAX
            $.ajax({
                url: '{{ route("pinjaman.bayar.pendingDetail", ":id") }}'.replace(':id', detailBayarId),
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        Swal.close();
                        displayVerifikasiModal(response.data);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengambil data pembayaran'
                    });
                }
            });
        }

        // Function: Display Verifikasi Modal Content
        function displayVerifikasiModal(data) {
            const content = `
                                                        <div class="row">
                                                            <!-- LEFT: Detail Pembayaran -->
                                                            <div class="col-md-6">
                                                                <div class="card border-warning">
                                                                    <div class="card-header bg-warning-subtle">
                                                                        <h6 class="mb-0"><i class="ti ti-file-text"></i> Detail Pembayaran</h6>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <table class="table table-sm">
                                                                            <tr>
                                                                                <td class="fw-semibold">Kode Bayar</td>
                                                                                <td>:</td>
                                                                                <td><span class="badge bg-primary-subtle text-primary fw-semibold">${data.kode_bayar}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-semibold">Nama Anggota</td>
                                                                                <td>:</td>
                                                                                <td>${data.anggota_nama}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-semibold">Angsuran Ke</td>
                                                                                <td>:</td>
                                                                                <td><span class="badge bg-secondary">Ke-${data.angsuran_ke}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-semibold">Tanggal Bayar</td>
                                                                                <td>:</td>
                                                                                <td>${data.tanggal_bayar_formatted}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-semibold">Jumlah Angsuran</td>
                                                                                <td>:</td>
                                                                                <td>Rp ${data.jumlah_angsuran_formatted}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-semibold">Jumlah Bayar</td>
                                                                                <td>:</td>
                                                                                <td class="text-success fw-bold">Rp ${data.jumlah_bayar_formatted}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="fw-semibold">Bank Transfer</td>
                                                                                <td>:</td>
                                                                                <td><span class="badge bg-info">${data.bank_nama}</span></td>
                                                                            </tr>
                                                                            ${data.keterangan ? `
                                                                            <tr>
                                                                                <td class="fw-semibold">Keterangan</td>
                                                                                <td>:</td>
                                                                                <td>${data.keterangan}</td>
                                                                            </tr>
                                                                            ` : ''}
                                                                        </table>

                                                                        <!-- Form Denda -->
                                                                        <div class="mt-3">
                                                                            <label class="form-label fw-semibold">Tambah Denda (Opsional)</label>
                                                                            <div class="input-group">
                                                                                <span class="input-group-text">Rp</span>
                                                                                <input type="number" class="form-control" id="dendaInput" min="0" step="1000" value="${data.denda_otomatis}">
                                                                            </div>
                                                                            <small class="text-muted">Denda otomatis: Rp ${data.denda_otomatis.toLocaleString('id-ID')}</small>
                                                                        </div>

                                                                        <div class="mt-3">
                                                                            <label class="form-label fw-semibold">Catatan Verifikasi (Opsional)</label>
                                                                            <textarea class="form-control" id="catatanInput" rows="2" placeholder="Catatan tambahan..."></textarea>
                                                                        </div>

                                                                        <div class="alert alert-success mt-3">
                                                                            <div class="d-flex justify-content-between">
                                                                                <span>Total yang Diterima:</span>
                                                                                <strong class="fs-5" id="totalVerifikasi">Rp ${(parseFloat(data.jumlah_bayar) + parseFloat(data.denda_otomatis)).toLocaleString('id-ID')}</strong>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Action Buttons -->
                                                                        <div class="d-grid gap-2 mt-3">
                                                                            <button class="btn btn-success" onclick="approvePayment(${data.id})">
                                                                                <i class="ti ti-check"></i> Approve Pembayaran
                                                                            </button>
                                                                            <button class="btn btn-danger" onclick="rejectPayment(${data.id})">
                                                                                <i class="ti ti-x"></i> Tolak Pembayaran
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- RIGHT: Bukti Transfer -->
                                                            <div class="col-md-6">
                                                                <div class="card border-light">
                                                                    <div class="card-header bg-info-subtle">
                                                                        <h6 class="mb-0"><i class="ti ti-photo"></i> Bukti Transfer</h6>
                                                                    </div>
                                                                    <div class="card-body text-center">
                                                                        <img src="${data.bukti_url}" 
                                                                             alt="Bukti Transfer" 
                                                                             class="img-fluid rounded border" 
                                                                             style="max-height: 500px; cursor: pointer;"
                                                                             onclick="showFullImage('${data.bukti_url}')">
                                                                        <p class="text-muted small mt-2">
                                                                            <i class="ti ti-info-circle"></i> Klik gambar untuk memperbesar
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    `;

            $('#verifikasiContent').html(content);
            $('#modalVerifikasi').modal('show');

            // Update total saat denda berubah
            $('#dendaInput').on('input', function () {
                // ✅ Pastikan jumlah_bayar di-parse ke number
                const bayar = parseFloat(data.jumlah_bayar) || 0;

                const dendaValue = $(this).val().replace(/\D/g, '');
                const denda = parseFloat(dendaValue) || 0;

                const total = bayar + denda;

                $('#totalVerifikasi').text('Rp ' + total.toLocaleString('id-ID'));

                console.log('Bayar:', bayar, 'Denda:', denda, 'Total:', total);
            });
        }

        // Function: Show Full Image
        function showFullImage(url) {
            Swal.fire({
                imageUrl: url,
                imageAlt: 'Bukti Transfer',
                showConfirmButton: true,
                confirmButtonText: '<i class="ti ti-x"></i> Tutup',
                confirmButtonColor: '#6c757d',
                width: '90%',
                imageHeight: '80vh',
                customClass: {
                    popup: 'swal-image-fullscreen'
                },
                didOpen: () => {
                    // Tambahkan click to zoom
                    const img = Swal.getHtmlContainer().querySelector('img');
                    if (img) {
                        img.style.objectFit = 'contain';
                        img.style.cursor = 'zoom-in';
                        img.style.transition = 'transform 0.3s ease';

                        img.addEventListener('click', function () {
                            if (this.style.transform === 'scale(1.5)') {
                                this.style.transform = 'scale(1)';
                                this.style.cursor = 'zoom-in';
                            } else {
                                this.style.transform = 'scale(1.5)';
                                this.style.cursor = 'zoom-out';
                            }
                        });
                    }
                }
            });
        }

        // Function: Approve Payment
        function approvePayment(detailBayarId) {
            const denda = $('#dendaInput').val() || 0;
            const catatan = $('#catatanInput').val();

            Swal.fire({
                title: 'Konfirmasi Approve',
                html: `
                                                            <div class="text-start">
                                                                <p>Apakah Anda yakin ingin <strong class="text-success">menyetujui</strong> pembayaran ini?</p>
                                                                ${denda > 0 ? `<p class="text-danger mb-0"><i class="ti ti-alert-circle"></i> Denda: Rp ${parseFloat(denda).toLocaleString('id-ID')}</p>` : ''}
                                                                <div class="alert alert-info mt-2 mb-0">
                                                                    <i class="ti ti-info-circle"></i>
                                                                    Pembayaran akan langsung diproses dan status angsuran akan menjadi <strong>Lunas</strong>
                                                                </div>
                                                            </div>
                                                        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-check"></i> Ya, Approve',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#13a460'
            }).then((result) => {
                if (result.isConfirmed) {
                    processApprove(detailBayarId, denda, catatan);
                }
            });
        }

        // Function: Process Approve
        function processApprove(detailBayarId, denda, catatan) {
            Swal.fire({
                title: 'Memproses Verifikasi...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("pinjaman.bayar.approveTransfer", ":id") }}'.replace(':id', detailBayarId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    denda: denda,
                    catatan: catatan
                },
                success: function (response) {
                    if (response.success) {
                        $('#modalVerifikasi').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran berhasil diverifikasi dan disetujui',
                            confirmButtonColor: '#13a460'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        }

        // Function: Reject Payment
        function rejectPayment(detailBayarId) {
            Swal.fire({
                title: 'Tolak Pembayaran',
                html: `
                                                            <div class="text-start">
                                                                <div class="alert alert-danger">
                                                                    <i class="ti ti-alert-triangle"></i>
                                                                    <strong>Perhatian!</strong><br>
                                                                    Pembayaran akan ditolak dan user harus melakukan pembayaran ulang
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="alasanPenolakan" rows="3" placeholder="Jelaskan alasan penolakan..." required></textarea>
                                                                    <small class="text-muted">Alasan akan ditampilkan kepada user</small>
                                                                </div>
                                                            </div>
                                                        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-x"></i> Tolak Pembayaran',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545',
                width: '600px',
                preConfirm: () => {
                    const alasan = $('#alasanPenolakan').val().trim();
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan penolakan wajib diisi');
                        return false;
                    }
                    return { alasan: alasan };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    processReject(detailBayarId, result.value.alasan);
                }
            });
        }

        // Function: Process Reject
        function processReject(detailBayarId, alasan) {
            Swal.fire({
                title: 'Memproses Penolakan...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("pinjaman.bayar.rejectTransfer", ":id") }}'.replace(':id', detailBayarId),
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    catatan: alasan
                },
                success: function (response) {
                    if (response.success) {
                        $('#modalVerifikasi').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pembayaran ditolak',
                            confirmButtonColor: '#dc3545'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        }

        // ============================================
        // EXISTING FUNCTIONS (Bayar Tunai, Edit, Delete, etc.)
        // ============================================

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

        // Function: Validasi Lunas
        function validasiLunas(pinjamanId) {
            Swal.fire({
                title: 'Validasi Pelunasan',
                html: `
                                                            <div class="text-start">
                                                                <div class="alert alert-warning">
                                                                    <i class="ti ti-alert-triangle"></i>
                                                                    <strong>Perhatian!</strong>
                                                                    <ul class="mb-0 mt-2">
                                                                        <li>Pastikan SEMUA angsuran sudah dibayar</li>
                                                                        <li>Tidak ada pembayaran yang pending verifikasi</li>
                                                                        <li>Data akan dipindahkan ke <strong>Pinjaman Lunas</strong></li>
                                                                        <li>Status pinjaman akan menjadi <strong>LUNAS</strong></li>
                                                                        <li>Proses ini tidak dapat dibatalkan</li>
                                                                    </ul>
                                                                </div>
                                                                <p class="mb-0">Apakah Anda yakin ingin memvalidasi pinjaman ini sebagai <strong>LUNAS</strong>?</p>
                                                            </div>
                                                        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-check"></i> Ya, Validasi Lunas',
                cancelButtonText: '<i class="ti ti-x"></i> Batal',
                confirmButtonColor: '#13a460',
                cancelButtonColor: '#6c757d',
                width: '600px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Memproses Validasi...',
                        html: 'Mohon tunggu, sedang memvalidasi pelunasan pinjaman',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Process validation
                    $.ajax({
                        url: '{{ route("pinjaman.bayar.validasi", ":id") }}'.replace(':id', pinjamanId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Validasi Berhasil!',
                                    html: `
                                                                                <div class="text-start">
                                                                                    <div class="alert alert-success mb-3">
                                                                                        <i class="ti ti-check-circle"></i>
                                                                                        Pinjaman berhasil divalidasi sebagai <strong>LUNAS</strong>
                                                                                    </div>
                                                                                    <p class="mb-2"><strong>Kode Lunas:</strong> ${response.kode_lunas}</p>
                                                                                    <p class="mb-0 text-muted">Data telah dipindahkan ke menu Pinjaman Lunas</p>
                                                                                </div>
                                                                            `,
                                    confirmButtonText: '<i class="ti ti-eye"></i> Lihat Data Lunas',
                                    showCancelButton: true,
                                    cancelButtonText: 'Tutup',
                                    confirmButtonColor: '#13a460'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = response.redirect || '{{ route("pinjaman.lunas") }}';
                                    } else {
                                        location.reload();
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            const errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat memvalidasi pelunasan';

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal!',
                                html: `
                                                                            <div class="text-start">
                                                                                <div class="alert alert-danger">
                                                                                    <i class="ti ti-alert-circle"></i>
                                                                                    ${errorMessage}
                                                                                </div>
                                                                                ${xhr.status === 400 ? '<p class="text-muted mb-0">Periksa kembali status pembayaran angsuran</p>' : ''}
                                                                            </div>
                                                                        `,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
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
                success: function (response) {
                    if (response.success) {
                        showModalBayar(response.data);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Gagal mengambil data'
                    });
                }
            });
        }

        // Function: Show Modal Bayar (TUNAI)
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
                    $('#dendaBayar').on('input', function () {
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
                success: function (response) {
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
                error: function (xhr) {
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
                success: function (response) {
                    if (response.success) {
                        showModalEdit(response.data);
                    }
                },
                error: function (xhr) {
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
                    $('#jumlahBayarEdit, #dendaBayarEdit').on('input', function () {
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
                success: function (response) {
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
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupdate data'
                    });
                }
            });
        }

        // ============================================
        // SOFT DELETE FUNCTIONS
        // ============================================

        // Function: Hapus Bayar (Soft Delete)
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
                html: `
                                                            <div class="text-start">
                                                                <p>Apakah Anda yakin ingin menghapus pembayaran ini?</p>
                                                                <div class="alert alert-info">
                                                                    <i class="ti ti-info-circle"></i>
                                                                    Data akan dipindahkan ke <strong>Riwayat Hapus</strong> dan dapat dipulihkan kembali
                                                                </div>
                                                            </div>
                                                        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-trash"></i> Ya, Hapus',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    prosesSoftDelete(angsuranId);
                }
            });
        }

        // Function: Proses Soft Delete
        function prosesSoftDelete(angsuranId) {
            Swal.fire({
                title: 'Menghapus Data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            // Get detail pembayaran ID dulu
            $.ajax({
                url: '{{ route("pinjaman.bayar.getPembayaran", ":id") }}'.replace(':id', angsuranId),
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        const detailId = response.data.id;

                        // Lakukan soft delete
                        $.ajax({
                            url: '{{ route("pinjaman.bayar.softDelete", ":id") }}'.replace(':id', detailId),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        html: `
                                                                                    ${response.message}
                                                                                    <div class="mt-3">
                                                                                        <button class="btn btn-sm btn-secondary" onclick="lihatRiwayatHapus()">
                                                                                            <i class="ti ti-history"></i> Lihat Riwayat
                                                                                        </button>
                                                                                    </div>
                                                                                `,
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data'
                                });
                            }
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengambil data pembayaran'
                    });
                }
            });
        }

        // Function: Lihat Riwayat Hapus
        function lihatRiwayatHapus() {
            const pinjamanId = {{ $pinjaman->id }};

            Swal.fire({
                title: 'Memuat Riwayat...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: '{{ route("pinjaman.bayar.riwayatHapus", ":id") }}'.replace(':id', pinjamanId),
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        showModalRiwayat(response.data);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengambil riwayat hapus'
                    });
                }
            });
        }

        // Function: Show Modal Riwayat
        function showModalRiwayat(data) {
            if (data.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Riwayat Kosong',
                    text: 'Tidak ada pembayaran yang dihapus',
                    confirmButtonColor: '#5d87ff'
                });
                return;
            }

            let tableRows = '';
            data.forEach((item, index) => {
                const deletedAt = new Date(item.deleted_at);
                const formattedDate = deletedAt.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
                const formattedTime = deletedAt.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                const totalBayar = parseFloat(item.jumlah_bayar) + (parseFloat(item.denda) || 0);

                tableRows += `
                                                            <tr class="align-middle">
                                                                <td class="text-center">
                                                                    <span class="badge rounded-pill bg-primary fs-3">${index + 1}</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-primary-subtle text-primary shadow-sm fw-semibold fs-3 px-3 py-2">
                                                                        ${item.kode_bayar}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge bg-secondary-subtle text-secondary shadow-sm fw-semibold fs-3 px-3 py-2">
                                                                        Ke-${item.angsuran_ke}
                                                                    </span>
                                                                </td>
                                                                <td class="text-end">
                                                                    <strong class="text-dark fs-4">Rp ${parseFloat(item.jumlah_bayar).toLocaleString('id-ID')}</strong>
                                                                    ${item.denda > 0 ? `<br><small class="text-danger fs-4 fw-bold">Denda: Rp ${parseFloat(item.denda).toLocaleString('id-ID')}</small>` : ''}
                                                                    <br><small class="text-muted fw-bold">Total: Rp ${totalBayar.toLocaleString('id-ID')}</small>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div>
                                                                        <i class="ti ti-calendar me-1 fs-3"></i>${formattedDate}
                                                                    </div>
                                                                    <small class="text-muted fs-3">
                                                                        <i class="ti ti-clock me-1"></i>${formattedTime}
                                                                    </small>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center gap-1">
                                                                        <button class="btn btn-success btn-sm" onclick="restorePembayaran(${item.id})">
                                                                            <i class="ti ti-refresh me-1"></i>Pulihkan
                                                                        </button>
                                                                        <button class="btn btn-danger btn-sm" onclick="hapusPermanen(${item.id})">
                                                                            <i class="ti ti-trash-x me-1"></i>Hapus
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        `;
            });

            Swal.fire({
                title: 'Riwayat Pembayaran Terhapus',
                html: `
                                                            <div class="container-fluid px-0">
                                                                <!-- Alert Info -->
                                                                <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                                                                    <div class="d-flex align-items-start">
                                                                        <i class="ti ti-info-circle fs-4 me-2 mt-1"></i>
                                                                        <div class="text-start flex-grow-1">
                                                                            <h6 class="alert-heading mb-1">Informasi Penting</h6>
                                                                            <p class="mb-0 small">
                                                                                Data yang telah dihapus masih dapat dipulihkan. Untuk menghapus permanen dari database, 
                                                                                klik tombol <strong>"Hapus"</strong>.
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                                                </div>

                                                                <!-- Stats Card -->
                                                                <div class="row g-2 mb-3">
                                                                    <div class="col-md-4">
                                                                        <div class="card border-primary">
                                                                            <div class="card-body text-center py-2">
                                                                                <h6 class="text-muted mb-0 small">Total Dihapus</h6>
                                                                                <h4 class="mb-0 text-primary">${data.length}</h4>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="card border-success">
                                                                            <div class="card-body text-center py-2">
                                                                                <h6 class="text-muted mb-0 small">Total Nilai</h6>
                                                                                <h4 class="mb-0 text-success">Rp ${data.reduce((sum, item) => sum + parseFloat(item.jumlah_bayar) + (parseFloat(item.denda) || 0), 0).toLocaleString('id-ID')}</h4>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="card border-danger">
                                                                            <div class="card-body text-center py-2">
                                                                                <h6 class="text-muted mb-0 small">Total Denda</h6>
                                                                                <h4 class="mb-0 text-danger">Rp ${data.reduce((sum, item) => sum + (parseFloat(item.denda) || 0), 0).toLocaleString('id-ID')}</h4>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Table -->
                                                                <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                                                                    <table class="table table-hover mb-0">
                                                                        <thead class="table-primary position-sticky top-0">
                                                                            <tr>
                                                                                <th class="text-center" style="width: 60px;">No</th>
                                                                                <th class="text-center" style="width: 120px;">Kode</th>
                                                                                <th class="text-center" style="width: 100px;">Angsuran</th>
                                                                                <th class="text-end" style="width: 150px;">Jumlah</th>
                                                                                <th class="text-center" style="width: 140px;">Dihapus Pada</th>
                                                                                <th class="text-center" style="width: 160px;">Aksi</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            ${tableRows}
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                <!-- Footer -->
                                                                <div class="card border-0 bg-light mt-3">
                                                                    <div class="card-body py-2">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <small class="text-muted">
                                                                                <i class="ti ti-database me-1"></i>
                                                                                Menampilkan ${data.length} dari ${data.length} data
                                                                            </small>
                                                                            <button class="btn btn-sm btn-secondary" onclick="Swal.close()">
                                                                                <i class="ti ti-x me-1"></i>Tutup
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        `,
                width: '1100px',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    container: 'swal-wide',
                    popup: 'rounded-3 shadow-lg'
                }
            });
        }

        // Function: Restore Pembayaran
        function restorePembayaran(detailId) {
            Swal.fire({
                title: 'Pulihkan Pembayaran?',
                text: 'Data akan dikembalikan ke daftar pembayaran',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pulihkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memulihkan Data...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '{{ route("pinjaman.bayar.restore", ":id") }}'.replace(':id', detailId),
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Gagal memulihkan data'
                            });
                        }
                    });
                }
            });
        }

        // Function: Hapus Permanen
        function hapusPermanen(detailId) {
            Swal.fire({
                title: 'Hapus Permanen?',
                html: `
                                                            <div class="alert alert-danger">
                                                                <i class="ti ti-alert-triangle"></i>
                                                                <strong>PERHATIAN!</strong><br>
                                                                Data akan dihapus secara permanen dari database dan tidak dapat dipulihkan kembali
                                                            </div>
                                                        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Permanen',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Permanen...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: '{{ route("pinjaman.bayar.forceDelete", ":id") }}'.replace(':id', detailId),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(() => {
                                    lihatRiwayatHapus(); // Refresh modal riwayat
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data'
                            });
                        }
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