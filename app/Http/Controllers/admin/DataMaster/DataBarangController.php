<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataBarang;
use Illuminate\Http\Request;

class DataBarangController extends Controller
{
    public function index()
    {
        $dataBarang = DataBarang::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.DataBarang.DataBarang',
            compact('dataBarang')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'merk' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DataBarang::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'merk' => 'nullable|string|max:50',
            'harga' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:0',
            'keterangan' => 'nullable|string',
        ]);

        DataBarang::findOrFail($id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        DataBarang::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data barang berhasil dihapus'
        ]);
    }

    public function export()
    {
        return response('Export Excel Data Barang');
    }

    public function cetak()
    {
        return response('Cetak Laporan Data Barang');
    }
}