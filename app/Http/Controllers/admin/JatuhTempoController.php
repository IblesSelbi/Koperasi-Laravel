<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JatuhTempoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        
        // Dummy data jatuh tempo
        $jatuhTempo = collect([
            (object)[
                'id' => 1,
                'kode_pinjam' => 'TPJ00017',
                'id_anggota' => 'har',
                'nama_anggota' => 'hartati',
                'tanggal_pinjam' => '2025-05-16',
                'tanggal_tempo' => '2025-06-16',
                'lama_pinjam' => 1,
                'jumlah_tagihan' => 1575000,
                'dibayar' => 0,
                'sisa_tagihan' => 1575000,
            ],
            (object)[
                'id' => 2,
                'kode_pinjam' => 'TPJ00006',
                'id_anggota' => 'cahyadi001',
                'nama_anggota' => 'Cahyadi',
                'tanggal_pinjam' => '2024-06-24',
                'tanggal_tempo' => '2025-06-24',
                'lama_pinjam' => 12,
                'jumlah_tagihan' => 22017600,
                'dibayar' => 1834800,
                'sisa_tagihan' => 20182800,
            ],
        ]);

        // Calculate totals
        $totalTagihan = $jatuhTempo->sum('jumlah_tagihan');
        $totalDibayar = $jatuhTempo->sum('dibayar');
        $sisaTagihan = $jatuhTempo->sum('sisa_tagihan');

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.laporan.JatuhTempo.JatuhTempo', compact(
            'jatuhTempo', 
            'notifications', 
            'periode',
            'totalTagihan',
            'totalDibayar',
            'sisaTagihan'
        ));
    }

    /**
     * Print laporan jatuh tempo with filters
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        // TODO: Get filtered data and generate print view
        // $jatuhTempo = JatuhTempo::query()
        //     ->whereYear('tanggal_tempo', '=', substr($periode, 0, 4))
        //     ->whereMonth('tanggal_tempo', '=', substr($periode, 5, 2))
        //     ->get();
        
        // return view('admin.laporan.cetak_jatuh_tempo', compact('jatuhTempo', 'periode'));
        
        return response('Cetak laporan jatuh tempo periode: ' . $periode);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $includeSummary = $request->get('summary', 'true') === 'true';
        $includeChart = $request->get('chart', 'true') === 'true';
        
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new JatuhTempoExport($periode, $includeSummary, $includeChart), 'jatuh-tempo.xlsx');
        
        return response('Export Excel Data Jatuh Tempo periode: ' . $periode);
    }

    /**
     * Send notifications to members
     */
    public function kirimNotifikasi(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $template = $request->get('template', 'standard');
        
        // TODO: Implement notification sending (SMS, Email, WhatsApp)
        // $jatuhTempo = JatuhTempo::whereMonth('tanggal_tempo', substr($periode, 5, 2))
        //     ->whereYear('tanggal_tempo', substr($periode, 0, 4))
        //     ->get();
        
        // Send notifications...
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dikirim',
            'data' => [
                'sms' => 2,
                'email' => 2,
                'whatsapp' => 1
            ]
        ]);
    }
}