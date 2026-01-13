@extends('layouts.app')

@section('title', 'Data Pengajuan Pinjaman Saya')

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Data Pengajuan Pinjaman Saya</h4>
                    <p class="text-muted fs-3 mb-0">Kelola pengajuan pinjaman Anda</p>
                </div>
                <div>
                    <a href="{{ route('user.pengajuan.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> Tambah Pengajuan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Alert -->
    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
        <i class="ti ti-info-circle me-2"></i>
        <strong>Informasi:</strong> Anda dapat mengubah nominal, lama angsuran, dan keterangan pada pengajuan dengan status "Menunggu Konfirmasi".
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Data Table Card -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelPengajuan" class="table table-hover align-middle rounded-2 border overflow-hidden" style="width:100%">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center align-middle">Tanggal</th>
                            <th class="text-center align-middle">Jenis</th>
                            <th width="150px" class="text-end align-middle">Jumlah</th>
                            <th class="text-center align-middle" title="Jumlah Angsuran (Bulan)">Jumlah Angsuran</th>
                            <th width="200px" class="align-middle">Keterangan</th>
                            <th width="150px" class="align-middle">Alasan</th>
                            <th class="text-center align-middle">Tanggal Update</th>
                            <th width="130px" class="text-center align-middle">Status</th>
                            <th class="text-center align-middle">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuan as $item)
                        <tr>
                            <td class="text-center text-muted">
                                {{ $item->tanggal_pengajuan->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                @if($item->jenis_pinjaman == 'Biasa')
                                    <span class="badge bg-info-subtle text-info">Biasa</span>
                                @elseif($item->jenis_pinjaman == 'Darurat')
                                    <span class="badge bg-warning-subtle text-warning">Darurat</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">Barang</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($item->status == 0)
                                    <a href="javascript:void(0)" class="editable-nominal text-decoration-none fw-bold text-success" 
                                       data-id="{{ $item->id }}" data-value="{{ $item->jumlah }}">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </a>
                                @else
                                    <span class="fw-bold @if($item->status == 1 || $item->status == 3) text-success @else text-muted @endif">
                                        Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status == 0)
                                    <a href="javascript:void(0)" class="editable-angsuran text-decoration-none" 
                                       data-id="{{ $item->id }}" data-value="{{ $item->lama_angsuran_id }}">
                                        <span class="badge bg-secondary">{{ $item->lamaAngsuran->lama_angsuran }}</span>
                                    </a>
                                @else
                                    <span class="badge bg-secondary">{{ $item->lamaAngsuran->lama_angsuran }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 0)
                                    <a href="javascript:void(0)" class="editable-keterangan text-decoration-none" 
                                       data-id="{{ $item->id }}" data-value="{{ $item->keterangan }}">
                                        {{ Str::limit($item->keterangan, 50) }}
                                    </a>
                                @else
                                    {{ Str::limit($item->keterangan, 50) }}
                                @endif
                            </td>
                            <td class="@if($item->status == 1 || $item->status == 3) text-success @elseif($item->status == 2) text-danger @else text-muted @endif">
                                {{ $item->alasan ?? '-' }}
                            </td>
                            <td class="text-center text-muted">
                                {{ $item->updated_at->format('d M Y') }}
                            </td>
                            <td class="text-center">
                                @if($item->status == 0)
                                    <span class="text-primary">
                                        <i class="ti ti-clock"></i> Menunggu<br>Konfirmasi
                                    </span>
                                @elseif($item->status == 1)
                                    <span class="text-success">
                                        <i class="ti ti-check"></i> Disetujui<br>
                                        <small>Cair: {{ $item->tanggal_cair ? \Carbon\Carbon::parse($item->tanggal_cair)->format('d M Y') : '-' }}</small>
                                    </span>
                                @elseif($item->status == 2)
                                    <span class="text-danger">
                                        <i class="ti ti-x"></i> Ditolak
                                    </span>
                                @elseif($item->status == 3)
                                    <span class="text-success">
                                        <i class="ti ti-rocket"></i> Terlaksana
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="ti ti-ban"></i> Batal
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->status == 0)
                                    <button class="btn btn-danger btn-sm" onclick="batalkanPengajuan({{ $item->id }})">
                                        <i class="ti ti-ban"></i> Batal
                                    </button>
                                @elseif($item->status == 1 || $item->status == 3)
                                    <button class="btn btn-secondary btn-sm" onclick="cetakPengajuan({{ $item->id }})">
                                        <i class="ti ti-printer"></i> Cetak
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="ti ti-database-off fs-1 mb-2"></i>
                                    <p class="mb-0">Belum ada pengajuan pinjaman</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Pass PHP data to JavaScript
        const lamaAngsuranList = @json($lama_angsuran_list ?? []);

        // Initialize DataTable
        let table;
        $(document).ready(function () {
            table = $('#tabelPengajuan').DataTable({
                pageLength: 10,
                order: [[0, 'desc']],
                columnDefs: [
                    { orderable: false, targets: [8] }
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

            // Editable Nominal
            $('.editable-nominal').on('click', function () {
                const id = $(this).data('id');
                const currentValue = $(this).data('value');

                Swal.fire({
                    title: 'Ubah Nominal',
                    html: `
                        <div class="text-start">
                            <label class="form-label fw-semibold">Nominal Baru</label>
                            <input type="text" id="swal-nominal" class="form-control" value="${formatRupiah(currentValue)}">
                            <small class="text-muted">Minimal Rp 500.000</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check"></i> Simpan',
                    cancelButtonText: '<i class="ti ti-x"></i> Batal',
                    confirmButtonColor: '#0d6efd',
                    preConfirm: () => {
                        const nominal = $('#swal-nominal').val().replace(/[^0-9]/g, '');
                        if (!nominal || nominal == '0') {
                            Swal.showValidationMessage('Nominal wajib diisi!');
                            return false;
                        }
                        if (parseInt(nominal) < 500000) {
                            Swal.showValidationMessage('Minimal pinjaman Rp 500.000');
                            return false;
                        }
                        return { nominal };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateData('nominal', id, result.value.nominal);
                    }
                });

                // Format input saat mengetik
                setTimeout(() => {
                    $('#swal-nominal').on('keyup', function () {
                        const val = $(this).val().replace(/[^0-9]/g, '');
                        $(this).val(formatRupiah(val));
                    });
                }, 100);
            });

            // Editable Lama Angsuran
            $('.editable-angsuran').on('click', function () {
                const id = $(this).data('id');
                const currentValue = $(this).data('value');

                // Build options HTML from JavaScript array
                let optionsHtml = '';
                lamaAngsuranList.forEach(la => {
                    const selected = la.id == currentValue ? 'selected' : '';
                    optionsHtml += `<option value="${la.id}" ${selected}>${la.lama_angsuran} bulan</option>`;
                });

                Swal.fire({
                    title: 'Ubah Lama Angsuran',
                    html: `
                        <div class="text-start">
                            <label class="form-label fw-semibold">Pilih Lama Angsuran (Bulan)</label>
                            <select id="swal-angsuran" class="form-select">
                                ${optionsHtml}
                            </select>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check"></i> Simpan',
                    cancelButtonText: '<i class="ti ti-x"></i> Batal',
                    confirmButtonColor: '#0d6efd',
                    didOpen: () => {
                        $('#swal-angsuran').val(currentValue);
                    },
                    preConfirm: () => {
                        return { angsuran: $('#swal-angsuran').val() };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateData('lama_ags', id, result.value.angsuran);
                    }
                });
            });

            // Editable Keterangan
            $('.editable-keterangan').on('click', function () {
                const id = $(this).data('id');
                const currentValue = $(this).data('value');

                Swal.fire({
                    title: 'Ubah Keterangan',
                    html: `
                        <div class="text-start">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea id="swal-keterangan" class="form-control" rows="4">${currentValue}</textarea>
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="ti ti-check"></i> Simpan',
                    cancelButtonText: '<i class="ti ti-x"></i> Batal',
                    confirmButtonColor: '#0d6efd',
                    preConfirm: () => {
                        const keterangan = $('#swal-keterangan').val().trim();
                        if (!keterangan) {
                            Swal.showValidationMessage('Keterangan wajib diisi!');
                            return false;
                        }
                        if (keterangan.length > 500) {
                            Swal.showValidationMessage('Keterangan maksimal 500 karakter!');
                            return false;
                        }
                        return { keterangan };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateData('keterangan', id, result.value.keterangan);
                    }
                });
            });
        });

        // Format Rupiah Helper
        function formatRupiah(angka) {
            if (!angka) return '';
            return parseInt(angka).toLocaleString('id-ID');
        }

        // Update Data Function
        function updateData(field, id, value) {
            Swal.fire({
                title: 'Menyimpan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("user.pengajuan.update") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    field: field,
                    value: value
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message || 'Data berhasil diperbarui',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan, silakan coba lagi'
                    });
                }
            });
        }

        // Batalkan Pengajuan
        function batalkanPengajuan(id) {
            Swal.fire({
                title: 'Batalkan Pengajuan?',
                text: 'Pengajuan yang dibatalkan tidak dapat dikembalikan',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-ban"></i> Ya, Batalkan',
                cancelButtonText: '<i class="ti ti-x"></i> Tidak',
                confirmButtonColor: '#dc3545',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: `/user/pengajuan/batal/${id}`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message || 'Pengajuan berhasil dibatalkan',
                                timer: 1500,
                        showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                            });
                        }
                    });
                }
            });
        }

        // Cetak Pengajuan
        function cetakPengajuan(id) {
            const url = `{{ url('user/pengajuan/cetak') }}/${id}`;
            window.open(url, '_blank');
        }
    </script>
@endpush