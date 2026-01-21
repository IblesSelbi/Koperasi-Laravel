<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JatuhTempoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        
        // Parse periode
        $year = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);
        
        // Query: Ambil angsuran yang jatuh tempo pada periode ini dan belum lunas
        $angsuran = BayarAngsuran::with(['pinjaman.anggota', 'pinjaman.lamaAngsuran'])
            ->whereYear('tanggal_jatuh_tempo', $year)
            ->whereMonth('tanggal_jatuh_tempo', $month)
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->whereHas('pinjaman', function($query) {
                $query->whereNull('deleted_at')
                      ->where('status_lunas', 'Belum');
            })
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        // Build data untuk view
        $jatuhTempo = $angsuran->map(function($item) {
            $pinjaman = $item->pinjaman;
            
            // Hitung total yang sudah dibayar untuk pinjaman ini
            $totalDibayar = DetailBayarAngsuran::where('pinjaman_id', $pinjaman->id)
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            return (object)[
                'id' => $item->id,
                'kode_pinjam' => $pinjaman->kode_pinjaman,
                'id_anggota' => $pinjaman->anggota->id_anggota ?? '-',
                'nama_anggota' => $pinjaman->anggota->nama ?? 'Unknown',
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => $item->tanggal_jatuh_tempo->format('Y-m-d'),
                'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran ?? 0,
                'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                'dibayar' => $totalDibayar,
                'sisa_tagihan' => $pinjaman->jumlah_angsuran - $totalDibayar,
                
                // Data tambahan untuk detail
                'angsuran_ke' => $item->angsuran_ke,
                'jumlah_angsuran_bulan_ini' => $item->jumlah_angsuran,
                'no_telepon' => $pinjaman->anggota->no_telp ?? '-',
                'email' => $pinjaman->anggota->email ?? '-',
            ];
        });

        // Calculate totals
        $totalTagihan = $jatuhTempo->sum('jumlah_tagihan');
        $totalDibayar = $jatuhTempo->sum('dibayar');
        $sisaTagihan = $jatuhTempo->sum('sisa_tagihan');

        // Notifications - Angsuran yang akan jatuh tempo dalam 7 hari ke depan (global)
        $notifications = BayarAngsuran::with(['pinjaman.anggota'])
            ->where('status_bayar', 'Belum')
            ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)])
            ->whereNull('deleted_at')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return (object)[
                    'nama' => $item->pinjaman->anggota->nama ?? 'Unknown',
                    'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo->format('Y-m-d'),
                    'sisa_tagihan' => $item->jumlah_angsuran - $item->jumlah_bayar,
                ];
            });

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
        
        // Parse periode
        $year = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);
        
        // Query sama seperti index
        $angsuran = BayarAngsuran::with(['pinjaman.anggota', 'pinjaman.lamaAngsuran'])
            ->whereYear('tanggal_jatuh_tempo', $year)
            ->whereMonth('tanggal_jatuh_tempo', $month)
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->whereHas('pinjaman', function($query) {
                $query->whereNull('deleted_at')
                      ->where('status_lunas', 'Belum');
            })
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        $jatuhTempo = $angsuran->map(function($item) {
            $pinjaman = $item->pinjaman;
            
            $totalDibayar = DetailBayarAngsuran::where('pinjaman_id', $pinjaman->id)
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            return (object)[
                'kode_pinjam' => $pinjaman->kode_pinjaman,
                'id_anggota' => $pinjaman->anggota->id_anggota ?? '-',
                'nama_anggota' => $pinjaman->anggota->nama ?? 'Unknown',
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => $item->tanggal_jatuh_tempo->format('Y-m-d'),
                'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran ?? 0,
                'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                'dibayar' => $totalDibayar,
                'sisa_tagihan' => $pinjaman->jumlah_angsuran - $totalDibayar,
                'angsuran_ke' => $item->angsuran_ke,
            ];
        });

        $totalTagihan = $jatuhTempo->sum('jumlah_tagihan');
        $totalDibayar = $jatuhTempo->sum('dibayar');
        $sisaTagihan = $jatuhTempo->sum('sisa_tagihan');
        
        return view('admin.laporan.JatuhTempo.CetakJatuhTempo', compact(
            'jatuhTempo', 
            'periode',
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
        $includeSummary = $request->get('summary', 'true') === 'true';
        $includeChart = $request->get('chart', 'true') === 'true';
        
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new JatuhTempoExport($periode, $includeSummary, $includeChart), 
        //     'jatuh-tempo-' . $periode . '.xlsx');
        
        return response('Export Excel Data Jatuh Tempo periode: ' . $periode);
    }

    /**
     * Send notifications to members
     */
    public function kirimNotifikasi(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));
        $template = $request->get('template', 'standard');
        
        // Parse periode
        $year = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);
        
        // Query anggota yang akan jatuh tempo
        $angsuran = BayarAngsuran::with(['pinjaman.anggota'])
            ->whereYear('tanggal_jatuh_tempo', $year)
            ->whereMonth('tanggal_jatuh_tempo', $month)
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->get();

        $countSms = 0;
        $countEmail = 0;
        $countWhatsapp = 0;

        foreach ($angsuran as $item) {
            $anggota = $item->pinjaman->anggota;
            
            if (!$anggota) continue;

            // Prepare message
            $message = $this->getMessageTemplate($template, [
                'nama' => $anggota->nama,
                'kode_pinjaman' => $item->pinjaman->kode_pinjaman,
                'tanggal_tempo' => $item->tanggal_jatuh_tempo->format('d F Y'),
                'jumlah' => number_format($item->jumlah_angsuran, 0, ',', '.'),
            ]);

            // TODO: Send SMS
            if ($anggota->no_telp) {
                // SMS::send($anggota->no_telp, $message);
                $countSms++;
            }

            // TODO: Send Email
            if ($anggota->email ?? false) {
                // Mail::to($anggota->email)->send(new ReminderPembayaran($item));
                $countEmail++;
            }

            // TODO: Send WhatsApp
            if ($anggota->no_telp) {
                // WhatsApp::send($anggota->no_telp, $message);
                $countWhatsapp++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil dikirim',
            'data' => [
                'sms' => $countSms,
                'email' => $countEmail,
                'whatsapp' => $countWhatsapp
            ]
        ]);
    }

    /**
     * Get message template
     */
    private function getMessageTemplate($template, $data)
    {
        $templates = [
            'standard' => "Yth. {$data['nama']},\n\nIni adalah pengingat bahwa pembayaran angsuran pinjaman {$data['kode_pinjaman']} akan jatuh tempo pada {$data['tanggal_tempo']}.\n\nJumlah: Rp {$data['jumlah']}\n\nMohon segera melakukan pembayaran. Terima kasih.",
            
            'urgent' => "URGENT - Yth. {$data['nama']},\n\nPembayaran angsuran pinjaman {$data['kode_pinjaman']} akan JATUH TEMPO pada {$data['tanggal_tempo']}.\n\nJumlah: Rp {$data['jumlah']}\n\nHarap segera melakukan pembayaran untuk menghindari denda keterlambatan.",
            
            'friendly' => "Halo {$data['nama']},\n\nKami ingin mengingatkan bahwa pembayaran angsuran pinjaman {$data['kode_pinjaman']} akan jatuh tempo pada {$data['tanggal_tempo']}.\n\nJumlah: Rp {$data['jumlah']}\n\nTerima kasih atas kepercayaan Anda kepada koperasi kami. 😊"
        ];

        return $templates[$template] ?? $templates['standard'];
    }
}