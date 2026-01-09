@extends('layouts.app')

@section('title', 'Master Data - Data Pengguna')

@push('styles')
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
                    <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
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
                            <th class="text-center align-middle" width="50px">No</th>
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
                                <td class="text-center text-muted fw-medium"></td>
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
                        @csrf
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
                            <label class="form-label">Password <span id="passLabel" class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password"
                                placeholder="Masukkan password" required>
                            <small class="text-muted" id="passHint" style="display: none;">Kosongkan jika tidak ingin mengubah password</small>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" required>
                                <option value="Y" selected>Aktif</option>
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

    <script>
        let table;

        // Init DataTable
        $(document).ready(function () {
            table = $('#tabelPengguna').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/i18n/id.json') }}"
                },
                pageLength: 10,
                order: [],
                columnDefs: [
                    { orderable: false, targets: [0, 4] }
                ]
            });

            // Fix nomor urut
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' })
                    .nodes()
                    .each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
            }).draw();
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
            document.getElementById('modalTitle').innerText = 'Tambah Data Pengguna';
            document.getElementById('formPengguna').reset();
            document.getElementById('editId').value = '';
            document.getElementById('password').required = true;
            document.getElementById('passLabel').innerHTML = '<span class="text-danger">*</span>';
            document.getElementById('passHint').style.display = 'none';
            document.getElementById('status').value = 'Y';

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // Edit Data
        function editData(btn) {
            const row = btn.closest('tr');

            document.getElementById('modalTitle').innerText = 'Ubah Data Pengguna';
            document.getElementById('editId').value = row.dataset.id;
            document.getElementById('username').value = row.dataset.username;
            document.getElementById('level').value = row.dataset.level;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('passLabel').textContent = 'Password';
            document.getElementById('passHint').style.display = 'block';
            document.getElementById('status').value = row.dataset.status;

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formPengguna');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const id = document.getElementById('editId').value;
            const username = document.getElementById('username').value;
            const level = document.getElementById('level').value;
            const password = document.getElementById('password').value;
            const status = document.getElementById('status').value;

            const url = id
                ? `/admin/data-pengguna/${id}`
                : `/admin/data-pengguna`;

            const payload = { username, level, status };
            if (password) {
                payload.password = password;
            }

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
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

        // Hapus Data
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
                    fetch(`/admin/data-pengguna/${id}`, {
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

        // Cari Data
        function cariData() {
            table.search(document.getElementById('searchInput').value).draw();
        }

        // Reset Filter
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

        // Cetak & Export
        function cetakLaporan() {
            window.location.href = "{{ route('master.data-pengguna.cetak') }}";
        }

        function eksporData() {
            window.location.href = "{{ route('master.data-pengguna.export') }}";
        }
    </script>

@endpush