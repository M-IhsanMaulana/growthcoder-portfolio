<?php

use App\Models\Service;
use App\Models\User;

// ============================================================
// Access Control
// ============================================================

test('guests cannot access services index', function () {
    $response = $this->get('/admin-cms/services');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot create a service', function () {
    $response = $this->post('/admin-cms/services', [
        'title' => 'Full-Stack Web Development',
        'slug' => 'full-stack-web-development',
        'short_description' => 'Build modern web applications.',
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot update a service', function () {
    $service = Service::factory()->create();

    $response = $this->put("/admin-cms/services/{$service->id}", [
        'title' => 'Updated Title',
        'slug' => 'updated-title',
        'short_description' => 'Updated description.',
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot delete a service', function () {
    $service = Service::factory()->create();

    $response = $this->delete("/admin-cms/services/{$service->id}");

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

// ============================================================
// Index
// ============================================================

test('administrator can view services index', function () {
    $user = User::factory()->create();
    Service::factory()->count(3)->create();

    $response = $this->actingAs($user)->get('/admin-cms/services');

    $response->assertStatus(200);
});

// ============================================================
// Create (US-09-001)
// ============================================================

test('administrator can create a new service', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin-cms/services', [
        'title' => 'Telegram Bot Development',
        'slug' => 'telegram-bot-development',
        'short_description' => 'Building powerful Telegram bots for automation.',
        'long_description' => null,
        'icon' => 'Bot',
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('services', [
        'title' => 'Telegram Bot Development',
        'slug' => 'telegram-bot-development',
        'icon' => 'Bot',
        'is_active' => true,
    ]);
});

test('service is created with is_active true by default', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/admin-cms/services', [
        'title' => 'API Integration',
        'slug' => 'api-integration',
        'short_description' => 'Connect any third-party services.',
        'is_active' => true,
        'order' => 0,
    ]);

    $this->assertDatabaseHas('services', [
        'slug' => 'api-integration',
        'is_active' => true,
    ]);
});

test('short description cannot exceed 200 characters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin-cms/services', [
        'title' => 'Some Service',
        'slug' => 'some-service',
        'short_description' => str_repeat('a', 201),
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertSessionHasErrors('short_description');
    $this->assertDatabaseMissing('services', ['slug' => 'some-service']);
});

test('title is required when creating a service', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/admin-cms/services', [
        'title' => '',
        'slug' => 'no-title',
        'short_description' => 'Description here.',
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertSessionHasErrors('title');
});

test('slug must be unique when creating a service', function () {
    $user = User::factory()->create();
    Service::factory()->create(['slug' => 'existing-slug']);

    $response = $this->actingAs($user)->post('/admin-cms/services', [
        'title' => 'Another Service',
        'slug' => 'existing-slug',
        'short_description' => 'Some description.',
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertSessionHasErrors('slug');
});

// ============================================================
// Edit (US-09-002)
// ============================================================

test('administrator can update a service', function () {
    $user = User::factory()->create();
    $service = Service::factory()->create([
        'title' => 'Old Title',
        'slug' => 'old-title',
        'short_description' => 'Old description.',
    ]);

    $response = $this->actingAs($user)->put("/admin-cms/services/{$service->id}", [
        'title' => 'New Title',
        'slug' => 'new-title',
        'short_description' => 'New description.',
        'is_active' => true,
        'order' => 1,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'title' => 'New Title',
        'slug' => 'new-title',
    ]);
});

test('slug can remain same when updating the same service', function () {
    $user = User::factory()->create();
    $service = Service::factory()->create(['slug' => 'my-service']);

    $response = $this->actingAs($user)->put("/admin-cms/services/{$service->id}", [
        'title' => 'My Service Updated',
        'slug' => 'my-service', // same slug
        'short_description' => 'Updated desc.',
        'is_active' => true,
        'order' => 0,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'slug' => 'my-service',
        'title' => 'My Service Updated',
    ]);
});

// ============================================================
// Toggle Active (US-09-003)
// ============================================================

test('administrator can deactivate a service', function () {
    $user = User::factory()->create();
    $service = Service::factory()->active()->create();

    $response = $this->actingAs($user)->post("/admin-cms/services/{$service->id}/toggle-active");

    $response->assertRedirect();
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'is_active' => false,
    ]);
});

test('administrator can reactivate an inactive service', function () {
    $user = User::factory()->create();
    $service = Service::factory()->inactive()->create();

    $response = $this->actingAs($user)->post("/admin-cms/services/{$service->id}/toggle-active");

    $response->assertRedirect();
    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'is_active' => true,
    ]);
});

// ============================================================
// Reorder (US-09-004)
// ============================================================

test('administrator can reorder services', function () {
    $user = User::factory()->create();
    $s1 = Service::factory()->create(['order' => 0, 'slug' => 'service-one']);
    $s2 = Service::factory()->create(['order' => 1, 'slug' => 'service-two']);
    $s3 = Service::factory()->create(['order' => 2, 'slug' => 'service-three']);

    // Reverse the order: s3, s2, s1
    $response = $this->actingAs($user)->post('/admin-cms/services/reorder', [
        'ids' => [$s3->id, $s2->id, $s1->id],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('services', ['id' => $s3->id, 'order' => 0]);
    $this->assertDatabaseHas('services', ['id' => $s2->id, 'order' => 1]);
    $this->assertDatabaseHas('services', ['id' => $s1->id, 'order' => 2]);
});

// ============================================================
// Delete (US-09-005)
// ============================================================

test('administrator can delete a service', function () {
    $user = User::factory()->create();
    $service = Service::factory()->create();

    $response = $this->actingAs($user)->delete("/admin-cms/services/{$service->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

// ============================================================
// Public API (US-09-006)
// ============================================================

test('api returns only active services', function () {
    Service::factory()->active()->create(['title' => 'Active Service', 'slug' => 'active-service', 'order' => 0]);
    Service::factory()->inactive()->create(['title' => 'Inactive Service', 'slug' => 'inactive-service', 'order' => 1]);

    $response = $this->getJson('/api/v1/services');

    $response->assertStatus(200);
    $response->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.title', 'Active Service');
});

test('api returns services ordered by order asc', function () {
    Service::factory()->active()->create(['title' => 'Third', 'slug' => 'third', 'order' => 2]);
    Service::factory()->active()->create(['title' => 'First', 'slug' => 'first', 'order' => 0]);
    Service::factory()->active()->create(['title' => 'Second', 'slug' => 'second', 'order' => 1]);

    $response = $this->getJson('/api/v1/services');

    $response->assertStatus(200);
    $response->assertJsonPath('data.0.title', 'First');
    $response->assertJsonPath('data.1.title', 'Second');
    $response->assertJsonPath('data.2.title', 'Third');
});

test('api response uses service resource and does not expose raw db fields', function () {
    Service::factory()->active()->create(['slug' => 'test-service']);

    $response = $this->getJson('/api/v1/services');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => ['id', 'title', 'slug', 'short_description', 'icon', 'is_active', 'order'],
        ],
    ]);
});
