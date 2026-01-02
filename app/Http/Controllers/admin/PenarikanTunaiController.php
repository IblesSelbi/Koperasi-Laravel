<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenarikanTunaiController extends Controller
{
    public function index()
    {
        // Data kosong untuk penarikan tunai
        $penarikan = collect([]);

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
            'admin.penarikan.PenarikanTunai',
            compact('penarikan', 'anggota_list', 'notifications')
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