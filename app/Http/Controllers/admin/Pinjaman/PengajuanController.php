<?php

namespace App\Http\Controllers\Admin\Pinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\PengajuanPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengajuanController extends Controller
{
    /**
     * Display pengajuan list with filters
     */
    public function index(Request $request)
    {
        $query = PengajuanPinjaman::with(['anggota', 'lamaAngsuran', 'user', 'approvedBy']);

        // Filter by Jenis Pinjaman
        if ($request->filled('jenis')) {
            $query->byJenis($request->jenis);
        }

        // Filter by Status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by Bulan (21 bulan lalu - 20 bulan ini)
        if ($request->filled('bulan')) {
            try {
                $bulan = Carbon::createFromFormat('Y-m', $request->bulan);
                $startDate = $bulan->copy()->subMonth()->day(21)->startOfDay();
                $endDate = $bulan->copy()->day(20)->endOfDay();

                $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::warning('Invalid bulan format: ' . $request->bulan);
            }
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal')) {
            $dates = explode(' - ', $request->tanggal);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

                    $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    Log::warning('Invalid tanggal format: ' . $request->tanggal);
                }
            }
        }

        $pengajuan = $query->orderBy('tanggal_pengajuan', 'desc')->get();

        // Hitung data untuk setiap pengajuan (dummy untuk sisa pinjaman/angsuran)
        foreach ($pengajuan as $item) {
            $item->sisa_pinjaman = 0; // TODO: Implementasi setelah ada tabel pembayaran
            $item->sisa_angsuran = 0;
            $item->sisa_tagihan = 0;
        }

        $total_pengajuan = $pengajuan->count();

        return view('admin.Pinjaman.pengajuan.Pengajuan', compact('pengajuan', 'total_pengajuan'));
    }

    /**
     * Process action (setujui, tolak, pending, batal, terlaksana, hapus)
     */
    public function aksi(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'aksi' => 'required|string|in:setujui,tolak,pending,batal,terlaksana,belum,hapus',
            'alasan' => 'nullable|string|max:1000',
            'tgl_cair' => 'nullable|date',
        ]);

        $pengajuan = PengajuanPinjaman::find($validated['id']);

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $message = $this->processAction($pengajuan, $validated['aksi'], $validated['alasan'] ?? null, $validated['tgl_cair'] ?? null);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing aksi pengajuan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process specific action
     */
    private function processAction($pengajuan, $aksi, $alasan, $tglCair)
    {
        switch ($aksi) {
            case 'setujui':
                if (!$pengajuan->canBeApproved()) {
                    throw new \Exception('Hanya pengajuan dengan status pending yang dapat disetujui');
                }

                $pengajuan->status = 1;
                $pengajuan->tanggal_cair = $tglCair ?? now()->format('Y-m-d');
                $pengajuan->alasan = $alasan;
                $pengajuan->approved_by = Auth::id();
                $pengajuan->save();

                return 'Pengajuan berhasil disetujui';

            case 'tolak':
                if (!$pengajuan->canBeRejected()) {
                    throw new \Exception('Pengajuan ini tidak dapat ditolak');
                }

                if (empty($alasan)) {
                    throw new \Exception('Alasan penolakan wajib diisi');
                }

                $pengajuan->status = 2;
                $pengajuan->alasan = $alasan;
                $pengajuan->approved_by = Auth::id();
                $pengajuan->tanggal_cair = null;
                $pengajuan->save();

                return 'Pengajuan berhasil ditolak';

            case 'pending':
                $pengajuan->status = 0;
                $pengajuan->alasan = $alasan;
                $pengajuan->tanggal_cair = null;
                $pengajuan->save();

                return 'Pengajuan berhasil dikembalikan ke status pending';

            case 'batal':
                $pengajuan->status = 4;
                $pengajuan->approved_by = Auth::id();
                $pengajuan->save();

                return 'Pengajuan berhasil dibatalkan';

            case 'terlaksana':
                if (!$pengajuan->canBeMarkedTerlaksana()) {
                    throw new \Exception('Hanya pengajuan yang disetujui yang dapat ditandai terlaksana');
                }

                $pengajuan->status = 3;
                $pengajuan->save();

                return 'Pengajuan berhasil ditandai terlaksana';

            case 'belum':
                if ($pengajuan->status != 3) {
                    throw new \Exception('Hanya pengajuan terlaksana yang dapat dikembalikan');
                }

                $pengajuan->status = 1;
                $pengajuan->save();

                return 'Status pengajuan berhasil dikembalikan ke disetujui';

            case 'hapus':
                $pengajuan->delete();
                return 'Pengajuan berhasil dihapus';

            default:
                throw new \Exception('Aksi tidak valid');
        }
    }

    /**
     * Print single pengajuan (Cetak per ID)
     */
    public function cetak($id)
    {
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran', 'user', 'approvedBy'])
            ->findOrFail($id);

        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        // ✅ Hitung terbilang di controller
        $terbilang = $this->terbilang($pengajuan->jumlah);

        $pdf = Pdf::loadView('admin.Pinjaman.pengajuan.cetak', compact('pengajuan', 'identitas', 'terbilang'));
        $pdf->setPaper([0, 0, 595.28, 419.53]);

        return $pdf->stream('Bukti_Pengajuan_' . $pengajuan->id_ajuan . '.pdf');
    }

    /**
     * Fungsi terbilang (tambahkan di controller)
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . " puluh" . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " seratus" . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . " ratus" . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " seribu" . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . " ribu" . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . " juta" . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000) . " milyar" . $this->terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000000) . " trilyun" . $this->terbilang(fmod($angka, 1000000000000));
        }

        return trim($terbilang);
    }

    /**
     * Print laporan with filters
     */
    /**
     * Print laporan with filters
     */
    public function cetakLaporan(Request $request)
    {
        $jenis = $request->get('jenis', '');
        $status = $request->get('status', '');
        $bulan = $request->get('bulan', '');
        $tanggal = $request->get('tanggal', '');

        // ✅ Mulai query builder
        $query = PengajuanPinjaman::query()
            ->with(['anggota', 'lamaAngsuran', 'user', 'approvedBy']);

        // Apply filters HANYA jika ada nilai
        if (!empty($jenis)) {
            $query->where('jenis_pinjaman', $jenis);
        }

        // ✅ Perbaikan: Gunakan !empty() atau strlen() untuk status
        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        if (!empty($bulan)) {
            try {
                $bulanDate = Carbon::createFromFormat('Y-m', $bulan);
                $startDate = $bulanDate->copy()->subMonth()->day(21)->startOfDay();
                $endDate = $bulanDate->copy()->day(20)->endOfDay();
                $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::warning('Invalid bulan format in cetak: ' . $bulan);
            }
        }

        if (!empty($tanggal)) {
            $dates = explode(' - ', $tanggal);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    Log::warning('Invalid tanggal format in cetak: ' . $tanggal);
                }
            }
        }

        // ✅ Execute query
        $pengajuan = $query->orderBy('tanggal_pengajuan', 'desc')->get();

        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        $pdf = Pdf::loadView('admin.Pinjaman.pengajuan.cetakLaporan', compact(
            'pengajuan',
            'jenis',
            'status',
            'bulan',
            'tanggal',
            'identitas'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Pengajuan_Pinjaman.pdf');
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export using Laravel Excel
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran', 'user'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // return Excel::download(new PengajuanExport($pengajuan), 'pengajuan-pinjaman.xlsx');

        return response()->download(public_path('dummy.xlsx'));
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        // TODO: Implement PDF export
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran', 'user'])
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        // $pdf = PDF::loadView('admin.Pinjaman.pengajuan.exportPdf', compact('pengajuan'));
        // return $pdf->download('pengajuan-pinjaman.pdf');

        return response()->download(public_path('dummy.pdf'));
    }
}