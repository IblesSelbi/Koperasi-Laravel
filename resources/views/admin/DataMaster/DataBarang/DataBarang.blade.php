@extends('layouts.app')

@section('title', 'Master Data - Data Barang')

@push('styles')
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
                <table id="tabelDataBarang" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
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
                            <tr data-id="{{ $item->id }}" data-nama="{{ $item->nama_barang }}"
                                data-type="{{ $item->type }}" data-merk="{{ $item->merk }}"
                                data-harga="{{ $item->harga }}" data-jumlah="{{ $item->jumlah }}"
                                data-ket="{{ $item->keterangan }}">
                                <td class="text-center text-muted fw-medium"></td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $item->nama_barang }}</div>
                                </td>
                                <td>
                                    @if($item->type)
                                        <span
                                            class="badge bg-{{ $item->type == 'Uang' ? 'success' : 'warning' }}-subtle text-{{ $item->type == 'Uang' ? 'success' : 'warning' }} px-3 py-2">
                                            {{ $item->type }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->merk)
                                        <span class="text-dark fw-medium">{{ $item->merk }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-dark">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $item->jumlah }}</span>
                                </td>
                                <td>
                                    @if($item->keterangan)
                                        <span class="text-muted">{{ $item->keterangan }}</span>
                                    @else
                                        <span class="text-muted">-</span>
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
                        @csrf
                        <input type="hidden" id="editId" value="">
                        <div class="mb-3">
                            <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="namaBarang" placeholder="Masukkan nama barang"
                                maxlength="255" required>
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
                                <input type="text" class="form-control" id="merk" placeholder="Masukkan merk"
                                    maxlength="50">
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
                                <input type="number" class="form-control" id="jumlahBarang" placeholder="0" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" rows="3" placeholder="Masukkan keterangan"
                                maxlength="255"></textarea>
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

        // INIT DATATABLE
        $(document).ready(function () {
            table = $('#tabelDataBarang').DataTable({
                language: {
                    url: "{{ asset('assets/datatables/i18n/id.json') }}"
                },
                pageLength: 10,
                order: [],
                columnDefs: [
                    { orderable: false, targets: [0, 7] }
                ]
            });

            // FIX NOMOR AGAR SELALU URUT
            table.on('order.dt search.dt draw.dt', function () {
                table.column(0, { search: 'applied', order: 'applied' })
                    .nodes()
                    .each(function (cell, i) {
                        cell.innerHTML = i + 1;
                    });
            }).draw();
        });

        // FORMAT RUPIAH
        function formatRupiah(angka) {
            return new Intl.NumberFormat('id-ID').format(angka);
        }

        // TAMBAH DATA
        function tambahData() {
            document.getElementById('modalTitle').innerText = 'Tambah Data Barang';
            document.getElementById('formDataBarang').reset();
            document.getElementById('editId').value = '';

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // EDIT DATA
        function editData(btn) {
            const row = btn.closest('tr');

            document.getElementById('modalTitle').innerText = 'Ubah Data Barang';
            document.getElementById('editId').value = row.dataset.id;
            document.getElementById('namaBarang').value = row.dataset.nama;
            document.getElementById('type').value = row.dataset.type;
            document.getElementById('merk').value = row.dataset.merk;
            document.getElementById('harga').value = formatRupiah(row.dataset.harga);
            document.getElementById('jumlahBarang').value = row.dataset.jumlah;
            document.getElementById('keterangan').value = row.dataset.ket;

            new bootstrap.Modal(document.getElementById('modalForm'), {
                backdrop: 'static',
                keyboard: false
            }).show();
        }

        // SIMPAN DATA (ADD & UPDATE)
        function simpanData() {
            const id = document.getElementById('editId').value;
            const nama_barang = document.getElementById('namaBarang').value;
            const type = document.getElementById('type').value;
            const merk = document.getElementById('merk').value;
            const harga = document.getElementById('harga').value.replace(/\./g, '');
            const jumlah = document.getElementById('jumlahBarang').value;
            const keterangan = document.getElementById('keterangan').value;

            const url = id
                ? `/admin/data-barang/${id}`
                : `/admin/data-barang`;

            fetch(url, {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    nama_barang,
                    type,
                    merk,
                    harga,
                    jumlah,
                    keterangan
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

        // HAPUS DATA
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
                    fetch(`/admin/data-barang/${id}`, {
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

        // CARI DATA
        function cariData() {
            table.search(document.getElementById('searchInput').value).draw();
        }

        // RESET FILTER
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

        // CETAK & EXPORT (SERVER SIDE)
        function cetakLaporan() {
            window.location.href = "{{ route('master.data-barang.cetak') }}";
        }

        function eksporData() {
            window.location.href = "{{ route('master.data-barang.export') }}";
        }

        // FORMAT INPUT HARGA
        document.getElementById('harga').addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = value ? formatRupiah(value) : '';
        });
    </script>

@endpush