<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LabaRugiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default date range
        $tglDari = $request->get('tgl_dari', '2025-01-01');
        $tglSamp = $request->get('tgl_samp', '2025-12-31');

        // Dummy data estimasi pinjaman
        $estimasiPinjaman = collect([
            (object)[
                'no' => 1,
                'keterangan' => 'Jumlah Pinjaman',
                'jumlah' => 27300000,
            ],
            (object)[
                'no' => 2,
                'keterangan' => 'Pendapatan Biaya Administrasi',
                'jumlah' => 63000,
            ],
            (object)[
                'no' => 3,
                'keterangan' => 'Pendapatan Biaya Bunga',
                'jumlah' => 13335000,
            ],
            (object)[
                'no' => 4,
                'keterangan' => 'Pendapatan Biaya Pembulatan',
                'jumlah' => 600,
            ],
        ]);

        // Dummy data pendapatan
        $pendapatan = collect([
            (object)[
                'no' => 1,
                'keterangan' => 'Pendapatan Pinjaman',
                'jumlah' => -6487600,
            ],
            (object)[
                'no' => 2,
                'keterangan' => 'Pendapatan Lainnya',
                'jumlah' => 0,
            ],
        ]);

        // Dummy data biaya
        $biaya = collect([
            (object)[
                'no' => 1,
                'keterangan' => 'transaksi',
                'jumlah' => 0,
            ],
            (object)[
                'no' => 2,
                'keterangan' => 'Beban Gaji Karyawan',
                'jumlah' => 0,
            ],
            (object)[
                'no' => 3,
                'keterangan' => 'Biaya Listrik dan Air',
                'jumlah' => 0,
            ],
            (object)[
                'no' => 4,
                'keterangan' => 'Biaya Transportasi',
                'jumlah' => 0,
            ],
            (object)[
                'no' => 5,
                'keterangan' => 'Biaya Lainnya',
                'jumlah' => 0,
            ],
        ]);

        // Calculate totals
        $jumlahTagihan = 40698600;
        $estimasiPendapatanPinjaman = 13398600;
        $jumlahPendapatan = $pendapatan->sum('jumlah');
        $jumlahBiaya = $biaya->sum('jumlah');
        $labaRugi = $jumlahPendapatan - $jumlahBiaya;

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.LabaRugi.LabaRugi', compact(
            'estimasiPinjaman',
            'pendapatan',
            'biaya',
            'jumlahTagihan',
            'estimasiPendapatanPinjaman',
            'jumlahPendapatan',
            'jumlahBiaya',
            'labaRugi',
            'notifications',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Print laporan laba rugi
     */
    public function cetakLaporan(Request $request)
    {
        $tglDari = $request->get('tgl_dari', '');
        $tglSamp = $request->get('tgl_samp', '');

        // TODO: Generate print view with filtered data
        
        return response('Cetak laporan laba rugi');
    }
}