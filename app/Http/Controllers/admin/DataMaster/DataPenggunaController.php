<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataPengguna;
use Illuminate\Http\Request;

class DataPenggunaController extends Controller
{
    public function index()
    {
        $dataPengguna = DataPengguna::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.DataPengguna.DataPengguna',
            compact('dataPengguna')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255|unique:data_pengguna,username',
            'password' => 'required|string|min:6',
            'level' => 'required|in:admin,operator,pinjaman',
            'status' => 'required|in:Y,N',
        ]);

        DataPengguna::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255|unique:data_pengguna,username,' . $id,
            'password' => 'nullable|string|min:6',
            'level' => 'required|in:admin,operator,pinjaman',
            'status' => 'required|in:Y,N',
        ]);

        // Hapus password dari array jika kosong
        if (empty($data['password'])) {
            unset($data['password']);
        }

        DataPengguna::findOrFail($id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        DataPengguna::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengguna berhasil dihapus'
        ]);
    }

    public function export()
    {
        return response('Export Excel Data Pengguna');
    }

    public function cetak()
    {
        return response('Cetak Laporan Data Pengguna');
    }
}