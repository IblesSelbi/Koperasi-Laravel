@extends('layouts.app')

@section('title', 'Pemasukan Kas')

@push('styles')
@endpush

@section('content')
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
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" class="form-control" id="filterTanggal" placeholder="Pilih Tanggal" readonly>
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="ti ti-calendar fs-4"></i>
                        </button>
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
                    <button class="btn btn-success btn-sm" onclick="cetakLaporan()">
                        <i class="ti ti-printer"></i> Cetak Laporan
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
                            <th class="text-center" width="50px">No</th>
                            <th>Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Uraian</th>
                            <th>Untuk Kas</th>
                            <th>Dari Akun</th>
                            <th class="text-end">Jumlah</th>
                            <th class="text-center">User</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($pemasukan as $index => $item)
                            <tr data-id="{{ $item->id }}">
                                <td class="text-center" onclick="event.stopPropagation()">
                                    <input type="checkbox" class="row-checkbox form-check-input" data-id="{{ $item->id }}">
                                </td>
                                <td class="text-center text-muted">
                                    {{ $index + 1 }}
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">
                                        {{ $item->kode_transaksi }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $item->tanggal_transaksi->format('d M Y H:i') }}
                                </td>
                                <td class="text-dark">{{ $item->uraian }}</td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">
                                        {{ $item->untukKas->nama_kas ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-muted fw-semibold">
                                    {{ $item->dariAkun->jns_transaksi ?? '-' }}
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary">
                                        {{ $item->user->name ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="table-light">
                            <th colspan="7" class="text-end fw-semibold">Total:</th>
                            <th class="text-end">
                                <span class="fw-semibold text-success fs-4">
                                    Rp {{ number_format($total_pemasukan ?? 0, 0, ',', '.') }}
                                </span>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($pemasukan->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="ti ti-database-off fs-1"></i>
                    <p class="mb-0">Tidak ada data pemasukan</p>
                </div>
            @endif
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
                <form id="formPemasukan">
                    <input type="hidden" id="formId">
                    <input type="hidden" id="formMethod" value="POST">

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="tanggalTransaksi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uraian <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="uraian" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Untuk Kas <span class="text-danger">*</span></label>
                            <select class="form-select" id="untukKas" required>
                                <option value="">-- Pilih Kas --</option>
                                @foreach($kas_list as $kas)
                                    <option value="{{ $kas->id }}">{{ $kas->nama_kas }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya kas yang aktif dan bisa pemasukan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Dari Akun <span class="text-danger">*</span></label>
                            <select class="form-select" id="dariAkun" required>
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akun_list as $akun)
                                    <option value="{{ $akun->id }}">
                                        {{ $akun->jns_transaksi }} ({{ $akun->akun }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya akun yang aktif dan bisa pemasukan</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="jumlah" required>
                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        let fpTanggal; // ✅ Deklarasi global

        $(document).ready(function () {
            // Initialize DataTable
            const table = $('#tabelPemasukan').DataTable({
                pageLength: 10,
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [0, 1] }
                ],
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // ✅ Initialize Flatpickr - SIMPAN KE VARIABLE GLOBAL
            fpTanggal = flatpickr("#filterTanggal", {
                mode: "range",
                dateFormat: "d M Y",
                locale: "id",
                allowInput: false,
                clickOpens: true,
                onChange: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        filterByDateRange(selectedDates[0], selectedDates[1]);
                    }
                }
            });

            // Click row to check checkbox
            $('#tabelPemasukan tbody').on('click', 'tr', function (e) {
                if ($(e.target).is('input[type="checkbox"]')) return;

                const checkbox = $(this).find('.row-checkbox');
                checkbox.prop('checked', !checkbox.prop('checked'));
                $(this).toggleClass('table-active');
            });

            // Select All
            $('#selectAll').on('click', function (e) {
                e.stopPropagation();
                const isChecked = this.checked;
                $('.row-checkbox').prop('checked', isChecked);
                if (isChecked) {
                    $('#tabelPemasukan tbody tr').addClass('table-active');
                } else {
                    $('#tabelPemasukan tbody tr').removeClass('table-active');
                }
            });

            // Format Currency
            $('#jumlah').on('input', function (e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value) {
                    value = parseInt(value).toLocaleString('id-ID');
                }
                e.target.value = value;
            });

            // Set default datetime
            $('#modalForm').on('show.bs.modal', function () {
                if ($('#modalTitle').text() === 'Tambah Data Pemasukan') {
                    const now = new Date();
                    $('#tanggalTransaksi').val(now.toISOString().slice(0, 16));
                }
            });

            // Submit Form
            $('#formPemasukan').on('submit', function (e) {
                e.preventDefault();

                const method = $('#formMethod').val();
                const id = $('#formId').val();
                const url = method === 'POST'
                    ? '{{ route("kas.pemasukan.store") }}'
                    : `/admin/pemasukan/${id}`;

                const data = {
                    tanggal_transaksi: $('#tanggalTransaksi').val(),
                    uraian: $('#uraian').val(),
                    untuk_kas_id: $('#untukKas').val(),
                    dari_akun_id: $('#dariAkun').val(),
                    jumlah: $('#jumlah').val().replace(/\./g, ''),
                };

                fetch(url, {
                    method: method === 'POST' ? 'POST' : 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => location.reload());
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat menyimpan data'
                        });
                    });
            });
        });

        function tambahData() {
            $('#modalTitle').text('Tambah Data Pemasukan');
            $('#formPemasukan')[0].reset();
            $('#formMethod').val('POST');
            $('#formId').val('');
            const now = new Date();
            $('#tanggalTransaksi').val(now.toISOString().slice(0, 16));
            $('#modalForm').modal('show');
        }

        function editData() {
            const checked = $('.row-checkbox:checked');
            if (checked.length === 0) {
                Swal.fire('Peringatan', 'Pilih data yang akan diedit!', 'warning');
                return;
            }
            if (checked.length > 1) {
                Swal.fire('Peringatan', 'Pilih hanya satu data!', 'warning');
                return;
            }

            const id = checked.first().data('id');

            Swal.fire({
                title: 'Loading...',
                text: 'Mengambil data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/admin/pemasukan/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            })
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    Swal.close();

                    $('#modalTitle').text('Edit Data Pemasukan');
                    $('#formMethod').val('PUT');
                    $('#formId').val(data.id);

                    const date = new Date(data.tanggal_transaksi);
                    $('#tanggalTransaksi').val(date.toISOString().slice(0, 16));
                    $('#uraian').val(data.uraian);
                    $('#untukKas').val(data.untuk_kas_id);
                    $('#dariAkun').val(data.dari_akun_id);
                    $('#jumlah').val(parseInt(data.jumlah).toLocaleString('id-ID'));
                    $('#modalForm').modal('show');
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal mengambil data!', 'error');
                    console.error('Error:', err);
                });
        }

        function hapusData() {
            const checked = $('.row-checkbox:checked');
            if (checked.length === 0) {
                Swal.fire('Peringatan', 'Pilih data yang akan dihapus!', 'warning');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Hapus ${checked.length} data pemasukan?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const ids = [];
                    checked.each(function () {
                        ids.push($(this).data('id'));
                    });

                    Promise.all(ids.map(id =>
                        fetch(`/admin/pemasukan/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            }
                        })
                    )).then(() => {
                        Swal.fire('Berhasil!', 'Data berhasil dihapus', 'success')
                            .then(() => location.reload());
                    });
                }
            });
        }

        function filterByDateRange(startDate, endDate) {
            $.fn.dataTable.ext.search.push(function (settings, data) {
                // data[3] = "15 Jan 2026 14:30"
                const parts = data[3].split(' ');
                const dateStr = `${parts[1]} ${parts[0]} ${parts[2]}`;
                const rowDate = new Date(dateStr);

                rowDate.setHours(0, 0, 0, 0);
                startDate.setHours(0, 0, 0, 0);
                endDate.setHours(23, 59, 59, 999);

                return rowDate >= startDate && rowDate <= endDate;
            });

            $('#tabelPemasukan').DataTable().draw();
            $.fn.dataTable.ext.search.pop();
        }

        function cariData() {
            const search = $('#searchInput').val();
            $('#tabelPemasukan').DataTable().search(search).draw();
        }

        function resetFilter() {
            $('#searchInput').val('');
            $('#tabelPemasukan').DataTable().search('').draw();
            
            if (fpTanggal) {
                fpTanggal.clear();
            }
        }

        // ✅ FUNCTION CETAK LAPORAN - SATU-SATUNYA VERSI
        function cetakLaporan() {
            const dates = fpTanggal ? fpTanggal.selectedDates : [];
            let url = '{{ route("kas.pemasukan.cetak") }}';

            // Jika ada filter tanggal, tambahkan ke URL
            if (dates.length === 2) {
                const startDate = dates[0].toISOString().split('T')[0];
                const endDate = dates[1].toISOString().split('T')[0];
                url += `?start_date=${startDate}&end_date=${endDate}`;
            }

            // Buka PDF di tab baru
            window.open(url, '_blank');
        }
    </script>
@endpush