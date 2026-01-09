<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataKas;
use Illuminate\Http\Request;

class DataKasController extends Controller
{
    public function index()
    {
        $dataKas = DataKas::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.DataKas.DataKas',
            compact('dataKas')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kas' => 'required|string|max:225',
            'aktif' => 'required|in:Y,T',
            'simpanan' => 'required|in:Y,T',
            'penarikan' => 'required|in:Y,T',
            'pinjaman' => 'required|in:Y,T',
            'angsuran' => 'required|in:Y,T',
            'pemasukan_kas' => 'required|in:Y,T',
            'pengeluaran_kas' => 'required|in:Y,T',
            'transfer_kas' => 'required|in:Y,T',
        ]);

        DataKas::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data kas berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_kas' => 'required|string|max:225',
            'aktif' => 'required|in:Y,T',
            'simpanan' => 'required|in:Y,T',
            'penarikan' => 'required|in:Y,T',
            'pinjaman' => 'required|in:Y,T',
            'angsuran' => 'required|in:Y,T',
            'pemasukan_kas' => 'required|in:Y,T',
            'pengeluaran_kas' => 'required|in:Y,T',
            'transfer_kas' => 'required|in:Y,T',
        ]);

        DataKas::findOrFail($id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data kas berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        DataKas::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kas berhasil dihapus'
        ]);
    }

    public function export()
    {
        return response('Export Excel Data Kas');
    }

    public function cetak()
    {
        return response('Cetak Laporan Data Kas');
    }
}