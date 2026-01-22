<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\Simpanan\PenarikanTunai;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\Setting\SukuBunga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SHUController extends Controller
{
    /**
     * Display laporan SHU
     */
    public function index(Request $request)
    {
        // Default date range (tahun berjalan)
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));
        $anggotaId = $request->get('anggota_id', '');

        try {
            // Ambil setting SHU dari database
            $sukuBunga = SukuBunga::getSetting();

            // Hitung Pendapatan
            $pendapatan = $this->hitungPendapatan($tglDari, $tglSamp, $anggotaId);
            
            // Hitung Beban
            $beban = $this->hitungBeban($tglDari, $tglSamp, $anggotaId);

            // SHU Sebelum Pajak = Total Pendapatan - Total Beban
            $shuSebelumPajak = $pendapatan['total'] - $beban['total'];

            // Pajak PPh (ambil dari setting, default 5%)
            $persenPajak = $sukuBunga->pjk_pph ?? 5;
            $pajakPPh = $shuSebelumPajak * ($persenPajak / 100);

            // SHU Setelah Pajak
            $shuSetelahPajak = $shuSebelumPajak - $pajakPPh;

            // Pembagian SHU untuk Dana-dana (ambil dari setting)
            $danaCadangan = $shuSetelahPajak * (($sukuBunga->dana_cadangan ?? 40) / 100);
            $jasaAnggota = $shuSetelahPajak * (($sukuBunga->jasa_anggota ?? 40) / 100);
            $danaPengurus = $shuSetelahPajak * (($sukuBunga->dana_pengurus ?? 5) / 100);
            $danaKaryawan = $shuSetelahPajak * (($sukuBunga->dana_karyawan ?? 5) / 100);
            $danaPendidikan = $shuSetelahPajak * (($sukuBunga->dana_pend ?? 5) / 100);
            $danaSosial = $shuSetelahPajak * (($sukuBunga->dana_sosial ?? 5) / 100);

            // Pembagian SHU Anggota
            $persenJasaUsaha = $sukuBunga->jasa_usaha ?? 70;
            $persenJasaModal = $sukuBunga->jasa_modal ?? 30;
            
            $jasaUsaha = $jasaAnggota * ($persenJasaUsaha / 100);
            $jasaModal = $jasaAnggota * ($persenJasaModal / 100);

            // Total Pendapatan Anggota & Total Simpanan Anggota
            if ($anggotaId) {
                $totalPendapatanAnggota = $this->hitungPendapatanAnggota($tglDari, $tglSamp, $anggotaId);
                $totalSimpananAnggota = $this->hitungSimpananAnggota($tglDari, $tglSamp, $anggotaId);
            } else {
                $totalPendapatanAnggota = $pendapatan['total'];
                $totalSimpananAnggota = $this->hitungTotalSimpanan($tglDari, $tglSamp);
            }

            // Data anggota untuk dropdown
            $anggotaList = DataAnggota::where('aktif', 'Aktif')
                ->orderBy('nama', 'asc')
                ->get();

            // Notifications
            $notifications = collect([
                (object)[
                    'nama' => 'Hartati',
                    'tanggal_jatuh_tempo' => '2025-06-16',
                    'sisa_tagihan' => 1575000,
                ]
            ]);

            return view('admin.Laporan.shu.SHU', compact(
                'anggotaList',
                'shuSebelumPajak',
                'pajakPPh',
                'shuSetelahPajak',
                'danaCadangan',
                'jasaAnggota',
                'danaPengurus',
                'danaKaryawan',
                'danaPendidikan',
                'danaSosial',
                'jasaUsaha',
                'jasaModal',
                'totalPendapatanAnggota',
                'totalSimpananAnggota',
                'notifications',
                'tglDari',
                'tglSamp',
                'anggotaId',
                'pendapatan',
                'beban'
            ));

        } catch (\Exception $e) {
            Log::error('Error calculating SHU: ' . $e->getMessage());
            
            return back()->with('error', 'Terjadi kesalahan saat menghitung SHU: ' . $e->getMessage());
        }
    }

    /**
     * Hitung Total Pendapatan
     */
    private function hitungPendapatan($tglDari, $tglSamp, $anggotaId = null)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        // 1. Pendapatan dari Bunga Pinjaman (DetailBayarAngsuran)
        $queryBunga = DetailBayarAngsuran::with('angsuran.pinjaman')
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at');

        if ($anggotaId) {
            $queryBunga->whereHas('pinjaman', function($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            });
        }

        $pendapatanBunga = $queryBunga->get()->sum(function($item) {
            // Biaya bunga dari relasi pinjaman
            return $item->pinjaman->biaya_bunga ?? 0;
        });

        // 2. Pendapatan dari Denda
        $queryDenda = DetailBayarAngsuran::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at');

        if ($anggotaId) {
            $queryDenda->whereHas('pinjaman', function($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            });
        }

        $pendapatanDenda = $queryDenda->sum('denda');

        // 3. Pendapatan Lain-lain (dari tabel Pemasukan)
        $queryPemasukan = Pemasukan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at');

        $pendapatanLainLain = $queryPemasukan->sum('jumlah');

        $totalPendapatan = $pendapatanBunga + $pendapatanDenda + $pendapatanLainLain;

        return [
            'bunga' => $pendapatanBunga,
            'denda' => $pendapatanDenda,
            'lain_lain' => $pendapatanLainLain,
            'total' => $totalPendapatan
        ];
    }

    /**
     * Hitung Total Beban
     */
    private function hitungBeban($tglDari, $tglSamp, $anggotaId = null)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        // 1. Beban Operasional (dari tabel Pengeluaran)
        $queryPengeluaran = Pengeluaran::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at');

        $bebanOperasional = $queryPengeluaran->sum('jumlah');

        // 2. Beban Administrasi (dari pembayaran angsuran)
        $queryAdmin = DetailBayarAngsuran::with('pinjaman')
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at');

        if ($anggotaId) {
            $queryAdmin->whereHas('pinjaman', function($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            });
        }

        $bebanAdministrasi = $queryAdmin->get()->sum(function($item) {
            return $item->pinjaman->biaya_admin ?? 0;
        });

        $totalBeban = $bebanOperasional + $bebanAdministrasi;

        return [
            'operasional' => $bebanOperasional,
            'administrasi' => $bebanAdministrasi,
            'total' => $totalBeban
        ];
    }

    /**
     * Hitung Pendapatan Anggota Tertentu
     */
    private function hitungPendapatanAnggota($tglDari, $tglSamp, $anggotaId)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        // Total pembayaran angsuran dari anggota
        $totalBayar = DetailBayarAngsuran::whereHas('pinjaman', function($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            })
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        return $totalBayar;
    }

    /**
     * Hitung Total Simpanan Anggota Tertentu
     */
    private function hitungSimpananAnggota($tglDari, $tglSamp, $anggotaId)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        // Total setoran
        $totalSetoran = SetoranTunai::where('anggota_id', $anggotaId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        // Total penarikan
        $totalPenarikan = PenarikanTunai::where('anggota_id', $anggotaId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        return $totalSetoran - $totalPenarikan;
    }

    /**
     * Hitung Total Simpanan Semua Anggota
     */
    private function hitungTotalSimpanan($tglDari, $tglSamp)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        // Total setoran
        $totalSetoran = SetoranTunai::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        // Total penarikan
        $totalPenarikan = PenarikanTunai::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        return $totalSetoran - $totalPenarikan;
    }

    /**
     * Print laporan SHU
     */
    public function cetakLaporan(Request $request)
    {
        $anggotaId = $request->get('anggota_id', '');
        $tglDari = $request->get('tgl_dari', '');
        $tglSamp = $request->get('tgl_samp', '');

        try {
            // Ambil setting SHU dari database
            $sukuBunga = SukuBunga::getSetting();

            // Hitung ulang data untuk cetak
            $pendapatan = $this->hitungPendapatan($tglDari, $tglSamp, $anggotaId);
            $beban = $this->hitungBeban($tglDari, $tglSamp, $anggotaId);

            $shuSebelumPajak = $pendapatan['total'] - $beban['total'];
            $persenPajak = $sukuBunga->pjk_pph ?? 5;
            $pajakPPh = $shuSebelumPajak * ($persenPajak / 100);
            $shuSetelahPajak = $shuSebelumPajak - $pajakPPh;

            $danaCadangan = $shuSetelahPajak * (($sukuBunga->dana_cadangan ?? 40) / 100);
            $jasaAnggota = $shuSetelahPajak * (($sukuBunga->jasa_anggota ?? 40) / 100);
            $danaPengurus = $shuSetelahPajak * (($sukuBunga->dana_pengurus ?? 5) / 100);
            $danaKaryawan = $shuSetelahPajak * (($sukuBunga->dana_karyawan ?? 5) / 100);
            $danaPendidikan = $shuSetelahPajak * (($sukuBunga->dana_pend ?? 5) / 100);
            $danaSosial = $shuSetelahPajak * (($sukuBunga->dana_sosial ?? 5) / 100);

            $jasaUsaha = $jasaAnggota * (($sukuBunga->jasa_usaha ?? 70) / 100);
            $jasaModal = $jasaAnggota * (($sukuBunga->jasa_modal ?? 30) / 100);

            if ($anggotaId) {
                $totalPendapatanAnggota = $this->hitungPendapatanAnggota($tglDari, $tglSamp, $anggotaId);
                $totalSimpananAnggota = $this->hitungSimpananAnggota($tglDari, $tglSamp, $anggotaId);
                $anggota = DataAnggota::find($anggotaId);
            } else {
                $totalPendapatanAnggota = $pendapatan['total'];
                $totalSimpananAnggota = $this->hitungTotalSimpanan($tglDari, $tglSamp);
                $anggota = null;
            }

            return view('admin.Laporan.shu.cetak_shu', compact(
                'shuSebelumPajak',
                'pajakPPh',
                'shuSetelahPajak',
                'danaCadangan',
                'jasaAnggota',
                'danaPengurus',
                'danaKaryawan',
                'danaPendidikan',
                'danaSosial',
                'jasaUsaha',
                'jasaModal',
                'totalPendapatanAnggota',
                'totalSimpananAnggota',
                'tglDari',
                'tglSamp',
                'anggota',
                'pendapatan',
                'beban'
            ));

        } catch (\Exception $e) {
            Log::error('Error printing SHU: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan.');
        }
    }
}