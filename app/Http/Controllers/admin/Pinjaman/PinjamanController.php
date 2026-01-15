<?php

namespace App\Http\Controllers\Admin\Pinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\PengajuanPinjaman;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\DataBarang;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\DataMaster\LamaAngsuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PinjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pinjaman::with(['anggota', 'pengajuan', 'lamaAngsuran', 'barang', 'kas', 'user']);

        // Filter by Status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by Kode
        if ($request->filled('kode')) {
            $query->where('kode_pinjaman', 'like', '%' . $request->kode . '%');
        }

        // Filter by Nama Anggota
        if ($request->filled('nama')) {
            $query->whereHas('anggota', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->nama . '%');
            });
        }

        // Filter by Tanggal Range
        if ($request->filled('tanggal')) {
            $dates = explode(' - ', $request->tanggal);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();

                    $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    Log::warning('Invalid tanggal format: ' . $request->tanggal);
                }
            }
        }

        $pinjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        // Hitung data untuk setiap pinjaman
        foreach ($pinjaman as $item) {
            // TODO: Implementasi setelah ada tabel pembayaran
            $item->sudah_dibayar = 0;
            $item->sisa_angsuran = $item->lamaAngsuran->lama_angsuran;
            $item->jumlah_denda = 0;
            $item->sisa_tagihan = $item->jumlah_angsuran;

            // Data untuk view
            $item->nama_barang = $item->barang ? $item->barang->nama_barang : 'Pinjaman Tunai';
            $item->harga_barang = $item->pokok_pinjaman;
            $item->lama_angsuran = $item->lamaAngsuran->lama_angsuran;
            $item->pokok_angsuran = $item->angsuran_pokok;
            $item->bunga_pinjaman = $item->biaya_bunga;

            // Data anggota
            $item->anggota_nama = $item->anggota->nama;
            $item->anggota_id = $item->anggota->id_anggota;
            $item->anggota_lokasi = $item->anggota->kota;
            $item->anggota_foto = $item->anggota->photo;
        }

        // Notifikasi dummy
        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Pinjaman.datapinjaman.Pinjaman', compact('pinjaman', 'notifications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengajuan_id' => 'required|exists:pengajuan_pinjaman,id',
            'tanggal_pinjam' => 'required|date',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Ambil data pengajuan
            $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran'])->findOrFail($validated['pengajuan_id']);

            // Validasi pengajuan sudah disetujui
            if ($pengajuan->status != 1) {
                throw new \Exception('Hanya pengajuan yang disetujui yang dapat diproses menjadi pinjaman');
            }

            // Validasi pengajuan belum pernah diproses
            $existingPinjaman = Pinjaman::where('pengajuan_id', $pengajuan->id)->first();
            if ($existingPinjaman) {
                throw new \Exception('Pengajuan ini sudah pernah diproses menjadi pinjaman');
            }

            // Generate kode pinjaman
            $kodePinjaman = Pinjaman::generateKodePinjaman();

            // Hitung angsuran
            $lamaAngsuran = $pengajuan->lamaAngsuran->lama_angsuran;
            $pokokPinjaman = $pengajuan->jumlah;
            $angsuranPokok = $pokokPinjaman / $lamaAngsuran;

            // Bunga 5% per angsuran
            $bungaPersen = 5;
            $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran;

            // Biaya admin
            $biayaAdmin = 0;

            // Total angsuran
            $jumlahAngsuran = $pokokPinjaman + ($biayaBunga * $lamaAngsuran) + $biayaAdmin;

            // Buat pinjaman
            $pinjaman = Pinjaman::create([
                'kode_pinjaman' => $kodePinjaman,
                'pengajuan_id' => $pengajuan->id,
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'anggota_id' => $pengajuan->anggota_id,
                'barang_id' => null,
                'jenis_pinjaman' => $pengajuan->jenis_pinjaman,
                'pokok_pinjaman' => $pokokPinjaman,
                'lama_angsuran_id' => $pengajuan->lama_angsuran_id,
                'angsuran_pokok' => $angsuranPokok,
                'bunga_persen' => $bungaPersen,
                'biaya_bunga' => $biayaBunga,
                'biaya_admin' => $biayaAdmin,
                'jumlah_angsuran' => $jumlahAngsuran,
                'dari_kas_id' => $validated['dari_kas_id'],
                'keterangan' => $validated['keterangan'],
                'status_lunas' => 'Belum',
                'user_id' => Auth::id(),
            ]);

            // AUTO GENERATE ANGSURAN
            $this->generateAngsuran($pinjaman);

            // Update status pengajuan menjadi terlaksana
            $pengajuan->status = 3;
            $pengajuan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman dan jadwal angsuran berhasil dibuat',
                'data' => $pinjaman
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pinjaman: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function generateAngsuran(Pinjaman $pinjaman)
    {
        $lamaAngsuran = $pinjaman->lamaAngsuran->lama_angsuran;
        $angsuranPerBulan = $pinjaman->angsuran_pokok + $pinjaman->biaya_bunga;
        $tanggalMulai = Carbon::parse($pinjaman->tanggal_pinjam);

        for ($i = 1; $i <= $lamaAngsuran; $i++) {
            // Tanggal jatuh tempo: tanggal pinjam + ($i bulan)
            $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths($i);

            BayarAngsuran::create([
                'kode_bayar' => BayarAngsuran::generateKodeBayar(),
                'pinjaman_id' => $pinjaman->id,
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                'tanggal_bayar' => null,
                'jumlah_angsuran' => $angsuranPerBulan,
                'jumlah_bayar' => 0,
                'denda' => 0,
                'ke_kas_id' => null,
                'status_bayar' => 'Belum',
                'keterangan' => null,
                'user_id' => null,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tanggal_pinjam' => 'required|date',
            'lama_angsuran_id' => 'required|exists:lama_angsuran,id',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::findOrFail($id);

            // Cek apakah sudah ada pembayaran
            $adaPembayaran = $pinjaman->angsuran()->where('status_bayar', 'Lunas')->exists();

            if ($adaPembayaran && $pinjaman->lama_angsuran_id != $validated['lama_angsuran_id']) {
                throw new \Exception('Tidak dapat mengubah lama angsuran karena sudah ada pembayaran');
            }

            $lamaAngsuranBerubah = $pinjaman->lama_angsuran_id != $validated['lama_angsuran_id'];

            if ($lamaAngsuranBerubah) {
                // Hapus angsuran lama (yang belum dibayar)
                $pinjaman->angsuran()->where('status_bayar', 'Belum')->delete();

                // Hitung ulang
                $lamaAngsuran = LamaAngsuran::findOrFail($validated['lama_angsuran_id']);
                $pokokPinjaman = $pinjaman->pokok_pinjaman;
                $angsuranPokok = $pokokPinjaman / $lamaAngsuran->lama_angsuran;
                $bungaPersen = 5;
                $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran->lama_angsuran;
                $jumlahAngsuran = $pokokPinjaman + ($biayaBunga * $lamaAngsuran->lama_angsuran) + $pinjaman->biaya_admin;

                $pinjaman->update([
                    'tanggal_pinjam' => $validated['tanggal_pinjam'],
                    'lama_angsuran_id' => $validated['lama_angsuran_id'],
                    'angsuran_pokok' => $angsuranPokok,
                    'biaya_bunga' => $biayaBunga,
                    'jumlah_angsuran' => $jumlahAngsuran,
                    'dari_kas_id' => $validated['dari_kas_id'],
                    'keterangan' => $validated['keterangan'],
                ]);

                // Regenerate angsuran
                $this->generateAngsuran($pinjaman);
            } else {
                $pinjaman->update([
                    'tanggal_pinjam' => $validated['tanggal_pinjam'],
                    'dari_kas_id' => $validated['dari_kas_id'],
                    'keterangan' => $validated['keterangan'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pinjaman berhasil diupdate' . ($lamaAngsuranBerubah ? ' dan jadwal angsuran telah di-generate ulang' : '')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pinjaman: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Display detail pinjaman
     */
    /**
     * Display detail pinjaman
     */
    public function show($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota',
            'lamaAngsuran',
            'detailPembayaran.user',
            'detailPembayaran.kas',
            'kas',
            'user'
        ])->findOrFail($id);

        // Data anggota
        $pinjaman->anggota_id = $pinjaman->anggota->id_anggota;
        $pinjaman->anggota_nama = $pinjaman->anggota->nama;
        $pinjaman->anggota_departemen = $pinjaman->anggota->departement ?? '-';
        $pinjaman->anggota_ttl = $pinjaman->anggota->tempat_lahir . ', ' .
            Carbon::parse($pinjaman->anggota->tanggal_lahir)->translatedFormat('d F Y');
        $pinjaman->anggota_kota = $pinjaman->anggota->kota;
        $pinjaman->anggota_foto = $pinjaman->anggota->photo;

        // Data pinjaman
        $pinjaman->kode = $pinjaman->kode_pinjaman;
        $pinjaman->lama_pinjaman = $pinjaman->lamaAngsuran->lama_angsuran;
        $pinjaman->tanggal_tempo = $pinjaman->tanggal_pinjam->copy()->addMonths($pinjaman->lama_pinjaman);

        // Simulasi jadwal angsuran
        $simulasi = collect();
        $angsuranPerBulan = $pinjaman->angsuran_pokok + $pinjaman->biaya_bunga;
        $tanggalMulai = Carbon::parse($pinjaman->tanggal_pinjam);

        for ($i = 1; $i <= $pinjaman->lama_pinjaman; $i++) {
            $simulasi->push((object) [
                'bulan_ke' => $i,
                'angsuran_pokok' => $pinjaman->angsuran_pokok,
                'angsuran_bunga' => $pinjaman->biaya_bunga,
                'biaya_admin' => ($i == 1) ? $pinjaman->biaya_admin : 0,
                'jumlah_angsuran' => $angsuranPerBulan + (($i == 1) ? $pinjaman->biaya_admin : 0),
                'tanggal_tempo' => $tanggalMulai->copy()->addMonths($i)->translatedFormat('d F Y')
            ]);
        }

        // PERBAIKAN: Ambil dari DetailBayarAngsuran
        $transaksi = DetailBayarAngsuran::with(['kas', 'user', 'angsuran'])
            ->where('pinjaman_id', $id)
            ->orderBy('tanggal_bayar', 'asc')
            ->get()
            ->map(function ($item, $index) {
                return (object) [
                    'no' => $index + 1,
                    'kode_bayar' => $item->kode_bayar,
                    'tanggal_bayar' => Carbon::parse($item->tanggal_bayar)->translatedFormat('d F Y H:i'),
                    'angsuran_ke' => $item->angsuran_ke,
                    'jenis_pembayaran' => $item->kas->nama_kas ?? '-',
                    'jumlah_bayar' => $item->jumlah_bayar,
                    'denda' => $item->denda,
                    'user' => $item->user->name ?? '-',
                ];
            });

        return view('admin.Pinjaman.datapinjaman.DetailPinjaman', compact(
            'pinjaman',
            'simulasi',
            'transaksi'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pinjaman = Pinjaman::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pinjaman->id,
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d\TH:i'),
                'lama_angsuran_id' => $pinjaman->lama_angsuran_id,
                'dari_kas_id' => $pinjaman->dari_kas_id,
                'keterangan' => $pinjaman->keterangan,
            ]
        ]);
    }

    public function recalculate(Request $request, $id)
    {
        $pinjaman = Pinjaman::findOrFail($id);
        $lamaAngsuran = LamaAngsuran::findOrFail($request->lama_angsuran_id);

        $pokokPinjaman = $pinjaman->pokok_pinjaman;
        $angsuranPokok = $pokokPinjaman / $lamaAngsuran->lama_angsuran;
        $bungaPersen = 5;
        $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran->lama_angsuran;
        $jumlahAngsuran = $pokokPinjaman + ($biayaBunga * $lamaAngsuran->lama_angsuran) + $pinjaman->biaya_admin;

        return response()->json([
            'success' => true,
            'data' => [
                'lama_angsuran' => $lamaAngsuran->lama_angsuran,
                'angsuran_pokok' => $angsuranPokok,
                'biaya_bunga' => $biayaBunga,
                'jumlah_angsuran' => $jumlahAngsuran,
                'angsuran_per_bulan' => ($angsuranPokok + $biayaBunga),
            ]
        ]);
    }

    public function getKasList()
    {
        try {
            $kasList = DataKas::select('id', 'nama_kas', 'saldo')
                ->where('status', 'Aktif')
                ->orderBy('nama_kas', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kasList
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting kas list: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kas'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::findOrFail($id);

            // Cek apakah sudah ada pembayaran
            $adaPembayaran = $pinjaman->angsuran()->where('status_bayar', 'Lunas')->exists();

            if ($adaPembayaran) {
                throw new \Exception('Tidak dapat menghapus pinjaman karena sudah ada pembayaran');
            }

            // Hapus semua angsuran
            $pinjaman->angsuran()->delete();

            // Kembalikan status pengajuan
            if ($pinjaman->pengajuan) {
                $pinjaman->pengajuan->status = 1;
                $pinjaman->pengajuan->save();
            }

            $pinjaman->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pinjaman dan jadwal angsuran berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pinjaman: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get pengajuan yang sudah disetujui untuk dropdown
     */
    public function getPengajuanDisetujui()
    {
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran'])
            ->where('status', 1) // Disetujui
            ->whereNotIn('id', function ($query) {
                $query->select('pengajuan_id')
                    ->from('pinjaman')
                    ->whereNull('deleted_at');
            })
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $pengajuan
        ]);
    }

    /**
     * Get detail pengajuan
     */
    public function getDetailPengajuan($id)
    {
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran'])
            ->findOrFail($id);

        // Hitung proyeksi angsuran
        $lamaAngsuran = $pengajuan->lamaAngsuran->lama_angsuran;
        $pokokPinjaman = $pengajuan->jumlah;
        $angsuranPokok = $pokokPinjaman / $lamaAngsuran;
        $bungaPersen = 5;
        $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran;
        $biayaAdmin = 0;
        $jumlahAngsuran = $pokokPinjaman + ($biayaBunga * $lamaAngsuran) + $biayaAdmin;

        return response()->json([
            'success' => true,
            'data' => [
                'pengajuan' => $pengajuan,
                'proyeksi' => [
                    'pokok_pinjaman' => $pokokPinjaman,
                    'angsuran_pokok' => $angsuranPokok,
                    'bunga_persen' => $bungaPersen,
                    'biaya_bunga' => $biayaBunga,
                    'biaya_admin' => $biayaAdmin,
                    'jumlah_angsuran' => $jumlahAngsuran,
                    'angsuran_per_bulan' => ($angsuranPokok + $biayaBunga),
                ]
            ]
        ]);
    }

    /**
     * Validasi pinjaman sebagai lunas
     */
    public function validasiLunas($id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::findOrFail($id);

            // TODO: Validasi sudah lunas semua angsuran

            $pinjaman->status_lunas = 'Lunas';
            $pinjaman->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman telah divalidasi sebagai lunas'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi'
            ], 400);
        }
    }

    /**
     * Print single pinjaman nota
     */
    public function cetak($id)
    {
        $pinjaman = Pinjaman::with(['anggota', 'pengajuan', 'lamaAngsuran', 'kas', 'user'])
            ->findOrFail($id);

        return view('admin.Pinjaman.datapinjaman.cetak', compact('pinjaman'));
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

        $query = Pinjaman::with(['anggota', 'pengajuan', 'lamaAngsuran', 'barang', 'kas', 'user']);

        if ($status !== '') {
            $query->byStatus($status);
        }

        if ($kode) {
            $query->where('kode_pinjaman', 'like', '%' . $kode . '%');
        }

        if ($nama) {
            $query->whereHas('anggota', function ($q) use ($nama) {
                $q->where('nama', 'like', '%' . $nama . '%');
            });
        }

        if ($tanggal) {
            $dates = explode(' - ', $tanggal);
            if (count($dates) === 2) {
                try {
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    Log::warning('Invalid tanggal format in cetak: ' . $tanggal);
                }
            }
        }

        $pinjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        return view('admin.Pinjaman.datapinjaman.cetakLaporan', compact(
            'pinjaman',
            'status',
            'kode',
            'nama',
            'tanggal'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export using Laravel Excel
        return response()->download(public_path('dummy.xlsx'));
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        // TODO: Implement PDF export
        return response()->download(public_path('dummy.pdf'));
    }
}