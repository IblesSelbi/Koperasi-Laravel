<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\User;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\User\DashboardController as UserDashboard;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        session(['user_role' => $request->role ?? 'user']);

        if ($request->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

/*
|--------------------------------------------------------------------------
| REGISTER
|--------------------------------------------------------------------------
*/
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', function (Request $request) {
    // TODO: Implement proper registration logic
    session(['user_role' => $request->role ?? 'user']);

    if ($request->role === 'admin') {
        return redirect('/admin/dashboard');
    }
    return redirect('/dashboard');
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [UserDashboard::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
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

Route::middleware('auth')->prefix('admin')->name('master.')->group(function () {
    // Route Jenis Simpanan
    Route::get('/jenis-simpanan', [Admin\JenisSimpananController::class, 'index'])
        ->name('jenis-simpanan');
    Route::post('/jenis-simpanan', [Admin\JenisSimpananController::class, 'store'])
        ->name('jenis-simpanan.store');
    Route::put('/jenis-simpanan/{id}', [Admin\JenisSimpananController::class, 'update'])
        ->name('jenis-simpanan.update');
    Route::delete('/jenis-simpanan/{id}', [Admin\JenisSimpananController::class, 'destroy'])
        ->name('jenis-simpanan.destroy');
    Route::get('/jenis-simpanan/export', [Admin\JenisSimpananController::class, 'export'])
        ->name('jenis-simpanan.export');
    Route::get('/jenis-simpanan/cetak', [Admin\JenisSimpananController::class, 'cetak'])
        ->name('jenis-simpanan.cetak');

    // Route Jenis Akun
    Route::get('/jenis-akun', [Admin\JenisAkunController::class, 'index'])
        ->name('jenis-akun');
    Route::post('/jenis-akun', [Admin\JenisAkunController::class, 'store'])
        ->name('jenis-akun.store');
    Route::put('/jenis-akun/{id}', [Admin\JenisAkunController::class, 'update'])
        ->name('jenis-akun.update');
    Route::delete('/jenis-akun/{id}', [Admin\JenisAkunController::class, 'destroy'])
        ->name('jenis-akun.destroy');
    Route::get('/jenis-akun/export', [Admin\JenisAkunController::class, 'export'])
        ->name('jenis-akun.export');
    Route::get('/jenis-akun/cetak', [Admin\JenisAkunController::class, 'cetak'])
        ->name('jenis-akun.cetak');

    // Route Data Kas
    Route::get('/data-kas', [Admin\DataKasController::class, 'index'])
        ->name('data-kas');
    Route::post('/data-kas', [Admin\DataKasController::class, 'store'])
        ->name('data-kas.store');
    Route::put('/data-kas/{id}', [Admin\DataKasController::class, 'update'])
        ->name('data-kas.update');
    Route::delete('/data-kas/{id}', [Admin\DataKasController::class, 'destroy'])
        ->name('data-kas.destroy');
    Route::get('/data-kas/export', [Admin\DataKasController::class, 'export'])
        ->name('data-kas.export');
    Route::get('/data-kas/cetak', [Admin\DataKasController::class, 'cetak'])
        ->name('data-kas.cetak');

    // Route Lama Angsuran
    Route::get('/lama-angsuran', [Admin\LamaAngsuranController::class, 'index'])
        ->name('lama-angsuran');
    Route::post('/lama-angsuran', [Admin\LamaAngsuranController::class, 'store'])
        ->name('lama-angsuran.store');
    Route::put('/lama-angsuran/{id}', [Admin\LamaAngsuranController::class, 'update'])
        ->name('lama-angsuran.update');
    Route::delete('/lama-angsuran/{id}', [Admin\LamaAngsuranController::class, 'destroy'])
        ->name('lama-angsuran.destroy');
    Route::get('/lama-angsuran/export', [Admin\LamaAngsuranController::class, 'export'])
        ->name('lama-angsuran.export');
    Route::get('/lama-angsuran/cetak', [Admin\LamaAngsuranController::class, 'cetak'])
        ->name('lama-angsuran.cetak');

    // Route Data Barang
    Route::get('/data-barang', [Admin\DataBarangController::class, 'index'])
        ->name('data-barang');
    Route::post('/data-barang', [Admin\DataBarangController::class, 'store'])
        ->name('data-barang.store');
    Route::put('/data-barang/{id}', [Admin\DataBarangController::class, 'update'])
        ->name('data-barang.update');
    Route::delete('/data-barang/{id}', [Admin\DataBarangController::class, 'destroy'])
        ->name('data-barang.destroy');
    Route::get('/data-barang/export', [Admin\DataBarangController::class, 'export'])
        ->name('data-barang.export');
    Route::get('/data-barang/cetak', [Admin\DataBarangController::class, 'cetak'])
        ->name('data-barang.cetak');

    // Route Data Anggota 
    Route::get('/data-anggota/export', [Admin\DataAnggotaController::class, 'export'])
        ->name('data-anggota.export');

    Route::get('/data-anggota/import', [Admin\DataAnggotaController::class, 'showImport'])
        ->name('data-anggota.import');

    Route::post('/data-anggota/import', [Admin\DataAnggotaController::class, 'processImport'])
        ->name('data-anggota.import.process');

    Route::get('/data-anggota/cetak', [Admin\DataAnggotaController::class, 'cetak'])
        ->name('data-anggota.cetak');

    Route::get('/data-anggota', [Admin\DataAnggotaController::class, 'index'])
        ->name('data-anggota');

    Route::post('/data-anggota', [Admin\DataAnggotaController::class, 'store'])
        ->name('data-anggota.store');

    Route::put('/data-anggota/{id}', [Admin\DataAnggotaController::class, 'update'])
        ->name('data-anggota.update');

    Route::delete('/data-anggota/{id}', [Admin\DataAnggotaController::class, 'destroy'])
        ->name('data-anggota.destroy');


    // Route Data Pengguna - Route SPESIFIK harus di ATAS route umum
    Route::get('/data-pengguna/export', [Admin\DataPenggunaController::class, 'export'])
        ->name('data-pengguna.export');

    Route::get('/data-pengguna/cetak', [Admin\DataPenggunaController::class, 'cetak'])
        ->name('data-pengguna.cetak');

    Route::get('/data-pengguna', [Admin\DataPenggunaController::class, 'index'])
        ->name('data-pengguna');

    Route::post('/data-pengguna', [Admin\DataPenggunaController::class, 'store'])
        ->name('data-pengguna.store');

    Route::put('/data-pengguna/{id}', [Admin\DataPenggunaController::class, 'update'])
        ->name('data-pengguna.update');

    Route::delete('/data-pengguna/{id}', [Admin\DataPenggunaController::class, 'destroy'])
        ->name('data-pengguna.destroy');
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

// require __DIR__ . '/auth.php';