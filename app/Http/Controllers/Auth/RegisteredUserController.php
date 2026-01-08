<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,user'],
        ]);

        // Ambil role sesuai pilihan
        $selectedRole = $request->input('role');
        $role = Role::where('nama', $selectedRole)->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $role->id,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect('/admin/dashboard')
            ->with('success', "Akun {$user->name} berhasil dibuat sebagai {$selectedRole}.");
    }

}
