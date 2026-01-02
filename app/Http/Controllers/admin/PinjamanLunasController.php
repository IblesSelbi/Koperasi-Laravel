<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PinjamanLunasController extends Controller
{
    /**
     * Display a listing of paid off loans
     */
    public function index(Request $request)
    {
        // Dummy data untuk pinjaman lunas
        $pinjamanLunas = collect([
            (object)[
                'id' => 1,
                'kode' => 'TPJ001',
                'tanggal_pinjam' => '2025-12-07 09:00:00',
                'tanggal_tempo' => '2026-12-07',
                'anggota_id' => 'anggota',
                'anggota_nama' => 'Widi Aljatsiyah',
                'anggota_departemen' => '-',
                'anggota_foto' => 'assets/images/profile/user-2.jpg',
                'lama_pinjaman' => 12,
                'total_tagihan' => 4160400,
                'total_denda' => 0,
                'sudah_dibayar' => 4160400,
                'status_lunas' => 'Lunas',
                'user' => 'admin',
            ],
            (object)[
                'id' => 2,
                'kode' => 'TPJ002',
                'tanggal_pinjam' => '2025-12-14 10:30:00',
                'tanggal_tempo' => '2026-06-14',
                'anggota_id' => '001235',
                'anggota_nama' => 'Siti Aminah',
                'anggota_departemen' => 'Keuangan',
                'anggota_foto' => 'assets/images/profile/user-3.jpg',
                'lama_pinjaman' => 6,
                'total_tagihan' => 5300000,
                'total_denda' => 0,
                'sudah_dibayar' => 5300000,
                'status_lunas' => 'Lunas',
                'user' => 'admin',
            ]
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.lunas.PinjamanLunas', compact('pinjamanLunas', 'notifications'));
    }

    /**
     * Show the detail of a specific paid off loan
     */
    public function show($id)
    {
        // Dummy data detail pinjaman lunas
        $pinjaman = (object)[
            'id' => $id,
            'kode' => 'TPJ00016',
            'tanggal_pinjam' => '2025-05-16',
            'tanggal_tempo' => '2025-08-16',
            'lama_pinjaman' => 3,
            'anggota_id' => 'masud',
            'anggota_nama' => 'masud',
            'anggota_departemen' => 'Produksi BOPP',
            'anggota_ttl' => 'kotabaru, 02 Mei 2015',
            'anggota_kota' => 'kotabaru',
            'anggota_foto' => 'assets/images/profile/user-2.jpg',
            'pokok_pinjaman' => 2000000,
            'angsuran_pokok' => 666667,
            'biaya_bunga' => 100000,
            'jumlah_angsuran' => 766700,
            'sudah_dibayar' => 2300100,
            'jumlah_denda' => 0,
            'sisa_tagihan' => 0,
            'status_lunas' => 'Lunas',
        ];

        // Simulasi tagihan
        $simulasi = collect([
            (object)[
                'bulan_ke' => 1,
                'angsuran_pokok' => 666667,
                'angsuran_bunga' => 100000,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 766700,
                'tanggal_tempo' => '15 Juni 2025',
                'status' => 'Lunas'
            ],
            (object)[
                'bulan_ke' => 2,
                'angsuran_pokok' => 666667,
                'angsuran_bunga' => 100000,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 766700,
                'tanggal_tempo' => '15 Juli 2025',
                'status' => 'Lunas'
            ],
            (object)[
                'bulan_ke' => 3,
                'angsuran_pokok' => 666667,
                'angsuran_bunga' => 100000,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 766700,
                'tanggal_tempo' => '15 Agustus 2025',
                'status' => 'Lunas'
            ]
        ]);

        // Detail transaksi pembayaran
        $transaksi = collect([
            (object)[
                'no' => 1,
                'kode_bayar' => 'TBY00025',
                'tanggal_bayar' => '2025-05-16',
                'angsuran_ke' => 1,
                'jenis_pembayaran' => 'Angsuran',
                'jumlah_bayar' => 766700,
                'denda' => 0,
                'user' => 'admin'
            ],
            (object)[
                'no' => 2,
                'kode_bayar' => 'TBY00026',
                'tanggal_bayar' => '2025-06-16',
                'angsuran_ke' => 2,
                'jenis_pembayaran' => 'Angsuran',
                'jumlah_bayar' => 766700,
                'denda' => 0,
                'user' => 'admin'
            ],
            (object)[
                'no' => 3,
                'kode_bayar' => 'TBY00027',
                'tanggal_bayar' => '2025-07-16',
                'angsuran_ke' => 3,
                'jenis_pembayaran' => 'Pelunasan',
                'jumlah_bayar' => 766700,
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

        return view('admin.pinjaman.DetailPinjamanLunas', compact('pinjaman', 'simulasi', 'transaksi', 'notifications'));
    }

    /**
     * Print single pinjaman lunas detail
     */
    public function cetakDetail($id)
    {
        // TODO: Get data and generate print view
        $pinjaman = (object)[
            'kode' => 'TPJ00016',
            'tanggal_pinjam' => '2025-05-16',
            'anggota_nama' => 'masud',
            'jumlah' => 2000000,
        ];
        
        return view('admin.pinjaman.cetak_detail_lunas', compact('pinjaman'));
    }

    /**
     * Print laporan pinjaman lunas
     */
    public function cetakLaporan(Request $request)
    {
        $kode = $request->get('kode', '');
        $nama = $request->get('nama', '');
        $tanggal = $request->get('tanggal', '');

        // Sample data for print view
        $pinjamanLunas = collect([
            (object)[
                'kode' => 'TPJ001',
                'tanggal_pinjam' => '2025-12-07',
                'anggota_nama' => 'Widi Aljatsiyah',
                'total_tagihan' => 4160400,
                'status_lunas' => 'Lunas',
            ],
        ]);
        
        return view('admin.pinjaman.cetak_laporan_lunas', compact('pinjamanLunas', 'kode', 'nama', 'tanggal'));
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export using Laravel Excel
        return response('Export Excel Data Pinjaman Lunas');
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        // TODO: Implement PDF export using DomPDF or similar
        return response('Export PDF Data Pinjaman Lunas');
    }
}