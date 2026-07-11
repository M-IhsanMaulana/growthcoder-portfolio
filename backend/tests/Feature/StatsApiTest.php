<?php

use App\Models\Experience;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests can fetch stats', function () {
    // Seed technologies
    Technology::create([
        'name' => 'Vue.js',
        'slug' => 'vuejs',
        'category' => 'frontend',
    ]);
    Technology::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'category' => 'backend',
    ]);

    // Seed projects
    $category = ProjectCategory::create([
        'name' => 'Web App',
        'slug' => 'web-app',
        'order' => 1,
    ]);
    Project::create([
        'title' => 'Project 1',
        'slug' => 'project-1',
        'short_description' => 'A short description.',
        'category_id' => $category->id,
        'status' => 'published',
    ]);
    Project::create([
        'title' => 'Project 2',
        'slug' => 'project-2',
        'short_description' => 'A short description.',
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Seed experiences
    Experience::create([
        'company' => 'GrowthCoder Agency',
        'title_position' => 'Lead Full-Stack Developer',
        'start_date' => now()->subYears(3)->format('Y-m-d'),
        'end_date' => null,
        'order' => 1,
    ]);

    $response = $this->getJson('/api/v1/stats');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'technologies_count' => 2,
                'projects_count' => 2,
                'years_of_experience' => 3,
            ],
        ]);
});
