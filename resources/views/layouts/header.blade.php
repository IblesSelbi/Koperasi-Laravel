<header class="app-header shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
            
            <!-- Notifikasi Jatuh Tempo -->
            <li class="nav-item dropdown">
                <a class="nav-link position-relative" href="javascript:void(0)" id="drop1" 
                   data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bell fs-7"></i>
                    <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger notif-badge" 
                          style="font-size:11px">
                        {{ $notifications->count() ?? 0 }}
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1" 
                     style="min-width: 320px;">
                    <div class="message-body">
                        <div class="px-3 py-2 bg-light border-bottom">
                            <h6 class="mb-0">Notifikasi Jatuh Tempo</h6>
                        </div>
                        
                        @forelse($notifications ?? [] as $notif)
                        <a href="javascript:void(0)" class="dropdown-item border-bottom py-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-alert-circle fs-5 text-warning"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <h6 class="mb-1 fw-semibold">{{ $notif->nama }}</h6>
                                    <p class="mb-1 fs-2 text-muted">Jatuh tempo: {{ $notif->tanggal_jatuh_tempo }}</p>
                                    <span class="badge bg-danger-subtle text-danger">
                                        Sisa: Rp {{ number_format($notif->sisa_tagihan, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="px-3 py-3 text-center text-muted">
                            <small>Tidak ada notifikasi</small>
                        </div>
                        @endforelse
                    </div>
                </div>
            </li>
        </ul>

        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <!-- Tanggal dan Jam -->
                <li class="nav-item me-3">
                    <span class="text-dark">
                        <i class="ti ti-calendar"></i> <span id="tanggal">{{ date('d F Y') }}</span> &nbsp;
                        <i class="ti ti-clock"></i> <span id="jam">00:00:00</span>
                    </span>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="javascript:void(0)" id="drop2" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ Auth::user()->avatar ?? asset('assets/images/profile/user-1.jpg') }}" 
                             alt="" width="35" height="35" class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" 
                         aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="{{ route('profile.edit') }}" 
                               class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">Profil Saya</p>
                            </a>
                            <a href="{{ route('profile.edit') }}" 
                               class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-key fs-6"></i>
                                <p class="mb-0 fs-3">Ubah Password</p>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary mx-3 mt-2 d-block w-100">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>