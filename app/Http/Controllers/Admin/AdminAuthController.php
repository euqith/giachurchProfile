<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash; // 👈 Pastikan baris ini di-import di paling atas controller!

class AdminAuthController extends Controller
{
    // Tampilkan Form Login
    public function showLogin()
    {
    // 💡 JURUS ANTI-MENTAL: Jika user sudah login DAN dia adalah Admin/Fulltimer, langsung oper ke dashboard admin
    if (auth()->check() && in_array(auth()->user()->role, ['admin', 'fulltimer'])) {
        return redirect()->route('admin.dashboard');
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

            // Cek apakah user memiliki hak akses CMS (bukan jemaat biasa)
            if (in_array(Auth::user()->role, ['admin', 'fulltimer'])) {
                return redirect()->intended(route('admin.dashboard')); // 👈 Redirect aman ke /admin/dashboard
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

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar sistem.');
    }

    // Proses Ganti Password Mandiri oleh User yang Sedang Login
// Proses Ganti Password Mandiri oleh User yang Sedang Login
    public function updatePassword(Request $request)
    {
        // 1. Validasi Super Ketat dengan Pesan Kustom
        $request->validate([
            'current_password' => ['required', 'string', 'current_password'], 
            'password'         => ['required', 'string', 'min:8', 'confirmed'], 
        ], [
            'current_password.current_password' => 'Password lama yang Anda masukkan salah.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password baru minimal harus 8 karakter.',
        ]);

        // 2. Ambil data user yang sedang login saat ini
        $user = Auth::user();
        
        // 3. Ubah properti password secara langsung dengan enkripsi Hash baru
        $user->password = Hash::make($request->password);
        
        // 4. Paksa simpan ke database menggunakan save()
        if ($user->save()) {
            return back()->with('success', 'Password Anda telah berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal memperbarui password, silakan coba lagi.');
    }
}
