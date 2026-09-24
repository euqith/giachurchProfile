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
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\JenisIbadahController;
use App\Http\Controllers\Admin\JemaatController;

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
| 🔐 2. JALUR GERBANG AUTENTIKASI ADMIN CMS (/adminlogin)
|--------------------------------------------------------------------------
*/
// 💡 Redirect otomatis dari /admin atau /admin/ ke /adminlogin
Route::redirect('/admin', '/adminlogin', 301);

// Tampilkan Halaman Form Login Khusus Admin
Route::get('/adminlogin', [AdminAuthController::class, 'showLogin'])->name('admin.login');

// Proses Validasi & Submit Login Admin
Route::post('/adminlogin', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Proses Keluar (Logout) Admin
Route::post('/adminlogin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');


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
    Route::resource('warta', AdminWartaController::class)->except(['show']);
    Route::patch('/warta/{id}/toggle', [AdminWartaController::class, 'toggleActive'])->name('warta.toggle');
    Route::post('/warta/{warta_id}/slides/store', [AdminWartaSlideController::class, 'store'])->name('warta.slides.store');
    Route::delete('/warta/slides/{slide_id}', [AdminWartaSlideController::class, 'destroy'])->name('warta.slides.destroy');

    // 📋 D. Modul Absensi Ibadah
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/input', [AttendanceController::class, 'input'])->name('input');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
    });

    // 🏬 E. Master Data Cabang
    Route::resource('cabang', CabangController::class)->except(['create', 'edit', 'show']);

    // 👥 F. Master Data Pengguna / Staf Admin
    Route::resource('users', AdminUserController::class)->except(['show', 'create', 'edit']);

    // 🔐 G. Profile / Change Password
    Route::patch('/update-password', [AdminAuthController::class, 'updatePassword'])->name('profile.password.update');

    // ⛪ Master Data Jenis Ibadah
    Route::resource('ibadah', JenisIbadahController::class)->except(['create', 'edit', 'show']);

    // 👥 Master Data Jemaat
    Route::resource('jemaat', JemaatController::class)->except(['create', 'edit', 'show']);

});


/*
|--------------------------------------------------------------------------
| ⛵ 4. AUTENTIKASI BAWAAN LARAVEL BREEZE
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';