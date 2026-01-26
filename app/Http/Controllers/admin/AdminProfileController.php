<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Admin\DataMaster\DataAnggota;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    /**
     * Display admin profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        return view('admin.profile.edit', compact('user'));
    }

    /**
     * Update admin profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('admin.profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Update admin profile image.
     * SINKRONISASI: Update foto di users DAN data_anggota
     */
    public function updateImage(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_image' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,gif',
                'max:2048' // 2MB
            ]
        ], [
            'profile_image.required' => 'Silakan pilih foto profil',
            'profile_image.image' => 'File harus berupa gambar',
            'profile_image.mimes' => 'Format gambar harus: JPEG, JPG, PNG, atau GIF',
            'profile_image.max' => 'Ukuran gambar maksimal 2MB'
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            $oldPhoto = $user->profile_image;

            // Store new profile image
            $file = $request->file('profile_image');
            $fileName = 'profile_' . $user->id . '_' . time() . '.' . $file->extension();
            $path = $file->storeAs('profile-images', $fileName, 'public');

            // Update user profile_image
            $user->update([
                'profile_image' => $path
            ]);

            // ✅ SINKRONISASI: Update juga foto di data_anggota jika user terhubung
            $dataAnggota = DataAnggota::where('user_id', $user->id)->first();
            
            if ($dataAnggota) {
                // Simpan foto lama data anggota
                $oldAnggotaPhoto = $dataAnggota->photo;
                
                // Update foto di data_anggota
                $dataAnggota->update([
                    'photo' => $path
                ]);

                Log::info('Profile image synced to DataAnggota', [
                    'user_id' => $user->id,
                    'anggota_id' => $dataAnggota->id,
                    'new_path' => $path
                ]);

                // Hapus foto lama dari data_anggota jika berbeda dan bukan default
                if ($oldAnggotaPhoto && 
                    $oldAnggotaPhoto !== $oldPhoto &&
                    $oldAnggotaPhoto !== 'assets/images/profile/user-1.jpg' && 
                    Storage::disk('public')->exists($oldAnggotaPhoto)) {
                    Storage::disk('public')->delete($oldAnggotaPhoto);
                    
                    Log::info('Old DataAnggota photo deleted', [
                        'path' => $oldAnggotaPhoto
                    ]);
                }
            }

            // Delete old profile image if exists (setelah update anggota)
            if ($oldPhoto && Storage::disk('public')->exists($oldPhoto)) {
                Storage::disk('public')->delete($oldPhoto);
                
                Log::info('Old user profile image deleted', [
                    'path' => $oldPhoto
                ]);
            }

            DB::commit();

            $message = 'Foto profil berhasil diperbarui';
            if ($dataAnggota) {
                $message .= ' dan tersinkronisasi dengan data anggota';
            }

            return Redirect::route('admin.profile.edit')
                ->with('status', 'image-updated')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating profile image', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return Redirect::route('admin.profile.edit')
                ->with('error', 'Gagal mengupload foto profil: ' . $e->getMessage());
        }
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return Redirect::route('admin.profile.edit')
            ->with('status', 'password-updated');
    }

    /**
     * Delete admin account.
     * SINKRONISASI: Hapus foto dari users DAN data_anggota
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            // ✅ Cari data anggota terkait
            $dataAnggota = DataAnggota::where('user_id', $user->id)->first();

            // Delete profile image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // ✅ SINKRONISASI: Hapus juga foto dari data_anggota
            if ($dataAnggota && $dataAnggota->photo) {
                // Jika foto berbeda dari user dan bukan default, hapus
                if ($dataAnggota->photo !== $user->profile_image &&
                    $dataAnggota->photo !== 'assets/images/profile/user-1.jpg' &&
                    Storage::disk('public')->exists($dataAnggota->photo)) {
                    Storage::disk('public')->delete($dataAnggota->photo);
                }

                // Set photo ke default atau null
                $dataAnggota->update([
                    'photo' => 'assets/images/profile/user-1.jpg'
                ]);

                Log::info('DataAnggota photo reset on user deletion', [
                    'anggota_id' => $dataAnggota->id
                ]);
            }

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            DB::commit();

            return Redirect::to('/');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error deleting user account', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return Redirect::route('admin.profile.edit')
                ->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}