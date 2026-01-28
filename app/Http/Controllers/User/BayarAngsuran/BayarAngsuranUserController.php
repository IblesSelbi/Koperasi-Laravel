<?php

namespace App\Http\Controllers\User\BayarAngsuran;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BayarAngsuranUserController extends Controller
{
    /**
     * Get anggota by authenticated user
     */
    private function getAnggota()
    {
        $user = Auth::user();
        $user->load(['anggota.user']);
        return $user->anggota;
    }

    /**
     * Tampilkan daftar pinjaman yang perlu dibayar
     */
    public function index()
    {
        $anggota = $this->getAnggota();

        if (!$anggota) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Data anggota tidak ditemukan!');
        }

        $pinjaman = Pinjaman::with(['anggota.user', 'lamaAngsuran'])
            ->where('anggota_id', $anggota->id)
            ->where('status_lunas', 'Belum')
            ->whereNull('deleted_at')
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();

        foreach ($pinjaman as $item) {
            $photoPath = 'assets/images/profile/user-1.jpg';

            if ($item->anggota) {
                if ($item->anggota->photo && $item->anggota->photo !== 'assets/images/profile/user-1.jpg') {
                    $photoPath = 'storage/' . $item->anggota->photo;
                } elseif ($item->anggota->user && $item->anggota->user->profile_image) {
                    $photoPath = 'storage/' . $item->anggota->user->profile_image;
                }
            }

            $item->anggota->photo_display = $photoPath;
            $item->angsuran_per_bulan = $item->angsuran_per_bulan ?? 0;

            // ✅ LOAD JADWAL DULU (1x query)
            $item->jadwal_angsuran = BayarAngsuran::where('pinjaman_id', $item->id)
                ->orderBy('angsuran_ke', 'asc')
                ->get();

            // ✅ Hitung dari collection (tidak query lagi)
            $angsuranLunas = $item->jadwal_angsuran->where('status_bayar', 'Lunas')->count();
            $totalAngsuran = $item->jadwal_angsuran->count();

            $item->angsuran_terakhir = $item->jadwal_angsuran->where('status_bayar', 'Lunas')->max('angsuran_ke') ?? 0;

            // Jika semua lunas, tetap tampilkan max angsuran (bukan +1)
            if ($angsuranLunas >= $totalAngsuran) {
                $item->angsuran_berikutnya = $totalAngsuran;
            } else {
                $item->angsuran_berikutnya = $item->angsuran_terakhir + 1;
            }

            // ✅ Hitung total POKOK yang sudah dibayar (exclude denda)
            $riwayat = DetailBayarAngsuran::where('pinjaman_id', $item->id)
                ->where('status_verifikasi', 'approved')
                ->selectRaw('SUM(jumlah_bayar) as total_pokok, SUM(denda) as total_denda, SUM(total_bayar) as grand_total')
                ->first();

            $item->total_dibayar_pokok = $riwayat->total_pokok ?? 0;
            $item->total_denda = $riwayat->total_denda ?? 0;
            $item->total_dibayar = $riwayat->grand_total ?? 0; // Include denda

            // ✅ Sisa pinjaman = Total angsuran pokok - Total pokok dibayar
            $item->sisa_pinjaman_calculated = $item->jumlah_angsuran - $item->total_dibayar_pokok;

            // ✅ Progress berdasarkan POKOK (bukan total dengan denda)
            if ($item->jumlah_angsuran > 0) {
                $progressRaw = ($item->total_dibayar_pokok / $item->jumlah_angsuran) * 100;
                $item->progress_percentage = min($progressRaw, 100);
            } else {
                $item->progress_percentage = 0;
            }

            // ✅ Cek pending
            $item->has_pending_payment = DetailBayarAngsuran::where('pinjaman_id', $item->id)
                ->where('status_verifikasi', 'pending')
                ->exists();

            // ✅ Riwayat pembayaran
            $item->riwayat_bayar = DetailBayarAngsuran::with(['user', 'verifiedBy', 'kas'])
                ->where('pinjaman_id', $item->id)
                ->orderBy('tanggal_bayar', 'desc')
                ->get();
        }

        $has_pending = DetailBayarAngsuran::whereHas('pinjaman', function ($q) use ($anggota) {
            $q->where('anggota_id', $anggota->id)
                ->where('status_lunas', 'Belum')
                ->whereNull('deleted_at');
        })
            ->where('status_verifikasi', 'pending')
            ->exists();

        $is_lunas = false;
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('angsuran', 'Y')
            ->where('transfer_kas', 'Y')
            ->orderBy('nama_kas', 'asc')
            ->get();

