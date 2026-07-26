<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use App\Models\User;

test('guests cannot access projects cms routes', function () {
    $this->get('/admin-cms/projects')->assertRedirect(route('login'));
    $this->get('/admin-cms/projects/create')->assertRedirect(route('login'));
    $this->post('/admin-cms/projects', [])->assertRedirect(route('login'));
    $this->get('/admin-cms/projects/1')->assertRedirect(route('login'));
    $this->get('/admin-cms/projects/1/edit')->assertRedirect(route('login'));
    $this->put('/admin-cms/projects/1', [])->assertRedirect(route('login'));
    $this->delete('/admin-cms/projects/1')->assertRedirect(route('login'));
    $this->post('/admin-cms/projects/reorder', [])->assertRedirect(route('login'));
});

test('administrator can view projects index', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/admin-cms/projects');
    $response->assertStatus(200);
});

test('administrator can view project preview page', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Web App',
        'slug' => 'web-app',
        'order' => 1,
    ]);
    $project = Project::create([
        'title' => 'Project to Preview',
        'slug' => 'project-to-preview',
        'short_description' => 'A short description.',
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($user)->get("/admin-cms/projects/{$project->id}");
    $response->assertStatus(200);
});

test('administrator can create a project with auto-generated slug', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Mobile App',
        'slug' => 'mobile-app',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->post('/admin-cms/projects', [
        'title' => 'My Mobile App',
        'short_description' => 'A short desc for mobile app.',
        'category_id' => $category->id,
        'status' => 'draft',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'title' => 'My Mobile App',
        'slug' => 'my-mobile-app',
        'short_description' => 'A short desc for mobile app.',
        'category_id' => $category->id,
        'status' => 'draft',
        'is_featured' => false,
    ]);
});

test('administrator can create a project with technologies and visual gallery in one step', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create(['name' => 'Web', 'slug' => 'web', 'order' => 1]);
    $tech = Technology::create(['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'backend']);
    $media = Media::create([
        'original_filename' => 'slide.png', 'filename' => 'slide', 'storage_path' => 'media/slide.png',
        'mime_type' => 'image/png', 'file_size' => 1024, 'width' => 10, 'height' => 10,
    ]);

    $response = $this->actingAs($user)->post('/admin-cms/projects', [
        'title' => 'Unified Project',
        'short_description' => 'A short description.',
        'full_description' => '<p>Detailed story study</p>',
        'category_id' => $category->id,
        'cover_image_id' => $media->id,
        'status' => 'published',
        'is_featured' => true,
        'order' => 5,
        'technology_ids' => [$tech->id],
        'gallery' => [
            [
                'media_id' => $media->id,
                'order' => 0,
                'caption' => 'First visual screenshot',
            ],
        ],
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'title' => 'Unified Project',
        'slug' => 'unified-project',
        'cover_image_id' => $media->id,
        'status' => 'published',
        'is_featured' => true,
        'order' => 5,
    ]);

    $this->assertDatabaseHas('project_technology', [
        'technology_id' => $tech->id,
    ]);

    $this->assertDatabaseHas('project_images', [
        'media_id' => $media->id,
        'order' => 0,
        'caption' => 'First visual screenshot',
    ]);
});

test('slug auto-generates sequential suffixes on duplicate title', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Web',
        'slug' => 'web',
        'order' => 1,
    ]);

    Project::create([
        'title' => 'Dup Proj',
        'slug' => 'dup-proj',
        'short_description' => 'Old desc',
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $response = $this->actingAs($user)->post('/admin-cms/projects', [
        'title' => 'Dup Proj',
        'short_description' => 'New desc',
        'category_id' => $category->id,
        'status' => 'draft',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('projects', [
        'title' => 'Dup Proj',
        'slug' => 'dup-proj-1',
        'short_description' => 'New desc',
    ]);
});

test('administrator cannot create a project with invalid URL format', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Web',
        'slug' => 'web',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->post('/admin-cms/projects', [
        'title' => 'Invalid URL Project',
        'short_description' => 'A short desc.',
        'category_id' => $category->id,
        'status' => 'draft',
        'is_featured' => false,
        'order' => 0,
        'live_url' => 'not-a-valid-url',
    ]);

    $response->assertSessionHasErrors(['live_url']);
});

test('administrator can update a project and sync relationships', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Web',
        'slug' => 'web',
        'order' => 1,
    ]);

    $project = Project::create([
        'title' => 'Initial Project',
        'slug' => 'initial-project',
        'short_description' => 'Initial desc',
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $tech = Technology::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'category' => 'backend',
    ]);

    $media = Media::create([
        'original_filename' => 'screenshot.png',
        'filename' => 'screenshot',
        'storage_path' => 'media/screenshot.png',
        'mime_type' => 'image/png',
        'file_size' => 1024,
        'width' => 100,
        'height' => 100,
    ]);

    $response = $this->actingAs($user)->put("/admin-cms/projects/{$project->id}", [
        'title' => 'Updated Project Title',
        'slug' => 'updated-project-slug',
        'short_description' => 'Updated description text',
        'full_description' => '<h1>Detailed case study</h1>',
        'category_id' => $category->id,
        'cover_image_id' => $media->id,
        'status' => 'published',
        'is_featured' => true,
        'order' => 2,
        'live_url' => 'https://example.com/live',
        'github_url' => 'https://github.com/example/repo',
        'technology_ids' => [$tech->id],
        'gallery' => [
            [
                'media_id' => $media->id,
                'order' => 0,
                'caption' => 'Initial Screenshot',
            ],
        ],
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'title' => 'Updated Project Title',
        'slug' => 'updated-project-slug',
        'cover_image_id' => $media->id,
        'status' => 'published',
        'is_featured' => true,
        'order' => 2,
        'live_url' => 'https://example.com/live',
        'github_url' => 'https://github.com/example/repo',
    ]);

    $this->assertDatabaseHas('project_technology', [
        'project_id' => $project->id,
        'technology_id' => $tech->id,
    ]);

    $this->assertDatabaseHas('project_images', [
        'project_id' => $project->id,
        'media_id' => $media->id,
        'order' => 0,
        'caption' => 'Initial Screenshot',
    ]);
});

