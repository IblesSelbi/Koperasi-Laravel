<header class="app-header shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
               <a class="nav-link fw-semibold {{ 
                            $isAdmin 
                                ? request()->routeIs('admin.dashboard') 
                                : request()->routeIs('user.dashboard') 
                        }}"
                    href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}">
                         Sistem Koperasi
                    </a>
            </li>
        </ul>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ 
                            $isAdmin 
                                ? request()->routeIs('admin.dashboard') 
                                : request()->routeIs('user.dashboard') 
                        }}"
                    href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}">
                        <i class="ti ti-home me-1"></i> Beranda
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('user.pengajuan.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="dropdown">
                        <i class="ti ti-file-text me-1"></i> Pengajuan Pinjaman
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('user.pengajuan.index') ? 'active' : '' }}" 
                               href="{{ route('user.pengajuan.index') }}">
                                Data Pengajuan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('user.pengajuan.create') ? 'active' : '' }}" 
                               href="{{ route('user.pengajuan.create') }}">
                                Tambah Pengajuan Baru
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('user.laporan.*') ? 'active' : '' }}" 
                       href="#" data-bs-toggle="dropdown">
                        <i class="ti ti-report me-1"></i> Laporan
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('user.laporan.simpanan') ? 'active' : '' }}" 
                               href="{{ route('user.laporan.simpanan') }}">
                                Simpanan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('user.laporan.pinjaman') ? 'active' : '' }}" 
                               href="{{ route('user.laporan.pinjaman') }}">
                                Pinjaman
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('user.laporan.pembayaran') ? 'active' : '' }}" 
                               href="{{ route('user.laporan.pembayaran') }}">
                                Pembayaran
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="navbar-collapse justify-content-end px-0">
            <ul class="navbar-nav flex-row ms-auto align-items-center">
                <li class="nav-item me-3">
                    <span class="text-dark">
                        <i class="ti ti-calendar"></i> <span id="tanggal">{{ date('d F Y') }}</span>
                        <i class="ti ti-clock ms-2"></i> <span id="jam">00:00:00</span>
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="javascript:void(0)" data-bs-toggle="dropdown">
                        <img src="{{ asset('assets/images/profile/user-1.jpg') }}" width="35" height="35"
                            class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up">
                        <a href="#" class="dropdown-item"><i class="ti ti-user"></i> Profil Saya</a>
                        <a href="#" class="dropdown-item"><i class="ti ti-camera"></i> Ubah Foto</a>
                        <a href="#" class="dropdown-item"><i class="ti ti-key"></i> Ubah Password</a>
                        <form method="POST" action="{{ route('logout') }}" class="px-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary mt-1 d-block w-100">
                                Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>

@push('scripts')
<script>
    // Update Jam Real-time
    function updateJam() {
        const now = new Date();
        const jam = now.getHours().toString().padStart(2, '0');
        const menit = now.getMinutes().toString().padStart(2, '0');
        const detik = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('jam').textContent = `${jam}:${menit}:${detik}`;
    }
    setInterval(updateJam, 1000);
    updateJam();
</script>
@endpush