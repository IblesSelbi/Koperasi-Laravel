<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\PengajuanPinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get notifikasi pengajuan baru (status pending = 0)
     */
    public function getPengajuanBaru()
    {
        try {
            Log::info('🔔 Loading Pengajuan Notifications');

            $pengajuan = PengajuanPinjaman::with(['anggota'])
                ->where('status', 0) // Pending
                ->whereNull('deleted_at')
                ->orderBy('tanggal_pengajuan', 'desc')
                ->limit(10)
                ->get();

            Log::info('📊 Pengajuan Count: ' . $pengajuan->count());

            $data = $pengajuan->map(function ($item) {
                return [
                    'id' => $item->id,
                    'id_ajuan' => $item->id_ajuan,
                    'nama' => $item->anggota->nama,
                    'jenis' => $item->jenis_pinjaman,
                    'jumlah' => $item->jumlah,
                    'tanggal' => $item->tanggal_pengajuan->diffForHumans(),
                    'tanggal_full' => $item->tanggal_pengajuan->translatedFormat('d F Y H:i'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => $data->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error getPengajuanBaru: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get notifikasi jatuh tempo (7 hari ke depan + yang sudah lewat)
     */
    public function getJatuhTempo()
    {
        try {
            Log::info('🔔 Loading Jatuh Tempo Notifications');

            $today = Carbon::now();
            $sevenDaysLater = $today->copy()->addDays(7);

            $angsuran = BayarAngsuran::with(['pinjaman.anggota'])
                ->where('status_bayar', 'Belum')
                ->whereNull('deleted_at')
                ->where('tanggal_jatuh_tempo', '<=', $sevenDaysLater)
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->limit(10)
                ->get();

            Log::info('📊 Jatuh Tempo Count: ' . $angsuran->count());

            $data = $angsuran->map(function ($item) use ($today) {
                $jatuhTempo = Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
                $todayStart = $today->copy()->startOfDay();

                // Hitung selisih hari dan bulatkan
                $selisihHari = (int) $todayStart->diffInDays($jatuhTempo, false);

                // Tentukan status dan styling
                if ($selisihHari < 0) {
                    // Sudah lewat jatuh tempo (terlambat)
                    $status = 'terlambat';
                    $badge = 'danger';
                    $icon = 'ti-alert-circle';
                    $keterangan = 'Terlambat ' . abs($selisihHari) . ' hari';
                } elseif ($selisihHari == 0) {
                    // Jatuh tempo hari ini
                    $status = 'hari_ini';
                    $badge = 'danger';
                    $icon = 'ti-clock-exclamation';
                    $keterangan = 'Jatuh tempo hari ini';
                } elseif ($selisihHari <= 3) {
                    // Mendekati jatuh tempo (1-3 hari lagi)
                    $status = 'mendekati';
                    $badge = 'warning';
                    $icon = 'ti-alert-triangle';
                    $keterangan = $selisihHari . ' hari lagi';
                } else {
                    // Masih lama (4-7 hari lagi)
                    $status = 'akan_datang';
                    $badge = 'info';
                    $icon = 'ti-bell';
                    $keterangan = $selisihHari . ' hari lagi';
                }

                return [
                    'id' => $item->id,
                    'pinjaman_id' => $item->pinjaman_id,
                    'kode_pinjaman' => $item->pinjaman->kode_pinjaman,
                    'nama' => $item->pinjaman->anggota->nama,
                    'angsuran_ke' => $item->angsuran_ke,
                    'tanggal_jatuh_tempo' => $jatuhTempo->format('d M Y'), // ✅ Ubah jadi format standar
                    'tanggal_jatuh_tempo_full' => $jatuhTempo->translatedFormat('d F Y'), // ✅ Tambah versi lengkap
                    'jumlah_angsuran' => $item->jumlah_angsuran,
                    'status' => $status,
                    'badge' => $badge,
                    'icon' => $icon,
                    'keterangan' => $keterangan,
                    'selisih_hari' => $selisihHari,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => $data->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error getJatuhTempo: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }

    public function getPembayaranPending()
    {
        try {
            Log::info('🔔 Loading Pembayaran Pending Notifications');

            $pembayaran = DetailBayarAngsuran::with([
                'pinjaman.anggota',
                'angsuran',
                'kas',
                'user'
            ])
                ->where('status_verifikasi', 'pending')
                ->whereNotNull('bukti_transfer') // Hanya yang transfer
                ->whereNull('deleted_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            Log::info('📊 Pembayaran Pending Count: ' . $pembayaran->count());

            $data = $pembayaran->map(function ($item) {
                $selisihWaktu = Carbon::parse($item->created_at)->diffForHumans();

                return [
                    'id' => $item->id,
                    'kode_bayar' => $item->kode_bayar,
                    'pinjaman_id' => $item->pinjaman_id,
                    'kode_pinjaman' => $item->pinjaman->kode_pinjaman,
                    'nama_anggota' => $item->pinjaman->anggota->nama,
                    'angsuran_ke' => $item->angsuran_ke,
                    'jumlah_bayar' => $item->jumlah_bayar,
                    'denda' => $item->denda ?? 0,
                    'total_bayar' => $item->total_bayar,
                    'bank_nama' => $item->kas->nama_kas ?? '-',
                    'waktu' => $selisihWaktu,
                    'waktu_full' => $item->created_at->translatedFormat('d F Y H:i'),
                    'bukti_url' => $item->bukti_transfer ? asset('storage/' . $item->bukti_transfer) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => $data->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error getPembayaranPending: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get total count semua notifikasi
     */
    public function getNotificationCount()
    {
        try {
            $pengajuanCount = PengajuanPinjaman::where('status', 0)
                ->whereNull('deleted_at')
                ->count();

            $jatuhTempoCount = BayarAngsuran::where('status_bayar', 'Belum')
                ->whereNull('deleted_at')
                ->where('tanggal_jatuh_tempo', '<=', Carbon::now()->addDays(7))
                ->count();

            // ✅ TAMBAH COUNT PEMBAYARAN PENDING
            $pembayaranPendingCount = DetailBayarAngsuran::where('status_verifikasi', 'pending')
                ->whereNotNull('bukti_transfer')
                ->whereNull('deleted_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'pengajuan_baru' => $pengajuanCount,
                    'jatuh_tempo' => $jatuhTempoCount,
                    'pembayaran_pending' => $pembayaranPendingCount, // ✅ TAMBAH INI
                    'total' => $pengajuanCount + $jatuhTempoCount + $pembayaranPendingCount // ✅ UPDATE TOTAL
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Error getNotificationCount: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'pengajuan_baru' => 0,
                    'jatuh_tempo' => 0,
                    'pembayaran_pending' => 0,
                    'total' => 0
                ]
            ], 500);
        }
    }
}