@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-semibold mb-1">Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}</h4>
            <p class="text-muted fs-3 mb-0">Silahkan pilih menu untuk mengoperasikan aplikasi</p>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row g-3">
        <!-- Pinjaman Kredit -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background: #fff3cd;">
                            <i class="ti ti-cash fs-6 text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">Pinjaman Kredit</h6>
                            <small class="text-muted">Data bulan ini</small>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Transaksi Bulan Ini</span>
                                    <span class="fw-bold">{{ $stats['pinjaman']['transaksi_bulan_ini'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Jml Tagihan</span>
                                    <span class="fw-semibold text-success">Rp
                                        {{ number_format($stats['pinjaman']['jml_tagihan'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Sisa Tagihan</span>
                                    <span class="fw-semibold text-danger">Rp
                                        {{ number_format($stats['pinjaman']['sisa_tagihan'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('laporan.kas-pinjaman') }}" class="btn btn-warning text-white btn-sm w-100 mt-3">
                        <small>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- Simpanan -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background: #d1e7dd;">
                            <i class="ti ti-wallet fs-6 text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">Simpanan</h6>
                            <small class="text-muted">{{ date('F Y') }}</small>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Simpanan Anggota</span>
                                    <span class="fw-bold">{{ $stats['simpanan']['simpanan_anggota'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Penarikan Tunai</span>
                                    <span class="fw-semibold">{{ $stats['simpanan']['penarikan_tunai'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Jumlah Simpanan</span>
                                    <span class="fw-semibold text-primary">Rp
                                        {{ number_format($stats['simpanan']['jumlah_simpanan'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('laporan.kas-simpanan') }}" class="btn btn-success btn-sm w-100 mt-3">
                        <small>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kas Bulan Ini -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background: #e7e3fc;">
                            <i class="ti ti-book fs-6" style="color: #7460ee;"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">Kas Bulan Ini</h6>
                            <small class="text-muted">{{ date('F Y') }}</small>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Debet</span>
                                    <span class="fw-semibold text-success">Rp
                                        {{ number_format($stats['kas']['debet'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Kredit</span>
                                    <span class="fw-semibold text-danger">Rp
                                        {{ number_format($stats['kas']['kredit'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Jumlah</span>
                                    <span
                                        class="fw-bold {{ $stats['kas']['jumlah'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($stats['kas']['jumlah'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('laporan.saldo-kas') }}" class="btn btn-sm w-100 mt-3"
                        style="background: #7460ee; color: white;">
                        <small>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- Data Anggota -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background: #cfe2ff;">
                            <i class="ti ti-users fs-6 text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">Data Anggota</h6>
                            <small class="text-muted">Total anggota</small>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Anggota Aktif</span>
                                    <span class="fw-bold text-primary">{{ $stats['anggota']['aktif'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Tidak Aktif</span>
                                    <span class="fw-semibold">{{ $stats['anggota']['tidak_aktif'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Jumlah Anggota</span>
                                    <span class="fw-bold text-primary">{{ $stats['anggota']['total'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('laporan.anggota') }}" class="btn btn-primary btn-sm w-100 mt-3">
                        <small>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- Data Peminjam -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background: #f8d7da;">
                            <i class="ti ti-building-bank fs-6 text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">Data Peminjam</h6>
                            <small class="text-muted">Status pinjaman</small>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Peminjam</span>
                                    <span class="fw-bold">{{ $stats['peminjam']['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Sudah Lunas</span>
                                    <span class="fw-semibold text-success">{{ $stats['peminjam']['lunas'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Belum Lunas</span>
                                    <span class="fw-semibold text-danger">{{ $stats['peminjam']['belum_lunas'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('pinjaman.pinjaman') }}" class="btn btn-danger btn-sm w-100 mt-3">
                        <small>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>

        <!-- Data Pengguna -->
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background: #cff4fc;">
                            <i class="ti ti-user-circle fs-6 text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 fw-semibold">Data Pengguna</h6>
                            <small class="text-muted">User sistem</small>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">User Aktif</span>
                                    <span class="fw-bold text-info">{{ $stats['pengguna']['aktif'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">User Non-Aktif</span>
                                    <span class="fw-semibold">{{ $stats['pengguna']['non_aktif'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-2 bg-light rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Jumlah User</span>
                                    <span class="fw-bold text-info">{{ $stats['pengguna']['total'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('master.data-anggota') }}" class="btn btn-info btn-sm w-100 mt-3">
                        <small>Lihat Detail</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activity Row -->
    <div class="row mt-4">
        <!-- Chart Area -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body shadow-sm">
                    <div class="d-md-flex align-items-center mb-3">
                        <div>
                            <h4 class="card-title">Grafik Keuangan Koperasi</h4>
                            <p class="card-subtitle text-muted">Simpanan & Pinjaman 6 Bulan Terakhir</p>
                        </div>
                        <div class="ms-auto">
                            <ul class="list-unstyled mb-0">
                                <li class="list-inline-item">
                                    <span class="round-8 bg-success rounded-circle me-1 d-inline-block"></span>
                                    <span class="text-muted">Simpanan</span>
                                </li>
                                <li class="list-inline-item">
                                    <span class="round-8 bg-warning rounded-circle me-1 d-inline-block"></span>
                                    <span class="text-muted">Pinjaman</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="sales-overview" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body shadow-sm">
                    <h4 class="card-title mb-4">Ringkasan Cepat</h4>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Simpanan</span>
                            <h5 class="mb-0 fw-semibold text-success">Rp
                                {{ number_format($stats['simpanan']['jumlah_simpanan'], 0, ',', '.') }}</h5>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 60%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Total Pinjaman</span>
                            <h5 class="mb-0 fw-semibold text-warning">Rp
                                {{ number_format($stats['pinjaman']['jml_tagihan'], 0, ',', '.') }}</h5>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Sisa Tagihan</span>
                            <h5 class="mb-0 fw-semibold text-danger">Rp
                                {{ number_format($stats['pinjaman']['sisa_tagihan'], 0, ',', '.') }}</h5>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 50%"></div>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle fs-5 me-2"></i>
                            <div>
                                <h6 class="mb-1 fw-semibold">Peringatan!</h6>
                                <p class="mb-0 fs-2">Ada <strong>{{ $notifications->count() }} angsuran</strong> yang akan
                                    jatuh tempo</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        // Data dari backend
        const chartData = @json($chartData);

        // Chart Configuration
        var options = {
            series: [{
                name: 'Simpanan',
                data: chartData.simpanan
            }, {
                name: 'Pinjaman',
                data: chartData.pinjaman
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            colors: ['#13deb9', '#ffae1f'],
            xaxis: {
                categories: chartData.months,
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            },
            legend: {
                show: false
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#sales-overview"), options);
        chart.render();
    </script>
@endpush