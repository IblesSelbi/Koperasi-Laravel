<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\DataMaster;
use App\Http\Controllers\Admin\dash;
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
// ========== USER ROUTES (Anggota/Member) ==========
Route::middleware('auth')->group(function () {

    // ========== PENGAJUAN PINJAMAN USER ==========
    Route::prefix('user')->name('user.pengajuan.')->group(function () {
        Route::get('/pengajuan', [User\PengajuanUserController::class, 'index'])
            ->name('index');

        Route::get('/tambah', [User\PengajuanUserController::class, 'create'])
            ->name('create');

        Route::post('/', [User\PengajuanUserController::class, 'store'])
            ->name('store');

        Route::post('/update', [User\PengajuanUserController::class, 'update'])
            ->name('update');

        Route::post('/batal/{id}', [User\PengajuanUserController::class, 'batal'])
            ->name('batal');

        Route::get('/cetak/{id}', [User\PengajuanUserController::class, 'cetak'])
            ->name('cetak');
    });

    // ========== LAPORAN USER ==========
    Route::middleware('auth')->group(function () {
        // ========== LAPORAN USER ==========
        Route::prefix('user')->name('user.laporan.')->group(function () {
            Route::get('/simpanan', [User\LaporanUserController::class, 'simpanan'])
                ->name('simpanan');

            Route::get('/pinjaman', [User\LaporanUserController::class, 'pinjaman'])
                ->name('pinjaman');

            Route::get('/pinjaman/{id}', [User\LaporanUserController::class, 'detailPinjaman'])
                ->name('pinjaman.detail');

            Route::get('/pembayaran', [User\LaporanUserController::class, 'pembayaran'])
                ->name('pembayaran');
        });
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

        // Pemasukan Routes
        Route::get('/pemasukan', [Admin\PemasukanController::class, 'index'])
            ->name('pemasukan');

        Route::post('/pemasukan', [Admin\PemasukanController::class, 'store'])
            ->name('pemasukan.store');

        Route::put('/pemasukan/{id}', [Admin\PemasukanController::class, 'update'])
            ->name('pemasukan.update');

        Route::delete('/pemasukan/{id}', [Admin\PemasukanController::class, 'destroy'])
            ->name('pemasukan.destroy');

        // Pengeluaran Routes
        Route::get('/pengeluaran', [Admin\PengeluaranController::class, 'index'])
            ->name('pengeluaran');

        Route::post('/pengeluaran', [Admin\PengeluaranController::class, 'store'])
            ->name('pengeluaran.store');

        Route::put('/pengeluaran/{id}', [Admin\PengeluaranController::class, 'update'])
            ->name('pengeluaran.update');

        Route::delete('/pengeluaran/{id}', [Admin\PengeluaranController::class, 'destroy'])
            ->name('pengeluaran.destroy');

        // Transfer Routes
        Route::get('/transfer', [Admin\TransferController::class, 'index'])
            ->name('transfer');

        Route::post('/transfer', [Admin\TransferController::class, 'store'])
            ->name('transfer.store');

        Route::put('/transfer/{id}', [Admin\TransferController::class, 'update'])
            ->name('transfer.update');

        Route::delete('/transfer/{id}', [Admin\TransferController::class, 'destroy'])
            ->name('transfer.destroy');
    });

Route::middleware('auth')->prefix('admin')->name('simpanan.')->group(function () {
    // Setoran Tunai Routes
    Route::get('/setoran', [Admin\SetoranTunaiController::class, 'index'])
        ->name('setoran');

    Route::post('/setoran', [Admin\SetoranTunaiController::class, 'store'])
        ->name('setoran.store');

    Route::put('/setoran/{id}', [Admin\SetoranTunaiController::class, 'update'])
        ->name('setoran.update');

    Route::delete('/setoran/{id}', [Admin\SetoranTunaiController::class, 'destroy'])
        ->name('setoran.destroy');

    // Penarikan Tunai Routes
    Route::get('/penarikan', [Admin\PenarikanTunaiController::class, 'index'])
        ->name('penarikan');

    Route::post('/penarikan', [Admin\PenarikanTunaiController::class, 'store'])
        ->name('penarikan.store');

    Route::put('/penarikan/{id}', [Admin\PenarikanTunaiController::class, 'update'])
        ->name('penarikan.update');

    Route::delete('/penarikan/{id}', [Admin\PenarikanTunaiController::class, 'destroy'])
        ->name('penarikan.destroy');
});

Route::middleware('auth')->prefix('admin')->name('pinjaman.')->group(function () {
    // ========== PENGAJUAN ROUTES ==========
    Route::get('/pengajuan', [Admin\PengajuanController::class, 'index'])
        ->name('pengajuan');

    Route::post('/pengajuan/aksi', [Admin\PengajuanController::class, 'aksi'])
        ->name('pengajuan.aksi');

    Route::get('/pengajuan/cetak', [Admin\PengajuanController::class, 'cetak'])
        ->name('pengajuan.cetak');

    Route::get('/pengajuan/cetak-laporan', [Admin\PengajuanController::class, 'cetakLaporan'])
        ->name('pengajuan.cetak-laporan');

    Route::get('/pengajuan/export/excel', [Admin\PengajuanController::class, 'exportExcel'])
        ->name('pengajuan.export.excel');

    Route::get('/pengajuan/export/pdf', [Admin\PengajuanController::class, 'exportPDF'])
        ->name('pengajuan.export.pdf');

    // ========== PINJAMAN ROUTES ==========
    Route::get('/pinjaman', [Admin\PinjamanController::class, 'index'])
        ->name('pinjaman.pinjaman');

    Route::post('/pinjaman/store', [Admin\PinjamanController::class, 'store'])
        ->name('pinjaman.pinjaman.store');

    Route::get('/pinjaman/cetak-laporan', [Admin\PinjamanController::class, 'cetakLaporan'])
        ->name('pinjaman.pinjaman.cetak-laporan');

    Route::get('/pinjaman/export/excel', [Admin\PinjamanController::class, 'exportExcel'])
        ->name('pinjaman.pinjaman.export.excel');

    Route::get('/pinjaman/export/pdf', [Admin\PinjamanController::class, 'exportPDF'])
        ->name('pinjaman.pinjaman.export.pdf');

    Route::get('/pinjaman/cetak/{id}', [Admin\PinjamanController::class, 'cetak'])
        ->name('pinjaman.pinjaman.cetak');

    Route::post('/pinjaman/validasi-lunas/{id}', [Admin\PinjamanController::class, 'validasiLunas'])
        ->name('pinjaman.pinjaman.validasi-lunas');

    // ===== ROUTE DENGAN {id} HARUS PALING BAWAH =====
    Route::get('/pinjaman/detail/{id}', [Admin\PinjamanController::class, 'detail'])
        ->name('pinjaman.pinjaman.detail');

    Route::put('/pinjaman/{id}', [Admin\PinjamanController::class, 'update'])
        ->name('pinjaman.pinjaman.update');

    Route::delete('/pinjaman/{id}', [Admin\PinjamanController::class, 'destroy'])
        ->name('pinjaman.pinjaman.destroy');

    // ========== BAYAR ANGSURAN ROUTES ==========
    Route::get('/bayar', [Admin\BayarAngsuranController::class, 'index'])
        ->name('bayar');

    Route::post('/bayar/proses', [Admin\BayarAngsuranController::class, 'proses'])
        ->name('bayar.proses');

    Route::get('/bayar/cetak-bukti/{id}', [Admin\BayarAngsuranController::class, 'cetakBukti'])
        ->name('bayar.cetak-bukti');

    Route::get('/bayar/detail/{id}', [Admin\BayarAngsuranController::class, 'show'])
        ->name('bayar.detail');

    // ========== PINJAMAN LUNAS ROUTES ==========
    Route::get('/lunas', [Admin\PinjamanLunasController::class, 'index'])
        ->name('lunas');

    // Routes spesifik HARUS SEBELUM {id}
    Route::get('/lunas/cetak-detail/{id}', [Admin\PinjamanLunasController::class, 'cetakDetail'])
        ->name('lunas.cetak-detail');

    Route::get('/lunas/cetak-laporan', [Admin\PinjamanLunasController::class, 'cetakLaporan'])
        ->name('lunas.cetak-laporan');

    Route::get('/lunas/export/excel', [Admin\PinjamanLunasController::class, 'exportExcel'])
        ->name('lunas.export.excel');

    Route::get('/lunas/export/pdf', [Admin\PinjamanLunasController::class, 'exportPDF'])
        ->name('lunas.export.pdf');

    // Route {id} HARUS PALING BAWAH
    Route::get('/lunas/{id}', [Admin\PinjamanLunasController::class, 'show'])
        ->name('lunas.detail');
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

        /*
        |--------------------------------------------------------------------------
        | JENIS SIMPANAN
        |--------------------------------------------------------------------------
        */
        Route::controller(JenisSimpananController::class)->group(function () {
            Route::get('/jenis-simpanan', 'index')->name('jenis-simpanan');
            Route::post('/jenis-simpanan', 'store')->name('jenis-simpanan.store');
            Route::put('/jenis-simpanan/{id}', 'update')->name('jenis-simpanan.update');
            Route::delete('/jenis-simpanan/{id}', 'destroy')->name('jenis-simpanan.destroy');
            Route::get('/jenis-simpanan/export', 'export')->name('jenis-simpanan.export');
            Route::get('/jenis-simpanan/cetak', 'cetak')->name('jenis-simpanan.cetak');
        });

        /*
        |--------------------------------------------------------------------------
        | JENIS AKUN
        |--------------------------------------------------------------------------
        */
        Route::controller(JenisAkunController::class)->group(function () {
            Route::get('/jenis-akun', 'index')->name('jenis-akun');
            Route::post('/jenis-akun', 'store')->name('jenis-akun.store');
            Route::put('/jenis-akun/{id}', 'update')->name('jenis-akun.update');
            Route::delete('/jenis-akun/{id}', 'destroy')->name('jenis-akun.destroy');
            Route::get('/jenis-akun/export', 'export')->name('jenis-akun.export');
            Route::get('/jenis-akun/cetak', 'cetak')->name('jenis-akun.cetak');
        });

        /*
        |--------------------------------------------------------------------------
        | DATA KAS
        |--------------------------------------------------------------------------
        */
        Route::controller(DataKasController::class)->group(function () {
            Route::get('/data-kas', 'index')->name('data-kas');
            Route::post('/data-kas', 'store')->name('data-kas.store');
            Route::put('/data-kas/{id}', 'update')->name('data-kas.update');
            Route::delete('/data-kas/{id}', 'destroy')->name('data-kas.destroy');
            Route::get('/data-kas/export', 'export')->name('data-kas.export');
            Route::get('/data-kas/cetak', 'cetak')->name('data-kas.cetak');
        });

        /*
        |--------------------------------------------------------------------------
        | LAMA ANGSURAN
        |--------------------------------------------------------------------------
        */
        Route::controller(LamaAngsuranController::class)->group(function () {
            Route::get('/lama-angsuran', 'index')->name('lama-angsuran');
            Route::post('/lama-angsuran', 'store')->name('lama-angsuran.store');
            Route::put('/lama-angsuran/{id}', 'update')->name('lama-angsuran.update');
            Route::delete('/lama-angsuran/{id}', 'destroy')->name('lama-angsuran.destroy');
            Route::get('/lama-angsuran/export', 'export')->name('lama-angsuran.export');
            Route::get('/lama-angsuran/cetak', 'cetak')->name('lama-angsuran.cetak');
        });

        /*
        |--------------------------------------------------------------------------
        | DATA BARANG
        |--------------------------------------------------------------------------
        */
        Route::controller(DataBarangController::class)->group(function () {
            Route::get('/data-barang', 'index')->name('data-barang');
            Route::post('/data-barang', 'store')->name('data-barang.store');
            Route::put('/data-barang/{id}', 'update')->name('data-barang.update');
            Route::delete('/data-barang/{id}', 'destroy')->name('data-barang.destroy');
            Route::get('/data-barang/export', 'export')->name('data-barang.export');
            Route::get('/data-barang/cetak', 'cetak')->name('data-barang.cetak');
        });

        /*
        |--------------------------------------------------------------------------
        | DATA ANGGOTA
        |--------------------------------------------------------------------------
        */
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

        /*
        |--------------------------------------------------------------------------
        | DATA PENGGUNA
        |--------------------------------------------------------------------------
        */
        Route::controller(DataPenggunaController::class)->group(function () {
            Route::get('/data-pengguna', 'index')->name('data-pengguna');
            Route::post('/data-pengguna', 'store')->name('data-pengguna.store');
            Route::put('/data-pengguna/{id}', 'update')->name('data-pengguna.update');
            Route::delete('/data-pengguna/{id}', 'destroy')->name('data-pengguna.destroy');

            Route::get('/data-pengguna/export', 'export')->name('data-pengguna.export');
            Route::get('/data-pengguna/cetak', 'cetak')->name('data-pengguna.cetak');
        });

    });

Route::middleware('auth')->prefix('admin')->name('setting.')->group(function () {
    Route::get('/identitas', [Admin\IdentitasKoperasiController::class, 'index'])
        ->name('identitas');

    Route::put('/identitas', [Admin\IdentitasKoperasiController::class, 'update'])
        ->name('identitas.update');

    Route::get('/suku-bunga', [Admin\SukuBungaController::class, 'index'])
        ->name('suku-bunga');

    Route::put('/suku-bunga', [Admin\SukuBungaController::class, 'update'])
        ->name('suku-bunga.update');
});

require __DIR__ . '/auth.php';