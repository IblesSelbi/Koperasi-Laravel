<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SaldoKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default periode
        $periode = $request->get('periode', '2025-12');
        
        // Dummy data saldo kas
        $saldoKas = collect([
            (object)[
                'no' => 1,
                'nama_kas' => 'Kas Tunai',
                'saldo' => -996600,
            ],
            (object)[
                'no' => 2,
                'nama_kas' => 'Kas Besar',
                'saldo' => 0,
            ],
            (object)[
                'no' => 3,
                'nama_kas' => 'Transfer',
                'saldo' => 0,
            ],
        ]);

        // Calculate totals
        $saldoPeriodeSebelumnya = 0;
        $jumlahSaldo = $saldoKas->sum('saldo');
        $totalSaldo = $saldoPeriodeSebelumnya + $jumlahSaldo;

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Format periode untuk display
        $periodeDisplay = \Carbon\Carbon::parse($periode . '-01')->locale('id')->isoFormat('MMMM YYYY');

        return view('admin.Laporan.SaldoKas.SaldoKas', compact(
            'saldoKas',
            'saldoPeriodeSebelumnya',
            'jumlahSaldo',
            'totalSaldo',
            'notifications',
            'periode',
            'periodeDisplay'
        ));
    }

    /**
     * Print laporan saldo kas
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->get('periode', '');

        // TODO: Generate print view with filtered data
        
        return response('Cetak laporan saldo kas');
    }
}