<?php

namespace App\Http\Controllers\Admin\Pinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\Pinjaman\PinjamanLunas;
use App\Models\Admin\DataMaster\DataAnggota;
use Illuminate\Support\Facades\Storage;
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
        $query = Pinjaman::with(['anggota.user', 'lamaAngsuran', 'angsuran'])
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

            // ✅ PERBAIKAN: Prioritas foto seperti di PinjamanController
            $photoPath = 'assets/images/profile/user-1.jpg';

            // Priority 1: data_anggota.photo (bukan default)
            if ($item->anggota->photo && $item->anggota->photo !== 'assets/images/profile/user-1.jpg') {
                $photoPath = 'storage/' . $item->anggota->photo;
            }
            // Priority 2: users.profile_image
            elseif ($item->anggota->user && $item->anggota->user->profile_image) {
                $photoPath = 'storage/' . $item->anggota->user->profile_image;
            }

            $item->anggota_foto = $photoPath;
        }

        // ✅ Notifikasi angsuran yang akan jatuh tempo (7 hari ke depan)
        $today = Carbon::now()->startOfDay();
        $sevenDaysLater = $today->copy()->addDays(7);

        $notifications = BayarAngsuran::with(['pinjaman.anggota'])
            ->where('status_bayar', 'Belum')
            ->whereBetween('tanggal_jatuh_tempo', [$today, $sevenDaysLater])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($item) use ($today) {
                $jatuhTempo = Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
                $selisihHari = (int) $today->diffInDays($jatuhTempo, false);

                // Tentukan keterangan status
                if ($selisihHari < 0) {
                    $keterangan = 'Terlambat ' . abs($selisihHari) . ' hari';
                } elseif ($selisihHari == 0) {
                    $keterangan = 'Jatuh tempo hari ini';
                } else {
                    $keterangan = $selisihHari . ' hari lagi';
                }

                return (object) [
                    'nama' => $item->pinjaman->anggota->nama,
                    'tanggal_jatuh_tempo' => $jatuhTempo->format('d F Y'),
                    'sisa_tagihan' => $item->jumlah_angsuran,
                    'selisih_hari' => $selisihHari,
                    'keterangan' => $keterangan,
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
            'anggota.user',
            'lamaAngsuran',
            'angsuran.detailPembayaran.user',
            'angsuran.detailPembayaran.kas',
            'kas'
        ])->findOrFail($id);

        // Data jadwal angsuran dari tabel bayar_angsuran
        $jadwalAngsuran = $pinjaman->angsuran()
            ->orderBy('angsuran_ke', 'asc')
            ->get()
            ->map(function ($item) {
                // ✅ Pastikan tanggal_jatuh_tempo adalah Carbon instance
                if (!($item->tanggal_jatuh_tempo instanceof \Carbon\Carbon)) {
                    $item->tanggal_jatuh_tempo = \Carbon\Carbon::parse($item->tanggal_jatuh_tempo);
                }

                // Hitung status keterlambatan
                $today = now()->startOfDay();
                $jatuhTempo = $item->tanggal_jatuh_tempo->copy()->startOfDay();
                $selisihHari = (int) $today->diffInDays($jatuhTempo, false);

                $item->is_terlambat = $selisihHari < 0 && $item->status_bayar === 'Belum';
                $item->hari_terlambat = $item->is_terlambat ? abs($selisihHari) : 0;

                // Hitung denda otomatis jika terlambat
                $item->denda_otomatis = $item->is_terlambat ? ($item->hari_terlambat * 5000) : 0;

                return $item;
            });

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

        // ✅ Parse tanggal_lahir dengan benar
        $tanggalLahir = $pinjaman->anggota->tanggal_lahir;
        if (!($tanggalLahir instanceof \Carbon\Carbon)) {
            $tanggalLahir = \Carbon\Carbon::parse($tanggalLahir);
        }

        $pinjaman->anggota_ttl = $pinjaman->anggota->tempat_lahir . ', ' . $tanggalLahir->translatedFormat('d F Y');
        $pinjaman->anggota_kota = $pinjaman->anggota->kota;

        // ✅ PERBAIKAN: Prioritas foto
        $photoPath = 'assets/images/profile/user-1.jpg';

        // Priority 1: data_anggota.photo (bukan default)
        if ($pinjaman->anggota->photo && $pinjaman->anggota->photo !== 'assets/images/profile/user-1.jpg') {
            $photoPath = 'storage/' . $pinjaman->anggota->photo;
        }
        // Priority 2: users.profile_image
        elseif ($pinjaman->anggota->user && $pinjaman->anggota->user->profile_image) {
            $photoPath = 'storage/' . $pinjaman->anggota->user->profile_image;
        }

        $pinjaman->anggota_foto = $photoPath;

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
     * Store new payment ke detail_bayar_angsuran (ADMIN - TUNAI)
     * ✅ PERBAIKAN: JANGAN auto-update status_lunas pinjaman
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

            // ✅ PERBAIKAN: Pembayaran tunai ADMIN langsung approved
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
                // ✅ TUNAI ADMIN = LANGSUNG APPROVED
                'status_verifikasi' => 'approved',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
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

            // ✅ PERBAIKAN: JANGAN auto-update status_lunas pinjaman
            // Biarkan admin klik "Validasi Lunas" secara manual
            // HAPUS KODE INI:
            // $sisaAngsuran = $pinjaman->angsuran()->where('status_bayar', 'Belum')->count();
            // if ($sisaAngsuran === 0) {
            //     $pinjaman->update(['status_lunas' => 'Lunas']);
            // }

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

            // ✅ PERBAIKAN: JANGAN auto-update status pinjaman ke "Belum"
            // Biarkan tetap "Belum" atau "Lunas" sesuai validasi manual
            // HAPUS KODE INI:
            // if ($pinjaman->status_lunas === 'Lunas') {
            //     $pinjaman->update(['status_lunas' => 'Belum']);
            // }

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

            // ✅ PERBAIKAN: JANGAN auto-update status pinjaman
            // HAPUS KODE INI:
            // $pinjaman = $angsuran->pinjaman;
            // $sisaAngsuran = $pinjaman->angsuran()->where('status_bayar', 'Belum')->count();
            // if ($sisaAngsuran === 0) {
            //     $pinjaman->update(['status_lunas' => 'Lunas']);
            // }

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

            // ✅ PERBAIKAN: JANGAN auto-update status pinjaman
            // HAPUS KODE INI:
            // $pinjaman = $angsuran->pinjaman;
            // if ($pinjaman->status_lunas === 'Lunas') {
            //     $pinjaman->update(['status_lunas' => 'Belum']);
            // }

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

            $denda = 0;
            $hariTerlambat = 0;

            if ($angsuran->status_bayar == 'Belum') {
                // ✅ PERBAIKAN
                $today = now()->startOfDay();
                $jatuhTempo = Carbon::parse($angsuran->tanggal_jatuh_tempo)->startOfDay();

                if ($today->gt($jatuhTempo)) {
                    $hariTerlambat = $jatuhTempo->diffInDays($today);
                    $dendaPerHari = 5000;
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
                    'is_terlambat' => $today->gt($jatuhTempo),
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
            'pinjaman.anggota.user',
            'pinjaman.lamaAngsuran',
            'angsuran',
            'kas',
            'user'
        ])->findOrFail($id);

        // ✅ PERBAIKAN: Set foto untuk cetak nota
        $pinjaman = $detailBayar->pinjaman;
        if ($pinjaman && $pinjaman->anggota) {
            $photoPath = 'assets/images/profile/user-1.jpg';

            // Priority 1: data_anggota.photo (bukan default)
            if ($pinjaman->anggota->photo && $pinjaman->anggota->photo !== 'assets/images/profile/user-1.jpg') {
                $photoPath = 'storage/' . $pinjaman->anggota->photo;
            }
            // Priority 2: users.profile_image
            elseif ($pinjaman->anggota->user && $pinjaman->anggota->user->profile_image) {
                $photoPath = 'storage/' . $pinjaman->anggota->user->profile_image;
            }

            $pinjaman->anggota->foto_display = $photoPath;
        }

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

    public function pendingVerification()
    {
        $pending_payments = DetailBayarAngsuran::with([
            'pinjaman.anggota.user',
            'angsuran',
            'user',
            'kas'
        ])
            ->pendingVerification()
            ->orderBy('created_at', 'desc')
            ->get();

        // Set foto untuk setiap payment
        foreach ($pending_payments as $payment) {
            if ($payment->pinjaman && $payment->pinjaman->anggota) {
                $photoPath = 'assets/images/profile/user-1.jpg';

                if ($payment->pinjaman->anggota->photo && $payment->pinjaman->anggota->photo !== 'assets/images/profile/user-1.jpg') {
                    $photoPath = 'storage/' . $payment->pinjaman->anggota->photo;
                } elseif ($payment->pinjaman->anggota->user && $payment->pinjaman->anggota->user->profile_image) {
                    $photoPath = 'storage/' . $payment->pinjaman->anggota->user->profile_image;
                }

                $payment->pinjaman->anggota->photo_display = $photoPath;
            }
        }

        return view('admin.Pinjaman.BayarAngsuran.PendingVerification', compact('pending_payments'));
    }

    /**
     * Approve pembayaran transfer dari user
     * ✅ PERBAIKAN: JANGAN auto-update status_lunas pinjaman
     */
    public function approveTransfer(Request $request, $id)
    {
        $detailBayar = DetailBayarAngsuran::with(['angsuran', 'pinjaman'])
            ->findOrFail($id);

        if ($detailBayar->status_verifikasi !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran ini sudah diverifikasi sebelumnya'
            ], 400);
        }

        $validated = $request->validate([
            'denda' => 'nullable|numeric|min:0',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $denda = $validated['denda'] ?? 0;
            $total_bayar = $detailBayar->jumlah_bayar + $denda;

            // 1. Update detail pembayaran
            $detailBayar->update([
                'denda' => $denda,
                'total_bayar' => $total_bayar,
                'status_verifikasi' => 'approved',
                'catatan_verifikasi' => $validated['catatan'] ?? 'Pembayaran diverifikasi dan disetujui',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            // 2. Update jadwal angsuran (bayar_angsuran)
            $jadwal = $detailBayar->angsuran;
            if ($jadwal) {
                $jadwal->update([
                    'status_bayar' => 'Lunas',
                    'tanggal_bayar' => $detailBayar->tanggal_bayar,
                    'jumlah_bayar' => $total_bayar,
                    'denda' => $denda,
                    'ke_kas_id' => $detailBayar->ke_kas_id,
                    'user_id' => Auth::id(),
                ]);
            }

            // ✅ PERBAIKAN: JANGAN auto-update status_lunas pinjaman
            // Biarkan admin klik "Validasi Lunas" secara manual

            // ✅ TAMBAHAN: Log info agar admin tahu perlu validasi
            $sisaAngsuran = BayarAngsuran::where('pinjaman_id', $detailBayar->pinjaman_id)
                ->where('status_bayar', 'Belum')
                ->count();

            Log::info('Admin approve pembayaran transfer', [
                'kode_bayar' => $detailBayar->kode_bayar,
                'admin_id' => Auth::id(),
                'denda' => $denda,
                'total_bayar' => $total_bayar,
                'sisa_angsuran' => $sisaAngsuran,
                'perlu_validasi' => $sisaAngsuran === 0 ? 'YA - Semua angsuran lunas, siap validasi' : 'BELUM'
            ]);

            DB::commit();

            // ✅ Response dengan info tambahan jika semua angsuran lunas
            $message = 'Pembayaran berhasil diverifikasi dan disetujui';
            if ($sisaAngsuran === 0) {
                $message .= '. Semua angsuran sudah dibayar. Klik tombol "Validasi Lunas" untuk memproses pelunasan.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'perlu_validasi' => $sisaAngsuran === 0
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approve pembayaran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat verifikasi pembayaran'
            ], 500);
        }
    }

    /**
     * Reject pembayaran transfer dari user
     */
    public function rejectTransfer(Request $request, $id)
    {
        $detailBayar = DetailBayarAngsuran::findOrFail($id);

        if ($detailBayar->status_verifikasi !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran ini sudah diverifikasi sebelumnya'
            ], 400);
        }

        $validated = $request->validate([
            'catatan' => 'required|string|max:500',
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi'
        ]);

        DB::beginTransaction();
        try {
            // Hapus file bukti transfer
            if ($detailBayar->bukti_transfer) {
                Storage::disk('public')->delete($detailBayar->bukti_transfer);
            }

            $detailBayar->update([
                'status_verifikasi' => 'rejected',
                'catatan_verifikasi' => $validated['catatan'],
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Admin reject pembayaran transfer', [
                'kode_bayar' => $detailBayar->kode_bayar,
                'admin_id' => Auth::id(),
                'alasan' => $validated['catatan']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran ditolak'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error reject pembayaran: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menolak pembayaran'
            ], 500);
        }
    }

    public function pendingDetail($id)
    {
        try {
            $detailBayar = DetailBayarAngsuran::with([
                'pinjaman.anggota',
                'angsuran',
                'kas'
            ])->findOrFail($id);

            if ($detailBayar->status_verifikasi !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran ini sudah diverifikasi'
                ], 400);
            }

            // Hitung denda otomatis
            $dendaOtomatis = 0;
            $jadwal = $detailBayar->angsuran;

            if ($jadwal) {
                $today = now()->startOfDay();
                $jatuhTempo = Carbon::parse($jadwal->tanggal_jatuh_tempo)->startOfDay();

                if ($today->gt($jatuhTempo)) {
                    $hariTerlambat = $jatuhTempo->diffInDays($today);
                    $dendaPerHari = 5000;
                    $dendaOtomatis = $hariTerlambat * $dendaPerHari;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $detailBayar->id,
                    'kode_bayar' => $detailBayar->kode_bayar,
                    'anggota_nama' => $detailBayar->pinjaman->anggota->nama ?? '-',
                    'angsuran_ke' => $detailBayar->angsuran_ke,
                    'tanggal_bayar_formatted' => $detailBayar->tanggal_bayar->format('d M Y H:i'),
                    'jumlah_angsuran' => $jadwal ? $jadwal->jumlah_angsuran : 0,
                    'jumlah_angsuran_formatted' => number_format($jadwal->jumlah_angsuran ?? 0, 0, ',', '.'),
                    'jumlah_bayar' => $detailBayar->jumlah_bayar,
                    'jumlah_bayar_formatted' => number_format($detailBayar->jumlah_bayar ?? 0, 0, ',', '.'),
                    'bank_nama' => $detailBayar->kas->nama_kas ?? '-',
                    'bukti_url' => $detailBayar->bukti_transfer ? asset('storage/' . $detailBayar->bukti_transfer) : null,
                    'keterangan' => $detailBayar->keterangan,
                    'denda_otomatis' => $dendaOtomatis
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error get pending detail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembayaran'
            ], 500);
        }
    }

    /**
     * Get detail pembayaran dengan bukti transfer (untuk view saja)
     */
    public function getBuktiTransfer($id)
    {
        try {
            $detailBayar = DetailBayarAngsuran::with([
                'pinjaman.anggota',
                'angsuran',
                'kas',
                'verifiedBy'
            ])->findOrFail($id);

            // Validasi bahwa pembayaran ini adalah transfer
            if (!$detailBayar->bukti_transfer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembayaran ini bukan pembayaran transfer'
                ], 400);
            }

            // Status verifikasi
            $statusText = match ($detailBayar->status_verifikasi) {
                'approved' => 'Disetujui',
                'pending' => 'Menunggu Verifikasi',
                'rejected' => 'Ditolak',
                default => 'Unknown'
            };

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $detailBayar->id,
                    'kode_bayar' => $detailBayar->kode_bayar,
                    'anggota_nama' => $detailBayar->pinjaman->anggota->nama ?? '-',
                    'angsuran_ke' => $detailBayar->angsuran_ke,
                    'tanggal_bayar_formatted' => $detailBayar->tanggal_bayar->format('d M Y H:i'),
                    'jumlah_bayar' => $detailBayar->jumlah_bayar,
                    'jumlah_bayar_formatted' => number_format($detailBayar->jumlah_bayar, 0, ',', '.'),
                    'denda' => $detailBayar->denda ?? 0,
                    'denda_formatted' => number_format($detailBayar->denda ?? 0, 0, ',', '.'),
                    'total_bayar' => $detailBayar->total_bayar,
                    'total_bayar_formatted' => number_format($detailBayar->total_bayar, 0, ',', '.'),
                    'bank_nama' => $detailBayar->kas->nama_kas ?? '-',
                    'bukti_url' => $detailBayar->bukti_transfer ? asset('storage/' . $detailBayar->bukti_transfer) : null,
                    'keterangan' => $detailBayar->keterangan,
                    'status_verifikasi' => $detailBayar->status_verifikasi,
                    'status_verifikasi_text' => $statusText,
                    'verified_by' => $detailBayar->verifiedBy->name ?? null,
                    'verified_at_formatted' => $detailBayar->verified_at ?
                        Carbon::parse($detailBayar->verified_at)->format('d M Y H:i') : null,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error get bukti transfer: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pembayaran'
            ], 500);
        }
    }

}