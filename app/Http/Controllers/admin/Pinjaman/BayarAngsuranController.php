<?php

namespace App\Http\Controllers\Admin\Pinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\Pinjaman\PinjamanLunas;
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
            $item->lama_angsuran = $item->lamaAngsuran->lama_angsuran;
            $item->bunga_angsuran = $item->biaya_bunga;
            $item->angsuran_per_bulan = $item->angsuran_pokok + $item->biaya_bunga;

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
        $pinjaman = Pinjaman::with([
            'anggota',
            'lamaAngsuran',
            'angsuran.detailPembayaran.user',
            'angsuran.detailPembayaran.kas',
            'kas'
        ])->findOrFail($id);

        // Data jadwal angsuran dari tabel bayar_angsuran
        $jadwalAngsuran = $pinjaman->angsuran()
            ->orderBy('angsuran_ke', 'asc')
            ->get();

        // Data pembayaran aktual dari tabel detail_bayar_angsuran
        $pembayaran = DetailBayarAngsuran::with(['angsuran', 'kas', 'user'])
            ->where('pinjaman_id', $id)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        // Hitung data agregat
        $pinjaman->lama_pinjaman = $pinjaman->lamaAngsuran->lama_angsuran;
        $pinjaman->sudah_dibayar = $pinjaman->total_bayar;
        $pinjaman->jumlah_denda = $pinjaman->total_denda;
        $pinjaman->total_sisa_tagihan = $pinjaman->sisa_tagihan;
        $pinjaman->jumlah_sisa_angsuran = $pinjaman->sisa_angsuran;

        // Data anggota
        $pinjaman->anggota_id = $pinjaman->anggota->id_anggota;
        $pinjaman->anggota_nama = $pinjaman->anggota->nama;
        $pinjaman->anggota_departement = $pinjaman->anggota->departement ?? '-';
        $pinjaman->anggota_ttl = $pinjaman->anggota->tempat_lahir . ', ' .
            Carbon::parse($pinjaman->anggota->tanggal_lahir)->translatedFormat('d F Y');
        $pinjaman->anggota_kota = $pinjaman->anggota->kota;
        $pinjaman->anggota_foto = $pinjaman->anggota->photo;

        // Data kas untuk dropdown
        $kasList = DataKas::where('aktif', 'Y')->orderBy('nama_kas')->get();

        return view('admin.Pinjaman.bayar.DetailBayarAngsuran', compact(
            'pinjaman',
            'jadwalAngsuran',
            'pembayaran',
            'kasList'
        ));
    }

    /**
     * Store new payment ke detail_bayar_angsuran
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
            $angsuran = BayarAngsuran::with('pinjaman')->findOrFail($validated['angsuran_id']);

            // Validasi belum lunas
            if ($angsuran->status_bayar === 'Lunas') {
                throw new \Exception('Angsuran ini sudah lunas');
            }

            // Simpan ke detail_bayar_angsuran (kode TBY auto-generate)
            $detailBayar = DetailBayarAngsuran::create([
                'bayar_angsuran_id' => $angsuran->id,
                'pinjaman_id' => $angsuran->pinjaman_id,
                'angsuran_ke' => $angsuran->angsuran_ke,
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'denda' => $validated['denda'] ?? 0,
                'ke_kas_id' => $validated['ke_kas_id'],
                'keterangan' => $validated['keterangan'],
                'user_id' => Auth::id(),
            ]);

            // Update status di bayar_angsuran
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
                'kode_bayar' => $detailBayar->kode_bayar,
                'detail_id' => $detailBayar->id
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
            'ke_kas_id' => 'required|exists:data_kas,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update detail pembayaran
            $detailBayar = DetailBayarAngsuran::findOrFail($id);

            $detailBayar->update([
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'denda' => $validated['denda'] ?? 0,
                'ke_kas_id' => $validated['ke_kas_id'],
                'keterangan' => $validated['keterangan'],
            ]);

            // Update juga di bayar_angsuran
            $angsuran = $detailBayar->angsuran;
            $angsuran->update([
                'tanggal_bayar' => $validated['tanggal_bayar'],
                'jumlah_bayar' => $validated['jumlah_bayar'],
                'denda' => $validated['denda'] ?? 0,
                'ke_kas_id' => $validated['ke_kas_id'],
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

    public function softDelete($id)
    {
        DB::beginTransaction();
        try {
            // Cari detail pembayaran
            $detailBayar = DetailBayarAngsuran::findOrFail($id);
            $angsuran = $detailBayar->angsuran;

            // Validasi: tidak bisa hapus jika pinjaman sudah divalidasi lunas
            $pinjaman = $angsuran->pinjaman;
            $sudahValidasiLunas = PinjamanLunas::where('pinjaman_id', $pinjaman->id)->exists();

            if ($sudahValidasiLunas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus pembayaran karena pinjaman sudah divalidasi lunas. Hubungi admin untuk pembatalan validasi.'
                ], 400);
            }

            // Soft delete detail pembayaran
            $detailBayar->delete(); // Ini akan soft delete karena menggunakan SoftDeletes trait

            // Reset status di bayar_angsuran
            $angsuran->update([
                'tanggal_bayar' => null,
                'jumlah_bayar' => 0,
                'denda' => 0,
                'ke_kas_id' => null,
                'status_bayar' => 'Belum',
                'keterangan' => null,
                'user_id' => null,
            ]);

            // Update status pinjaman jika ada angsuran yang belum lunas
            if ($pinjaman->status_lunas === 'Lunas') {
                $pinjaman->update(['status_lunas' => 'Belum']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dihapus dan dapat dipulihkan dari menu riwayat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error soft delete bayar angsuran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function restore($id)
    {
        DB::beginTransaction();
        try {
            // Restore dari soft delete
            $detailBayar = DetailBayarAngsuran::withTrashed()->findOrFail($id);

            if (!$detailBayar->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembayaran tidak ditemukan di riwayat hapus'
                ], 400);
            }

            $detailBayar->restore();

            // Update kembali status di bayar_angsuran
            $angsuran = $detailBayar->angsuran;
            $angsuran->update([
                'tanggal_bayar' => $detailBayar->tanggal_bayar,
                'jumlah_bayar' => $detailBayar->jumlah_bayar,
                'denda' => $detailBayar->denda,
                'ke_kas_id' => $detailBayar->ke_kas_id,
                'status_bayar' => 'Lunas',
                'keterangan' => $detailBayar->keterangan,
                'user_id' => $detailBayar->user_id,
            ]);

            // Cek apakah semua angsuran lunas
            $pinjaman = $angsuran->pinjaman;
            $sisaAngsuran = $pinjaman->angsuran()->where('status_bayar', 'Belum')->count();

            if ($sisaAngsuran === 0) {
                $pinjaman->update(['status_lunas' => 'Lunas']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dipulihkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restore bayar angsuran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function riwayatHapus($pinjamanId)
    {
        try {
            $riwayat = DetailBayarAngsuran::onlyTrashed()
                ->with(['angsuran', 'kas', 'user'])
                ->where('pinjaman_id', $pinjamanId)
                ->orderBy('deleted_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $riwayat
            ]);

        } catch (\Exception $e) {
            Log::error('Error get riwayat hapus: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat hapus'
            ], 500);
        }
    }

    /**
     * Permanent delete (hapus permanen dari database)
     */
    public function forceDelete($id)
    {
        DB::beginTransaction();
        try {
            $detailBayar = DetailBayarAngsuran::withTrashed()->findOrFail($id);

            if (!$detailBayar->trashed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hapus data terlebih dahulu sebelum menghapus permanen'
                ], 400);
            }

            $detailBayar->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus permanen dari database'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force delete: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete payment record
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Hapus dari detail_bayar_angsuran
            $detailBayar = DetailBayarAngsuran::findOrFail($id);
            $angsuran = $detailBayar->angsuran;

            // Reset status di bayar_angsuran
            $angsuran->update([
                'tanggal_bayar' => null,
                'jumlah_bayar' => 0,
                'denda' => 0,
                'ke_kas_id' => null,
                'status_bayar' => 'Belum',
                'keterangan' => null,
                'user_id' => null,
            ]);

            // Hapus detail pembayaran
            $detailBayar->delete();

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
        try {
            $angsuran = BayarAngsuran::with(['pinjaman.anggota'])
                ->findOrFail($id);

            // Hitung denda jika terlambat
            $denda = 0;
            $hariTerlambat = 0;

            if ($angsuran->status_bayar == 'Belum') {
                $hariTerlambat = max(0, now()->diffInDays($angsuran->tanggal_jatuh_tempo, false) * -1);
                if ($hariTerlambat > 0) {
                    // Ambil dari setting suku bunga
                    $dendaPerHari = 5000; // TODO: Ambil dari tabel suku_bunga
                    $denda = $hariTerlambat * $dendaPerHari;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $angsuran->id,
                    'angsuran_ke' => $angsuran->angsuran_ke,
                    'tanggal_jatuh_tempo' => $angsuran->tanggal_jatuh_tempo->format('Y-m-d'),
                    'tanggal_jatuh_tempo_formatted' => $angsuran->tanggal_jatuh_tempo->translatedFormat('d F Y'),
                    'jumlah_angsuran' => $angsuran->jumlah_angsuran,
                    'status' => $angsuran->status_bayar,
                    'is_terlambat' => now()->gt($angsuran->tanggal_jatuh_tempo),
                    'hari_terlambat' => $hariTerlambat,
                    'denda_otomatis' => $denda,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error get detail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data angsuran'
            ], 500);
        }
    }

    /**
     * Get detail pembayaran untuk edit (TAMBAHKAN INI) ⭐
     */
    public function getPembayaran($id)
    {
        try {
            // $id adalah bayar_angsuran_id
            $angsuran = BayarAngsuran::with([
                'detailPembayaran' => function ($query) {
                    $query->latest();
                }
            ])->findOrFail($id);

            if ($angsuran->status_bayar !== 'Lunas') {
                return response()->json([
                    'success' => false,
                    'message' => 'Angsuran belum dibayar'
                ], 400);
            }

            // Ambil pembayaran terakhir
            $pembayaran = $angsuran->detailPembayaran->first();

            if (!$pembayaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data pembayaran tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pembayaran->id,
                    'kode_bayar' => $pembayaran->kode_bayar,
                    'angsuran_ke' => $angsuran->angsuran_ke,
                    'tanggal_bayar' => Carbon::parse($pembayaran->tanggal_bayar)->format('Y-m-d\TH:i'),
                    'jumlah_bayar' => $pembayaran->jumlah_bayar,
                    'denda' => $pembayaran->denda ?? 0,
                    'ke_kas_id' => $pembayaran->ke_kas_id,
                    'keterangan' => $pembayaran->keterangan ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error get pembayaran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validasiLunas($id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::with(['angsuran', 'anggota', 'lamaAngsuran'])->findOrFail($id);

            // Cek apakah semua angsuran sudah lunas
            $sisaAngsuran = $pinjaman->angsuran()
                ->where('status_bayar', 'Belum')
                ->whereNull('deleted_at')
                ->count();

            if ($sisaAngsuran > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Masih ada ' . $sisaAngsuran . ' angsuran yang belum dibayar'
                ], 400);
            }

            // Cek apakah ada angsuran sama sekali
            $totalAngsuran = $pinjaman->angsuran()
                ->whereNull('deleted_at')
                ->count();

            if ($totalAngsuran == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pinjaman ini tidak memiliki data angsuran'
                ], 400);
            }

            // Cek apakah sudah pernah divalidasi
            $sudahLunas = PinjamanLunas::where('pinjaman_id', $id)->exists();
            if ($sudahLunas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pinjaman ini sudah pernah divalidasi lunas'
                ], 400);
            }

            // Hitung total pembayaran dari detail_bayar_angsuran
            $pembayaran = DetailBayarAngsuran::where('pinjaman_id', $id)
                ->whereNull('deleted_at')
                ->selectRaw('
                SUM(jumlah_bayar) as total_bayar,
                SUM(denda) as total_denda,
                COUNT(*) as jumlah_pembayaran
            ')
                ->first();

            $totalPokok = $pinjaman->pokok_pinjaman;
            $totalBunga = $pinjaman->biaya_bunga * $pinjaman->lamaAngsuran->lama_angsuran;
            $totalDenda = $pembayaran->total_denda ?? 0;
            $totalDibayar = $pembayaran->total_bayar ?? 0;

            // Ambil tanggal bayar terakhir
            $angsuranTerakhir = $pinjaman->angsuran()
                ->where('status_bayar', 'Lunas')
                ->whereNull('deleted_at')
                ->orderBy('tanggal_bayar', 'desc')
                ->first();

            // ✅ HANYA INSERT ke pinjaman_lunas (header saja)
            $pinjamanLunas = PinjamanLunas::create([
                'pinjaman_id' => $pinjaman->id,
                'tanggal_lunas' => $angsuranTerakhir->tanggal_bayar ?? now(),
                'total_pokok' => $totalPokok,
                'total_bunga' => $totalBunga,
                'total_denda' => $totalDenda,
                'total_dibayar' => $totalDibayar + $totalDenda,
                'lama_cicilan' => $pinjaman->lamaAngsuran->lama_angsuran,
                'total_angsuran' => $totalAngsuran,
                'keterangan' => 'Validasi pelunasan pinjaman ' . $pinjaman->kode_pinjaman,
                'user_id' => Auth::id(),
            ]);

            // ✅ Update status pinjaman menjadi Lunas
            $pinjaman->update([
                'status_lunas' => 'Lunas'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman berhasil divalidasi sebagai lunas',
                'kode_lunas' => $pinjamanLunas->kode_lunas,
                'redirect' => route('pinjaman.lunas')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error validasi lunas: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memvalidasi pinjaman: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print bukti pembayaran
     */
    public function cetakNota($id)
    {
        $detailBayar = DetailBayarAngsuran::with([
            'pinjaman.anggota',
            'pinjaman.lamaAngsuran',
            'angsuran',
            'kas',
            'user'
        ])->findOrFail($id);

        return view('admin.Pinjaman.bayar.cetak_nota', compact('detailBayar'));
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