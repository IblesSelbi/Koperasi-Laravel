<?php

namespace App\Http\Controllers\User;

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

class UserProfileController extends Controller
{
    /**
     * Display user profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        return view('user.profile.edit', compact('user'));
    }

    /**
     * Update user profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('user.profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Update user profile image.
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

            return Redirect::route('user.profile.edit')
                ->with('status', 'image-updated')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error updating profile image', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return Redirect::route('user.profile.edit')
                ->with('error', 'Gagal mengupload foto profil: ' . $e->getMessage());
        }
    }

    /**
     * Update user password.
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

        return Redirect::route('user.profile.edit')
            ->with('status', 'password-updated');
    }
}