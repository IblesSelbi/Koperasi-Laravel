@extends('layouts.app')

@section('title', 'Beranda Member')

@section('content')
<!-- Page Header -->
<div class="row mb-3">
    <div class="col-12">
        <h4 class="fw-semibold mb-1">Laporan Data Kas Anggota</h4>
        <p class="text-muted fs-3 mb-0">Informasi lengkap simpanan dan pinjaman Anda</p>
    </div>
</div>

<!-- Data Anggota Card -->
<div class="card mb-3 shadow-sm border-0">
    <div class="card-header bg-light border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-dark small">
            <i class="ti ti-id me-1"></i> Identitas Anggota
        </h6>
    </div>

    <div class="card-body pt-4 pb-4">
        <div class="row g-4 align-items-start">
            <!-- Foto -->
            <div class="col-md-3 text-center">
                <img src="{{ asset('assets/images/profile/user-2.jpg') }}" class="rounded-3 shadow-sm mb-2"
                    width="100" height="120" alt="Foto Anggota">
                <div class="fw-semibold text-dark small">{{ $anggota['nama'] }}</div>
            </div>

            <!-- Data Kiri -->
            <div class="col-md-4">
                <h6 class="fw-semibold text-success border-bottom border-success pb-2 mb-3 small">
                    <i class="ti ti-user-circle me-1"></i> Data Utama
                </h6>
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted" width="40%">ID Anggota</td>
                        <td width="5%">:</td>
                        <td class="fw-semibold">{{ $anggota['id_anggota'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td>:</td>
                        <td class="fw-semibold">{{ $anggota['nama'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Gender</td>
                        <td>:</td>
                        <td>{{ $anggota['gender'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jabatan</td>
                        <td>:</td>
                        <td>{{ $anggota['jabatan'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Data Kanan -->
            <div class="col-md-5">
                <h6 class="fw-semibold text-primary border-bottom border-primary pb-2 mb-3 small">
                    <i class="ti ti-map-pin me-1"></i> Kontak
                </h6>
                <table class="table table-sm table-borderless mb-0 small">
                    <tr>
                        <td class="text-muted" width="40%">Alamat</td>
                        <td width="5%">:</td>
                        <td>{{ $anggota['alamat'] }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. Telp</td>
                        <td>:</td>
                        <td>{{ $anggota['no_telp'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pengajuan Pinjaman Mutakhir -->
@if($pengajuan_terakhir)
<div class="alert alert-info shadow-sm d-flex align-items-start mb-3" role="alert">
    <i class="ti ti-info-circle fs-5 me-3 mt-1"></i>
    <div class="flex-grow-1">
        <strong class="d-block mb-1">Pengajuan Pinjaman Mutakhir</strong>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <span>{{ $pengajuan_terakhir['tanggal'] }}</span>
            <span class="text-dark">Nominal: <strong>Rp {{ number_format($pengajuan_terakhir['nominal'], 0, ',', '.') }}</strong></span>
            <span class="badge rounded-pill bg-success-subtle text-success px-2 py-1 d-inline-flex shadow-sm align-items-center gap-1">
                <i class="ti ti-circle-check fs-5"></i>
                <span class="fw-bold">{{ $pengajuan_terakhir['status'] }}</span>
                <span class="text-muted fw-semibold">• {{ $pengajuan_terakhir['keterangan'] }}</span>
            </span>
        </div>
    </div>
</div>
@endif

<div class="row g-3">
    <!-- Saldo Simpanan -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 border-0 rounded-3">
            <div class="card- bg-white border-0 px-3 pt-3 pb-2">
                <div class="d-flex align-items-center">
                    <div class="rounded-2 d-flex align-items-center justify-content-center bg-success-subtle"
                        style="width:38px;height:38px;">
                        <i class="ti ti-wallet text-success fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-semibold ms-2 text-dark small">Saldo Simpanan</h6>
                </div>
            </div>

            <div class="card-body px-3 pt-2 pb-3">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Simpanan Sukarela</span>
                    <span class="fw-semibold small text-dark">Rp {{ number_format($simpanan['sukarela'], 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Simpanan Pokok</span>
                    <span class="fw-semibold small text-dark">Rp {{ number_format($simpanan['pokok'], 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Simpanan Wajib</span>
                    <span class="fw-semibold small text-dark">Rp {{ number_format($simpanan['wajib'], 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Lainnya</span>
                    <span class="fw-semibold small text-dark">Rp {{ number_format($simpanan['lainnya'], 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 mt-2 border-top border-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="small text-dark">Total Simpanan</strong>
                        <strong class="fs-5 text-success">Rp {{ number_format($simpanan['total'], 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tagihan Kredit -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 border-0 rounded-3">
            <div class="card-header bg-white border-0 px-3 pt-3 pb-2">
                <div class="d-flex align-items-center">
                    <div class="rounded-2 d-flex align-items-center justify-content-center bg-warning-subtle"
                        style="width:38px;height:38px;">
                        <i class="ti ti-cash text-warning fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-semibold ms-2 text-dark small">Tagihan Kredit</h6>
                </div>
            </div>

            <div class="card-body px-3 pt-2 pb-3">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Pokok Pinjaman</span>
                    <span class="fw-semibold small text-dark">Rp {{ number_format($pinjaman['pokok'], 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Tagihan + Denda</span>
                    <span class="fw-semibold small text-dark">Rp {{ number_format($pinjaman['tagihan_total'], 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <span class="text-muted small">Dibayar</span>
                    <span class="fw-semibold small text-success">Rp {{ number_format($pinjaman['dibayar'], 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 mt-2 border-top border-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <strong class="small text-dark">Sisa Tagihan</strong>
                        <strong class="fs-5 text-danger">Rp {{ number_format($pinjaman['sisa_tagihan'], 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keterangan -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100 border-0 rounded-3">
            <div class="card-header bg-white border-0 px-3 pt-3 pb-2">
                <div class="d-flex align-items-center">
                    <div class="rounded-2 d-flex align-items-center justify-content-center bg-primary-subtle"
                        style="width:38px;height:38px;">
                        <i class="ti ti-clipboard-text text-primary fs-5"></i>
                    </div>
                    <h6 class="mb-0 fw-semibold ms-2 text-dark">Keterangan</h6>
                </div>
            </div>

            <div class="card-body px-3 pt-2 pb-3">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Jumlah Pinjaman</span>
                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3">{{ $keterangan['jumlah_pinjaman'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Pinjaman Lunas</span>
                    <span class="badge rounded-pill bg-success-subtle text-success px-3">{{ $keterangan['pinjaman_lunas'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="text-muted small">Status Pembayaran</span>
                    <span class="badge rounded-pill bg-{{ $keterangan['status_color'] }}-subtle text-{{ $keterangan['status_color'] }} px-3">
                        {{ $keterangan['status_pembayaran'] }}
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center text-muted fw-semibold py-2">
                    <span class="text-dark fw-semibold small">Tanggal Tempo</span>
                    {{ $keterangan['tanggal_tempo'] }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection