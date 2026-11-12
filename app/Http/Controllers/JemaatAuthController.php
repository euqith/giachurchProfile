<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class JemaatAuthController extends Controller
{
    /**
     * Tampilkan Halaman Login Jemaat
     */
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isJemaat()) {
                $redirect = $request->query('redirect', route('jemaat.profil'));

                return redirect($redirect);
            }
        }

        $redirect = $request->query('redirect', '');

        return view('homepage.auth.login', compact('redirect'));
    }

    /**
     * Proses Login Jemaat via Username / Email & Password
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($request->login);

        // Cari berdasarkan username ATAU email
        $user = User::where(function ($q) use ($loginInput) {
            $q->where('username', $loginInput)
                ->orWhere('email', $loginInput);
        })->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            $redirectUrl = $request->filled('redirect') ? $request->redirect : route('jemaat.profil');

            return redirect($redirectUrl)->with('success', 'Selamat datang kembali, '.($user->name ?: $user->username).'! Tuhan memberkati.');
        }

        return back()->withInput($request->only('login', 'remember'))
            ->withErrors(['login' => 'Username atau password yang Anda masukkan tidak sesuai.']);
    }

    /**
     * Tampilkan Halaman Pendaftaran (Register) Jemaat
     */
    public function showRegister(Request $request)
    {
        if (Auth::check() && Auth::user()->isJemaat()) {
            return redirect()->route('jemaat.profil');
        }

        $redirect = $request->query('redirect', '');

        return view('homepage.auth.register', compact('redirect'));
    }

    /**
     * Validasi ketersediaan username secara realtime (AJAX)
     */
    public function checkUsername(Request $request)
    {
        $username = strtolower(trim($request->query('username', '')));

        if (empty($username) || strlen($username) < 3) {
            return response()->json([
                'available' => false,
                'message' => 'Username minimal 3 karakter.',
            ]);
        }

        if (! preg_match('/^[a-z0-9_]+$/', $username)) {
            return response()->json([
                'available' => false,
                'message' => 'Username hanya boleh huruf kecil, angka, dan underscore (_).',
            ]);
        }

        $exists = User::where('username', $username)->exists();

        return response()->json([
            'available' => ! $exists,
            'message' => $exists ? 'Username sudah digunakan, silakan pilih yang lain.' : 'Username tersedia!',
        ]);
    }

    /**
     * Proses Pendaftaran Jemaat (Hanya perlu Username & Password)
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:40',
                'regex:/^[a-zA-Z0-9_]+$/',
                'unique:users,username',
            ],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, dan underscore (_).',
            'username.unique' => 'Username ini sudah terdaftar. Silakan pilih username lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $cleanUsername = strtolower(trim($request->username));

        // Buat user baru dengan role jemaat
        $user = User::create([
            'name' => ucfirst($cleanUsername),
            'username' => $cleanUsername,
            'email' => $cleanUsername.'@jemaat.local',
            'password' => Hash::make($request->password),
            'role' => 'jemaat',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirectUrl = $request->filled('redirect') ? $request->redirect : route('jemaat.profil');

        return redirect($redirectUrl)->with('success', 'Akun jemaat Anda berhasil didaftarkan! Selamat datang di keluarga GIA Surabaya.');
    }

    /**
     * Redirect ke Google OAuth
     */
    public function redirectToGoogle(Request $request)
    {
        $clientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));
        $clientSecret = config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET'));

        // Simpan redirect target jika ada
        if ($request->filled('redirect')) {
            session(['jemaat_oauth_redirect' => $request->redirect]);
        }

        // Jika Google API credentials belum di-setup di .env, sediakan instruksi
        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('jemaat.login')
                ->with('error', 'Google Sign-In membutuhkan konfigurasi GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET di file .env server. Anda tetap dapat masuk atau daftar dengan Username & Password di bawah.');
        }

        $redirectUri = config('services.google.redirect') ?: route('jemaat.google.callback');
        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
            'prompt' => 'select_account',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$query);
    }

    /**
     * Callback dari Google OAuth
     */
    public function handleGoogleCallback(Request $request)
    {
        $code = $request->code;
        $state = $request->state;

        if (empty($code) || $state !== session('oauth_state')) {
            return redirect()->route('jemaat.login')->with('error', 'Autentikasi Google dibatalkan atau sesi kedaluwarsa.');
        }

        $clientId = config('services.google.client_id', env('GOOGLE_CLIENT_ID'));
        $clientSecret = config('services.google.client_secret', env('GOOGLE_CLIENT_SECRET'));
        $redirectUri = config('services.google.redirect') ?: route('jemaat.google.callback');

        try {
            // Tukar code dengan access token
            $tokenRes = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if (! $tokenRes->successful()) {
                return redirect()->route('jemaat.login')->with('error', 'Gagal memperoleh otorisasi dari Google.');
            }

            $accessToken = $tokenRes->json('access_token');

            // Ambil profile user dari Google UserInfo API
            $userInfoRes = Http::withToken($accessToken)->timeout(10)->get('https://www.googleapis.com/oauth2/v3/userinfo');

            if (! $userInfoRes->successful()) {
                return redirect()->route('jemaat.login')->with('error', 'Gagal memuat profil akun Google.');
            }

            $googleData = $userInfoRes->json();
            $googleId = $googleData['sub'] ?? null;
            $email = $googleData['email'] ?? null;
            $name = $googleData['name'] ?? 'Jemaat Google';
            $picture = $googleData['picture'] ?? null;

            if (! $googleId) {
                return redirect()->route('jemaat.login')->with('error', 'Data Google ID tidak valid.');
            }

            // Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleId)
                ->when($email, fn ($q) => $q->orWhere('email', $email))
                ->first();

            if (! $user) {
                // Generate username unik dari nama / email
                $base = strtolower(preg_replace('/[^a-z0-9_]/', '', explode('@', $email ?: $name)[0]));
                if (empty($base)) {
                    $base = 'jemaat';
                }
                $candidate = $base;
                $i = 1;
                while (User::where('username', $candidate)->exists()) {
                    $candidate = $base.$i;
                    $i++;
                }

                $user = User::create([
                    'name' => $name,
                    'username' => $candidate,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $picture,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'jemaat',
                ]);
            } else {
                // Update avatar atau google_id jika belum ada
                $update = [];
                if (empty($user->google_id)) {
                    $update['google_id'] = $googleId;
                }
                if (empty($user->avatar) && $picture) {
                    $update['avatar'] = $picture;
                }
                if (! empty($update)) {
                    $user->update($update);
                }
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            $intendedRedirect = session()->pull('jemaat_oauth_redirect', route('jemaat.profil'));

            return redirect($intendedRedirect)->with('success', 'Berhasil masuk dengan akun Google! Selamat datang, '.$user->name.'.');
        } catch (\Throwable $e) {
            return redirect()->route('jemaat.login')->with('error', 'Terjadi kesalahan saat memproses Google Sign-In: '.$e->getMessage());
        }
    }

    /**
     * Halaman Profil Jemaat
     */
    public function profile()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('jemaat.login');
        }

        // Ambil riwayat artikel yang disukai oleh jemaat
        $likedArticles = $user->likes()
            ->with(['article' => fn ($q) => $q->where('isDelete', false)])
            ->latest()
            ->take(20)
            ->get()
            ->pluck('article')
            ->filter();

        // Ambil komentar yang ditulis oleh jemaat
        $myComments = $user->comments()
            ->with(['article' => fn ($q) => $q->where('isDelete', false)])
            ->where('isDelete', false)
            ->latest()
            ->take(20)
            ->get();

        return view('homepage.auth.profile', compact('user', 'likedArticles', 'myComments'));
    }

    /**
     * Perbarui Profil Jemaat
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('jemaat.login');
        }

        $request->validate([
            'name' => 'required|string|max:100',
            'username' => [
                'required',
                'string',
                'min:3',
                'max:40',
                'regex:/^[a-zA-Z0-9_]+$/',
                Rule::unique('users')->ignore($user->id),
            ],
            'telepon' => 'nullable|string|max:30',
            'bio' => 'nullable|string|max:300',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'username' => strtolower(trim($request->username)),
            'telepon' => $request->telepon,
            'bio' => $request->bio,
        ];

        // Jika upload avatar baru
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'avatar_'.$user->id.'_'.time().'.'.$file->getClientOriginalExtension();
            $targetDir = storage_path('app/public/avatars');
            if (! File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            $file->move($targetDir, $fileName);
            $updateData['avatar'] = $fileName;
        }

        // Jika ganti password
        if ($request->filled('new_password')) {
            $updateData['password'] = Hash::make($request->new_password);
        }

        $user->update($updateData);

        return redirect()->route('jemaat.profil')->with('success', 'Profil Anda berhasil diperbarui!');
    }

    /**
     * Logout Jemaat
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil keluar. Sampai jumpa kembali!');
    }
}
