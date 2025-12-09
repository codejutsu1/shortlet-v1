<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PropertyController::class, 'homepage'])->name('home');

// Property Routes
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');

// Booking Routes (protected)
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [\App\Http\Controllers\ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Booking Routes
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store')->middleware('throttle.booking');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Payment Routes
    Route::post('/payments/initialize/{booking}', [PaymentController::class, 'initialize'])->name('payments.initialize')->middleware('throttle.payment');
    Route::get('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/success/{booking}', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failed', [PaymentController::class, 'failed'])->name('payments.failed');

    // Review Routes
    Route::get('/bookings/{booking}/review', [\App\Http\Controllers\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/bookings/{booking}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');
});

// Paystack Webhook (no auth required)
Route::post('/webhooks/paystack', [PaymentController::class, 'webhook'])->name('webhooks.paystack');

// Registration Routes
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle.login');

// Authentication Routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');
Route::post('/login', [LoginController::class, 'store'])->middleware('throttle.login');
Route::post('/logout', [LogoutController::class, 'destroy'])->middleware('auth')->name('logout');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
    ->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');
