<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SHUController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default date range
        $tglDari = $request->get('tgl_dari', '2025-01-01');
        $tglSamp = $request->get('tgl_samp', '2025-12-31');
        $anggotaId = $request->get('anggota_id', '');

        // Dummy data anggota
        $anggotaList = collect([
            (object)[
                'id' => 1,
                'id_anggota' => 'member1',
                'nama' => 'FAISAL',
            ],
            (object)[
                'id' => 2,
                'id_anggota' => 'member2',
                'nama' => 'GEO HALOMOAN SIMANJUNTAK',
            ],
            (object)[
                'id' => 3,
                'id_anggota' => 'anggota',
                'nama' => 'WIDI ALJATSIYAH',
            ],
        ]);

        // Dummy SHU data
        $shuSebelumPajak = -6487600;
        $pajakPPh = -324380; // 5%
        $shuSetelahPajak = -6163220;

        // Pembagian SHU untuk Dana-dana
        $danaCadangan = -2465288; // 40%
        $jasaAnggota = -2465288; // 40%
        $danaPengurus = -308161; // 5%
        $danaKaryawan = -308161; // 5%
        $danaPendidikan = -308161; // 5%
        $danaSosial = -308161; // 5%

        // Pembagian SHU Anggota
        $jasaUsaha = -1725702; // 70%
        $jasaModal = -739586; // 30%
        $totalPendapatanAnggota = -6487600;
        $totalSimpananAnggota = 100000;

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.shu.SHU', compact(
            'anggotaList',
            'shuSebelumPajak',
            'pajakPPh',
            'shuSetelahPajak',
            'danaCadangan',
            'jasaAnggota',
            'danaPengurus',
            'danaKaryawan',
            'danaPendidikan',
            'danaSosial',
            'jasaUsaha',
            'jasaModal',
            'totalPendapatanAnggota',
            'totalSimpananAnggota',
            'notifications',
            'tglDari',
            'tglSamp',
            'anggotaId'
        ));
    }

    /**
     * Print laporan SHU
     */
    public function cetakLaporan(Request $request)
    {
        $anggotaId = $request->get('anggota_id', '');
        $tglDari = $request->get('tgl_dari', '');
        $tglSamp = $request->get('tgl_samp', '');
        $jsModal = $request->get('js_modal', '');
        $jsUsaha = $request->get('js_usaha', '');
        $totPendapatan = $request->get('tot_pendpatan', '');
        $totSimpanan = $request->get('tot_simpanan', '');

        // TODO: Generate print view with filtered data
        
        return response('Cetak laporan SHU');
    }
}