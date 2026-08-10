<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PresentationController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', [LandingController::class, 'index'])->name('landing');

// Static info pages
Route::inertia('/about', 'About')->name('about');
Route::inertia('/faq', 'Faq')->name('faq');


// Public presentation routes (PDFs and slides only)
Route::get('/presentations', [PresentationController::class, 'index'])->name('presentations.index');
Route::get('/presentations/{presentation}', [PresentationController::class, 'show'])->name('presentations.show');
Route::get('/presentations/{presentation}/slides', [PresentationController::class, 'slides'])->name('presentations.slides');

// Public video routes
Route::get('/videos', [PresentationController::class, 'videoIndex'])->name('videos.index');


// Streaming and download routes - removed from this file
// These are now in routes/streaming.php with minimal middleware

// Public quiz routes
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{quiz:slug}', [QuizController::class, 'show'])->name('quizzes.show');
Route::post('/quizzes/{quiz:slug}/start', [QuizController::class, 'start'])->name('quizzes.start');
Route::post('/quizzes/{quiz:slug}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
Route::get('/quizzes/{quiz:slug}/results/{attempt}', [QuizController::class, 'results'])->name('quizzes.results');

// Admin routes — accessible only to users with role = 'admin'
Route::middleware(['admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Quiz attempts
    Route::get('/quiz-attempts', [QuizController::class, 'adminAttempts'])->name('admin.quiz-attempts');
    Route::get('/quiz-attempts/{attempt}', [QuizController::class, 'adminAttemptDetails'])->name('admin.quiz-attempts.show');
    
    // Presentation routes
    Route::get('/presentations', [PresentationController::class, 'adminIndex'])->name('admin.presentations.index');
    Route::get('/presentations/create', [PresentationController::class, 'create'])->name('admin.presentations.create');
    Route::get('/presentations/bulk-create', [PresentationController::class, 'bulkCreate'])->name('admin.presentations.bulk-create');
    Route::post('/presentations', [PresentationController::class, 'store'])->name('admin.presentations.store');
    Route::post('/presentations/bulk-store', [PresentationController::class, 'bulkStore'])->name('admin.presentations.bulk-store');
    Route::post('/presentations/generate', [PresentationController::class, 'generate'])->name('admin.presentations.generate');
    Route::get('/presentations/{presentation}', [PresentationController::class, 'adminShow'])->name('admin.presentations.show');
    Route::get('/presentations/{presentation}/edit', [PresentationController::class, 'edit'])->name('admin.presentations.edit');
    Route::put('/presentations/{presentation}', [PresentationController::class, 'update'])->name('admin.presentations.update');
    Route::delete('/presentations/{presentation}', [PresentationController::class, 'destroy'])->name('admin.presentations.destroy');
    Route::delete('/presentations-bulk', [PresentationController::class, 'bulkDestroy'])->name('admin.presentations.bulk-destroy');
    
    // Category routes
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::get('/categories/active', [CategoryController::class, 'getActive'])->name('categories.active');
    
    // Diagnostic route
    Route::get('/diagnostics/upload', [\App\Http\Controllers\Admin\UploadDiagnosticController::class, 'diagnose'])->name('admin.diagnostics.upload');
});

Route::middleware(['admin'])->group(function () {
    Route::get('dashboard', function () {
        return redirect('/admin/dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
