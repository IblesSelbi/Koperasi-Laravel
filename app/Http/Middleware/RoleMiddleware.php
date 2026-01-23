<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Load relasi role jika belum
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $userRole = $user->role->nama;

        Log::info('RoleMiddleware Check', [
            'user_id' => $user->id,
            'user_role' => $userRole,
            'required_role' => $role,
            'path' => $request->path()
        ]);

        // Cek apakah role sesuai
        if ($userRole !== $role) {
            Log::warning('Role mismatch', [
                'expected' => $role,
                'actual' => $userRole
            ]);

            // Redirect ke dashboard sesuai role user
            if ($userRole === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'user') {
                return redirect()->route('user.dashboard');
            }
            
            // Role tidak dikenali
            abort(403, 'Unauthorized access - Unknown role: ' . $userRole);
        }

        Log::info('RoleMiddleware Passed', ['role' => $role]);
        
        return $next($request);
    }
}