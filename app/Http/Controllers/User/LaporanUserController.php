<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanUserController extends Controller
{
    /**
     * Laporan Simpanan
     */
    public function simpanan()
    {
        // Dummy data simpanan
        $simpanan = collect([
            (object)[
                'id' => 1,
                'tanggal' => '2025-12-20',
                'jenis' => 'Simpanan Wajib',
                'jumlah' => 500000,
                'keterangan' => 'Setoran simpanan wajib bulan Desember 2025',
            ],
            (object)[
                'id' => 2,
                'tanggal' => '2025-12-15',
                'jenis' => 'Simpanan Sukarela',
                'jumlah' => 2000000,
                'keterangan' => 'Setoran sukarela untuk tabungan',
            ],
            (object)[
                'id' => 3,
                'tanggal' => '2025-12-10',
                'jenis' => 'Penarikan',
                'jumlah' => 1000000,
                'keterangan' => 'Penarikan simpanan sukarela',
            ],
            (object)[
                'id' => 4,
                'tanggal' => '2025-12-05',
                'jenis' => 'Simpanan Sukarela',
                'jumlah' => 1500000,
                'keterangan' => 'Setoran tambahan simpanan sukarela',
            ],
        ]);

        // Dummy summary data
        $summary = [
            'jumlah_peminjam' => 9,
            'peminjam_lunas' => 3,
            'belum_lunas' => 6,
        ];

        return view('user.Laporan.Simpanan.Simpanan', compact('simpanan', 'summary'));
    }

    /**
     * Laporan Pinjaman
     */
    public function pinjaman()
    {
        // Dummy data pinjaman
        $pinjaman = collect([
            (object)[
                'id' => 1,
                'tanggal' => '2025-12-15 10:30:00',
                'lama_angsuran' => 6,
                'jumlah_pinjaman' => 2600000,
                'bunga' => 130000,
                'persen_bunga' => 5,
                'biaya_admin' => 0,
                'angsuran_per_bulan' => 455000,
                'total_tagihan' => 2730000,
                'sisa_tagihan' => 1575000,
                'jatuh_tempo' => '2026-06-15',
                'status_lunas' => false,
                'keterangan' => 'Pinjaman Dana Tunai untuk kebutuhan usaha',
            ],
            (object)[
                'id' => 2,
                'tanggal' => '2025-11-10 14:15:00',
                'lama_angsuran' => 12,
                'jumlah_pinjaman' => 5000000,
                'bunga' => 250000,
                'persen_bunga' => 5,
                'biaya_admin' => 50000,
                'angsuran_per_bulan' => 441667,
                'total_tagihan' => 5300000,
                'sisa_tagihan' => 1575000,
                'jatuh_tempo' => '2026-11-10',
                'status_lunas' => false,
                'keterangan' => 'Pinjaman untuk renovasi rumah',
            ],
        ]);

        // Dummy summary
        $summary = [
            'total_pinjaman' => 3,
            'sudah_lunas' => 1,
            'belum_lunas' => 2,
            'sisa_tagihan' => 3150000,
        ];

        return view('user.Laporan.Pinjaman.Pinjaman', compact('pinjaman', 'summary'));
    }

    /**
     * Detail Pinjaman
     */
    public function detailPinjaman($id)
    {
        // Dummy data pinjaman (sesuai ID)
        $pinjaman = (object)[
            'id' => $id,
            'tanggal' => '2025-12-15 10:30:00',
            'lama_angsuran' => 6,
            'jumlah_pinjaman' => 2600000,
            'bunga' => 130000,
            'persen_bunga' => 5,
            'biaya_admin' => 0,
            'angsuran_per_bulan' => 455000,
            'total_tagihan' => 2730000,
            'sisa_tagihan' => 1365000,
            'jatuh_tempo' => '2026-06-15',
            'status_lunas' => false,
            'keterangan' => 'Pinjaman Dana Tunai untuk kebutuhan usaha',
        ];

        // Dummy data angsuran per bulan
        $angsuran = collect([
            (object)[
                'bulan_ke' => 1,
                'angsuran_pokok' => 433333,
                'angsuran_bunga' => 21667,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 455000,
                'tanggal_tempo' => '2026-01-15',
                'status' => 'Lunas',
            ],
            (object)[
                'bulan_ke' => 2,
                'angsuran_pokok' => 433333,
                'angsuran_bunga' => 21667,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 455000,
                'tanggal_tempo' => '2026-02-15',
                'status' => 'Lunas',
            ],
            (object)[
                'bulan_ke' => 3,
                'angsuran_pokok' => 433333,
                'angsuran_bunga' => 21667,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 455000,
                'tanggal_tempo' => '2026-03-15',
                'status' => 'Lunas',
            ],
            (object)[
                'bulan_ke' => 4,
                'angsuran_pokok' => 433333,
                'angsuran_bunga' => 21667,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 455000,
                'tanggal_tempo' => '2026-04-15',
                'status' => 'Belum Bayar',
            ],
            (object)[
                'bulan_ke' => 5,
                'angsuran_pokok' => 433333,
                'angsuran_bunga' => 21667,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 455000,
                'tanggal_tempo' => '2026-05-15',
                'status' => 'Belum Bayar',
            ],
            (object)[
                'bulan_ke' => 6,
                'angsuran_pokok' => 433335,
                'angsuran_bunga' => 21665,
                'biaya_admin' => 0,
                'jumlah_angsuran' => 455000,
                'tanggal_tempo' => '2026-06-15',
                'status' => 'Belum Bayar',
            ],
        ]);

        // Total untuk tfoot
        $total = (object)[
            'angsuran_pokok' => 2600000,
            'angsuran_bunga' => 130000,
            'biaya_admin' => 0,
            'jumlah_angsuran' => 2730000,
        ];

        return view('user.Laporan.Pinjaman.DetailPinjamanUser', compact('pinjaman', 'angsuran', 'total'));
    }

    /**
     * Laporan Pembayaran
     */
    public function pembayaran()
    {
        // Dummy data pembayaran
        $pembayaran = collect([
            (object)[
                'id' => 1,
                'tanggal' => '2025-12-20',
                'jenis' => 'Pembayaran Angsuran',
                'angsuran_ke' => 3,
                'denda' => 0,
                'jumlah_bayar' => 455000,
                'keterangan' => 'Pembayaran angsuran pinjaman PJ001 bulan ke-3',
            ],
            (object)[
                'id' => 2,
                'tanggal' => '2025-12-18',
                'jenis' => 'Pembayaran Angsuran',
                'angsuran_ke' => 2,
                'denda' => 25000,
                'jumlah_bayar' => 466667,
                'keterangan' => 'Pembayaran angsuran pinjaman PJ002 bulan ke-2 (terlambat 5 hari)',
            ],
            (object)[
                'id' => 3,
                'tanggal' => '2025-12-15',
                'jenis' => 'Pembayaran Angsuran',
                'angsuran_ke' => 2,
                'denda' => 0,
                'jumlah_bayar' => 455000,
                'keterangan' => 'Pembayaran angsuran pinjaman PJ001 bulan ke-2',
            ],
        ]);

        // Dummy summary
        $summary = [
            'total_pembayaran' => 12,
            'total_dibayar' => 5460000,
            'total_denda' => 50000,
            'bulan_ini' => 910000,
        ];

        return view('user.Laporan.Pembayaran.Pembayaran', compact('pembayaran', 'summary'));
    }
}