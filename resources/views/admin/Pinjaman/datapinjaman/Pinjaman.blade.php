@extends('layouts.app')

@section('title', 'Data Pinjaman Anggota')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <!-- Daterangepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        #tabelPinjaman tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px #dfe2e5 !important;
            color: #777e89 !important;
        }

        #tabelPinjaman tbody tr.selected>* strong,
        #tabelPinjaman tbody tr.selected>* .text-muted,
        #tabelPinjaman tbody tr.selected>* .text-info,
        #tabelPinjaman tbody tr.selected>* .text-warning,
        #tabelPinjaman tbody tr.selected>* .text-success {
            color: inherit !important;
        }

        #tabelPinjaman tbody tr:hover {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Data Pinjaman Anggota</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data pinjaman anggota koperasi</p>
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
            <h6 class="mb-0 fw-semibold"><i class="ti ti-filter me-2"></i>Filter & Pencarian Data</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <!-- Filter Tanggal -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-calendar text-primary"></i> Rentang Tanggal
                    </label>
                    <input type="text" class="form-control" id="filterTanggal" placeholder="Pilih tanggal..." readonly>
                </div>

                <!-- Filter Status -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-list-check text-success"></i> Status Lunas
                    </label>
                    <select class="form-select" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="Belum">Belum Lunas</option>
                        <option value="Lunas">Sudah Lunas</option>
                    </select>
                </div>

                <!-- Kode Transaksi -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-barcode text-info"></i> Kode Transaksi
                    </label>
                    <input type="text" class="form-control" id="filterKode" placeholder="Masukkan kode...">
                </div>

                <!-- Nama Anggota -->
                <div class="col-md-6 col-lg-2">
                    <label class="form-label fw-semibold mb-2">
                        <i class="ti ti-user text-warning"></i> Nama Anggota
                    </label>
                    <input type="text" class="form-control" id="filterNama" placeholder="Nama anggota...">
                </div>

                <!-- Action Buttons -->
                <div class="col-md-12 col-lg-2">
                    <label class="form-label fw-semibold mb-2 d-none d-lg-block">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary w-100" onclick="filterData()">
                            <i class="ti ti-search"></i> Cari
                        </button>
                        <button class="btn btn-outline-secondary" onclick="resetFilter()" data-bs-toggle="tooltip"
                            title="Reset Filter">
                            <i class="ti ti-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Secondary Actions -->
            <div class="row mt-3 pt-3 border-top">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button class="btn btn-success btn-sm" onclick="tambahData()">
                            <i class="ti ti-plus"></i> Proses Pinjaman
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="editData()">
                            <i class="ti ti-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="hapusData()">
                            <i class="ti ti-trash"></i> Hapus
                        </button>
                        <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                            <i class="ti ti-printer"></i> Cetak Laporan
                        </button>
                        <button class="btn btn-secondary btn-sm" onclick="lihatRiwayatHapus()">
                            <i class="ti ti-history"></i> Riwayat Hapus
                        </button>
                        <div class="ms-auto">
                            <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                <i class="ti ti-file-text"></i> Total Data: <strong
                                    id="totalData">{{ $pinjaman->count() }}</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelPinjaman" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Kode</th>
                            <th class="text-center align-middle">Tanggal<br>Pinjam</th>
                            <th class="align-middle">Nama Anggota</th>
                            <th width="195px" class="align-middle">Hitungan</th>
                            <th width="195px" class="align-middle">Total Tagihan</th>
                            <th class="text-center align-middle">Status<br>Lunas</th>
                            <th class="text-center align-middle">User</th>
                            <th class="text-center align-middle">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pinjaman as $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode_pinjaman }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ $item->tanggal_pinjam->translatedFormat('d F Y') }}<br>
                                    <small>{{ $item->tanggal_pinjam->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $photoPath = 'assets/images/profile/user-1.jpg';

                                            // Priority 1: data_anggota.photo (bukan default)
                                            if ($item->anggota->photo && $item->anggota->photo !== 'assets/images/profile/user-1.jpg') {
                                                $photoPath = 'storage/' . $item->anggota->photo;
                                            }
                                            // Priority 2: users.profile_image
                                            elseif ($item->anggota->user && $item->anggota->user->profile_image) {
                                                $photoPath = 'storage/' . $item->anggota->user->profile_image;
                                            }
                                        @endphp

                                        <img src="{{ asset($photoPath) }}" width="40" height="40" class="rounded-circle me-2"
                                            alt="Foto" onerror="this.src='{{ asset('assets/images/profile/user-1.jpg') }}'">
                                        <div>
                                            <strong>{{ $item->anggota->nama }}</strong><br>
                                            <small class="text-muted">ID: {{ $item->anggota->id_anggota }} •
                                                {{ $item->anggota->kota }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Jenis Pinjaman</small>
                                            <strong>{{ $item->jenis_pinjaman }}</strong>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Pokok Pinjaman</small>
                                            <span>Rp {{ number_format($item->pokok_pinjaman, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Lama Angsuran</small>
                                            <span>{{ $item->lamaAngsuran->lama_angsuran }} Bulan</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Angsuran Pokok</small>
                                            <span>Rp {{ number_format($item->angsuran_pokok, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Bunga ({{ $item->bunga_persen }}%)</small>
                                            <span class="text-info">Rp
                                                {{ number_format($item->biaya_bunga * $item->lamaAngsuran->lama_angsuran, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between pt-1">
                                            <small class="text-muted">Biaya Admin</small>
                                            <span class="text-warning">Rp
                                                {{ number_format($item->biaya_admin, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Total Angsuran</small>
                                            <span>Rp {{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Sudah Dibayar</small>
                                            <span>Rp {{ number_format($item->total_bayar ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Angsuran/Bulan</small>
                                            <span>Rp
                                                {{ number_format($item->angsuran_pokok + $item->biaya_bunga, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between border-bottom py-1">
                                            <small class="text-muted">Sisa Angsuran</small>
                                            <span>{{ $item->sisa_angsuran }}x</span>
                                        </div>
                                        <div class="d-flex justify-content-between pt-2">
                                            <small class="text-muted fw-semibold">Sisa Tagihan</small>
                                            <span class="fw-semibold text-success fs-4">Rp
                                                {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($item->status_lunas == 'Lunas')
                                        <span class="badge bg-success-subtle text-success shadow-sm px-3 py-2">
                                            <i class="ti ti-check"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle shadow-sm text-danger shadow-sm px-2 py-2">
                                            <i class="ti ti-x"></i> Belum Lunas
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary px-3 py-1">
                                        {{ $item->user->name }}
                                    </span>
                                </td>
                                <td class="text-center" style="min-width: 150px;">
                                    <div class="btn-group-vertical" role="group">
                                        <a href="{{ route('pinjaman.pinjaman.detail', $item->id) }}"
                                            class="btn btn-outline-info btn-sm">
                                            <i class="ti ti-eye"></i> Detail
                                        </a>
                                        <button class="btn btn-primary btn-sm" onclick="cetakNota({{ $item->id }})">
                                            <i class="ti ti-printer"></i> Nota
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah Pinjaman (Dari Pengajuan) -->
    <div class="modal fade" id="modalFormPinjaman" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light text-white">
                    <h5 class="modal-title">
                        <i class="ti ti-plus"></i> Proses Pinjaman dari Pengajuan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPinjaman">
                        @csrf
                        <input type="hidden" id="pengajuanId" name="pengajuan_id">

                        <!-- Step 1: Pilih Pengajuan -->
                        <div id="step1">
                            <div class="alert alert-info">
                                <i class="ti ti-info-circle me-2"></i>
                                <strong>Info:</strong> Pilih pengajuan yang sudah disetujui untuk diproses menjadi pinjaman
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Pengajuan yang Disetujui <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="selectPengajuan" required>
                                    <option value="">-- Pilih Pengajuan --</option>
                                </select>
                                <small class="text-muted">Hanya pengajuan dengan status "Disetujui" yang ditampilkan</small>
                            </div>

                            <div id="loadingPengajuan" class="text-center py-4" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Memuat data pengajuan...</p>
                            </div>
                        </div>

                        <!-- Step 2: Detail Pengajuan & Form Pinjaman -->
                        <div id="step2" style="display: none;">
                            <div class="row">
                                <!-- Kolom Kiri: Info Pengajuan -->
                                <div class="col-md-6">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="ti ti-file-text me-2"></i>Informasi Pengajuan</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Kode Pengajuan</strong></td>
                                                    <td>: <span id="infoPengajuanKode">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal Pengajuan</strong></td>
                                                    <td>: <span id="infoPengajuanTanggal">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Nama Anggota</strong></td>
                                                    <td>: <span id="infoPengajuanNama">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>ID Anggota</strong></td>
                                                    <td>: <span id="infoPengajuanIdAnggota">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jenis Pinjaman</strong></td>
                                                    <td>: <span id="infoPengajuanJenis">-</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jumlah Pinjaman</strong></td>
                                                    <td>: <strong class="text-primary" id="infoPengajuanJumlah">-</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Lama Angsuran</strong></td>
                                                    <td>: <span id="infoPengajuanLama">-</span></td>
                                                </tr>
                                            </table>

                                            <!-- Foto Anggota -->
                                            <div class="text-center mt-3 pt-3 border-top">
                                                <label class="form-label fw-semibold d-block mb-2">Foto Anggota</label>
                                                <div id="anggotaPhoto" class="border rounded p-2 mx-auto"
                                                    style="height: 200px; width: 150px;">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                        <i class="ti ti-user" style="font-size: 64px;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kolom Kanan: Proyeksi & Form -->
                                <div class="col-md-6">
                                    <!-- Proyeksi Angsuran -->
                                    <div class="card border border-success mb-3">
                                        <div class="card-header bg-success-subtle">
                                            <h6 class="mb-0 text-success"><i class="ti ti-calculator me-2"></i>Proyeksi
                                                Angsuran</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Pokok Pinjaman</strong></td>
                                                    <td class="text-end"><span id="proyeksiPokok">Rp 0</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Bunga (<span
                                                                id="proyeksiPersen">{{ $sukuBunga->bg_pinjam }}%</span>)</strong>
                                                    </td>
                                                    <td class="text-end"><span id="proyeksiBunga">Rp 0</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Biaya Admin</strong></td>
                                                    <td class="text-end"><span id="proyeksiAdmin">Rp 0</span></td>
                                                </tr>
                                                <tr class="border-top">
                                                    <td><strong>Total Angsuran</strong></td>
                                                    <td class="text-end"><strong class="text-success" id="proyeksiTotal">Rp
                                                            0</strong></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Angsuran/Bulan</strong></td>
                                                    <td class="text-end"><strong class="text-primary"
                                                            id="proyeksiPerBulan">Rp 0</strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Form Input -->
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="ti ti-edit me-2"></i>Form Pinjaman</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Tanggal Pinjam <span
                                                        class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" id="tglPinjam"
                                                    name="tanggal_pinjam" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Ambil Dari Kas <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="kasId" name="dari_kas_id" required>
                                                    <option value="">-- Pilih Kas --</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Keterangan</label>
                                                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"
                                                    placeholder="Tambahkan keterangan (opsional)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Kembali -->
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-secondary" onclick="backToStep1()">
                                    <i class="ti ti-arrow-left"></i> Kembali ke Pilih Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="simpanPinjaman()"
                        style="display: none;">
                        <i class="ti ti-check"></i> Proses Pinjaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Pinjaman -->
    <div class="modal fade" id="modalEditPinjaman" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-2">
                <div class="modal-header bg-light text-white">
                    <h5 class="modal-title">
                        <i class="ti ti-edit"></i> Edit Data Pinjaman
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditPinjaman">
                        @csrf
                        <input type="hidden" id="editPinjamanId" name="id">

                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            Hanya beberapa field yang dapat diedit setelah pinjaman dibuat
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="editTglPinjam" name="tanggal_pinjam"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lama Angsuran <span class="text-danger">*</span></label>
                            <select class="form-select" id="editLamaAngsuran" name="lama_angsuran_id" required>
                                <option value="">-- Pilih Lama Angsuran --</option>
                            </select>
                            <small class="text-muted">Perubahan lama angsuran akan menghitung ulang angsuran</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dari Kas <span class="text-danger">*</span></label>
                            <select class="form-select" id="editKasId" name="dari_kas_id" required>
                                <option value="">-- Pilih Kas --</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea class="form-control" id="editKeterangan" name="keterangan" rows="3"></textarea>
                        </div>

                        <!-- Preview Perhitungan Ulang -->
                        <div id="editProyeksiContainer" class="card border border-info mb-3" style="display: none;">
                            <div class="card-header bg-info-subtle">
                                <h6 class="mb-0 text-info"><i class="ti ti-calculator me-2"></i>Perhitungan Baru</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tr>
                                        <td><strong>Angsuran Pokok</strong></td>
                                        <td class="text-end"><span id="editProyeksiPokok">Rp 0</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Biaya Bunga (<span id="editProyeksiPersen">0%</span>)</strong></td>
                                        <td class="text-end"><span id="editProyeksiBunga">Rp 0</span></td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong>Total Angsuran</strong></td>
                                        <td class="text-end"><strong class="text-success" id="editProyeksiTotal">Rp
                                                0</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Angsuran/Bulan</strong></td>
                                        <td class="text-end"><strong class="text-primary" id="editProyeksiPerBulan">Rp
                                                0</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x"></i> Batal
                    </button>
                    <button type="button" class="btn btn-warning" onclick="updatePinjaman()">
                        <i class="ti ti-check"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables -->
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.bootstrap5.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/sweetalert/sweetalert2.min.js') }}"></script>

    <!-- Moment.js & Daterangepicker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        let table;
        let selectedRow = null;

        $(document).ready(function () {
            // Hide table initially
            const tableWrapper = $('#tabelPinjaman').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            // Initialize DataTable
            table = $('#tabelPinjaman').DataTable({
                language: {
                    "sProcessing": "Sedang memproses...",
                    "sLengthMenu": "Tampilkan _MENU_ entri",
                    "sZeroRecords": "Tidak ditemukan data yang sesuai",
                    "sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix": "",
                    "sSearch": "Cari:",
                    "sUrl": "",
                    "oPaginate": {
                        "sFirst": "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext": "Selanjutnya",
                        "sLast": "Terakhir"
                    }
                },
                pageLength: 10,
                order: [[1, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [7] }
                ],
                initComplete: function () {
                    tableWrapper.css('opacity', 1);
                }
            });

            // Initialize Daterangepicker
            $('#filterTanggal').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Batal',
                    applyLabel: 'Terapkan',
                    format: 'DD/MM/YYYY',
                    daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                }
            });

            $('#filterTanggal').on('apply.daterangepicker', function (ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('#filterTanggal').on('cancel.daterangepicker', function (ev, picker) {
                $(this).val('');
            });

            // Initialize Bootstrap Tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Table row selection
            $('#tabelPinjaman tbody').on('click', 'tr', function (e) {
                // Jangan select jika klik tombol
                if ($(e.target).closest('button, a').length) {
                    return;
                }

                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                    selectedRow = null;
                } else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                    selectedRow = $(this).data('id');
                }
            });
        });

        // Load pengajuan yang disetujui saat modal dibuka
        $('#modalFormPinjaman').on('show.bs.modal', function () {
            loadPengajuanDisetujui();
            loadKasList();

            // Set default datetime
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            $('#tglPinjam').val(`${year}-${month}-${day}T${hours}:${minutes}`);
        });

        // Load data pengajuan yang sudah disetujui
        function loadPengajuanDisetujui() {
            $('#loadingPengajuan').show();
            $('#selectPengajuan').prop('disabled', true);

            $.ajax({
                url: '{{ route("pinjaman.pinjaman.pengajuan-disetujui") }}',
                type: 'GET',
                success: function (response) {
                    $('#loadingPengajuan').hide();
                    $('#selectPengajuan').prop('disabled', false);

                    const select = $('#selectPengajuan');
                    select.html('<option value="">-- Pilih Pengajuan --</option>');

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (item) {
                            const option = `<option value="${item.id}">
                                                                    ${item.kode_pengajuan} - ${item.anggota.nama} - Rp ${formatRupiah(item.jumlah)} - ${item.lama_angsuran.lama_angsuran} Bulan
                                                                </option>`;
                            select.append(option);
                        });
                    } else {
                        select.html('<option value="">Tidak ada pengajuan yang disetujui</option>');
                    }
                },
                error: function () {
                    $('#loadingPengajuan').hide();
                    $('#selectPengajuan').prop('disabled', false);
                    Swal.fire('Error', 'Gagal memuat data pengajuan', 'error');
                }
            });
        }

        // Load list kas untuk dropdown
        function loadKasList() {
            $.ajax({
                url: '{{ route("pinjaman.pinjaman.kas-list") }}',
                type: 'GET',
                success: function (response) {
                    const kasSelect = $('#kasId, #editKasId');
                    kasSelect.html('<option value="">-- Pilih Kas --</option>');

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (kas) {
                            kasSelect.append(`<option value="${kas.id}">${kas.nama_kas}</option>`);
                        });
                    }
                },
                error: function (xhr) {
                    console.error('Error loading kas list:', xhr);
                    const kasSelect = $('#kasId, #editKasId');
                    kasSelect.html(`
                                                            <option value="">-- Pilih Kas --</option>
                                                            <option value="1">Kas Tunai</option>
                                                            <option value="2">Kas Besar</option>
                                                        `);
                }
            });
        }

        // Event ketika pengajuan dipilih
        $('#selectPengajuan').on('change', function () {
            const pengajuanId = $(this).val();

            if (!pengajuanId) {
                backToStep1();
                return;
            }

            loadDetailPengajuan(pengajuanId);
        });

        // Load detail pengajuan dan proyeksi
        function loadDetailPengajuan(pengajuanId) {
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `{{ url("admin/pinjaman/pengajuan-detail") }}/${pengajuanId}`,
                type: 'GET',
                success: function (response) {
                    Swal.close();

                    const pengajuan = response.data.pengajuan;
                    const proyeksi = response.data.proyeksi;

                    // Set pengajuan ID
                    $('#pengajuanId').val(pengajuan.id);

                    // Tampilkan info pengajuan
                    $('#infoPengajuanKode').text(pengajuan.kode_pengajuan);
                    $('#infoPengajuanTanggal').text(formatTanggal(pengajuan.tanggal_pengajuan));
                    $('#infoPengajuanNama').text(pengajuan.anggota.nama);
                    $('#infoPengajuanIdAnggota').text(pengajuan.anggota.id_anggota);
                    $('#infoPengajuanJenis').text(pengajuan.jenis_pinjaman);
                    $('#infoPengajuanJumlah').text('Rp ' + formatRupiah(pengajuan.jumlah));
                    $('#infoPengajuanLama').text(pengajuan.lama_angsuran.lama_angsuran + ' Bulan');

                    // Tampilkan foto anggota
                    if (pengajuan.anggota_id) {
                        fetch(`/admin/pinjaman/anggota-detail/${pengajuan.anggota_id}`)
                            .then(res => {
                                if (!res.ok) {
                                    throw new Error(`HTTP error! status: ${res.status}`);
                                }
                                return res.json();
                            })
                            .then(data => {
                                console.log('Anggota data loaded:', data); // Debug log

                                // Gunakan foto terbaru dengan timestamp untuk avoid cache
                                const photoUrl = data.photo_url
                                    ? `${data.photo_url}?v=${Date.now()}`
                                    : '{{ asset("assets/images/profile/user-1.jpg") }}';

                                $('#anggotaPhoto').html(`
                                        <img src="${photoUrl}" 
                                             class="img-fluid rounded" 
                                             alt="Foto Anggota"
                                             style="max-width: 100%; height: auto;"
                                             onerror="this.src='{{ asset("assets/images/profile/user-1.jpg") }}'">
                                    `);

                                // Set departemen jika ada field
                                if ($('#anggotaDepartemen').length) {
                                    $('#anggotaDepartemen').text(data.departement || '-');
                                }
                            })
                            .catch(err => {
                                console.error('Error loading anggota photo:', err);
                                $('#anggotaPhoto').html(`
                                        <div class="text-center text-muted p-3">
                                            <i class="ti ti-photo-off fs-1 d-block mb-2"></i>
                                            <small>Foto tidak tersedia</small>
                                        </div>
                                    `);
                            });
                    } else {
                        $('#anggotaPhoto').html(`
                                <div class="text-center text-muted p-3">
                                    <i class="ti ti-user fs-1 d-block mb-2"></i>
                                    <small>Pilih pengajuan terlebih dahulu</small>
                                </div>
                            `);
                    }

                    // PERBAIKAN: Update persentase bunga dinamis
                    $('#proyeksiPersen').text(proyeksi.bunga_persen + '%');

                    // Tampilkan proyeksi
                    $('#proyeksiPokok').text('Rp ' + formatRupiah(proyeksi.pokok_pinjaman));
                    $('#proyeksiBunga').text('Rp ' + formatRupiah(proyeksi.biaya_bunga * pengajuan.lama_angsuran.lama_angsuran));
                    $('#proyeksiAdmin').text('Rp ' + formatRupiah(proyeksi.biaya_admin));
                    $('#proyeksiTotal').text('Rp ' + formatRupiah(proyeksi.jumlah_angsuran));
                    $('#proyeksiPerBulan').text('Rp ' + formatRupiah(proyeksi.angsuran_per_bulan));

                    // Pindah ke step 2
                    $('#step1').hide();
                    $('#step2').show();
                    $('#btnSimpan').show();
                },
                error: function (xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memuat detail pengajuan', 'error');
                }
            });
        }

        // Kembali ke step 1
        function backToStep1() {
            $('#step2').hide();
            $('#step1').show();
            $('#btnSimpan').hide();
            $('#selectPengajuan').val('');
            $('#formPinjaman')[0].reset();
        }

        // Function: Tambah Data
        function tambahData() {
            $('#formPinjaman')[0].reset();
            $('#step1').show();
            $('#step2').hide();
            $('#btnSimpan').hide();
            $('#modalFormPinjaman').modal('show');
        }

        // Function: Edit Data
        function editData() {
            if (!selectedRow) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih data yang akan diedit terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            $.ajax({
                url: `{{ url('admin/pinjaman') }}/${selectedRow}/edit`,
                type: 'GET',
                success: function (response) {
                    $('#editPinjamanId').val(response.data.id);
                    $('#editTglPinjam').val(response.data.tanggal_pinjam);
                    $('#editKasId').val(response.data.dari_kas_id);
                    $('#editLamaAngsuran').val(response.data.lama_angsuran_id);
                    $('#editKeterangan').val(response.data.keterangan);

                    loadKasList();
                    loadLamaAngsuranList();

                    $('#editProyeksiContainer').hide();
                    $('#modalEditPinjaman').modal('show');
                },
                error: function () {
                    Swal.fire('Error', 'Gagal memuat data pinjaman', 'error');
                }
            });
        }

        // Function: Hapus Data dengan Soft Delete
        function hapusData() {
            if (!selectedRow) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silakan pilih data yang akan dihapus terlebih dahulu',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Get delete info dulu
            $.ajax({
                url: `{{ url('admin/pinjaman') }}/${selectedRow}/delete-info`,
                type: 'GET',
                success: function (response) {
                    if (response.success) {
                        showDeleteConfirmation(response.data);
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Tidak dapat mengambil informasi pinjaman'
                    });
                }
            });
        }

        // Show Delete Confirmation Modal
        function showDeleteConfirmation(data) {
            // Jika tidak bisa dihapus
            if (!data.can_delete) {
                // Cek apakah karena sudah validasi lunas
                const isValidasiLunas = data.reason && data.reason.includes('validasi lunas');

                Swal.fire({
                    icon: 'error',
                    title: 'Tidak Dapat Dihapus',
                    html: `
                                                    <div class="alert alert-danger text-start mb-3">
                                                        <i class="ti ti-alert-circle"></i>
                                                        ${data.reason}
                                                    </div>
                                                    ${isValidasiLunas ? `
                                                        <div class="alert alert-info text-start">
                                                            <i class="ti ti-info-circle me-2"></i>
                                                            Untuk menghapus pinjaman ini, Anda harus membatalkan validasi lunas terlebih dahulu.
                                                        </div>
                                                    ` : ''}
                                                `,
                    showCancelButton: isValidasiLunas,
                    confirmButtonText: isValidasiLunas ? '<i class="ti ti-eye"></i> Lihat Detail Validasi Lunas' : 'OK',
                    cancelButtonText: 'Tutup',
                    confirmButtonColor: isValidasiLunas ? '#0d6efd' : '#dc3545',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed && isValidasiLunas) {
                        // Redirect ke halaman detail pinjaman lunas
                        window.location.href = "{{ route('pinjaman.lunas.detail', ':id') }}".replace(':id', selectedRow);
                    }
                });
                return;
            }

            // Jika butuh alasan (sudah ada pembayaran)
            if (data.require_reason) {
                Swal.fire({
                    title: 'Konfirmasi Penghapusan',
                    html: `
                                                            <div class="text-start">
                                                                <div class="alert alert-warning mb-3">
                                                                    <i class="ti ti-alert-triangle me-2"></i>
                                                                    <strong>Perhatian!</strong> Pinjaman ini sudah ada pembayaran.
                                                                </div>

                                                                <div class="card border-info mb-3">
                                                                    <div class="card-body p-3">
                                                                        <table class="table table-sm table-borderless mb-0">
                                                                            <tr>
                                                                                <td class="text-muted">Kode Pinjaman</td>
                                                                                <td>:</td>
                                                                                <td><strong>${data.kode_pinjaman}</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-muted">Anggota</td>
                                                                                <td>:</td>
                                                                                <td>${data.anggota_nama}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-muted">Pokok Pinjaman</td>
                                                                                <td>:</td>
                                                                                <td>Rp ${formatRupiah(data.pokok_pinjaman)}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-muted">Sudah Dibayar</td>
                                                                                <td>:</td>
                                                                                <td class="text-success"><strong>Rp ${formatRupiah(data.sudah_dibayar)}</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-muted">Sisa Tagihan</td>
                                                                                <td>:</td>
                                                                                <td class="text-danger"><strong>Rp ${formatRupiah(data.sisa_tagihan)}</strong></td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-semibold">Alasan Penghapusan <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="alasanHapus" rows="3" 
                                                                        placeholder="Jelaskan alasan penghapusan pinjaman ini (minimal 10 karakter)"></textarea>
                                                                    <small class="text-muted">Alasan ini akan disimpan sebagai audit trail</small>
                                                                </div>

                                                                <div class="alert alert-info mb-0">
                                                                    <i class="ti ti-info-circle me-2"></i>
                                                                    Data akan dipindahkan ke <strong>Riwayat Hapus</strong> dan dapat dipulihkan kembali.
                                                                </div>
                                                            </div>
                                                        `,
                    width: '600px',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-trash"></i> Hapus dengan Alasan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545',
                    preConfirm: () => {
                        const alasan = document.getElementById('alasanHapus').value;

                        if (!alasan || alasan.trim().length < 10) {
                            Swal.showValidationMessage('Alasan minimal 10 karakter');
                            return false;
                        }

                        return { alasan: alasan.trim() };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosesHapusWithReason(selectedRow, result.value.alasan);
                    }
                });
            } else {
                // Hapus tanpa alasan (belum ada pembayaran)
                Swal.fire({
                    title: 'Hapus Data Pinjaman?',
                    html: `
                                                            <div class="text-start">
                                                                <p>Apakah Anda yakin ingin menghapus pinjaman ini?</p>

                                                                <div class="card border-info mb-3">
                                                                    <div class="card-body p-3">
                                                                        <table class="table table-sm table-borderless mb-0">
                                                                            <tr>
                                                                                <td class="text-muted" width="40%">Kode Pinjaman</td>
                                                                                <td width="5%">:</td>
                                                                                <td><strong>${data.kode_pinjaman}</strong></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-muted">Anggota</td>
                                                                                <td>:</td>
                                                                                <td>${data.anggota_nama}</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td class="text-muted">Jumlah Pinjaman</td>
                                                                                <td>:</td>
                                                                                <td>Rp ${formatRupiah(data.pokok_pinjaman)}</td>
                                                                            </tr>
                                                                        </table>
                                                                    </div>
                                                                </div>

                                                                <div class="alert alert-info mb-0">
                                                                    <i class="ti ti-info-circle me-2"></i>
                                                                    Data akan dipindahkan ke <strong>Riwayat Hapus</strong> dan status pengajuan akan dikembalikan.
                                                                </div>
                                                            </div>
                                                        `,
                    icon: 'warning',
                    width: '550px',
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-trash"></i> Ya, Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosesHapus(selectedRow);
                    }
                });
            }
        }

        // Proses Hapus Tanpa Alasan
        function prosesHapus(id) {
            Swal.fire({
                title: 'Menghapus Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: `{{ url('admin/pinjaman') }}/${id}`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
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
                },
                error: function (xhr) {
                    // Jika butuh alasan
                    if (xhr.responseJSON?.require_reason) {
                        Swal.close();
                        $.ajax({
                            url: `{{ url('admin/pinjaman') }}/${id}/delete-info`,
                            type: 'GET',
                            success: function (response) {
                                showDeleteConfirmation(response.data);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                        });
                    }
                }
            });
        }

        // Proses Hapus Dengan Alasan
        function prosesHapusWithReason(id, alasan) {
            Swal.fire({
                title: 'Menghapus Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: `{{ url('admin/pinjaman') }}/${id}/soft-delete-with-reason`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}',
                    alasan_hapus: alasan
                },
                success: function (response) {
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

        // Lihat Riwayat Hapus
        function lihatRiwayatHapus() {
            window.location.href = '{{ route("pinjaman.pinjaman.riwayat-hapus") }}';
        }

        // Load list lama angsuran
        function loadLamaAngsuranList() {
            $.ajax({
                url: '{{ route("master.lama-angsuran.list") }}',
                type: 'GET',
                success: function (response) {
                    const select = $('#editLamaAngsuran');
                    select.html('<option value="">-- Pilih Lama Angsuran --</option>');

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function (item) {
                            select.append(`<option value="${item.id}">${item.lama_angsuran} Bulan</option>`);
                        });
                    }
                },
                error: function (xhr) {
                    console.error('Error loading lama angsuran:', xhr);
                    const select = $('#editLamaAngsuran');
                    select.html(`
                                                            <option value="">-- Pilih Lama Angsuran --</option>
                                                            <option value="1">6 Bulan</option>
                                                            <option value="2">12 Bulan</option>
                                                            <option value="3">18 Bulan</option>
                                                            <option value="4">24 Bulan</option>
                                                        `);
                }
            });
        }

        // Hitung ulang proyeksi saat lama angsuran berubah
        $('#editLamaAngsuran').on('change', function () {
            const lamaAngsuranId = $(this).val();
            const pinjamanId = $('#editPinjamanId').val();

            if (!lamaAngsuranId || !pinjamanId) {
                $('#editProyeksiContainer').hide();
                return;
            }

            $.ajax({
                url: `{{ url('admin/pinjaman') }}/${pinjamanId}/recalculate`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    lama_angsuran_id: lamaAngsuranId
                },
                success: function (response) {
                    const proyeksi = response.data;

                    // PERBAIKAN: Update persentase bunga di modal edit
                    $('#editProyeksiPersen').text(proyeksi.bunga_persen + '%');

                    $('#editProyeksiPokok').text('Rp ' + formatRupiah(proyeksi.angsuran_pokok));
                    $('#editProyeksiBunga').text('Rp ' + formatRupiah(proyeksi.biaya_bunga * proyeksi.lama_angsuran));
                    $('#editProyeksiTotal').text('Rp ' + formatRupiah(proyeksi.jumlah_angsuran));
                    $('#editProyeksiPerBulan').text('Rp ' + formatRupiah(proyeksi.angsuran_per_bulan));

                    $('#editProyeksiContainer').show();
                },
                error: function () {
                    $('#editProyeksiContainer').hide();
                }
            });
        });

        // Function: Simpan Pinjaman
        function simpanPinjaman() {
            const form = $('#formPinjaman');
            const formData = new FormData(form[0]);

            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("pinjaman.pinjaman.store") }}',
                type: 'POST',
                data: Object.fromEntries(formData),
                success: function (response) {
                    $('#modalFormPinjaman').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Pinjaman berhasil diproses',
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

        // Function: Update Pinjaman
        function updatePinjaman() {
            const form = $('#formEditPinjaman');
            const formData = new FormData(form[0]);
            const id = $('#editPinjamanId').val();

            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }

            Swal.fire({
                title: 'Memproses...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `{{ url('admin/pinjaman') }}/${id}`,
                type: 'PUT',
                data: Object.fromEntries(formData),
                success: function (response) {
                    $('#modalEditPinjaman').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Data pinjaman berhasil diupdate',
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
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });
                }
            });
        }

        function cetakNota(id) {
            const url = `{{ url('admin/pinjaman/cetak') }}/${id}`;
            window.open(url, '_blank');
        }

        // Function: Filter Data
        function filterData() {
            const status = $('#filterStatus').val();
            const kode = $('#filterKode').val();
            const nama = $('#filterNama').val();
            const tanggal = $('#filterTanggal').val();

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menerapkan filter',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                const params = new URLSearchParams({
                    status: status,
                    kode: kode,
                    nama: nama,
                    tanggal: tanggal
                }).toString();

                location.href = "{{ route('pinjaman.pinjaman') }}" + '?' + params;
            }, 800);
        }

        // Function: Reset Filter
        function resetFilter() {
            $('#filterStatus').val('');
            $('#filterKode').val('');
            $('#filterNama').val('');
            $('#filterTanggal').val('');

            if (table) {
                table.search('').draw();
            }

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Semua filter telah dikembalikan',
                timer: 1500,
                showConfirmButton: false
            });
        }

        function cetakLaporan() {
            const status = $('#filterStatus').val() || '';
            const kode = $('#filterKode').val() || '';
            const nama = $('#filterNama').val() || '';
            const tanggal = $('#filterTanggal').val() || '';

            const params = new URLSearchParams({
                status: status,
                kode: kode,
                nama: nama,
                tanggal: tanggal
            }).toString();

            const url = "{{ route('pinjaman.pinjaman.cetak.laporan') }}" + '?' + params;
            window.open(url, '_blank');
        }

        // Helper: Format rupiah
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Helper: Format tanggal
        function formatTanggal(tanggal) {
            const date = new Date(tanggal);
            const options = { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            return date.toLocaleDateString('id-ID', options);
        }
    </script>
@endpush