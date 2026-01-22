@extends('layouts.app')

@section('title', 'Master Data - Jenis Akun')

@push('styles')
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Master Data - Jenis Akun</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data jenis akun koperasi</p>
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Jenis Akun">
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
                <table id="tabelJenisAkun" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle" width="50px">No</th>
                            <th>Kode Aktiva</th>
                            <th>Jenis Transaksi</th>
                            <th>Akun</th>
                            <th class="text-center">Pemasukan</th>
                            <th class="text-center">Pengeluaran</th>
                            <th class="text-center">Aktif</th>
                            <th class="text-center">Laba Rugi</th>
                            <th class="text-center" width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jenisAkun as $index => $item)
                            <tr data-id="{{ $item->id }}" data-kd="{{ $item->kd_aktiva }}"
                                data-jns="{{ $item->jns_transaksi }}" data-akun="{{ $item->akun }}"
                                data-pemasukan="{{ $item->pemasukan }}" data-pengeluaran="{{ $item->pengeluaran }}"
                                data-aktif="{{ $item->aktif }}" data-laba="{{ $item->laba_rugi }}">
                                <td class="text-center text-muted fw-medium"></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $item->kd_aktiva }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->jns_transaksi }}</div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-muted">{{ $item->akun }}</span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->pemasukan == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->pemasukan == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->pemasukan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->pengeluaran == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->pengeluaran == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->pengeluaran }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge bg-{{ $item->aktif == 'Y' ? 'success' : 'danger' }}-subtle text-{{ $item->aktif == 'Y' ? 'success' : 'danger' }} fw-semibold px-3 py-1">
                                        {{ $item->aktif }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($item->laba_rugi)
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">{{ $item->laba_rugi }}</span>
                                    @else
                                        -
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Jenis Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formJenisAkun">
                        @csrf
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Kode Aktiva <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kdAktiva" placeholder="Masukkan kode aktiva"
                                maxlength="10" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jnsTransaksi" placeholder="Masukkan jenis transaksi"
                                maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="akun" required>
                                <option value="">-- Pilih Akun --</option>
                                <option value="Aktiva">Aktiva</option>
                                <option value="Pasiva">Pasiva</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pemasukan <span class="text-danger">*</span></label>
                            <select class="form-select" id="pemasukan" required>
                                <option value="Y" selected>Y</option>
                                <option value="T">T</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pengeluaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="pengeluaran" required>
                                <option value="Y" selected>Y</option>
                                <option value="T">T</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Aktif <span class="text-danger">*</span></label>
                            <select class="form-select" id="aktif" required>
                                <option value="Y" selected>Y</option>
                                <option value="T">T</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Laba Rugi</label>
                            <select class="form-select" id="labaRugi">
                                <option value="">-- Pilih --</option>
                                <option value="PENDAPATAN">PENDAPATAN</option>
                                <option value="BIAYA">BIAYA</option>
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

        // ===============================
        // INIT DATATABLE
        // ===============================
        $(document).ready(function () {
            table = $('#tabelJenisAkun').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/i18n/id.json') }}"
                },
                pageLength: 10,
                order: [],
                columnDefs: [
                    { orderable: false, targets: [0, 8] }
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
        // TAMBAH DATA
        // ===============================
        function tambahData() {
            document.getElementById('modalTitle').innerText = 'Tambah Jenis Akun';
            document.getElementById('formJenisAkun').reset();
            document.getElementById('editId').value = '';
            document.getElementById('aktif').value = 'Y';

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

            document.getElementById('modalTitle').innerText = 'Ubah Jenis Akun';
            document.getElementById('editId').value = row.dataset.id;
            document.getElementById('kdAktiva').value = row.dataset.kd;
            document.getElementById('jnsTransaksi').value = row.dataset.jns;
            document.getElementById('akun').value = row.dataset.akun;
            document.getElementById('pemasukan').value = row.dataset.pemasukan;
            document.getElementById('pengeluaran').value = row.dataset.pengeluaran;
            document.getElementById('aktif').value = row.dataset.aktif;
            document.getElementById('labaRugi').value = row.dataset.laba;

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
            const kd_aktiva = document.getElementById('kdAktiva').value;
            const jns_transaksi = document.getElementById('jnsTransaksi').value;
            const akun = document.getElementById('akun').value;
            const pemasukan = document.getElementById('pemasukan').value;
            const pengeluaran = document.getElementById('pengeluaran').value;
            const aktif = document.getElementById('aktif').value;
            const laba_rugi = document.getElementById('labaRugi').value;

            const url = id
                ? `/admin/jenis-akun/${id}`
                : `/admin/jenis-akun`;

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    kd_aktiva,
                    jns_transaksi,
                    akun,
                    pemasukan,
                    pengeluaran,
                    aktif,
                    laba_rugi
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
                    fetch(`/admin/jenis-akun/${id}`, {
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
            window.location.href = "{{ route('master.jenis-akun.cetak') }}";
        }

        function eksporData() {
            window.location.href = "{{ route('master.jenis-akun.export') }}";
        }
    </script>

@endpush