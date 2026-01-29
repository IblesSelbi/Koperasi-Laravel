<?php

namespace App\Http\Controllers\Admin\Simpanan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Simpanan\PenarikanTunai;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PenarikanTunaiController extends Controller
{
    public function index()
    {
        $penarikan = PenarikanTunai::with(['anggota', 'jenisSimpanan', 'dariKas', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_penarikan = $penarikan->sum('jumlah');

        // Filter: Anggota aktif
        $anggota_list = DataAnggota::where('aktif', 'Aktif')
            ->orderBy('nama', 'asc')
            ->get();

        // Filter: Jenis simpanan tampil
        $jenis_simpanan_list = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        // Filter: Kas aktif dan penarikan = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('penarikan', 'Y')
            ->get();

        return view('admin.simpanan.penarikan.PenarikanTunai', compact(
            'penarikan',
            'total_penarikan',
            'anggota_list',
            'jenis_simpanan_list',
            'kas_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $penarikan = PenarikanTunai::with(['anggota'])->findOrFail($id);
        return response()->json($penarikan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'nama_penarik' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = PenarikanTunai::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        PenarikanTunai::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data penarikan tunai berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $penarikan = PenarikanTunai::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'anggota_id' => 'required|exists:data_anggota,id',
            'jenis_simpanan_id' => 'required|exists:jenis_simpanan,id',
            'jumlah' => 'required|numeric|min:0',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'nama_penarik' => 'nullable|string|max:255',
            'no_identitas' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $penarikan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data penarikan tunai berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $penarikan = PenarikanTunai::findOrFail($id);
        $penarikan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data penarikan tunai berhasil dihapus!'
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
     * Cetak Nota Penarikan Tunai (HTML View)
     */
    /**
     * Cetak Nota Penarikan Tunai (Langsung PDF di Browser)
     */
    public function cetakNota($id)
    {
        $penarikan = PenarikanTunai::with(['anggota', 'jenisSimpanan', 'dariKas', 'user'])->findOrFail($id);
        $terbilang = $this->terbilang($penarikan->jumlah);
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        $pdf = Pdf::loadView('admin.simpanan.penarikan.cetak', compact('penarikan', 'terbilang', 'identitas'));
        $pdf->setPaper('a5', 'landscape');

        // ✅ Stream PDF (tampil di browser)
        return $pdf->stream('Bukti_Penarikan_' . $penarikan->kode_transaksi . '.pdf');
    }

    /**
     * Cetak PDF Penarikan Tunai (Download)
     */
    public function cetakPDF($id)
    {
        $penarikan = PenarikanTunai::with(['anggota', 'jenisSimpanan', 'dariKas', 'user'])->findOrFail($id);
        $terbilang = $this->terbilang($penarikan->jumlah);
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        $pdf = Pdf::loadView('admin.simpanan.penarikan.cetak', compact('penarikan', 'terbilang', 'identitas'));
        $pdf->setPaper('a5', 'landscape');

        // ✅ Download PDF
        return $pdf->download('Bukti_Penarikan_' . $penarikan->kode_transaksi . '.pdf');
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

    public function cetakLaporan(Request $request)
    {
        $query = PenarikanTunai::with(['anggota', 'jenisSimpanan', 'dariKas', 'user']);

        // Filter berdasarkan tanggal jika ada
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date]);
        }

        $penarikan = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $total_penarikan = $penarikan->sum('jumlah');
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        // Periode untuk header
        if ($request->has('start_date') && $request->has('end_date')) {
            $periode = 'Periode ' . \Carbon\Carbon::parse($request->start_date)->format('d F Y');
            $periode .= ' - ' . \Carbon\Carbon::parse($request->end_date)->format('d F Y');
        } else {
            $periode = 'Periode Semua Data';
        }

        $pdf = Pdf::loadView('admin.simpanan.penarikan.cetaklaporan', compact('penarikan', 'total_penarikan', 'identitas', 'periode'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Penarikan_Tunai.pdf');
    }
}