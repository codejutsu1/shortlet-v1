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
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Payment Routes
    Route::post('/payments/initialize/{booking}', [PaymentController::class, 'initialize'])->name('payments.initialize');
    Route::get('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/success/{booking}', [PaymentController::class, 'success'])->name('payments.success');
    Route::get('/payments/failed', [PaymentController::class, 'failed'])->name('payments.failed');
});

// Paystack Webhook (no auth required)
Route::post('/webhooks/paystack', [PaymentController::class, 'webhook'])->name('webhooks.paystack');

// Registration Routes
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

// Authentication Routes
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LogoutController::class, 'destroy'])->middleware('auth')->name('logout');

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
    ->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');
