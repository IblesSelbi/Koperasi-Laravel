<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\User;
use App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DataMaster\{
    JenisSimpananController,
    JenisAkunController,
    DataKasController,
    LamaAngsuranController,
    DataBarangController,
    DataAnggotaController,
    DataPenggunaController
};
use App\Http\Controllers\Admin\TransaksiKas\{
    PemasukanController,
    PengeluaranController,
    TransferController
};
use App\Http\Controllers\Admin\Simpanan\{
    SetoranTunaiController,
    PenarikanTunaiController
};
use App\Http\Controllers\Admin\Pinjaman\{
    PengajuanController,
    PinjamanController,
    BayarAngsuranController,
    PinjamanLunasController
};
use App\Http\Controllers\Admin\Laporan\{
    AnggotaController,
    KasAnggotaController,
    JatuhTempoController,
    KreditMacetController,
    TransaksiKasController,
    BukuBesarController,
    NeracaSaldoController,
    KasSimpananController,
    KasPinjamanController,
    SaldoKasController,
    LabaRugiController,
    SHUController
};
use App\Http\Controllers\Admin\Setting\{
    IdentitasKoperasiController,
    SukuBungaController
};

//USER
use App\Http\Controllers\User\PengajuanPinjaman\{
    PengajuanUserController,
};
use App\Http\Controllers\User\Laporan\{
    LaporanUserController,
};
use App\Http\Controllers\User\BayarAngsuran\{
    BayarAngsuranUSerController,
};

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [Auth\AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [Auth\AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/register', [Auth\RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('/register', [Auth\RegisteredUserController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', [Auth\AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [Admin\AdminProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [Admin\AdminProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [Admin\AdminProfileController::class, 'updatePassword'])
            ->name('profile.password.update');

        Route::delete('/profile', [Admin\AdminProfileController::class, 'destroy'])
            ->name('profile.destroy');

        Route::patch('/profile/image', [Admin\AdminProfileController::class, 'updateImage'])
            ->name('profile.updateImage');

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/pengajuan', [Admin\NotificationController::class, 'getPengajuanBaru'])
                ->name('pengajuan');

            Route::get('/jatuh-tempo', [Admin\NotificationController::class, 'getJatuhTempo'])
                ->name('jatuh-tempo');
        });
    });

