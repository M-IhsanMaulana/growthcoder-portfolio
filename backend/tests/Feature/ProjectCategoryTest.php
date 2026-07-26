<?php

use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('guests cannot access categories index', function () {
    $response = $this->get(route('project-categories.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot store categories', function () {
    $response = $this->post(route('project-categories.store'), [
        'name' => 'Web App',
        'order' => 1,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view project categories', function () {
    $user = User::factory()->create();
    ProjectCategory::create([
        'name' => 'Web Application',
        'slug' => 'web-application',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)
        ->get(route('project-categories.index'));

    $response->assertStatus(200);
});

test('administrator can create a project category with auto-generated slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('project-categories.store'), [
            'name' => 'Telegram Bot',
            'description' => 'Automation and telegram integration services',
            'icon' => 'Bot',
            'order' => 2,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('project_categories', [
        'name' => 'Telegram Bot',
        'slug' => 'telegram-bot',
        'description' => 'Automation and telegram integration services',
        'icon' => 'Bot',
        'order' => 2,
    ]);
});

test('administrator cannot create a category with duplicate name', function () {
    $user = User::factory()->create();
    ProjectCategory::create([
        'name' => 'Web Application',
        'slug' => 'web-application',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)
        ->post(route('project-categories.store'), [
            'name' => 'Web Application',
            'order' => 2,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors(['name']);
});

test('administrator can update a project category', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Original Name',
        'slug' => 'original-name',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)
        ->put(route('project-categories.update', $category), [
            'name' => 'Updated Name',
            'slug' => 'updated-name-slug',
            'description' => 'Updated description',
            'icon' => 'Code',
            'order' => 5,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('project_categories', [
        'id' => $category->id,
        'name' => 'Updated Name',
        'slug' => 'updated-name-slug',
        'description' => 'Updated description',
        'icon' => 'Code',
        'order' => 5,
    ]);
});

test('administrator can delete a category that is not in use', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'Temp Category',
        'slug' => 'temp-category',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)
        ->delete(route('project-categories.destroy', $category));

    $response->assertRedirect();
    $this->assertDatabaseMissing('project_categories', [
        'id' => $category->id,
    ]);
});

test('administrator cannot delete a category that is in use by projects', function () {
    $user = User::factory()->create();
    $category = ProjectCategory::create([
        'name' => 'In Use Category',
        'slug' => 'in-use-category',
        'order' => 1,
    ]);

    DB::table('projects')->insert([
        'title' => 'In Use Project',
        'slug' => 'in-use-project',
        'short_description' => 'A test project that is using the category.',
        'category_id' => $category->id,
        'status' => 'draft',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->delete(route('project-categories.destroy', $category));

    $response->assertRedirect();

    // Category should still be in database
    $this->assertDatabaseHas('project_categories', [
        'id' => $category->id,
    ]);
});

test('administrator can swap/move display order of categories', function () {
    $user = User::factory()->create();
    $cat1 = ProjectCategory::create([
        'name' => 'Category 1',
        'slug' => 'cat-1',
        'order' => 1,
    ]);
    $cat2 = ProjectCategory::create([
        'name' => 'Category 2',
        'slug' => 'cat-2',
        'order' => 2,
    ]);

    // Move cat2 up (should swap orders with cat1)
    $response = $this->actingAs($user)
        ->post(route('project-categories.move', $cat2), [
            'direction' => 'up',
        ]);

    $response->assertRedirect();

    $cat1->refresh();
    $cat2->refresh();

    // Check if swapped
    $this->assertEquals(2, $cat1->order);
    $this->assertEquals(1, $cat2->order);
});

test('public api can list project categories and get details by slug', function () {
    ProjectCategory::create([
        'name' => 'API Category',
        'slug' => 'api-category',
        'order' => 0,
    ]);

    // Test API Index
    $response = $this->getJson('/api/v1/project-categories');
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment([
            'name' => 'API Category',
            'slug' => 'api-category',
        ]);

    // Test API Show
    $responseShow = $this->getJson('/api/v1/project-categories/api-category');
    $responseShow->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'API Category',
            'slug' => 'api-category',
        ]);
});
