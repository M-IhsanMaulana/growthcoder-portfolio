<?php

use App\Http\Controllers\Api\V1\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\V1\ContactMessageController;
use App\Http\Controllers\Api\V1\DevelopmentPhilosophyController;
use App\Http\Controllers\Api\V1\EducationController as ApiEducationController;
use App\Http\Controllers\Api\V1\ExperienceController as ApiExperienceController;
use App\Http\Controllers\Api\V1\PostController as ApiPostController;
use App\Http\Controllers\Api\V1\ProjectCategoryController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ServiceController as ApiServiceController;
use App\Http\Controllers\Api\V1\SiteSettingController as ApiSiteSettingController;
use App\Http\Controllers\Api\V1\SkillController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\TechnologyController;
use App\Http\Controllers\Api\V1\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::post('contact', [ContactMessageController::class, 'store'])->middleware('throttle:5,60');

    Route::get('project-categories', [ProjectCategoryController::class, 'index']);
    Route::get('project-categories/{slug}', [ProjectCategoryController::class, 'show']);

    Route::get('technologies', [TechnologyController::class, 'index']);
    Route::get('technologies/{slug}', [TechnologyController::class, 'show']);

    Route::get('skills', [SkillController::class, 'index']);
    Route::get('stats', [StatsController::class, 'index']);

    Route::get('projects', [ProjectController::class, 'index']);
    Route::get('projects/{slug}', [ProjectController::class, 'show']);

    Route::get('blog-categories', [ApiCategoryController::class, 'index']);
    Route::get('blog-categories/{slug}', [ApiCategoryController::class, 'show']);

    Route::get('posts', [ApiPostController::class, 'index']);
    Route::get('posts/{slug}', [ApiPostController::class, 'show']);
    Route::post('posts/{slug}/view', [ApiPostController::class, 'incrementView']);

    Route::get('services', [ApiServiceController::class, 'index']);
    Route::get('workflows', [WorkflowController::class, 'index']);
    Route::get('development-philosophies', [DevelopmentPhilosophyController::class, 'index']);

    Route::get('experiences', [ApiExperienceController::class, 'index']);
    Route::get('educations', [ApiEducationController::class, 'index']);
    Route::get('settings', [ApiSiteSettingController::class, 'index']);
});
