<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Admin bisa akses semua
        if ($user->role->nama === 'admin') {
            return $next($request);
        }

        // User hanya bisa akses role sendiri
        if ($user->role->nama !== $role) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}