        return view('user.BayarAngsuran.BayarAngsuranUser', compact('pinjaman', 'has_pending', 'is_lunas', 'kas_list'));
    }

    /**
     * Tampilkan detail pinjaman dan form pembayaran
     */
    public function show($id)
    {
        $anggota = $this->getAnggota();

        if (!$anggota) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Data anggota tidak ditemukan!');
        }

        $dataPinjaman = Pinjaman::with(['anggota.user', 'lamaAngsuran'])
            ->where('id', $id)
            ->where('anggota_id', $anggota->id)
            ->where('status_lunas', 'Belum')
            ->whereNull('deleted_at')
            ->firstOrFail();

        // Set foto display
        $photoPath = 'assets/images/profile/user-1.jpg';
        if ($dataPinjaman->anggota) {
            if ($dataPinjaman->anggota->photo && $dataPinjaman->anggota->photo !== 'assets/images/profile/user-1.jpg') {
                $photoPath = 'storage/' . $dataPinjaman->anggota->photo;
            } elseif ($dataPinjaman->anggota->user && $dataPinjaman->anggota->user->profile_image) {
                $photoPath = 'storage/' . $dataPinjaman->anggota->user->profile_image;
            }
        }
        $dataPinjaman->anggota->photo_display = $photoPath;

        // ✅ Ambil jadwal angsuran dengan DENDA OTOMATIS
        $jadwal_angsuran = BayarAngsuran::where('pinjaman_id', $dataPinjaman->id)
            ->orderBy('angsuran_ke', 'asc')
            ->get()
            ->map(function ($jadwal) {
                // ✅ Hitung denda otomatis untuk setiap jadwal
                if ($jadwal->status_bayar === 'Belum') {
                    $today = now()->startOfDay();
                    $jatuhTempo = Carbon::parse($jadwal->tanggal_jatuh_tempo)->startOfDay();

                    if ($today->gt($jatuhTempo)) {
                        $hariTerlambat = $jatuhTempo->diffInDays($today);
                        $dendaPerHari = 5000;
                        $jadwal->denda_otomatis = $hariTerlambat * $dendaPerHari;
                        $jadwal->hari_terlambat = $hariTerlambat;
                        $jadwal->total_tagihan = $jadwal->jumlah_angsuran + $jadwal->denda_otomatis;
                        $jadwal->is_terlambat = true;
                    } else {
                        $jadwal->denda_otomatis = 0;
                        $jadwal->hari_terlambat = 0;
                        $jadwal->total_tagihan = $jadwal->jumlah_angsuran;
                        $jadwal->is_terlambat = false;
                    }
                } else {
                    // Sudah lunas, ambil denda aktual yang dibayar
                    $pembayaran = DetailBayarAngsuran::where('bayar_angsuran_id', $jadwal->id)
                        ->where('status_verifikasi', 'approved')
                        ->first();

                    $jadwal->denda_otomatis = $pembayaran->denda ?? 0;
                    $jadwal->hari_terlambat = 0;
                    $jadwal->total_tagihan = $pembayaran->total_bayar ?? $jadwal->jumlah_angsuran;
                    $jadwal->is_terlambat = $jadwal->denda_otomatis > 0;
                }

                return $jadwal;
            });

        // ✅ VALIDASI: Pastikan ada jadwal angsuran
        if ($jadwal_angsuran->isEmpty()) {
            return redirect()->route('user.bayar.index')
                ->with('error', 'Pinjaman ini belum memiliki jadwal angsuran. Silakan hubungi admin.');
        }

        // Ambil riwayat pembayaran (detail_bayar_angsuran)
        $riwayat_bayar = DetailBayarAngsuran::with(['user', 'verifiedBy', 'kas'])
            ->where('pinjaman_id', $dataPinjaman->id)
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        // ✅ Hitung POKOK dan DENDA terpisah
        $summary = $riwayat_bayar->where('status_verifikasi', 'approved')->reduce(function ($carry, $item) {
            $carry['total_pokok'] += $item->jumlah_bayar;
            $carry['total_denda'] += $item->denda;
            $carry['total_dibayar'] += $item->total_bayar;
            return $carry;
        }, ['total_pokok' => 0, 'total_denda' => 0, 'total_dibayar' => 0]);

        $total_dibayar_pokok = $summary['total_pokok'];
        $total_denda_dibayar = $summary['total_denda'];
        $total_dibayar = $summary['total_dibayar'];

        // ✅ Sisa pinjaman = Pokok total - Pokok dibayar (EXCLUDE denda!)
        $sisa_pinjaman = $dataPinjaman->jumlah_angsuran - $total_dibayar_pokok;

        $angsuranLunas = $jadwal_angsuran->where('status_bayar', 'Lunas')->count();
        $totalAngsuran = $jadwal_angsuran->count();

        $angsuran_terakhir = $jadwal_angsuran->where('status_bayar', 'Lunas')->max('angsuran_ke') ?? 0;

        // Jika semua lunas, tetap tampilkan max angsuran (bukan +1)
        if ($angsuranLunas >= $totalAngsuran) {
            $angsuran_berikutnya = $totalAngsuran;
        } else {
            $angsuran_berikutnya = $angsuran_terakhir + 1;
        }

        $angsuran_per_bulan = $dataPinjaman->angsuran_per_bulan ?? 0;

        // Cari jadwal angsuran berikutnya yang belum lunas
        $jadwal_berikutnya = $jadwal_angsuran->where('status_bayar', 'Belum')->first();

        // Cek pending untuk SELURUH PINJAMAN
        $has_pending = DetailBayarAngsuran::where('pinjaman_id', $dataPinjaman->id)
            ->where('status_verifikasi', 'pending')
            ->exists();

        // ✅ Hitung total jadwal dan yang sudah lunas
        $total_jadwal = $jadwal_angsuran->count();
        $jadwal_lunas_count = $jadwal_angsuran->where('status_bayar', 'Lunas')->count();

        // ✅ Pinjaman dianggap lunas HANYA jika:
        $is_lunas = (
            $sisa_pinjaman <= 0 &&              // 1. Pokok sudah dibayar semua
            !$has_pending &&                     // 2. TIDAK ada pending payment
            $jadwal_lunas_count >= $total_jadwal // 3. SEMUA jadwal sudah "Lunas"
        );

        // Ambil list kas yang aktif untuk transfer
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('angsuran', 'Y')
            ->where('transfer_kas', 'Y')
            ->orderBy('nama_kas', 'asc')
            ->get();

        // Assign ke variable pinjaman untuk view
        $pinjaman = $dataPinjaman;

        // ✅ DEBUG LOG dengan breakdown denda
        Log::info('=== DEBUG USER BAYAR (WITH DENDA) ===', [
            'pinjaman_id' => $pinjaman->id,
            'kode_pinjaman' => $pinjaman->kode_pinjaman,
            'user_id' => Auth::id(),
            'jumlah_angsuran_total' => $pinjaman->jumlah_angsuran,
            'total_dibayar_pokok' => $total_dibayar_pokok,
            'total_denda_dibayar' => $total_denda_dibayar,
            'total_dibayar' => $total_dibayar,
            'sisa_pinjaman' => $sisa_pinjaman,
            'has_pending' => $has_pending,
            'is_lunas' => $is_lunas,
            'total_jadwal' => $total_jadwal,
            'jadwal_lunas_count' => $jadwal_lunas_count,
        ]);

        return view('user.BayarAngsuran.DetailBayarAngsuranUser', compact(
            'pinjaman',
            'jadwal_angsuran',
            'riwayat_bayar',
            'total_dibayar',
            'total_dibayar_pokok',
            'total_denda_dibayar',
            'sisa_pinjaman',
            'angsuran_terakhir',
            'angsuran_berikutnya',
            'angsuran_per_bulan',
            'jadwal_berikutnya',
            'has_pending',
            'is_lunas',
            'kas_list'
        ));
    }

    /**
     * Proses pembayaran angsuran via transfer
     * ✅ DENGAN DENDA
     */
    public function bayar(Request $request)
    {
        Log::debug('=== STEP 1: RAW INPUT ===', [
            'jumlah_bayar' => $request->input('jumlah_bayar'),
            'denda_terlambat' => $request->input('denda_terlambat'),
            'ke_kas_id' => $request->input('ke_kas_id'),
            'pinjaman_id' => $request->input('pinjaman_id'),
        ]);

        $validated = $request->validate([
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'bayar_angsuran_id' => 'required|exists:bayar_angsuran,id',
            'ke_kas_id' => 'required|exists:data_kas,id',
            'jumlah_bayar' => 'required|string',
            'denda_terlambat' => 'nullable|numeric|min:0', // ✅ ACCEPT DENDA
            'keterangan' => 'nullable|string|max:500',
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'pinjaman_id.required' => 'Data pinjaman tidak valid',
            'bayar_angsuran_id.required' => 'Jadwal angsuran tidak valid',
            'ke_kas_id.required' => 'Pilih metode pembayaran',
            'jumlah_bayar.required' => 'Jumlah bayar harus diisi',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'bukti_transfer.required' => 'Bukti transfer wajib diupload',
            'bukti_transfer.image' => 'File harus berupa gambar',
            'bukti_transfer.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'bukti_transfer.max' => 'Ukuran file maksimal 2MB',
        ]);

        $anggota = $this->getAnggota();

        if (!$anggota) {
            return response()->json([
                'success' => false,
                'message' => 'Data anggota tidak ditemukan'
            ], 404);
        }

        // Cek pinjaman
        $pinjaman = Pinjaman::where('id', $validated['pinjaman_id'])
            ->where('anggota_id', $anggota->id)
            ->where('status_lunas', 'Belum')
            ->whereNull('deleted_at')
            ->first();

        if (!$pinjaman) {
            return response()->json([
                'success' => false,
                'message' => 'Data pinjaman tidak ditemukan'
            ], 404);
        }

        // Cek jadwal angsuran
        $jadwal = BayarAngsuran::where('id', $validated['bayar_angsuran_id'])
            ->where('pinjaman_id', $pinjaman->id)
            ->where('status_bayar', 'Belum')
            ->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'message' => 'Jadwal angsuran tidak valid atau sudah dibayar'
            ], 404);
        }

        // Cek pending
        $hasPending = DetailBayarAngsuran::where('pinjaman_id', $pinjaman->id)
            ->where('status_verifikasi', 'pending')
            ->exists();

        if ($hasPending) {
            return response()->json([
                'success' => false,
                'message' => 'Anda masih memiliki pembayaran yang menunggu verifikasi admin.'
            ], 400);
        }

        // Cek kas
        $kas = DataKas::find($validated['ke_kas_id']);
        if (!$kas || $kas->transfer_kas !== 'Y') {
            return response()->json([
                'success' => false,
                'message' => 'Kas yang dipilih harus bertipe transfer'
            ], 400);
        }

        // ✅ PARSING JUMLAH BAYAR (angsuran pokok)
        $cleanedInput = preg_replace('/\D/', '', $validated['jumlah_bayar']);
        $jumlah = (int) $cleanedInput;

        // ✅ DENDA dari form
        $denda = (int) ($validated['denda_terlambat'] ?? 0);

        // ✅ TOTAL = ANGSURAN + DENDA
        $total_bayar = $jumlah + $denda;

        Log::debug('=== STEP 2: PARSING WITH DENDA ===', [
            'input_string' => $validated['jumlah_bayar'],
            'jumlah_bayar' => $jumlah,
            'denda' => $denda,
            'total_bayar' => $total_bayar,
        ]);

        if ($jumlah < 1000) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran minimal Rp 1.000'
            ], 400);
        }

        if ($jumlah > $jadwal->jumlah_angsuran) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran melebihi angsuran (Rp ' . number_format((float) $jadwal->jumlah_angsuran, 0, ',', '.') . ')'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Upload bukti transfer
            $buktiPath = null;
            if ($request->hasFile('bukti_transfer')) {
                $file = $request->file('bukti_transfer');
                $fileName = 'bukti_transfer_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $buktiPath = $file->storeAs('bukti_transfer', $fileName, 'public');
            }

            // ✅ CREATE DENGAN DENDA
            $dataToCreate = [
                'bayar_angsuran_id' => $jadwal->id,
                'pinjaman_id' => $pinjaman->id,
                'angsuran_ke' => $jadwal->angsuran_ke,
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $jumlah,        // Angsuran pokok
                'denda' => $denda,                 // Denda keterlambatan
                'total_bayar' => $total_bayar,     // Total (angsuran + denda)
                'ke_kas_id' => $validated['ke_kas_id'],
                'bukti_transfer' => $buktiPath,
                'status_verifikasi' => 'pending',
                'keterangan' => $validated['keterangan'] ?? 'Pembayaran via transfer dari user',
                'user_id' => Auth::id(),
            ];

            Log::debug('=== STEP 3: DATA TO CREATE ===', $dataToCreate);

            // Create record
            $detailBayar = DetailBayarAngsuran::create($dataToCreate);

            Log::debug('=== STEP 4: AFTER SAVE ===', [
                'id' => $detailBayar->id,
                'kode_bayar' => $detailBayar->kode_bayar,
                'jumlah_bayar_saved' => $detailBayar->jumlah_bayar,
                'denda_saved' => $detailBayar->denda,
                'total_bayar_saved' => $detailBayar->total_bayar,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikirim! Menunggu verifikasi admin.',
                'kode_bayar' => $detailBayar->kode_bayar
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($buktiPath) && $buktiPath) {
                Storage::disk('public')->delete($buktiPath);
            }

            Log::error('=== ERROR BAYAR ===', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran'
            ], 500);
        }
    }

    /**
     * Riwayat pembayaran user
     */
    public function riwayat()
    {
        $anggota = $this->getAnggota();

        if (!$anggota) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Data anggota tidak ditemukan!');
        }

        $riwayat = DetailBayarAngsuran::with(['pinjaman.lamaAngsuran', 'kas', 'verifiedBy', 'angsuran'])
            ->whereHas('pinjaman', function ($q) use ($anggota) {
                $q->where('anggota_id', $anggota->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.BayarAngsuran.RiwayatBayar', compact('riwayat'));
    }
}