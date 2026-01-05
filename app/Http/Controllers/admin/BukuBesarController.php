<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data untuk Kas Tunai
        $kasTunai = collect([
            (object)[
                'id' => 1,
                'tanggal' => '2025-12-07',
                'jenis_transaksi' => 'Pembayaran Angsuran',
                'keterangan' => 'cicilan 7',
                'debet' => 563300,
                'kredit' => 0,
                'saldo' => 563300,
            ],
            (object)[
                'id' => 2,
                'tanggal' => '2025-12-07',
                'jenis_transaksi' => 'Pinjaman Anggota',
                'keterangan' => 'sewa HP',
                'debet' => 0,
                'kredit' => 2600000,
                'saldo' => -2036700,
            ],
        ]);

        // Dummy data untuk Kas Besar (kosong)
        $kasBesar = collect([]);

        // Dummy data untuk Transfer (kosong)
        $transfer = collect([]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Calculate summary
        $totalAkun = 0;
        if ($kasTunai->count() > 0) $totalAkun++;
        if ($kasBesar->count() > 0) $totalAkun++;
        if ($transfer->count() > 0) $totalAkun++;

        // Get last saldo from each account
        $saldoKasTunai = $kasTunai->last()->saldo ?? 0;
        $saldoKasBesar = $kasBesar->last()->saldo ?? 0;
        $saldoTransfer = $transfer->last()->saldo ?? 0;

        $totalSaldo = $saldoKasTunai + $saldoKasBesar + $saldoTransfer;

        return view('admin.Laporan.bukuBesar.BukuBesar', compact(
            'kasTunai',
            'kasBesar',
            'transfer',
            'notifications',
            'totalAkun',
            'totalSaldo'
        ));
    }

    /**
     * Print laporan buku besar with filters
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->get('periode', '');

        // TODO: Get filtered data and generate print view
        // $kasTunai = TransaksiBukuBesar::where('akun', 'Kas Tunai')
        //     ->whereMonth('tanggal', date('m', strtotime($periode)))
        //     ->whereYear('tanggal', date('Y', strtotime($periode)))
        //     ->orderBy('tanggal', 'asc')
        //     ->get();
        
        // return view('admin.laporan.cetak_buku_besar', compact('kasTunai', 'kasBesar', 'transfer', 'periode'));
        
        return response('Cetak laporan buku besar periode: ' . $periode);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export using Laravel Excel
        // $periode = $request->get('periode');
        // $summary = $request->get('summary', true);
        // $separate = $request->get('separate', true);
        // $chart = $request->get('chart', false);
        // return Excel::download(new BukuBesarExport($periode, $summary, $separate, $chart), 'buku-besar.xlsx');
        
        return response('Export Excel Buku Besar');
    }

    /**
     * Get data for filtering
     */
    public function getData(Request $request)
    {
        // TODO: Implement filtering logic
        // $periode = $request->get('periode');
        
        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }
}