<?php

namespace App\Http\Controllers\User\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\Simpanan\PenarikanTunai;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaporanUserController extends Controller
{
    /**
     * Get anggota_id from current user
     */
    private function getAnggotaId()
    {
        $user = Auth::user();
        
        // MAPPING USER ID → ANGGOTA ID
        // Sesuaikan dengan data di database Anda
        $userToAnggota = [
            1 => 1, // user id 1 = anggota id 1
            2 => 7, // user id 2 = anggota id 7
            // tambahkan mapping lainnya sesuai kebutuhan
        ];
        
        return $userToAnggota[$user->id] ?? null;
    }

    /**
     * Laporan Simpanan
     */
    public function simpanan()
    {
        $anggotaId = $this->getAnggotaId();

        if (!$anggotaId) {
            return view('user.Laporan.Simpanan.Simpanan')->with([
                'simpanan' => collect([]),
                'summary' => [
                    'jumlah_transaksi' => 0,
                    'total_setoran' => 0,
                    'total_penarikan' => 0,
                    'saldo_akhir' => 0,
                ]
            ]);
        }

        // Get data setoran (simpanan)
        $setoran = SetoranTunai::with(['jenisSimpanan'])
            ->where('anggota_id', $anggotaId)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis' => $item->jenisSimpanan->jenis_simpanan ?? 'Simpanan',
                    'jumlah' => $item->jumlah,
                    'keterangan' => $item->keterangan ?? 'Setoran ' . ($item->jenisSimpanan->jenis_simpanan ?? 'Simpanan'),
                    'tipe' => 'simpanan'
                ];
            });

        // Get data penarikan
        $penarikan = PenarikanTunai::with(['jenisSimpanan'])
            ->where('anggota_id', $anggotaId)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis' => 'Penarikan',
                    'jumlah' => $item->jumlah,
                    'keterangan' => $item->keterangan ?? 'Penarikan ' . ($item->jenisSimpanan->jenis_simpanan ?? 'Simpanan'),
                    'tipe' => 'penarikan'
                ];
            });

        // Gabungkan setoran dan penarikan
        $simpanan = $setoran->merge($penarikan)->sortByDesc('tanggal')->values();

        // Summary data
        $totalSetoran = $setoran->sum('jumlah');
        $totalPenarikan = $penarikan->sum('jumlah');
        $saldoAkhir = $totalSetoran - $totalPenarikan;

        $summary = [
            'jumlah_transaksi' => $simpanan->count(),
            'total_setoran' => $totalSetoran,
            'total_penarikan' => $totalPenarikan,
            'saldo_akhir' => $saldoAkhir,
        ];

        return view('user.Laporan.Simpanan.Simpanan', compact('simpanan', 'summary'));
    }

    /**
     * Laporan Pinjaman
     */
    public function pinjaman()
    {
        $anggotaId = $this->getAnggotaId();

        if (!$anggotaId) {
            return view('user.Laporan.Pinjaman.Pinjaman')->with([
                'pinjaman' => collect([]),
                'summary' => [
                    'total_pinjaman' => 0,
                    'sudah_lunas' => 0,
                    'belum_lunas' => 0,
                    'sisa_tagihan' => 0,
                ]
            ]);
        }

        // Get pinjaman untuk user ini
        $pinjamanData = Pinjaman::with(['lamaAngsuran', 'angsuran'])
            ->where('anggota_id', $anggotaId)
            ->whereNull('deleted_at')
            ->orderBy('tanggal_pinjam', 'desc')
            ->get();

        $pinjaman = $pinjamanData->map(function ($item) {
            // Hitung angsuran per bulan
            $angsuranPerBulan = $item->angsuran_pokok + $item->biaya_bunga;
            
            // Hitung total tagihan
            $totalTagihan = $item->jumlah_angsuran;
            
            // Hitung sisa tagihan
            $sudahDibayar = $item->angsuran()
                ->where('status_bayar', 'Lunas')
                ->whereNull('deleted_at')
                ->sum('jumlah_bayar');
            
            $sisaTagihan = $totalTagihan - $sudahDibayar;
            
            // Tanggal jatuh tempo
            $jatuhTempo = Carbon::parse($item->tanggal_pinjam)
                ->addMonths($item->lamaAngsuran->lama_angsuran);

            return (object)[
                'id' => $item->id,
                'tanggal' => $item->tanggal_pinjam,
                'lama_angsuran' => $item->lamaAngsuran->lama_angsuran,
                'jumlah_pinjaman' => $item->pokok_pinjaman,
                'bunga' => $item->biaya_bunga * $item->lamaAngsuran->lama_angsuran,
                'persen_bunga' => $item->bunga_persen,
                'biaya_admin' => $item->biaya_admin,
                'angsuran_per_bulan' => $angsuranPerBulan,
                'total_tagihan' => $totalTagihan,
                'sisa_tagihan' => $sisaTagihan,
                'jatuh_tempo' => $jatuhTempo,
                'status_lunas' => $item->status_lunas === 'Lunas',
                'keterangan' => $item->keterangan ?? '-',
            ];
        });

        // Summary
        $totalPinjaman = $pinjaman->count();
        $sudahLunas = $pinjaman->where('status_lunas', true)->count();
        $belumLunas = $pinjaman->where('status_lunas', false)->count();
        $totalSisaTagihan = $pinjaman->where('status_lunas', false)->sum('sisa_tagihan');

        $summary = [
            'total_pinjaman' => $totalPinjaman,
            'sudah_lunas' => $sudahLunas,
            'belum_lunas' => $belumLunas,
            'sisa_tagihan' => $totalSisaTagihan,
        ];

        return view('user.Laporan.Pinjaman.Pinjaman', compact('pinjaman', 'summary'));
    }

    /**
     * Detail Pinjaman
     */
    public function detailPinjaman($id)
    {
        $anggotaId = $this->getAnggotaId();

        if (!$anggotaId) {
            return redirect()->route('user.laporan.pinjaman')
                ->with('error', 'Data anggota tidak ditemukan.');
        }

        // Get pinjaman detail (pastikan milik user ini)
        $pinjamanData = Pinjaman::with(['lamaAngsuran', 'angsuran'])
            ->where('id', $id)
            ->where('anggota_id', $anggotaId)
            ->whereNull('deleted_at')
            ->first();

        if (!$pinjamanData) {
            return redirect()->route('user.laporan.pinjaman')
                ->with('error', 'Data pinjaman tidak ditemukan.');
        }

        // Hitung total yang sudah dibayar
        $sudahDibayar = $pinjamanData->angsuran()
            ->where('status_bayar', 'Lunas')
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        $pinjaman = (object)[
            'id' => $pinjamanData->id,
            'tanggal' => $pinjamanData->tanggal_pinjam,
            'lama_angsuran' => $pinjamanData->lamaAngsuran->lama_angsuran,
            'jumlah_pinjaman' => $pinjamanData->pokok_pinjaman,
            'bunga' => $pinjamanData->biaya_bunga * $pinjamanData->lamaAngsuran->lama_angsuran,
            'persen_bunga' => $pinjamanData->bunga_persen,
            'biaya_admin' => $pinjamanData->biaya_admin,
            'angsuran_per_bulan' => $pinjamanData->angsuran_pokok + $pinjamanData->biaya_bunga,
            'total_tagihan' => $pinjamanData->jumlah_angsuran,
            'sisa_tagihan' => $pinjamanData->jumlah_angsuran - $sudahDibayar,
            'jatuh_tempo' => Carbon::parse($pinjamanData->tanggal_pinjam)
                ->addMonths($pinjamanData->lamaAngsuran->lama_angsuran),
            'status_lunas' => $pinjamanData->status_lunas === 'Lunas',
            'keterangan' => $pinjamanData->keterangan ?? '-',
        ];

        // Get detail angsuran per bulan
        $angsuranData = BayarAngsuran::where('pinjaman_id', $id)
            ->whereNull('deleted_at')
            ->orderBy('angsuran_ke', 'asc')
            ->get();

        $angsuran = $angsuranData->map(function ($item) use ($pinjamanData) {
            // Biaya admin hanya dikenakan pada angsuran pertama
            $biayaAdmin = ($item->angsuran_ke == 1) ? $pinjamanData->biaya_admin : 0;
            
            return (object)[
                'bulan_ke' => $item->angsuran_ke,
                'angsuran_pokok' => $pinjamanData->angsuran_pokok,
                'angsuran_bunga' => $pinjamanData->biaya_bunga,
                'biaya_admin' => $biayaAdmin,
                'jumlah_angsuran' => $item->jumlah_angsuran + $biayaAdmin,
                'tanggal_tempo' => $item->tanggal_jatuh_tempo,
                'status' => $item->status_bayar,
            ];
        });

        // Total untuk tfoot
        $total = (object)[
            'angsuran_pokok' => $pinjamanData->angsuran_pokok * $pinjamanData->lamaAngsuran->lama_angsuran,
            'angsuran_bunga' => $pinjamanData->biaya_bunga * $pinjamanData->lamaAngsuran->lama_angsuran,
            'biaya_admin' => $pinjamanData->biaya_admin,
            'jumlah_angsuran' => $pinjamanData->jumlah_angsuran,
        ];

        return view('user.Laporan.Pinjaman.DetailPinjamanUser', compact('pinjaman', 'angsuran', 'total'));
    }

    /**
     * Laporan Pembayaran
     */
    public function pembayaran()
    {
        $anggotaId = $this->getAnggotaId();

        if (!$anggotaId) {
            return view('user.Laporan.Pembayaran.Pembayaran')->with([
                'pembayaran' => collect([]),
                'summary' => [
                    'total_pembayaran' => 0,
                    'total_dibayar' => 0,
                    'total_denda' => 0,
                    'bulan_ini' => 0,
                ]
            ]);
        }

        // Get all pembayaran dari user ini
        $pembayaranData = DetailBayarAngsuran::with(['pinjaman', 'angsuran'])
            ->whereHas('pinjaman', function ($query) use ($anggotaId) {
                $query->where('anggota_id', $anggotaId)
                    ->whereNull('deleted_at');
            })
            ->whereNull('deleted_at')
            ->orderBy('tanggal_bayar', 'desc')
            ->get();

        $pembayaran = $pembayaranData->map(function ($item) {
            return (object)[
                'id' => $item->id,
                'tanggal' => $item->tanggal_bayar,
                'jenis' => 'Pembayaran Angsuran',
                'angsuran_ke' => $item->angsuran_ke,
                'denda' => $item->denda ?? 0,
                'jumlah_bayar' => $item->jumlah_bayar,
                'keterangan' => $item->keterangan ?? 'Pembayaran angsuran bulan ke-' . $item->angsuran_ke,
            ];
        });

        // Summary
        $totalPembayaran = $pembayaran->count();
        $totalDibayar = $pembayaran->sum('jumlah_bayar');
        $totalDenda = $pembayaran->sum('denda');
        
        // Pembayaran bulan ini
        $bulanIni = $pembayaran->filter(function ($item) {
            return Carbon::parse($item->tanggal)->isCurrentMonth();
        })->sum('jumlah_bayar');

        $summary = [
            'total_pembayaran' => $totalPembayaran,
            'total_dibayar' => $totalDibayar,
            'total_denda' => $totalDenda,
            'bulan_ini' => $bulanIni,
        ];

        return view('user.Laporan.Pembayaran.Pembayaran', compact('pembayaran', 'summary'));
    }
}