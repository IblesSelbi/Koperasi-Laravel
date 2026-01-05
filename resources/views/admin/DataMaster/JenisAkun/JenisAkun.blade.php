@extends('layouts.app')

@section('title', 'Master Data - Jenis Akun Transaksi')

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
                    <h4 class="fw-semibold mb-1">Master Data - Jenis Akun Transaksi</h4>
                    <p class="text-muted fs-3 mb-0">Kelola data jenis akun transaksi koperasi</p>
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
                            <tr data-id="{{ $item->id }}" data-kd="{{ $item->kd_aktiva }}" data-jns="{{ $item->jns_transaksi }}"
                                data-akun="{{ $item->akun }}" data-pemasukan="{{ $item->pemasukan }}"
                                data-pengeluaran="{{ $item->pengeluaran }}" data-aktif="{{ $item->aktif }}"
                                data-laba="{{ $item->laba_rugi }}">
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">{{ $item->kd_aktiva }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->jns_transaksi }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ $item->akun }}</span>
                                </td>
                                <td class="text-center">
                                    @if($item->pemasukan == 'Y')
                                        <span class="badge bg-success-subtle text-success fw-semibold px-3 py-1">Y</span>
                                    @elseif($item->pemasukan == 'N')
                                        <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-1">N</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->pengeluaran == 'Y')
                                        <span class="badge bg-success-subtle text-success fw-semibold px-3 py-1">Y</span>
                                    @elseif($item->pengeluaran == 'N')
                                        <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-1">N</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->aktif == 'Y')
                                        <span class="badge bg-success-subtle text-success fw-semibold px-3 py-1">Y</span>
                                    @elseif($item->aktif == 'N')
                                        <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-1">N</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->laba_rugi)
                                        <span class="badge bg-warning-subtle text-warning fw-semibold">{{ $item->laba_rugi }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-warning me-1" onclick="editData(this)">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-danger" onclick="hapusData(this)">
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
                        <input type="hidden" id="dataId">

                        <div class="mb-3">
                            <label class="form-label">Kode Aktiva <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="kdAktiva" maxlength="5" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Transaksi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jnsTransaksi" maxlength="50" required>
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
                                <option value="">-- Pilih --</option>
                                <option value="Y">Y</option>
                                <option value="N">N</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pengeluaran <span class="text-danger">*</span></label>
                            <select class="form-select" id="pengeluaran" required>
                                <option value="">-- Pilih --</option>
                                <option value="Y">Y</option>
                                <option value="N">N</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Aktif <span class="text-danger">*</span></label>
                            <select class="form-select" id="aktif" required>
                                <option value="">-- Pilih --</option>
                                <option value="Y">Y</option>
                                <option value="N">N</option>
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
            table = $('#tabelJenisAkun').DataTable({
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

        // Tambah Data
        function tambahData() {
            document.getElementById('modalTitle').textContent = 'Tambah Jenis Akun';
            document.getElementById('formJenisAkun').reset();
            document.getElementById('dataId').value = '';

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Edit Data
        function editData(button) {
            const row = button.closest('tr');
            const id = row.getAttribute('data-id');

            document.getElementById('modalTitle').textContent = 'Edit Jenis Akun';
            document.getElementById('dataId').value = id;

            document.getElementById('kdAktiva').value = row.getAttribute('data-kd');
            document.getElementById('jnsTransaksi').value = row.getAttribute('data-jns');
            document.getElementById('akun').value = row.getAttribute('data-akun');
            document.getElementById('pemasukan').value = row.getAttribute('data-pemasukan');
            document.getElementById('pengeluaran').value = row.getAttribute('data-pengeluaran');
            document.getElementById('aktif').value = row.getAttribute('data-aktif');
            document.getElementById('labaRugi').value = row.getAttribute('data-laba');

            const modal = new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        }

        // Hapus Data
        function hapusData(button) {
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
                    const row = button.closest('tr');
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

        // Simpan Data
        function simpanData() {
            const form = document.getElementById('formJenisAkun');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const dataId = document.getElementById('dataId').value;
            const kdAktiva = document.getElementById('kdAktiva').value;
            const jnsTransaksi = document.getElementById('jnsTransaksi').value;
            const akun = document.getElementById('akun').value;
            const pemasukan = document.getElementById('pemasukan').value;
            const pengeluaran = document.getElementById('pengeluaran').value;
            const aktif = document.getElementById('aktif').value;
            const labaRugi = document.getElementById('labaRugi').value;

            if (dataId) {
                // Update existing row
                const rows = document.querySelectorAll('#tabelJenisAkun tbody tr');
                rows.forEach(row => {
                    if (row.getAttribute('data-id') === dataId) {
                        row.setAttribute('data-kd', kdAktiva);
                        row.setAttribute('data-jns', jnsTransaksi);
                        row.setAttribute('data-akun', akun);
                        row.setAttribute('data-pemasukan', pemasukan);
                        row.setAttribute('data-pengeluaran', pengeluaran);
                        row.setAttribute('data-aktif', aktif);
                        row.setAttribute('data-laba', labaRugi);

                        row.cells[1].innerHTML = `<span class="badge bg-primary-subtle text-primary fw-semibold">${kdAktiva}</span>`;
                        row.cells[2].innerHTML = `<div class="fw-semibold text-dark">${jnsTransaksi}</div>`;
                        row.cells[3].innerHTML = `<span class="badge bg-info-subtle text-info">${akun}</span>`;
                        row.cells[4].innerHTML = pemasukan ? `<span class="badge bg-${pemasukan === 'Y' ? 'success' : 'danger'}-subtle text-${pemasukan === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${pemasukan}</span>` : '-';
                        row.cells[5].innerHTML = pengeluaran ? `<span class="badge bg-${pengeluaran === 'Y' ? 'success' : 'danger'}-subtle text-${pengeluaran === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${pengeluaran}</span>` : '-';
                        row.cells[6].innerHTML = aktif ? `<span class="badge bg-${aktif === 'Y' ? 'success' : 'danger'}-subtle text-${aktif === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${aktif}</span>` : '-';
                        row.cells[7].innerHTML = labaRugi ? `<span class="badge bg-warning-subtle text-warning fw-semibold">${labaRugi}</span>` : '-';
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
                const newRow = `
                        <tr data-id="${Date.now()}" data-kd="${kdAktiva}" data-jns="${jnsTransaksi}" data-akun="${akun}" data-pemasukan="${pemasukan}" data-pengeluaran="${pengeluaran}" data-aktif="${aktif}" data-laba="${labaRugi}">
                            <td class="text-center text-muted fw-medium"></td>
                            <td><span class="badge bg-primary-subtle text-primary fw-semibold">${kdAktiva}</span></td>
                            <td><div class="fw-semibold text-dark">${jnsTransaksi}</div></td>
                            <td><span class="badge bg-info-subtle text-info">${akun}</span></td>
                            <td class="text-center">${pemasukan ? `<span class="badge bg-${pemasukan === 'Y' ? 'success' : 'danger'}-subtle text-${pemasukan === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${pemasukan}</span>` : '-'}</td>
                            <td class="text-center">${pengeluaran ? `<span class="badge bg-${pengeluaran === 'Y' ? 'success' : 'danger'}-subtle text-${pengeluaran === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${pengeluaran}</span>` : '-'}</td>
                            <td class="text-center">${aktif ? `<span class="badge bg-${aktif === 'Y' ? 'success' : 'danger'}-subtle text-${aktif === 'Y' ? 'success' : 'danger'} fw-semibold px-3 py-1">${aktif}</span>` : '-'}</td>
                            <td class="text-center">${labaRugi ? `<span class="badge bg-warning-subtle text-warning fw-semibold">${labaRugi}</span>` : '-'}</td>
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

        // Ekspor Data
        function eksporData() {
            const rows = [];
            const headers = ['No', 'Kode Aktiva', 'Jenis Transaksi', 'Akun', 'Pemasukan', 'Pengeluaran', 'Aktif', 'Laba Rugi'];
            rows.push(headers);

            table.rows({ search: 'applied' }).every(function () {
                const row = this.node();
                const no = this.index() + 1;
                const kd = row.getAttribute('data-kd');
                const jns = row.getAttribute('data-jns');
                const akun = row.getAttribute('data-akun');
                const pemasukan = row.getAttribute('data-pemasukan');
                const pengeluaran = row.getAttribute('data-pengeluaran');
                const aktif = row.getAttribute('data-aktif');
                const laba = row.getAttribute('data-laba');

                rows.push([no, kd, jns, akun, pemasukan, pengeluaran, aktif, laba]);
            });

            let csvContent = '\ufeff';
            csvContent += rows.map(row => row.join(',')).join('\n');

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            const tanggal = new Date().toISOString().slice(0, 10);
            link.setAttribute('href', url);
            link.setAttribute('download', `Jenis_Akun_${tanggal}.csv`);
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