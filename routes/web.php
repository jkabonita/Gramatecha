<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PresentationController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Public presentation routes
Route::get('/presentations', [PresentationController::class, 'index'])->name('presentations.index');
Route::get('/presentations/{presentation}', [PresentationController::class, 'show'])->name('presentations.show');

// Admin presentation routes (requires authentication)
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/presentations/create', [PresentationController::class, 'create'])->name('admin.presentations.create');
    Route::post('/presentations', [PresentationController::class, 'store'])->name('admin.presentations.store');
    Route::get('/presentations/{presentation}/edit', [PresentationController::class, 'edit'])->name('admin.presentations.edit');
    Route::put('/presentations/{presentation}', [PresentationController::class, 'update'])->name('admin.presentations.update');
    Route::delete('/presentations/{presentation}', [PresentationController::class, 'destroy'])->name('admin.presentations.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/settings.php';