/*
|--------------------------------------------------------------------------
| USER ROUTES - PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [User\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [User\UserProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::patch('/profile', [User\UserProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [User\UserProfileController::class, 'updatePassword'])
            ->name('profile.password.update');

        Route::delete('/profile', [User\UserProfileController::class, 'destroy'])
            ->name('profile.destroy');

        Route::patch('/profile/image', [User\UserProfileController::class, 'updateImage'])
            ->name('profile.updateImage');
    });


/*
|--------------------------------------------------------------------------
| USERS - TAMBAHKAN MIDDLEWARE AUTH
|--------------------------------------------------------------------------
*/
// USER ROUTES (Anggota/Member)
Route::middleware('auth')
    ->prefix('user')
    ->group(function () {

        // PENGAJUAN PINJAMAN
        Route::controller(PengajuanUserController::class)
            ->name('user.pengajuan.')
            ->group(function () {

            Route::get('/pengajuan', 'index')->name('index');
            Route::get('/pengajuan/tambah', 'create')->name('create');

            Route::post('/pengajuan', 'store')->name('store');
            Route::post('/pengajuan/update', 'update')->name('update');

            Route::post('/pengajuan/batal/{id}', 'batal')->name('batal');
            Route::get('/pengajuan/cetak/{id}', 'cetak')->name('cetak');
        });

        // BAYAR ANGSURAN
        Route::controller(BayarAngsuranUserController::class)
            ->name('user.bayar.')
            ->group(function () {

            Route::get('/bayar-angsuran', 'index')->name('index');
            Route::get('/bayar-angsuran/{id}', 'show')->name('show');
            Route::post('/bayar-angsuran/bayar', 'bayar')->name('bayar');
            Route::get('/riwayat-bayar', 'riwayat')->name('riwayat');
        });

        // LAPORAN USER
        Route::controller(LaporanUserController::class)
            ->name('user.laporan.')
            ->group(function () {

            // SIMPANAN
            Route::get('/laporan/simpanan', 'simpanan')->name('simpanan');

            // PINJAMAN
            Route::get('/laporan/pinjaman', 'pinjaman')->name('pinjaman');
            Route::get('/laporan/pinjaman/{id}', 'detailPinjaman')->name('pinjaman.detail');

            // PEMBAYARAN
            Route::get('/laporan/pembayaran', 'pembayaran')->name('pembayaran');
        });
    });


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('admin')
    ->name('kas.')
    ->group(function () {

        // PEMASUKAN
        Route::controller(PemasukanController::class)->group(function () {
            Route::get('/pemasukan', 'index')->name('pemasukan');
            Route::get('/pemasukan/cetak', 'cetakLaporan')->name('pemasukan.cetak');
            Route::get('/pemasukan/{id}', 'show')->name('pemasukan.show');
            Route::post('/pemasukan', 'store')->name('pemasukan.store');
            Route::put('/pemasukan/{id}', 'update')->name('pemasukan.update');
            Route::delete('/pemasukan/{id}', 'destroy')->name('pemasukan.destroy');
        });

        // PENGELUARAN
        Route::controller(PengeluaranController::class)->group(function () {
            Route::get('/pengeluaran', 'index')->name('pengeluaran');
            Route::get('/pengeluaran/cetak', 'cetakLaporan')->name('pengeluaran.cetak');
            Route::get('/pengeluaran/{id}', 'show')->name('pengeluaran.show');
            Route::post('/pengeluaran', 'store')->name('pengeluaran.store');
            Route::put('/pengeluaran/{id}', 'update')->name('pengeluaran.update');
            Route::delete('/pengeluaran/{id}', 'destroy')->name('pengeluaran.destroy');
        });

        // TRANSFER
        Route::controller(TransferController::class)->group(function () {
            Route::get('/transfer', 'index')->name('transfer');
            Route::get('/transfer/cetak', 'cetakLaporan')->name('transfer.cetak');
            Route::get('/transfer/{id}', 'show')->name('transfer.show');
            Route::post('/transfer', 'store')->name('transfer.store');
            Route::put('/transfer/{id}', 'update')->name('transfer.update');
            Route::delete('/transfer/{id}', 'destroy')->name('transfer.destroy');
        });

    });

Route::middleware('auth')
    ->prefix('admin')
    ->name('simpanan.')
    ->group(function () {

        // SETORAN TUNAI
        Route::controller(SetoranTunaiController::class)->group(function () {
            Route::get('/setoran', 'index')->name('setoran');
            Route::get('/setoran/cetak-laporan', 'cetakLaporan')->name('setoran.cetak-laporan');
            Route::get('/setoran/cetak/{id}', 'cetakNota')->name('setoran.cetak');
            Route::get('/setoran/anggota-detail/{id}', 'getAnggotaDetail')->name('setoran.anggota.detail');
            Route::get('/setoran/cetak-pdf/{id}', 'cetakPDF')->name('setoran.cetak.pdf');
            Route::get('/setoran/{id}', 'show')->name('setoran.show');
            Route::post('/setoran', 'store')->name('setoran.store');
            Route::put('/setoran/{id}', 'update')->name('setoran.update');
            Route::delete('/setoran/{id}', 'destroy')->name('setoran.destroy');
        });

        // PENARIKAN TUNAI
        Route::controller(PenarikanTunaiController::class)->group(function () {
            Route::get('/penarikan', 'index')->name('penarikan');
            Route::get('/penarikan/cetak-laporan', 'cetakLaporan')->name('penarikan.cetak-laporan');
            Route::get('/penarikan/cetak/{id}', 'cetakNota')->name('penarikan.cetak');
            Route::get('/penarikan/anggota-detail/{id}', 'getAnggotaDetail')->name('penarikan.anggota.detail');
            Route::get('/penarikan/cetak-pdf/{id}', 'cetakPDF')->name('penarikan.cetak.pdf');
            Route::get('/penarikan/{id}', 'show')->name('penarikan.show');
            Route::post('/penarikan', 'store')->name('penarikan.store');
            Route::put('/penarikan/{id}', 'update')->name('penarikan.update');
            Route::delete('/penarikan/{id}', 'destroy')->name('penarikan.destroy');
        });

    });


