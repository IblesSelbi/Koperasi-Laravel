<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JenisSimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data jenis simpanan
        $jenisSimpanan = collect([
            (object)[
                'id' => 32,
                'jenis_simpanan' => 'Simpanan Sukarela',
                'jumlah' => 0,
                'tampil' => 'Y',
            ],
            (object)[
                'id' => 40,
                'jenis_simpanan' => 'Simpanan Pokok',
                'jumlah' => 100000,
                'tampil' => 'Y',
            ],
            (object)[
                'id' => 41,
                'jenis_simpanan' => 'Simpanan Wajib',
                'jumlah' => 50000,
                'tampil' => 'Y',
            ],
            (object)[
                'id' => 42,
                'jenis_simpanan' => 'asdf',
                'jumlah' => 0,
                'tampil' => 'Y',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.JenisSimpanan.JenisSimpanan', compact('jenisSimpanan', 'notifications'));
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
        return response('Export Excel Jenis Simpanan');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Jenis Simpanan');
    }
}