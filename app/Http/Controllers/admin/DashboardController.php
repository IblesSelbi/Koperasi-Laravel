<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\Simpanan\JenisSimpanan;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\Laporan\KasAnggota;
use App\Models\Admin\Laporan\KasPinjaman;
use App\Models\Admin\Laporan\KasSimpanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Tanggal hari ini dan bulan ini
        $today = Carbon::now();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // === STATISTIK PINJAMAN ===
        $pinjamanBulanIni = Pinjaman::whereBetween('tanggal_pinjam', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->count();

        $totalTagihanPinjaman = Pinjaman::whereNull('deleted_at')
            ->sum('jumlah_angsuran');

        $totalDibayar = DetailBayarAngsuran::whereNull('deleted_at')
            ->sum('total_bayar');

        $sisaTagihan = $totalTagihanPinjaman - $totalDibayar;

        $statsPinjaman = [
            'transaksi_bulan_ini' => $pinjamanBulanIni,
            'jml_tagihan' => $totalTagihanPinjaman,
            'sisa_tagihan' => $sisaTagihan > 0 ? $sisaTagihan : 0,
        ];

        // === STATISTIK SIMPANAN ===
        // Menggunakan tabel setoran_tunai dan penarikan_tunai
        $simpananAnggotaBulanIni = DB::table('setoran_tunai')
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->count();

        $penarikanTunaiBulanIni = DB::table('penarikan_tunai')
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->count();

        $totalSimpanan = DB::table('setoran_tunai')
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $totalPenarikan = DB::table('penarikan_tunai')
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $jumlahSimpanan = $totalSimpanan - $totalPenarikan;

        $statsSimpanan = [
            'simpanan_anggota' => $simpananAnggotaBulanIni,
            'penarikan_tunai' => $penarikanTunaiBulanIni,
            'jumlah_simpanan' => $jumlahSimpanan,
        ];

        // === STATISTIK KAS BULAN INI ===
        // Debet: Pemasukan + Setoran + Angsuran
        $debetBulanIni = DB::table('pemasukan')
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $debetBulanIni += DB::table('setoran_tunai')
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $debetBulanIni += DetailBayarAngsuran::whereBetween('tanggal_bayar', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->sum('total_bayar');

        // Kredit: Pengeluaran + Penarikan + Pinjaman
        $kreditBulanIni = DB::table('pengeluaran')
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $kreditBulanIni += DB::table('penarikan_tunai')
            ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $kreditBulanIni += Pinjaman::whereBetween('tanggal_pinjam', [$startOfMonth, $endOfMonth])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        $jumlahKas = $debetBulanIni - $kreditBulanIni;

        $statsKas = [
            'debet' => $debetBulanIni,
            'kredit' => $kreditBulanIni,
            'jumlah' => $jumlahKas,
        ];

        // === STATISTIK ANGGOTA ===
        $anggotaAktif = DataAnggota::where('aktif', 'Aktif')->count();
        $anggotaTidakAktif = DataAnggota::where('aktif', 'Non Aktif')->count();
        $totalAnggota = DataAnggota::count();

        $statsAnggota = [
            'aktif' => $anggotaAktif,
            'tidak_aktif' => $anggotaTidakAktif,
            'total' => $totalAnggota,
        ];

        // === STATISTIK PEMINJAM ===
        $totalPeminjam = Pinjaman::whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $pinjamanLunas = Pinjaman::where('status_lunas', 'Lunas')
            ->whereNull('deleted_at')
            ->count();

        $pinjamanBelumLunas = Pinjaman::where('status_lunas', 'Belum')
            ->whereNull('deleted_at')
            ->count();

        $statsPeminjam = [
            'total' => $totalPeminjam,
            'lunas' => $pinjamanLunas,
            'belum_lunas' => $pinjamanBelumLunas,
        ];

        // === STATISTIK PENGGUNA ===
        $userAktif = User::count();
        $userNonAktif = 0;
        $totalUser = User::count();

        $statsPengguna = [
            'aktif' => $userAktif,
            'non_aktif' => $userNonAktif,
            'total' => $totalUser,
        ];

        // === NOTIFIKASI JATUH TEMPO ===
        $notifications = BayarAngsuran::where('status_bayar', 'Belum')
            ->where('tanggal_jatuh_tempo', '<=', $today->copy()->addDays(7))
            ->whereNull('deleted_at')
            ->with(['pinjaman.anggota'])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        // === DATA GRAFIK 6 BULAN TERAKHIR ===
        $chartData = $this->getChartData();

        $stats = [
            'pinjaman' => $statsPinjaman,
            'simpanan' => $statsSimpanan,
            'kas' => $statsKas,
            'anggota' => $statsAnggota,
            'peminjam' => $statsPeminjam,
            'pengguna' => $statsPengguna,
        ];

        return view('admin.dashboard', compact('stats', 'notifications', 'chartData'));
    }

    private function getChartData()
    {
        $months = [];
        $simpananData = [];
        $pinjamanData = [];

        // Generate 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // Nama bulan
            $months[] = $date->translatedFormat('M Y');

            // Total simpanan bulan ini
            $totalSimpanan = DB::table('setoran_tunai')
                ->whereBetween('tanggal_transaksi', [$startOfMonth, $endOfMonth])
                ->whereNull('deleted_at')
                ->sum('jumlah');

            $simpananData[] = (float) $totalSimpanan;

            // Total pinjaman bulan ini
            $totalPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$startOfMonth, $endOfMonth])
                ->whereNull('deleted_at')
                ->sum('pokok_pinjaman');

            $pinjamanData[] = (float) $totalPinjaman;
        }

        return [
            'months' => $months,
            'simpanan' => $simpananData,
            'pinjaman' => $pinjamanData,
        ];
    }
}