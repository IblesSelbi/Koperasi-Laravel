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
use App\Models\Admin\Setting\SukuBunga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
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

        // ✅ Ambil setting suku bunga untuk ditampilkan di view
        $sukuBunga = SukuBunga::getSetting();

        // Notifikasi dummy
        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Pinjaman.datapinjaman.Pinjaman', compact('pinjaman', 'sukuBunga', 'notifications'));
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

            // ✅ Ambil setting suku bunga dari database
            $sukuBunga = SukuBunga::getSetting();
            $bungaPersen = $sukuBunga->bg_pinjam; // Ambil dari setting
            $biayaAdmin = $sukuBunga->biaya_adm ?? 0; // Ambil dari setting

            // Generate kode pinjaman
            $kodePinjaman = Pinjaman::generateKodePinjaman();

            // Hitung angsuran
            $lamaAngsuran = $pengajuan->lamaAngsuran->lama_angsuran;
            $pokokPinjaman = $pengajuan->jumlah;
            $angsuranPokok = $pokokPinjaman / $lamaAngsuran;

            // ✅ Hitung bunga berdasarkan tipe dari setting
            if ($sukuBunga->pinjaman_bunga_tipe == 'A') {
                // Tipe A: Persen Bunga dikali angsuran per bulan
                $biayaBunga = ($angsuranPokok * $bungaPersen / 100);
            } else {
                // Tipe B: Persen Bunga dikali total pinjaman, dibagi lama angsuran
                $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran;
            }

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

        // GENERATE SEMUA KODE DULU
        $lastBayar = BayarAngsuran::withTrashed()->orderBy('id', 'desc')->first();
        $startNumber = $lastBayar ? ((int) substr($lastBayar->kode_bayar, 3)) + 1 : 1;

        for ($i = 1; $i <= $lamaAngsuran; $i++) {
            $tanggalJatuhTempo = $tanggalMulai->copy()->addMonths($i);
            $kodeBayar = 'BYR' . str_pad($startNumber + ($i - 1), 5, '0', STR_PAD_LEFT);

            BayarAngsuran::create([
                'kode_bayar' => $kodeBayar,
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

                // ✅ Ambil setting suku bunga
                $sukuBunga = SukuBunga::getSetting();
                $bungaPersen = $sukuBunga->bg_pinjam;

                // Hitung ulang
                $lamaAngsuran = LamaAngsuran::findOrFail($validated['lama_angsuran_id']);
                $pokokPinjaman = $pinjaman->pokok_pinjaman;
                $angsuranPokok = $pokokPinjaman / $lamaAngsuran->lama_angsuran;

                // ✅ Hitung bunga berdasarkan tipe
                if ($sukuBunga->pinjaman_bunga_tipe == 'A') {
                    $biayaBunga = ($angsuranPokok * $bungaPersen / 100);
                } else {
                    $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran->lama_angsuran;
                }

                $jumlahAngsuran = $pokokPinjaman + ($biayaBunga * $lamaAngsuran->lama_angsuran) + $pinjaman->biaya_admin;

                $pinjaman->update([
                    'tanggal_pinjam' => $validated['tanggal_pinjam'],
                    'lama_angsuran_id' => $validated['lama_angsuran_id'],
                    'angsuran_pokok' => $angsuranPokok,
                    'bunga_persen' => $bungaPersen,
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
    public function show($id)
    {
        $pinjaman = Pinjaman::with([
            'anggota.user',
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

        // Foto
        $photoPath = 'assets/images/profile/user-1.jpg';
        if ($pinjaman->anggota->photo && $pinjaman->anggota->photo !== 'assets/images/profile/user-1.jpg') {
            $photoPath = 'storage/' . $pinjaman->anggota->photo;
        } elseif ($pinjaman->anggota->user && $pinjaman->anggota->user->profile_image) {
            $photoPath = 'storage/' . $pinjaman->anggota->user->profile_image;
        }
        $pinjaman->anggota_foto = $photoPath;

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

        // Ambil dari DetailBayarAngsuran
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

        // ✅ Ambil setting suku bunga
        $sukuBunga = SukuBunga::getSetting();
        $bungaPersen = $sukuBunga->bg_pinjam;

        $pokokPinjaman = $pinjaman->pokok_pinjaman;
        $angsuranPokok = $pokokPinjaman / $lamaAngsuran->lama_angsuran;

        // ✅ Hitung bunga berdasarkan tipe
        if ($sukuBunga->pinjaman_bunga_tipe == 'A') {
            $biayaBunga = ($angsuranPokok * $bungaPersen / 100);
        } else {
            $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran->lama_angsuran;
        }

        $jumlahAngsuran = $pokokPinjaman + ($biayaBunga * $lamaAngsuran->lama_angsuran) + $pinjaman->biaya_admin;

        return response()->json([
            'success' => true,
            'data' => [
                'lama_angsuran' => $lamaAngsuran->lama_angsuran,
                'angsuran_pokok' => $angsuranPokok,
                'biaya_bunga' => $biayaBunga,
                'bunga_persen' => $bungaPersen,
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

            // ✅ Cek apakah bisa dihapus
            $canDelete = $pinjaman->canDelete();

            if (!$canDelete['can_delete']) {
                throw new \Exception($canDelete['reason']);
            }

            // ✅ Jika sudah ada pembayaran, wajib isi alasan
            if ($canDelete['require_reason']) {
                return response()->json([
                    'success' => false,
                    'require_reason' => true,
                    'message' => 'Pinjaman ini sudah ada pembayaran. Mohon berikan alasan penghapusan.'
                ], 400);
            }

            // ✅ Soft delete tanpa alasan jika belum ada pembayaran
            $pinjaman->deleted_by = Auth::id();
            $pinjaman->save();
            $pinjaman->delete();

            // ✅ Soft delete jadwal angsuran terkait
            BayarAngsuran::where('pinjaman_id', $id)->delete();

            // ✅ Kembalikan status pengajuan
            if ($pinjaman->pengajuan) {
                $pinjaman->pengajuan->status = 1; // Kembali ke "Disetujui"
                $pinjaman->pengajuan->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman berhasil dihapus dan dipindahkan ke riwayat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error soft deleting pinjaman: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Soft delete dengan alasan
     */
    public function softDeleteWithReason(Request $request, $id)
    {
        $validated = $request->validate([
            'alasan_hapus' => 'required|string|min:10|max:500'
        ], [
            'alasan_hapus.required' => 'Alasan penghapusan wajib diisi',
            'alasan_hapus.min' => 'Alasan minimal 10 karakter',
            'alasan_hapus.max' => 'Alasan maksimal 500 karakter'
        ]);

        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::findOrFail($id);

            // ✅ Validasi
            $canDelete = $pinjaman->canDelete();
            if (!$canDelete['can_delete']) {
                throw new \Exception($canDelete['reason']);
            }

            // ✅ Soft delete dengan alasan
            $pinjaman->softDeleteWithReason($validated['alasan_hapus'], Auth::id());

            // ✅ Soft delete jadwal angsuran
            BayarAngsuran::where('pinjaman_id', $id)->delete();

            // ✅ Kembalikan status pengajuan
            if ($pinjaman->pengajuan) {
                $pinjaman->pengajuan->status = 1;
                $pinjaman->pengajuan->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman berhasil dihapus dengan alasan: ' . $validated['alasan_hapus']
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error soft deleting with reason: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Tampilkan riwayat pinjaman terhapus
     */
    public function riwayatHapus()
    {
        $pinjamanTerhapus = Pinjaman::onlyTrashed()
            ->with(['anggota', 'lamaAngsuran', 'deletedBy', 'pengajuan'])
            ->orderBy('deleted_at', 'desc')
            ->get()
            ->map(function ($item) {
                return $item->riwayat_info;
            });

        return view('admin.Pinjaman.datapinjaman.RiwayatHapus', compact('pinjamanTerhapus'));
    }

    /**
     * Restore pinjaman dari soft delete
     */
    public function restore($id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::onlyTrashed()->findOrFail($id);

            // ✅ Validasi: pengajuan masih ada dan belum diproses ulang
            $pengajuanSudahDiproses = Pinjaman::where('pengajuan_id', $pinjaman->pengajuan_id)
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists();

            if ($pengajuanSudahDiproses) {
                throw new \Exception('Pengajuan ini sudah diproses menjadi pinjaman baru. Tidak dapat dipulihkan.');
            }

            // ✅ Restore pinjaman
            $pinjaman->restorePinjaman();

            // ✅ Restore jadwal angsuran
            BayarAngsuran::onlyTrashed()
                ->where('pinjaman_id', $id)
                ->restore();

            // ✅ Update status pengajuan kembali
            if ($pinjaman->pengajuan) {
                $pinjaman->pengajuan->status = 3; // Terlaksana
                $pinjaman->pengajuan->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman berhasil dipulihkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error restoring pinjaman: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Hapus permanen dari database
     */
    public function forceDelete($id)
    {
        DB::beginTransaction();
        try {
            $pinjaman = Pinjaman::onlyTrashed()->findOrFail($id);

            // ✅ Validasi: hanya bisa force delete jika BELUM ada pembayaran
            if ($pinjaman->sudah_ada_pembayaran) {
                throw new \Exception('Tidak dapat menghapus permanen karena sudah ada riwayat pembayaran');
            }

            // ✅ Force delete jadwal angsuran
            BayarAngsuran::onlyTrashed()
                ->where('pinjaman_id', $id)
                ->forceDelete();

            // ✅ Force delete pinjaman
            $pinjaman->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pinjaman berhasil dihapus permanen dari database'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error force deleting pinjaman: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get detail pinjaman untuk modal konfirmasi
     */
    public function getDeleteInfo($id)
    {
        try {
            $pinjaman = Pinjaman::with(['anggota', 'lamaAngsuran'])->findOrFail($id);
            $canDelete = $pinjaman->canDelete();

            return response()->json([
                'success' => true,
                'data' => [
                    'kode_pinjaman' => $pinjaman->kode_pinjaman,
                    'anggota_nama' => $pinjaman->anggota->nama,
                    'pokok_pinjaman' => $pinjaman->pokok_pinjaman,
                    'jumlah_angsuran' => $pinjaman->jumlah_angsuran,
                    'sudah_dibayar' => $pinjaman->total_bayar,
                    'sisa_tagihan' => $pinjaman->sisa_tagihan,
                    'sudah_ada_pembayaran' => $pinjaman->sudah_ada_pembayaran,
                    'can_delete' => $canDelete['can_delete'],
                    'require_reason' => $canDelete['require_reason'] ?? false,
                    'reason' => $canDelete['reason'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil info pinjaman'
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

        // ✅ Ambil setting suku bunga
        $sukuBunga = SukuBunga::getSetting();
        $bungaPersen = $sukuBunga->bg_pinjam;
        $biayaAdmin = $sukuBunga->biaya_adm ?? 0;

        // Hitung proyeksi angsuran
        $lamaAngsuran = $pengajuan->lamaAngsuran->lama_angsuran;
        $pokokPinjaman = $pengajuan->jumlah;
        $angsuranPokok = $pokokPinjaman / $lamaAngsuran;

        // ✅ Hitung bunga berdasarkan tipe
        if ($sukuBunga->pinjaman_bunga_tipe == 'A') {
            $biayaBunga = ($angsuranPokok * $bungaPersen / 100);
        } else {
            $biayaBunga = ($pokokPinjaman * $bungaPersen / 100) / $lamaAngsuran;
        }

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

    // Tambahkan method ini di PinjamanController.php

    /**
     * Print single pinjaman (Cetak per ID)
     */
    public function cetak($id)
    {
        $pinjaman = Pinjaman::with(['anggota', 'lamaAngsuran', 'user', 'dariKas'])
            ->findOrFail($id);

        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        // Hitung terbilang di controller
        $terbilang = $this->terbilang($pinjaman->pokok_pinjaman);

        $pdf = Pdf::loadView('admin.Pinjaman.DataPinjaman.Cetak', compact('pinjaman', 'identitas', 'terbilang'));
        $pdf->setPaper([0, 0, 595.28, 419.53]); // A5 Landscape

        return $pdf->stream('Bukti_Pinjaman_' . $pinjaman->kode_pinjaman . '.pdf');
    }

    public function cetakDetail($id)
    {
        try {
            $pinjaman = Pinjaman::with([
                'anggota',
                'lamaAngsuran',
                'dariKas',
                'user',
                'detailPembayaran' => function ($query) {
                    $query->whereNull('deleted_at')
                        ->orderBy('created_at', 'asc');
                }
            ])->findOrFail($id);

            // Ambil data transaksi pembayaran
            $transaksi = DetailBayarAngsuran::where('pinjaman_id', $id)
                ->whereNull('deleted_at')
                ->with(['kas', 'user'])
                ->orderBy('tanggal_bayar', 'asc')
                ->get()
                ->map(function ($item, $index) {
                    return (object) [
                        'no' => $index + 1,
                        'kode_bayar' => $item->kode_bayar,
                        'tanggal_bayar' => $item->tanggal_bayar,
                        'angsuran_ke' => $item->angsuran_ke,
                        'jenis_pembayaran' => $item->kas->nama_kas ?? 'Kas Tunai',
                        'jumlah_bayar' => $item->jumlah_bayar,
                        'denda' => $item->denda ?? 0,
                    ];
                });

            $pdf = Pdf::loadView('admin.Pinjaman.datapinjaman.CetakDetail', compact('pinjaman', 'transaksi'));
            $pdf->setPaper('a4', 'portrait');

            return $pdf->stream('Detail-Pinjaman-' . $pinjaman->kode_pinjaman . '.pdf');
        } catch (\Exception $e) {
            Log::error('Error cetak detail: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mencetak detail: ' . $e->getMessage());
        }
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

        // Mulai query builder
        $query = Pinjaman::query()
            ->with(['anggota', 'lamaAngsuran', 'user', 'dariKas']);

        // Apply filters HANYA jika ada nilai
        if ($status !== '' && $status !== null) {
            $query->where('status_lunas', $status);
        }

        if (!empty($kode)) {
            $query->where('kode_pinjaman', 'LIKE', '%' . $kode . '%');
        }

        if (!empty($nama)) {
            $query->whereHas('anggota', function ($q) use ($nama) {
                $q->where('nama', 'LIKE', '%' . $nama . '%');
            });
        }

        if (!empty($tanggal)) {
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

        // Execute query
        $pinjaman = $query->orderBy('tanggal_pinjam', 'desc')->get();

        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        $pdf = Pdf::loadView('admin.Pinjaman.datapinjaman.cetakLaporan', compact(
            'pinjaman',
            'status',
            'kode',
            'nama',
            'tanggal',
            'identitas'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Pinjaman_Anggota.pdf');
    }

    /**
     * Fungsi terbilang (helper)
     */
    private function terbilang($angka)
    {
        $angka = abs($angka);
        $baca = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        $terbilang = "";

        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " belas";
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . " puluh" . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = " seratus" . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . " ratus" . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = " seribu" . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . " ribu" . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . " juta" . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000) . " milyar" . $this->terbilang(fmod($angka, 1000000000));
        } elseif ($angka < 1000000000000000) {
            $terbilang = $this->terbilang($angka / 1000000000000) . " trilyun" . $this->terbilang(fmod($angka, 1000000000000));
        }

        return trim($terbilang);
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

    public function getAnggotaDetail($id)
    {
        $anggota = DataAnggota::with('user')->findOrFail($id);

        // ✅ Prioritaskan foto terbaru (sync dengan user)
        $photoPath = null;

        // Cek foto di data_anggota dulu
        if ($anggota->photo && $anggota->photo !== 'assets/images/profile/user-1.jpg') {
            $photoPath = $anggota->photo;
        }
        // Fallback ke foto user jika ada
        elseif ($anggota->user && $anggota->user->profile_image) {
            $photoPath = $anggota->user->profile_image;
        }

        // Generate full URL
        $photoUrl = $photoPath
            ? asset('storage/' . $photoPath)
            : asset('assets/images/profile/user-1.jpg');

        return response()->json([
            'id_anggota' => $anggota->id_anggota,
            'nama' => $anggota->nama,
            'departement' => $anggota->departement ?? '-',
            'photo' => $photoPath ?? 'assets/images/profile/user-1.jpg',
            'photo_url' => $photoUrl
        ]);
    }

}