<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransaksiKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data transaksi kas
        $transaksiKas = collect([
            (object)[
                'id' => 1,
                'kode_transaksi' => 'TKM001',
                'tanggal_transaksi' => '2025-01-15',
                'akun_transaksi' => 'Setoran Simpanan Pokok',
                'keterangan' => 'Anggota: Ahmad Hidayat',
                'dari_kas' => 'Kas Tunai',
                'untuk_kas' => null,
                'debet' => 5000000,
                'kredit' => 0,
                'saldo' => 5000000,
            ],
            (object)[
                'id' => 2,
                'kode_transaksi' => 'TKK002',
                'tanggal_transaksi' => '2025-01-16',
                'akun_transaksi' => 'Pembayaran Angsuran Pinjaman',
                'keterangan' => 'Anggota: Dewi Lestari - TPJ00012',
                'dari_kas' => null,
                'untuk_kas' => 'Kas Besar',
                'debet' => 0,
                'kredit' => 2500000,
                'saldo' => 2500000,
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Calculate saldo sebelumnya (for display purposes)
        $saldoSebelumnya = 0;

        // Calculate totals
        $totalDebet = $transaksiKas->sum('debet');
        $totalKredit = $transaksiKas->sum('kredit');
        $saldoAkhir = $totalDebet - $totalKredit;
        $totalData = $transaksiKas->count();

        return view('admin.Laporan.transaksiKas.TransaksiKas', compact(
            'transaksiKas',
            'notifications',
            'saldoSebelumnya',
            'totalDebet',
            'totalKredit',
            'saldoAkhir',
            'totalData'
        ));
    }

    /**
     * Print laporan transaksi kas with filters
     */
    public function cetakLaporan(Request $request)
    {
        $startDate = $request->get('start_date', '');
        $endDate = $request->get('end_date', '');
        $format = $request->get('format', 'lengkap');

        // TODO: Get filtered data and generate print view
        // $transaksiKas = TransaksiKas::query();
        
        // if ($startDate && $endDate) {
        //     $transaksiKas->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
        // }
        
        // $transaksiKas = $transaksiKas->orderBy('tanggal_transaksi', 'asc')->get();
        
        // return view('admin.laporan.cetak_transaksi_kas', compact('transaksiKas', 'startDate', 'endDate', 'format'));
        
        return response('Cetak laporan transaksi kas dari ' . $startDate . ' sampai ' . $endDate . ' format: ' . $format);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export using Laravel Excel
        // $startDate = $request->get('start_date');
        // $endDate = $request->get('end_date');
        // $format = $request->get('format', 'xlsx');
        // $template = $request->get('template', 'standard');
        // return Excel::download(new TransaksiKasExport($startDate, $endDate, $template), 'transaksi-kas.' . $format);
        
        return response('Export Excel Transaksi Kas');
    }

    /**
     * Export to PDF
     */
    public function exportPDF(Request $request)
    {
        // TODO: Implement PDF export using DomPDF or similar
        // $startDate = $request->get('start_date');
        // $endDate = $request->get('end_date');
        // $orientasi = $request->get('orientasi', 'portrait');
        // $paper = $request->get('paper', 'A4');
        
        // $transaksiKas = TransaksiKas::whereBetween('tanggal_transaksi', [$startDate, $endDate])->get();
        // $pdf = PDF::loadView('admin.laporan.export_pdf_transaksi_kas', compact('transaksiKas'))
        //           ->setPaper($paper, $orientasi);
        // return $pdf->download('transaksi-kas.pdf');
        
        return response('Export PDF Transaksi Kas');
    }

    /**
     * Get data for filtering
     */
    public function getData(Request $request)
    {
        // TODO: Implement filtering logic
        // $startDate = $request->get('start_date');
        // $endDate = $request->get('end_date');
        
        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }
}