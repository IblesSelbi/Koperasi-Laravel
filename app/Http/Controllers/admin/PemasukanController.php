<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    public function index()
    {
        $pemasukan = collect([
           
        ]);

        $akun_list = collect([
          
        ]);

        $notifications = collect([]); 

        return view(
            'admin.TransaksiKas.pemasukan.Pemasukan',
            compact('pemasukan', 'akun_list', 'notifications')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'dari_akun' => 'required|integer',
        ]);

        // Simpan ke database (nanti)

        return redirect()->route('kas.pemasukan')->with('success', 'Data berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'dari_akun' => 'required|integer',
        ]);

        // Update database (nanti)

        return redirect()->route('kas.pemasukan')->with('success', 'Data berhasil diupdate!');
    }

    public function destroy($id)
    {

        return redirect()->route('kas.pemasukan')->with('success', 'Data berhasil dihapus!');
    }
}