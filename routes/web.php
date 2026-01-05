<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\admin;
use Illuminate\Support\Facades\Route;

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [admin\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')
    ->prefix('kas')
    ->name('kas.')
    ->group(function () {

        Route::get('/pemasukan', [admin\PemasukanController::class, 'index'])
            ->name('pemasukan');

        // Pengeluaran Routes
        Route::get('/pengeluaran', [admin\PengeluaranController::class, 'index'])
            ->name('pengeluaran');

        Route::post('/pengeluaran', [admin\PengeluaranController::class, 'store'])
            ->name('pengeluaran.store');

        Route::put('/pengeluaran/{id}', [admin\PengeluaranController::class, 'update'])
            ->name('pengeluaran.update');

        Route::delete('/pengeluaran/{id}', [admin\PengeluaranController::class, 'destroy'])
            ->name('pengeluaran.destroy');

        // Transfer Routes
        Route::get('/transfer', [admin\TransferController::class, 'index'])
            ->name('transfer');

        Route::post('/transfer', [admin\TransferController::class, 'store'])
            ->name('transfer.store');

        Route::put('/transfer/{id}', [admin\TransferController::class, 'update'])
            ->name('transfer.update');

        Route::delete('/transfer/{id}', [admin\TransferController::class, 'destroy'])
            ->name('transfer.destroy');
    });

Route::middleware('auth')->prefix('simpanan')->name('simpanan.')->group(function () {
    // Setoran Tunai Routes
    Route::get('/setoran', [admin\SetoranTunaiController::class, 'index'])
        ->name('setoran');

    Route::post('/setoran', [admin\SetoranTunaiController::class, 'store'])
        ->name('setoran.store');

    Route::put('/setoran/{id}', [admin\SetoranTunaiController::class, 'update'])
        ->name('setoran.update');

    Route::delete('/setoran/{id}', [admin\SetoranTunaiController::class, 'destroy'])
        ->name('setoran.destroy');

    // Penarikan Tunai Routes
    Route::get('/penarikan', [admin\PenarikanTunaiController::class, 'index'])
        ->name('penarikan');

    Route::post('/penarikan', [admin\PenarikanTunaiController::class, 'store'])
        ->name('penarikan.store');

    Route::put('/penarikan/{id}', [admin\PenarikanTunaiController::class, 'update'])
        ->name('penarikan.update');

    Route::delete('/penarikan/{id}', [admin\PenarikanTunaiController::class, 'destroy'])
        ->name('penarikan.destroy');
});

Route::middleware('auth')->prefix('pinjaman')->name('pinjaman.')->group(function () {

    // ========== PENGAJUAN ROUTES ==========
    Route::get('/pengajuan', [admin\PengajuanController::class, 'index'])
        ->name('pengajuan');

    Route::post('/pengajuan/aksi', [admin\PengajuanController::class, 'aksi'])
        ->name('pengajuan.aksi');

    Route::get('/pengajuan/cetak', [admin\PengajuanController::class, 'cetak'])
        ->name('pengajuan.cetak');

    Route::get('/pengajuan/cetak-laporan', [admin\PengajuanController::class, 'cetakLaporan'])
        ->name('pengajuan.cetak-laporan');

    Route::get('/pengajuan/export/excel', [admin\PengajuanController::class, 'exportExcel'])
        ->name('pengajuan.export.excel');

    Route::get('/pengajuan/export/pdf', [admin\PengajuanController::class, 'exportPDF'])
        ->name('pengajuan.export.pdf');

    // ========== PINJAMAN ROUTES ==========
    // Index
    Route::get('/pinjaman', [admin\PinjamanController::class, 'index'])
        ->name('pinjaman');

    // Routes spesifik HARUS SEBELUM {id}
    Route::post('/pinjaman/store', [admin\PinjamanController::class, 'store'])
        ->name('pinjaman.store');

    Route::get('/pinjaman/cetak-laporan', [admin\PinjamanController::class, 'cetakLaporan'])
        ->name('pinjaman.cetak-laporan');

    Route::get('/pinjaman/export/excel', [admin\PinjamanController::class, 'exportExcel'])
        ->name('pinjaman.export.excel');

    Route::get('/pinjaman/export/pdf', [admin\PinjamanController::class, 'exportPDF'])
        ->name('pinjaman.export.pdf');

    Route::get('/pinjaman/cetak/{id}', [admin\PinjamanController::class, 'cetak'])
        ->name('pinjaman.cetak');

    Route::post('/pinjaman/{id}/validasi-lunas', [admin\PinjamanController::class, 'validasiLunas'])
        ->name('pinjaman.validasi-lunas');

    // Routes dengan {id} HARUS PALING BAWAH
    Route::get('/pinjaman/{id}', [admin\PinjamanController::class, 'show'])
        ->name('pinjaman.detail');

    Route::put('/pinjaman/{id}', [admin\PinjamanController::class, 'update'])
        ->name('pinjaman.update');

    Route::delete('/pinjaman/{id}', [admin\PinjamanController::class, 'destroy'])
        ->name('pinjaman.destroy');

    // ========== BAYAR ANGSURAN ROUTES ==========
    Route::get('/bayar', [admin\BayarAngsuranController::class, 'index'])
        ->name('bayar');

    Route::post('/bayar/proses', [admin\BayarAngsuranController::class, 'proses'])
        ->name('bayar.proses');

    Route::get('/bayar/cetak-bukti/{id}', [admin\BayarAngsuranController::class, 'cetakBukti'])
        ->name('bayar.cetak-bukti');

    Route::get('/bayar/detail/{id}', [admin\BayarAngsuranController::class, 'show'])
        ->name('bayar.detail');

    // ========== PINJAMAN LUNAS ROUTES ==========
    Route::get('/lunas', [admin\PinjamanLunasController::class, 'index'])
        ->name('lunas');

    // Routes spesifik HARUS SEBELUM {id}
    Route::get('/lunas/cetak-detail/{id}', [admin\PinjamanLunasController::class, 'cetakDetail'])
        ->name('lunas.cetak-detail');

    Route::get('/lunas/cetak-laporan', [admin\PinjamanLunasController::class, 'cetakLaporan'])
        ->name('lunas.cetak-laporan');

    Route::get('/lunas/export/excel', [admin\PinjamanLunasController::class, 'exportExcel'])
        ->name('lunas.export.excel');

    Route::get('/lunas/export/pdf', [admin\PinjamanLunasController::class, 'exportPDF'])
        ->name('lunas.export.pdf');

    // Route {id} HARUS PALING BAWAH
    Route::get('/lunas/{id}', [admin\PinjamanLunasController::class, 'show'])
        ->name('lunas.detail');
});

Route::middleware('auth')->prefix('laporan')->name('laporan.')->group(function () {
    // Route Data Anggota
    Route::get('/anggota', [admin\AnggotaController::class, 'index'])
        ->name('anggota');

    Route::get('/anggota/cetak', [admin\AnggotaController::class, 'cetakLaporan'])
        ->name('anggota.cetak');

    Route::get('/anggota/export/excel', [admin\AnggotaController::class, 'exportExcel'])
        ->name('anggota.export.excel');

    Route::get('/anggota/export/pdf', [admin\AnggotaController::class, 'exportPDF'])
        ->name('anggota.export.pdf');

    // Route Kas Anggota
    Route::get('/kas-anggota', [admin\KasAnggotaController::class, 'index'])
        ->name('kas-anggota');

    Route::get('/kas-anggota/cetak', [admin\KasAnggotaController::class, 'cetakLaporan'])
        ->name('kas-anggota.cetak');

    Route::get('/kas-anggota/export/excel', [admin\KasAnggotaController::class, 'exportExcel'])
        ->name('kas-anggota.export.excel');

    // Route Jatuh Tempo
    Route::get('/jatuh-tempo', [admin\JatuhTempoController::class, 'index'])
        ->name('jatuh-tempo');

    Route::get('/jatuh-tempo/cetak', [admin\JatuhTempoController::class, 'cetakLaporan'])
        ->name('jatuh-tempo.cetak');

    Route::get('/jatuh-tempo/export/excel', [admin\JatuhTempoController::class, 'exportExcel'])
        ->name('jatuh-tempo.export.excel');

    Route::post('/jatuh-tempo/kirim-notifikasi', [admin\JatuhTempoController::class, 'kirimNotifikasi'])
        ->name('jatuh-tempo.kirim-notifikasi');

    // Route Kredit Macet
    Route::get('/kredit-macet', [admin\KreditMacetController::class, 'index'])
        ->name('kredit-macet');

    Route::get('/kredit-macet/cetak', [admin\KreditMacetController::class, 'cetakLaporan'])
        ->name('kredit-macet.cetak');

    Route::get('/kredit-macet/export/excel', [admin\KreditMacetController::class, 'exportExcel'])
        ->name('kredit-macet.export.excel');

    Route::post('/kredit-macet/kirim-pemanggilan', [admin\KreditMacetController::class, 'kirimPemanggilan'])
        ->name('kredit-macet.kirim-pemanggilan');

    Route::post('/kredit-macet/get-data', [admin\KreditMacetController::class, 'getData'])
        ->name('kredit-macet.get-data');

    // Route Transaksi Kas
    Route::get('/transaksi-kas', [admin\TransaksiKasController::class, 'index'])
        ->name('transaksi-kas');

    Route::get('/transaksi-kas/cetak', [admin\TransaksiKasController::class, 'cetakLaporan'])
        ->name('transaksi-kas.cetak');

    Route::get('/transaksi-kas/export/excel', [admin\TransaksiKasController::class, 'exportExcel'])
        ->name('transaksi-kas.export.excel');

    Route::get('/transaksi-kas/export/pdf', [admin\TransaksiKasController::class, 'exportPDF'])
        ->name('transaksi-kas.export.pdf');

    Route::post('/transaksi-kas/get-data', [admin\TransaksiKasController::class, 'getData'])
        ->name('transaksi-kas.get-data');

    // Route Buku Besar
    Route::get('/buku-besar', [admin\BukuBesarController::class, 'index'])
        ->name('buku-besar');

    Route::get('/buku-besar/cetak', [admin\BukuBesarController::class, 'cetakLaporan'])
        ->name('buku-besar.cetak');

    Route::get('/buku-besar/export/excel', [admin\BukuBesarController::class, 'exportExcel'])
        ->name('buku-besar.export.excel');

    Route::post('/buku-besar/get-data', [admin\BukuBesarController::class, 'getData'])
        ->name('buku-besar.get-data');

    // Route Neraca Saldo
    Route::get('/neraca-saldo', [admin\NeracaSaldoController::class, 'index'])
        ->name('neraca-saldo');

    Route::get('/neraca-saldo/cetak', [admin\NeracaSaldoController::class, 'cetakLaporan'])
        ->name('neraca-saldo.cetak');

    Route::post('/neraca-saldo/get-data', [admin\NeracaSaldoController::class, 'getData'])
        ->name('neraca-saldo.get-data');

    // Route Kas Simpanan
    Route::get('/kas-simpanan', [admin\KasSimpananController::class, 'index'])
        ->name('kas-simpanan');

    Route::get('/kas-simpanan/cetak', [admin\KasSimpananController::class, 'cetakLaporan'])
        ->name('kas-simpanan.cetak');

    // Route Kas Pinjaman
    Route::get('/kas-pinjaman', [admin\KasPinjamanController::class, 'index'])
        ->name('kas-pinjaman');

    Route::get('/kas-pinjaman/cetak', [admin\KasPinjamanController::class, 'cetakLaporan'])
        ->name('kas-pinjaman.cetak');

    // Route Saldo Kas
    Route::get('/saldo-kas', [admin\SaldoKasController::class, 'index'])
        ->name('saldo-kas');

    Route::get('/saldo-kas/cetak', [admin\SaldoKasController::class, 'cetakLaporan'])
        ->name('saldo-kas.cetak');

    // Route Laba Rugi
    Route::get('/laba-rugi', [admin\LabaRugiController::class, 'index'])
        ->name('laba-rugi');

    Route::get('/laba-rugi/cetak', [admin\LabaRugiController::class, 'cetakLaporan'])
        ->name('laba-rugi.cetak');

    // Route SHU
    Route::get('/shu', [admin\SHUController::class, 'index'])
        ->name('shu');

    Route::get('/shu/cetak', [admin\SHUController::class, 'cetakLaporan'])
        ->name('shu.cetak');
});

Route::middleware('auth')->prefix('master')->name('master.')->group(function () {
    // Route Jenis Simpanan
    Route::get('/jenis-simpanan', [admin\JenisSimpananController::class, 'index'])
        ->name('jenis-simpanan');
    Route::post('/jenis-simpanan', [admin\JenisSimpananController::class, 'store'])
        ->name('jenis-simpanan.store');
    Route::put('/jenis-simpanan/{id}', [admin\JenisSimpananController::class, 'update'])
        ->name('jenis-simpanan.update');
    Route::delete('/jenis-simpanan/{id}', [admin\JenisSimpananController::class, 'destroy'])
        ->name('jenis-simpanan.destroy');
    Route::get('/jenis-simpanan/export', [admin\JenisSimpananController::class, 'export'])
        ->name('jenis-simpanan.export');
    Route::get('/jenis-simpanan/cetak', [admin\JenisSimpananController::class, 'cetak'])
        ->name('jenis-simpanan.cetak');

    // Route Jenis Akun
    Route::get('/jenis-akun', [admin\JenisAkunController::class, 'index'])
        ->name('jenis-akun');
    Route::post('/jenis-akun', [admin\JenisAkunController::class, 'store'])
        ->name('jenis-akun.store');
    Route::put('/jenis-akun/{id}', [admin\JenisAkunController::class, 'update'])
        ->name('jenis-akun.update');
    Route::delete('/jenis-akun/{id}', [admin\JenisAkunController::class, 'destroy'])
        ->name('jenis-akun.destroy');
    Route::get('/jenis-akun/export', [admin\JenisAkunController::class, 'export'])
        ->name('jenis-akun.export');
    Route::get('/jenis-akun/cetak', [admin\JenisAkunController::class, 'cetak'])
        ->name('jenis-akun.cetak');

    // Route Data Kas
    Route::get('/data-kas', [admin\DataKasController::class, 'index'])
        ->name('data-kas');
    Route::post('/data-kas', [admin\DataKasController::class, 'store'])
        ->name('data-kas.store');
    Route::put('/data-kas/{id}', [admin\DataKasController::class, 'update'])
        ->name('data-kas.update');
    Route::delete('/data-kas/{id}', [admin\DataKasController::class, 'destroy'])
        ->name('data-kas.destroy');
    Route::get('/data-kas/export', [admin\DataKasController::class, 'export'])
        ->name('data-kas.export');
    Route::get('/data-kas/cetak', [admin\DataKasController::class, 'cetak'])
        ->name('data-kas.cetak');

    // Route Lama Angsuran
    Route::get('/lama-angsuran', [admin\LamaAngsuranController::class, 'index'])
        ->name('lama-angsuran');
    Route::post('/lama-angsuran', [admin\LamaAngsuranController::class, 'store'])
        ->name('lama-angsuran.store');
    Route::put('/lama-angsuran/{id}', [admin\LamaAngsuranController::class, 'update'])
        ->name('lama-angsuran.update');
    Route::delete('/lama-angsuran/{id}', [admin\LamaAngsuranController::class, 'destroy'])
        ->name('lama-angsuran.destroy');
    Route::get('/lama-angsuran/export', [admin\LamaAngsuranController::class, 'export'])
        ->name('lama-angsuran.export');
    Route::get('/lama-angsuran/cetak', [admin\LamaAngsuranController::class, 'cetak'])
        ->name('lama-angsuran.cetak');

    // Route Data Barang
    Route::get('/data-barang', [admin\DataBarangController::class, 'index'])
        ->name('data-barang');
    Route::post('/data-barang', [admin\DataBarangController::class, 'store'])
        ->name('data-barang.store');
    Route::put('/data-barang/{id}', [admin\DataBarangController::class, 'update'])
        ->name('data-barang.update');
    Route::delete('/data-barang/{id}', [admin\DataBarangController::class, 'destroy'])
        ->name('data-barang.destroy');
    Route::get('/data-barang/export', [admin\DataBarangController::class, 'export'])
        ->name('data-barang.export');
    Route::get('/data-barang/cetak', [admin\DataBarangController::class, 'cetak'])
        ->name('data-barang.cetak');

    // Route Data Anggota 
    Route::get('/data-anggota/export', [admin\DataAnggotaController::class, 'export'])
        ->name('data-anggota.export');

    Route::get('/data-anggota/import', [admin\DataAnggotaController::class, 'showImport'])
        ->name('data-anggota.import');

    Route::post('/data-anggota/import', [admin\DataAnggotaController::class, 'processImport'])
        ->name('data-anggota.import.process');

    Route::get('/data-anggota/cetak', [admin\DataAnggotaController::class, 'cetak'])
        ->name('data-anggota.cetak');

    Route::get('/data-anggota', [admin\DataAnggotaController::class, 'index'])
        ->name('data-anggota');

    Route::post('/data-anggota', [admin\DataAnggotaController::class, 'store'])
        ->name('data-anggota.store');

    Route::put('/data-anggota/{id}', [admin\DataAnggotaController::class, 'update'])
        ->name('data-anggota.update');

    Route::delete('/data-anggota/{id}', [admin\DataAnggotaController::class, 'destroy'])
        ->name('data-anggota.destroy');


    // Route Data Pengguna - Route SPESIFIK harus di ATAS route umum
    Route::get('/data-pengguna/export', [admin\DataPenggunaController::class, 'export'])
        ->name('data-pengguna.export');

    Route::get('/data-pengguna/cetak', [admin\DataPenggunaController::class, 'cetak'])
        ->name('data-pengguna.cetak');

    Route::get('/data-pengguna', [admin\DataPenggunaController::class, 'index'])
        ->name('data-pengguna');

    Route::post('/data-pengguna', [admin\DataPenggunaController::class, 'store'])
        ->name('data-pengguna.store');

    Route::put('/data-pengguna/{id}', [admin\DataPenggunaController::class, 'update'])
        ->name('data-pengguna.update');

    Route::delete('/data-pengguna/{id}', [admin\DataPenggunaController::class, 'destroy'])
        ->name('data-pengguna.destroy');
});

Route::middleware('auth')->prefix('setting')->name('setting.')->group(function () {
    Route::get('/identitas', [admin\IdentitasKoperasiController::class, 'index'])
        ->name('identitas');

    Route::put('/identitas', [admin\IdentitasKoperasiController::class, 'update'])
        ->name('identitas.update');

    Route::get('/identitas', [admin\IdentitasKoperasiController::class, 'index'])
        ->name('identitas');

    Route::put('/identitas', [admin\IdentitasKoperasiController::class, 'update'])
        ->name('identitas.update');

    Route::get('/suku-bunga', [admin\SukuBungaController::class, 'index'])
        ->name('suku-bunga');

    Route::put('/suku-bunga', [admin\SukuBungaController::class, 'update'])
        ->name('suku-bunga.update');
});

require __DIR__ . '/auth.php';