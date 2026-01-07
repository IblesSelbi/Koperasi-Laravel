<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    public function index()
    {
        // Dummy data pemasukan
        $pemasukan = collect([
            (object)[
                'id' => 1,
                'kode_transaksi' => 'TRX00001',
                'tanggal_transaksi' => '2025-12-15 10:30:00',
                'uraian' => 'Setoran Awal',
                'untuk_kas' => 'Kas Tunai',
                'dari_akun' => 'Modal Awal',
                'jumlah' => 5000000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 2,
                'kode_transaksi' => 'TRX00002',
                'tanggal_transaksi' => '2025-12-14 14:15:00',
                'uraian' => 'Pendapatan Bunga',
                'untuk_kas' => 'Kas Besar',
                'dari_akun' => 'Pendapatan Lainnya',
                'jumlah' => 1500000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 3,
                'kode_transaksi' => 'TRX00003',
                'tanggal_transaksi' => '2025-12-13 09:00:00',
                'uraian' => 'Pembayaran Simpanan Wajib',
                'untuk_kas' => 'Kas Tunai',
                'dari_akun' => 'Simpanan Anggota',
                'jumlah' => 2500000,
                'user' => 'Kasir',
            ],
            (object)[
                'id' => 4,
                'kode_transaksi' => 'TRX00004',
                'tanggal_transaksi' => '2025-12-12 11:45:00',
                'uraian' => 'Pendapatan Jasa Administrasi',
                'untuk_kas' => 'Kas Besar',
                'dari_akun' => 'Pendapatan Operasional',
                'jumlah' => 750000,
                'user' => 'Admin',
            ],
        ]);

        // Hitung total pemasukan
        $total_pemasukan = $pemasukan->sum('jumlah');

        // Dummy data akun untuk dropdown
        $akun_list = collect([
            (object)['id' => 1, 'nama' => 'Modal Awal'],
            (object)['id' => 2, 'nama' => 'Pendapatan Lainnya'],
            (object)['id' => 3, 'nama' => 'Simpanan Anggota'],
            (object)['id' => 4, 'nama' => 'Pendapatan Operasional'],
        ]);

        $notifications = collect([]); 

        return view(
            'admin.TransaksiKas.pemasukan.Pemasukan',
            compact('pemasukan', 'akun_list', 'notifications', 'total_pemasukan')
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
        // Delete database (nanti)
        return redirect()->route('kas.pemasukan')->with('success', 'Data berhasil dihapus!');
    }
}