<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dummy data pengajuan user (2 data)
        $pengajuan = collect([
            (object)[
                'id' => 1,
                'tanggal' => '2025-12-15',
                'jenis' => 'Biasa',
                'jumlah' => 10000000,
                'jumlah_angsuran' => 12,
                'keterangan' => 'Untuk renovasi rumah',
                'alasan' => null,
                'tanggal_update' => '2025-12-15',
                'status' => 0, // 0=menunggu, 1=disetujui, 2=ditolak, 3=terlaksana, 4=batal
                'tanggal_cair' => null,
            ],
            (object)[
                'id' => 2,
                'tanggal' => '2025-12-10',
                'jenis' => 'Darurat',
                'jumlah' => 5000000,
                'jumlah_angsuran' => 1,
                'keterangan' => 'Keperluan medis mendesak',
                'alasan' => 'Disetujui untuk pencairan',
                'tanggal_update' => '2025-12-10',
                'status' => 1,
                'tanggal_cair' => '2025-12-10',
            ],
        ]);

        return view('user.PengajuanPinjaman.Pengajuan.PengajuanUser', compact('pengajuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.PengajuanPinjaman.Pengajuan.FormPengajuanUser');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:Biasa,Darurat,Barang',
            'nominal' => 'required',
            'lama_ags' => 'required|integer',
            'keterangan' => 'required|string|max:255',
        ]);

        // TODO: Store data to database
        // $pengajuan = new Pengajuan();
        // $pengajuan->jenis = $validated['jenis'];
        // $pengajuan->jumlah = str_replace('.', '', $validated['nominal']);
        // $pengajuan->jumlah_angsuran = $validated['lama_ags'];
        // $pengajuan->keterangan = $validated['keterangan'];
        // $pengajuan->save();

        return redirect()->route('user.pengajuan.index')
            ->with('success', 'Pengajuan pinjaman berhasil dikirim');
    }

    /**
     * Update pengajuan data (nominal, angsuran, keterangan)
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'pk' => 'required|integer',
            'name' => 'required|string|in:nominal,lama_ags,keterangan',
            'value' => 'required',
        ]);

        // TODO: Update data to database
        // $pengajuan = Pengajuan::findOrFail($validated['pk']);
        // $pengajuan->{$validated['name']} = $validated['value'];
        // $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui'
        ]);
    }

    /**
     * Cancel pengajuan
     */
    public function batal($id)
    {
        // TODO: Update status to canceled
        // $pengajuan = Pengajuan::findOrFail($id);
        // $pengajuan->status = 4;
        // $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil dibatalkan'
        ]);
    }

    /**
     * Print pengajuan
     */
    public function cetak($id)
    {
        // TODO: Get data and generate print view
        // $pengajuan = Pengajuan::with('anggota')->findOrFail($id);
        // return view('user.pengajuan.cetak', compact('pengajuan'));
        
        return response('Cetak pengajuan ID: ' . $id);
    }
}