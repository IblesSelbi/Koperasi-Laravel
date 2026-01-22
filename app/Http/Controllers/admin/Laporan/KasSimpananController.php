<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\Simpanan\PenarikanTunai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasSimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default date range
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get all jenis simpanan yang tampil = Y
        $jenisSimpananList = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        $kasSimpanan = collect();
        $totalSimpanan = 0;
        $totalPenarikan = 0;
        $totalJumlah = 0;
        $no = 1;

        foreach ($jenisSimpananList as $jenis) {
            // Get total simpanan (setoran) untuk jenis simpanan ini dalam periode
            $simpanan = SetoranTunai::where('jenis_simpanan_id', $jenis->id)
                ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
                ->sum('jumlah');

            // Get total penarikan untuk jenis simpanan ini dalam periode
            $penarikan = PenarikanTunai::where('jenis_simpanan_id', $jenis->id)
                ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
                ->sum('jumlah');

            // Calculate jumlah (simpanan - penarikan)
            $jumlah = $simpanan - $penarikan;

            // Add to collection
            $kasSimpanan->push((object) [
                'no' => $no++,
                'jenis_akun' => $jenis->jenis_simpanan,
                'simpanan' => $simpanan,
                'penarikan' => $penarikan,
                'jumlah' => $jumlah,
            ]);

            // Calculate totals
            $totalSimpanan += $simpanan;
            $totalPenarikan += $penarikan;
            $totalJumlah += $jumlah;
        }

        // Dummy notifications (you can modify this based on your actual notification logic)
        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.KasSimpanan.KasSimpanan', compact(
            'kasSimpanan',
            'notifications',
            'totalSimpanan',
            'totalPenarikan',
            'totalJumlah',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Print laporan kas simpanan
     */
    public function cetakLaporan(Request $request)
    {
        // Get date range from request
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Get all jenis simpanan yang tampil = Y
        $jenisSimpananList = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        $kasSimpanan = collect();
        $totalSimpanan = 0;
        $totalPenarikan = 0;
        $totalJumlah = 0;
        $no = 1;

        foreach ($jenisSimpananList as $jenis) {
            // Get total simpanan (setoran) untuk jenis simpanan ini dalam periode
            $simpanan = SetoranTunai::where('jenis_simpanan_id', $jenis->id)
                ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
                ->sum('jumlah');

            // Get total penarikan untuk jenis simpanan ini dalam periode
            $penarikan = PenarikanTunai::where('jenis_simpanan_id', $jenis->id)
                ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
                ->sum('jumlah');

            // Calculate jumlah (simpanan - penarikan)
            $jumlah = $simpanan - $penarikan;

            // Add to collection
            $kasSimpanan->push((object) [
                'no' => $no++,
                'jenis_akun' => $jenis->jenis_simpanan,
                'simpanan' => $simpanan,
                'penarikan' => $penarikan,
                'jumlah' => $jumlah,
            ]);

            // Calculate totals
            $totalSimpanan += $simpanan;
            $totalPenarikan += $penarikan;
            $totalJumlah += $jumlah;
        }

        // Return view for printing
        return view('admin.Laporan.KasSimpanan.CetakKasSimpanan', compact(
            'kasSimpanan',
            'totalSimpanan',
            'totalPenarikan',
            'totalJumlah',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Get summary statistics (optional - for dashboard widgets)
     */
    public function getSummary(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $totalSimpanan = SetoranTunai::whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $totalPenarikan = PenarikanTunai::whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $totalJumlah = $totalSimpanan - $totalPenarikan;

        return response()->json([
            'success' => true,
            'data' => [
                'total_simpanan' => $totalSimpanan,
                'total_penarikan' => $totalPenarikan,
                'total_jumlah' => $totalJumlah,
            ]
        ]);
    }
}