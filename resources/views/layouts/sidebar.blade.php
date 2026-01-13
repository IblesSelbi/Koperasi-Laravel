<!-- Sidebar Start -->
<aside class="left-sidebar shadow-sm">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between shadow-sm">
           @auth
            <a href="{{ auth()->user()->role->nama === 'admin'
                ? route('admin.dashboard')
                : route('user.dashboard') }}"
            class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/logoAkeno-no-name.png') }}" alt="Logo" style="height:37px;width:auto;">
                <span class="fw-semibold fs-5 text-dark lh-1 ms-2">
                    Sistem <span class="fw-normal"> Koperasi</span>
                </span>
            </a>
            @endauth
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-6"></i>
            </div>
        </div>

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <!-- HOME -->
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i class="ti ti-atom"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <!-- KEUANGAN -->
                <li><span class="sidebar-divider lg"></span></li>
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Keuangan</span>
                </li>

                <!-- Transaksi Kas -->
                <li class="sidebar-item {{ request()->routeIs('kas.*') ? 'active' : '' }}">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex"><i class="ti ti-cash"></i></span>
                            <span class="hide-menu">Transaksi Kas</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item {{ request()->routeIs('kas.pemasukan') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kas.pemasukan') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Pemasukan</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('kas.pengeluaran') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kas.pengeluaran') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Pengeluaran</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('kas.transfer') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('kas.transfer') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Transfer</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Simpanan -->
                <li class="sidebar-item {{ request()->routeIs('simpanan.*') ? 'active' : '' }}">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex"><i class="ti ti-wallet"></i></span>
                            <span class="hide-menu">Simpanan</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item {{ request()->routeIs('simpanan.setoran') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('simpanan.setoran') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Setoran Tunai</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('simpanan.penarikan') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('simpanan.penarikan') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Penarikan Tunai</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Pinjaman -->
                <li class="sidebar-item {{ request()->routeIs('pinjaman.*') ? 'active' : '' }}">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex"><i class="ti ti-building-bank"></i></span>
                            <span class="hide-menu">Pinjaman</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item {{ request()->routeIs('pinjaman.pengajuan') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('pinjaman.pengajuan') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Pengajuan</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('pinjaman.pinjaman') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('pinjaman.pinjaman') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Pinjaman</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('pinjaman.bayar') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('pinjaman.bayar') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Bayar Angsuran</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('pinjaman.lunas') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('pinjaman.lunas') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Pinjaman Lunas</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- REKAP -->
                <li><span class="sidebar-divider lg"></span></li>
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Rekap</span>
                </li>

                <!-- Laporan -->
                <li class="sidebar-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex"><i class="ti ti-file-text"></i></span>
                            <span class="hide-menu">Laporan</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item {{ request()->routeIs('laporan.anggota') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.anggota') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Anggota</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.kas-anggota') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.kas-anggota') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Kas Anggota</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.jatuh-tempo') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.jatuh-tempo') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Jatuh Tempo</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.kredit-macet') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.kredit-macet') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Kredit Macet</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.transaksi-kas') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.transaksi-kas') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Transaksi Kas</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.buku-besar') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.buku-besar') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Buku Besar</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.neraca-saldo') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.neraca-saldo') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Neraca Saldo</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.kas-simpanan') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.kas-simpanan') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Kas Simpanan</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.kas-pinjaman') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.kas-pinjaman') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Kas Pinjaman</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.saldo-kas') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.saldo-kas') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Saldo Kas</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.laba-rugi') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.laba-rugi') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Laba Rugi</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('laporan.shu') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('laporan.shu') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">SHU</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- DATA -->
                <li><span class="sidebar-divider lg"></span></li>
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Data</span>
                </li>

                <!-- Data Master -->
                <li class="sidebar-item {{ request()->routeIs('master.*') ? 'active' : '' }}">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex"><i class="ti ti-database"></i></span>
                            <span class="hide-menu">Data Master</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item {{ request()->routeIs('master.jenis-simpanan') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.jenis-simpanan') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Jenis Simpanan</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('master.jenis-akun') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.jenis-akun') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Jenis Akun</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('master.data-kas') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.data-kas') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Kas</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('master.lama-angsuran') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.lama-angsuran') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Lama Angsuran</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('master.data-barang') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.data-barang') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Barang</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('master.data-anggota') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.data-anggota') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Anggota</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('master.data-pengguna') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('master.data-pengguna') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Data Pengguna</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- PENGATURAN -->
                <li><span class="sidebar-divider lg"></span></li>
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Pengaturan</span>
                </li>

                <!-- Setting -->
                <li class="sidebar-item {{ request()->routeIs('setting.*') ? 'active' : '' }}">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex"><i class="ti ti-settings"></i></span>
                            <span class="hide-menu">Setting</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item {{ request()->routeIs('setting.identitas') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('setting.identitas') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Identitas Koperasi</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item {{ request()->routeIs('setting.suku-bunga') ? 'active' : '' }}">
                            <a class="sidebar-link" href="{{ route('setting.suku-bunga') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Suku Bunga</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</aside>