<header class="app-header shadow-sm">
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>

            <!-- ============ Notifikasi Pengajuan Baru ============ -->
            <li class="nav-item dropdown">
                <a class="nav-link position-relative" href="javascript:void(0)" id="dropPengajuan"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-bell fs-7"></i>
                    <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger notif-badge"
                        style="font-size:11px" id="badge-pengajuan">
                        0
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="dropPengajuan"
                    style="min-width: 360px; max-height: 450px; overflow-y: auto;">
                    <div class="message-body">
                        <div class="px-3 py-2 bg-light border-bottom">
                            <h6 class="mb-0">Pengajuan Pinjaman Baru</h6>
                        </div>

                        <div id="notif-pengajuan-list">
                            <div class="px-3 py-3 text-center text-muted">
                                <small>Tidak ada pengajuan baru</small>
                            </div>
                        </div>
                    </div>
                </div>
            </li>

            <!-- ============ Notifikasi Jatuh Tempo ============ -->
            <li class="nav-item dropdown">
                <a class="nav-link position-relative" href="javascript:void(0)" id="dropJatuhTempo"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti ti-alert-triangle fs-7"></i>
                    <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger notif-badge"
                        style="font-size:11px" id="badge-jatuh-tempo">
                        0
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="dropJatuhTempo"
                    style="min-width: 380px; max-height: 450px; overflow-y: auto;">
                    <div class="message-body">
                        <div class="px-3 py-2 bg-light border-bottom">
                            <h6 class="mb-0">Notifikasi Jatuh Tempo</h6>
                        </div>

                        <div id="notif-jatuh-tempo-list">
                            <div class="px-3 py-3 text-center text-muted">
                                <small>Tidak ada notifikasi</small>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                <!-- Tanggal dan Jam -->
                <li class="nav-item me-3">
                    <span class="text-dark">
                        <i class="ti ti-calendar"></i>
                        <span id="tanggal">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span> &nbsp;
                        <i class="ti ti-clock"></i>
                        <span id="jam">00:00:00</span>
                    </span>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <img src="{{ Auth::user()->avatar ?? asset('assets/images/profile/user-1.jpg') }}" alt=""
                            width="35" height="35" class="rounded-circle">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user fs-6"></i>
                                <p class="mb-0 fs-3">Profil Saya</p>
                            </a>
                            <a href="{{ route('profile.edit') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-key fs-6"></i>
                                <p class="mb-0 fs-3">Ubah Password</p>
                            </a>
                            <a href="{{ route('register') }}" class="d-flex align-items-center gap-2 dropdown-item">
                                <i class="ti ti-user-plus fs-6"></i>
                                <p class="mb-0 fs-3">Register Akun</p>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="px-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary mt-1 w-100">
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

<!-- ============ Modal Detail Pengajuan ============ -->
<div class="modal fade" id="modalDetailPengajuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 overflow-hidden shadow-sm">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-file-text me-2"></i>Detail Pengajuan Pinjaman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-primary d-flex align-items-center mb-4">
                    <i class="ti ti-info-circle fs-5 me-2"></i>
                    <span>Pengajuan pinjaman baru menunggu persetujuan Anda</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">ID Pengajuan</label>
                        <div class="fw-semibold fs-5" id="detail-id-ajuan">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Tanggal Pengajuan</label>
                        <div class="fw-semibold" id="detail-tanggal">-</div>
                    </div>
                    <div class="col-md-12">
                        <hr class="my-2">
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Nama Anggota</label>
                        <div class="fw-semibold text-dark fs-5" id="detail-nama">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Jenis Pinjaman</label>
                        <div id="detail-jenis">-</div>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small mb-1">Nominal Pengajuan</label>
                        <div class="fw-semibold text-success fs-7" id="detail-jumlah">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Tutup
                </button>
                <a href="{{ route('pinjaman.pengajuan') }}" class="btn btn-primary">
                    <i class="ti ti-eye me-1"></i>Lihat Data Pengajuan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ============ Modal Detail Jatuh Tempo ============ -->