Route::middleware('auth')
    ->prefix('admin')
    ->name('pinjaman.')
    ->group(function () {

        // PENGAJUAN PINJAMAN
        Route::controller(PengajuanController::class)->group(function () {
            Route::get('/pengajuan', 'index')->name('pengajuan');
            Route::post('/pengajuan/aksi', 'aksi')->name('pengajuan.aksi');
        
            Route::get('/pengajuan/cetak/{id}', 'cetak')->name('pengajuan.cetak-single');
            Route::get('/pengajuan/cetak-laporan', 'cetakLaporan')->name('pengajuan.cetak');

            Route::get('/pengajuan/export/excel', 'exportExcel')->name('pengajuan.export.excel');
            Route::get('/pengajuan/export/pdf', 'exportPDF')->name('pengajuan.export.pdf');
        });

        Route::controller(PinjamanController::class)->group(function () {
            Route::get('/pinjaman', 'index')->name('pinjaman');

            Route::get('/pinjaman/anggota-detail/{id}', 'getAnggotaDetail')->name('anggota.detail');

            // NEW: Soft Delete Routes
            Route::get('/pinjaman/{id}/delete-info', 'getDeleteInfo')->name('pinjaman.delete-info');
            Route::delete('/pinjaman/{id}/soft-delete-with-reason', 'softDeleteWithReason')->name('pinjaman.soft-delete-reason');
            Route::get('/pinjaman/riwayat-hapus', 'riwayatHapus')->name('pinjaman.riwayat-hapus');
            Route::post('/pinjaman/{id}/restore', 'restore')->name('pinjaman.restore');
            Route::delete('/pinjaman/{id}/force-delete', 'forceDelete')->name('pinjaman.force-delete');

            // Existing routes...
            Route::get('/pinjaman/pengajuan-disetujui', 'getPengajuanDisetujui')->name('pinjaman.pengajuan-disetujui');
            Route::get('/pinjaman/pengajuan-detail/{id}', 'getDetailPengajuan')->name('pinjaman.detail-pengajuan');
            Route::get('/pinjaman/kas-list', 'getKasList')->name('pinjaman.kas-list');
            Route::post('/pinjaman/{id}/recalculate', 'recalculate')->name('pinjaman.recalculate');

            Route::get('/pinjaman/cetak-laporan', 'cetakLaporan')->name('pinjaman.cetak.laporan');
            Route::get('/pinjaman/cetak/{id}', 'cetak')->name('pinjaman.cetak');
            Route::get('/pinjaman/export/excel', 'exportExcel')->name('pinjaman.export.excel');
            Route::get('/pinjaman/export/pdf', 'exportPDF')->name('pinjaman.export.pdf');

            Route::post('/pinjaman', 'store')->name('pinjaman.store');
            Route::get('/pinjaman/{id}/edit', 'edit')->name('pinjaman.edit');
            Route::put('/pinjaman/{id}', 'update')->name('pinjaman.update');
            Route::delete('/pinjaman/{id}', 'destroy')->name('pinjaman.destroy');

            Route::get('/pinjaman/{id}', 'show')->name('pinjaman.detail');
            Route::get('/pinjaman/cetak-detail/{id}', 'cetakDetail')->name('pinjaman.cetak.detail');
            Route::post('/pinjaman/validasi-lunas/{id}', 'validasiLunas')->name('pinjaman.validasi-lunas');
        });

        // BAYAR ANGSURAN
        Route::controller(BayarAngsuranController::class)->group(function () {
            // List & Detail
            Route::get('/bayar', 'index')->name('bayar');
            Route::get('/bayar/detail/{id}', 'show')->name('bayar.detail');

            // CRITICAL: Route spesifik HARUS di atas route dinamis
            Route::get('/bayar/get-bukti-transfer/{id}', 'getBuktiTransfer')->name('bayar.getBuktiTransfer');
            Route::get('/bayar/get-detail/{id}', 'getDetail')->name('bayar.getDetail');
            Route::get('/bayar/get-pembayaran/{id}', 'getPembayaran')->name('bayar.getPembayaran');
            Route::get('/bayar/pending-detail/{id}', 'pendingDetail')->name('bayar.pendingDetail');
            Route::get('/bayar/cetak-nota/{id}', 'cetakNota')->name('bayar.cetak');
            Route::get('/bayar/riwayat-hapus/{pinjamanId}', 'riwayatHapus')->name('bayar.riwayatHapus');

            // Route dinamis (HARUS di bawah semua route spesifik)
            Route::get('/bayar/{id}', 'show')->name('bayar.show');

            // Pembayaran Tunai (Admin)
            Route::post('/bayar/proses', 'bayar')->name('bayar.store');
            Route::put('/bayar/update/{id}', 'update')->name('bayar.update');

            // Soft Delete & Restore
            Route::delete('/bayar/soft-delete/{id}', 'softDelete')->name('bayar.softDelete');
            Route::post('/bayar/restore/{id}', 'restore')->name('bayar.restore');
            Route::delete('/bayar/force-delete/{id}', 'forceDelete')->name('bayar.forceDelete');

            // Validasi Lunas
            Route::post('/bayar/validasi-lunas/{id}', 'validasiLunas')->name('bayar.validasi');

            // Verifikasi Pembayaran Transfer
            Route::post('/bayar/approve-transfer/{id}', 'approveTransfer')->name('bayar.approveTransfer');
            Route::post('/bayar/reject-transfer/{id}', 'rejectTransfer')->name('bayar.rejectTransfer');
        });

        // routes/web.php - di dalam group Pinjaman Lunas
        Route::controller(PinjamanLunasController::class)->group(function () {
            Route::get('/lunas', 'index')->name('lunas');

            // Route spesifik di atas route dengan {id}
            Route::get('/lunas/riwayat-batal', 'riwayatBatal')->name('lunas.riwayat-batal');
            Route::get('/lunas/cetak/{id}', 'cetak')->name('lunas.cetak');
            Route::get('/lunas/cetak-detail/{id}', 'cetakDetail')->name('lunas.cetak-detail');
            Route::get('/lunas/cetak-laporan', 'cetakLaporan')->name('lunas.cetak-laporan');
            Route::get('/lunas/export/excel', 'exportExcel')->name('lunas.export.excel');
            Route::get('/lunas/export/pdf', 'exportPDF')->name('lunas.export.pdf');

            // Batalkan pelunasan (Admin Only)
            Route::post('/lunas/batalkan/{id}', 'batalkanLunas')
                ->name('lunas.batalkan')
                ->middleware('role:admin');

            // Restore pelunasan (Admin Only)
            Route::post('/lunas/restore/{id}', 'restorePelunasan')
                ->name('lunas.restore')
                ->middleware('role:admin');

            // Route dengan {id} di paling bawah
            Route::get('/lunas/{id}', 'show')->name('lunas.detail');
        });

    });


