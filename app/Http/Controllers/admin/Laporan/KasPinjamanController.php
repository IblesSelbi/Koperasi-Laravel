<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\PinjamanLunas;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KasPinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default date range
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // 1. POKOK PINJAMAN
        $pokokPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        // 2. TAGIHAN PINJAMAN (Pokok + Bunga)
        $tagihanPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('jumlah_angsuran');

        // 3. TAGIHAN DENDA
        $tagihanDenda = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('denda');

        // 4. TAGIHAN SUDAH DIBAYAR (Total pembayaran dalam periode)
        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        // 5. SISA TAGIHAN
        $sisaTagihan = ($tagihanPinjaman + $tagihanDenda) - $sudahDibayar;

        // Build collection untuk view
        $kasPinjaman = collect([
            (object)[
                'no' => 1,
                'keterangan' => 'Pokok Pinjaman',
                'jumlah' => $pokokPinjaman,
            ],
            (object)[
                'no' => 2,
                'keterangan' => 'Tagihan Pinjaman',
                'jumlah' => $tagihanPinjaman,
            ],
            (object)[
                'no' => 3,
                'keterangan' => 'Tagihan Denda',
                'jumlah' => $tagihanDenda,
            ],
            (object)[
                'no' => 4,
                'keterangan' => 'Tagihan Sudah Dibayar',
                'jumlah' => $sudahDibayar,
            ],
            (object)[
                'no' => 5,
                'keterangan' => 'Sisa Tagihan',
                'jumlah' => $sisaTagihan,
            ],
        ]);

        // Summary statistics
        $jumlahPeminjam = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $peminjamLunas = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->where('status_lunas', 'Lunas')
            ->whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $belumLunas = $jumlahPeminjam - $peminjamLunas;

        $summary = (object)[
            'jumlah_peminjam' => $jumlahPeminjam,
            'peminjam_lunas' => $peminjamLunas,
            'belum_lunas' => $belumLunas,
        ];

        // Calculate jumlah tagihan + denda
        $jumlahTagihanDenda = $tagihanPinjaman + $tagihanDenda;

        // Notifications - angsuran yang akan jatuh tempo 7 hari ke depan
        $notifications = BayarAngsuran::with(['pinjaman.anggota'])
            ->where('status_bayar', 'Belum')
            ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)])
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'nama' => $item->pinjaman->anggota->nama ?? 'Unknown',
                    'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo->format('Y-m-d'),
                    'sisa_tagihan' => $item->jumlah_angsuran,
                ];
            });

        return view('admin.Laporan.KasPinjaman.KasPinjaman', compact(
            'kasPinjaman',
            'summary',
            'jumlahTagihanDenda',
            'notifications',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Print laporan kas pinjaman
     */
    public function cetakLaporan(Request $request)
    {
        // Get date range from request
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // 1. POKOK PINJAMAN
        $pokokPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        // 2. TAGIHAN PINJAMAN (Pokok + Bunga)
        $tagihanPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('jumlah_angsuran');

        // 3. TAGIHAN DENDA
        $tagihanDenda = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('denda');

        // 4. TAGIHAN SUDAH DIBAYAR
        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        // 5. SISA TAGIHAN
        $sisaTagihan = ($tagihanPinjaman + $tagihanDenda) - $sudahDibayar;

        // Build collection untuk view
        $kasPinjaman = collect([
            (object)[
                'no' => 1,
                'keterangan' => 'Pokok Pinjaman',
                'jumlah' => $pokokPinjaman,
            ],
            (object)[
                'no' => 2,
                'keterangan' => 'Tagihan Pinjaman',
                'jumlah' => $tagihanPinjaman,
            ],
            (object)[
                'no' => 3,
                'keterangan' => 'Tagihan Denda',
                'jumlah' => $tagihanDenda,
            ],
            (object)[
                'no' => 4,
                'keterangan' => 'Tagihan Sudah Dibayar',
                'jumlah' => $sudahDibayar,
            ],
            (object)[
                'no' => 5,
                'keterangan' => 'Sisa Tagihan',
                'jumlah' => $sisaTagihan,
            ],
        ]);

        // Summary statistics
        $jumlahPeminjam = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $peminjamLunas = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->where('status_lunas', 'Lunas')
            ->whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $belumLunas = $jumlahPeminjam - $peminjamLunas;

        $summary = (object)[
            'jumlah_peminjam' => $jumlahPeminjam,
            'peminjam_lunas' => $peminjamLunas,
            'belum_lunas' => $belumLunas,
        ];

        // Calculate jumlah tagihan + denda
        $jumlahTagihanDenda = $tagihanPinjaman + $tagihanDenda;

        // Return view for printing
        return view('admin.Laporan.KasPinjaman.CetakKasPinjaman', compact(
            'kasPinjaman',
            'summary',
            'jumlahTagihanDenda',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Get summary statistics (optional - for API/dashboard)
     */
    public function getSummary(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        $pokokPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        $tagihanPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('jumlah_angsuran');

        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp]);
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        return response()->json([
            'success' => true,
            'data' => [
                'pokok_pinjaman' => $pokokPinjaman,
                'tagihan_pinjaman' => $tagihanPinjaman,
                'sudah_dibayar' => $sudahDibayar,
                'sisa_tagihan' => $tagihanPinjaman - $sudahDibayar,
            ]
        ]);
    }
}