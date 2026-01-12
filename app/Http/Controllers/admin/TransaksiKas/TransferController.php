<?php

namespace App\Http\Controllers\Admin\TransaksiKas;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransaksiKas\Transfer;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransferController extends Controller
{
    public function index()
    {
        $transfer = Transfer::with(['dariKas', 'untukKas', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_transfer = $transfer->sum('jumlah');

        // Filter: Kas aktif dan transfer_kas = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('transfer_kas', 'Y')
            ->get();

        return view('admin.TransaksiKas.transfer.Transfer', compact(
            'transfer',
            'total_transfer',
            'kas_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $transfer = Transfer::findOrFail($id);
        return response()->json($transfer);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = Transfer::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        Transfer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data transfer berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $transfer = Transfer::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $transfer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data transfer berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $transfer = Transfer::findOrFail($id);
        $transfer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data transfer berhasil dihapus!'
        ]);
    }
}