Route::middleware('auth')
    ->prefix('admin')
    ->name('laporan.')
    ->group(function () {

        // Anggota
        Route::controller(AnggotaController::class)->group(function () {
            Route::get('/anggota', 'index')->name('anggota');
            Route::get('/anggota/cetak', 'cetakLaporan')->name('anggota.cetak');
            Route::get('/anggota/export/excel', 'exportExcel')->name('anggota.export.excel');
            Route::get('/anggota/export/pdf', 'exportPDF')->name('anggota.export.pdf');
        });

        // Kas Anggota
        Route::controller(KasAnggotaController::class)->group(function () {
            Route::get('/kas-anggota', 'index')->name('kas-anggota');
            Route::get('/kas-anggota/cetak', 'cetakLaporan')->name('kas-anggota.cetak');
            Route::get('/kas-anggota/export/excel', 'exportExcel')->name('kas-anggota.export.excel');
        });

        // Jatuh Tempo
        Route::controller(JatuhTempoController::class)->group(function () {
            Route::get('/jatuh-tempo', 'index')->name('jatuh-tempo');
            Route::get('/jatuh-tempo/cetak', 'cetakLaporan')->name('jatuh-tempo.cetak');
            Route::get('/jatuh-tempo/export/excel', 'exportExcel')->name('jatuh-tempo.export.excel');
            Route::post('/jatuh-tempo/kirim-notifikasi', 'kirimNotifikasi')
                ->name('jatuh-tempo.kirim-notifikasi');
        });

        // Kredit Macet
        Route::controller(KreditMacetController::class)->group(function () {
            Route::get('/kredit-macet', 'index')->name('kredit-macet');
            Route::get('/kredit-macet/cetak', 'cetakLaporan')->name('kredit-macet.cetak');
            Route::get('/kredit-macet/export/excel', 'exportExcel')->name('kredit-macet.export.excel');
            Route::post('/kredit-macet/kirim-pemanggilan', 'kirimPemanggilan')
                ->name('kredit-macet.kirim-pemanggilan');
            Route::post('/kredit-macet/get-data', 'getData')
                ->name('kredit-macet.get-data');
        });

        Route::controller(TransaksiKasController::class)->group(function () {
            Route::get('/transaksi-kas', 'index')->name('transaksi-kas');
            Route::get('/transaksi-kas/cetak', 'cetakLaporan')->name('transaksi-kas.cetak');
            Route::get('/transaksi-kas/export/excel', 'exportExcel')->name('transaksi-kas.export.excel');
            Route::get('/transaksi-kas/export/pdf', 'exportPDF')->name('transaksi-kas.export.pdf');
            Route::post('/transaksi-kas/get-data', 'getData')->name('transaksi-kas.get-data');
        });

        // Buku Besar
        Route::controller(BukuBesarController::class)->group(function () {
            Route::get('/buku-besar', 'index')->name('buku-besar');
            Route::get('/buku-besar/cetak', 'cetakLaporan')->name('buku-besar.cetak');
            Route::get('/buku-besar/export/excel', 'exportExcel')->name('buku-besar.export.excel');
            Route::post('/buku-besar/get-data', 'getData')->name('buku-besar.get-data');
        });

        // Neraca Saldo
        Route::controller(NeracaSaldoController::class)->group(function () {
            Route::get('/neraca-saldo', 'index')->name('neraca-saldo');
            Route::get('/neraca-saldo/cetak', 'cetakLaporan')->name('neraca-saldo.cetak');
            Route::post('/neraca-saldo/get-data', 'getData')->name('neraca-saldo.get-data');
        });

        // Kas Simpanan
        Route::controller(KasSimpananController::class)->group(function () {
            Route::get('/kas-simpanan', 'index')->name('kas-simpanan');
            Route::get('/kas-simpanan/cetak', 'cetakLaporan')->name('kas-simpanan.cetak');
        });

        // Kas Pinjaman
        Route::controller(KasPinjamanController::class)->group(function () {
            Route::get('/kas-pinjaman', 'index')->name('kas-pinjaman');
            Route::get('/kas-pinjaman/cetak', 'cetakLaporan')->name('kas-pinjaman.cetak');
        });

        // Saldo Kas
        Route::controller(SaldoKasController::class)->group(function () {
            Route::get('/saldo-kas', 'index')->name('saldo-kas');
            Route::get('/saldo-kas/cetak', 'cetakLaporan')->name('saldo-kas.cetak');
        });

        // Laba Rugi
        Route::controller(LabaRugiController::class)->group(function () {
            Route::get('/laba-rugi', 'index')->name('laba-rugi');
            Route::get('/laba-rugi/cetak', 'cetakLaporan')->name('laba-rugi.cetak');
        });

        // SHU
        Route::controller(SHUController::class)->group(function () {
            Route::get('/shu', 'index')->name('shu');
            Route::get('/shu/cetak', 'cetakLaporan')->name('shu.cetak');
        });

    });


