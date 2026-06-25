<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::prefix('admin-cms')->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::inertia('dashboard', 'Dashboard')->name('dashboard');

        // Media Library routes
        Route::apiResource('media', \App\Http\Controllers\MediaController::class)->except(['show'])->parameters(['media' => 'media']);
        Route::get('media/{media}/usage', [\App\Http\Controllers\MediaController::class, 'usage'])->name('media.usage');

        // Project Categories routes
        Route::apiResource('project-categories', \App\Http\Controllers\ProjectCategoryController::class)->except(['show']);
        Route::post('project-categories/{projectCategory}/move', [\App\Http\Controllers\ProjectCategoryController::class, 'move'])->name('project-categories.move');

        // Technologies routes
        Route::apiResource('technologies', \App\Http\Controllers\TechnologyController::class)->except(['show']);

        // Projects routes
        Route::post('projects/reorder', [\App\Http\Controllers\ProjectController::class, 'reorder'])->name('projects.reorder');
        Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    });

    require __DIR__.'/settings.php';
});

Route::get('/media/{slug_id}/{variant?}', [\App\Http\Controllers\MediaController::class, 'show'])->name('media.show');

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
