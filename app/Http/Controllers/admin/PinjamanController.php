<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pinjaman = collect([
            (object)[
                'id' => 1,
                'kode' => 'PJ001',
                'tanggal_pinjam' => '2025-12-15 10:30:00',
                'anggota_id' => '001234',
                'anggota_nama' => 'Budi Santoso',
                'anggota_departemen' => 'Departemen IT',
                'anggota_foto' => 'assets/images/profile/user-2.jpg',
                'nama_barang' => 'Pinjaman Dana Tunai',
                'harga_barang' => 2600000,
                'lama_angsuran' => 6,
                'pokok_angsuran' => 433333,
                'bunga_pinjaman' => 130000,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 1575000,
                'jumlah_denda' => 0,
                'sudah_dibayar' => 0,
                'sisa_angsuran' => 1,
                'sisa_tagihan' => 1575000,
                'status_lunas' => 'Belum',
                'user' => 'Admin',
            ],
            (object)[
                'id' => 2,
                'kode' => 'PJ005',
                'tanggal_pinjam' => '2025-12-11 16:20:00',
                'anggota_id' => '001238',
                'anggota_nama' => 'Eko Prasetyo',
                'anggota_departemen' => 'Departemen Marketing',
                'anggota_foto' => 'assets/images/profile/user-6.jpg',
                'nama_barang' => 'Pinjaman Usaha',
                'harga_barang' => 12000000,
                'lama_angsuran' => 18,
                'pokok_angsuran' => 666667,
                'bunga_pinjaman' => 600000,
                'biaya_admin' => 120000,
                'jumlah_angsuran' => 12720000,
                'jumlah_denda' => 0,
                'sudah_dibayar' => 12720000,
                'sisa_angsuran' => 0,
                'sisa_tagihan' => 0,
                'status_lunas' => 'Lunas',
                'user' => 'Admin',
            ]
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.pinjaman.Pinjaman', compact('pinjaman', 'notifications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pinjam' => 'required|date',
            'anggota_id' => 'required|string',
            'barang_id' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'lama_angsuran' => 'required|integer|min:1',
            'kas_id' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // TODO: Process storage
        // $pinjaman = Pinjaman::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pinjaman berhasil disimpan'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_pinjam' => 'required|date',
            'anggota_id' => 'required|string',
            'barang_id' => 'required|string',
            'jumlah' => 'required|numeric|min:0',
            'lama_angsuran' => 'required|integer|min:1',
            'kas_id' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // TODO: Process update
        // $pinjaman = Pinjaman::findOrFail($id);
        // $pinjaman->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pinjaman berhasil diupdate'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // TODO: Process deletion
        // $pinjaman = Pinjaman::findOrFail($id);
        // $pinjaman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pinjaman berhasil dihapus'
        ]);
    }

    /**
     * Show the detail of a specific pinjaman
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

        // Simulasi tagihan
        $simulasi = collect();
        $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        for ($i = 1; $i <= 12; $i++) {
            $simulasi->push((object)[
                'bulan_ke' => $i,
                'angsuran_pokok' => 216667,
                'angsuran_bunga' => 130000,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 346700,
                'tanggal_tempo' => "15 {$bulan[$i - 1]} 2026"
            ]);
        }

        // Detail transaksi pembayaran
        $transaksi = collect([
            (object)[
                'no' => 1,
                'kode_bayar' => 'TBY00019',
                'tanggal_bayar' => '2025-12-07',
                'angsuran_ke' => 1,
                'jenis_pembayaran' => 'Angsuran',
                'jumlah_bayar' => 346700,
                'denda' => 0,
                'user' => 'admin'
            ],
            (object)[
                'no' => 2,
                'kode_bayar' => 'TBY00021',
                'tanggal_bayar' => '2025-12-07',
                'angsuran_ke' => 3,
                'jenis_pembayaran' => 'Angsuran',
                'jumlah_bayar' => 346700,
                'denda' => 0,
                'user' => 'admin'
            ],
            (object)[
                'no' => 3,
                'kode_bayar' => 'TBY00020',
                'tanggal_bayar' => '2025-12-07',
                'angsuran_ke' => 2,
                'jenis_pembayaran' => 'Angsuran',
                'jumlah_bayar' => 346700,
                'denda' => 0,
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

        return view('admin.pinjaman.DetailPinjaman', compact('pinjaman', 'simulasi', 'transaksi', 'notifications'));
    }

    /**
     * Print single pinjaman nota
     */
    public function cetak($id)
    {
        // TODO: Get data and generate print view
        $pinjaman = (object)[
            'kode' => 'PJ001',
            'tanggal_pinjam' => '2025-12-15',
            'anggota_nama' => 'Budi Santoso',
            'jumlah' => 2600000,
        ];
        
        return view('admin.pinjaman.cetak_nota', compact('pinjaman'));
    }

    /**
     * Validate pinjaman as lunas (paid off)
     */
    public function validasiLunas($id)
    {
        // TODO: Update status to lunas
        // $pinjaman = Pinjaman::findOrFail($id);
        // $pinjaman->status_lunas = 'Lunas';
        // $pinjaman->save();

        return response()->json([
            'success' => true,
            'message' => 'Pinjaman telah divalidasi sebagai lunas'
        ]);
    }

    /**
     * Print laporan with filters
     */
    public function cetakLaporan(Request $request)
    {
        $status = $request->get('status', '');
        $kode = $request->get('kode', '');
        $nama = $request->get('nama', '');
        $tanggal = $request->get('tanggal', '');

        // Sample data for print view
        $pinjaman = collect([
            (object)[
                'kode' => 'PJ001',
                'tanggal_pinjam' => '2025-12-15',
                'anggota_nama' => 'Budi Santoso',
                'jumlah' => 2600000,
                'status_lunas' => 'Belum',
            ],
        ]);
        
        return view('admin.pinjaman.cetak_laporan', compact('pinjaman', 'status', 'kode', 'nama', 'tanggal'));
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new PinjamanExport, 'data-pinjaman.xlsx');
        
        return response('Export Excel Data Pinjaman');
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        // TODO: Implement PDF export using DomPDF or similar
        // $pinjaman = Pinjaman::with('anggota')->get();
        // $pdf = PDF::loadView('admin.pinjaman.export_pdf_pinjaman', compact('pinjaman'));
        // return $pdf->download('data-pinjaman.pdf');
        
        return response('Export PDF Data Pinjaman');
    }
}