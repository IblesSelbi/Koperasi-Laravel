<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\JenisAkun;
use Illuminate\Http\Request;

class JenisAkunController extends Controller
{
    public function index()
    {
        $jenisAkun = JenisAkun::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.JenisAkun.JenisAkun',
            compact('jenisAkun')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kd_aktiva' => 'required|string|max:10',
            'jns_transaksi' => 'required|string|max:100',
            'akun' => 'required|in:Aktiva,Pasiva',
            'pemasukan' => 'required|in:Y,T',
            'pengeluaran' => 'required|in:Y,T',
            'aktif' => 'required|in:Y,T',
            'laba_rugi' => 'nullable|in:PENDAPATAN,BIAYA',
        ]);

        JenisAkun::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jenis akun berhasil ditambahkan'
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'kd_aktiva' => 'required|string|max:10',
            'jns_transaksi' => 'required|string|max:100',
            'akun' => 'required|in:Aktiva,Pasiva',
            'pemasukan' => 'required|in:Y,T',
            'pengeluaran' => 'required|in:Y,T',
            'aktif' => 'required|in:Y,T',
            'laba_rugi' => 'nullable|in:PENDAPATAN,BIAYA',
        ]);

        JenisAkun::findOrFail($id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jenis akun berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        JenisAkun::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jenis akun berhasil dihapus'
        ]);
    }

    public function export()
    {
        return response('Export Excel Jenis Akun');
    }

    public function cetak()
    {
        return response('Cetak Laporan Jenis Akun');
    }
}