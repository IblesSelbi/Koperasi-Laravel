<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LamaAngsuranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data lama angsuran
        $lamaAngsuran = collect([
            (object)[
                'id' => 1,
                'lama_angsuran' => 3,
                'aktif' => 'Y',
            ],
            (object)[
                'id' => 2,
                'lama_angsuran' => 6,
                'aktif' => 'Y',
            ],
            (object)[
                'id' => 3,
                'lama_angsuran' => 12,
                'aktif' => 'Y',
            ],
            (object)[
                'id' => 11,
                'lama_angsuran' => 24,
                'aktif' => 'Y',
            ],
            (object)[
                'id' => 12,
                'lama_angsuran' => 36,
                'aktif' => 'Y',
            ],
            (object)[
                'id' => 14,
                'lama_angsuran' => 1,
                'aktif' => 'Y',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.LamaAngsuran.LamaAngsuran', compact('lamaAngsuran', 'notifications'));
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
        return response('Export Excel Lama Angsuran');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Lama Angsuran');
    }
}