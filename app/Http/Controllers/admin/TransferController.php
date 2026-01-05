<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        // Data kosong untuk transfer
        $transfer = collect([]);

        $notifications = collect([]);

        return view(
            'admin.TransaksiKas.transfer.Transfer',
            compact('transfer', 'notifications')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
            'dari_kas' => 'required|integer',
            'untuk_kas' => 'required|integer',
        ]);

        // Validasi: dari_kas dan untuk_kas tidak boleh sama
        if ($validated['dari_kas'] == $validated['untuk_kas']) {
            return back()->withErrors(['error' => 'Kas asal dan kas tujuan tidak boleh sama!']);
        }

        // Remove format ribuan dari jumlah
        $jumlah = (int) str_replace('.', '', $validated['jumlah']);

        // TODO: Simpan ke database
        // Transfer::create([
        //     'tanggal_transaksi' => $validated['tanggal_transaksi'],
        //     'jumlah' => $jumlah,
        //     'keterangan' => $validated['keterangan'],
        //     'dari_kas' => $validated['dari_kas'],
        //     'untuk_kas' => $validated['untuk_kas'],
        //     'user_id' => auth()->id(),
        // ]);

        return redirect()->route('kas.transfer')->with('success', 'Data transfer berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'jumlah' => 'required|string',
            'keterangan' => 'nullable|string',
            'dari_kas' => 'required|integer',
            'untuk_kas' => 'required|integer',
        ]);

        // Validasi: dari_kas dan untuk_kas tidak boleh sama
        if ($validated['dari_kas'] == $validated['untuk_kas']) {
            return back()->withErrors(['error' => 'Kas asal dan kas tujuan tidak boleh sama!']);
        }

        // Remove format ribuan dari jumlah
        $jumlah = (int) str_replace('.', '', $validated['jumlah']);

        // TODO: Update database
        // $transfer = Transfer::findOrFail($id);
        // $transfer->update([
        //     'tanggal_transaksi' => $validated['tanggal_transaksi'],
        //     'jumlah' => $jumlah,
        //     'keterangan' => $validated['keterangan'],
        //     'dari_kas' => $validated['dari_kas'],
        //     'untuk_kas' => $validated['untuk_kas'],
        // ]);

        return redirect()->route('kas.transfer')->with('success', 'Data transfer berhasil diupdate!');
    }

    public function destroy($id)
    {
        // TODO: Hapus dari database
        // $transfer = Transfer::findOrFail($id);
        // $transfer->delete();

        return redirect()->route('kas.transfer')->with('success', 'Data transfer berhasil dihapus!');
    }
}