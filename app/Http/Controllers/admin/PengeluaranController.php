<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index()
    {
        // Data kosong untuk pengeluaran
        $pengeluaran = collect([]);

        // Data sample untuk dropdown akun
        $akun_list = collect([
            (object)['id' => 5, 'nama' => 'Piutang Usaha'],
            (object)['id' => 9, 'nama' => 'Persediaan Barang'],
            (object)['id' => 10, 'nama' => 'Biaya Dibayar Dimuka'],
            (object)['id' => 11, 'nama' => 'Perlengkapan Usaha'],
            (object)['id' => 18, 'nama' => 'Peralatan Kantor'],
            (object)['id' => 19, 'nama' => 'Inventaris Kendaraan'],
            (object)['id' => 20, 'nama' => 'Mesin'],
            (object)['id' => 29, 'nama' => 'Utang Usaha'],
            (object)['id' => 33, 'nama' => 'Utang Pajak'],
            (object)['id' => 37, 'nama' => 'Utang Bank'],
            (object)['id' => 42, 'nama' => 'Modal Awal'],
            (object)['id' => 44, 'nama' => 'Modal Sumbangan'],
            (object)['id' => 45, 'nama' => 'Modal Cadangan'],
            (object)['id' => 52, 'nama' => 'Beban Gaji Karyawan'],
            (object)['id' => 53, 'nama' => 'Biaya Listrik dan Air'],
            (object)['id' => 54, 'nama' => 'Biaya Transportasi'],
            (object)['id' => 60, 'nama' => 'Biaya Lainnya'],
            (object)['id' => 111, 'nama' => 'Permisalan'],
            (object)['id' => 112, 'nama' => 'Transaksi'],
        ]);

        // Hitung total pengeluaran
        $total_pengeluaran = $pengeluaran->sum('jumlah');

        $notifications = collect([]);

        return view(
            'admin.pengeluaran.pengeluaran',
            compact('pengeluaran', 'akun_list', 'total_pengeluaran', 'notifications')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
            'dari_kas' => 'required|integer',
            'untuk_akun' => 'required|integer',
        ]);

        // Remove format ribuan dari jumlah
        $jumlah = (int) str_replace('.', '', $validated['jumlah']);

        // TODO: Simpan ke database

        return redirect()->route('kas.pengeluaran')->with('success', 'Data pengeluaran berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
            'dari_kas' => 'required|integer',
            'untuk_akun' => 'required|integer',
        ]);

        // Remove format ribuan dari jumlah
        $jumlah = (int) str_replace('.', '', $validated['jumlah']);

        // TODO: Update database

        return redirect()->route('kas.pengeluaran')->with('success', 'Data pengeluaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        // TODO: Hapus dari database

        return redirect()->route('kas.pengeluaran')->with('success', 'Data pengeluaran berhasil dihapus!');
    }
}