@extends('layouts.app')

@section('title', 'Riwayat Pembatalan Pelunasan')

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        #tabelRiwayat th, td {
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
                    <h4 class="fw-semibold mb-1">Riwayat Pembatalan Pelunasan</h4>
                    <p class="text-muted fs-3 mb-0">Data validasi pelunasan yang dibatalkan (Admin Only)</p>
                </div>
                <a href="{{ route('pinjaman.lunas') }}" class="btn btn-outline-secondary">
                    <i class="ti ti-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-danger">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-x-circle fs-3 text-danger"></i>
                    <div>
                        <small class="text-muted">Total Dibatalkan</small>
                        <div class="fw-semibold text-danger fs-5">
                            {{ count($riwayatBatal) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-warning">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-cash fs-3 text-warning"></i>
                    <div>
                        <small class="text-muted">Total Nilai</small>
                        <div class="fw-semibold text-warning fs-6">
                            Rp {{ number_format(collect($riwayatBatal)->sum('total_dibayar'), 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-info">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-calendar fs-3 text-info"></i>
                    <div>
                        <small class="text-muted">Bulan Ini</small>
                        <div class="fw-semibold text-info fs-5">
                            {{-- ✅ GUNAKAN tanggal_batal_obj (Carbon object) --}}
                            {{ collect($riwayatBatal)->filter(function ($item) {
                                return $item['tanggal_batal_obj']->isCurrentMonth();
                            })->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-success">
                <div class="card-body d-flex align-items-center gap-3 py-3">
                    <i class="ti ti-user fs-3 text-success"></i>
                    <div>
                        <small class="text-muted">Total Anggota</small>
                        <div class="fw-semibold text-success fs-5">
                            {{ collect($riwayatBatal)->pluck('anggota')->unique()->count() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-light border-bottom">
            <h6 class="mb-0 fw-semibold"><i class="ti ti-history me-2"></i>Daftar Pembatalan Pelunasan</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelRiwayat" class="table table-hover align-middle rounded-2 border overflow-hidden"
                    style="width:100%">
                    <thead class="table-danger">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode Lunas</th>
                            <th>Kode Pinjaman</th>
                            <th>Anggota</th>
                            <th class="text-end">Total Dibayar</th>
                            <th class="text-center">Tanggal Lunas</th>
                            <th class="text-center">Dibatalkan Pada</th>
                            <th>Dibatalkan Oleh</th>
                            <th>Divalidasi Oleh</th>
                            <th width="100px">Alasan</th>
                            <th width="100px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatBatal as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger fw-semibold px-3 py-2">
                                        {{ $item['kode_lunas'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary fw-semibold px-2 py-1">
                                        {{ $item['kode_pinjaman'] }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $item['anggota'] }}</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">Rp
                                        {{ number_format($item['total_dibayar'], 0, ',', '.') }}</strong>
                                </td>
                                <td class="text-center">
                                    <div>{{ $item['tanggal_lunas'] }}</div>
                                </td>
                                <td class="text-center">
                                    <div class="text-danger fw-semibold">{{ $item['tanggal_batal'] }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-danger text-danger">
                                        {{ $item['dibatalkan_oleh'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge border border-secondary text-secondary">
                                        {{ $item['divalidasi_oleh'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($item['alasan_batal'])
                                        <button class="btn btn-sm btn-outline-info"
                                            onclick="showAlasan('{{ addslashes($item['alasan_batal']) }}')">
                                            <i class="ti ti-eye"></i> Lihat
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    <button class="btn btn-success btn-sm" onclick="restorePelunasan({{ $item['id'] }})">
                                        <i class="ti ti-refresh"></i> Pulihkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <i class="ti ti-inbox fs-1 text-muted d-block mb-2"></i>
                                    <p class="text-muted mb-0">Belum ada pembatalan pelunasan</p>
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
                order: [[6, 'desc']] // Order by tanggal batal
            });
        });

        // Show Alasan Modal
        function showAlasan(alasan) {
            Swal.fire({
                title: 'Alasan Pembatalan',
                html: `
                        <div class="alert alert-warning text-start">
                            <i class="ti ti-info-circle me-2"></i>
                            ${alasan}
                        </div>
                    `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        }

        // Restore Pelunasan
        function restorePelunasan(id) {
            Swal.fire({
                title: 'Pulihkan Validasi Pelunasan?',
                html: `
                        <div class="text-start">
                            <p>Apakah Anda yakin ingin memulihkan validasi pelunasan ini?</p>
                            <div class="alert alert-info mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                Data akan dikembalikan ke daftar <strong>Pinjaman Lunas</strong> dan status pinjaman akan menjadi <strong>Lunas</strong>.
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
                        url: `{{ url('admin/pinjaman/lunas') }}/${id}/restore`,
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
    </script>
@endpush