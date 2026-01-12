<?php

namespace App\Http\Controllers\Admin\TransaksiKas;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\DataMaster\JenisAkun;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluaran = Pengeluaran::with(['dariKas', 'untukAkun', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_pengeluaran = $pengeluaran->sum('jumlah');

        // Filter: Kas aktif dan pengeluaran_kas = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('pengeluaran_kas', 'Y')
            ->get();

        // Filter: Akun aktif dan pengeluaran = Y
        $akun_list = JenisAkun::where('aktif', 'Y')
            ->where('pengeluaran', 'Y')
            ->get();

        return view('admin.TransaksiKas.pengeluaran.Pengeluaran', compact(
            'pengeluaran',
            'total_pengeluaran',
            'kas_list',
            'akun_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        return response()->json($pengeluaran);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'untuk_akun_id' => 'required|exists:jenis_akun,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = Pengeluaran::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        Pengeluaran::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'untuk_akun_id' => 'required|exists:jenis_akun,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $pengeluaran->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil dihapus!'
        ]);
    }
}