test('administrator can delete a project and it cascades relationships', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create(['name' => 'Web', 'slug' => 'web', 'order' => 1]);
    $project = Project::create([
        'title' => 'To Be Deleted',
        'slug' => 'to-be-deleted',
        'short_description' => 'Desc',
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $tech = Technology::create(['name' => 'Vue', 'slug' => 'vue', 'category' => 'frontend']);
    $media = Media::create([
        'original_filename' => 'image.jpg', 'filename' => 'image', 'storage_path' => 'media/image.jpg',
        'mime_type' => 'image/jpeg', 'file_size' => 1024, 'width' => 10, 'height' => 10,
    ]);

    $project->technologies()->attach($tech->id);
    $project->galleryImages()->attach($media->id, ['order' => 0, 'caption' => 'Test']);

    $response = $this->actingAs($user)->delete("/admin-cms/projects/{$project->id}");
    $response->assertRedirect();

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
    $this->assertDatabaseMissing('project_technology', ['project_id' => $project->id]);
    $this->assertDatabaseMissing('project_images', ['project_id' => $project->id]);

    // Original media should NOT be deleted
    $this->assertDatabaseHas('media', ['id' => $media->id]);
});

test('administrator can reorder projects', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create(['name' => 'Web', 'slug' => 'web', 'order' => 1]);

    $p1 = Project::create(['title' => 'P1', 'slug' => 'p1', 'short_description' => 'D', 'category_id' => $category->id, 'order' => 1]);
    $p2 = Project::create(['title' => 'P2', 'slug' => 'p2', 'short_description' => 'D', 'category_id' => $category->id, 'order' => 2]);
    $p3 = Project::create(['title' => 'P3', 'slug' => 'p3', 'short_description' => 'D', 'category_id' => $category->id, 'order' => 3]);

    $response = $this->actingAs($user)->post('/admin-cms/projects/reorder', [
        'ids' => [$p3->id, $p1->id, $p2->id],
    ]);

    $response->assertRedirect();

    $this->assertEquals(0, $p3->refresh()->order);
    $this->assertEquals(1, $p1->refresh()->order);
    $this->assertEquals(2, $p2->refresh()->order);
});

