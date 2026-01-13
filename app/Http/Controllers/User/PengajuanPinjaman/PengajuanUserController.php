<?php

namespace App\Http\Controllers\User\PengajuanPinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\PengajuanPinjaman;
use App\Models\Admin\DataMaster\LamaAngsuran;
use App\Models\Admin\DataMaster\DataAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengajuanUserController extends Controller
{
    /**
     * Get anggota by authenticated user
     */
    private function getAnggota()
    {
        $user = Auth::user();
        // HANYA pakai username, JANGAN pakai user_id
        return DataAnggota::where('username', $user->email)->first();
    }

    /**
     * Display user's pengajuan list
     */
    public function index()
    {
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran'])
            ->byUser(Auth::id())
            ->orderBy('tanggal_pengajuan', 'desc')
            ->get();

        $lama_angsuran_list = LamaAngsuran::where('aktif', 'Y')
            ->orderBy('lama_angsuran', 'asc')
            ->get();

        return view('user.PengajuanPinjaman.Pengajuan.PengajuanUser', compact('pengajuan', 'lama_angsuran_list'));
    }

    /**
     * Show form for creating new pengajuan
     */
    public function create()
    {
        $anggota = $this->getAnggota();

        if (!$anggota) {
            return redirect()->back()
                ->with('error', 'Data anggota tidak ditemukan! Silakan hubungi administrator.');
        }

        // Cek apakah ada pengajuan pending
        $hasPending = PengajuanPinjaman::where('anggota_id', $anggota->id)
            ->where('status', 0)
            ->exists();

        if ($hasPending) {
            return redirect()->route('user.pengajuan.index')
                ->with('error', 'Anda masih memiliki pengajuan yang menunggu konfirmasi. Mohon tunggu hingga pengajuan sebelumnya diproses.');
        }

        $lama_angsuran = LamaAngsuran::where('aktif', 'Y')
            ->orderBy('lama_angsuran', 'asc')
            ->get();

        if ($lama_angsuran->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Data lama angsuran tidak tersedia! Silakan hubungi administrator.');
        }

        return view('user.PengajuanPinjaman.Pengajuan.FormPengajuanUser', compact('anggota', 'lama_angsuran'));
    }

    /**
     * Store new pengajuan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:Biasa,Darurat,Barang',
            'nominal' => 'required|string',
            'lama_ags' => 'required|exists:lama_angsuran,id',
            'keterangan' => 'required|string|max:500',
        ], [
            'jenis.required' => 'Jenis pinjaman wajib dipilih',
            'nominal.required' => 'Nominal pinjaman wajib diisi',
            'lama_ags.required' => 'Lama angsuran wajib dipilih',
            'keterangan.required' => 'Keterangan wajib diisi',
        ]);

        // Parse nominal
        $jumlah = (int) str_replace('.', '', $validated['nominal']);

        if ($jumlah < 500000) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Minimal pinjaman adalah Rp 500.000');
        }

        $anggota = $this->getAnggota();

        if (!$anggota) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data anggota tidak ditemukan!');
        }

        // Cek duplikasi pengajuan pending
        $hasPending = PengajuanPinjaman::where('anggota_id', $anggota->id)
            ->where('status', 0)
            ->exists();

        if ($hasPending) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Anda masih memiliki pengajuan yang menunggu konfirmasi.');
        }

        DB::beginTransaction();
        try {
            $idAjuan = PengajuanPinjaman::generateIdAjuan($validated['jenis']);

            PengajuanPinjaman::create([
                'id_ajuan' => $idAjuan,
                'tanggal_pengajuan' => now(),
                'anggota_id' => $anggota->id,
                'jenis_pinjaman' => $validated['jenis'],
                'jumlah' => $jumlah,
                'lama_angsuran_id' => $validated['lama_ags'],
                'keterangan' => $validated['keterangan'],
                'status' => 0,
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('user.pengajuan.index')
                ->with('success', "Pengajuan pinjaman berhasil dikirim dengan ID: {$idAjuan}. Menunggu konfirmasi admin.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pengajuan: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan pengajuan. Silakan coba lagi.');
        }
    }

    /**
     * Update pengajuan (inline edit via AJAX)
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'field' => 'required|string|in:nominal,lama_ags,keterangan',
            'value' => 'required',
        ]);

        $pengajuan = PengajuanPinjaman::byUser(Auth::id())
            ->where('id', $validated['id'])
            ->first();

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan'
            ], 404);
        }

        if (!$pengajuan->canBeEditedByUser()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini tidak dapat diubah karena sudah diproses'
            ], 403);
        }

        DB::beginTransaction();
        try {
            if ($validated['field'] === 'nominal') {
                $jumlah = (int) str_replace('.', '', $validated['value']);
                
                if ($jumlah < 500000) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Minimal pinjaman adalah Rp 500.000'
                    ], 400);
                }
                
                $pengajuan->jumlah = $jumlah;
                
            } elseif ($validated['field'] === 'lama_ags') {
                $lamaAngsuran = LamaAngsuran::where('id', $validated['value'])
                    ->where('aktif', 'Y')
                    ->first();
                
                if (!$lamaAngsuran) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Lama angsuran tidak valid'
                    ], 400);
                }
                
                $pengajuan->lama_angsuran_id = $validated['value'];
                
            } elseif ($validated['field'] === 'keterangan') {
                if (strlen($validated['value']) > 500) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keterangan maksimal 500 karakter'
                    ], 400);
                }
                
                $pengajuan->keterangan = $validated['value'];
            }

            $pengajuan->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pengajuan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data'
            ], 500);
        }
    }

    /**
     * Cancel pengajuan (status = 4)
     */
    public function batal(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
        ]);

        $pengajuan = PengajuanPinjaman::byUser(Auth::id())
            ->where('id', $validated['id'])
            ->first();

        if (!$pengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan'
            ], 404);
        }

        if (!$pengajuan->canBeCancelledByUser()) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan ini tidak dapat dibatalkan karena sudah diproses'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $pengajuan->status = 4;
            $pengajuan->save();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dibatalkan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling pengajuan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pengajuan'
            ], 500);
        }
    }

    /**
     * Print pengajuan
     */
    public function cetak($id)
    {
        $pengajuan = PengajuanPinjaman::with(['anggota', 'lamaAngsuran', 'user'])
            ->byUser(Auth::id())
            ->findOrFail($id);

        return view('user.PengajuanPinjaman.Pengajuan.cetak', compact('pengajuan'));
    }
}