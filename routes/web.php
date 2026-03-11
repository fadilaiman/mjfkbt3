<?php

use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [App\Http\Controllers\Public\HomeController::class, 'index'])->name('home');
Route::get('/kuliah', [App\Http\Controllers\Public\KuliahController::class, 'index'])->name('kuliah');
Route::get('/aktiviti', [App\Http\Controllers\Public\AktivitiController::class, 'index'])->name('aktiviti');
Route::get('/pengumuman', [App\Http\Controllers\Public\PengumumanController::class, 'index'])->name('pengumuman');
Route::get('/derma', [App\Http\Controllers\Public\DermaController::class, 'index'])->name('derma');
Route::get('/hubungi', [App\Http\Controllers\Public\HubungiController::class, 'index'])->name('hubungi');
Route::get('/dasar-privasi', [App\Http\Controllers\Public\StaticPageController::class, 'dasarPrivasi'])->name('dasar-privasi');
Route::get('/terma-penggunaan', [App\Http\Controllers\Public\StaticPageController::class, 'termaPenggunaan'])->name('terma-penggunaan');

// Admin auth routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [App\Http\Controllers\Admin\AdminAuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [App\Http\Controllers\Admin\AdminAuthController::class, 'logout'])->name('admin.logout');
});

// Admin protected routes
Route::prefix('admin')->middleware(['auth:admin', 'admin.active'])->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Pengumuman CRUD
    Route::get('/pengumuman', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('admin.pengumuman.index');
    Route::get('/pengumuman/cipta', [App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->name('admin.pengumuman.create');
    Route::post('/pengumuman', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->name('admin.pengumuman.store');
    Route::get('/pengumuman/{id}', [App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->name('admin.pengumuman.edit');
    Route::put('/pengumuman/{id}', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->name('admin.pengumuman.update');
    Route::delete('/pengumuman/{id}', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->name('admin.pengumuman.destroy');

    // Aktiviti CRUD
    Route::get('/aktiviti', [App\Http\Controllers\Admin\EventController::class, 'index'])->name('admin.aktiviti.index');
    Route::get('/aktiviti/cipta', [App\Http\Controllers\Admin\EventController::class, 'create'])->name('admin.aktiviti.create');
    Route::post('/aktiviti', [App\Http\Controllers\Admin\EventController::class, 'store'])->name('admin.aktiviti.store');
    Route::get('/aktiviti/{id}', [App\Http\Controllers\Admin\EventController::class, 'edit'])->name('admin.aktiviti.edit');
    Route::put('/aktiviti/{id}', [App\Http\Controllers\Admin\EventController::class, 'update'])->name('admin.aktiviti.update');
    Route::delete('/aktiviti/{id}', [App\Http\Controllers\Admin\EventController::class, 'destroy'])->name('admin.aktiviti.destroy');

    // Derma
    Route::get('/derma', [App\Http\Controllers\Admin\DonationController::class, 'index'])->name('admin.derma.index');
    Route::get('/derma/{id}', [App\Http\Controllers\Admin\DonationController::class, 'edit'])->name('admin.derma.edit');
    Route::put('/derma/{id}', [App\Http\Controllers\Admin\DonationController::class, 'update'])->name('admin.derma.update');

    // Kandungan moderation
    Route::get('/kandungan/youtube', [App\Http\Controllers\Admin\ContentModerationController::class, 'youtube'])->name('admin.kandungan.youtube');
    Route::get('/kandungan/facebook', [App\Http\Controllers\Admin\ContentModerationController::class, 'facebook'])->name('admin.kandungan.facebook');
    Route::post('/kandungan/facebook', [App\Http\Controllers\Admin\ContentModerationController::class, 'storeFacebook'])->name('admin.kandungan.facebook.store');
    Route::delete('/kandungan/facebook/{id}', [App\Http\Controllers\Admin\ContentModerationController::class, 'destroyFacebook'])->name('admin.kandungan.facebook.destroy');
    Route::get('/kandungan/tiktok', [App\Http\Controllers\Admin\ContentModerationController::class, 'tiktok'])->name('admin.kandungan.tiktok');
    Route::put('/kandungan/{type}/{id}/toggle', [App\Http\Controllers\Admin\ContentModerationController::class, 'toggle'])->name('admin.kandungan.toggle');
    Route::post('/kandungan/tiktok', [App\Http\Controllers\Admin\ContentModerationController::class, 'storeTiktok'])->name('admin.kandungan.tiktok.store');
    Route::delete('/kandungan/tiktok/{id}', [App\Http\Controllers\Admin\ContentModerationController::class, 'destroyTiktok'])->name('admin.kandungan.tiktok.destroy');

    // Fail / Media
    Route::get('/fail', [App\Http\Controllers\Admin\MediaController::class, 'index'])->name('admin.fail.index');
    Route::post('/fail', [App\Http\Controllers\Admin\MediaController::class, 'store'])->name('admin.fail.store');
    Route::delete('/fail/{id}', [App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('admin.fail.destroy');

    // WhatsApp contacts
    Route::get('/whatsapp', [App\Http\Controllers\Admin\WhatsappContactController::class, 'index'])->name('admin.whatsapp.index');
    Route::put('/whatsapp/{id}', [App\Http\Controllers\Admin\WhatsappContactController::class, 'update'])->name('admin.whatsapp.update');

    // Log
    Route::get('/log', [App\Http\Controllers\Admin\AdminLogController::class, 'index'])->name('admin.log.index');

    // Tetapan
    Route::get('/tetapan', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.tetapan.index');
    Route::put('/tetapan', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('admin.tetapan.update');
});
