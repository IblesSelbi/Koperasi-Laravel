<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IdentitasKoperasiController extends Controller
{
    /**
     * Display the form.
     */
    public function index()
    {
        // Dummy data identitas koperasi
        $identitas = (object)[
            'nama_lembaga' => 'KOPERASI',
            'nama_ketua' => 'ATNAN',
            'hp_ketua' => '0852',
            'alamat' => 'JL LASWI 2 TONJONG MAJALENGKA',
            'telepon' => '0231-36387985',
            'kota' => 'SULAWESI',
            'email' => 'king@gmail.com',
            'web' => 'www.kingnet.id',
            'logo' => 'assets/images/logos/logo-placeholder.png',
        ];

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Setting.IdentitasKoperasi.IdentitasKoperasi', compact('identitas', 'notifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // TODO: Implement update logic
        return response()->json(['success' => true, 'message' => 'Data koperasi berhasil diupdate']);
    }
}