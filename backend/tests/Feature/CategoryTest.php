<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

test('guests cannot access categories index', function () {
    $response = $this->get(route('categories.index'));

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view categories', function () {
    $user = User::factory()->create();
    Category::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $response = $this->actingAs($user)
        ->get(route('categories.index'));

    $response->assertStatus(200);
});

test('administrator can create a category with auto-generated slug', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post(route('categories.store'), [
            'name' => 'Docker DevOps',
            'description' => 'Docker related tutorials',
            'meta_title' => 'Docker Meta Title',
            'meta_description' => 'Docker Meta Description',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('categories', [
        'name' => 'Docker DevOps',
        'slug' => 'docker-devops',
        'description' => 'Docker related tutorials',
        'meta_title' => 'Docker Meta Title',
        'meta_description' => 'Docker Meta Description',
    ]);
});

test('administrator cannot delete a category that is in use by posts', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Docker',
        'slug' => 'docker',
    ]);

    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content' => 'Lorem ipsum content for test post',
        'status' => 'draft',
    ]);

    $post->categories()->attach($category);

    $response = $this->actingAs($user)
        ->delete(route('categories.destroy', $category));

    $response->assertRedirect();
    $response->assertSessionHasErrors(['error']);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
    ]);
});

test('public api can list categories and show category details', function () {
    Category::create([
        'name' => 'API Category',
        'slug' => 'api-category',
    ]);

    $response = $this->getJson('/api/v1/blog-categories');
    $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'API Category',
            'slug' => 'api-category',
        ]);

    $responseShow = $this->getJson('/api/v1/blog-categories/api-category');
    $responseShow->assertStatus(200)
        ->assertJsonFragment([
            'name' => 'API Category',
            'slug' => 'api-category',
        ]);
});
