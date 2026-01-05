@extends('layouts.app')

@section('title', 'Pemasukan Kas')

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
                    <h4 class="fw-semibold mb-1">Transaksi Pemasukan Kas</h4>
                    <p class="text-muted fs-3 mb-0">Kelola transaksi pemasukan kas tunai</p>
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

    <!-- Toolbar Card -->
    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-lg-auto">
                    <button class="btn btn-primary btn-sm" onclick="tambahData()">
                        <i class="ti ti-plus"></i> Tambah
                    </button>
                    <button class="btn btn-warning btn-sm" onclick="editData()">
                        <i class="ti ti-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="hapusData()">
                        <i class="ti ti-trash"></i> Hapus
                    </button>
                </div>
                <div class="col-lg-auto ms-auto">
                    <div class="input-group input-group-sm" style="width: 180px;">
                        <input type="date" class="form-control" id="filterTanggal" placeholder="Pilih Tanggal">
                    </div>
                </div>
                <div class="col-lg-auto">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari Kode Transaksi">
                        <button class="btn btn-primary" onclick="cariData()">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-lg-auto">
                    <button class="btn btn-info btn-sm" onclick="cetakLaporan()">
                        <i class="ti ti-printer"></i> Cetak
                    </button>
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
                <table id="tabelPemasukan" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" width="50px">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th class="text-center align-middle" width="20px">No</th>
                            <th>Kode Transaksi</th>
                            <th>Tanggal Transaksi</th>
                            <th>Uraian</th>
                            <th>Untuk Kas</th>
                            <th>Dari Akun</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="row-checkbox form-check-input">
                            </td>
                            <td class="text-center text-muted fw-medium">1</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                    TRX00001
                                </span>
                            </td>
                            <td class="text-muted">15 Desember 2025</td>
                            <td>
                                <div class="fw-semibold text-dark mb-1">Setoran Awal</div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info fw-semibold px-2 py-1">
                                    Kas Tunai
                                </span>
                            </td>
                            <td class="text-muted">Modal Awal</td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-4">
                                    Rp 5.000.000
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge border border-secondary text-secondary px-3 py-1 fw-semibold">
                                    Admin
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="row-checkbox form-check-input">
                            </td>
                            <td class="text-center text-muted fw-medium">2</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                    TRX00002
                                </span>
                            </td>
                            <td class="text-muted">14 Desember 2025</td>
                            <td>
                                <div class="fw-semibold text-dark mb-1">Pendapatan Bunga</div>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning fw-semibold px-2 py-1">
                                    Kas Besar
                                </span>
                            </td>
                            <td class="text-muted">Pendapatan Lainnya</td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-4">
                                    Rp 1.500.000
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge border border-secondary text-secondary px-3 py-1 fw-semibold">
                                    Admin
                                </span>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr class="table-light">
                            <td colspan="7" class="text-end fw-bolder fs-4">Total Pengeluaran:</td>
                            <td class="text-end">
                                <span class="fw-bold text-success fs-4 fw-bolder">
                                    Rp {{ number_format($total_pengeluaran ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modalForm" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Data Pemasukan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formPemasukan" method="POST" action="#">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="id" id="formId">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="tglTransaksi" name="tanggal_transaksi"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="jumlah" name="jumlah" placeholder="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <input type="text" class="form-control" id="keterangan" name="keterangan"
                                placeholder="Masukkan keterangan">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dari Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="dariAkun" name="dari_akun" required>
                                <option value="">-- Pilih Jenis Akun --</option>
                                @foreach($akun_list as $akun)
                                    <option value="{{ $akun->id ?? '' }}">
                                        {{ $akun->nama ?? '-' }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-device-floppy"></i> Simpan
                        </button>
                    </div>
                </form>
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

    <script>
        $(document).ready(function () {
            // Initialize DataTable
            var table = $('#tabelPemasukan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0, 1] },
                    {
                        targets: 1,
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }
                ]
            });

            // Click row to select checkbox
            $('#tabelPemasukan tbody').on('click', 'tr', function (e) {
                if ($(e.target).is('input[type="checkbox"]')) {
                    e.stopPropagation();
                    updateSelectAllState();
                    return;
                }

                const checkbox = $(this).find('.row-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                updateSelectAllState();
            });

            // Select All Checkbox
            $('#selectAll').on('click', function (e) {
                e.stopPropagation();
                $('.row-checkbox').prop('checked', this.checked);
            });

            // Individual checkbox change
            $(document).on('change', '.row-checkbox', function (e) {
                e.stopPropagation();
                updateSelectAllState();
            });

            // Update selectAll state
            function updateSelectAllState() {
                const checkboxes = $('.row-checkbox');
                const checkedCheckboxes = $('.row-checkbox:checked');
                const selectAll = $('#selectAll');

                if (checkedCheckboxes.length === 0) {
                    selectAll.prop('checked', false);
                    selectAll.prop('indeterminate', false);
                } else if (checkedCheckboxes.length === checkboxes.length) {
                    selectAll.prop('checked', true);
                    selectAll.prop('indeterminate', false);
                } else {
                    selectAll.prop('checked', false);
                    selectAll.prop('indeterminate', true);
                }
            }

            // Format Currency Input
            $('#jumlah').on('input', function (e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                }
                e.target.value = value;
            });

            // Set default datetime on modal open
            $('#modalForm').on('show.bs.modal', function () {
                if ($('#modalTitle').text() === 'Tambah Data Pemasukan') {
                    const now = new Date();
                    $('#tglTransaksi').val(now.toISOString().slice(0, 16));
                }
            });

            // Fix checkbox styling
            setTimeout(() => {
                const theadCheckbox = document.getElementById('selectAll');
                if (theadCheckbox) {
                    theadCheckbox.style.backgroundColor = 'white';
                    theadCheckbox.style.borderColor = '#495057';
                    theadCheckbox.style.accentColor = '#0d6efd';
                    theadCheckbox.style.width = '18px';
                    theadCheckbox.style.height = '18px';
                    theadCheckbox.style.cursor = 'pointer';
                }

                document.querySelectorAll('.row-checkbox').forEach(cb => {
                    cb.style.cursor = 'pointer';
                    cb.style.width = '18px';
                    cb.style.height = '18px';
                    cb.style.accentColor = '#0d6efd';
                });
            }, 100);
        });

        // Functions
        function tambahData() {
            $('#modalTitle').text('Tambah Data Pemasukan');
            $('#formPemasukan')[0].reset();
            $('#formMethod').val('POST');

            const now = new Date();
            $('#tglTransaksi').val(now.toISOString().slice(0, 16));

            $('#modalForm').modal('show');
        }

        function editData() {
            const checked = $('.row-checkbox:checked');
            if (checked.length === 0) {
                alert('Pilih data yang akan diedit!');
                return;
            }
            if (checked.length > 1) {
                alert('Pilih hanya satu data!');
                return;
            }

            const id = checked.first().data('id');
            $('#modalTitle').text('Edit Data Pemasukan');
            $('#formMethod').val('PUT');
            $('#formId').val(id);
            $('#formPemasukan').attr('action', '/kas/pemasukan/' + id);

            // TODO: Load data via AJAX
            $('#modalForm').modal('show');
        }

        function hapusData() {
            const checked = $('.row-checkbox:checked');
            if (checked.length === 0) {
                alert('Pilih data yang akan dihapus!');
                return;
            }

            if (confirm('Apakah Anda yakin ingin menghapus ' + checked.length + ' data?')) {
                // TODO: Implement delete via AJAX or form submission
                alert('Fitur hapus akan diimplementasikan');
            }
        }

        function cariData() {
            const search = $('#searchInput').val();
            $('#tabelPemasukan').DataTable().search(search).draw();
        }

        function resetFilter() {
            $('#searchInput').val('');
            $('#filterTanggal').val('');
            $('#tabelPemasukan').DataTable().search('').draw();
        }

        function cetakLaporan() {
            window.print();
        }

    </script>
@endpush