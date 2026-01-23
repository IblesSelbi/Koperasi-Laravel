<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\Pinjaman\PengajuanPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil user yang login dengan relasi anggota
        $user = Auth::user();
        $user->load('anggota');

        Log::info('User Dashboard Access', [
            'user_id' => $user->id,
            'has_anggota' => $user->anggota ? true : false,
            'anggota_id' => $user->anggota ? $user->anggota->id : null
        ]);

        // GANTI REDIRECT DENGAN ABORT
        // Ini mencegah redirect loop
        if (!$user->anggota) {
            Log::warning('User tidak memiliki data anggota', ['user_id' => $user->id]);
            abort(403, 'Akun Anda belum terhubung dengan data anggota. Silakan hubungi administrator.');
        }

        $anggotaData = $user->anggota;

        // GANTI REDIRECT DENGAN ABORT
        if ($anggotaData->aktif !== 'Aktif') {
            Log::warning('Anggota tidak aktif', [
                'user_id' => $user->id,
                'anggota_id' => $anggotaData->id,
                'status' => $anggotaData->aktif
            ]);
            abort(403, 'Akun anggota Anda tidak aktif. Silakan hubungi administrator.');
        }

        // === DATA IDENTITAS ANGGOTA ===
        $anggota = [
            'id_anggota' => $anggotaData->id_anggota,
            'nama' => $anggotaData->nama,
            'gender' => $anggotaData->jenis_kelamin,
            'jabatan' => $anggotaData->jabatan ?? 'Anggota',
            'alamat' => $anggotaData->alamat,
            'no_telp' => $anggotaData->no_telp ?? '-',
            'foto' => $anggotaData->photo_url,
        ];

        // === PENGAJUAN PINJAMAN TERAKHIR ===
        $pengajuanTerakhir = PengajuanPinjaman::where('anggota_id', $anggotaData->id)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->first();

        $pengajuan_terakhir = null;
        if ($pengajuanTerakhir) {
            $statusMap = [
                0 => 'Menunggu',
                1 => 'Disetujui',
                2 => 'Ditolak',
                3 => 'Terlaksana',
                4 => 'Batal'
            ];
            
            $pengajuan_terakhir = [
                'tanggal' => Carbon::parse($pengajuanTerakhir->tanggal_pengajuan)
                    ->translatedFormat('d F Y \j\a\m H:i'),
                'nominal' => $pengajuanTerakhir->jumlah,
                'status' => $statusMap[$pengajuanTerakhir->status] ?? 'Menunggu',
                'keterangan' => $pengajuanTerakhir->alasan ?? 
                    ($pengajuanTerakhir->status === 1 && $pengajuanTerakhir->tanggal_cair
                        ? 'Cair ' . Carbon::parse($pengajuanTerakhir->tanggal_cair)->translatedFormat('d M Y')
                        : '-'),
            ];
        }

        // === HITUNG SIMPANAN ===
        $setoranTunai = SetoranTunai::where('anggota_id', $anggotaData->id)
            ->whereNull('deleted_at')
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

        $totalSimpanan = $simpananSukarela + $simpananPokok + $simpananWajib + $simpananLainnya;

        $simpanan = [
            'sukarela' => $simpananSukarela,
            'pokok' => $simpananPokok,
            'wajib' => $simpananWajib,
            'lainnya' => $simpananLainnya,
            'total' => $totalSimpanan,
        ];

        // === HITUNG KREDIT/PINJAMAN ===
        $pinjamanList = Pinjaman::where('anggota_id', $anggotaData->id)
            ->whereNull('deleted_at')
            ->get();

        $pokokPinjaman = $pinjamanList->sum('pokok_pinjaman');
        $totalTagihan = $pinjamanList->sum('jumlah_angsuran');

        // Total yang sudah dibayar
        $totalDibayar = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjamanList->pluck('id'))
            ->whereNull('deleted_at')
            ->sum('total_bayar');

        // Total denda
        $totalDenda = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjamanList->pluck('id'))
            ->whereNull('deleted_at')
            ->sum('denda');

        // Sisa tagihan
        $sisaTagihan = $totalTagihan - $totalDibayar;

        $pinjaman = [
            'pokok' => $pokokPinjaman,
            'tagihan_total' => $totalTagihan + $totalDenda,
            'dibayar' => $totalDibayar,
            'sisa_tagihan' => $sisaTagihan > 0 ? $sisaTagihan : 0,
        ];

        // === KETERANGAN ===
        $jumlahPinjaman = $pinjamanList->count();
        $pinjamanLunas = $pinjamanList->where('status_lunas', 'Lunas')->count();

        // Cek angsuran yang terlambat
        $angsuranTerlambat = BayarAngsuran::whereIn('pinjaman_id', $pinjamanList->pluck('id'))
            ->where('status_bayar', 'Belum')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->whereNull('deleted_at')
            ->count();

        $statusPembayaran = $angsuranTerlambat > 0 ? 'Macet' : 'Lancar';
        
        $statusColor = match($statusPembayaran) {
            'Lancar' => 'success',
            'Macet' => 'danger',
            default => 'warning',
        };

        // Tanggal tempo terdekat
        $tanggalTempoData = BayarAngsuran::whereIn('pinjaman_id', $pinjamanList->pluck('id'))
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->first();

        $keterangan = [
            'jumlah_pinjaman' => $jumlahPinjaman,
            'pinjaman_lunas' => $pinjamanLunas,
            'status_pembayaran' => $statusPembayaran,
            'status_color' => $statusColor,
            'tanggal_tempo' => $tanggalTempoData 
                ? Carbon::parse($tanggalTempoData->tanggal_jatuh_tempo)->translatedFormat('d M Y')
                : '-',
        ];

        Log::info('Dashboard data loaded successfully', [
            'anggota_id' => $anggotaData->id,
            'total_simpanan' => $totalSimpanan,
            'total_pinjaman' => $jumlahPinjaman
        ]);

        return view('user.dashboard', compact(
            'anggota',
            'pengajuan_terakhir',
            'simpanan',
            'pinjaman',
            'keterangan'
        ));
    }
}