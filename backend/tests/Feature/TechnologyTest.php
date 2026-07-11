<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

test('guests cannot access technologies index', function () {
    $response = $this->get('/admin-cms/technologies');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot store technologies', function () {
    $response = $this->post('/admin-cms/technologies', [
        'name' => 'Laravel',
        'category' => 'backend',
        'is_featured' => false,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view technologies', function () {
    $user = User::factory()->create();
    Technology::create([
        'name' => 'Vue.js',
        'slug' => 'vue-js',
        'category' => 'frontend',
        'is_featured' => true,
    ]);

    $response = $this->actingAs($user)
        ->get('/admin-cms/technologies');

    $response->assertStatus(200);
});

test('administrator can create a technology with auto-generated slug', function () {
    $user = User::factory()->create();
    $media = Media::create([
        'original_filename' => 'laravel.png',
        'filename' => 'laravel',
        'storage_path' => 'media/laravel.png',
        'mime_type' => 'image/png',
        'file_size' => 1024,
        'width' => 100,
        'height' => 100,
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/technologies', [
            'name' => 'Laravel Framework',
            'category' => 'backend',
            'logo_media_id' => $media->id,
            'description' => 'A PHP framework for web artisans.',
            'url' => 'https://laravel.com',
            'is_featured' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('technologies', [
        'name' => 'Laravel Framework',
        'slug' => 'laravel-framework',
        'category' => 'backend',
        'logo_media_id' => $media->id,
        'description' => 'A PHP framework for web artisans.',
        'url' => 'https://laravel.com',
        'is_featured' => true,
    ]);
});

test('administrator cannot create a technology with duplicate name', function () {
    $user = User::factory()->create();
    Technology::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'category' => 'backend',
        'is_featured' => true,
    ]);

    $response = $this->actingAs($user)
        ->post('/admin-cms/technologies', [
            'name' => 'Laravel',
            'category' => 'backend',
            'is_featured' => false,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['name']);
});

test('administrator can update a technology', function () {
    $user = User::factory()->create();
    $tech = Technology::create([
        'name' => 'Docker',
        'slug' => 'docker',
        'category' => 'devops',
        'is_featured' => false,
    ]);

    $response = $this->actingAs($user)
        ->put("/admin-cms/technologies/{$tech->id}", [
            'name' => 'Docker CE',
            'slug' => 'docker-ce',
            'category' => 'devops',
            'logo_media_id' => null,
            'description' => 'Containerization platform',
            'url' => 'https://docker.com',
            'is_featured' => true,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('technologies', [
        'id' => $tech->id,
        'name' => 'Docker CE',
        'slug' => 'docker-ce',
        'category' => 'devops',
        'description' => 'Containerization platform',
        'url' => 'https://docker.com',
        'is_featured' => true,
    ]);
});

test('administrator can delete a technology that is not in use', function () {
    $user = User::factory()->create();
    $tech = Technology::create([
        'name' => 'Temporary Tech',
        'slug' => 'temporary-tech',
        'category' => 'tools',
        'is_featured' => false,
    ]);

    $response = $this->actingAs($user)
        ->delete("/admin-cms/technologies/{$tech->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('technologies', [
        'id' => $tech->id,
    ]);
});

test('administrator can delete a technology that is in use and it cascades', function () {
    $user = User::factory()->create();
    $tech = Technology::create([
        'name' => 'Cascade Tech',
        'slug' => 'cascade-tech',
        'category' => 'backend',
        'is_featured' => false,
    ]);

    $category = ProjectCategory::create([
        'name' => 'Web',
        'slug' => 'web',
        'order' => 1,
    ]);

    $project = Project::create([
        'title' => 'Test Project',
        'slug' => 'test-project',
        'short_description' => 'A short description.',
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    // Attach technology to project (real pivot table)
    $project->technologies()->attach($tech->id);

    // Create temporary skills table (only if not migrated yet)
    $hasSkillItemsTable = Schema::hasTable('skill_items');

    if (! $hasSkillItemsTable) {
        $hasSkillsTable = Schema::hasTable('skills');
        if (! $hasSkillsTable) {
            Schema::create('skills', function ($table) {
                $table->id();
                $table->unsignedBigInteger('technology_id');
                $table->timestamps();
            });
        }

        DB::table('skills')->insert([
            'technology_id' => $tech->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    } else {
        $groupId = DB::table('skills')->insertGetId([
            'name' => 'Backend',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('skill_items')->insert([
            'skill_id' => $groupId,
            'technology_id' => $tech->id,
            'level' => 'beginner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $response = $this->actingAs($user)
        ->delete("/admin-cms/technologies/{$tech->id}");

    $response->assertRedirect();

    // Check technology is missing
    $this->assertDatabaseMissing('technologies', [
        'id' => $tech->id,
    ]);

    // Check relationships were cascaded
    $this->assertDatabaseMissing('project_technology', [
        'technology_id' => $tech->id,
    ]);

    if (! $hasSkillItemsTable) {
        $this->assertDatabaseMissing('skills', [
            'technology_id' => $tech->id,
        ]);

        if (! $hasSkillsTable) {
            Schema::dropIfExists('skills');
        }
    } else {
        $this->assertDatabaseMissing('skill_items', [
            'technology_id' => $tech->id,
        ]);
    }
});

test('public api can list, filter, and show technologies', function () {
    $tech1 = Technology::create([
        'name' => 'React',
        'slug' => 'react',
        'category' => 'frontend',
        'is_featured' => true,
    ]);

    $tech2 = Technology::create([
        'name' => 'Express',
        'slug' => 'express',
        'category' => 'backend',
        'is_featured' => false,
    ]);

    // Test API Index
    $response = $this->getJson('/api/v1/technologies');
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment([
            'name' => 'React',
            'slug' => 'react',
        ])
        ->assertJsonFragment([
            'name' => 'Express',
            'slug' => 'express',
        ]);

    // Test Featured Filter
    $responseFeatured = $this->getJson('/api/v1/technologies?featured=1');
    $responseFeatured->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'slug' => 'react',
        ])
        ->assertJsonMissing([
            'slug' => 'express',
        ]);

    // Test Category Filter
    $responseCategory = $this->getJson('/api/v1/technologies?category=backend');
    $responseCategory->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'slug' => 'express',
        ])
        ->assertJsonMissing([
            'slug' => 'react',
        ]);

    // Test API Show
    $responseShow = $this->getJson('/api/v1/technologies/react');
    $responseShow->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'React',
            'slug' => 'react',
        ]);
});
