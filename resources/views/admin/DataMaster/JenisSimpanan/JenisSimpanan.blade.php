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
                <table id="tabelJenisSimpanan" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
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
                            <tr data-id="{{ $item->id }}" data-jns="{{ $item->jenis_simpanan }}"
                                data-jumlah="{{ $item->jumlah }}" data-tampil="{{ $item->tampil }}">
                                <td class="text-center text-muted fw-medium"></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->jenis_simpanan }}</div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->tampil == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->tampil == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
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
                        @csrf
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Jenis Simpanan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jnsSimpanan" placeholder="Masukkan jenis simpanan"
                                maxlength="30" required>
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
    <script src="{{ asset('assets/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/datatables/js/buttons.bootstrap5.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('assets/sweetalert/sweetalert2.all.min.js') }}"></script>


    <script>
        let table;

        // ===============================
        // INIT DATATABLE
        // ===============================
        $(document).ready(function () {
            table = $('#tabelJenisSimpanan').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/i18n/id.json') }}"
                },
                pageLength: 10,
                order: [],
                columnDefs: [
                    { orderable: false, targets: [0, 4] }
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

        // ===============================
        // FORMAT RUPIAH
        // ===============================
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // ===============================
        // TAMBAH DATA
        // ===============================
        function tambahData() {
            document.getElementById('modalTitle').innerText = 'Tambah Jenis Simpanan';
            document.getElementById('formJenisSimpanan').reset();
            document.getElementById('editId').value = '';
            document.getElementById('tampil').value = 'Y';

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // ===============================
        // EDIT DATA
        // ===============================
        function editData(btn) {
            const row = btn.closest('tr');

            document.getElementById('modalTitle').innerText = 'Ubah Jenis Simpanan';
            document.getElementById('editId').value = row.dataset.id;
            document.getElementById('jnsSimpanan').value = row.dataset.jns;
            document.getElementById('jumlah').value = formatRupiah(row.dataset.jumlah);
            document.getElementById('tampil').value = row.dataset.tampil;

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // ===============================
        // SIMPAN DATA (ADD & UPDATE)
        // ===============================
        function simpanData() {
            const id = document.getElementById('editId').value;
            const jenis_simpanan = document.getElementById('jnsSimpanan').value;
            const jumlah = document.getElementById('jumlah').value.replace(/\./g, '');
            const tampil = document.getElementById('tampil').value;

            const url = id
                ? `/admin/jenis-simpanan/${id}`
                : `/admin/jenis-simpanan`;

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    jenis_simpanan,
                    jumlah,
                    tampil
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

        // ===============================
        // HAPUS DATA
        // ===============================
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
                    fetch(`/admin/jenis-simpanan/${id}`, {
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

        // ===============================
        // CARI DATA
        // ===============================
        function cariData() {
            table.search(document.getElementById('searchInput').value).draw();
        }

        // ===============================
        // RESET FILTER
        // ===============================
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

        // ===============================
        // CETAK & EXPORT (SERVER SIDE)
        // ===============================
        function cetakLaporan() {
            window.location.href = "{{ route('master.jenis-simpanan.cetak') }}";
        }

        function eksporData() {
            window.location.href = "{{ route('master.jenis-simpanan.export') }}";
        }

        // ===============================
        // FORMAT INPUT JUMLAH
        // ===============================
        document.getElementById('jumlah').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value ? formatRupiah(value) : '';
        });
    </script>

@endpush