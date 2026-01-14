<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\LamaAngsuran;
use Illuminate\Http\Request;

class LamaAngsuranController extends Controller
{
    public function index()
    {
        $lamaAngsuran = LamaAngsuran::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.LamaAngsuran.LamaAngsuran',
            compact('lamaAngsuran')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lama_angsuran' => 'required|integer|min:1|max:120',
            'aktif' => 'required|in:Y,T',
        ]);

        LamaAngsuran::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Lama angsuran berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'lama_angsuran' => 'required|integer|min:1|max:120',
            'aktif' => 'required|in:Y,T',
        ]);

        LamaAngsuran::findOrFail($id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lama angsuran berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        LamaAngsuran::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lama angsuran berhasil dihapus'
        ]);
    }

    /**
     * Get list of lama angsuran for dropdown
     */
    public function list()
    {
        $lamaAngsuran = LamaAngsuran::select('id', 'lama_angsuran')
            ->where('aktif', 'Y') 
            ->orderBy('lama_angsuran', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lamaAngsuran
        ]);
    }

    public function export()
    {
        return response('Export Excel Lama Angsuran');
    }

    public function cetak()
    {
        return response('Cetak Laporan Lama Angsuran');
    }
}