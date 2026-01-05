@extends('layouts.app')

@section('title', 'Detail Pinjaman Lunas')

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
<!-- Daterangepicker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-semibold mb-1">Detail Pinjaman Lunas</h4>
                <p class="text-muted fs-3 mb-0">Kode Pinjaman: <strong class="text-success">{{ $pinjaman->kode
                        }}</strong></p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pinjaman.lunas') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
                <button class="btn btn-primary" onclick="bayarAngsuran({{ $pinjaman->id }})">
                    <i class="ti ti-wallet"></i> Pembayaran Angsuran
                </button>
                <button class="btn btn-success" onclick="cetakDetail({{ $pinjaman->id }})">
                    <i class="ti ti-printer"></i> Cetak Detail
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Info -->
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ti ti-alert-circle me-2"></i>
    <strong>Perhatian!</strong> Hapus salah satu transaksi pembayaran untuk membatalkan status lunas
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                    <span class="badge bg-success-subtle text-success fw-semibold px-2 py-1">
                                        {{ $pinjaman->kode }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Pinjam</td>
                                <td>:</td>
                                <td><strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->format('d F Y')
                                        }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Tempo</td>
                                <td>:</td>
                                <td><strong>{{ \Carbon\Carbon::parse($pinjaman->tanggal_tempo)->format('d F Y')
                                        }}</strong></td>
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
                                <td class="text-end"><strong>{{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.')
                                        }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Angsuran Pokok</td>
                                <td>:</td>
                                <td class="text-end"><strong>{{ number_format($pinjaman->angsuran_pokok, 0, ',', '.')
                                        }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Biaya & Bunga</td>
                                <td>:</td>
                                <td class="text-end"><strong class="text-info">{{ number_format($pinjaman->biaya_bunga,
                                        0, ',', '.') }}</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted fw-semibold pt-2">Jumlah Angsuran</td>
                                <td class="pt-2">:</td>
                                <td class="text-end pt-2">
                                    <strong class="text-success fs-6">{{ number_format($pinjaman->jumlah_angsuran, 0,
                                        ',', '.') }}</strong>
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
                <div class="col-md-3 col-6 mb-2">
                    <p class="text-muted mb-1">Dibayar</p>
                    <span class="fw-bold fs-6 text-success">Rp {{ number_format($pinjaman->sudah_dibayar, 0, ',', '.')
                        }}</span>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <p class="text-muted mb-1">Denda</p>
                    <span class="fw-bold fs-6">Rp {{ number_format($pinjaman->jumlah_denda, 0, ',', '.') }}</span>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <p class="text-muted mb-1">Sisa Tagihan</p>
                    <span class="fw-bold fs-6">Rp {{ number_format($pinjaman->sisa_tagihan, 0, ',', '.') }}</span>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <p class="text-muted mb-1">Status Pelunasan</p>
                    <span class="badge bg-success fs-5">Lunas</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simulasi Tagihan -->
<div class="card mb-3 mt-4 shadow-sm">
    <div class="card-header bg-light border-bottom">
        <h6 class="mb-0 fw-semibold text-success"><i class="ti ti-calendar-stats me-2"></i>Simulasi Tagihan</h6>
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
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($simulasi as $index => $item)
                    <tr class="{{ $index % 2 == 1 ? 'table-light' : '' }}">
                        <td class="text-center">{{ $item->bulan_ke }}</td>
                        <td class="text-end">Rp {{ number_format($item->angsuran_pokok, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->angsuran_bunga, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->biaya_admin, 0, ',', '.') }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($item->jumlah_angsuran, 0, ',', '.')
                                }}</strong></td>
                        <td class="text-center">{{ $item->tanggal_tempo }}</td>
                        <td class="text-center"><span class="badge bg-success">{{ $item->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Rp {{ number_format($simulasi->sum('angsuran_pokok'), 0, ',', '.') }}</th>
                        <th class="text-end">Rp {{ number_format($simulasi->sum('angsuran_bunga'), 0, ',', '.') }}</th>
                        <th class="text-end">Rp {{ number_format($simulasi->sum('biaya_admin'), 0, ',', '.') }}</th>
                        <th class="text-end text-success">Rp {{ number_format($simulasi->sum('jumlah_angsuran'), 0, ',',
                            '.') }}</th>
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Detail Transaksi Pembayaran -->
<div class="card mb-3 mt-4 shadow-sm">
    <div class="card-header bg-light border-bottom">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-semibold text-success"><i class="ti ti-receipt me-2"></i>Detail Transaksi Pembayaran</h6>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary" onclick="bayarPelunasan()">
                    <i class="ti ti-plus"></i> Bayar
                </button>
                <button class="btn btn-sm btn-danger" onclick="hapusTransaksi()">
                    <i class="ti ti-trash"></i> Hapus
                </button>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <!-- Filter Section -->
        <div class="p-3 border-bottom bg-light">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="kodeTransaksi" placeholder="Cari Kode Transaksi...">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                        <input type="text" class="form-control" id="filterTanggal" placeholder="Pilih Tanggal" readonly>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-primary" onclick="doSearch()">
                            <i class="ti ti-search"></i> Cari
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="clearSearch()">
                            <i class="ti ti-x"></i> Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th class="text-center">No</th>
                        <th class="text-center">Kode Bayar</th>
                        <th class="text-center">Tanggal Bayar</th>
                        <th class="text-center">Angsuran Ke</th>
                        <th class="text-center">Jenis Pembayaran</th>
                        <th class="text-end">Jumlah Bayar</th>
                        <th class="text-end">Denda</th>
                        <th class="text-center">User</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transaksi as $index => $item)
                    <tr class="{{ $index % 2 == 1 ? 'table-light' : '' }}">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input row-checkbox">
                        </td>
                        <td class="text-center">{{ $item->no }}</td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info">{{ $item->kode_bayar }}</span>
                        </td>
                        <td class="text-center">{{ $item->tanggal_bayar }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $item->angsuran_ke }}</span>
                        </td>
                        <td class="text-center">{{ $item->jenis_pembayaran }}</td>
                        <td class="text-end"><strong>Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</strong>
                        </td>
                        <td class="text-end">Rp {{ number_format($item->denda, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge border border-secondary text-secondary">{{ $item->user }}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="cetakNota({{ $item->no }})">
                                <i class="ti ti-printer"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <th class="text-center" colspan="6">Jumlah</th>
                        <th class="text-end text-success">Rp {{ number_format($transaksi->sum('jumlah_bayar'), 0, ',',
                            '.') }}</th>
                        <th class="text-end">Rp {{ number_format($transaksi->sum('denda'), 0, ',', '.') }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Bayar Pelunasan -->
<div class="modal fade" id="modalBayarPelunasan" tabindex="-1" aria-labelledby="modalBayarPelunasanLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title" id="modalBayarPelunasanLabel">
                    <i class="ti ti-wallet me-2"></i>Form Bayar Pelunasan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formBayarPelunasan">
                    <div class="mb-3">
                        <label for="tglTransaksi" class="form-label">Tanggal Transaksi</label>
                        <input type="datetime-local" class="form-control" id="tglTransaksi" name="tglTransaksi"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="nomorPinjam" class="form-label">Nomor Pinjam</label>
                        <input type="text" class="form-control" id="nomorPinjam" name="nomorPinjam"
                            value="{{ $pinjaman->kode }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="sisaTagihan" class="form-label">Sisa Tagihan</label>
                        <input type="text" class="form-control" id="sisaTagihan" name="sisaTagihan" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="jumlahBayar" class="form-label">Jumlah Bayar</label>
                        <input type="text" class="form-control" id="jumlahBayar" name="jumlahBayar" required>
                    </div>
                    <div class="mb-3">
                        <label for="simpanKeKas" class="form-label">Simpan Ke Kas</label>
                        <select class="form-select" id="simpanKeKas" name="simpanKeKas" required>
                            <option value="">-- Pilih Kas --</option>
                            <option value="1">Kas Tunai</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <input type="text" class="form-control" id="keterangan" name="keterangan">
                    </div>
                    <div class="alert alert-info" role="alert">
                        <small>
                            <strong>Angsuran Ke:</strong> <span id="angsuranKe">-</span><br>
                            <strong>Sisa Angsuran:</strong> <span id="sisaAngsuran">-</span><br>
                            <strong>Denda:</strong> <span id="denda">Rp 0</span>
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x"></i> Batal
                </button>
                <button type="button" class="btn btn-success" onclick="simpanPelunasan()">
                    <i class="ti ti-check"></i> Simpan
                </button>
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
    // Function: Cetak Detail
    function cetakDetail(id) {
        const url = `{{ url('pinjaman/lunas/cetak') }}/${id}`;
        window.open(url, '_blank');
    }

    // Function: Bayar Angsuran
    function bayarAngsuran(id) {
        window.location.href = `{{ url('pinjaman/bayar') }}?id=${id}`;
    }

    // Function: Bayar Pelunasan
    function bayarPelunasan() {
        const modal = new bootstrap.Modal(document.getElementById('modalBayarPelunasan'));
        modal.show();

        // Set tanggal sekarang
        const now = new Date();
        const dateStr = now.toISOString().slice(0, 16);
        document.getElementById('tglTransaksi').value = dateStr;

        // Load data sisa tagihan
        document.getElementById('sisaTagihan').value = 'Rp 0';
        document.getElementById('angsuranKe').textContent = '3';
        document.getElementById('sisaAngsuran').textContent = '0 Bulan';
    }

    // Function: Simpan Pelunasan
    function simpanPelunasan() {
        // Validasi form
        const tglTransaksi = document.getElementById('tglTransaksi').value;
        const jumlahBayar = document.getElementById('jumlahBayar').value;
        const simpanKeKas = document.getElementById('simpanKeKas').value;

        if (!tglTransaksi) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Tanggal transaksi harus diisi',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!jumlahBayar) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Jumlah bayar harus diisi',
                confirmButtonText: 'OK'
            });
            return;
        }

        if (!simpanKeKas) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Kas harus dipilih',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Simpan data
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Data pembayaran berhasil disimpan',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalBayarPelunasan'));
            modal.hide();
            location.reload();
        });
    }

    // Function: Hapus Transaksi
    function hapusTransaksi() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');

        if (checkedBoxes.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Silakan pilih transaksi yang akan dihapus',
                confirmButtonText: 'OK'
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin akan menghapus ${checkedBoxes.length} transaksi?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-check"></i> Ya, Hapus',
            cancelButtonText: '<i class="ti ti-x"></i> Batal',
            confirmButtonColor: '#dc3545',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Transaksi berhasil dihapus',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        });
    }

    // Function: Cetak Nota
    function cetakNota(id) {
        window.open('{{ url("cetak/nota") }}/' + id, '_blank');
    }

    // Function: Search
    function doSearch() {
        const kodeTransaksi = document.getElementById('kodeTransaksi').value;
        const filterTanggal = document.getElementById('filterTanggal').value;
        console.log('Searching...', { kodeTransaksi, filterTanggal });
    }

    // Function: Clear Search
    function clearSearch() {
        document.getElementById('kodeTransaksi').value = '';
        document.getElementById('filterTanggal').value = '';
        location.reload();
    }

    // Select All Checkbox
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }
    });
</script>
@endpush