<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsCMSAccessible
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login atau belum
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Ambil role user yang sedang login
        $role = Auth::user()->role;

        // 3. Hanya izinkan jika role-nya adalah admin ATAU fulltimer
        if ($role === 'admin' || $role === 'fulltimer') {
            return $next($request); // Lolos! Silakan masuk ke CMS
        }

        // 4. Jika jemaat biasa mencoba menerobos, tendang ke beranda dengan pesan peringatan
        return redirect('/')->with('error', 'Akses ditolak! Halaman tersebut khusus Admin dan Fulltimer.');
    }
}