test('public api lists only published projects and supports filters', function () {
    $category = ProjectCategory::create(['name' => 'Web', 'slug' => 'web', 'order' => 1]);
    $category2 = ProjectCategory::create(['name' => 'Mobile', 'slug' => 'mobile', 'order' => 2]);

    $p1 = Project::create(['title' => 'Pub Web Featured', 'slug' => 'p1', 'short_description' => 'D', 'category_id' => $category->id, 'status' => 'published', 'is_featured' => true, 'published_at' => now()]);
    $p2 = Project::create(['title' => 'Pub Mobile Standard', 'slug' => 'p2', 'short_description' => 'D', 'category_id' => $category2->id, 'status' => 'published', 'is_featured' => false, 'published_at' => now()]);
    $p3 = Project::create(['title' => 'Draft Web', 'slug' => 'p3', 'short_description' => 'D', 'category_id' => $category->id, 'status' => 'draft']);

    // API Index - all published
    $response = $this->getJson('/api/v1/projects');
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['slug' => 'p1'])
        ->assertJsonFragment(['slug' => 'p2'])
        ->assertJsonMissing(['slug' => 'p3']);

    // API Filter by category slug
    $responseCat = $this->getJson('/api/v1/projects?category=mobile');
    $responseCat->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['slug' => 'p2'])
        ->assertJsonMissing(['slug' => 'p1']);

    // API Filter by featured
    $responseFeatured = $this->getJson('/api/v1/projects?featured=true');
    $responseFeatured->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['slug' => 'p1'])
        ->assertJsonMissing(['slug' => 'p2']);
});

test('public api shows single published project and returns 404 for draft', function () {
    $category = ProjectCategory::create(['name' => 'Web', 'slug' => 'web', 'order' => 1]);
    $p1 = Project::create(['title' => 'Published Proj', 'slug' => 'pub-proj', 'short_description' => 'D', 'category_id' => $category->id, 'status' => 'published', 'published_at' => now()]);
    $p2 = Project::create(['title' => 'Draft Proj', 'slug' => 'draft-proj', 'short_description' => 'D', 'category_id' => $category->id, 'status' => 'draft']);

    // Test Show Published
    $response = $this->getJson('/api/v1/projects/pub-proj');
    $response->assertStatus(200)
        ->assertJsonFragment(['title' => 'Published Proj']);

    // Test Show Draft (should return 404)
    $responseDraft = $this->getJson('/api/v1/projects/draft-proj');
    $responseDraft->assertStatus(404);
});

test('administrator can create a project with a cover image caption', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Mobile App',
        'slug' => 'mobile-app',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->post('/admin-cms/projects', [
        'title' => 'Project with Cover Caption',
        'short_description' => 'A short desc for mobile app.',
        'category_id' => $category->id,
        'cover_image_caption' => 'This is a cover image caption',
        'status' => 'draft',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'title' => 'Project with Cover Caption',
        'cover_image_caption' => 'This is a cover image caption',
    ]);
});

test('cover image caption must be at most 255 characters', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Mobile App',
        'slug' => 'mobile-app',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->post('/admin-cms/projects', [
        'title' => 'Project with Long Cover Caption',
        'short_description' => 'A short desc for mobile app.',
        'category_id' => $category->id,
        'cover_image_caption' => str_repeat('a', 256),
        'status' => 'draft',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response->assertSessionHasErrors(['cover_image_caption']);
});

test('administrator can update a project with cover image caption', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Web',
        'slug' => 'web',
        'order' => 1,
    ]);

    $project = Project::create([
        'title' => 'Initial Project',
        'slug' => 'initial-project',
        'short_description' => 'Initial desc',
        'category_id' => $category->id,
        'status' => 'draft',
        'cover_image_caption' => 'Old Caption',
    ]);

    $response = $this->actingAs($user)->put("/admin-cms/projects/{$project->id}", [
        'title' => 'Updated Project Title',
        'slug' => 'updated-project-slug',
        'short_description' => 'Updated description text',
        'category_id' => $category->id,
        'cover_image_caption' => 'New Caption',
        'status' => 'draft',
        'is_featured' => false,
        'order' => 0,
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'cover_image_caption' => 'New Caption',
    ]);
});
