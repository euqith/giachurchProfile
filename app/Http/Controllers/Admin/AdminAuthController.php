<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    // Tampilkan Form Login
    public function showLogin()
    {
        // 💡 Ambil slug role user jika sedang login (Handling Object Role / String)
        if (auth()->check()) {
            $userRole = auth()->user()->role->slug ?? auth()->user()->role;
            
            if (in_array($userRole, ['admin', 'fulltimer', 'pendeta'])) {
                return redirect()->route('admin.dashboard');
            }
        }

        // Jika belum login, tampilkan halaman login seperti biasa
        return view('admin.auth.login');
    }

    // Proses Autentikasi Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Coba login dengan kredensial input
        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            // 💡 Cek slug role dari relasi Role model
            $userRole = $user->role->slug ?? $user->role;

            // Cek apakah user memiliki hak akses CMS
            if (in_array($userRole, ['admin', 'fulltimer', 'pendeta'])) {
                // Paksa redirect langsung ke route nama tanpa membawa jejak session intended lama
                return redirect()->route('admin.dashboard');
            }

            // Jika jemaat biasa mencoba masuk, logout otomatis
            Auth::logout();
            return redirect()->route('admin.login')->with('error', 'Akses ditolak. Akun Anda tidak memiliki otoritas CMS.');
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    // Proses Logout Admin
public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Gunakan redirect ke URL /admin secara eksplisit agar aman di lokal maupun server
    return redirect('/admin')->with('success', 'Anda telah berhasil keluar sistem.');
}

    // Proses Ganti Password Mandiri
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'], 
            'password'         => ['required', 'string', 'min:8', 'confirmed'], 
        ], [
            'current_password.current_password' => 'Password lama yang Anda masukkan salah.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password baru minimal harus 8 karakter.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        
        if ($user->save()) {
            return back()->with('success', 'Password Anda telah berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui password, silakan coba lagi.');
    }
}