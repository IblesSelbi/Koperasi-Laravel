<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasPinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data kas pinjaman
        $kasPinjaman = collect([
            (object)[
                'no' => 1,
                'keterangan' => 'Pokok Pinjaman',
                'jumlah' => 27300000,
            ],
            (object)[
                'no' => 2,
                'keterangan' => 'Tagihan Pinjaman',
                'jumlah' => 40698600,
            ],
            (object)[
                'no' => 3,
                'keterangan' => 'Tagihan Denda',
                'jumlah' => 0,
            ],
            (object)[
                'no' => 4,
                'keterangan' => 'Tagihan Sudah Dibayar',
                'jumlah' => 20812400,
            ],
            (object)[
                'no' => 5,
                'keterangan' => 'Sisa Tagihan',
                'jumlah' => 19886200,
            ],
        ]);

        // Summary statistics
        $summary = (object)[
            'jumlah_peminjam' => 9,
            'peminjam_lunas' => 3,
            'belum_lunas' => 6,
        ];

        // Calculate totals
        $jumlahTagihanDenda = $kasPinjaman->where('no', 2)->first()->jumlah + 
                              $kasPinjaman->where('no', 3)->first()->jumlah;

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Default date range
        $tglDari = $request->get('tgl_dari', '2025-01-01');
        $tglSamp = $request->get('tgl_samp', '2025-12-31');

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
        $tglDari = $request->get('tgl_dari', '');
        $tglSamp = $request->get('tgl_samp', '');

        // TODO: Generate print view with filtered data
        
        return response('Cetak laporan kas pinjaman');
    }
}