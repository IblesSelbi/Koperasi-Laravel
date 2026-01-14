<?php

namespace App\Http\Controllers\Admin\Pinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BayarAngsuranController extends Controller
{
    /**
     * Display a listing of pinjaman yang belum lunas
     */
    public function index(Request $request)
    {
        $query = Pinjaman::with(['anggota', 'lamaAngsuran', 'angsuran'])
            ->where('status_lunas', 'Belum');

        // Filter by Kode
        if ($request->filled('kode')) {
            $query->where('kode_pinjaman', 'like', '%' . $request->kode . '%');
        }

        // Filter by Nama Anggota
        if ($request->filled('nama')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            });
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal')) {
            $dates = explode(' - ', $request->tanggal);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    Log::warning('Invalid tanggal format: ' . $request->tanggal);
                }
            }
        }

        $pinjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        // Hitung data untuk setiap pinjaman
        foreach ($pinjaman as $item) {
            // Data angsuran - FIXED: Tidak ada circular reference
            $item->lama_angsuran = $item->lamaAngsuran->lama_angsuran;
            $item->bunga_angsuran = $item->biaya_bunga;
            $item->angsuran_per_bulan = $item->angsuran_pokok + $item->biaya_bunga;
            
            // Data anggota
            $item->anggota_nama = $item->anggota->nama;
            $item->anggota_id = $item->anggota->id_anggota;
            $item->anggota_kota = $item->anggota->kota;
            $item->anggota_foto = $item->anggota->photo;
        }

        // Notifikasi angsuran yang akan jatuh tempo (7 hari ke depan)
        $notifications = BayarAngsuran::with(['pinjaman.anggota'])
            ->where('status_bayar', 'Belum')
            ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'nama' => $item->pinjaman->anggota->nama,
                    // FIXED: Gunakan translatedFormat untuk Bahasa Indonesia
                    'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo->translatedFormat('d F Y'),
                    'sisa_tagihan' => $item->jumlah_angsuran,
                ];
            });

        return view('admin.Pinjaman.bayar.BayarAngsuran', compact('pinjaman', 'notifications'));
    }

    /**
     * Display detail bayar angsuran untuk pinjaman tertentu
     */
    public function show($id)
    {
        $pinjaman = Pinjaman::with(['anggota', 'lamaAngsuran', 'angsuran.user', 'kas'])
            ->findOrFail($id);

        // Data pembayaran angsuran
        $pembayaran = $pinjaman->angsuran()
            ->orderBy('angsuran_ke', 'asc')
            ->get();

        // FIXED: Hitung data agregat - langsung simpan di variable berbeda
        $pinjaman->lama_pinjaman = $pinjaman->lamaAngsuran->lama_angsuran;
        $pinjaman->sudah_dibayar = $pinjaman->total_bayar; // Dari accessor
        $pinjaman->jumlah_denda = $pinjaman->total_denda; // Dari accessor
        $pinjaman->total_sisa_tagihan = $pinjaman->sisa_tagihan; // Dari accessor
        $pinjaman->jumlah_sisa_angsuran = $pinjaman->sisa_angsuran; // Dari accessor

        // Data anggota
        $pinjaman->anggota_id = $pinjaman->anggota->id_anggota;
        $pinjaman->anggota_nama = $pinjaman->anggota->nama;
        $pinjaman->anggota_departemen = $pinjaman->anggota->departemen ?? '-';
        
        // FIXED: Gunakan translatedFormat untuk Bahasa Indonesia
        $pinjaman->anggota_ttl = $pinjaman->anggota->tempat_lahir . ', ' . 
                                  Carbon::parse($pinjaman->anggota->tanggal_lahir)->translatedFormat('d F Y');
        $pinjaman->anggota_kota = $pinjaman->anggota->kota;
        $pinjaman->anggota_foto = $pinjaman->anggota->photo;

        // Notifikasi (bisa dihapus atau ambil dari database)
        $notifications = collect([]);

        // Data kas untuk dropdown
        $kasList = DataKas::orderBy('nama_kas')->get();

        return view('admin.Pinjaman.bayar.DetailBayarAngsuran', compact('pinjaman', 'pembayaran', 'notifications', 'kasList'));
    }

    /**
     * Store new payment (from detail page)
     */
    public function bayar(Request $request)
    {
        $validated = $request->validate([
            'angsuran_id' => 'required|exists:bayar_angsuran,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'ke_kas_id' => 'required|exists:data_kas,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $angsuran = BayarAngsuran::findOrFail($validated['angsuran_id']);

            // Validasi belum lunas
            if ($angsuran->status_bayar === 'Lunas') {
                throw new \Exception('Angsuran ini sudah lunas');
            }

            // Update angsuran
            $angsuran->update([
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'denda' => $validated['denda'] ?? 0,
                'ke_kas_id' => $validated['ke_kas_id'],
                'status_bayar' => 'Lunas',
                'keterangan' => $validated['keterangan'],
                'user_id' => Auth::id(),
            ]);

            // Cek apakah semua angsuran sudah lunas
            $pinjaman = $angsuran->pinjaman;
            $sisaAngsuran = $pinjaman->angsuran()->where('status_bayar', 'Belum')->count();
            
            if ($sisaAngsuran === 0) {
                $pinjaman->update(['status_lunas' => 'Lunas']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran angsuran berhasil disimpan',
                'angsuran_id' => $angsuran->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bayar angsuran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update existing payment
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $angsuran = BayarAngsuran::findOrFail($id);

            if ($angsuran->status_bayar !== 'Lunas') {
                throw new \Exception('Hanya pembayaran yang sudah lunas yang dapat diupdate');
            }

            $angsuran->update([
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'denda' => $validated['denda'] ?? 0,
                'keterangan' => $validated['keterangan'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error update bayar angsuran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete payment record
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $angsuran = BayarAngsuran::findOrFail($id);

            if ($angsuran->status_bayar !== 'Lunas') {
                throw new \Exception('Hanya pembayaran yang sudah lunas yang dapat dihapus');
            }

            // Reset angsuran
            $angsuran->update([
                'tanggal_bayar' => null,
                'jumlah_bayar' => 0,
                'denda' => 0,
                'ke_kas_id' => null,
                'status_bayar' => 'Belum',
                'keterangan' => null,
                'user_id' => null,
            ]);

            // Update status pinjaman
            $pinjaman = $angsuran->pinjaman;
            if ($pinjaman->status_lunas === 'Lunas') {
                $pinjaman->update(['status_lunas' => 'Belum']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pembayaran berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error delete bayar angsuran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get detail angsuran untuk modal bayar
     */
    public function getDetail($id)
    {
        $angsuran = BayarAngsuran::with(['pinjaman.anggota'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $angsuran->id,
                'angsuran_ke' => $angsuran->angsuran_ke,
                // FIXED: Gunakan format biasa untuk API (Y-m-d)
                'tanggal_jatuh_tempo' => $angsuran->tanggal_jatuh_tempo->format('Y-f-d'),
                'jumlah_angsuran' => $angsuran->jumlah_angsuran,
                'status' => $angsuran->status_bayar,
                'is_terlambat' => $angsuran->is_terlambat,
                'hari_terlambat' => $angsuran->hari_terlambat,
            ]
        ]);
    }

    /**
     * Print bukti pembayaran
     */
    public function cetakNota($id)
    {
        $angsuran = BayarAngsuran::with(['pinjaman.anggota', 'pinjaman.lamaAngsuran', 'kas', 'user'])
            ->findOrFail($id);

        return view('admin.Pinjaman.bayar.cetak_nota', compact('angsuran'));
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export
        return response()->download(public_path('dummy.xlsx'));
    }
}