<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AnnouncementController as AdminAnnouncementController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EducationArticleController as AdminEducationArticleController;
use App\Http\Controllers\Admin\GuideController as AdminGuideController;
use App\Http\Controllers\Admin\IntegrationController;
use App\Http\Controllers\Admin\NotificationSettingController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\AirQualityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\GuideController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HotspotMapController;
use App\Http\Middleware\EnsureAdmin;
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
Route::get('/panduan', [GuideController::class, 'index'])->name('guides.index');
Route::get('/panduan/{slug}', [GuideController::class, 'show'])->name('guides.show');

// Halaman Edukasi Karhutla
Route::get('/edukasi', [EducationController::class, 'index'])->name('edukasi.index');
Route::get('/edukasi/{slug}', [EducationController::class, 'show'])->name('edukasi.show');

// Halaman Pengumuman & Peringatan Dini
Route::get('/pengumuman', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/pengumuman/{slug}', [AnnouncementController::class, 'show'])->name('announcements.show');

// Halaman Tentang FireAware Kalimantan
Route::get('/tentang', [AboutController::class, 'index'])->name('about');
Route::get('/about', fn () => redirect()->route('about'));

// Endpoint JSON notifikasi browser aktif untuk masyarakat di portal publik
Route::get('/api/notifications/active', [NotificationSettingController::class, 'activePublicAlerts'])->name('notifications.active');

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
    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('guides', AdminGuideController::class);
        Route::resource('education', AdminEducationArticleController::class);
        Route::resource('announcements', AdminAnnouncementController::class);
        Route::get('/wilayah', [RegionController::class, 'index'])->name('regions.index');
        Route::post('/wilayah', [RegionController::class, 'store'])->name('regions.store');
        Route::put('/wilayah/{province}', [RegionController::class, 'update'])->name('regions.update');
        Route::get('/integrasi', [IntegrationController::class, 'index'])->name('integrations.index');
        Route::post('/integrasi/sync/{source}', [IntegrationController::class, 'sync'])->name('integrations.sync');
        Route::get('/notifikasi', [NotificationSettingController::class, 'index'])->name('notifications.index');
        Route::put('/notifikasi', [NotificationSettingController::class, 'update'])->name('notifications.update');
        Route::post('/notifikasi/test', [NotificationSettingController::class, 'testNotification'])->name('notifications.test');
    });
});
