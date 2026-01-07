<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenarikanTunaiController extends Controller
{
    public function index()
    {
        // Dummy data penarikan tunai
        $penarikan = collect([
            (object)[
                'id' => 1,
                'kode_transaksi' => 'PNR00001',
                'tanggal_transaksi' => '2025-12-16 10:30:00',
                'id_anggota' => 'A001',
                'nama_anggota' => 'Budi Santoso',
                'departemen' => 'Keuangan',
                'jenis_simpanan' => 'Simpanan Wajib',
                'jumlah' => 500000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 2,
                'kode_transaksi' => 'PNR00002',
                'tanggal_transaksi' => '2025-12-15 14:00:00',
                'id_anggota' => 'A002',
                'nama_anggota' => 'Siti Rahayu',
                'departemen' => 'Operasional',
                'jenis_simpanan' => 'Simpanan Sukarela',
                'jumlah' => 1000000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 3,
                'kode_transaksi' => 'PNR00003',
                'tanggal_transaksi' => '2025-12-14 09:15:00',
                'id_anggota' => 'A003',
                'nama_anggota' => 'Ahmad Hidayat',
                'departemen' => 'IT',
                'jenis_simpanan' => 'Simpanan Pokok',
                'jumlah' => 2500000,
                'user' => 'Kasir',
            ],
            (object)[
                'id' => 4,
                'kode_transaksi' => 'PNR00004',
                'tanggal_transaksi' => '2025-12-13 11:30:00',
                'id_anggota' => 'A004',
                'nama_anggota' => 'Dewi Lestari',
                'departemen' => 'Marketing',
                'jenis_simpanan' => 'Simpanan Wajib',
                'jumlah' => 750000,
                'user' => 'Admin',
            ],
            (object)[
                'id' => 5,
                'kode_transaksi' => 'PNR00005',
                'tanggal_transaksi' => '2025-12-12 15:45:00',
                'id_anggota' => 'A005',
                'nama_anggota' => 'Eko Prasetyo',
                'departemen' => 'Produksi',
                'jenis_simpanan' => 'Simpanan Sukarela',
                'jumlah' => 1500000,
                'user' => 'Kasir',
            ],
        ]);

        // Hitung total penarikan
        $total_penarikan = $penarikan->sum('jumlah');

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
            'admin.simpanan.penarikan.PenarikanTunai',
            compact('penarikan', 'anggota_list', 'total_penarikan', 'notifications')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'nama_penarik' => 'nullable|string',
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
        // PenarikanTunai::create([
        //     'tanggal_transaksi' => $validated['tanggal_transaksi'],
        //     'nama_penarik' => $validated['nama_penarik'],
        //     'no_identitas' => $validated['no_identitas'],
        //     'alamat' => $validated['alamat'],
        //     'anggota_id' => $validated['anggota_id'],
        //     'jenis_simpanan' => $validated['jenis_simpanan'],
        //     'jumlah' => $jumlah,
        //     'keterangan' => $validated['keterangan'],
        //     'kas' => $validated['kas'],
        //     'user_id' => auth()->id(),
        // ]);

        return redirect()->route('simpanan.penarikan')->with('success', 'Data penarikan tunai berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'nama_penarik' => 'nullable|string',
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
        // $penarikan = PenarikanTunai::findOrFail($id);
        // $penarikan->update([...]);

        return redirect()->route('simpanan.penarikan')->with('success', 'Data penarikan tunai berhasil diupdate!');
    }

    public function destroy($id)
    {
        // TODO: Hapus dari database
        // $penarikan = PenarikanTunai::findOrFail($id);
        // $penarikan->delete();

        return redirect()->route('simpanan.penarikan')->with('success', 'Data penarikan tunai berhasil dihapus!');
    }
}