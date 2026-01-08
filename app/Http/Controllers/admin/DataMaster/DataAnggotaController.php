<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataAnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data anggota
        $dataAnggota = collect([
            (object)[
                'id' => 1,
                'photo' => 'assets/images/profile/user-1.jpg',
                'id_anggota' => 'AG0001',
                'username' => 'member1',
                'nama' => 'Faisal',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1995-05-07',
                'status' => 'Belum Kawin',
                'departement' => 'Produksi BOPP',
                'pekerjaan' => 'Lainnya',
                'agama' => 'Islam',
                'alamat' => 'Duren Sawit, Jakarta Timur',
                'kota' => 'Jakarta',
                'no_telp' => '089533222331',
                'tanggal_registrasi' => '2023-05-11',
                'jabatan' => 'Anggota',
                'aktif' => 'Aktif',
            ],
            (object)[
                'id' => 2,
                'photo' => 'assets/images/profile/user-2.jpg',
                'id_anggota' => 'AG0011',
                'username' => 'cahyadi001',
                'nama' => 'Cahyadi',
                'jenis_kelamin' => 'Laki-laki',
                'tempat_lahir' => 'Cirebon',
                'tanggal_lahir' => '1990-03-15',
                'status' => 'Kawin',
                'departement' => '',
                'pekerjaan' => 'Karyawan Swasta',
                'agama' => 'Islam',
                'alamat' => 'Jl Swasembada No 69 Kota Cirebon',
                'kota' => 'Cirebon',
                'no_telp' => '081234567890',
                'tanggal_registrasi' => '2024-06-24',
                'jabatan' => 'Anggota',
                'aktif' => 'Aktif',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.DataAnggota.DataAnggota', compact('dataAnggota', 'notifications'));
    }

    /**
     * Show the import page
     */
    public function showImport()
    {
        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Dummy data hasil import (untuk contoh tampilan)
        $hasilImport = collect([
            (object)[
                'no' => 1,
                'status' => 'success',
                'id_anggota' => 'AG0012',
                'username' => 'member_contoh',
                'nama' => 'Nama Contoh',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Contoh No. 123',
                'kota' => 'Jakarta',
                'jabatan' => 'Anggota',
                'keterangan' => 'Berhasil diimport'
            ],
            (object)[
                'no' => 2,
                'status' => 'failed',
                'id_anggota' => 'AG0013',
                'username' => '',
                'nama' => 'Test User',
                'jenis_kelamin' => '',
                'alamat' => 'Jl. Test',
                'kota' => 'Bandung',
                'jabatan' => 'Anggota',
                'keterangan' => 'Username kosong, Jenis kelamin kosong'
            ],
        ]);

        return view('admin.DataMaster.DataAnggota.ImportAnggota', compact('notifications', 'hasilImport'));
    }

    /**
     * Process import from Excel
     */
    public function processImport(Request $request)
    {
        // TODO: Implement Excel import logic
        return response()->json(['success' => true, 'message' => 'Data berhasil diimport']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store logic
        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement update logic
        return response()->json(['success' => true, 'message' => 'Data berhasil diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // TODO: Implement delete logic
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    /**
     * Export data to Excel
     */
    public function export()
    {
        // TODO: Implement Excel export
        return response('Export Excel Data Anggota');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Data Anggota');
    }
}