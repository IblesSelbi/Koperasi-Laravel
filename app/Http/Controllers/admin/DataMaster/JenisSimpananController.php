<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\JenisSimpanan;
use Illuminate\Http\Request;

class JenisSimpananController extends Controller
{
    public function index()
    {
        $jenisSimpanan = JenisSimpanan::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.JenisSimpanan.JenisSimpanan',
            compact('jenisSimpanan')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'jenis_simpanan' => 'required|string|max:100',
            'jumlah' => 'nullable|numeric|min:0',
            'tampil' => 'required|in:Y,T',
        ]);

        $data['jumlah'] = $data['jumlah'] ?? 0;

        JenisSimpanan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jenis simpanan berhasil ditambahkan'
        ]);
    }


    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'jenis_simpanan' => 'required|string|max:100',
            'jumlah' => 'required|numeric|min:0',
            'tampil' => 'required|in:Y,T',
        ]);

        JenisSimpanan::findOrFail($id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jenis simpanan berhasil diperbarui'
        ]);
    }


    public function destroy($id)
    {
        JenisSimpanan::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis simpanan berhasil dihapus'
        ]);
    }


    public function export()
    {
        return response('Export Excel Jenis Simpanan');
    }

    public function cetak()
    {
        return response('Cetak Laporan Jenis Simpanan');
    }
}
