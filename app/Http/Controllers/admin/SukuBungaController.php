<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SukuBungaController extends Controller
{
    /**
     * Display the form.
     */
    public function index()
    {
        // Dummy data suku bunga dan administrasi
        $sukuBunga = (object)[
            'pinjaman_bunga_tipe' => 'B',
            'bg_pinjam' => 5,
            'biaya_adm' => 0,
            'denda' => 0,
            'denda_hari' => 15,
            'dana_cadangan' => 40,
            'jasa_usaha' => 70,
            'jasa_anggota' => 40,
            'jasa_modal' => 30,
            'dana_pengurus' => 5,
            'dana_karyawan' => 5,
            'dana_pend' => 5,
            'dana_sosial' => 5,
            'pjk_pph' => 5,
        ];

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Setting.SukuBunga.SukuBunga', compact('sukuBunga', 'notifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // TODO: Implement update logic
        return response()->json(['success' => true, 'message' => 'Setting biaya dan administrasi berhasil diupdate']);
    }
}