<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KreditMacetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data kredit macet
        $kreditMacet = collect([
            (object)[
                'id' => 1,
                'kode_pinjam' => 'TPJ00017',
                'id_anggota' => 'har',
                'nama' => 'hartati',
                'tanggal_pinjam' => '2025-05-16',
                'tanggal_tempo' => '2025-06-16',
                'lama_pinjam' => '1 Bulan',
                'jumlah_tagihan' => 1575000,
                'dibayar' => 0,
                'sisa_tagihan' => 1575000,
            ],
            (object)[
                'id' => 2,
                'kode_pinjam' => 'TPJ00006',
                'id_anggota' => 'cahyadi001',
                'nama' => 'Cahyadi',
                'tanggal_pinjam' => '2024-06-24',
                'tanggal_tempo' => '2025-06-24',
                'lama_pinjam' => '12 Bulan',
                'jumlah_tagihan' => 22017600,
                'dibayar' => 1834800,
                'sisa_tagihan' => 20182800,
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Calculate totals
        $totalTagihan = $kreditMacet->sum('jumlah_tagihan');
        $totalDibayar = $kreditMacet->sum('dibayar');
        $sisaTagihan = $kreditMacet->sum('sisa_tagihan');
        $totalData = $kreditMacet->count();

        return view('admin.Laporan.kreditMacet.KreditMacet', compact(
            'kreditMacet',
            'notifications',
            'totalTagihan',
            'totalDibayar',
            'sisaTagihan',
            'totalData'
        ));
    }

    /**
     * Print laporan kredit macet with filters
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->get('periode', '');

        // TODO: Get filtered data and generate print view
        // $kreditMacet = KreditMacet::query();
        
        // if ($periode) {
        //     $kreditMacet->whereMonth('tanggal_tempo', date('m', strtotime($periode)))
        //                 ->whereYear('tanggal_tempo', date('Y', strtotime($periode)));
        // }
        
        // $kreditMacet = $kreditMacet->get();
        
        // return view('admin.laporan.cetak_kredit_macet', compact('kreditMacet', 'periode'));
        
        return response('Cetak laporan kredit macet dengan periode: ' . $periode);
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export using Laravel Excel
        // $periode = $request->get('periode', '');
        // return Excel::download(new KreditMacetExport($periode), 'kredit-macet.xlsx');
        
        return response('Export Excel Kredit Macet');
    }

    /**
     * Kirim surat pemanggilan
     */
    public function kirimPemanggilan(Request $request)
    {
        // TODO: Implement sending notification
        // $periode = $request->get('periode');
        // $metode = $request->get('metode');
        // $catatan = $request->get('catatan');
        
        return response()->json([
            'status' => 'success',
            'message' => 'Surat pemanggilan berhasil dikirim'
        ]);
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