@extends('layouts.app')

@section('title', 'Master Data - Data Kas')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
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
                <table id="tabelDataKas"
                    class="table table-hover align-middle rounded-2 border overflow-hidden nowrap-table" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="50px">No</th>
                            <th class="align-middle" width="150px">Nama Kas</th>
                            <th class="text-center" width="80px">Aktif</th>
                            <th class="text-center" width="100px">Simpanan</th>
                            <th class="text-center" width="100px">Penarikan</th>
                            <th class="text-center" width="100px">Pinjaman</th>
                            <th class="text-center" width="100px">Angsuran</th>
                            <th class="text-center" width="130px">Pemasukan Kas</th>
                            <th class="text-center" width="140px">Pengeluaran Kas</th>
                            <th class="text-center" width="120px">Transfer Kas</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataKas as $index => $item)
                            <tr data-id="{{ $item->id }}" data-nama="{{ $item->nama_kas }}" data-aktif="{{ $item->aktif }}"
                                data-simpan="{{ $item->simpanan }}" data-penarikan="{{ $item->penarikan }}"
                                data-pinjaman="{{ $item->pinjaman }}" data-bayar="{{ $item->angsuran }}"
                                data-pemasukan="{{ $item->pemasukan_kas }}" data-pengeluaran="{{ $item->pengeluaran_kas }}"
                                data-transfer="{{ $item->transfer_kas }}">
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
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
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Nama Kas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaKas" placeholder="Masukkan nama kas"
                                maxlength="225" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Aktif</label>
                                <select class="form-select" id="aktif">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Simpanan</label>
                                <select class="form-select" id="simpanan">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Penarikan</label>
                                <select class="form-select" id="penarikan">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pinjaman</label>
                                <select class="form-select" id="pinjaman">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Angsuran</label>
                                <select class="form-select" id="angsuran">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pemasukan Kas</label>
                                <select class="form-select" id="pemasukanKas">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pengeluaran Kas</label>
                                <select class="form-select" id="pengeluaranKas">
                                    <option value="">-- Pilih --</option>
                                    <option value="Y" selected>Y</option>
                                    <option value="T">T</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Transfer Kas</label>
                                <select class="form-select" id="transferKas">
                                    <option value="">-- Pilih --</option>
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
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Initialize DataTable
        let table;
        $(document).ready(function () {
            table = $('#tabelDataKas').DataTable({
                ordering: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                scrollX: true,
                columnDefs: [
                    { orderable: false, targets: '_all' }
                ],
                drawCallback: function () {
                    const api = this.api();
                    const startIndex = api.context[0]._iDisplayStart;
                    api.column(0, { page: 'current' }).nodes().each(function (cell, i) {
                        cell.innerHTML = startIndex + i + 1;
                    });
                }
            });
        });

        // Function untuk membuat badge
        function createBadge(value) {
            const badgeClass = value === 'Y' ? 'success' : 'danger';
            return `<span class="badge bg-${badgeClass}-subtle text-${badgeClass} fw-semibold px-3 py-1">${value}</span>`;
        }

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Jenis Kas';
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

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Edit Data
        function editData(btn) {
            const row = btn.closest('tr');
            const id = row.getAttribute('data-id');
            const nama = row.getAttribute('data-nama');
            const aktif = row.getAttribute('data-aktif');
            const simpan = row.getAttribute('data-simpan');
            const penarikan = row.getAttribute('data-penarikan');
            const pinjaman = row.getAttribute('data-pinjaman');
            const bayar = row.getAttribute('data-bayar');
            const pemasukan = row.getAttribute('data-pemasukan');
            const pengeluaran = row.getAttribute('data-pengeluaran');
            const transfer = row.getAttribute('data-transfer');

            document.getElementById('modalTitle').textContent = 'Ubah Jenis Kas';
            document.getElementById('editId').value = id;
            document.getElementById('namaKas').value = nama;
            document.getElementById('aktif').value = aktif;
            document.getElementById('simpanan').value = simpan;
            document.getElementById('penarikan').value = penarikan;
            document.getElementById('pinjaman').value = pinjaman;
            document.getElementById('angsuran').value = bayar;
            document.getElementById('pemasukanKas').value = pemasukan;
            document.getElementById('pengeluaranKas').value = pengeluaran;
            document.getElementById('transferKas').value = transfer;

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formDataKas');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const editId = document.getElementById('editId').value;
            const namaKas = document.getElementById('namaKas').value;
            const aktif = document.getElementById('aktif').value || 'Y';
            const simpanan = document.getElementById('simpanan').value || 'Y';
            const penarikan = document.getElementById('penarikan').value || 'Y';
            const pinjaman = document.getElementById('pinjaman').value || 'Y';
            const angsuran = document.getElementById('angsuran').value || 'Y';
            const pemasukanKas = document.getElementById('pemasukanKas').value || 'Y';
            const pengeluaranKas = document.getElementById('pengeluaranKas').value || 'Y';
            const transferKas = document.getElementById('transferKas').value || 'Y';

            if (editId) {
                // Update existing row
                const rows = document.querySelectorAll('#tabelDataKas tbody tr');
                rows.forEach(row => {
                    if (row.getAttribute('data-id') === editId) {
                        row.setAttribute('data-nama', namaKas);
                        row.setAttribute('data-aktif', aktif);
                        row.setAttribute('data-simpan', simpanan);
                        row.setAttribute('data-penarikan', penarikan);
                        row.setAttribute('data-pinjaman', pinjaman);
                        row.setAttribute('data-bayar', angsuran);
                        row.setAttribute('data-pemasukan', pemasukanKas);
                        row.setAttribute('data-pengeluaran', pengeluaranKas);
                        row.setAttribute('data-transfer', transferKas);

                        row.cells[1].innerHTML = `<div class="fw-semibold text-dark">${namaKas}</div>`;
                        row.cells[2].innerHTML = createBadge(aktif);
                        row.cells[3].innerHTML = createBadge(simpanan);
                        row.cells[4].innerHTML = createBadge(penarikan);
                        row.cells[5].innerHTML = createBadge(pinjaman);
                        row.cells[6].innerHTML = createBadge(angsuran);
                        row.cells[7].innerHTML = createBadge(pemasukanKas);
                        row.cells[8].innerHTML = createBadge(pengeluaranKas);
                        row.cells[9].innerHTML = createBadge(transferKas);
                    }
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil diubah',
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                // Add new row
                const newId = Date.now();
                const newRow = `
                                    <tr data-id="${newId}" data-nama="${namaKas}" data-aktif="${aktif}" data-simpan="${simpanan}" data-penarikan="${penarikan}" data-pinjaman="${pinjaman}" data-bayar="${angsuran}" data-pemasukan="${pemasukanKas}" data-pengeluaran="${pengeluaranKas}" data-transfer="${transferKas}">
                                        <td class="text-center text-muted fw-medium"></td>
                                        <td><div class="fw-semibold text-dark">${namaKas}</div></td>
                                        <td class="text-center">${createBadge(aktif)}</td>
                                        <td class="text-center">${createBadge(simpanan)}</td>
                                        <td class="text-center">${createBadge(penarikan)}</td>
                                        <td class="text-center">${createBadge(pinjaman)}</td>
                                        <td class="text-center">${createBadge(angsuran)}</td>
                                        <td class="text-center">${createBadge(pemasukanKas)}</td>
                                        <td class="text-center">${createBadge(pengeluaranKas)}</td>
                                        <td class="text-center">${createBadge(transferKas)}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning me-1" onclick="editData(this)" title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="hapusData(this)" title="Hapus">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                table.row.add($(newRow)).draw();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Data berhasil ditambahkan',
                    timer: 1500,
                    showConfirmButton: false
                });
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById('modalForm'));
            modal.hide();
        }

        // Hapus Data
        function hapusData(btn) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = btn.closest('tr');
                    table.row(row).remove().draw();

                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data berhasil dihapus',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        // Cari Data
        function cariData() {
            const search = document.getElementById('searchInput').value;
            table.search(search).draw();
        }

        // Reset Filter
        function resetFilter() {
            document.getElementById('searchInput').value = '';
            table.search('').draw();

            Swal.fire({
                icon: 'info',
                title: 'Filter Direset',
                text: 'Pencarian telah dikembalikan',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Ekspor Data ke Excel
        function eksporData() {
            const rows = [];
            const headers = ['No', 'Nama Kas', 'Aktif', 'Simpanan', 'Penarikan', 'Pinjaman', 'Angsuran', 'Pemasukan Kas', 'Pengeluaran Kas', 'Transfer Kas'];
            rows.push(headers);

            table.rows({ search: 'applied' }).every(function () {
                const row = this.node();
                const no = this.index() + 1;
                const nama = row.getAttribute('data-nama');
                const aktif = row.getAttribute('data-aktif');
                const simpan = row.getAttribute('data-simpan');
                const penarikan = row.getAttribute('data-penarikan');
                const pinjaman = row.getAttribute('data-pinjaman');
                const bayar = row.getAttribute('data-bayar');
                const pemasukan = row.getAttribute('data-pemasukan');
                const pengeluaran = row.getAttribute('data-pengeluaran');
                const transfer = row.getAttribute('data-transfer');

                rows.push([no, nama, aktif, simpan, penarikan, pinjaman, bayar, pemasukan, pengeluaran, transfer]);
            });

            let csvContent = '\ufeff';
            csvContent += rows.map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const tanggal = new Date().toISOString().slice(0, 10);
            link.setAttribute('href', url);
            link.setAttribute('download', `Data_Kas_${tanggal}.csv`);
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.fire({
                icon: 'success',
                title: 'Export Berhasil',
                text: 'File CSV akan segera diunduh',
                timer: 1500,
                showConfirmButton: false
            });
        }

        // Cetak Laporan
        function cetakLaporan() {
            window.print();
        }
    </script>
@endpush