<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AIController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

RateLimiter::for('ai', fn (Request $r) => Limit::perMinute(10)->by($r->user()?->id ?: $r->ip()));
RateLimiter::for('checkin', fn (Request $r) => Limit::perMinute(20)->by($r->user()?->id ?: $r->ip()));

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/facilities/{category}', [FacilityController::class, 'index'])->name('facilities.index');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/history', [BookingController::class, 'history'])->name('bookings.history');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');

    Route::get('/check-in', [CheckInController::class, 'index'])->name('checkin.index');
    Route::get('/check-in/scan/{token}', [CheckInController::class, 'scan'])->name('checkin.scan');
    Route::post('/check-in', [CheckInController::class, 'store'])->middleware('throttle:checkin')->name('checkin.store');

    Route::get('/assistant', [AIController::class, 'index'])->name('ai.index');
    Route::post('/assistant', [AIController::class, 'message'])->middleware('throttle:ai')->name('ai.message');
    Route::delete('/assistant', [AIController::class, 'clear'])->name('ai.clear');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Staff & admin: operasional (booking, QR, review). Admin: sisanya. Dicek di backend.
Route::middleware(['auth', 'verified', EnsureRole::class . ':admin,staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');
    Route::get('/bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings/{booking}/cancel', [Admin\BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::get('/qr', [Admin\QRController::class, 'index'])->name('qr.index');
    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
});

Route::middleware(['auth', 'verified', EnsureRole::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::get('/facilities', [Admin\FacilityController::class, 'index'])->name('facilities.index');
    Route::post('/facilities', [Admin\FacilityController::class, 'store'])->name('facilities.store');
    Route::patch('/facilities/{facility}', [Admin\FacilityController::class, 'update'])->name('facilities.update');
    Route::delete('/facilities/{facility}', [Admin\FacilityController::class, 'destroy'])->name('facilities.destroy');
    Route::get('/units', [Admin\FacilityUnitController::class, 'index'])->name('units.index');
    Route::post('/units', [Admin\FacilityUnitController::class, 'store'])->name('units.store');
    Route::patch('/units/{unit}', [Admin\FacilityUnitController::class, 'update'])->name('units.update');
    Route::delete('/units/{unit}', [Admin\FacilityUnitController::class, 'destroy'])->name('units.destroy');
    Route::post('/qr/{unit}/regenerate', [Admin\QRController::class, 'regenerate'])->name('qr.regenerate');
    Route::delete('/reviews/{review}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/settings', [Admin\SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});

Route::get('/internal/cron/process-bookings', \App\Http\Controllers\Internal\CronController::class)->middleware('throttle:10,1');

require __DIR__ . '/auth.php';
