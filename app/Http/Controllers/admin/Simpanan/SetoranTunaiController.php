<?php

namespace App\Http\Controllers\Admin\Simpanan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetoranTunaiController extends Controller
{
    public function index()
    {
        $setoran = SetoranTunai::with(['anggota', 'jenisSimpanan', 'untukKas', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_setoran = $setoran->sum('jumlah');

        // Filter: Anggota aktif
        $anggota_list = DataAnggota::where('aktif', 'Aktif')
            ->orderBy('nama', 'asc')
            ->get();

        // Filter: Jenis simpanan tampil
        $jenis_simpanan_list = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        // Filter: Kas aktif dan simpanan = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('simpanan', 'Y')
            ->get();

        return view('admin.simpanan.setorantunai.SetoranTunai', compact(
            'setoran',
            'total_setoran',
            'anggota_list',
            'jenis_simpanan_list',
            'kas_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $setoran = SetoranTunai::with(['anggota'])->findOrFail($id);
        return response()->json($setoran);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'nama_penyetor' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = SetoranTunai::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        SetoranTunai::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data setoran tunai berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $setoran = SetoranTunai::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'nama_penyetor' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $setoran->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data setoran tunai berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $setoran = SetoranTunai::findOrFail($id);
        $setoran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data setoran tunai berhasil dihapus!'
        ]);
    }

    // API untuk mendapatkan detail anggota (foto, departemen)
    public function getAnggotaDetail($id)
    {
        $anggota = DataAnggota::with('user')->findOrFail($id);

        // ✅ Prioritaskan foto terbaru (sync dengan user)
        $photoPath = null;

        // Cek foto di data_anggota dulu
        if ($anggota->photo && $anggota->photo !== 'assets/images/profile/user-1.jpg') {
            $photoPath = $anggota->photo;
        }
        // Fallback ke foto user jika ada
        elseif ($anggota->user && $anggota->user->profile_image) {
            $photoPath = $anggota->user->profile_image;
        }

        // Generate full URL
        $photoUrl = $photoPath
            ? asset('storage/' . $photoPath)
            : asset('assets/images/profile/user-1.jpg');

        return response()->json([
            'id_anggota' => $anggota->id_anggota,
            'nama' => $anggota->nama,
            'departement' => $anggota->departement ?? '-',
            'photo' => $photoPath ?? 'assets/images/profile/user-1.jpg', // Path saja
            'photo_url' => $photoUrl // Full URL untuk ditampilkan
        ]);
    }
}