<?php

namespace App\Http\Controllers\Admin\Simpanan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Simpanan\PenarikanTunai;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenarikanTunaiController extends Controller
{
    public function index()
    {
        $penarikan = PenarikanTunai::with(['anggota', 'jenisSimpanan', 'dariKas', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_penarikan = $penarikan->sum('jumlah');

        // Filter: Anggota aktif
        $anggota_list = DataAnggota::where('aktif', 'Aktif')
            ->orderBy('nama', 'asc')
            ->get();

        // Filter: Jenis simpanan tampil
        $jenis_simpanan_list = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        // Filter: Kas aktif dan penarikan = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('penarikan', 'Y')
            ->get();

        return view('admin.simpanan.penarikan.PenarikanTunai', compact(
            'penarikan',
            'total_penarikan',
            'anggota_list',
            'jenis_simpanan_list',
            'kas_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $penarikan = PenarikanTunai::with(['anggota'])->findOrFail($id);
        return response()->json($penarikan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'nama_penarik' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = PenarikanTunai::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        PenarikanTunai::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data penarikan tunai berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $penarikan = PenarikanTunai::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'nama_penarik' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $penarikan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data penarikan tunai berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $penarikan = PenarikanTunai::findOrFail($id);
        $penarikan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data penarikan tunai berhasil dihapus!'
        ]);
    }

    // API untuk mendapatkan detail anggota (foto, departemen)
    public function getAnggotaDetail($id)
    {
        $anggota = DataAnggota::findOrFail($id);
        return response()->json([
            'id_anggota' => $anggota->id_anggota,
            'nama' => $anggota->nama,
            'departement' => $anggota->departement,
            'photo' => $anggota->photo ?? 'assets/images/profile/user-1.jpg'
        ]);
    }
}