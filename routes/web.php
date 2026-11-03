<?php

use App\Http\Controllers\AirQualityController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotspotMapController;
use Illuminate\Support\Facades\Route;

// --- Halaman publik -----------------------------------------------------
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman peta interaktif
Route::get('/peta-hotspot', [HotspotMapController::class, 'index'])->name('hotspot.map');

// Halaman kualitas udara
Route::get('/kualitas-udara', [AirQualityController::class, 'index'])->name('air.quality');

// Endpoint JSON kecil untuk memuat ringkasan per provinsi (dipakai JS pemilih wilayah)
Route::get('/api/ringkasan', [HomeController::class, 'summary'])->name('home.summary');

// Endpoint data titik panas untuk peta Leaflet
Route::get('/api/hotspot-map/data', [HotspotMapController::class, 'data'])->name('hotspot.map.data');

// Halaman Panduan Keselamatan
Route::get('/panduan', [\App\Http\Controllers\GuideController::class, 'index'])->name('guides.index');
Route::get('/panduan/{slug}', [\App\Http\Controllers\GuideController::class, 'show'])->name('guides.show');

// Halaman Edukasi Karhutla
Route::get('/edukasi', [\App\Http\Controllers\EducationController::class, 'index'])->name('edukasi.index');
Route::get('/edukasi/{slug}', [\App\Http\Controllers\EducationController::class, 'show'])->name('edukasi.show');

// Halaman Pengumuman & Peringatan Dini
Route::get('/pengumuman', [\App\Http\Controllers\AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/pengumuman/{slug}', [\App\Http\Controllers\AnnouncementController::class, 'show'])->name('announcements.show');

// --- Autentikasi admin --------------------------------------------------
Route::prefix('admin')->name('admin.')->group(function () {
    // Route login (tamu saja)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    // Area admin yang dilindungi middleware auth
    Route::middleware(\App\Http\Middleware\EnsureAdmin::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('guides', \App\Http\Controllers\Admin\GuideController::class)->except(['show']);
        Route::resource('education', \App\Http\Controllers\Admin\EducationArticleController::class)->except(['show']);
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show']);
    });
});
