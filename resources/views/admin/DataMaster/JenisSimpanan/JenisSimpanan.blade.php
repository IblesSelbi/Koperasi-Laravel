@extends('layouts.app')

@section('title', 'Master Data - Jenis Simpanan')

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
                    <h4 class="fw-semibold mb-1">Master Data - Jenis Simpanan</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data jenis simpanan koperasi</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Jenis Simpanan">
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
                <table id="tabelJenisSimpanan" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="50px">No</th>
                            <th>Jenis Simpanan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">Tampil</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jenisSimpanan as $index => $item)
                            <tr data-id="{{ $item->id }}" data-jns="{{ $item->jenis_simpanan }}" data-jumlah="{{ $item->jumlah }}" data-tampil="{{ $item->tampil }}">
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->jenis_simpanan }}</div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $item->tampil == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->tampil == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->tampil }}
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Jenis Simpanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formJenisSimpanan">
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Jenis Simpanan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jnsSimpanan" placeholder="Masukkan jenis simpanan" maxlength="30" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="jumlah" placeholder="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tampil</label>
                            <select class="form-select" id="tampil">
                                <option value="">-- Pilih --</option>
                                <option value="Y" selected>Y</option>
                                <option value="T">T</option>
                            </select>
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
            table = $('#tabelJenisSimpanan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [4] },
                    {
                        targets: 0,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }
                ]
            });
        });

        // Format Currency
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Jenis Simpanan';
            document.getElementById('formJenisSimpanan').reset();
            document.getElementById('editId').value = '';
            document.getElementById('tampil').value = 'Y';

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
            const jns = row.getAttribute('data-jns');
            const jumlah = row.getAttribute('data-jumlah');
            const tampil = row.getAttribute('data-tampil');

            document.getElementById('modalTitle').textContent = 'Ubah Jenis Simpanan';
            document.getElementById('editId').value = id;
            document.getElementById('jnsSimpanan').value = jns;
            document.getElementById('jumlah').value = formatRupiah(jumlah);
            document.getElementById('tampil').value = tampil;

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formJenisSimpanan');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const editId = document.getElementById('editId').value;
            const jnsSimpanan = document.getElementById('jnsSimpanan').value;
            const jumlah = document.getElementById('jumlah').value.replace(/\./g, '') || '0';
            const tampil = document.getElementById('tampil').value || 'Y';

            if (editId) {
                // Update existing row
                const rows = document.querySelectorAll('#tabelJenisSimpanan tbody tr');
                rows.forEach(row => {
                    if (row.getAttribute('data-id') === editId) {
                        row.setAttribute('data-jns', jnsSimpanan);
                        row.setAttribute('data-jumlah', jumlah);
                        row.setAttribute('data-tampil', tampil);

                        row.cells[1].innerHTML = `<div class="fw-semibold text-dark">${jnsSimpanan}</div>`;
                        row.cells[2].innerHTML = `<span class="fw-bold text-dark">Rp ${formatRupiah(jumlah)}</span>`;
                        row.cells[3].innerHTML = `<span class="badge bg-${tampil === 'Y' ? 'success' : 'danger'}-subtle text-${tampil === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${tampil}</span>`;
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
                    <tr data-id="${newId}" data-jns="${jnsSimpanan}" data-jumlah="${jumlah}" data-tampil="${tampil}">
                        <td class="text-center text-muted fw-medium"></td>
                        <td><div class="fw-semibold text-dark">${jnsSimpanan}</div></td>
                        <td class="text-end"><span class="fw-bold text-dark">Rp ${formatRupiah(jumlah)}</span></td>
                        <td class="text-center"><span class="badge bg-${tampil === 'Y' ? 'success' : 'danger'}-subtle text-${tampil === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${tampil}</span></td>
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

        // Cetak Laporan
        function cetakLaporan() {
            window.print();
        }

        // Ekspor Data ke Excel
        function eksporData() {
            // Ambil semua data dari tabel
            const rows = [];
            const headers = ['No', 'Jenis Simpanan', 'Jumlah', 'Tampil'];
            rows.push(headers);

            // Ambil data dari DataTable
            table.rows({ search: 'applied' }).every(function () {
                const row = this.node();
                const jns = row.getAttribute('data-jns');
                const jumlah = row.getAttribute('data-jumlah');
                const tampil = row.getAttribute('data-tampil');
                const no = this.index() + 1;

                rows.push([no, jns, jumlah, tampil]);
            });

            // Buat CSV content
            let csvContent = '\ufeff'; // UTF-8 BOM untuk Excel
            csvContent += rows.map(row => row.join(',')).join('\n');

            // Download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const tanggal = new Date().toISOString().slice(0, 10);
            link.setAttribute('href', url);
            link.setAttribute('download', `Jenis_Simpanan_${tanggal}.csv`);
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

        // Format Currency Input
        document.getElementById('jumlah').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });
    </script>
@endpush