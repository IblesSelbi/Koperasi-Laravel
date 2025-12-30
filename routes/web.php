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
    // Pengajuan Routes
    Route::get('/pengajuan', [Admin\PengajuanController::class, 'index'])
        ->name('pengajuan');

    Route::post('/pengajuan/aksi', [Admin\PengajuanController::class, 'aksi'])
        ->name('pengajuan.aksi');

    Route::get('/pengajuan/cetak', [Admin\PengajuanController::class, 'cetak'])
        ->name('pengajuan.cetak');

    Route::get('/pengajuan/cetak-laporan', [Admin\PengajuanController::class, 'cetakLaporan'])
        ->name('pengajuan.cetak');

    Route::get('/pengajuan/export/excel', [Admin\PengajuanController::class, 'exportExcel'])
        ->name('pengajuan.export.excel');

    Route::get('/pengajuan/export/pdf', [Admin\PengajuanController::class, 'exportPDF'])
        ->name('pengajuan.export.pdf');

    // Other Pinjaman Routes (Coming Soon)
    Route::get('/', function () {
        return view('coming-soon');
    })->name('index');

    Route::get('/bayar', function () {
        return view('coming-soon');
    })->name('bayar');

    Route::get('/lunas', function () {
        return view('coming-soon');
    })->name('lunas');
});

Route::middleware('auth')->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/anggota', function () {
        return view('coming-soon');
    })->name('anggota');

    Route::get('/kas-anggota', function () {
        return view('coming-soon');
    })->name('kas-anggota');

    Route::get('/jatuh-tempo', function () {
        return view('coming-soon');
    })->name('jatuh-tempo');

    Route::get('/kredit-macet', function () {
        return view('coming-soon');
    })->name('kredit-macet');

    Route::get('/transaksi-kas', function () {
        return view('coming-soon');
    })->name('transaksi-kas');

    Route::get('/buku-besar', function () {
        return view('coming-soon');
    })->name('buku-besar');

    Route::get('/neraca-saldo', function () {
        return view('coming-soon');
    })->name('neraca-saldo');

    Route::get('/kas-simpanan', function () {
        return view('coming-soon');
    })->name('kas-simpanan');

    Route::get('/kas-pinjaman', function () {
        return view('coming-soon');
    })->name('kas-pinjaman');

    Route::get('/saldo-kas', function () {
        return view('coming-soon');
    })->name('saldo-kas');

    Route::get('/laba-rugi', function () {
        return view('coming-soon');
    })->name('laba-rugi');

    Route::get('/shu', function () {
        return view('coming-soon');
    })->name('shu');
});

Route::middleware('auth')->prefix('master')->name('master.')->group(function () {
    Route::get('/jenis-simpanan', function () {
        return view('coming-soon');
    })->name('jenis-simpanan');

    Route::get('/jenis-akun', function () {
        return view('coming-soon');
    })->name('jenis-akun');

    Route::get('/data-kas', function () {
        return view('coming-soon');
    })->name('data-kas');

    Route::get('/lama-angsuran', function () {
        return view('coming-soon');
    })->name('lama-angsuran');

    Route::get('/data-barang', function () {
        return view('coming-soon');
    })->name('data-barang');

    Route::get('/anggota', function () {
        return view('coming-soon');
    })->name('anggota');

    Route::get('/pengguna', function () {
        return view('coming-soon');
    })->name('pengguna');
});

Route::middleware('auth')->prefix('setting')->name('setting.')->group(function () {
    Route::get('/identitas', function () {
        return view('coming-soon');
    })->name('identitas');

    Route::get('/suku-bunga', function () {
        return view('coming-soon');
    })->name('suku-bunga');
});

require __DIR__ . '/auth.php';