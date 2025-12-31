<?php

use Inertia\Inertia;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::prefix('/api/cart')->name('api.cart.')->group(function () {
        Route::put('/{product}', [CartController::class, 'update'])->name('update');
        Route::delete('/{product}', [CartController::class, 'destroy'])->name('destroy');
        Route::post('/buy-now', [CartController::class, 'buyNow'])->name('buy-now');
    });
});

require __DIR__ . '/settings.php';
