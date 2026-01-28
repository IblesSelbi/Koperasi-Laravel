<header class="app-header shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link fw-semibold {{ 
                            $isAdmin
    ? request()->routeIs('admin.dashboard')
    : request()->routeIs('user.dashboard') 
                        }}" href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}">
                    Sistem Koperasi
                </a>
            </li>
        </ul>
        <button class="navbar-toggler d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-3">
                <li class="nav-item">
                    <a class="nav-link {{ 
                            $isAdmin
    ? request()->routeIs('admin.dashboard')
    : request()->routeIs('user.dashboard') 
                        }}" href="{{ $isAdmin ? route('admin.dashboard') : route('user.dashboard') }}">
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
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('user.bayar.index') ? 'active' : '' }}"
                                href="{{ route('user.bayar.index') }}">
                                Bayar Angsuran
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
                        <li>
                            <hr class="dropdown-divider">
                        </li>
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
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

                <!-- Tanggal | Jam -->
                <li class="nav-item me-1 d-flex align-items-center text-dark">
                    <i class="ti ti-calendar me-1"></i>
                    <span id="tanggal">{{ date('d F Y') }}</span>

                    <span class="mx-2"></span>

                    <i class="ti ti-clock me-1"></i>
                    <span id="jam">00:00:00</span>
                </li>

                <!-- Divider -->
                <li class="nav-item d-flex align-items-center mx-3">
                    <div style="width:2px; height:40px; background:#adb5bd;"></div>
                </li>

                <!-- Nama + Avatar -->
                <li class="nav-item dropdown d-flex align-items-center me-3">

                    <!-- Nama -->
                    <a class="nav-link fs-4 p-0 mx-1" href="javascript:void(0)" id="dropUser" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="fw-semibold me-2 text-dark">
                            {{ Auth::user()->name }}
                        </span>
                    </a>

                    <!-- Avatar -->
                    <a class="nav-link p-0" href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->profile_image
                            ? asset('storage/' . Auth::user()->profile_image)
                            : asset('assets/images/profile/user-1.jpg') }}" alt="{{ Auth::user()->name }}" width="35" height="35"
                            class="rounded-circle object-fit-cover">
                    </a>

                    <!-- Dropdown -->
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="dropUser">
                        <div class="message-body">

                            <!-- Info user -->
                            <div class="px-3 py-2 border-bottom">
                                <p class="mb-1 fw-semibold">{{ Auth::user()->name }}</p>
                                <p class="mb-0 text-muted small">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('user.profile.edit') }}"
                                class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">Profil Saya</p>
                            </a>

                            <a href="{{ route('user.profile.edit') }}#password"
                                class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-key fs-6"></i>
                                <p class="mb-0 fs-3">Ubah Password</p>
                            </a>

                            <div class="dropdown-divider"></div>

                            <form method="POST" action="{{ route('logout') }}" class="px-2 py-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="ti ti-logout me-1"></i> Logout
                                </button>
                            </form>
                        </div>
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