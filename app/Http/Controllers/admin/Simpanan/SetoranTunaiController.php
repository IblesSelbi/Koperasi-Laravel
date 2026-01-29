<?php

namespace App\Http\Controllers\Admin\Simpanan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Simpanan\SetoranTunai;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SetoranTunaiController extends Controller
{
    public function index()
    {
        $setoran = SetoranTunai::with(['anggota', 'jenisSimpanan', 'untukKas', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_setoran = $setoran->sum('jumlah');

        // Filter: Anggota aktif
        $anggota_list = DataAnggota::where('aktif', 'Aktif')
            ->orderBy('nama', 'asc')
            ->get();

        // Filter: Jenis simpanan tampil
        $jenis_simpanan_list = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        // Filter: Kas aktif dan simpanan = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('simpanan', 'Y')
            ->get();

        return view('admin.simpanan.setorantunai.SetoranTunai', compact(
            'setoran',
            'total_setoran',
            'anggota_list',
            'jenis_simpanan_list',
            'kas_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $setoran = SetoranTunai::with(['anggota'])->findOrFail($id);
        return response()->json($setoran);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'nama_penyetor' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = SetoranTunai::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        SetoranTunai::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data setoran tunai berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $setoran = SetoranTunai::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'nama_penyetor' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $setoran->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data setoran tunai berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $setoran = SetoranTunai::findOrFail($id);
        $setoran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data setoran tunai berhasil dihapus!'
        ]);
    }

    // API untuk mendapatkan detail anggota (foto, departemen)
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
            'photo' => $photoPath ?? 'assets/images/profile/user-1.jpg', // Path saja
            'photo_url' => $photoUrl // Full URL untuk ditampilkan
        ]);
    }

    /**
     * Cetak Nota Setoran Tunai (Langsung PDF di Browser)
     */
    public function cetakNota($id)
    {
        $setoran = SetoranTunai::with(['anggota', 'jenisSimpanan', 'untukKas', 'user'])->findOrFail($id);
        $terbilang = $this->terbilang($setoran->jumlah);
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        // Load view untuk PDF
        $pdf = Pdf::loadView('admin.simpanan.setorantunai.cetak', compact('setoran', 'terbilang', 'identitas'));

        // Set paper size A5 landscape
        $pdf->setPaper('a5', 'landscape');

        // ✅ Stream PDF (tampil di browser, bukan download)
        return $pdf->stream('Bukti_Setoran_' . $setoran->kode_transaksi . '.pdf');
    }

    /**
     * Cetak PDF Setoran Tunai (Download)
     */
    public function cetakPDF($id)
    {
        $setoran = SetoranTunai::with(['anggota', 'jenisSimpanan', 'untukKas', 'user'])->findOrFail($id);
        $terbilang = $this->terbilang($setoran->jumlah);
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        $pdf = Pdf::loadView('admin.simpanan.setorantunai.cetak', compact('setoran', 'terbilang', 'identitas'));
        $pdf->setPaper('a5', 'landscape');

        // ✅ Download PDF (langsung download)
        return $pdf->download('Bukti_Setoran_' . $setoran->kode_transaksi . '.pdf');
    }

    /**
     * Fungsi untuk convert angka ke terbilang
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
     * Cetak Laporan Setoran Tunai (PDF)
     */
    public function cetakLaporan(Request $request)
    {
        $query = SetoranTunai::with(['anggota', 'jenisSimpanan', 'untukKas', 'user']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date]);
        }

        $setoran = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $total_setoran = $setoran->sum('jumlah');
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        if ($request->has('start_date') && $request->has('end_date')) {
            $periode = 'Periode ' . \Carbon\Carbon::parse($request->start_date)->format('d F Y');
            $periode .= ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d F Y');
        } else {
            $periode = 'Periode Semua Data';
        }

        $pdf = Pdf::loadView('admin.simpanan.setorantunai.cetaklaporan', compact('setoran', 'total_setoran', 'identitas', 'periode'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Setoran_Tunai.pdf');
    }
}