<div class="modal fade" id="modalDetailJatuhTempo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-3 overflow-hidden">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-semibold">
                    <i class="ti ti-alert-triangle me-2"></i>Detail Angsuran Jatuh Tempo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert d-flex align-items-center mb-4" id="alert-status-tempo">
                    <i class="ti ti-clock-exclamation fs-4 me-2"></i>
                    <span class="fw-semibold" id="status-tempo-text">-</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Kode Pinjaman</label>
                        <div class="fw-semibold fs-5" id="tempo-kode">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Angsuran Ke</label>
                        <div class="fw-semibold" id="tempo-angsuran-ke">-</div>
                    </div>
                    <div class="col-md-12">
                        <hr class="my-2">
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Nama Anggota</label>
                        <div class="fw-semibold text-dark fs-5" id="tempo-nama">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Tanggal Jatuh Tempo</label>
                        <div class="fw-semibold text-danger fs-5" id="tempo-tanggal">-</div>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small mb-1">Jumlah Angsuran</label>
                        <div class="fw-semibold text-success fs-7" id="tempo-jumlah">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Tutup
                </button>
                <a href="#" id="btn-bayar-angsuran" class="btn btn-danger">
                    <i class="ti ti-cash me-1"></i>Bayar Angsuran
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {
            // Load notifications saat halaman dimuat
            loadPengajuanNotifications();
            loadJatuhTempoNotifications();

            // Auto refresh setiap 3 menit
            setInterval(function () {
                loadPengajuanNotifications();
                loadJatuhTempoNotifications();
            }, 180000);

            // ========== Load Pengajuan Notifications ==========
            function loadPengajuanNotifications() {
                $.ajax({
                    url: "{{ route('admin.notifications.pengajuan') }}",
                    method: 'GET',
                    success: function (response) {
                        console.log('✅ Pengajuan Response:', response);
                        if (response.success) {
                            updatePengajuanBadge(response.count);
                            renderPengajuanList(response.data);
                        }
                    },
                    error: function (xhr) {
                        console.error('❌ Error loading pengajuan notifications:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            function updatePengajuanBadge(count) {
                const badge = $('#badge-pengajuan');
                if (count > 0) {
                    badge.text(count > 99 ? '99+' : count).show();
                    // Add animation to icon
                    $('#dropPengajuan i');
                } else {
                    badge.text('0').hide();
                    $('#dropPengajuan i')
                }
            }

            function renderPengajuanList(data) {
                const container = $('#notif-pengajuan-list');

                if (data.length === 0) {
                    container.html(`
                        <div class="px-3 py-3 text-center text-muted">
                            <small>Tidak ada pengajuan baru</small>
                        </div>
                    `);
                    return;
                }

                let html = '';
                data.forEach(item => {
                    const badgeClass = item.jenis === 'Darurat' ? 'danger' :
                        item.jenis === 'Barang' ? 'info' : 'primary';

                    html += `
                        <a href="javascript:void(0)" 
                           class="dropdown-item border-bottom py-3 notif-pengajuan-item"
                           data-id="${item.id}"
                           data-id-ajuan="${item.id_ajuan}"
                           data-nama="${item.nama}"
                           data-jenis="${item.jenis}"
                           data-jumlah="${item.jumlah}"
                           data-tanggal="${item.tanggal_full}">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="round-40 d-flex align-items-center justify-content-center bg-${badgeClass}-subtle">
                                        <i class="ti ti-file-text fs-6 text-${badgeClass}"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 fw-semibold">${item.nama}</h6>
                                    <p class="mb-1 fs-2 text-muted">${item.id_ajuan}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-${badgeClass}-subtle text-${badgeClass} fw-semibold">
                                            ${item.jenis}
                                        </span>
                                        <span class="text-success fw-semibold">Rp ${formatRupiah(item.jumlah)}</span>
                                    </div>
                                    <small class="text-muted">${item.tanggal}</small>
                                </div>
                            </div>
                        </a>
                    `;
                });

                container.html(html);
            }

            // ========== Load Jatuh Tempo Notifications ==========
            function loadJatuhTempoNotifications() {
                $.ajax({
                    url: "{{ route('admin.notifications.jatuh-tempo') }}",
                    method: 'GET',
                    success: function (response) {
                        console.log('✅ Jatuh Tempo Response:', response);
                        if (response.success) {
                            updateJatuhTempoBadge(response.count);
                            renderJatuhTempoList(response.data);
                        }
                    },
                    error: function (xhr) {
                        console.error('❌ Error loading jatuh tempo notifications:', xhr);
                        console.error('Status:', xhr.status);
                        console.error('Response:', xhr.responseText);
                    }
                });
            }

            function updateJatuhTempoBadge(count) {
                const badge = $('#badge-jatuh-tempo');
                if (count > 0) {
                    badge.text(count > 99 ? '99+' : count).show();
                } else {
                    badge.text('0').hide();
                }
            }

            function renderJatuhTempoList(data) {
                const container = $('#notif-jatuh-tempo-list');

                if (data.length === 0) {
                    container.html(`
                <div class="px-3 py-3 text-center text-muted">
                    <small>Tidak ada notifikasi</small>
                </div>
            `);
                    return;
                }

                let html = '';
                data.forEach(item => {
                    html += `
                <a href="javascript:void(0)" 
                   class="dropdown-item border-bottom py-3 notif-tempo-item"
                   data-id="${item.id}"
                   data-pinjaman-id="${item.pinjaman_id}"
                   data-kode="${item.kode_pinjaman}"
                   data-nama="${item.nama}"
                   data-angsuran-ke="${item.angsuran_ke}"
                   data-tanggal="${item.tanggal_jatuh_tempo_full}"
                   data-jumlah="${item.jumlah_angsuran}"
                   data-status="${item.status}"
                   data-badge="${item.badge}"
                   data-keterangan="${item.keterangan}">
                    <div class="d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="round-40 d-flex align-items-center justify-content-center bg-${item.badge}-subtle">
                                <i class="ti ${item.icon} fs-6 text-${item.badge}"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1 fw-semibold">${item.nama}</h6>
                            <p class="mb-1 fs-2 text-muted">${item.kode_pinjaman} - Angsuran ke-${item.angsuran_ke}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-${item.badge}-subtle text-${item.badge} fw-semibold">
                                    ${item.keterangan}
                                </span>
                                <span class="text-success fw-semibold">Rp ${formatRupiah(item.jumlah_angsuran)}</span>
                            </div>
                            <small class="text-muted">${item.tanggal_jatuh_tempo_full}</small>
                        </div>
                    </div>
                </a>
            `;
                });

                container.html(html);
            }

            // ========== Click Events - Show Modal ==========
            $(document).on('click', '.notif-pengajuan-item', function () {
                const data = $(this).data();

                // Fill modal data
                $('#detail-id-ajuan').text(data.idAjuan);
                $('#detail-tanggal').text(data.tanggal);
                $('#detail-nama').text(data.nama);

                const jenisClass = data.jenis === 'Darurat' ? 'danger' :
                    data.jenis === 'Barang' ? 'info' : 'primary';
                $('#detail-jenis').html(`<span class="badge bg-${jenisClass} px-3 py-2">${data.jenis}</span>`);
                $('#detail-jumlah').text('Rp ' + formatRupiah(data.jumlah));

                // Close dropdown
                $('#dropPengajuan').dropdown('hide');

                // Show modal
                $('#modalDetailPengajuan').modal('show');
            });

            $(document).on('click', '.notif-tempo-item', function () {
                const data = $(this).data();

                // Update alert status berdasarkan kondisi
                let alertClass, alertIcon;
                if (data.status === 'terlambat') {
                    alertClass = 'alert-danger';
                    alertIcon = 'ti-alert-circle';
                } else if (data.status === 'hari_ini') {
                    alertClass = 'alert-warning';
                    alertIcon = 'ti-clock-exclamation';
                } else {
                    alertClass = 'alert-info';
                    alertIcon = 'ti-info-circle';
                }

                $('#alert-status-tempo')
                    .removeClass('alert-danger alert-warning alert-info')
                    .addClass(alertClass)
                    .find('i')
                    .removeClass('ti-alert-circle ti-clock-exclamation ti-info-circle')
                    .addClass(alertIcon);

                $('#status-tempo-text').text(data.keterangan);

                // Fill modal data
                $('#tempo-kode').text(data.kode);
                $('#tempo-nama').text(data.nama);
                $('#tempo-angsuran-ke').text('Angsuran ke-' + data.angsuranKe);
                $('#tempo-tanggal').text(data.tanggal);
                $('#tempo-jumlah').text('Rp ' + formatRupiah(data.jumlah));

                // Update button link
                const bayarUrl = "{{ route('pinjaman.bayar.show', ':id') }}".replace(':id', data.pinjamanId);
                $('#btn-bayar-angsuran').attr('href', bayarUrl);

                // Close dropdown
                $('#dropJatuhTempo').dropdown('hide');

                // Show modal
                $('#modalDetailJatuhTempo').modal('show');
            });

            // ========== Helper Functions ==========
            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID').format(angka);
            }
        });
    </script>
@endpush

<style>
    /* Notification styles */
    .notif-badge {
        font-size: 10px !important;
        padding: 2px 6px;
        min-width: 18px;
    }

    .round-40 {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .notif-pengajuan-item:hover,
    .notif-tempo-item:hover {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .dropdown-menu {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
</style>