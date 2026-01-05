@extends('layouts.app')

@section('title', 'Master Data - Lama Angsuran')

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
                    <h4 class="fw-semibold mb-1">Master Data - Lama Angsuran</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data lama angsuran (dalam bulan)</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Lama Angsuran">
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
                <table id="tabelLamaAngsuran" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="50px">No</th>
                            <th>Lama Angsuran (Bulan)</th>
                            <th class="text-center">Aktif</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $colors = ['primary', 'info', 'warning', 'success', 'danger', 'secondary'];
                        @endphp
                        @foreach($lamaAngsuran as $index => $item)
                            @php
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <tr data-id="{{ $item->id }}" data-lama="{{ $item->lama_angsuran }}"
                                data-aktif="{{ $item->aktif }}">
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-{{ $color }}-subtle text-{{ $color }} rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="ti ti-calendar-time fs-5"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $item->lama_angsuran }} Bulan</h6>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->aktif == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->aktif == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->aktif }}
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
                    <h5 class="modal-title" id="modalTitle">Tambah Lama Angsuran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formLamaAngsuran">
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Lama Angsuran (Bulan) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="lamaAngsuran" placeholder="Masukkan lama angsuran"
                                min="1" max="120" required>
                            <div class="form-text">Masukkan jumlah bulan (contoh: 3, 6, 12, 24, 36)</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Aktif</label>
                            <select class="form-select" id="aktif">
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
            table = $('#tabelLamaAngsuran').DataTable({
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

        // Function untuk mendapatkan warna icon
        function getIconColor(bulan) {
            const colors = ['primary', 'info', 'warning', 'success', 'danger', 'secondary'];
            return colors[Math.floor(Math.random() * colors.length)];
        }

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Lama Angsuran';
            document.getElementById('formLamaAngsuran').reset();
            document.getElementById('editId').value = '';
            document.getElementById('aktif').value = 'Y';

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
            const lama = row.getAttribute('data-lama');
            const aktif = row.getAttribute('data-aktif');

            document.getElementById('modalTitle').textContent = 'Ubah Lama Angsuran';
            document.getElementById('editId').value = id;
            document.getElementById('lamaAngsuran').value = lama;
            document.getElementById('aktif').value = aktif;

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formLamaAngsuran');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const editId = document.getElementById('editId').value;
            const lamaAngsuran = document.getElementById('lamaAngsuran').value;
            const aktif = document.getElementById('aktif').value || 'Y';

            if (editId) {
                // Update existing row
                const rows = document.querySelectorAll('#tabelLamaAngsuran tbody tr');
                rows.forEach(row => {
                    if (row.getAttribute('data-id') === editId) {
                        row.setAttribute('data-lama', lamaAngsuran);
                        row.setAttribute('data-aktif', aktif);

                        const color = getIconColor(lamaAngsuran);

                        row.cells[1].innerHTML = `
                                <div class="d-flex align-items-center">
                                    <div class="bg-${color}-subtle text-${color} rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="ti ti-calendar-time fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-semibold text-dark">${lamaAngsuran} Bulan</h6>
                                    </div>
                                </div>
                            `;
                        row.cells[2].innerHTML = `<span class="badge bg-${aktif === 'Y' ? 'success' : 'danger'}-subtle text-${aktif === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${aktif}</span>`;
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
                const color = getIconColor(lamaAngsuran);

                const newRow = `
                        <tr data-id="${newId}" data-lama="${lamaAngsuran}" data-aktif="${aktif}">
                            <td class="text-center text-muted fw-medium"></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-${color}-subtle text-${color} rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="ti ti-calendar-time fs-5"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h6 class="mb-0 fw-semibold text-dark">${lamaAngsuran} Bulan</h6>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center"><span class="badge bg-${aktif === 'Y' ? 'success' : 'danger'}-subtle text-${aktif === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${aktif}</span></td>
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
            const headers = ['No', 'Lama Angsuran (Bulan)', 'Aktif'];
            rows.push(headers);

            table.rows({ search: 'applied' }).every(function () {
                const row = this.node();
                const no = this.index() + 1;
                const lama = row.getAttribute('data-lama');
                const aktif = row.getAttribute('data-aktif');

                rows.push([no, lama, aktif]);
            });

            let csvContent = '\ufeff';
            csvContent += rows.map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const tanggal = new Date().toISOString().slice(0, 10);
            link.setAttribute('href', url);
            link.setAttribute('download', `Lama_Angsuran_${tanggal}.csv`);
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