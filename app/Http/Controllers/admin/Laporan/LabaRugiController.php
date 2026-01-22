<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\DataMaster\JenisAkun;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LabaRugiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default date range: tahun ini
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // ==================== ESTIMASI DATA PINJAMAN ====================

        // 1. Jumlah Pinjaman (Total pokok pinjaman dalam periode)
        $jumlahPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        // 2. Pendapatan Biaya Administrasi
        $pendapatanAdmin = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('biaya_admin');

        // 3. Pendapatan Biaya Bunga (Total bunga dari semua angsuran)
        $pendapatanBunga = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->selectRaw('SUM(biaya_bunga * (SELECT lama_angsuran FROM lama_angsuran WHERE id = pinjaman.lama_angsuran_id)) as total_bunga')
            ->value('total_bunga') ?? 0;

        // 4. Pendapatan Denda
        $pendapatanDenda = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('denda');

        $estimasiPinjaman = collect([
            (object) [
                'no' => 1,
                'keterangan' => 'Jumlah Pinjaman',
                'jumlah' => $jumlahPinjaman,
            ],
            (object) [
                'no' => 2,
                'keterangan' => 'Pendapatan Biaya Administrasi',
                'jumlah' => $pendapatanAdmin,
            ],
            (object) [
                'no' => 3,
                'keterangan' => 'Pendapatan Biaya Bunga',
                'jumlah' => $pendapatanBunga,
            ],
            (object) [
                'no' => 4,
                'keterangan' => 'Pendapatan Denda',
                'jumlah' => $pendapatanDenda,
            ],
        ]);

        // Total tagihan = Pokok + Admin + Bunga + Denda
        $jumlahTagihan = $jumlahPinjaman + $pendapatanAdmin + $pendapatanBunga + $pendapatanDenda;

        // Estimasi pendapatan = Admin + Bunga + Denda (tanpa pokok)
        $estimasiPendapatanPinjaman = $pendapatanAdmin + $pendapatanBunga + $pendapatanDenda;

        // Sudah dibayar (realisasi)
        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        // Pendapatan Pinjaman yang sudah terealisasi = Sudah dibayar - Pokok pinjaman
        $pendapatanPinjamanRealisasi = $sudahDibayar - $jumlahPinjaman;

        // ==================== PENDAPATAN ====================

        $pendapatanList = collect();
        $no = 1;

        // 1. Pendapatan Pinjaman (realisasi)
        $pendapatanList->push((object) [
            'no' => $no++,
            'keterangan' => 'Pendapatan Pinjaman',
            'jumlah' => $pendapatanPinjamanRealisasi,
        ]);

        // 2. Ambil SEMUA jenis akun PENDAPATAN (meskipun tidak ada transaksi)
        $dataPendapatan = JenisAkun::leftJoin('pemasukan', function($join) use ($tglDari, $tglSamp) {
                $join->on('jenis_akun.id', '=', 'pemasukan.dari_akun_id')
                     ->whereBetween('pemasukan.tanggal_transaksi', [$tglDari, $tglSamp])
                     ->whereNull('pemasukan.deleted_at');
            })
            ->where('jenis_akun.laba_rugi', 'PENDAPATAN')
            ->where('jenis_akun.aktif', 'Y')
            ->select(
                'jenis_akun.id',
                'jenis_akun.jns_transaksi',
                DB::raw('COALESCE(SUM(pemasukan.jumlah), 0) as total')
            )
            ->groupBy('jenis_akun.id', 'jenis_akun.jns_transaksi')
            ->orderBy('jenis_akun.jns_transaksi')
            ->get();

        foreach ($dataPendapatan as $item) {
            $pendapatanList->push((object) [
                'no' => $no++,
                'keterangan' => $item->jns_transaksi,
                'jumlah' => $item->total,
            ]);
        }

        $jumlahPendapatan = $pendapatanList->sum('jumlah');

        // ==================== BIAYA-BIAYA ====================

        $biayaList = collect();
        $noBiaya = 1;

        // Ambil SEMUA jenis akun BIAYA (meskipun tidak ada transaksi)
        $dataBiaya = JenisAkun::leftJoin('pengeluaran', function($join) use ($tglDari, $tglSamp) {
                $join->on('jenis_akun.id', '=', 'pengeluaran.untuk_akun_id')
                     ->whereBetween('pengeluaran.tanggal_transaksi', [$tglDari, $tglSamp])
                     ->whereNull('pengeluaran.deleted_at');
            })
            ->where('jenis_akun.laba_rugi', 'BIAYA')
            ->where('jenis_akun.aktif', 'Y')
            ->select(
                'jenis_akun.id',
                'jenis_akun.jns_transaksi',
                DB::raw('COALESCE(SUM(pengeluaran.jumlah), 0) as total')
            )
            ->groupBy('jenis_akun.id', 'jenis_akun.jns_transaksi')
            ->orderBy('jenis_akun.jns_transaksi')
            ->get();

        foreach ($dataBiaya as $item) {
            $biayaList->push((object) [
                'no' => $noBiaya++,
                'keterangan' => $item->jns_transaksi,
                'jumlah' => $item->total,
            ]);
        }

        $jumlahBiaya = $biayaList->sum('jumlah');

        // ==================== LABA RUGI ====================
        $labaRugi = $jumlahPendapatan - $jumlahBiaya;

        // Notifications
        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.LabaRugi.LabaRugi', compact(
            'estimasiPinjaman',
            'pendapatanList',
            'biayaList',
            'jumlahTagihan',
            'estimasiPendapatanPinjaman',
            'jumlahPendapatan',
            'jumlahBiaya',
            'labaRugi',
            'notifications',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Print laporan laba rugi
     */
    public function cetakLaporan(Request $request)
    {
        // Get date range from request
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // ==================== ESTIMASI DATA PINJAMAN ====================

        $jumlahPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        $pendapatanAdmin = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('biaya_admin');

        $pendapatanBunga = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->selectRaw('SUM(biaya_bunga * (SELECT lama_angsuran FROM lama_angsuran WHERE id = pinjaman.lama_angsuran_id)) as total_bunga')
            ->value('total_bunga') ?? 0;

        $pendapatanDenda = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('denda');

        $estimasiPinjaman = collect([
            (object) [
                'no' => 1,
                'keterangan' => 'Jumlah Pinjaman',
                'jumlah' => $jumlahPinjaman,
            ],
            (object) [
                'no' => 2,
                'keterangan' => 'Pendapatan Biaya Administrasi',
                'jumlah' => $pendapatanAdmin,
            ],
            (object) [
                'no' => 3,
                'keterangan' => 'Pendapatan Biaya Bunga',
                'jumlah' => $pendapatanBunga,
            ],
            (object) [
                'no' => 4,
                'keterangan' => 'Pendapatan Denda',
                'jumlah' => $pendapatanDenda,
            ],
        ]);

        $jumlahTagihan = $jumlahPinjaman + $pendapatanAdmin + $pendapatanBunga + $pendapatanDenda;
        $estimasiPendapatanPinjaman = $pendapatanAdmin + $pendapatanBunga + $pendapatanDenda;

        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        $pendapatanPinjamanRealisasi = $sudahDibayar - $jumlahPinjaman;

        // ==================== PENDAPATAN ====================

        $pendapatanList = collect();
        $no = 1;

        $pendapatanList->push((object) [
            'no' => $no++,
            'keterangan' => 'Pendapatan Pinjaman',
            'jumlah' => $pendapatanPinjamanRealisasi,
        ]);

        // Ambil SEMUA jenis akun PENDAPATAN (meskipun tidak ada transaksi)
        $dataPendapatan = JenisAkun::leftJoin('pemasukan', function($join) use ($tglDari, $tglSamp) {
                $join->on('jenis_akun.id', '=', 'pemasukan.dari_akun_id')
                     ->whereBetween('pemasukan.tanggal_transaksi', [$tglDari, $tglSamp])
                     ->whereNull('pemasukan.deleted_at');
            })
            ->where('jenis_akun.laba_rugi', 'PENDAPATAN')
            ->where('jenis_akun.aktif', 'Y')
            ->select(
                'jenis_akun.id',
                'jenis_akun.jns_transaksi',
                DB::raw('COALESCE(SUM(pemasukan.jumlah), 0) as total')
            )
            ->groupBy('jenis_akun.id', 'jenis_akun.jns_transaksi')
            ->orderBy('jenis_akun.jns_transaksi')
            ->get();

        foreach ($dataPendapatan as $item) {
            $pendapatanList->push((object) [
                'no' => $no++,
                'keterangan' => $item->jns_transaksi,
                'jumlah' => $item->total,
            ]);
        }

        $jumlahPendapatan = $pendapatanList->sum('jumlah');

        // ==================== BIAYA-BIAYA ====================

        $biayaList = collect();
        $noBiaya = 1;

        // Ambil SEMUA jenis akun BIAYA (meskipun tidak ada transaksi)
        $dataBiaya = JenisAkun::leftJoin('pengeluaran', function($join) use ($tglDari, $tglSamp) {
                $join->on('jenis_akun.id', '=', 'pengeluaran.untuk_akun_id')
                     ->whereBetween('pengeluaran.tanggal_transaksi', [$tglDari, $tglSamp])
                     ->whereNull('pengeluaran.deleted_at');
            })
            ->where('jenis_akun.laba_rugi', 'BIAYA')
            ->where('jenis_akun.aktif', 'Y')
            ->select(
                'jenis_akun.id',
                'jenis_akun.jns_transaksi',
                DB::raw('COALESCE(SUM(pengeluaran.jumlah), 0) as total')
            )
            ->groupBy('jenis_akun.id', 'jenis_akun.jns_transaksi')
            ->orderBy('jenis_akun.jns_transaksi')
            ->get();

        foreach ($dataBiaya as $item) {
            $biayaList->push((object) [
                'no' => $noBiaya++,
                'keterangan' => $item->jns_transaksi,
                'jumlah' => $item->total,
            ]);
        }

        $jumlahBiaya = $biayaList->sum('jumlah');

        $labaRugi = $jumlahPendapatan - $jumlahBiaya;

        // Return view for printing
        return view('admin.Laporan.LabaRugi.CetakLabaRugi', compact(
            'estimasiPinjaman',
            'pendapatanList',
            'biayaList',
            'jumlahTagihan',
            'estimasiPendapatanPinjaman',
            'jumlahPendapatan',
            'jumlahBiaya',
            'labaRugi',
            'tglDari',
            'tglSamp'
        ));
    }
}