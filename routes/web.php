<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\OauthController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\User\LoginLinkController;
use App\Http\Controllers\PreQualificationController;
use App\Http\Controllers\ScholarController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::get('/', [WelcomeController::class, 'home'])->name('home');

// Scholarship Pre-qualification (public routes)
Route::get('/pre-qualification', [PreQualificationController::class, 'create'])->name('pre-qualification.create');
Route::post('/pre-qualification', [PreQualificationController::class, 'store'])->name('pre-qualification.store');

Route::prefix('auth')->group(
    function () {
        // OAuth
        Route::get('/redirect/{provider}', [OauthController::class, 'redirect'])->name('oauth.redirect');
        Route::get('/callback/{provider}', [OauthController::class, 'callback'])->name('oauth.callback');
        // Magic Link
        Route::middleware('throttle:login-link')->group(function () {
            Route::post('/login-link', [LoginLinkController::class, 'store'])->name('login-link.store');
            Route::get('/login-link/{token}', [LoginLinkController::class, 'login'])
                ->name('login-link.login')
                ->middleware('signed');
        });
    }
);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Scholar routes
    Route::middleware(['role:scholar'])->group(function () {
        Route::get('/scholar/dashboard', [ScholarController::class, 'dashboard'])->name('scholar.dashboard');
        Route::post('/scholar/documents', [ScholarController::class, 'uploadDocument'])->name('scholar.upload-document');
        Route::post('/scholar/submit', [ScholarController::class, 'submitApplication'])->name('scholar.submit-application');
    });

    Route::delete('/auth/destroy/{provider}', [OauthController::class, 'destroy'])->name('oauth.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');

    Route::resource('/subscriptions', SubscriptionController::class)
        ->names('subscriptions')
        ->only(['index', 'create', 'store', 'show']);
});

Route::middleware(['auth', 'verified', 'role:scholar'])->group(function () {
  Route::get('/scholar/dashboard', [ScholarController::class, 'dashboard'])
  ->name('scholar.dashboard');
  Route::post('/scholar/documents', [ScholarController::class, 'uploadDocument'])
  ->name('scholar.upload-document');
  Route::post('/scholar/submit', [ScholarController::class, 'submitApplication'])
  ->name('scholar.submit-application');

  // Add this new route for scholarship applications
  Route::post('/scholar/apply-scholarship', [ScholarController::class, 'applyScholarship'])
  ->name('scholar.apply-scholarship');
});

Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware(['guest', 'pre-qualified'])
    ->name('register');
