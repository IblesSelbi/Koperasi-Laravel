@extends('layouts.app')

@section('title', 'Detail Pinjaman')

@push('styles')
    <style>
        .table-borderless td {
            padding: 0.35rem 0.5rem;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="fw-semibold mb-1">Detail Pinjaman</h4>
                    <p class="text-muted fs-3 mb-0">Kode Pinjaman: <strong
                            class="text-primary">{{ $pinjaman->kode }}</strong></p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('pinjaman.pinjaman') }}" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                    <button class="btn btn-success" onclick="cetakDetailTransaksi({{ $pinjaman->id }})">
                        <i class="ti ti-printer"></i> Cetak Detail
                    </button>
                    <a href="{{ route('pinjaman.bayar.show', $pinjaman->id) }}" class="btn btn-primary">
                        <i class="ti ti-wallet"></i> Bayar Angsuran
                    </a>
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

    <!-- Detail Pinjaman Card -->
    <div class="card mb-3 shadow-sm info-card">
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
                                    <td><strong>{{ $pinjaman->tanggal_pinjam->translatedFormat('d F Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Tanggal Tempo</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->tanggal_tempo->translatedFormat('d F Y') }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Lama Pinjaman</td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $pinjaman->lama_pinjaman }}
                                            Bulan</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Jenis Pinjaman</td>
                                    <td>:</td>
                                    <td><strong>{{ $pinjaman->jenis_pinjaman }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dari Kas</td>
                                    <td>:</td>
                                    <td>{{ $pinjaman->kas->nama_kas ?? '-' }}</td>
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
                                    <td class="text-muted">Bunga ({{ $pinjaman->bunga_persen }}%)</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong
                                            class="text-info">{{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Biaya Admin</td>
                                    <td>:</td>
                                    <td class="text-end">
                                        <strong
                                            class="text-warning">{{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</strong>
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

                <!-- Keterangan -->
                @if($pinjaman->keterangan)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <strong><i class="ti ti-note me-2"></i>Keterangan:</strong>
                                <p class="mb-0 mt-2">{{ $pinjaman->keterangan }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Status Pembayaran -->
            <div class="card-footer bg-light">
                <div class="row text-center">
                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1"><i class="ti ti-calendar-stats"></i> Sisa Angsuran</p>
                        <span class="fw-semibold fs-5 text-primary">{{ $pinjaman->sisa_angsuran }} Bulan</span>
                    </div>
                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1"><i class="ti ti-coins"></i> Dibayar</p>
                        <span class="fw-semibold fs-5 text-success">Rp
                            {{ number_format($pinjaman->sudah_dibayar, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1"><i class="ti ti-alert-triangle"></i> Denda</p>
                        <span class="fw-semibold fs-5 text-danger">Rp
                            {{ number_format($pinjaman->jumlah_denda, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-md-3 col-6 mb-3 mb-md-0">
                        <p class="text-muted mb-1"><i class="ti ti-cash"></i> Sisa Tagihan</p>
                        <span class="fw-semibold text-warning fs-5">Rp
                            {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-md-3 col-12">
                        <p class="text-muted mb-1"><i class="ti ti-check-circle"></i> Status Pelunasan</p>
                        @if($pinjaman->status_lunas == 'Lunas')
                            <span class="badge bg-success px-3 py-2">
                                <i class="ti ti-check"></i> Lunas
                            </span>
                        @else
                            <span class="badge bg-danger shadow-sm px-2 py-2">
                                <i class="ti ti-x"></i> Belum Lunas
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulasi Tagihan -->
    <div class="card mb-3 shadow-sm info-card">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold text-success">
                <i class="ti ti-calendar-stats me-2"></i>Simulasi Jadwal Angsuran
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center">Bln Ke</th>
                            <th class="text-end">Angsuran Pokok</th>
                            <th class="text-end">Angsuran Bunga</th>
                            <th class="text-end">Biaya Admin</th>
                            <th class="text-end">Jumlah Angsuran</th>
                            <th class="text-center">Tanggal Tempo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($simulasi as $index => $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $item->bulan_ke }}</span>
                                </td>
                                <td class="text-end">Rp {{ number_format($item->angsuran_pokok, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->angsuran_bunga, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    @if($item->biaya_admin > 0)
                                        <span class="text-warning fw-bold">Rp
                                            {{ number_format($item->biaya_admin, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp
                                        {{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <small>{{ $item->tanggal_tempo }}</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th class="text-center">Total</th>
                            <th class="text-end">Rp {{ number_format($simulasi->sum('angsuran_pokok'), 0, ',', '.') }}</th>
                            <th class="text-end">Rp {{ number_format($simulasi->sum('angsuran_bunga'), 0, ',', '.') }}</th>
                            <th class="text-end">Rp {{ number_format($simulasi->sum('biaya_admin'), 0, ',', '.') }}</th>
                            <th class="text-end text-success fw-semibold fs-4">Rp
                                {{ number_format($simulasi->sum('jumlah_angsuran'), 0, ',', '.') }}
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Transaksi Pembayaran -->
    <div class="card mb-3 shadow-sm info-card">
        <div class="card-header bg-light border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-success">
                    <i class="ti ti-receipt me-2"></i>Riwayat Transaksi Pembayaran
                </h6>
                <span class="badge bg-info-subtle text-info shadow-sm border-1">
                    {{ $transaksi->count() }} Transaksi
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($transaksi->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Kode Bayar</th>
                                <th class="text-center">Tanggal Bayar</th>
                                <th class="text-center">Angsuran Ke</th>
                                <th class="text-center">Ke Kas</th>
                                <th class="text-end">Jumlah Bayar</th>
                                <th class="text-end">Denda</th>
                                <th class="text-center">User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaksi as $index => $item)
                                <tr>
                                    <td class="text-center">{{ $item->no }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info fw-semibold">
                                            {{ $item->kode_bayar }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <small>{{ $item->tanggal_bayar }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->angsuran_ke }}</span>
                                    </td>
                                    <td class="text-center">{{ $item->jenis_pembayaran }}</td>
                                    <td class="text-end">
                                        <strong class="text-success">Rp
                                            {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end">
                                        @if($item->denda > 0)
                                            <span class="text-danger fw-bold">Rp {{ number_format($item->denda, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge border border-secondary text-secondary">
                                            {{ $item->user }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th class="text-center" colspan="5">Total Pembayaran</th>
                                <th class="text-end text-success fw-semibold fs-4">
                                    Rp {{ number_format($transaksi->sum('jumlah_bayar'), 0, ',', '.') }}
                                </th>
                                <th class="text-end text-danger fw-semibold fs-4">
                                    Rp {{ number_format($transaksi->sum('denda'), 0, ',', '.') }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="ti ti-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3 mb-0">Belum ada transaksi pembayaran</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Function: Cetak Detail
        function cetakDetailTransaksi(id) {
            const url = `{{ url('admin/pinjaman/cetak-detail') }}/${id}`;
            window.open(url, '_blank', 'width=900,height=700');
        }

    </script>
@endpush