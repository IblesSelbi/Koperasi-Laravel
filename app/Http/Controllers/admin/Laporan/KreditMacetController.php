<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KreditMacetController extends Controller
{
    /**
     * Display a listing of kredit macet
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        
        // Parse periode
        [$year, $month] = explode('-', $periode);
        
        // Query untuk mendapatkan kredit macet
        // Kredit macet = pinjaman yang memiliki angsuran jatuh tempo lewat dari bulan ini
        $query = BayarAngsuran::with([
            'pinjaman.anggota',
            'pinjaman.lamaAngsuran'
        ])
        ->where('status_bayar', 'Belum')
        ->whereYear('tanggal_jatuh_tempo', '<=', $year)
        ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
        ->where('tanggal_jatuh_tempo', '<', Carbon::now());

        $angsuranMacet = $query->orderBy('tanggal_jatuh_tempo', 'asc')->get();

        // Group by pinjaman untuk menghitung total per anggota
        $kreditMacetGrouped = $angsuranMacet->groupBy('pinjaman_id')->map(function ($items) {
            $pinjaman = $items->first()->pinjaman;
            
            // Hitung total tagihan yang macet
            $jumlahTagihan = $items->sum('jumlah_angsuran');
            $totalDenda = $items->sum('denda');
            
            // Hitung total yang sudah dibayar dari pinjaman ini
            $totalDibayar = BayarAngsuran::where('pinjaman_id', $pinjaman->id)
                ->where('status_bayar', 'Lunas')
                ->sum('jumlah_bayar');
            
            // Hitung sisa tagihan
            $sisaTagihan = $pinjaman->jumlah_angsuran - $totalDibayar;
            
            return (object)[
                'id' => $pinjaman->id,
                'kode_pinjam' => $pinjaman->kode_pinjaman,
                'id_anggota' => $pinjaman->anggota->id_anggota,
                'nama' => $pinjaman->anggota->nama,
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => $items->min('tanggal_jatuh_tempo'),
                'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran . ' Bulan',
                'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                'dibayar' => $totalDibayar,
                'sisa_tagihan' => $sisaTagihan,
                'jumlah_denda' => $totalDenda,
                'angsuran_tertunggak' => $items->count(),
            ];
        })->values();

        $kreditMacet = $kreditMacetGrouped;

        // Generate notifications untuk angsuran yang akan jatuh tempo dalam 7 hari
        $upcomingDue = BayarAngsuran::with('pinjaman.anggota')
            ->where('status_bayar', 'Belum')
            ->whereBetween('tanggal_jatuh_tempo', [
                Carbon::now(),
                Carbon::now()->addDays(7)
            ])
            ->get();

        $notifications = $upcomingDue->map(function ($item) {
            return (object)[
                'nama' => $item->pinjaman->anggota->nama,
                'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo->format('Y-m-d'),
                'sisa_tagihan' => $item->jumlah_angsuran,
            ];
        });

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
     * Get data for filtering (AJAX)
     */
    public function getData(Request $request)
    {
        try {
            $periode = $request->get('periode', date('Y-m'));
            
            // Parse periode
            [$year, $month] = explode('-', $periode);
            
            // Query untuk mendapatkan kredit macet
            $query = BayarAngsuran::with([
                'pinjaman.anggota',
                'pinjaman.lamaAngsuran'
            ])
            ->where('status_bayar', 'Belum')
            ->whereYear('tanggal_jatuh_tempo', '<=', $year)
            ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
            ->where('tanggal_jatuh_tempo', '<', Carbon::now());

            $angsuranMacet = $query->orderBy('tanggal_jatuh_tempo', 'asc')->get();

            // Group by pinjaman
            $kreditMacetGrouped = $angsuranMacet->groupBy('pinjaman_id')->map(function ($items) {
                $pinjaman = $items->first()->pinjaman;
                
                $jumlahTagihan = $items->sum('jumlah_angsuran');
                $totalDenda = $items->sum('denda');
                
                $totalDibayar = BayarAngsuran::where('pinjaman_id', $pinjaman->id)
                    ->where('status_bayar', 'Lunas')
                    ->sum('jumlah_bayar');
                
                $sisaTagihan = $pinjaman->jumlah_angsuran - $totalDibayar;
                
                return (object)[
                    'id' => $pinjaman->id,
                    'kode_pinjam' => $pinjaman->kode_pinjaman,
                    'id_anggota' => $pinjaman->anggota->id_anggota,
                    'nama' => $pinjaman->anggota->nama,
                    'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                    'tanggal_tempo' => $items->min('tanggal_jatuh_tempo'),
                    'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran . ' Bulan',
                    'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                    'dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'jumlah_denda' => $totalDenda,
                    'angsuran_tertunggak' => $items->count(),
                ];
            })->values();

            // Calculate totals
            $totalTagihan = $kreditMacetGrouped->sum('jumlah_tagihan');
            $totalDibayar = $kreditMacetGrouped->sum('dibayar');
            $sisaTagihan = $kreditMacetGrouped->sum('sisa_tagihan');
            $totalData = $kreditMacetGrouped->count();

            return response()->json([
                'status' => 'success',
                'data' => $kreditMacetGrouped,
                'summary' => [
                    'total_tagihan' => $totalTagihan,
                    'total_dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'total_data' => $totalData,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting kredit macet data: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data kredit macet'
            ], 500);
        }
    }

    /**
     * Print laporan kredit macet with filters
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        
        // Parse periode
        [$year, $month] = explode('-', $periode);
        
        // Query untuk mendapatkan kredit macet
        $query = BayarAngsuran::with([
            'pinjaman.anggota',
            'pinjaman.lamaAngsuran'
        ])
        ->where('status_bayar', 'Belum')
        ->whereYear('tanggal_jatuh_tempo', '<=', $year)
        ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
        ->where('tanggal_jatuh_tempo', '<', Carbon::now());

        $angsuranMacet = $query->orderBy('tanggal_jatuh_tempo', 'asc')->get();

        // Group by pinjaman
        $kreditMacetGrouped = $angsuranMacet->groupBy('pinjaman_id')->map(function ($items) {
            $pinjaman = $items->first()->pinjaman;
            
            $jumlahTagihan = $items->sum('jumlah_angsuran');
            $totalDenda = $items->sum('denda');
            
            $totalDibayar = BayarAngsuran::where('pinjaman_id', $pinjaman->id)
                ->where('status_bayar', 'Lunas')
                ->sum('jumlah_bayar');
            
            $sisaTagihan = $pinjaman->jumlah_angsuran - $totalDibayar;
            
            return (object)[
                'id' => $pinjaman->id,
                'kode_pinjam' => $pinjaman->kode_pinjaman,
                'id_anggota' => $pinjaman->anggota->id_anggota,
                'nama' => $pinjaman->anggota->nama,
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => $items->min('tanggal_jatuh_tempo'),
                'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran . ' Bulan',
                'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                'dibayar' => $totalDibayar,
                'sisa_tagihan' => $sisaTagihan,
                'jumlah_denda' => $totalDenda,
                'angsuran_tertunggak' => $items->count(),
            ];
        })->values();

        $kreditMacet = $kreditMacetGrouped;

        // Calculate totals
        $totalTagihan = $kreditMacet->sum('jumlah_tagihan');
        $totalDibayar = $kreditMacet->sum('dibayar');
        $sisaTagihan = $kreditMacet->sum('sisa_tagihan');

        // Format periode untuk display
        $periodeText = Carbon::createFromFormat('Y-m', $periode)->locale('id')->translatedFormat('F Y');

        return view('admin.Laporan.kreditMacet.cetak', compact(
            'kreditMacet',
            'periode',
            'periodeText',
            'totalTagihan',
            'totalDibayar',
            'sisaTagihan'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        
        // TODO: Implement Excel export using Laravel Excel
        // Example:
        // return Excel::download(new KreditMacetExport($periode), 'laporan-kredit-macet-' . $periode . '.xlsx');
        
        // Temporary: Return dummy file or redirect
        return redirect()->back()->with('info', 'Fitur export Excel akan segera tersedia');
    }

    /**
     * Kirim surat pemanggilan
     */
    public function kirimPemanggilan(Request $request)
    {
        $validated = $request->validate([
            'periode' => 'required|string',
            'metode' => 'required|in:email,sms,whatsapp,cetak',
            'catatan' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $periode = $validated['periode'];
            [$year, $month] = explode('-', $periode);
            
            // Get kredit macet data
            $query = BayarAngsuran::with([
                'pinjaman.anggota',
                'pinjaman.lamaAngsuran'
            ])
            ->where('status_bayar', 'Belum')
            ->whereYear('tanggal_jatuh_tempo', '<=', $year)
            ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
            ->where('tanggal_jatuh_tempo', '<', Carbon::now());

            $angsuranMacet = $query->get();
            
            // Group by pinjaman to get unique anggota
            $pinjamanIds = $angsuranMacet->pluck('pinjaman_id')->unique();
            $pinjaman = Pinjaman::with('anggota')->whereIn('id', $pinjamanIds)->get();
            
            $totalAnggota = $pinjaman->count();

            // TODO: Implement actual notification sending based on method
            switch ($validated['metode']) {
                case 'email':
                    // Send email to each anggota
                    // foreach ($pinjaman as $p) {
                    //     Mail::to($p->anggota->email)->send(new SuratPemanggilanMail($p));
                    // }
                    break;
                    
                case 'sms':
                    // Send SMS
                    // foreach ($pinjaman as $p) {
                    //     SMS::send($p->anggota->phone, $message);
                    // }
                    break;
                    
                case 'whatsapp':
                    // Send WhatsApp
                    // foreach ($pinjaman as $p) {
                    //     WhatsApp::send($p->anggota->phone, $message);
                    // }
                    break;
                    
                case 'cetak':
                    // Generate printable document
                    // return view('admin.laporan.surat-pemanggilan', compact('pinjaman'));
                    break;
            }

            // Log the action
            Log::info('Surat pemanggilan dikirim', [
                'periode' => $periode,
                'metode' => $validated['metode'],
                'total_anggota' => $totalAnggota,
                'user_id' => auth()->id()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Surat pemanggilan berhasil dikirim kepada {$totalAnggota} anggota melalui {$validated['metode']}",
                'data' => [
                    'total_anggota' => $totalAnggota,
                    'metode' => $validated['metode']
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error sending pemanggilan: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim surat pemanggilan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detail kredit macet untuk satu anggota
     */
    public function detail($pinjamanId)
    {
        try {
            $pinjaman = Pinjaman::with([
                'anggota',
                'lamaAngsuran',
                'angsuran' => function ($query) {
                    $query->where('status_bayar', 'Belum')
                          ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                          ->orderBy('tanggal_jatuh_tempo', 'asc');
                }
            ])->findOrFail($pinjamanId);

            $angsuranMacet = $pinjaman->angsuran;
            
            $totalDibayar = BayarAngsuran::where('pinjaman_id', $pinjamanId)
                ->where('status_bayar', 'Lunas')
                ->sum('jumlah_bayar');
            
            $sisaTagihan = $pinjaman->jumlah_angsuran - $totalDibayar;
            $totalDenda = $angsuranMacet->sum('denda');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'pinjaman' => $pinjaman,
                    'angsuran_macet' => $angsuranMacet,
                    'total_dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan,
                    'total_denda' => $totalDenda,
                    'angsuran_tertunggak' => $angsuranMacet->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting detail kredit macet: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail kredit macet'
            ], 500);
        }
    }

    /**
     * Get summary statistics for dashboard
     */
    public function getSummary(Request $request)
    {
        try {
            $periode = $request->get('periode', date('Y-m'));
            [$year, $month] = explode('-', $periode);
            
            // Total kredit macet
            $totalMacet = BayarAngsuran::where('status_bayar', 'Belum')
                ->whereYear('tanggal_jatuh_tempo', '<=', $year)
                ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
                ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                ->count();
            
            // Total anggota bermasalah
            $anggotaBermasalah = BayarAngsuran::where('status_bayar', 'Belum')
                ->whereYear('tanggal_jatuh_tempo', '<=', $year)
                ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
                ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                ->distinct('pinjaman_id')
                ->count();
            
            // Total tagihan macet
            $totalTagihan = BayarAngsuran::where('status_bayar', 'Belum')
                ->whereYear('tanggal_jatuh_tempo', '<=', $year)
                ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
                ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                ->sum('jumlah_angsuran');
            
            // Total denda
            $totalDenda = BayarAngsuran::where('status_bayar', 'Belum')
                ->whereYear('tanggal_jatuh_tempo', '<=', $year)
                ->whereMonth('tanggal_jatuh_tempo', '<=', $month)
                ->where('tanggal_jatuh_tempo', '<', Carbon::now())
                ->sum('denda');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_angsuran_macet' => $totalMacet,
                    'total_anggota_bermasalah' => $anggotaBermasalah,
                    'total_tagihan' => $totalTagihan,
                    'total_denda' => $totalDenda,
                    'periode' => Carbon::createFromFormat('Y-m', $periode)->locale('id')->translatedFormat('F Y')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting summary: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil ringkasan data'
            ], 500);
        }
    }
}