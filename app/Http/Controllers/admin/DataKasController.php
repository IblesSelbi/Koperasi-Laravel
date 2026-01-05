<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data kas
        $dataKas = collect([
            (object)[
                'id' => 1,
                'nama_kas' => 'Kas Tunai',
                'aktif' => 'Y',
                'simpanan' => 'Y',
                'penarikan' => 'Y',
                'pinjaman' => 'Y',
                'angsuran' => 'Y',
                'pemasukan_kas' => 'Y',
                'pengeluaran_kas' => 'Y',
                'transfer_kas' => 'Y',
            ],
            (object)[
                'id' => 2,
                'nama_kas' => 'Kas Besar',
                'aktif' => 'Y',
                'simpanan' => 'T',
                'penarikan' => 'T',
                'pinjaman' => 'Y',
                'angsuran' => 'T',
                'pemasukan_kas' => 'Y',
                'pengeluaran_kas' => 'Y',
                'transfer_kas' => 'Y',
            ],
            (object)[
                'id' => 3,
                'nama_kas' => 'Transfer',
                'aktif' => 'Y',
                'simpanan' => 'T',
                'penarikan' => 'T',
                'pinjaman' => 'T',
                'angsuran' => 'T',
                'pemasukan_kas' => 'Y',
                'pengeluaran_kas' => 'Y',
                'transfer_kas' => 'Y',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.DataKas.DataKas', compact('dataKas', 'notifications'));
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
        return response('Export Excel Data Kas');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Data Kas');
    }
}