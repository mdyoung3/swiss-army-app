<?php

use App\Http\Controllers\PiholeController;
use App\Http\Controllers\UrlListController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('PiHoleManagementHub');
});

Route::get('/urllist', function () {
    return Inertia::render('UrlList');
});

Route::get('/converter', function () {
    return Inertia::render('Converter');
});

Route::prefix('api')->group(function () {
    Route::post('/pihole/temporary-disable', [PiholeController::class, 'disablePihole']);
    Route::post('/pihole/add-url', [PiholeController::class, 'submit']);

    // Blocked URLs endpoints
    Route::post('/blocked-urls', [PiholeController::class, 'storeBlockedUrl']);
    Route::get('/urls', [PiholeController::class, 'index']);
    Route::get('/blocked-urls', [UrlListController::class, 'getBlockedUrls']);
    Route::delete(  '/urls/{url}', [UrlListController::class, 'destroy']);
    Route::delete('/blocked-urls/{blockedUrl}', [UrlListController::class, 'destroyBlockedUrl']);

    // Converter endpoints
    Route::post('/converter', [\App\Http\Controllers\ConverterController::class, 'converter']);
});

Route::get('/download/mp3/{filename}', function ($filename) {
    $disk = Storage::disk('mp3_downloads');

    if (!$disk->exists($filename)) {
        abort(404);
    }

    return response()->download(
        $disk->path($filename),
        $filename,
        [
            'Content-Type' => 'audio/mpeg',
        ]
    );
})->name('mp3.download');
