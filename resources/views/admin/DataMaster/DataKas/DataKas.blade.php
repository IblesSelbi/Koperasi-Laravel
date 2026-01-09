@extends('layouts.app')

@section('title', 'Master Data - Data Kas')

@push('styles')
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Master Data - Data Kas</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data jenis kas koperasi</p>
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
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-lg-auto">
                    <button class="btn btn-primary btn-sm" onclick="tambahData()">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                    <button class="btn btn-success btn-sm" onclick="eksporData()">
                        <i class="ti ti-download"></i> Ekspor
                    </button>
                    <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                        <i class="ti ti-printer"></i> Cetak
                    </button>
                </div>
                <div class="col-lg-auto ms-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Data Kas">
                        <button class="btn btn-primary" onclick="cariData()">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-auto">
                    <button class="btn btn-secondary btn-sm" onclick="resetFilter()">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelDataKas" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="50px">No</th>
                            <th>Nama Kas</th>
                            <th class="text-center">Aktif</th>
                            <th class="text-center">Simpanan</th>
                            <th class="text-center">Penarikan</th>
                            <th class="text-center">Pinjaman</th>
                            <th class="text-center">Angsuran</th>
                            <th class="text-center">Pemasukan Kas</th>
                            <th class="text-center">Pengeluaran Kas</th>
                            <th class="text-center">Transfer Kas</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataKas as $index => $item)
                            <tr data-id="{{ $item->id }}" data-nama="{{ $item->nama_kas }}"
                                data-aktif="{{ $item->aktif }}" data-simpanan="{{ $item->simpanan }}"
                                data-penarikan="{{ $item->penarikan }}" data-pinjaman="{{ $item->pinjaman }}"
                                data-angsuran="{{ $item->angsuran }}" data-pemasukan="{{ $item->pemasukan_kas }}"
                                data-pengeluaran="{{ $item->pengeluaran_kas }}" data-transfer="{{ $item->transfer_kas }}">
                                <td class="text-center text-muted fw-medium"></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->nama_kas }}</div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->aktif == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->aktif == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->aktif }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->simpanan == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->simpanan == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->simpanan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->penarikan == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->penarikan == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->penarikan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->pinjaman == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->pinjaman == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->pinjaman }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->angsuran == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->angsuran == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->angsuran }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->pemasukan_kas == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->pemasukan_kas == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->pemasukan_kas }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->pengeluaran_kas == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->pengeluaran_kas == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->pengeluaran_kas }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->transfer_kas == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->transfer_kas == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->transfer_kas }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-1" onclick="editData(this)" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusData(this)" title="Hapus">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Jenis Kas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formDataKas">
                        @csrf
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Nama Kas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaKas" placeholder="Masukkan nama kas"
                                maxlength="225" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aktif <span class="text-danger">*</span></label>
                                <select class="form-select" id="aktif" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Simpanan <span class="text-danger">*</span></label>
                                <select class="form-select" id="simpanan" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penarikan <span class="text-danger">*</span></label>
                                <select class="form-select" id="penarikan" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pinjaman <span class="text-danger">*</span></label>
                                <select class="form-select" id="pinjaman" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Angsuran <span class="text-danger">*</span></label>
                                <select class="form-select" id="angsuran" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemasukan Kas <span class="text-danger">*</span></label>
                                <select class="form-select" id="pemasukanKas" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pengeluaran Kas <span class="text-danger">*</span></label>
                                <select class="form-select" id="pengeluaranKas" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transfer Kas <span class="text-danger">*</span></label>
                                <select class="form-select" id="transferKas" required>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpanData()">
                        <i class="ti ti-device-floppy"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

    <script>
        let table;

        // INIT DATATABLE
        $(document).ready(function () {
            table = $('#tabelDataKas').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/i18n/id.json') }}"
                },
                pageLength: 10,
                order: [],
                scrollX: true,
                columnDefs: [
                    { orderable: false, targets: [0, 10] }
                ]
            });

            // 🔥 FIX NOMOR AGAR SELALU URUT
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' })
                    .nodes()
                    .each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
            }).draw();
        });

        // TAMBAH DATA
        function tambahData() {
            document.getElementById('modalTitle').innerText = 'Tambah Jenis Kas';
            document.getElementById('formDataKas').reset();
            document.getElementById('editId').value = '';
            
            // Set default values
            document.getElementById('aktif').value = 'Y';
            document.getElementById('simpanan').value = 'Y';
            document.getElementById('penarikan').value = 'Y';
            document.getElementById('pinjaman').value = 'Y';
            document.getElementById('angsuran').value = 'Y';
            document.getElementById('pemasukanKas').value = 'Y';
            document.getElementById('pengeluaranKas').value = 'Y';
            document.getElementById('transferKas').value = 'Y';

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // EDIT DATA
        function editData(btn) {
            const row = btn.closest('tr');

            document.getElementById('modalTitle').innerText = 'Ubah Jenis Kas';
            document.getElementById('editId').value = row.dataset.id;
            document.getElementById('namaKas').value = row.dataset.nama;
            document.getElementById('aktif').value = row.dataset.aktif;
            document.getElementById('simpanan').value = row.dataset.simpanan;
            document.getElementById('penarikan').value = row.dataset.penarikan;
            document.getElementById('pinjaman').value = row.dataset.pinjaman;
            document.getElementById('angsuran').value = row.dataset.angsuran;
            document.getElementById('pemasukanKas').value = row.dataset.pemasukan;
            document.getElementById('pengeluaranKas').value = row.dataset.pengeluaran;
            document.getElementById('transferKas').value = row.dataset.transfer;

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // SIMPAN DATA (ADD & UPDATE)
        function simpanData() {
            const id = document.getElementById('editId').value;
            const nama_kas = document.getElementById('namaKas').value;
            const aktif = document.getElementById('aktif').value;
            const simpanan = document.getElementById('simpanan').value;
            const penarikan = document.getElementById('penarikan').value;
            const pinjaman = document.getElementById('pinjaman').value;
            const angsuran = document.getElementById('angsuran').value;
            const pemasukan_kas = document.getElementById('pemasukanKas').value;
            const pengeluaran_kas = document.getElementById('pengeluaranKas').value;
            const transfer_kas = document.getElementById('transferKas').value;

            const url = id
                ? `/admin/data-kas/${id}`
                : `/admin/data-kas`;

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nama_kas,
                    aktif,
                    simpanan,
                    penarikan,
                    pinjaman,
                    angsuran,
                    pemasukan_kas,
                    pengeluaran_kas,
                    transfer_kas
                })
            })
                .then(res => res.json())
                .then(res => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                })
                .catch(() => {
                    Swal.fire('Error', 'Gagal menyimpan data', 'error');
                });
        }

        // HAPUS DATA
        function hapusData(btn) {
            const id = btn.closest('tr').dataset.id;

            Swal.fire({
                title: 'Yakin hapus?',
                text: 'Data tidak bisa dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/admin/data-kas/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(res => res.json())
                        .then(res => {
                            Swal.fire('Terhapus!', res.message, 'success')
                                .then(() => location.reload());
                        });
                }
            });
        }

        // CARI DATA
        function cariData() {
            table.search(document.getElementById('searchInput').value).draw();
        }

        // RESET FILTER
        function resetFilter() {
            document.getElementById('searchInput').value = '';
            table.search('').draw();

            Swal.fire({
                icon: 'info',
                title: 'Filter direset',
                timer: 1200,
                showConfirmButton: false
            });
        }
       
        // CETAK & EXPORT (SERVER SIDE)
        function cetakLaporan() {
            window.location.href = "{{ route('master.data-kas.cetak') }}";
        }

        function eksporData() {
            window.location.href = "{{ route('master.data-kas.export') }}";
        }
    </script>

@endpush