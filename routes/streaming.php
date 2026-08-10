<?php

use App\Http\Controllers\PresentationController;
use App\Http\Controllers\VideoStreamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Streaming Routes
|--------------------------------------------------------------------------
|
| These routes are optimized for high-performance file streaming.
| They bypass the web middleware stack to minimize overhead.
| Only essential middleware is applied.
|
| The VideoStreamController implements HTTP Range Requests (RFC 7233)
| for efficient byte-range streaming of large video files.
|
*/

// Video streaming route - optimized with byte-range support
Route::get('/presentations/{presentation}/stream', [VideoStreamController::class, 'streamAny'])
    ->name('presentations.stream')
    ->middleware('throttle:1000,1'); // High rate limit, streaming needs many requests

// Legacy video streaming (for backward compatibility)
Route::get('/presentations/{presentation}/video-stream', [VideoStreamController::class, 'stream'])
    ->name('presentations.video-stream')
    ->middleware('throttle:1000,1');

// Download route - standard download with minimal middleware
Route::get('/presentations/{presentation}/download', [PresentationController::class, 'download'])
    ->name('presentations.download')
    ->middleware('throttle:60,1'); // Normal rate limit for downloads
