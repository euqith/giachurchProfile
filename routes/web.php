<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminStreamController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWartaController;
use App\Http\Controllers\Admin\AdminWartaSlideController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CabangController;
use App\Http\Controllers\Admin\ChurchProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JemaatController;
use App\Http\Controllers\Admin\JenisIbadahController;
use App\Http\Controllers\Admin\SesiController;
use App\Http\Controllers\Admin\WorshipPreparationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WartaController;
use Illuminate\Support\Facades\Route;

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

// Jalur Halaman Publik Jadwal & Persiapan Ibadah
Route::get('/ibadah', [WorshipPreparationController::class, 'publicIndex'])->name('ibadah.public');
Route::get('/ibadah/{id}', [WorshipPreparationController::class, 'publicDetail'])->name('ibadah.public.detail');

/*
|--------------------------------------------------------------------------
| 🔐 2. JALUR GERBANG AUTENTIKASI ADMIN CMS (/adminlogin)
|--------------------------------------------------------------------------
*/
// Redirect otomatis dari /admin atau /admin/ ke /adminlogin
Route::redirect('/admin', '/adminlogin', 301);

// Tampilkan Halaman Form Login Khusus Admin
Route::get('/adminlogin', [AdminAuthController::class, 'showLogin'])->name('admin.login');

// Proses Validasi & Submit Login Admin
Route::post('/adminlogin', [AdminAuthController::class, 'login'])->name('admin.login.submit');

// Proses Keluar (Logout) Admin
Route::post('/adminlogin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout')->middleware('auth');

// Alias untuk route('dashboard') bawaan Laravel Breeze
Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'))->name('dashboard')->middleware('auth');

/*
|--------------------------------------------------------------------------
| 🏢 3. KELOMPOK RUTE PANEL CMS ADMIN (DILINDUNGI PENUH MIDDLEWARE)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'cms.access'])->group(function () {

    // 📊 A. Main Dashboard Panel
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
        Route::get('/get-draft', [AttendanceController::class, 'getSessionDraft'])->name('get-draft');
        Route::post('/store', [AttendanceController::class, 'store'])->name('store');
        Route::post('/guest', [AttendanceController::class, 'storeGuest'])->name('storeGuest');
        Route::get('/jemaat/{id}/detail', [AttendanceController::class, 'jemaatDetail'])->name('jemaatDetail');
        Route::get('/history', [AttendanceController::class, 'history'])->name('history');
        Route::get('/history/{id}', [AttendanceController::class, 'show'])->name('show');
        Route::put('/history/{id}', [AttendanceController::class, 'update'])->name('update');
        Route::delete('/history/{id}', [AttendanceController::class, 'destroy'])->name('destroy');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
        Route::get('/report/export-kunjungan', [AttendanceController::class, 'exportKunjungan'])->name('exportKunjungan');

        // Persiapan Ibadah
        Route::get('/persiapan', [WorshipPreparationController::class, 'index'])->name('persiapan');
        Route::get('/persiapan/schedule/create', [WorshipPreparationController::class, 'createSchedule'])->name('createSchedule');
        Route::post('/persiapan/schedule', [WorshipPreparationController::class, 'storeSchedule'])->name('storeSchedule');
        Route::post('/persiapan/upload-media', [WorshipPreparationController::class, 'uploadMedia'])->name('uploadMedia');
        Route::get('/persiapan/schedule/{id}', [WorshipPreparationController::class, 'showSchedule'])->name('showSchedule');
        Route::get('/persiapan/schedule/{id}/edit', [WorshipPreparationController::class, 'editSchedule'])->name('editSchedule');
        Route::put('/persiapan/schedule/{id}', [WorshipPreparationController::class, 'updateSchedule'])->name('updateSchedule');
        Route::delete('/persiapan/schedule/{id}', [WorshipPreparationController::class, 'destroySchedule'])->name('destroySchedule');

        // Pustaka Lagu
        Route::post('/song', [WorshipPreparationController::class, 'storeSong'])->name('storeSong');
        Route::put('/song/{id}', [WorshipPreparationController::class, 'updateSong'])->name('updateSong');
        Route::delete('/song/{id}', [WorshipPreparationController::class, 'destroySong'])->name('destroySong');
    });

    // 🏬 E. Master Data Cabang
    Route::resource('cabang', CabangController::class)->except(['create', 'edit', 'show']);

    // ⛪ F. Master Data Jenis Ibadah
    Route::resource('ibadah', JenisIbadahController::class)->except(['create', 'edit', 'show']);

    // 🕒 G. Master Data Sesi
    Route::resource('sesi', SesiController::class)->except(['create', 'edit', 'show']);
    Route::patch('/sesi/{id}/toggle', [SesiController::class, 'toggleActive'])->name('sesi.toggle');

    // 👥 H. Master Data Jemaat
    Route::get('/jemaat/export', [JemaatController::class, 'export'])->name('jemaat.export');
    Route::post('/jemaat/import', [JemaatController::class, 'import'])->name('jemaat.import');
    Route::get('/jemaat/template', [JemaatController::class, 'downloadTemplate'])->name('jemaat.template');
    Route::resource('jemaat', JemaatController::class)->except(['create', 'edit', 'show']);

    // 👤 I. Master Data Pengguna / Staf Admin
    Route::resource('users', AdminUserController::class)->except(['show', 'create', 'edit']);

    // 🔐 J. Profile / Change Password
    Route::patch('/update-password', [AdminAuthController::class, 'updatePassword'])->name('profile.password.update');

    // 🏛️ K. Profil Gereja & Banner Foto
    Route::get('/church-profile', [ChurchProfileController::class, 'index'])->name('church.index');
    Route::post('/church-profile', [ChurchProfileController::class, 'update'])->name('church.update');

    // 📡 L. Manajemen Live Streaming Cabang
    Route::get('/streams', [AdminStreamController::class, 'index'])->name('stream.index');
    Route::put('/streams/{id}', [AdminStreamController::class, 'update'])->name('stream.update');
    Route::post('/streams/{id}/toggle', [AdminStreamController::class, 'toggleLive'])->name('stream.toggle');
    Route::post('/streams/auto-detect', [AdminStreamController::class, 'autoDetectAll'])->name('stream.auto-detect');
    Route::post('/streams/{id}/auto-detect', [AdminStreamController::class, 'autoDetectSingle'])->name('stream.auto-detect.single');
    Route::post('/streams/api-key', [AdminStreamController::class, 'updateApiKey'])->name('stream.api-key');

});

/*
|--------------------------------------------------------------------------
| ⛵ 4. AUTENTIKASI BAWAAN LARAVEL BREEZE
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
