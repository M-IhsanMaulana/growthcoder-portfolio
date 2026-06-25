<?php

use App\Http\Controllers\Api\V1\ProjectCategoryController;
use App\Http\Controllers\Api\V1\TechnologyController;
use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('project-categories', [ProjectCategoryController::class, 'index']);
    Route::get('project-categories/{slug}', [ProjectCategoryController::class, 'show']);

    Route::get('technologies', [TechnologyController::class, 'index']);
    Route::get('technologies/{slug}', [TechnologyController::class, 'show']);

    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{slug}', [ProjectController::class, 'show']);
});
