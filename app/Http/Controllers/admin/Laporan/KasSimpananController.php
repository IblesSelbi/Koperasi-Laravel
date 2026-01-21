<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasSimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data kas simpanan
        $kasSimpanan = collect([
            (object) [
                'no' => 1,
                'jenis_akun' => 'Simpanan Sukarela',
                'simpanan' => 10000,
                'penarikan' => 0,
                'jumlah' => 10000,
            ],
            (object) [
                'no' => 2,
                'jenis_akun' => 'Simpanan Pokok',
                'simpanan' => 100000,
                'penarikan' => 0,
                'jumlah' => 100000,
            ],
            (object) [
                'no' => 3,
                'jenis_akun' => 'Simpanan Wajib',
                'simpanan' => 0,
                'penarikan' => 0,
                'jumlah' => 0,
            ],
            (object) [
                'no' => 4,
                'jenis_akun' => 'asdf',
                'simpanan' => 0,
                'penarikan' => 0,
                'jumlah' => 0,
            ],
        ]);

        // Calculate totals
        $totalSimpanan = $kasSimpanan->sum('simpanan');
        $totalPenarikan = $kasSimpanan->sum('penarikan');
        $totalJumlah = $kasSimpanan->sum('jumlah');

        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Default date range
        $tglDari = $request->get('tgl_dari', '2025-01-01');
        $tglSamp = $request->get('tgl_samp', '2025-12-31');

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
        $tglDari = $request->get('tgl_dari', '');
        $tglSamp = $request->get('tgl_samp', '');

        // TODO: Generate print view with filtered data

        return response('Cetak laporan kas simpanan');
    }
}