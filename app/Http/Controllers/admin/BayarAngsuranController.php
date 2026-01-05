<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BayarAngsuranController extends Controller
{
    /**
     * Display a listing of pinjaman yang belum lunas
     */
    public function index(Request $request)
    {
        $pinjaman = collect([
            (object)[
                'id' => 1,
                'kode' => 'PJ001',
                'tanggal_pinjam' => '2025-12-15',
                'anggota_id' => '001234',
                'anggota_nama' => 'Budi Santoso',
                'anggota_kota' => 'Jakarta',
                'anggota_foto' => 'assets/images/profile/user-2.jpg',
                'pokok_pinjaman' => 10000000,
                'lama_angsuran' => 12,
                'angsuran_pokok' => 833333,
                'bunga_angsuran' => 50000,
                'biaya_admin' => 100000,
                'angsuran_per_bulan' => 983333,
                'sisa_angsuran' => 5,
                'status_lunas' => 'Belum',
            ],
            (object)[
                'id' => 2,
                'kode' => 'PJ002',
                'tanggal_pinjam' => '2025-12-14',
                'anggota_id' => '001235',
                'anggota_nama' => 'Siti Aminah',
                'anggota_kota' => 'Bandung',
                'anggota_foto' => 'assets/images/profile/user-3.jpg',
                'pokok_pinjaman' => 5000000,
                'lama_angsuran' => 6,
                'angsuran_pokok' => 833333,
                'bunga_angsuran' => 25000,
                'biaya_admin' => 50000,
                'angsuran_per_bulan' => 908333,
                'sisa_angsuran' => 3,
                'status_lunas' => 'Belum',
            ]
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Pinjaman.bayar.BayarAngsuran', compact('pinjaman', 'notifications'));
    }

    /**
     * Display detail bayar angsuran untuk pinjaman tertentu
     */
    public function show($id)
    {
        // TODO: Get real data from database
        $pinjaman = (object)[
            'id' => $id,
            'kode' => 'TPJ00019',
            'tanggal_pinjam' => '2025-12-07',
            'tanggal_tempo' => '2026-12-07',
            'lama_pinjaman' => 12,
            'anggota_id' => 'AG0008',
            'anggota_nama' => 'Widi Aljatsiyah',
            'anggota_departemen' => '-',
            'anggota_ttl' => 'Lampung, 16 Juni 2023',
            'anggota_kota' => 'Kota Tasikmalaya',
            'anggota_foto' => 'assets/images/profile/user-2.jpg',
            'pokok_pinjaman' => 2600000,
            'angsuran_pokok' => 216667,
            'biaya_bunga' => 130000,
            'jumlah_angsuran' => 346700,
            'sisa_angsuran' => 9,
            'sudah_dibayar' => 1040100,
            'jumlah_denda' => 0,
            'sisa_tagihan' => 3120300,
            'status_lunas' => 'Belum Lunas',
        ];

        // Data pembayaran angsuran
        $pembayaran = collect([
            (object)[
                'id' => 1,
                'kode_bayar' => 'TBY00019',
                'tanggal_bayar' => '2025-12-07',
                'waktu_bayar' => '10:30',
                'tanggal_tempo' => '2025-12-15',
                'angsuran_ke' => 1,
                'jumlah_bayar' => 346700,
                'denda' => 0,
                'status_keterlambatan' => 'Tepat Waktu',
                'user' => 'admin'
            ],
            (object)[
                'id' => 2,
                'kode_bayar' => 'TBY00020',
                'tanggal_bayar' => '2025-12-07',
                'waktu_bayar' => '11:15',
                'tanggal_tempo' => '2025-12-15',
                'angsuran_ke' => 2,
                'jumlah_bayar' => 346700,
                'denda' => 0,
                'status_keterlambatan' => 'Tepat Waktu',
                'user' => 'admin'
            ],
            (object)[
                'id' => 3,
                'kode_bayar' => 'TBY00021',
                'tanggal_bayar' => '2025-12-07',
                'waktu_bayar' => '14:20',
                'tanggal_tempo' => '2025-12-15',
                'angsuran_ke' => 3,
                'jumlah_bayar' => 346700,
                'denda' => 0,
                'status_keterlambatan' => 'Tepat Waktu',
                'user' => 'admin'
            ]
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Pinjaman.bayar.DetailBayarAngsuran', compact('pinjaman', 'pembayaran', 'notifications'));
    }

    /**
     * Process payment for angsuran (from modal in index page)
     */
    public function proses(Request $request)
    {
        $validated = $request->validate([
            'pinjaman_id' => 'required|integer',
            'tgl_bayar' => 'required|date',
            'angsuran_ke' => 'required|integer|min:1',
            'jumlah_bayar' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'kas_id' => 'required|integer',
            'status' => 'required|string|in:normal,pelunasan',
            'keterangan' => 'nullable|string',
        ]);

        // TODO: Process payment
        // $pembayaran = Pembayaran::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran angsuran berhasil diproses'
        ]);
    }

    /**
     * Store new payment (from detail page)
     */
    public function bayar(Request $request)
    {
        $validated = $request->validate([
            'pinjaman_id' => 'required|integer',
            'angsuran_ke' => 'required|integer|min:1',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
        ]);

        // TODO: Process payment
        
        return response()->json([
            'success' => true,
            'message' => 'Pembayaran angsuran berhasil disimpan'
        ]);
    }

    /**
     * Update existing payment
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:0',
        ]);

        // TODO: Process update
        
        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil diupdate'
        ]);
    }

    /**
     * Delete payment record
     */
    public function destroy($id)
    {
        // TODO: Process deletion
        
        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran berhasil dihapus'
        ]);
    }

    /**
     * Print bukti pembayaran
     */
    public function cetakBukti($id)
    {
        // TODO: Get data and generate print view
        return response('Cetak bukti pembayaran ID: ' . $id);
    }

    /**
     * Print payment receipt (from detail page)
     */
    public function cetakNota($id)
    {
        // TODO: Get data and generate print view
        
        return view('admin.bayar.cetak_nota', compact('id'));
    }
}