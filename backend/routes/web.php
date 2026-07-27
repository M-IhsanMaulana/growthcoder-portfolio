<?php

use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevelopmentPhilosophyController;
use App\Http\Controllers\EducationController;
use App\Http\Controllers\EducationExperienceController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\GlobalSettingController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectCategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\SkillItemController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

Route::prefix('admin-cms')->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Media Library routes
        Route::apiResource('media', MediaController::class)->except(['show'])->parameters(['media' => 'media']);
        Route::get('media/{media}/usage', [MediaController::class, 'usage'])->name('media.usage');

        // Project Categories routes
        Route::apiResource('project-categories', ProjectCategoryController::class)->except(['show']);
        Route::post('project-categories/{projectCategory}/move', [ProjectCategoryController::class, 'move'])->name('project-categories.move');

        // Technologies routes
        Route::apiResource('technologies', TechnologyController::class)->except(['show']);

        // Skills routes
        Route::post('skills/reorder', [SkillController::class, 'reorder'])->name('skills.reorder');
        Route::apiResource('skills', SkillController::class)->except(['show']);

        // Skill Items routes
        Route::post('skill-items/reorder', [SkillItemController::class, 'reorder'])->name('skill-items.reorder');
        Route::apiResource('skill-items', SkillItemController::class)->except(['index', 'show']);

        // Projects routes
        Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
        Route::resource('projects', ProjectController::class);

        // Blog Categories routes
        Route::apiResource('categories', CategoryController::class)->except(['show']);

        // Blog Posts routes
        Route::get('posts/{post}/preview', [PostController::class, 'preview'])->name('posts.preview');
        Route::resource('posts', PostController::class);

        // Services routes
        Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
        Route::post('services/{service}/toggle-active', [ServiceController::class, 'toggleActive'])->name('services.toggle-active');
        Route::apiResource('services', ServiceController::class)->except(['show']);

        // Workflows routes
        Route::post('workflows/reorder', [WorkflowController::class, 'reorder'])->name('workflows.reorder');
        Route::post('workflows/{workflow}/toggle-active', [WorkflowController::class, 'toggleActive'])->name('workflows.toggle-active');
        Route::apiResource('workflows', WorkflowController::class)->except(['show'])->parameters(['workflows' => 'workflow']);

        // Development Philosophies routes
        Route::post('development-philosophies/reorder', [DevelopmentPhilosophyController::class, 'reorder'])->name('development-philosophies.reorder');
        Route::post('development-philosophies/{developmentPhilosophy}/toggle-active', [DevelopmentPhilosophyController::class, 'toggleActive'])->name('development-philosophies.toggle-active');
        Route::apiResource('development-philosophies', DevelopmentPhilosophyController::class)->except(['show'])->parameters(['development-philosophies' => 'developmentPhilosophy']);

        // Education & Experience routes
        Route::get('education-experience', [EducationExperienceController::class, 'index'])->name('education-experience.index');
        Route::post('experiences/reorder', [ExperienceController::class, 'reorder'])->name('experiences.reorder');
        Route::apiResource('experiences', ExperienceController::class)->except(['index', 'show']);
        Route::post('educations/reorder', [EducationController::class, 'reorder'])->name('educations.reorder');
        Route::apiResource('educations', EducationController::class)->except(['index', 'show']);

        // Inbox routes
        Route::get('inbox', [InboxController::class, 'index'])->name('inbox.index');
        Route::patch('inbox/{contactMessage}/read', [InboxController::class, 'markAsRead'])->name('inbox.mark-as-read');
        Route::patch('inbox/{contactMessage}/replied', [InboxController::class, 'markAsReplied'])->name('inbox.mark-as-replied');
        Route::delete('inbox/{contactMessage}', [InboxController::class, 'destroy'])->name('inbox.destroy');

        // Global Settings routes
        Route::get('global-settings', [GlobalSettingController::class, 'edit'])->name('global-settings.edit');
        Route::put('global-settings', [GlobalSettingController::class, 'update'])->name('global-settings.update');
        Route::post('global-settings/cv', [GlobalSettingController::class, 'uploadCv'])->name('global-settings.upload-cv');
        Route::post('global-settings/sync-about-stats', [GlobalSettingController::class, 'syncAboutStats'])->name('global-settings.sync-about-stats');

        // Integration Settings routes
        Route::get('integrations', [IntegrationController::class, 'edit'])->name('integrations.edit');
        Route::put('integrations/telegram', [IntegrationController::class, 'updateTelegram'])->name('integrations.telegram.update');
        Route::post('integrations/telegram/test', [IntegrationController::class, 'testTelegram'])->name('integrations.telegram.test');

        // API Documentation routes
        Route::get('api-docs', [ApiDocsController::class, 'index'])->name('api-docs.index');
    });

    require __DIR__.'/settings.php';
});

Route::get('/media/{slug_id}/{variant?}', [MediaController::class, 'show'])->name('media.show');

Route::get('.well-known/passkey-endpoints', function () {
    return response()->json([
        'enroll' => route('security.edit'),
        'manage' => route('security.edit'),
    ]);
})->name('well-known.passkeys');