Route::middleware('auth')
    ->prefix('admin')
    ->name('master.')
    ->group(function () {

        // JENIS SIMPANAN
        Route::controller(JenisSimpananController::class)->group(function () {
            Route::get('/jenis-simpanan', 'index')->name('jenis-simpanan');
            Route::post('/jenis-simpanan', 'store')->name('jenis-simpanan.store');
            Route::put('/jenis-simpanan/{id}', 'update')->name('jenis-simpanan.update');
            Route::delete('/jenis-simpanan/{id}', 'destroy')->name('jenis-simpanan.destroy');
            Route::get('/jenis-simpanan/export', 'export')->name('jenis-simpanan.export');
            Route::get('/jenis-simpanan/cetak', 'cetak')->name('jenis-simpanan.cetak');
        });

        // JENIS AKUN
        Route::controller(JenisAkunController::class)->group(function () {
            Route::get('/jenis-akun', 'index')->name('jenis-akun');
            Route::post('/jenis-akun', 'store')->name('jenis-akun.store');
            Route::put('/jenis-akun/{id}', 'update')->name('jenis-akun.update');
            Route::delete('/jenis-akun/{id}', 'destroy')->name('jenis-akun.destroy');
            Route::get('/jenis-akun/export', 'export')->name('jenis-akun.export');
            Route::get('/jenis-akun/cetak', 'cetak')->name('jenis-akun.cetak');
        });

        // DATA KAS
        Route::controller(DataKasController::class)->group(function () {
            Route::get('/data-kas', 'index')->name('data-kas');
            Route::post('/data-kas', 'store')->name('data-kas.store');
            Route::put('/data-kas/{id}', 'update')->name('data-kas.update');
            Route::delete('/data-kas/{id}', 'destroy')->name('data-kas.destroy');
            Route::get('/data-kas/export', 'export')->name('data-kas.export');
            Route::get('/data-kas/cetak', 'cetak')->name('data-kas.cetak');
        });

        // LAMA ANGSURAN
        Route::controller(LamaAngsuranController::class)->group(function () {
            // API Endpoints 
            Route::get('/lama-angsuran/list', 'list')->name('lama-angsuran.list');
            Route::get('/lama-angsuran/export', 'export')->name('lama-angsuran.export');
            Route::get('/lama-angsuran/cetak', 'cetak')->name('lama-angsuran.cetak');

            // Index & CRUD
            Route::get('/lama-angsuran', 'index')->name('lama-angsuran');
            Route::post('/lama-angsuran', 'store')->name('lama-angsuran.store');
            Route::put('/lama-angsuran/{id}', 'update')->name('lama-angsuran.update');
            Route::delete('/lama-angsuran/{id}', 'destroy')->name('lama-angsuran.destroy');
        });

        // DATA BARANG
        Route::controller(DataBarangController::class)->group(function () {
            Route::get('/data-barang', 'index')->name('data-barang');
            Route::post('/data-barang', 'store')->name('data-barang.store');
            Route::put('/data-barang/{id}', 'update')->name('data-barang.update');
            Route::delete('/data-barang/{id}', 'destroy')->name('data-barang.destroy');
            Route::get('/data-barang/export', 'export')->name('data-barang.export');
            Route::get('/data-barang/cetak', 'cetak')->name('data-barang.cetak');
        });

        // DATA ANGGOTA
        Route::controller(DataAnggotaController::class)->group(function () {
            Route::get('/data-anggota', 'index')->name('data-anggota');
            Route::post('/data-anggota', 'store')->name('data-anggota.store');
            Route::get('/data-anggota/{id}/edit', 'edit')->name('data-anggota.edit');
            Route::put('/data-anggota/{id}', 'update')->name('data-anggota.update');
            Route::delete('/data-anggota/{id}', 'destroy')->name('data-anggota.destroy');

            Route::get('/data-anggota/export', 'export')->name('data-anggota.export');
            Route::get('/data-anggota/import', 'showImport')->name('data-anggota.import');
            Route::post('/data-anggota/import', 'processImport')->name('data-anggota.import.process');
            Route::get('/data-anggota/cetak', 'cetak')->name('data-anggota.cetak');
        });

        // DATA PENGGUNA
        Route::controller(DataPenggunaController::class)->group(function () {
            Route::get('/data-pengguna', 'index')->name('data-pengguna');
            Route::post('/data-pengguna', 'store')->name('data-pengguna.store');
            Route::put('/data-pengguna/{id}', 'update')->name('data-pengguna.update');
            Route::delete('/data-pengguna/{id}', 'destroy')->name('data-pengguna.destroy');

            Route::get('/data-pengguna/export', 'export')->name('data-pengguna.export');
            Route::get('/data-pengguna/cetak', 'cetak')->name('data-pengguna.cetak');
        });

    });

Route::middleware('auth')
    ->prefix('admin')
    ->name('setting.')
    ->group(function () {

        // IDENTITAS KOPERASI
        Route::controller(IdentitasKoperasiController::class)->group(function () {
            Route::get('/identitas', 'index')->name('identitas');
            Route::put('/identitas', 'update')->name('identitas.update');
        });

        // SUKU BUNGA
        Route::controller(SukuBungaController::class)->group(function () {
            Route::get('/suku-bunga', 'index')->name('suku-bunga');
            Route::put('/suku-bunga', 'update')->name('suku-bunga.update');
        });

    });


require __DIR__ . '/auth.php';