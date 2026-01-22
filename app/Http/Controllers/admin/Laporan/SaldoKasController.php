<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\TransaksiKas\Transfer;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\Simpanan\PenarikanTunai;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaldoKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Default periode: bulan sekarang
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));
        
        // Parse periode
        $periodeCarbon = Carbon::parse($periode . '-01');
        $startDate = $periodeCarbon->copy()->startOfMonth();
        $endDate = $periodeCarbon->copy()->endOfMonth();
        
        // Periode sebelumnya (untuk saldo awal)
        $periodeSebelumnya = $periodeCarbon->copy()->subMonth()->endOfMonth();

        // Get all active kas
        $kasList = DataKas::where('aktif', 'Y')
            ->orderBy('nama_kas', 'asc')
            ->get();

        $saldoKas = collect();
        $no = 1;

        // Hitung saldo periode sebelumnya (saldo awal)
        $saldoPeriodeSebelumnya = 0;

        foreach ($kasList as $kas) {
            // Hitung saldo periode sebelumnya
            $saldoAwalKas = $this->hitungSaldoSampai($kas->id, $periodeSebelumnya);
            $saldoPeriodeSebelumnya += $saldoAwalKas;

            // Hitung mutasi periode ini
            $mutasiPeriode = $this->hitungMutasiPeriode($kas->id, $startDate, $endDate);
            
            // Saldo kas = Saldo awal + Mutasi periode
            $saldoKas->push((object)[
                'no' => $no++,
                'nama_kas' => $kas->nama_kas,
                'saldo' => $mutasiPeriode,
            ]);
        }

        // Calculate totals
        $jumlahSaldo = $saldoKas->sum('saldo');
        $totalSaldo = $saldoPeriodeSebelumnya + $jumlahSaldo;

        // Notifications - angsuran yang akan jatuh tempo
        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Format periode untuk display
        $periodeDisplay = $periodeCarbon->locale('id')->isoFormat('MMMM YYYY');

        return view('admin.Laporan.SaldoKas.SaldoKas', compact(
            'saldoKas',
            'saldoPeriodeSebelumnya',
            'jumlahSaldo',
            'totalSaldo',
            'notifications',
            'periode',
            'periodeDisplay'
        ));
    }

    /**
     * Hitung saldo kas sampai tanggal tertentu (untuk saldo awal)
     */
    private function hitungSaldoSampai($kasId, $tanggal)
    {
        $saldo = 0;

        // 1. PEMASUKAN (+)
        $pemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo += $pemasukan;

        // 2. PENGELUARAN (-)
        $pengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo -= $pengeluaran;

        // 3. TRANSFER MASUK (+)
        $transferMasuk = Transfer::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo += $transferMasuk;

        // 4. TRANSFER KELUAR (-)
        $transferKeluar = Transfer::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo -= $transferKeluar;

        // 5. SETORAN TUNAI (+)
        $setoran = SetoranTunai::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo += $setoran;

        // 6. PENARIKAN TUNAI (-)
        $penarikan = PenarikanTunai::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo -= $penarikan;

        // 7. PINJAMAN (-)
        $pinjaman = Pinjaman::where('dari_kas_id', $kasId)
            ->where('tanggal_pinjam', '<=', $tanggal)
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');
        $saldo -= $pinjaman;

        // 8. ANGSURAN (+)
        $angsuran = DetailBayarAngsuran::where('ke_kas_id', $kasId)
            ->where('tanggal_bayar', '<=', $tanggal)
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');
        $saldo += $angsuran;

        return $saldo;
    }

    /**
     * Hitung mutasi kas dalam periode tertentu
     */
    private function hitungMutasiPeriode($kasId, $startDate, $endDate)
    {
        $mutasi = 0;

        // 1. PEMASUKAN (+)
        $pemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi += $pemasukan;

        // 2. PENGELUARAN (-)
        $pengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi -= $pengeluaran;

        // 3. TRANSFER MASUK (+)
        $transferMasuk = Transfer::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi += $transferMasuk;

        // 4. TRANSFER KELUAR (-)
        $transferKeluar = Transfer::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi -= $transferKeluar;

        // 5. SETORAN TUNAI (+)
        $setoran = SetoranTunai::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi += $setoran;

        // 6. PENARIKAN TUNAI (-)
        $penarikan = PenarikanTunai::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi -= $penarikan;

        // 7. PINJAMAN (-)
        $pinjaman = Pinjaman::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');
        $mutasi -= $pinjaman;

        // 8. ANGSURAN (+)
        $angsuran = DetailBayarAngsuran::where('ke_kas_id', $kasId)
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');
        $mutasi += $angsuran;

        return $mutasi;
    }

    /**
     * Print laporan saldo kas
     */
    public function cetakLaporan(Request $request)
    {
        // Get periode from request
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));
        
        // Parse periode
        $periodeCarbon = Carbon::parse($periode . '-01');
        $startDate = $periodeCarbon->copy()->startOfMonth();
        $endDate = $periodeCarbon->copy()->endOfMonth();
        
        // Periode sebelumnya
        $periodeSebelumnya = $periodeCarbon->copy()->subMonth()->endOfMonth();

        // Get all active kas
        $kasList = DataKas::where('aktif', 'Y')
            ->orderBy('nama_kas', 'asc')
            ->get();

        $saldoKas = collect();
        $no = 1;

        // Hitung saldo periode sebelumnya
        $saldoPeriodeSebelumnya = 0;

        foreach ($kasList as $kas) {
            // Hitung saldo periode sebelumnya
            $saldoAwalKas = $this->hitungSaldoSampai($kas->id, $periodeSebelumnya);
            $saldoPeriodeSebelumnya += $saldoAwalKas;

            // Hitung mutasi periode ini
            $mutasiPeriode = $this->hitungMutasiPeriode($kas->id, $startDate, $endDate);
            
            $saldoKas->push((object)[
                'no' => $no++,
                'nama_kas' => $kas->nama_kas,
                'saldo' => $mutasiPeriode,
            ]);
        }

        // Calculate totals
        $jumlahSaldo = $saldoKas->sum('saldo');
        $totalSaldo = $saldoPeriodeSebelumnya + $jumlahSaldo;

        // Format periode untuk display
        $periodeDisplay = $periodeCarbon->locale('id')->isoFormat('MMMM YYYY');

        // Return view for printing
        return view('admin.Laporan.SaldoKas.CetakSaldoKas', compact(
            'saldoKas',
            'saldoPeriodeSebelumnya',
            'jumlahSaldo',
            'totalSaldo',
            'periode',
            'periodeDisplay'
        ));
    }

    /**
     * Get detail mutasi kas (optional - for debugging/detail view)
     */
    public function getDetailMutasi(Request $request, $kasId)
    {
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));
        $periodeCarbon = Carbon::parse($periode . '-01');
        $startDate = $periodeCarbon->copy()->startOfMonth();
        $endDate = $periodeCarbon->copy()->endOfMonth();

        $detail = [
            'pemasukan' => Pemasukan::where('untuk_kas_id', $kasId)
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->sum('jumlah'),
            
            'pengeluaran' => Pengeluaran::where('dari_kas_id', $kasId)
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->sum('jumlah'),
            
            'transfer_masuk' => Transfer::where('untuk_kas_id', $kasId)
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->sum('jumlah'),
            
            'transfer_keluar' => Transfer::where('dari_kas_id', $kasId)
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->sum('jumlah'),
            
            'setoran' => SetoranTunai::where('untuk_kas_id', $kasId)
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->sum('jumlah'),
            
            'penarikan' => PenarikanTunai::where('dari_kas_id', $kasId)
                ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
                ->sum('jumlah'),
            
            'pinjaman' => Pinjaman::where('dari_kas_id', $kasId)
                ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
                ->whereNull('deleted_at')
                ->sum('pokok_pinjaman'),
            
            'angsuran' => DetailBayarAngsuran::where('ke_kas_id', $kasId)
                ->whereBetween('tanggal_bayar', [$startDate, $endDate])
                ->whereNull('deleted_at')
                ->sum('jumlah_bayar'),
        ];

        return response()->json([
            'success' => true,
            'data' => $detail
        ]);
    }
}