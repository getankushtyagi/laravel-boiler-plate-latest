<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// social media login routes
Route::middleware(['web'])->group(function () {
    Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
        ->name('social.redirect');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
        ->name('social.callback');
});

require __DIR__ . '/auth.php';
