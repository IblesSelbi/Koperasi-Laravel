@extends('layouts.app')

@section('title', 'Penarikan Tunai')

@push('styles')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <style>
  
</style>

@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Transaksi Penarikan Tunai</h4>
                    <p class="text-muted fs-3 mb-0">Kelola transaksi penarikan tunai simpanan anggota</p>
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
                    <button class="btn btn-warning btn-sm" onclick="editData()">
                        <i class="ti ti-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="hapusData()">
                        <i class="ti ti-trash"></i> Hapus
                    </button>
                </div>
                <div class="col-lg-auto ms-auto">
                    <select class="form-select form-select-sm" id="filterSimpanan" style="width: 200px;">
                        <option value="">-- Tampilkan Akun --</option>
                        <option value="Simpanan Sukarela">Simpanan Sukarela</option>
                        <option value="Simpanan Pokok">Simpanan Pokok</option>
                        <option value="Simpanan Wajib">Simpanan Wajib</option>
                    </select>
                </div>
                <div class="col-lg-auto">
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
                <table id="tabelPenarikan" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center" width="50px">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th class="text-center align-middle" width="20px">No</th>
                            <th>Kode Transaksi</th>
                            <th>Tanggal Transaksi</th>
                            <th>ID Anggota</th>
                            <th>Nama Anggota</th>
                            <th>Departemen</th>
                            <th>Jenis Simpanan</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">User</th>
                            <th class="text-center" width="75px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penarikan as $index => $item)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="row-checkbox form-check-input" data-id="{{ $item->id }}">
                                </td>
                                <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item->kode_transaksi }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d F Y') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info">{{ $item->id_anggota }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark mb-0">{{ $item->nama_anggota }}</div>
                                </td>
                                <td class="text-muted">{{ $item->departemen }}</td>
                                <td>
                                    <span class="badge text-dark fw-semibold">{{ $item->jenis_simpanan }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-danger fs-4">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary px-3 py-1 fw-semibold">
                                        {{ $item->user }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" onclick="cetakNota(this)">
                                        <i class="ti ti-printer"></i> Nota
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    <i class="ti ti-database-off fs-1 mb-2"></i>
                                    <p class="mb-0">Tidak ada data penarikan tunai</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($penarikan->count() > 0)
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="8" class="text-end fw-bolder fs-4">Total Penarikan:</td>
                                <td class="text-end">
                                    <span class="fw-bold text-danger fs-4 fw-bolder">
                                        Rp {{ number_format($total_penarikan ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
  <!-- Modal Form Penarikan Tunai -->
<div class="modal fade" id="modalForm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Data Penarikan Tunai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Form -->
            <form id="formPenarikan" method="POST" action="{{ route('simpanan.penarikan.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="formId">

                <!-- Modal Body (SCROLL AREA) -->
                <div class="modal-body">

                    <div class="row">
                        <!-- LEFT -->
                        <div class="col-md-8">

                            <div class="mb-3">
                                <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="tglTransaksi"
                                    name="tanggal_transaksi" required>
                            </div>

                            <h6 class="fw-semibold mb-3 text-primary">Identitas Penarik</h6>

                            <div class="mb-3">
                                <label class="form-label">Nama Penarik</label>
                                <input type="text" class="form-control" name="nama_penarik">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nomor Identitas</label>
                                <input type="text" class="form-control" name="no_identitas">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2"></textarea>
                            </div>

                            <h6 class="fw-semibold mb-3 text-primary mt-4">Identitas Anggota</h6>

                            <div class="mb-3">
                                <label class="form-label">Nama Anggota <span class="text-danger">*</span></label>
                                <select class="form-select" id="namaAnggota" name="anggota_id" required>
                                    <option value="">-- Pilih Anggota --</option>
                                    @foreach($anggota_list as $anggota)
                                        <option value="{{ $anggota->id }}">
                                            {{ $anggota->id_anggota }} - {{ $anggota->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Simpanan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenisSimpanan" name="jenis_simpanan" required>
                                    <option value="">-- Pilih Simpanan --</option>
                                    <option value="Simpanan Sukarela">Simpanan Sukarela</option>
                                    <option value="Simpanan Pokok">Simpanan Pokok</option>
                                    <option value="Simpanan Wajib">Simpanan Wajib</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Penarikan <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="jumlah" name="jumlah" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ambil Dari Kas <span class="text-danger">*</span></label>
                                <select class="form-select" name="kas" required>
                                    <option value="">-- Pilih Kas --</option>
                                    <option value="Kas Tunai">Kas Tunai</option>
                                    <option value="Kas Besar">Kas Besar</option>
                                </select>
                            </div>
                        </div>

                        <!-- RIGHT -->
                        <div class="col-md-4">
                            <label class="form-label">Foto Anggota</label>
                            <div class="border rounded p-3 text-center" style="min-height: 200px;">
                                <div id="fotoAnggota"
                                    class="d-flex align-items-center justify-content-center"
                                    style="height: 180px;">
                                    <span class="text-muted">Foto akan muncul setelah memilih anggota</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
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
        let table;
        $(document).ready(function () {
            // Hide table initially
            const tableWrapper = $('#tabelPenarikan').closest('.card-body');
            tableWrapper.css({ opacity: 0, transition: 'opacity 0.3s' });

            // Initialize DataTable
            table = $('#tabelPenarikan').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0, 1, 10] }
                ],
                initComplete: function () {
                    tableWrapper.css('opacity', 1);
                }
            });

            // Filter by Simpanan Type
            $('#filterSimpanan').on('change', function () {
                table.column(7).search(this.value).draw();
            });

            // Redraw on pagination
            table.on('draw', function () {
                updateSelectAllState();
            });

            // Click row to select checkbox
            $('#tabelPenarikan tbody').on('click', 'tr', function (e) {
                if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('button') || $(e.target).closest('button').length) {
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
                updateSelectAllState();
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

            // Handle Anggota Selection - Show Photo
            $('#namaAnggota').on('change', function () {
                const value = $(this).val();
                const fotoContainer = $('#fotoAnggota');

                if (value) {
                    fotoContainer.html('<img src="{{ asset("assets/images/profile/user-1.jpg") }}" alt="Foto Anggota" class="img-fluid rounded" style="max-height: 180px;">');
                } else {
                    fotoContainer.html('<span class="text-muted">Foto akan muncul setelah memilih anggota</span>');
                }
            });

            // Handle Jenis Simpanan Change - Auto fill amount
            $('#jenisSimpanan').on('change', function () {
                const jenis = $(this).val();
                let amount = '';

                if (jenis === 'Simpanan Wajib') {
                    amount = '500.000';
                } else if (jenis === 'Simpanan Pokok') {
                    amount = '2.500.000';
                }

                if (amount) {
                    $('#jumlah').val(amount);
                    $('#jumlah').focus().select();
                }
            });

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
                if ($('#modalTitle').text() === 'Tambah Data Penarikan Tunai') {
                    const now = new Date();
                    $('#tglTransaksi').val(now.toISOString().slice(0, 16));
                    $('#fotoAnggota').html('<span class="text-muted">Foto akan muncul setelah memilih anggota</span>');
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
            $('#modalTitle').text('Tambah Data Penarikan Tunai');
            $('#formPenarikan')[0].reset();
            $('#formMethod').val('POST');
            $('#formPenarikan').attr('action', '{{ route("simpanan.penarikan.store") }}');

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
            $('#modalTitle').text('Edit Data Penarikan Tunai');
            $('#formMethod').val('PUT');
            $('#formId').val(id);
            $('#formPenarikan').attr('action', '/admin/penarikan/' + id);

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
            table.search(search).draw();
        }

        function resetFilter() {
            $('#searchInput').val('');
            $('#filterTanggal').val('');
            $('#filterSimpanan').val('');
            table.search('').columns().search('').draw();
        }

        function cetakLaporan() {
            window.print();
        }

        function cetakNota(button) {
            const row = button.closest('tr');
            const kodeTransaksi = row.querySelector('td:nth-child(3)').textContent.trim();
            alert('Mencetak nota untuk transaksi: ' + kodeTransaksi);
        }
    </script>
@endpush