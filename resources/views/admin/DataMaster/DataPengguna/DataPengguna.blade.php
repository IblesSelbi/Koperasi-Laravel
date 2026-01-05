@extends('layouts.app')

@section('title', 'Master Data - Data Pengguna')

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
                    <h4 class="fw-semibold mb-1">Master Data - Data Pengguna</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data pengguna sistem koperasi</p>
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
                    <button class="btn btn-info btn-sm" onclick="window.print()">
                        <i class="ti ti-printer"></i> Cetak
                    </button>
                </div>
                <div class="col-lg-auto ms-auto">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Pengguna">
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
                <table id="tabelPengguna" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" width="50px">No</th>
                            <th>Username</th>
                            <th>Level</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataPengguna as $index => $item)
                            <tr data-id="{{ $item->id }}" data-username="{{ $item->username }}"
                                data-level="{{ $item->level }}" data-status="{{ $item->status }}">
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="ti ti-user fs-5"></i>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $item->username }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $levelColors = [
                                            'admin' => 'danger',
                                            'operator' => 'primary',
                                            'pinjaman' => 'info'
                                        ];
                                        $color = $levelColors[$item->level] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2">{{ $item->level }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->status === 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->status === 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->status === 'Y' ? 'Aktif' : 'Non Aktif' }}
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Data Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formPengguna">
                        <input type="hidden" id="editId" value="">

                        <!-- Username -->
                        <div class="mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" placeholder="Masukkan username"
                                maxlength="255" required>
                        </div>

                        <!-- Level -->
                        <div class="mb-3">
                            <label class="form-label">Level <span class="text-danger">*</span></label>
                            <select class="form-select" id="level" required>
                                <option value="">-- Pilih Level --</option>
                                <option value="admin">Admin</option>
                                <option value="operator">Operator</option>
                                <option value="pinjaman">Pinjaman</option>
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label">Password <span id="passLabel"></span></label>
                            <input type="password" class="form-control" id="password"
                                placeholder="Masukkan password">
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" required>
                                <option value="Y">Aktif</option>
                                <option value="N">Non Aktif</option>
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
        $(document).ready(function() {
            table = $('#tabelPengguna').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: [4]
                }]
            });
        });

        // Get Level Color
        function getLevelColor(level) {
            const colors = {
                admin: 'danger',
                operator: 'primary',
                pinjaman: 'info'
            };
            return colors[level] || 'secondary';
        }

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Data Pengguna';
            document.getElementById('formPengguna').reset();
            document.getElementById('editId').value = '';
            document.getElementById('passLabel').innerHTML = '<span class="text-danger">*</span>';
            document.getElementById('password').required = true;
            document.getElementById('status').value = 'Y';

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Edit Data
        function editData(btn) {
            const row = btn.closest('tr');

            document.getElementById('modalTitle').textContent = 'Ubah Data Pengguna';
            document.getElementById('editId').value = row.getAttribute('data-id');
            document.getElementById('username').value = row.getAttribute('data-username');
            document.getElementById('level').value = row.getAttribute('data-level');
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passLabel').textContent = '';
            document.getElementById('status').value = row.getAttribute('data-status');

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formPengguna');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const editId = document.getElementById('editId').value;
            const username = document.getElementById('username').value;
            const level = document.getElementById('level').value;
            const status = document.getElementById('status').value;

            if (editId) {
                // Update existing row
                const rows = document.querySelectorAll('#tabelPengguna tbody tr');
                rows.forEach(row => {
                    if (row.getAttribute('data-id') === editId) {
                        // Update data attributes
                        row.setAttribute('data-username', username);
                        row.setAttribute('data-level', level);
                        row.setAttribute('data-status', status);

                        const color = getLevelColor(level);

                        // Update table cells
                        row.cells[1].innerHTML = `
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="ti ti-user fs-5"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-semibold text-dark">${username}</h6>
                                </div>
                            </div>
                        `;
                        row.cells[2].innerHTML =
                            `<span class="badge bg-${color}-subtle text-${color} px-3 py-2">${level}</span>`;
                        row.cells[3].innerHTML = `
                            <span class="badge bg-${status === 'Y' ? 'success' : 'danger'}-subtle text-${status === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">
                                ${status === 'Y' ? 'Aktif' : 'Non Aktif'}
                            </span>
                        `;
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
                const rowCount = table.rows().count() + 1;
                const color = getLevelColor(level);

                const newRow = `
                    <tr data-id="${newId}"
                        data-username="${username}"
                        data-level="${level}"
                        data-status="${status}">
                        <td class="text-center text-muted fw-medium">${rowCount}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="ti ti-user fs-5"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0 fw-semibold text-dark">${username}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-${color}-subtle text-${color} px-3 py-2">${level}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-${status === 'Y' ? 'success' : 'danger'}-subtle text-${status === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">
                                ${status === 'Y' ? 'Aktif' : 'Non Aktif'}
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

            bootstrap.Modal.getInstance(document.getElementById('modalForm')).hide();
        }

        // Hapus Data
        function hapusData(btn) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
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
            table.search(document.getElementById('searchInput').value).draw();
        }

        // Reset Filter
        function resetFilter() {
            document.getElementById('searchInput').value = '';
            table.search('').draw();
        }

        // Ekspor Data
        function eksporData() {
            const rows = Array.from(document.querySelectorAll('#tabelPengguna tbody tr'));
            const csv = '\ufeffNo,Username,Level,Status\n' +
                rows.map((row, i) => {
                    const username = row.getAttribute('data-username');
                    const level = row.getAttribute('data-level');
                    const status = row.getAttribute('data-status') === 'Y' ? 'Aktif' : 'Non Aktif';
                    return `${i + 1},${username},${level},${status}`;
                }).join('\n');

            const blob = new Blob([csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Data_Pengguna_${new Date().toISOString().slice(0, 10)}.csv`;
            link.click();
        }
    </script>
@endpush