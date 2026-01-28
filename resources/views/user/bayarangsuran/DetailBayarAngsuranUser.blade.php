@extends('layouts.app')

@section('title', 'Detail & Bayar Angsuran')

@push('styles')
    <style>
        .jadwal-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .jadwal-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .jadwal-card.selected {
            border: 2px solid #13a460 !important;
            background: #f0fdf4;
        }

        .status-badge-lunas {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .status-badge-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .status-badge-belum {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* ✅ PENTING: Tambahkan CSS untuk disabled state */
        .jadwal-card.disabled {
            opacity: 0.6;
            cursor: not-allowed !important;
        }

        .jadwal-card.disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    </style>
@endpush

@section('content')

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-semibold mb-1">Bayar Angsuran</h4>
                    <p class="text-muted mb-0">Kode: <strong class="text-primary">{{ $pinjaman->kode_pinjaman }}</strong>
                    </p>
                    <p class="text-muted mb-0">Username: <strong
                            class="text-primary">{{ $pinjaman->anggota->nama }}</strong></p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('user.bayar.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-info" onclick="showHelp()">
                        <i class="ti ti-help"></i> Bantuan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-alert-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Alert -->
    @if($has_pending)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="ti ti-clock-hour-4 me-2"></i>
            <strong>Pembayaran Sedang Diverifikasi</strong>
            <p class="mb-0 mt-2">Anda memiliki pembayaran yang sedang menunggu verifikasi admin untuk pinjaman ini. Silakan
                tunggu proses verifikasi selesai sebelum melakukan pembayaran berikutnya.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(!$is_lunas)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="ti ti-info-circle me-2"></i>
            <strong>Cara Pembayaran:</strong>
            <ol class="mb-0 mt-2 ps-3">
                <li>Pilih angsuran yang ingin dibayar dengan klik card angsuran</li>
                <li>Isi form pembayaran di bagian kanan</li>
                <li>Pilih bank transfer tujuan</li>
                <li>Upload bukti transfer</li>
                <li>Klik "Kirim Pembayaran" dan tunggu verifikasi admin</li>
            </ol>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- LEFT: Detail Pinjaman & Jadwal -->
        <div class="col-lg-8">
            <!-- Info Pinjaman Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light text-white">
                    <h5 class="mb-0"><i class="ti ti-file-invoice me-2"></i>Informasi Pinjaman</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Foto -->
                        <div class="col-md-2 text-center mb-3">
                            <img src="{{ asset($pinjaman->anggota->photo_display) }}" alt="Foto"
                                class="rounded border shadow-sm" style="width: 100px; height: 120px; object-fit: cover;"
                                onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                        </div>

                        <!-- Data Anggota -->
                        <div class="col-md-4 mb-3">
                            <h6 class="text-primary fw-semibold mb-3 border-bottom pb-2">Data Anggota</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">ID</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->anggota->id_anggota }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Nama</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->anggota->nama }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dept</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->anggota->departement ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Data Pinjaman -->
                        <div class="col-md-3 mb-3">
                            <h6 class="text-primary fw-semibold mb-3 border-bottom pb-2">Data Pinjaman</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Tgl Pinjam</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->tanggal_pinjam->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis</td>
                                    <td>:</td>
                                    <td><span class="badge bg-primary">{{ $pinjaman->jenis_pinjaman }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lama</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->lamaAngsuran->lama_angsuran }} Bulan</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Ringkasan Pembayaran -->
                        <div class="col-md-3">
                            <h6 class="text-primary fw-semibold mb-3 border-bottom pb-2">Ringkasan</h6>
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">Pokok</td>
                                    <td>:</td>
                                    <td class="text-end fw-bold">{{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Bunga</td>
                                    <td>:</td>
                                    <td class="text-end fw-bold text-info">
                                        {{ number_format($pinjaman->biaya_bunga * $pinjaman->lamaAngsuran->lama_angsuran, 0, ',', '.') }}
                                    </td>
                                </tr>
                                {{-- ✅ TAMBAHAN: Tampilkan denda jika ada --}}
                                @if($total_denda_dibayar > 0)
                                    <tr>
                                        <td class="text-muted">Denda</td>
                                        <td>:</td>
                                        <td class="text-end fw-bold text-danger">
                                            {{ number_format($total_denda_dibayar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="text-muted fw-semibold">Total Angsuran</td>
                                    <td>:</td>
                                    <td class="text-end fw-bold text-success">
                                        {{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}
                                    </td>
                                </tr>
                                {{-- ✅ TAMBAHAN: Grand Total (jika ada denda) --}}
                                @if($total_denda_dibayar > 0)
                                    <tr class="border-top">
                                        <td class="text-muted fw-semibold">Grand Total</td>
                                        <td>:</td>
                                        <td class="text-end fw-bold text-primary">
                                            {{ number_format($pinjaman->jumlah_angsuran + $total_denda_dibayar, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @php
                        $progress = $pinjaman->jumlah_angsuran > 0 ? ($total_dibayar_pokok / $pinjaman->jumlah_angsuran) * 100 : 0;
                    @endphp
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Progress Pembayaran</span>
                            <span class="small fw-semibold">{{ number_format($progress, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: {{ min($progress, 100) }}%">
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Rp {{ number_format($total_dibayar_pokok, 0, ',', '.') }}</small>
                            <small class="text-muted">Rp
                                {{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</small>
                        </div>
                    </div>
                </div>

                <!-- Footer Status -->
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-3">
                            <small class="text-muted d-block">
                                <i class="ti ti-wallet"></i> Pokok Dibayar
                            </small>
                            <strong class="text-primary">
                                Rp {{ number_format($total_dibayar_pokok, 0, ',', '.') }}
                            </strong>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">
                                <i class="ti ti-alert-triangle"></i> Denda Dibayar
                            </small>
                            <strong class="text-danger">
                                Rp {{ number_format($total_denda_dibayar, 0, ',', '.') }}
                            </strong>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">
                                <i class="ti ti-check-circle"></i> Total Dibayar
                            </small>
                            <strong class="text-success">
                                Rp {{ number_format($total_dibayar, 0, ',', '.') }}
                            </strong>
                        </div>
                        <div class="col-3">
                            <small class="text-muted d-block">
                                <i class="ti ti-calendar"></i> Angsuran Ke
                            </small>
                            <strong>{{ $angsuran_berikutnya }} / {{ $pinjaman->lamaAngsuran->lama_angsuran }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jadwal Angsuran -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="ti ti-calendar me-2"></i>Jadwal Angsuran</h5>
                </div>
                <div class="card-body">
                    @if($is_lunas)
                        <div class="alert alert-success text-center">
                            <i class="ti ti-check-circle display-6"></i>
                            <h5 class="mt-3">Pinjaman Lunas!</h5>
                            <p class="mb-0">Selamat! Semua angsuran telah dibayar lunas.</p>
                        </div>
                    @else
                        {{-- ✅ PERBAIKAN: Tampilkan pesan jika ada pending global --}}
                        @if($has_pending)
                            <div class="alert alert-warning mb-3">
                                <i class="ti ti-alert-triangle"></i>
                                <strong>Perhatian!</strong> Semua angsuran tidak dapat dipilih karena ada pembayaran yang sedang
                                menunggu verifikasi.
                                Silakan tunggu hingga pembayaran sebelumnya diverifikasi oleh admin.
                            </div>
                        @endif

                        <div class="row g-3" id="jadwalContainer">
                            @foreach($jadwal_angsuran as $jadwal)
                                @php
                                    $status = 'belum';
                                    $badgeClass = 'bg-danger-subtle text-danger';
                                    $statusText = 'Belum Bayar';
                                    $icon = 'ti-clock';

                                    if ($jadwal->status_bayar == 'Lunas') {
                                        $status = 'lunas';
                                        $badgeClass = 'bg-success-subtle text-success';
                                        $statusText = 'Lunas';
                                        $icon = 'ti-check';
                                    }

                                    // ✅ PERBAIKAN: Cek pending berdasarkan variable global dari controller
                                    $pendingForThis = null;
                                    if ($has_pending && $jadwal->status_bayar == 'Belum') {
                                        // Cek apakah ini angsuran yang pending
                                        $pendingForThis = $riwayat_bayar->where('bayar_angsuran_id', $jadwal->id)
                                            ->where('status_verifikasi', 'pending')
                                            ->first();

                                        if ($pendingForThis) {
                                            $status = 'pending';
                                            $badgeClass = 'bg-warning-subtle text-warning';
                                            $statusText = 'Pending Verifikasi';
                                            $icon = 'ti-clock-hour-4';
                                        }
                                    }

                                    // ✅ LOGIKA BARU: Angsuran bisa dipilih HANYA jika:
                                    // 1. Status bayar = Belum
                                    // 2. TIDAK ada pending di seluruh pinjaman ($has_pending = false)
                                    $canSelect = $jadwal->status_bayar == 'Belum' && !$has_pending;

                                    // ✅ Tentukan class CSS
                                    $cardClass = 'border';
                                    if ($status == 'lunas') {
                                        $cardClass .= ' border-success';
                                    } elseif ($status == 'pending') {
                                        $cardClass .= ' border-warning';
                                    } elseif ($has_pending && $jadwal->status_bayar == 'Belum') {
                                        $cardClass .= ' border-secondary disabled'; // Disabled karena ada pending lain
                                    } else {
                                        $cardClass .= ' border-danger';
                                    }

                                    if ($canSelect) {
                                        $cardClass .= ' selectable-jadwal';
                                    }
                                @endphp

                                <div class="col-md-6">
                                    <div class="card jadwal-card {{ $cardClass }}" data-jadwal-id="{{ $jadwal->id }}"
                                        data-can-select="{{ $canSelect ? 'true' : 'false' }}"
                                        data-angsuran-ke="{{ $jadwal->angsuran_ke }}"
                                        data-jatuh-tempo="{{ $jadwal->tanggal_jatuh_tempo->format('Y-m-d') }}"
                                        data-jumlah="{{ $jadwal->jumlah_angsuran }}" data-denda="{{ $jadwal->denda_otomatis ?? 0 }}"
                                        data-total="{{ $jadwal->total_tagihan ?? $jadwal->jumlah_angsuran }}"
                                        data-is-terlambat="{{ isset($jadwal->is_terlambat) && $jadwal->is_terlambat ? 'true' : 'false' }}"
                                        data-hari-terlambat="{{ $jadwal->hari_terlambat ?? 0 }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Angsuran Ke-{{ $jadwal->angsuran_ke }}</h6>
                                                    <small class="text-muted">
                                                        <i class="ti ti-calendar"></i>
                                                        {{ $jadwal->tanggal_jatuh_tempo->format('d M Y') }}
                                                    </small>
                                                </div>
                                                <span class="badge {{ $badgeClass }} status-badge-{{ $status }}">
                                                    <i class="{{ $icon }}"></i> {{ $statusText }}
                                                </span>
                                            </div>

                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div style="flex: 1;">
                                                        <small class="text-muted d-block">Angsuran Pokok</small>
                                                        <strong class="text-primary d-block">
                                                            Rp {{ number_format($jadwal->jumlah_angsuran, 0, ',', '.') }}
                                                        </strong>

                                                        {{-- ✅ Tampilkan denda jika ada --}}
                                                        @if(isset($jadwal->denda_otomatis) && $jadwal->denda_otomatis > 0)
                                                            <small class="text-danger d-block mt-1">
                                                                <i class="ti ti-alert-circle"></i>
                                                                + Denda: Rp {{ number_format($jadwal->denda_otomatis, 0, ',', '.') }}
                                                                @if(isset($jadwal->hari_terlambat))
                                                                    <span
                                                                        class="badge bg-danger-subtle text-danger ms-1 fw-semibold shadow-sm"
                                                                        style="font-size: 12px;">
                                                                        {{ $jadwal->hari_terlambat }} hari
                                                                    </span>
                                                                @endif
                                                            </small>
                                                            <hr class="my-1">
                                                            <strong class="text-danger d-block">
                                                                Total Tagihan: Rp
                                                                {{ number_format($jadwal->total_tagihan, 0, ',', '.') }}
                                                            </strong>
                                                        @endif
                                                    </div>

                                                    @if($jadwal->status_bayar == 'Lunas' && $jadwal->tanggal_bayar)
                                                        <div class="text-end">
                                                            <small class="text-muted d-block">Dibayar</small>
                                                            <small class="text-success fw-bold d-block">
                                                                {{ $jadwal->tanggal_bayar->format('d M Y') }}
                                                            </small>
                                                            @if(isset($jadwal->denda_otomatis) && $jadwal->denda_otomatis > 0)
                                                                <small class="text-danger d-block mt-1" style="font-size: 10px;">
                                                                    (+Rp {{ number_format($jadwal->denda_otomatis, 0, ',', '.') }})
                                                                </small>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- ✅ Tampilkan info jika ini angsuran yang pending --}}
                                            @if($pendingForThis)
                                                <div class="mt-2 p-2 bg-warning-subtle rounded">
                                                    <small class="d-block fw-semibold">
                                                        <i class="ti ti-info-circle"></i>
                                                        Kode: {{ $pendingForThis->kode_bayar }}
                                                    </small>
                                                    <small class="text-muted d-block">Dibayar:
                                                        {{ $pendingForThis->tanggal_bayar->format('d M Y H:i') }}</small>
                                                    <small class="text-muted d-block">Jumlah: Rp
                                                        {{ number_format($pendingForThis->jumlah_bayar, 0, ',', '.') }}</small>
                                                </div>
                                            @elseif($has_pending && $jadwal->status_bayar == 'Belum')
                                                {{-- ✅ Tampilkan pesan untuk angsuran lain yang tidak bisa dipilih --}}
                                                <div class="mt-2 p-2 bg-light rounded border">
                                                    <small class="text-muted d-block">
                                                        <i class="ti ti-lock"></i>
                                                        Tidak dapat dibayar karena ada pembayaran lain yang pending
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="ti ti-history me-2"></i>Riwayat Pembayaran</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th class="text-center">Kode</th>
                                    <th>Tanggal</th>
                                    <th>Angsuran Ke</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Denda</th>
                                    <th class="text-end">Total</th>
                                    <th>Status</th>
                                    <th>Bank</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat_bayar as $bayar)
                                    <tr
                                        class="{{ $bayar->isPending() ? 'table-warning' : ($bayar->isRejected() ? 'table-danger' : '') }}">
                                        <td class="text-center">
                                            <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                                {{ $bayar->kode_bayar }}
                                            </span>
                                        </td>
                                        </td>
                                        <td>{{ $bayar->tanggal_bayar->format('d M Y H:i') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">Ke-{{ $bayar->angsuran_ke }}</span>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">
                                            {{ $bayar->denda > 0 ? 'Rp ' . number_format($bayar->denda, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="text-end fw-bold">Rp {{ number_format($bayar->total_bayar, 0, ',', '.') }}
                                        </td>
                                        <td>{!! $bayar->status_badge !!}</td>
                                        <td>
                                            @if($bayar->kas)
                                                @if($bayar->isTransfer())
                                                    {{-- Transfer --}}
                                                    <span class="badge bg-info-subtle text-info">
                                                        <i class="ti ti-credit-card"></i> {{ $bayar->kas->nama_kas }}
                                                    </span>
                                                @else
                                                    {{-- Tunai --}}
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="ti ti-cash"></i> {{ $bayar->kas->nama_kas }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($bayar->isRejected() && $bayar->catatan_verifikasi)
                                        <tr class="table-danger">
                                            <td colspan="8" class="small">
                                                <i class="ti ti-alert-circle"></i>
                                                <strong>Alasan Penolakan:</strong> {{ $bayar->catatan_verifikasi }}
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="ti ti-file-x display-6"></i>
                                            <p class="mb-0 mt-2">Belum ada pembayaran</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Form Pembayaran Transfer -->
        <div class="col-lg-4">
            @if(!$is_lunas && !$has_pending)
                <!-- Form Pembayaran -->
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-light text-white">
                        <h5 class="mb-0"><i class="ti ti-credit-card me-2"></i>Form Pembayaran Transfer</h5>
                    </div>
                    <div class="card-body">
                        <div id="formPlaceholder" class="text-center py-5">
                            <i class="ti ti-click display-4 text-muted mb-3"></i>
                            <p class="text-muted mb-0">Pilih jadwal angsuran yang ingin dibayar</p>
                        </div>

                        <form id="formBayar" class="d-none" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pinjaman_id" value="{{ $pinjaman->id }}">
                            <input type="hidden" name="bayar_angsuran_id" id="bayar_angsuran_id">
                            {{-- ✅ TAMBAHAN: Hidden field untuk denda --}}
                            <input type="hidden" name="denda_terlambat" id="dendaField" value="0">
                            <input type="hidden" name="jumlah_bayar" id="jumlahBayarHidden">

                            <!-- Info Angsuran Terpilih -->
                            <div class="alert alert-info mb-3" id="infoAngsuran">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong id="angsuranInfo">Angsuran Ke-</strong>
                                        <div class="small mt-1">
                                            <i class="ti ti-calendar"></i>
                                            <span id="jatuhTempoInfo"></span>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close" onclick="clearSelection()"></button>
                                </div>
                            </div>

                            <!-- Bank Transfer -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Bank Transfer <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" name="ke_kas_id" id="keKasId" required>
                                    <option value="">-- Pilih Bank --</option>
                                    @foreach($kas_list as $kas)
                                        <option value="{{ $kas->id }}">{{ $kas->nama_kas }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pilih bank tujuan transfer</small>
                            </div>

                            {{-- ✅ BARU: Rincian Tagihan (Readonly) --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Rincian Tagihan</label>
                                <div class="card bg-light border">
                                    <div class="card-body p-3">
                                        {{-- Angsuran Pokok --}}
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Angsuran Pokok:</span>
                                            <strong id="angsuranPokokDisplay">Rp 0</strong>
                                        </div>

                                        {{-- Denda (Hidden by default, show jika ada) --}}
                                        <div id="dendaRow" class="d-flex justify-content-between mb-2"
                                            style="display: none !important;">
                                            <span class="text-danger">
                                                <i class="ti ti-alert-circle"></i> Denda Keterlambatan:
                                            </span>
                                            <strong class="text-danger" id="dendaDisplay">Rp 0</strong>
                                        </div>

                                        {{-- Info Hari Terlambat --}}
                                        <div id="hariTerlambatInfo" class="d-flex justify-content-between mb-2"
                                            style="display: none;">
                                            <span class="text-danger">
                                                <i class="ti ti-clock"></i> Terlambat <span id="hariTerlambatText">0</span> hari
                                            </span>
                                        </div>

                                        <hr class="my-2">

                                        {{-- Total Tagihan --}}
                                        <div class="d-flex justify-content-between">
                                            <strong>Total Tagihan:</strong>
                                            <strong class="text-success fs-5" id="totalTagihanDisplay">Rp 0</strong>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="ti ti-info-circle"></i>
                                    Transfer sesuai total di atas (sudah termasuk denda jika ada)
                                </small>
                            </div>

                            <!-- Bukti Transfer -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Bukti Transfer <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control" name="bukti_transfer" id="buktiTransfer"
                                    accept="image/jpeg,image/png,image/jpg" required>
                                <small class="text-muted">Format: JPG, PNG (Max 2MB)</small>

                                <!-- Preview -->
                                <div id="previewBukti" class="mt-3 d-none">
                                    <img id="imgPreview" src="" alt="Preview" class="img-fluid rounded border"
                                        style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Keterangan (Opsional)</label>
                                <textarea class="form-control" name="keterangan" id="keterangan" rows="2"
                                    placeholder="Catatan tambahan..."></textarea>
                                <small class="text-muted">Max 500 karakter</small>
                            </div>

                            <!-- Buttons -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success" id="btnSubmit">
                                    <i class="ti ti-send"></i> Bayar Sekarang
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                                    <i class="ti ti-x"></i> Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card bg-light border-0 mt-3">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">
                            <i class="ti ti-bulb text-warning me-2"></i>Tips Pembayaran
                        </h6>
                        <ul class="small mb-0 ps-3">
                            <li class="mb-2">Transfer sesuai jumlah yang tertera</li>
                            <li class="mb-2">Upload bukti transfer yang jelas</li>
                            <li class="mb-2">Tunggu verifikasi admin (1-2 hari kerja)</li>
                            <li class="mb-2">Cek status di riwayat pembayaran</li>
                            <li class="mb-2">Hanya bisa bayar 1 angsuran dalam 1 waktu</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Bantuan -->
    <div class="modal fade" id="modalBantuan" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light text-white">
                    <h5 class="modal-title"><i class="ti ti-help"></i> Panduan Pembayaran</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card border-primary">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Cara Bayar Angsuran</h6>
                            <ol class="mb-3">
                                <li class="mb-2">Pilih card angsuran yang ingin dibayar (yang berwarna merah)</li>
                                <li class="mb-2">Isi form pembayaran di sebelah kanan</li>
                                <li class="mb-2">Pilih bank transfer tujuan</li>
                                <li class="mb-2">Masukkan jumlah sesuai angsuran</li>
                                <li class="mb-2">Upload foto bukti transfer (JPG/PNG, max 2MB)</li>
                                <li class="mb-2">Klik "Kirim Pembayaran"</li>
                                <li class="mb-2">Tunggu verifikasi admin (1-2 hari kerja)</li>
                            </ol>

                            <h6 class="fw-bold mb-3 mt-4">Status Pembayaran</h6>
                            <ul class="mb-3">
                                <li class="mb-2">
                                    <span class="badge bg-danger-subtle">Belum Bayar</span>
                                    - Angsuran yang belum dibayar
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-warning">Pending Verifikasi</span>
                                    - Pembayaran sedang diverifikasi admin
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-success">Terverifikasi</span>
                                    - Pembayaran sudah diverifikasi dan diterima
                                </li>
                                <li class="mb-2">
                                    <span class="badge bg-danger">Ditolak</span>
                                    - Pembayaran ditolak admin (lihat alasan penolakan)
                                </li>
                            </ul>

                            <div class="alert alert-warning mt-3">
                                <h6 class="fw-bold mb-2">⚠️ Penting!</h6>
                                <p class="mb-0">Anda hanya bisa melakukan pembayaran 1 angsuran dalam 1 waktu. Tunggu hingga
                                    pembayaran sebelumnya diverifikasi oleh admin sebelum melakukan pembayaran berikutnya.
                                </p>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            let selectedJadwal = null;

            // ✅ PERBAIKAN: Jadwal Card Selection dengan validasi yang lebih ketat
            $('.selectable-jadwal').on('click', function () {
                const canSelect = $(this).data('can-select');

                if (canSelect === 'true' || canSelect === true) {
                    $('.jadwal-card').removeClass('selected');
                    $(this).addClass('selected');

                    selectedJadwal = {
                        id: $(this).data('jadwal-id'),
                        angsuran_ke: $(this).data('angsuran-ke'),
                        jatuh_tempo: $(this).data('jatuh-tempo'),
                        jumlah: parseInt($(this).data('jumlah')),
                        denda: parseInt($(this).data('denda') || 0),
                        total: parseInt($(this).data('total')),
                        is_terlambat: $(this).data('is-terlambat') === 'true',
                        hari_terlambat: parseInt($(this).data('hari-terlambat') || 0)
                    };

                    console.log('✅ Selected:', selectedJadwal);
                    showForm();
                } else {
                    console.log('❌ Cannot select');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tidak Dapat Dipilih',
                        html: '<p>Angsuran ini tidak dapat dipilih karena:</p>' +
                            '<ul class="text-start">' +
                            '<li>Angsuran sudah dibayar/lunas, ATAU</li>' +
                            '<li>Ada pembayaran lain yang sedang menunggu verifikasi admin</li>' +
                            '</ul>' +
                            '<p class="text-muted small mt-2">Silakan tunggu hingga pembayaran sebelumnya diverifikasi.</p>',
                        confirmButtonColor: '#5d87ff'
                    });
                }
            });

            function showForm() {
                if (!selectedJadwal) return;

                $('#formPlaceholder').addClass('d-none');
                $('#formBayar').removeClass('d-none');

                $('#bayar_angsuran_id').val(selectedJadwal.id);
                $('#angsuranInfo').text(`Angsuran Ke-${selectedJadwal.angsuran_ke}`);
                $('#jatuhTempoInfo').text(formatDate(selectedJadwal.jatuh_tempo));

                // ✅ Set angsuran pokok
                const formattedAngsuran = selectedJadwal.jumlah.toLocaleString('id-ID');
                $('#angsuranPokokDisplay').text(`Rp ${formattedAngsuran}`);
                $('#jumlahBayarHidden').val(selectedJadwal.jumlah);

                // ✅ Set denda (jika ada)
                if (selectedJadwal.denda > 0) {
                    const formattedDenda = selectedJadwal.denda.toLocaleString('id-ID');
                    $('#dendaField').val(selectedJadwal.denda);
                    $('#dendaDisplay').text(`Rp ${formattedDenda}`);
                    $('#dendaRow').show();

                    // Tampilkan info hari terlambat
                    if (selectedJadwal.hari_terlambat > 0) {
                        $('#hariTerlambatText').text(selectedJadwal.hari_terlambat);
                        $('#hariTerlambatInfo').show();
                    }
                } else {
                    $('#dendaField').val(0);
                    $('#dendaRow').hide();
                    $('#hariTerlambatInfo').hide();
                }

                // ✅ Set total
                const formattedTotal = selectedJadwal.total.toLocaleString('id-ID');
                $('#totalTagihanDisplay').text(`Rp ${formattedTotal}`);
            }

            // Clear Selection
            window.clearSelection = function () {
                $('.jadwal-card').removeClass('selected');
                selectedJadwal = null;
                $('#formBayar')[0].reset();
                $('#formBayar').addClass('d-none');
                $('#formPlaceholder').removeClass('d-none');
                $('#previewBukti').addClass('d-none');
                $('#dendaRow').hide();
                $('#hariTerlambatInfo').hide();
            };

            // Format Rupiah
            $('#jumlahBayar').on('input', function () {
                // Hapus semua karakter non-digit
                let value = $(this).val().replace(/\D/g, '');

                if (value) {
                    // Convert ke integer untuk mencegah parsing desimal
                    let numValue = parseInt(value, 10);

                    // Format manual dengan titik pemisah ribuan (TANPA desimal)
                    let formatted = numValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    $(this).val(formatted);
                    updateDisplay();
                } else {
                    $(this).val('');
                    $('#displayJumlah').text('Rp 0');
                }
            });

            function updateDisplay() {
                // Hapus semua titik untuk mendapatkan nilai asli
                const jumlahStr = $('#jumlahBayar').val().replace(/\./g, '');
                const jumlah = parseInt(jumlahStr, 10) || 0;

                // Format manual (TANPA desimal)
                const formatted = jumlah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                $('#displayJumlah').text('Rp ' + formatted);
            }

            // Preview Bukti
            $('#buktiTransfer').on('change', function () {
                const file = this.files[0];
                if (file) {
                    // Validate size
                    if (file.size > 2048000) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File Terlalu Besar',
                            text: 'Ukuran file maksimal 2MB',
                            confirmButtonColor: '#dc3545'
                        });
                        $(this).val('');
                        $('#previewBukti').addClass('d-none');
                        return;
                    }

                    // Preview
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#imgPreview').attr('src', e.target.result);
                        $('#previewBukti').removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Submit Form
            // Submit Form
            $('#formBayar').on('submit', function (e) {
                e.preventDefault();

                if (!selectedJadwal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: 'Pilih jadwal angsuran terlebih dahulu',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                // ✅ HANYA CEK BANK & BUKTI TRANSFER
                if (!$('#keKasId').val()) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Bank Belum Dipilih',
                        text: 'Silakan pilih bank tujuan transfer',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                if (!$('#buktiTransfer')[0].files[0]) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Bukti Transfer Wajib',
                        text: 'Silakan upload bukti transfer',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }

                // Konfirmasi
                Swal.fire({
                    // ... kode konfirmasi tetap sama
                    title: 'Konfirmasi Pembayaran',
                    html: `
                                            <div class="text-start">
                                                <p class="mb-2"><strong>Detail Pembayaran:</strong></p>
                                                <table class="table table-sm table-bordered">
                                                    <tr>
                                                        <td><i class="ti ti-calendar"></i> Angsuran:</td>
                                                        <td class="text-end"><strong>Ke-${selectedJadwal.angsuran_ke}</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td><i class="ti ti-cash"></i> Angsuran Pokok:</td>
                                                        <td class="text-end">Rp ${selectedJadwal.jumlah.toLocaleString('id-ID')}</td>
                                                    </tr>
                                                    ${selectedJadwal.denda > 0 ? `
                                                    <tr>
                                                        <td><i class="ti ti-alert-circle"></i> Denda (${selectedJadwal.hari_terlambat} hari):</td>
                                                        <td class="text-end text-danger">Rp ${selectedJadwal.denda.toLocaleString('id-ID')}</td>
                                                    </tr>
                                                    ` : ''}
                                                    <tr class="table-success fw-bold">
                                                        <td>Total Tagihan:</td>
                                                        <td class="text-end">Rp ${selectedJadwal.total.toLocaleString('id-ID')}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><i class="ti ti-building-bank"></i> Bank:</td>
                                                        <td class="text-end">${$('#keKasId option:selected').text()}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-send"></i> Ya, Bayar',
                    cancelButtonText: '<i class="ti ti-x"></i> Batal',
                    confirmButtonColor: '#13a460',
                    width: '500px'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processPayment();
                    }
                });

                function processPayment() {
                    Swal.fire({
                        title: 'Mengirim Pembayaran...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // ✅ LANGSUNG PAKAI FormData DARI FORM (udah ada semua hidden field-nya)
                    const formData = new FormData($('#formBayar')[0]);

                    $.ajax({
                        url: '{{ route("user.bayar.bayar") }}',
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            // ... kode success tetap sama
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                html: `
                                            <div class="text-start">
                                                <div class="alert alert-success">
                                                    <p class="mb-2"><strong>Kode Pembayaran:</strong></p>
                                                    <h4 class="mb-0 text-primary">${response.kode_bayar}</h4>
                                                </div>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>Angsuran Pokok:</td>
                                                        <td class="text-end">Rp ${selectedJadwal.jumlah.toLocaleString('id-ID')}</td>
                                                    </tr>
                                                    ${selectedJadwal.denda > 0 ? `
                                                    <tr>
                                                        <td>Denda:</td>
                                                        <td class="text-end text-danger">Rp ${selectedJadwal.denda.toLocaleString('id-ID')}</td>
                                                    </tr>
                                                    ` : ''}
                                                    <tr class="fw-bold border-top">
                                                        <td>Total Dibayar:</td>
                                                        <td class="text-end text-success">Rp ${selectedJadwal.total.toLocaleString('id-ID')}</td>
                                                    </tr>
                                                </table>
                                                <p class="mb-0 text-muted">
                                                    <i class="ti ti-info-circle"></i>
                                                    Pembayaran Anda sedang diverifikasi. Cek status di riwayat pembayaran.
                                                </p>
                                            </div>
                                        `,
                                confirmButtonColor: '#13a460',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function (xhr) {
                            const message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });

            //  Helper - PINDAH KE SINI (di luar semua event handler)
            function formatDate(dateString) {
                const date = new Date(dateString);
                const options = { day: '2-digit', month: 'short', year: 'numeric' };
                return date.toLocaleDateString('id-ID', options);
            }

            //  Show Help - PINDAH KE SINI (di luar semua event handler)
            window.showHelp = function () {
                $('#modalBantuan').modal('show');
            };
        });  // ← TUTUP $(document).ready
    </script>
@endpush