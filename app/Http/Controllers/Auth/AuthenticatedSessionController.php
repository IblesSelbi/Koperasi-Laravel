<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // Pastikan relasi role DAN anggota kebaca
        $user->load(['role', 'anggota']);

        Log::info('User Login Attempt', [
            'user_id' => $user->id,
            'username' => $user->name,
            'email' => $user->email,
            'role_id' => $user->role_id,
            'role_nama' => $user->role->nama ?? 'NO ROLE',
            'has_anggota' => $user->anggota ? true : false,
            'anggota_id' => $user->anggota ? $user->anggota->id : null
        ]);

        // Cek apakah user punya role
        if (!$user->role) {
            Auth::logout();
            Log::error('User tidak memiliki role', ['user_id' => $user->id]);
            return redirect('/login')->withErrors(['email' => 'Akun Anda bermasalah. Hubungi administrator.']);
        }

        // Untuk user biasa, cek apakah punya data anggota
        if ($user->role->nama === 'user' && !$user->anggota) {
            Auth::logout();
            Log::error('User tidak memiliki data anggota', ['user_id' => $user->id]);
            return redirect('/login')->withErrors(['email' => 'Akun Anda belum terhubung dengan data anggota. Hubungi administrator.']);
        }

        // Untuk user biasa, cek status aktif (PERBAIKAN DI SINI)
        // Tambahkan pengecekan $user->anggota tidak null dulu
        if ($user->role->nama === 'user' && $user->anggota && $user->anggota->aktif !== 'Aktif') {
            Auth::logout();
            Log::error('Anggota tidak aktif', [
                'user_id' => $user->id,
                'status' => $user->anggota->aktif
            ]);
            return redirect('/login')->withErrors(['email' => 'Akun anggota Anda tidak aktif. Hubungi administrator.']);
        }

        // Redirect berdasarkan role
        if ($user->role->nama === 'admin') {
            Log::info('Redirecting to admin dashboard');
            return redirect()->intended(route('admin.dashboard'));
        }

        // User biasa
        Log::info('Redirecting to user dashboard');
        return redirect()->intended(route('user.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}