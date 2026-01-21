<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\DataMaster\JenisSimpanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasAnggotaController extends Controller
{
    /**
     * Display a listing of the resource with complete financial data
     */
    public function index(Request $request)
    {
        $query = DataAnggota::where('aktif', 'Aktif');

        // Filter by Anggota ID
        if ($request->filled('anggota')) {
            $query->where('id_anggota', $request->anggota);
        }

        // Filter by Jabatan
        if ($request->filled('jabatan')) {
            $query->where('jabatan', $request->jabatan);
        }

        $anggotaList = $query->orderBy('nama', 'asc')->get();

        // Build kas anggota data
        $kasAnggota = $anggotaList->map(function ($anggota) {
            // === HITUNG SIMPANAN ===
            $setoranTunai = SetoranTunai::where('anggota_id', $anggota->id)
                ->with('jenisSimpanan')
                ->get();

            $simpananSukarela = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Sukarela')
                ->sum('jumlah');

            $simpananPokok = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Pokok')
                ->sum('jumlah');

            $simpananWajib = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Wajib')
                ->sum('jumlah');

            $simpananLainnya = $setoranTunai
                ->whereNotIn('jenisSimpanan.jenis_simpanan', [
                    'Simpanan Sukarela',
                    'Simpanan Pokok',
                    'Simpanan Wajib'
                ])
                ->sum('jumlah');

            // === HITUNG KREDIT/PINJAMAN ===
            // Ambil semua pinjaman anggota (aktif dan lunas)
            $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
                ->whereNull('deleted_at')
                ->get();

            $pokokPinjaman = $pinjaman->sum('pokok_pinjaman');

            // Total tagihan = total jumlah angsuran dari semua pinjaman
            $totalTagihan = $pinjaman->sum('jumlah_angsuran');

            // Total yang sudah dibayar dari detail_bayar_angsuran
            $totalDibayar = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            // Total denda
            $totalDenda = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->whereNull('deleted_at')
                ->sum('denda');

            // Sisa tagihan
            $sisaTagihan = $totalTagihan - $totalDibayar;

            // === KETERANGAN ===
            $jumlahPinjaman = $pinjaman->count();
            $pinjamanLunas = $pinjaman->where('status_lunas', 'Lunas')->count();

            // Status pembayaran berdasarkan angsuran yang menunggak
            $angsuranTerlambat = BayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->where('status_bayar', 'Belum')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->whereNull('deleted_at')
                ->count();

            $statusPembayaran = $angsuranTerlambat > 0 ? 'Macet' : 'Lancar';

            // Tanggal tempo terdekat
            $tanggalTempo = BayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->where('status_bayar', 'Belum')
                ->whereNull('deleted_at')
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->first();

            return (object)[
                'id' => $anggota->id,
                'id_anggota' => $anggota->id_anggota,
                'nama' => $anggota->nama,
                'jenis_kelamin' => $anggota->jenis_kelamin,
                'jabatan' => $anggota->jabatan,
                'departemen' => $anggota->departement ?? '-',
                'alamat' => $anggota->alamat,
                'no_telepon' => $anggota->no_telp ?? '-',
                'foto' => $anggota->photo 
                    ? asset('storage/' . $anggota->photo)
                    : asset('assets/images/profile/user-1.jpg'),

                // Simpanan
                'simpanan' => [
                    'sukarela' => $simpananSukarela,
                    'pokok' => $simpananPokok,
                    'wajib' => $simpananWajib,
                    'lainnya' => $simpananLainnya,
                ],

                // Kredit
                'kredit' => [
                    'pokok_pinjaman' => $pokokPinjaman,
                    'tagihan_denda' => $totalTagihan + $totalDenda,
                    'dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan > 0 ? $sisaTagihan : 0,
                ],

                // Keterangan
                'keterangan' => [
                    'jumlah_pinjaman' => $jumlahPinjaman,
                    'pinjaman_lunas' => $pinjamanLunas,
                    'status_pembayaran' => $statusPembayaran,
                    'tanggal_tempo' => $tanggalTempo 
                        ? Carbon::parse($tanggalTempo->tanggal_jatuh_tempo)->translatedFormat('d M Y')
                        : '-',
                ],
            ];
        });

        // Filter by Status Pembayaran (setelah data terbuild)
        if ($request->filled('status')) {
            $kasAnggota = $kasAnggota->filter(function ($item) use ($request) {
                return $item->keterangan['status_pembayaran'] === $request->status;
            });
        }

        // Notifications - Anggota dengan angsuran jatuh tempo dalam 7 hari
        $notifications = BayarAngsuran::with(['pinjaman.anggota'])
            ->where('status_bayar', 'Belum')
            ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)])
            ->whereNull('deleted_at')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'nama' => $item->pinjaman->anggota->nama ?? 'Unknown',
                    'tanggal_jatuh_tempo' => Carbon::parse($item->tanggal_jatuh_tempo)->format('Y-m-d'),
                    'sisa_tagihan' => $item->jumlah_angsuran - $item->jumlah_bayar,
                ];
            });

        return view('admin.laporan.KasAnggota.KasAnggota', compact('kasAnggota', 'notifications'));
    }

    /**
     * Print laporan kas anggota with filters
     */
    public function cetakLaporan(Request $request)
    {
        $anggota = $request->get('anggota', '');
        $status = $request->get('status', '');
        $jabatan = $request->get('jabatan', '');

        // Reuse the same logic from index
        $query = DataAnggota::where('aktif', 'Aktif');

        if ($anggota) {
            $query->where('id_anggota', $anggota);
        }

        if ($jabatan) {
            $query->where('jabatan', $jabatan);
        }

        $anggotaList = $query->orderBy('nama', 'asc')->get();

        $kasAnggota = $anggotaList->map(function ($anggota) {
            // Same calculation logic as index
            $setoranTunai = SetoranTunai::where('anggota_id', $anggota->id)
                ->with('jenisSimpanan')
                ->get();

            $simpananSukarela = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Sukarela')
                ->sum('jumlah');

            $simpananPokok = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Pokok')
                ->sum('jumlah');

            $simpananWajib = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Wajib')
                ->sum('jumlah');

            $simpananLainnya = $setoranTunai
                ->whereNotIn('jenisSimpanan.jenis_simpanan', [
                    'Simpanan Sukarela',
                    'Simpanan Pokok',
                    'Simpanan Wajib'
                ])
                ->sum('jumlah');

            $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
                ->whereNull('deleted_at')
                ->get();

            $pokokPinjaman = $pinjaman->sum('pokok_pinjaman');
            $totalTagihan = $pinjaman->sum('jumlah_angsuran');

            $totalDibayar = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            $totalDenda = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->whereNull('deleted_at')
                ->sum('denda');

            $sisaTagihan = $totalTagihan - $totalDibayar;

            $jumlahPinjaman = $pinjaman->count();
            $pinjamanLunas = $pinjaman->where('status_lunas', 'Lunas')->count();

            $angsuranTerlambat = BayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->where('status_bayar', 'Belum')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->whereNull('deleted_at')
                ->count();

            $statusPembayaran = $angsuranTerlambat > 0 ? 'Macet' : 'Lancar';

            $tanggalTempo = BayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->where('status_bayar', 'Belum')
                ->whereNull('deleted_at')
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->first();

            return (object)[
                'id_anggota' => $anggota->id_anggota,
                'nama' => $anggota->nama,
                'jenis_kelamin' => $anggota->jenis_kelamin,
                'jabatan' => $anggota->jabatan,
                'departemen' => $anggota->departement ?? '-',
                'alamat' => $anggota->alamat,
                'no_telepon' => $anggota->no_telp ?? '-',
                'foto' => $anggota->photo 
                    ? asset('storage/' . $anggota->photo)
                    : asset('assets/images/profile/user-1.jpg'),
                'simpanan' => [
                    'sukarela' => $simpananSukarela,
                    'pokok' => $simpananPokok,
                    'wajib' => $simpananWajib,
                    'lainnya' => $simpananLainnya,
                ],
                'kredit' => [
                    'pokok_pinjaman' => $pokokPinjaman,
                    'tagihan_denda' => $totalTagihan + $totalDenda,
                    'dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan > 0 ? $sisaTagihan : 0,
                ],
                'keterangan' => [
                    'jumlah_pinjaman' => $jumlahPinjaman,
                    'pinjaman_lunas' => $pinjamanLunas,
                    'status_pembayaran' => $statusPembayaran,
                    'tanggal_tempo' => $tanggalTempo 
                        ? Carbon::parse($tanggalTempo->tanggal_jatuh_tempo)->translatedFormat('d M Y')
                        : '-',
                ],
            ];
        });

        // Filter by status after building
        if ($status) {
            $kasAnggota = $kasAnggota->filter(function ($item) use ($status) {
                return $item->keterangan['status_pembayaran'] === $status;
            });
        }

        return view('admin.laporan.KasAnggota.CetakKasAnggota', compact(
            'kasAnggota',
            'anggota',
            'status',
            'jabatan'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $includePhoto = $request->get('photo', false);
        
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new KasAnggotaExport($includePhoto), 'kas-anggota-' . date('Y-m-d') . '.xlsx');
        
        return response('Export Excel Data Kas Anggota');
    }
}