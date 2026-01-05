<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data barang
        $dataBarang = collect([
            (object)[
                'id' => 4,
                'nama_barang' => 'Pinjaman Uang',
                'type' => 'Uang',
                'merk' => '-',
                'harga' => 10000000,
                'jumlah' => 101,
                'keterangan' => '',
            ],
            (object)[
                'id' => 6,
                'nama_barang' => 'Pinjaman Dana Tunai',
                'type' => 'Uang',
                'merk' => '-',
                'harga' => 0,
                'jumlah' => 0,
                'keterangan' => '-',
            ],
            (object)[
                'id' => 7,
                'nama_barang' => 'HP Infinix Note 30 8/256 GB',
                'type' => 'Barang',
                'merk' => 'Infinix',
                'harga' => 2600000,
                'jumlah' => 3,
                'keterangan' => 'Hp',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.DataBarang.DataBarang', compact('dataBarang', 'notifications'));
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
        return response('Export Excel Data Barang');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Data Barang');
    }
}