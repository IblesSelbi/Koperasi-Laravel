@extends('layouts.app')

@section('title', 'Riwayat Pinjaman Terhapus')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tabelRiwayat th {
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-semibold mb-1">Riwayat Pinjaman Terhapus</h4>
                    <p class="text-muted fs-3 mb-0">Data pinjaman yang telah dihapus</p>
                </div>
                <a href="{{ route('pinjaman.pinjaman') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Info -->
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <div class="d-flex align-items-start">
            <i class="ti ti-info-circle fs-4 me-2 mt-1"></i>
            <div>
                <strong>Informasi:</strong>
                <ul class="mb-0 mt-2">
                    <li>Data yang dihapus masih tersimpan dan dapat dipulihkan kembali</li>
                    <li>Pinjaman yang sudah ada pembayaran wajib mencantumkan alasan penghapusan</li>
                    <li>Hapus permanen hanya bisa dilakukan pada pinjaman yang belum ada pembayaran</li>
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-primary">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-trash fs-3 text-primary"></i>
                    <div>
                        <small class="text-muted">Total Dihapus</small>
                        <div class="fw-semibold text-primary fs-5">
                            {{ count($pinjamanTerhapus) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-success">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-cash fs-3 text-success"></i>
                    <div>
                        <small class="text-muted">Total Nilai</small>
                        <div class="fw-semibold text-success fs-6">
                            Rp {{ number_format(collect($pinjamanTerhapus)->sum('jumlah'), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-warning">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-alert-circle fs-3 text-warning"></i>
                    <div>
                        <small class="text-muted">Ada Pembayaran</small>
                        <div class="fw-semibold text-warning fs-5">
                            {{ collect($pinjamanTerhapus)->where('sudah_ada_pembayaran', true)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-info">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-check-circle fs-3 text-info"></i>
                    <div>
                        <small class="text-muted">Belum Ada Bayar</small>
                        <div class="fw-semibold text-info fs-5">
                            {{ collect($pinjamanTerhapus)->where('sudah_ada_pembayaran', false)->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-history me-2"></i>Daftar Pinjaman Terhapus</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelRiwayat" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-danger">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode Pinjaman</th>
                            <th>Anggota</th>
                            <th class="text-end">Jumlah Pinjaman</th>
                            <th class="text-end">Sudah Dibayar</th>
                            <th class="text-center">Tanggal Pinjam</th>
                            <th class="text-center">Dihapus Pada</th>
                            <th>Dihapus Oleh</th>
                            <th width="70px">Alasan</th>
                            <th width="130px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjamanTerhapus as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-2">
                                        {{ $item['kode'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $item['anggota'] }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong>Rp {{ number_format($item['jumlah'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-end">
                                    @if($item['sudah_ada_pembayaran'])
                                        <span class="text-success fw-bold">
                                            Rp {{ number_format($item['total_sudah_dibayar'], 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-muted">Rp 0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div>{{ $item['tanggal_pinjam'] }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="text-danger fw-semibold">{{ $item['tanggal_hapus'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary">
                                        {{ $item['dihapus_oleh'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($item['alasan'])
                                        <button class="btn btn-sm btn-outline-info" onclick="showAlasan('{{ $item['alasan'] }}')">
                                            <i class="ti ti-eye"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                        <button class="btn btn-success btn-sm" onclick="restorePinjaman({{ $item['id'] }})">
                                            <i class="ti ti-refresh"></i> Pulihkan
                                        </button>
                                        @if(!$item['sudah_ada_pembayaran'])
                                            <button class="btn btn-danger btn-sm" onclick="hapusPermanenn({{ $item['id'] }})">
                                                <i class="ti ti-trash-x"></i> Hapus
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <i class="ti ti-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada pinjaman yang dihapus</p>
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
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $('#tabelRiwayat').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                },
                pageLength: 10,
                order: [[6, 'desc']] // Order by tanggal hapus
            });
        });

        // Show Alasan Modal
        function showAlasan(alasan) {
            Swal.fire({
                title: 'Alasan Penghapusan',
                html: `
                                <div class="alert alert-info text-start">
                                    <i class="ti ti-info-circle me-2"></i>
                                    ${alasan}
                                </div>
                            `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Restore Pinjaman
        function restorePinjaman(id) {
            Swal.fire({
                title: 'Pulihkan Pinjaman?',
                html: `
                                <div class="text-start">
                                    <p>Apakah Anda yakin ingin memulihkan pinjaman ini?</p>
                                    <div class="alert alert-info mb-0">
                                        <i class="ti ti-info-circle me-2"></i>
                                        Data akan dikembalikan ke daftar pinjaman aktif dan status pengajuan akan diupdate.
                                    </div>
                                </div>
                            `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-refresh"></i> Ya, Pulihkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memulihkan Data...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: `{{ url('admin/pinjaman') }}/${id}/restore`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Gagal memulihkan data'
                            });
                        }
                    });
                }
            });
        }

        // Hapus Permanen
        function hapusPermanenn(id) {
            Swal.fire({
                title: 'Hapus Permanen?',
                html: `
                                <div class="alert alert-danger text-start">
                                    <i class="ti ti-alert-triangle me-2"></i>
                                    <strong>PERHATIAN!</strong><br>
                                    Data akan dihapus secara permanen dari database dan tidak dapat dipulihkan kembali.
                                </div>
                                <p class="text-start mb-0">Apakah Anda benar-benar yakin?</p>
                            `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="ti ti-trash-x"></i> Ya, Hapus Permanen',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc3545'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Menghapus Permanen...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        willOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: `{{ url('admin/pinjaman') }}/${id}/force-delete`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Gagal menghapus data'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush