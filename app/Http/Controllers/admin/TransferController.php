<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function index()
    {
        // Dummy data transfer
        $transfer = collect([
            (object)[
                'id' => 1,
                'kode_transaksi' => 'TRX-TF-001',
                'tanggal_transaksi' => '2025-12-16 09:30:00',
                'uraian' => 'Transfer Kas Tunai ke Kas Besar',
                'jumlah' => 2000000,
                'dari_kas' => 'Kas Tunai',
                'untuk_kas' => 'Kas Besar',
                'user' => 'Admin',
            ],
            (object)[
                'id' => 2,
                'kode_transaksi' => 'TRX-TF-002',
                'tanggal_transaksi' => '2025-12-17 14:15:00',
                'uraian' => 'Transfer Kas Besar ke Kas Tunai',
                'jumlah' => 1250000,
                'dari_kas' => 'Kas Besar',
                'untuk_kas' => 'Kas Tunai',
                'user' => 'Admin',
            ],
            (object)[
                'id' => 3,
                'kode_transaksi' => 'TRX-TF-003',
                'tanggal_transaksi' => '2025-12-15 10:00:00',
                'uraian' => 'Transfer ke Rekening Bank',
                'jumlah' => 5000000,
                'dari_kas' => 'Kas Besar',
                'untuk_kas' => 'Transfer',
                'user' => 'Kasir',
            ],
            (object)[
                'id' => 4,
                'kode_transaksi' => 'TRX-TF-004',
                'tanggal_transaksi' => '2025-12-14 11:30:00',
                'uraian' => 'Transfer dari Bank ke Kas Tunai',
                'jumlah' => 3000000,
                'dari_kas' => 'Transfer',
                'untuk_kas' => 'Kas Tunai',
                'user' => 'Admin',
            ],
        ]);

        // Hitung total transfer
        $total_transfer = $transfer->sum('jumlah');

        $notifications = collect([]);

        return view(
            'admin.TransaksiKas.transfer.Transfer',
            compact('transfer', 'total_transfer', 'notifications')
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