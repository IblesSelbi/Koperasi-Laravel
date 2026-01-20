<?php

use App\Http\Controllers\ProfileController;
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
use App\Http\Controllers\Admin\Setting\{
    IdentitasKoperasiController,
    SukuBungaController
};

//USER
use App\Http\Controllers\User\PengajuanPinjaman\{
    PengajuanUserController,
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
    });

Route::middleware(['auth', 'role:user'])
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [User\DashboardController::class, 'index'])
            ->name('dashboard');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

        // LAPORAN
        Route::controller(User\LaporanUserController::class)
            ->name('user.laporan.')
            ->group(function () {

            Route::get('/laporan/simpanan', 'simpanan')->name('simpanan');
            Route::get('/laporan/pinjaman', 'pinjaman')->name('pinjaman');
            Route::get('/laporan/pinjaman/{id}', 'detailPinjaman')->name('pinjaman.detail');
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
            Route::get('/pemasukan/{id}', 'show')->name('pemasukan.show');
            Route::post('/pemasukan', 'store')->name('pemasukan.store');
            Route::put('/pemasukan/{id}', 'update')->name('pemasukan.update');
            Route::delete('/pemasukan/{id}', 'destroy')->name('pemasukan.destroy');
            Route::get('/pemasukan/cetak', 'cetak')->name('pemasukan.cetak');
        });

        // PENGELUARAN
        Route::controller(PengeluaranController::class)->group(function () {
            Route::get('/pengeluaran', 'index')->name('pengeluaran');
            Route::get('/pengeluaran/{id}', 'show')->name('pengeluaran.show');
            Route::post('/pengeluaran', 'store')->name('pengeluaran.store');
            Route::put('/pengeluaran/{id}', 'update')->name('pengeluaran.update');
            Route::delete('/pengeluaran/{id}', 'destroy')->name('pengeluaran.destroy');
        });

        // TRANSFER
        Route::controller(TransferController::class)->group(function () {
            Route::get('/transfer', 'index')->name('transfer');
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
            Route::get('/setoran/{id}', 'show')->name('setoran.show');
            Route::post('/setoran', 'store')->name('setoran.store');
            Route::put('/setoran/{id}', 'update')->name('setoran.update');
            Route::delete('/setoran/{id}', 'destroy')->name('setoran.destroy');
        });

        // PENARIKAN TUNAI
        Route::controller(PenarikanTunaiController::class)->group(function () {
            Route::get('/penarikan', 'index')->name('penarikan');
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

            Route::get('/pengajuan/cetak', 'cetak')->name('pengajuan.cetak');
            Route::get('/pengajuan/cetak-laporan', 'cetakLaporan')->name('pengajuan.cetak-laporan');

            Route::get('/pengajuan/export/excel', 'exportExcel')->name('pengajuan.export.excel');
            Route::get('/pengajuan/export/pdf', 'exportPDF')->name('pengajuan.export.pdf');
        });

        Route::controller(PinjamanController::class)->group(function () {
            Route::get('/pinjaman', 'index')->name('pinjaman');

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
            Route::get('/pinjaman/export/excel', 'exportExcel')->name('pinjaman.export.excel');
            Route::get('/pinjaman/export/pdf', 'exportPDF')->name('pinjaman.export.pdf');

            Route::post('/pinjaman', 'store')->name('pinjaman.store');
            Route::get('/pinjaman/{id}/edit', 'edit')->name('pinjaman.edit');
            Route::put('/pinjaman/{id}', 'update')->name('pinjaman.update');
            Route::delete('/pinjaman/{id}', 'destroy')->name('pinjaman.destroy');

            Route::get('/pinjaman/{id}', 'show')->name('pinjaman.detail');
            Route::get('/pinjaman/cetak/{id}', 'cetak')->name('pinjaman.cetak');
            Route::post('/pinjaman/validasi-lunas/{id}', 'validasiLunas')->name('pinjaman.validasi-lunas');
        });

        // BAYAR ANGSURAN
        Route::controller(BayarAngsuranController::class)->group(function () {
            Route::get('/bayar', 'index')->name('bayar');
            Route::get('/bayar/detail/{id}', 'show')->name('bayar.detail');
            Route::get('/bayar/{id}', 'show')->name('bayar.show');
            Route::get('/bayar/get-detail/{id}', 'getDetail')->name('bayar.getDetail');
            Route::post('/bayar/proses', 'bayar')->name('bayar.store');
            Route::get('/bayar/get-pembayaran/{id}', 'getPembayaran')->name('bayar.getPembayaran');
            Route::put('/bayar/update/{id}', 'update')->name('bayar.update');

            // Soft Delete & Restore
            Route::delete('/bayar/soft-delete/{id}', 'softDelete')->name('bayar.softDelete');
            Route::post('/bayar/restore/{id}', 'restore')->name('bayar.restore');
            Route::get('/bayar/riwayat-hapus/{pinjamanId}', 'riwayatHapus')->name('bayar.riwayatHapus');
            Route::delete('/bayar/force-delete/{id}', 'forceDelete')->name('bayar.forceDelete');

            Route::get('/bayar/cetak-nota/{id}', 'cetakNota')->name('bayar.cetak');

            // Route untuk Validasi Lunas
            Route::post('/bayar/validasi-lunas/{id}', 'validasiLunas')->name('bayar.validasi');
        });

        // routes/web.php - di dalam group Pinjaman Lunas
        Route::controller(PinjamanLunasController::class)->group(function () {
            Route::get('/lunas', 'index')->name('lunas');

            // Route spesifik di atas route dengan {id}
            Route::get('/lunas/riwayat-batal', 'riwayatBatal')->name('lunas.riwayat-batal');
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


Route::middleware('auth')->prefix('admin')->name('laporan.')->group(function () {
    // Route Data Anggota
    Route::get('/anggota', [Admin\AnggotaController::class, 'index'])
        ->name('anggota');

    Route::get('/anggota/cetak', [Admin\AnggotaController::class, 'cetakLaporan'])
        ->name('anggota.cetak');

    Route::get('/anggota/export/excel', [Admin\AnggotaController::class, 'exportExcel'])
        ->name('anggota.export.excel');

    Route::get('/anggota/export/pdf', [Admin\AnggotaController::class, 'exportPDF'])
        ->name('anggota.export.pdf');

    // Route Kas Anggota
    Route::get('/kas-anggota', [Admin\KasAnggotaController::class, 'index'])
        ->name('kas-anggota');

    Route::get('/kas-anggota/cetak', [Admin\KasAnggotaController::class, 'cetakLaporan'])
        ->name('kas-anggota.cetak');

    Route::get('/kas-anggota/export/excel', [Admin\KasAnggotaController::class, 'exportExcel'])
        ->name('kas-anggota.export.excel');

    // Route Jatuh Tempo
    Route::get('/jatuh-tempo', [Admin\JatuhTempoController::class, 'index'])
        ->name('jatuh-tempo');

    Route::get('/jatuh-tempo/cetak', [Admin\JatuhTempoController::class, 'cetakLaporan'])
        ->name('jatuh-tempo.cetak');

    Route::get('/jatuh-tempo/export/excel', [Admin\JatuhTempoController::class, 'exportExcel'])
        ->name('jatuh-tempo.export.excel');

    Route::post('/jatuh-tempo/kirim-notifikasi', [Admin\JatuhTempoController::class, 'kirimNotifikasi'])
        ->name('jatuh-tempo.kirim-notifikasi');

    // Route Kredit Macet
    Route::get('/kredit-macet', [Admin\KreditMacetController::class, 'index'])
        ->name('kredit-macet');

    Route::get('/kredit-macet/cetak', [Admin\KreditMacetController::class, 'cetakLaporan'])
        ->name('kredit-macet.cetak');

    Route::get('/kredit-macet/export/excel', [Admin\KreditMacetController::class, 'exportExcel'])
        ->name('kredit-macet.export.excel');

    Route::post('/kredit-macet/kirim-pemanggilan', [Admin\KreditMacetController::class, 'kirimPemanggilan'])
        ->name('kredit-macet.kirim-pemanggilan');

    Route::post('/kredit-macet/get-data', [Admin\KreditMacetController::class, 'getData'])
        ->name('kredit-macet.get-data');

    // Route Transaksi Kas
    Route::get('/transaksi-kas', [Admin\TransaksiKasController::class, 'index'])
        ->name('transaksi-kas');

    Route::get('/transaksi-kas/cetak', [Admin\TransaksiKasController::class, 'cetakLaporan'])
        ->name('transaksi-kas.cetak');

    Route::get('/transaksi-kas/export/excel', [Admin\TransaksiKasController::class, 'exportExcel'])
        ->name('transaksi-kas.export.excel');

    Route::get('/transaksi-kas/export/pdf', [Admin\TransaksiKasController::class, 'exportPDF'])
        ->name('transaksi-kas.export.pdf');

    Route::post('/transaksi-kas/get-data', [Admin\TransaksiKasController::class, 'getData'])
        ->name('transaksi-kas.get-data');

    // Route Buku Besar
    Route::get('/buku-besar', [Admin\BukuBesarController::class, 'index'])
        ->name('buku-besar');

    Route::get('/buku-besar/cetak', [Admin\BukuBesarController::class, 'cetakLaporan'])
        ->name('buku-besar.cetak');

    Route::get('/buku-besar/export/excel', [Admin\BukuBesarController::class, 'exportExcel'])
        ->name('buku-besar.export.excel');

    Route::post('/buku-besar/get-data', [Admin\BukuBesarController::class, 'getData'])
        ->name('buku-besar.get-data');

    // Route Neraca Saldo
    Route::get('/neraca-saldo', [Admin\NeracaSaldoController::class, 'index'])
        ->name('neraca-saldo');

    Route::get('/neraca-saldo/cetak', [Admin\NeracaSaldoController::class, 'cetakLaporan'])
        ->name('neraca-saldo.cetak');

    Route::post('/neraca-saldo/get-data', [Admin\NeracaSaldoController::class, 'getData'])
        ->name('neraca-saldo.get-data');

    // Route Kas Simpanan
    Route::get('/kas-simpanan', [Admin\KasSimpananController::class, 'index'])
        ->name('kas-simpanan');

    Route::get('/kas-simpanan/cetak', [Admin\KasSimpananController::class, 'cetakLaporan'])
        ->name('kas-simpanan.cetak');

    // Route Kas Pinjaman
    Route::get('/kas-pinjaman', [Admin\KasPinjamanController::class, 'index'])
        ->name('kas-pinjaman');

    Route::get('/kas-pinjaman/cetak', [Admin\KasPinjamanController::class, 'cetakLaporan'])
        ->name('kas-pinjaman.cetak');

    // Route Saldo Kas
    Route::get('/saldo-kas', [Admin\SaldoKasController::class, 'index'])
        ->name('saldo-kas');

    Route::get('/saldo-kas/cetak', [Admin\SaldoKasController::class, 'cetakLaporan'])
        ->name('saldo-kas.cetak');

    // Route Laba Rugi
    Route::get('/laba-rugi', [Admin\LabaRugiController::class, 'index'])
        ->name('laba-rugi');

    Route::get('/laba-rugi/cetak', [Admin\LabaRugiController::class, 'cetakLaporan'])
        ->name('laba-rugi.cetak');

    // Route SHU
    Route::get('/shu', [Admin\SHUController::class, 'index'])
        ->name('shu');

    Route::get('/shu/cetak', [Admin\SHUController::class, 'cetakLaporan'])
        ->name('shu.cetak');
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