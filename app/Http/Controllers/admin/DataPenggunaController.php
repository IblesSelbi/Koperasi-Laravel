<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataPenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data pengguna
        $dataPengguna = collect([
            (object)[
                'id' => 1,
                'username' => 'admin',
                'level' => 'admin',
                'status' => 'Y',
            ],
            (object)[
                'id' => 4,
                'username' => 'user',
                'level' => 'operator',
                'status' => 'Y',
            ],
            (object)[
                'id' => 5,
                'username' => 'pinjaman',
                'level' => 'pinjaman',
                'status' => 'Y',
            ],
            (object)[
                'id' => 7,
                'username' => 'admin1',
                'level' => 'operator',
                'status' => 'Y',
            ],
            (object)[
                'id' => 8,
                'username' => 'anggota',
                'level' => 'pinjaman',
                'status' => 'Y',
            ],
            (object)[
                'id' => 9,
                'username' => 'test',
                'level' => 'pinjaman',
                'status' => 'Y',
            ],
            (object)[
                'id' => 10,
                'username' => 'pengguna99',
                'level' => 'pinjaman',
                'status' => 'Y',
            ],
            (object)[
                'id' => 11,
                'username' => 'yanto',
                'level' => 'operator',
                'status' => 'Y',
            ],
            (object)[
                'id' => 12,
                'username' => 'anggota100',
                'level' => 'pinjaman',
                'status' => 'Y',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.DataPengguna.DataPengguna', compact('dataPengguna', 'notifications'));
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
        return response('Export Excel Data Pengguna');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Data Pengguna');
    }
}