<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\WartaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminWartaController;
use App\Http\Controllers\Admin\AdminWartaSlideController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminAuthController; // 👈 Controller Auth Baru untuk Admin


/*
|--------------------------------------------------------------------------
| 🌐 1. JALUR HALAMAN UTAMA (FRONTEND / PUBLIK)
|--------------------------------------------------------------------------
*/
// Jalur Halaman Utama (Homepage)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Jalur Halaman Khusus Daftar Semua Event Publik
Route::get('/event', [EventController::class, 'index'])->name('event.index');

// Jalur Halaman Khusus Buku Warta Jemaat Publik
Route::get('/warta', [WartaController::class, 'index'])->name('warta.index');


/*
|--------------------------------------------------------------------------
| 🔐 2. JALUR GERBANG AUTENTIKASI ADMIN CMS (/admin)
|--------------------------------------------------------------------------
| Ditempatkan di luar grup 'admin.' agar nama rute dan URL-nya murni.
| Dilindungi middleware 'guest' agar yang sudah login tidak bisa masuk lagi.
*/
// 🔐 2. JALUR GERBANG AUTENTIKASI ADMIN CMS (/admin)
// Kita lepas middleware ['guest'] agar controller bisa mengatur redirect secara pintar

// Tampilkan Halaman Form Login Khusus Admin
Route::get('/admin', [AdminAuthController::class, 'showLogin'])->name('admin.login');

// Proses Validasi & Submit Login Admin
Route::post('/admin', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Proses Keluar (Logout) Admin - Wajib sudah login untuk mengaksesnya
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');


/*
|--------------------------------------------------------------------------
| 🏢 3. KELOMPOK RUTE PANEL CMS ADMIN (DILINDUNGI PENUH MIDDLEWARE)
|--------------------------------------------------------------------------
| Semua rute di dalam grup ini otomatis memiliki prefix URL '/admin/...'
| dan nama rute berawalan 'admin....'
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'cms.access'])->group(function () {
    
    // 📊 A. Main Dashboard Panel (Diakses via /admin/dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 📅 B. Manajemen CRUD Event Gereja
    Route::resource('event', AdminEventController::class);

    // 📄 C. Manajemen CRUD Warta Jemaat Digital
    // 1. Rute Utama Induk Warta (Tanpa halaman show mandiri)
    Route::resource('warta', AdminWartaController::class)->except(['show']);
    
    // 2. Fitur Saklar Aktif/Nonaktif Warta Utama
    Route::patch('/warta/{id}/toggle', [AdminWartaController::class, 'toggleActive'])->name('warta.toggle');
    
    // 3. Manajemen Lembaran Gambar/Slide Anak Warta
    Route::post('/warta/{warta_id}/slides/store', [AdminWartaSlideController::class, 'store'])->name('warta.slides.store');
    Route::delete('/warta/slides/{slide_id}', [AdminWartaSlideController::class, 'destroy'])->name('warta.slides.destroy');
    
    // 👥 D. Master Manajemen CRUD Pengguna (Staf Admin & Fulltimer)
    // Dibatasi membuang rute show, create, dan edit karena semua memakai sistem Pop-up Modal terpadu.
    Route::resource('users', AdminUserController::class)->except(['show', 'create', 'edit']);

    // 🔐 Rute Khusus Staf untuk Mengubah Password Sendiri
Route::patch('/update-password', [AdminAuthController::class, 'updatePassword'])->name('profile.password.update');
});


/*
|--------------------------------------------------------------------------
| ⛵ 4. AUTENTIKASI BAWAAN LARAVEL BREEZE
|--------------------------------------------------------------------------
| Jalur bawaan tetap diletakkan di paling bawah agar tidak mengganggu rute kustom.
*/
require __DIR__.'/auth.php';