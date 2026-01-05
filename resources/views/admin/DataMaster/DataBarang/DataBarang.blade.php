@extends('layouts.app')

@section('title', 'Master Data - Data Barang')

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
                    <h4 class="fw-semibold mb-1">Master Data - Data Barang</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data barang pinjaman koperasi</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Data Barang">
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
                <table id="tabelDataBarang" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="50px">No</th>
                            <th>Nama Barang</th>
                            <th>Type</th>
                            <th>Merk</th>
                            <th class="text-end">Harga</th>
                            <th class="text-center">Jumlah</th>
                            <th>Keterangan</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataBarang as $index => $item)
                            <tr data-id="{{ $item->id }}" 
                                data-nama="{{ $item->nama_barang }}" 
                                data-type="{{ $item->type }}" 
                                data-merk="{{ $item->merk }}"
                                data-harga="{{ $item->harga }}"
                                data-jumlah="{{ $item->jumlah }}"
                                data-ket="{{ $item->keterangan }}">
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $item->nama_barang }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->type == 'Uang' ? 'success' : 'warning' }}-subtle text-{{ $item->type == 'Uang' ? 'success' : 'warning' }} px-3 py-2">
                                        {{ $item->type }}
                                    </span>
                                </td>
                                <td>
                                    @if($item->merk == '-' || empty($item->merk))
                                        <span class="text-muted">-</span>
                                    @else
                                        <span class="text-dark fw-medium">{{ $item->merk }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $item->jumlah }}</span>
                                </td>
                                <td>
                                    @if($item->keterangan == '-' || empty($item->keterangan))
                                        <span class="text-muted">-</span>
                                    @else
                                        <span class="text-muted">{{ $item->keterangan }}</span>
                                    @endif
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
                    <h5 class="modal-title" id="modalTitle">Tambah Data Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formDataBarang">
                        <input type="hidden" id="editId" value="">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="namaBarang" placeholder="Masukkan nama barang" maxlength="255" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" id="type">
                                    <option value="">-- Pilih Type --</option>
                                    <option value="Uang">Uang</option>
                                    <option value="Barang">Barang</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Merk</label>
                                <input type="text" class="form-control" id="merk" placeholder="Masukkan merk" maxlength="50">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="harga" placeholder="0" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Barang <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="jumlahBarang" placeholder="0" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" rows="3" placeholder="Masukkan keterangan" maxlength="255"></textarea>
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
            table = $('#tabelDataBarang').DataTable({
                ordering: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
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

        // Format Currency
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // Get Icon based on type
        function getIconByType(type) {
            return type === 'Uang' ? 'ti-cash' : 'ti-device-mobile';
        }

        // Get Color based on type
        function getColorByType(type) {
            return type === 'Uang' ? 'success' : 'primary';
        }

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Data Barang';
            document.getElementById('formDataBarang').reset();
            document.getElementById('editId').value = '';

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
            const type = row.getAttribute('data-type');
            const merk = row.getAttribute('data-merk');
            const harga = row.getAttribute('data-harga');
            const jumlah = row.getAttribute('data-jumlah');
            const ket = row.getAttribute('data-ket');

            document.getElementById('modalTitle').textContent = 'Ubah Data Barang';
            document.getElementById('editId').value = id;
            document.getElementById('namaBarang').value = nama;
            document.getElementById('type').value = type;
            document.getElementById('merk').value = merk === '-' ? '' : merk;
            document.getElementById('harga').value = formatRupiah(harga);
            document.getElementById('jumlahBarang').value = jumlah;
            document.getElementById('keterangan').value = ket === '-' ? '' : ket;

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formDataBarang');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const editId = document.getElementById('editId').value;
            const namaBarang = document.getElementById('namaBarang').value;
            const type = document.getElementById('type').value || 'Barang';
            const merk = document.getElementById('merk').value || '-';
            const harga = document.getElementById('harga').value.replace(/\./g, '') || '0';
            const jumlahBarang = document.getElementById('jumlahBarang').value || '0';
            const keterangan = document.getElementById('keterangan').value || '-';

            if (editId) {
                // Update existing row
                const rows = document.querySelectorAll('#tabelDataBarang tbody tr');
                rows.forEach(row => {
                    if (row.getAttribute('data-id') === editId) {
                        row.setAttribute('data-nama', namaBarang);
                        row.setAttribute('data-type', type);
                        row.setAttribute('data-merk', merk);
                        row.setAttribute('data-harga', harga);
                        row.setAttribute('data-jumlah', jumlahBarang);
                        row.setAttribute('data-ket', keterangan);

                        const typeBadgeColor = type === 'Uang' ? 'success' : 'warning';

                        row.cells[1].innerHTML = `
                            <div class="d-flex align-items-center">
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-semibold text-dark">${namaBarang}</h6>
                                </div>
                            </div>
                        `;
                        row.cells[2].innerHTML = `<span class="badge bg-${typeBadgeColor}-subtle text-${typeBadgeColor} px-3 py-2">${type}</span>`;
                        row.cells[3].innerHTML = merk === '-' ? '<span class="text-muted">-</span>' : `<span class="text-dark fw-medium">${merk}</span>`;
                        row.cells[4].innerHTML = `<span class="fw-bold text-dark">Rp ${formatRupiah(harga)}</span>`;
                        row.cells[5].innerHTML = `<span class="badge bg-primary-subtle text-primary px-3 py-2">${jumlahBarang}</span>`;
                        row.cells[6].innerHTML = keterangan === '-' ? '<span class="text-muted">-</span>' : `<span class="text-muted">${keterangan}</span>`;
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
                const typeBadgeColor = type === 'Uang' ? 'success' : 'warning';

                const newRow = `
                    <tr data-id="${newId}" data-nama="${namaBarang}" data-type="${type}" data-merk="${merk}" data-harga="${harga}" data-jumlah="${jumlahBarang}" data-ket="${keterangan}">
                        <td class="text-center text-muted fw-medium"></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-semibold text-dark">${namaBarang}</h6>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge bg-${typeBadgeColor}-subtle text-${typeBadgeColor} px-3 py-2">${type}</span></td>
                        <td>${merk === '-' ? '<span class="text-muted">-</span>' : `<span class="text-dark fw-medium">${merk}</span>`}</td>
                        <td class="text-end"><span class="fw-bold text-dark">Rp ${formatRupiah(harga)}</span></td>
                        <td class="text-center"><span class="badge bg-primary-subtle text-primary px-3 py-2">${jumlahBarang}</span></td>
                        <td>${keterangan === '-' ? '<span class="text-muted">-</span>' : `<span class="text-muted">${keterangan}</span>`}</td>
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
            const headers = ['No', 'Nama Barang', 'Type', 'Merk', 'Harga', 'Jumlah', 'Keterangan'];
            rows.push(headers);

            table.rows({ search: 'applied' }).every(function () {
                const row = this.node();
                const no = this.index() + 1;
                const nama = row.getAttribute('data-nama');
                const type = row.getAttribute('data-type');
                const merk = row.getAttribute('data-merk');
                const harga = row.getAttribute('data-harga');
                const jumlah = row.getAttribute('data-jumlah');
                const ket = row.getAttribute('data-ket');

                rows.push([no, nama, type, merk, harga, jumlah, ket]);
            });

            let csvContent = '\ufeff';
            csvContent += rows.map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const tanggal = new Date().toISOString().slice(0, 10);
            link.setAttribute('href', url);
            link.setAttribute('download', `Data_Barang_${tanggal}.csv`);
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

        // Format Currency Input
        document.getElementById('harga').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            e.target.value = value;
        });
    </script>
@endpush