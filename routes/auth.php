<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\GitHubOAuthController;
use App\Http\Controllers\Auth\MfaChallengeController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SecurityConfirmationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:auth-login')
        ->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:auth-registration')
        ->name('register.store');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('throttle:auth-recovery')
        ->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('throttle:auth-recovery')
        ->name('password.store');
});

Route::get('/mfa-challenge', [MfaChallengeController::class, 'create'])->name('mfa.challenge');
Route::post('/mfa-challenge', [MfaChallengeController::class, 'store'])
    ->middleware('throttle:auth-mfa')
    ->name('mfa.challenge.store');

Route::get('/auth/github/redirect', [GitHubOAuthController::class, 'redirect'])
    ->middleware('throttle:auth-oauth')
    ->name('auth.github.redirect');
Route::get('/auth/github/callback', [GitHubOAuthController::class, 'callback'])
    ->middleware('throttle:auth-oauth')
    ->name('auth.github.callback');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'login.enabled'])->group(function (): void {
    Route::get('/verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->whereUuid('id')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'login.enabled', 'verified'])->group(function (): void {
    Route::get('/confirm-security', [SecurityConfirmationController::class, 'create'])->name('security.confirm');
    Route::post('/confirm-security', [SecurityConfirmationController::class, 'store'])
        ->middleware('throttle:auth-sensitive')
        ->name('security.confirm.store');
});
