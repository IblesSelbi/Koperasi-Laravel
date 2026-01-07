<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetoranTunaiController extends Controller
{
    public function index()
    {
        // Dummy data setoran tunai
        $setoran = collect([
            (object)[
                'id' => 1,
                'kode_transaksi' => 'STR00001',
                'tanggal_transaksi' => '2025-12-16 09:00:00',
                'id_anggota' => 'A001',
                'nama_anggota' => 'Budi Santoso',
                'departemen' => 'Keuangan',
                'jenis_simpanan' => 'Simpanan Wajib',
                'jumlah' => 500000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 2,
                'kode_transaksi' => 'STR00002',
                'tanggal_transaksi' => '2025-12-15 10:30:00',
                'id_anggota' => 'A002',
                'nama_anggota' => 'Siti Rahayu',
                'departemen' => 'Operasional',
                'jenis_simpanan' => 'Simpanan Sukarela',
                'jumlah' => 2000000,
                'user' => 'Kasir',
            ],
            (object)[
                'id' => 3,
                'kode_transaksi' => 'STR00003',
                'tanggal_transaksi' => '2025-12-14 13:15:00',
                'id_anggota' => 'A003',
                'nama_anggota' => 'Ahmad Hidayat',
                'departemen' => 'IT',
                'jenis_simpanan' => 'Simpanan Pokok',
                'jumlah' => 2500000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 4,
                'kode_transaksi' => 'STR00004',
                'tanggal_transaksi' => '2025-12-13 14:45:00',
                'id_anggota' => 'A004',
                'nama_anggota' => 'Dewi Lestari',
                'departemen' => 'Marketing',
                'jenis_simpanan' => 'Simpanan Wajib',
                'jumlah' => 750000,
                'user' => 'Kasir',
            ],
            (object)[
                'id' => 5,
                'kode_transaksi' => 'STR00005',
                'tanggal_transaksi' => '2025-12-12 11:00:00',
                'id_anggota' => 'A005',
                'nama_anggota' => 'Eko Prasetyo',
                'departemen' => 'Produksi',
                'jenis_simpanan' => 'Simpanan Sukarela',
                'jumlah' => 1500000,
                'user' => 'Admin',
            ],
        ]);

        // Hitung total setoran
        $total_setoran = $setoran->sum('jumlah');

        // Data sample untuk dropdown anggota
        $anggota_list = collect([
            (object)[
                'id' => 1,
                'id_anggota' => 'A001',
                'nama' => 'Budi Santoso'
            ],
            (object)[
                'id' => 2,
                'id_anggota' => 'A002',
                'nama' => 'Siti Rahayu'
            ],
            (object)[
                'id' => 3,
                'id_anggota' => 'A003',
                'nama' => 'Ahmad Hidayat'
            ],
            (object)[
                'id' => 4,
                'id_anggota' => 'A004',
                'nama' => 'Dewi Lestari'
            ],
            (object)[
                'id' => 5,
                'id_anggota' => 'A005',
                'nama' => 'Eko Prasetyo'
            ],
        ]);

        $notifications = collect([]);

        return view(
            'admin.simpanan.setorantunai.SetoranTunai',
            compact('setoran', 'anggota_list', 'total_setoran', 'notifications')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'nama_penyetor' => 'nullable|string',
            'no_identitas' => 'nullable|string',
            'alamat' => 'nullable|string',
            'anggota_id' => 'required|integer',
            'jenis_simpanan' => 'required|string',
            'jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
            'kas' => 'required|string',
        ]);

        // Remove format ribuan dari jumlah
        $jumlah = (int) str_replace('.', '', $validated['jumlah']);

        // TODO: Simpan ke database
        // SetoranTunai::create([
        //     'tanggal_transaksi' => $validated['tanggal_transaksi'],
        //     'nama_penyetor' => $validated['nama_penyetor'],
        //     'no_identitas' => $validated['no_identitas'],
        //     'alamat' => $validated['alamat'],
        //     'anggota_id' => $validated['anggota_id'],
        //     'jenis_simpanan' => $validated['jenis_simpanan'],
        //     'jumlah' => $jumlah,
        //     'keterangan' => $validated['keterangan'],
        //     'kas' => $validated['kas'],
        //     'user_id' => auth()->id(),
        // ]);

        return redirect()->route('simpanan.setoran')->with('success', 'Data setoran tunai berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'nama_penyetor' => 'nullable|string',
            'no_identitas' => 'nullable|string',
            'alamat' => 'nullable|string',
            'anggota_id' => 'required|integer',
            'jenis_simpanan' => 'required|string',
            'jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
            'kas' => 'required|string',
        ]);

        // Remove format ribuan dari jumlah
        $jumlah = (int) str_replace('.', '', $validated['jumlah']);

        // TODO: Update database
        // $setoran = SetoranTunai::findOrFail($id);
        // $setoran->update([...]);

        return redirect()->route('simpanan.setoran')->with('success', 'Data setoran tunai berhasil diupdate!');
    }

    public function destroy($id)
    {
        // TODO: Hapus dari database
        // $setoran = SetoranTunai::findOrFail($id);
        // $setoran->delete();

        return redirect()->route('simpanan.setoran')->with('success', 'Data setoran tunai berhasil dihapus!');
    }
}