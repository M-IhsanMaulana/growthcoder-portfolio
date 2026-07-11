<?php

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// ============================================================
// Access Control
// ============================================================

test('guests cannot access global settings', function () {
    $response = $this->get('/admin-cms/global-settings');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot update about settings', function () {
    $response = $this->put('/admin-cms/global-settings', [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Test Headline',
        'site_name' => 'test.id',
        'about_location' => 'Indonesia',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

// ============================================================
// Update About Fields
// ============================================================

test('authenticated user can update about bio and location', function () {
    $user = User::factory()->create();
    SiteSetting::firstOrCreate(['id' => 1], [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Test Headline',
        'site_name' => 'growthcoder.id',
    ]);

    $response = $this->actingAs($user)->put('/admin-cms/global-settings', [
        'owner_full_name' => 'Muhammad Ihsan Maulana',
        'hero_headline' => 'Crafting solutions',
        'site_name' => 'growthcoder.id',
        'about_bio' => '<p>Saya adalah mahasiswa Informatika.</p>',
        'about_location' => 'Indonesia, Bandung',
        'about_specialities' => ['Web Dev', 'Backend Dev'],
        'about_stats' => [
            ['value' => '15+', 'label' => 'Projects Completed', 'emoji' => '📁'],
        ],
    ]);

    $response->assertRedirect();

    $settings = SiteSetting::find(1);
    expect($settings->about_bio)->toBe('<p>Saya adalah mahasiswa Informatika.</p>');
    expect($settings->about_location)->toBe('Indonesia, Bandung');
    expect($settings->about_specialities)->toBe(['Web Dev', 'Backend Dev']);
    expect($settings->about_stats[0]['value'])->toBe('15+');
    expect($settings->about_stats[0]['label'])->toBe('Projects Completed');
});

test('about_bio can be null and still saves successfully', function () {
    $user = User::factory()->create();
    SiteSetting::firstOrCreate(['id' => 1], [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
    ]);

    $response = $this->actingAs($user)->put('/admin-cms/global-settings', [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
        'about_bio' => null,
    ]);

    $response->assertRedirect();

    $settings = SiteSetting::find(1);
    expect($settings->about_bio)->toBeNull();
});

test('about_specialities must be an array', function () {
    $user = User::factory()->create();
    SiteSetting::firstOrCreate(['id' => 1], [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
    ]);

    $response = $this->actingAs($user)->put('/admin-cms/global-settings', [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
        'about_specialities' => 'not-an-array',
    ]);

    $response->assertSessionHasErrors('about_specialities');
});

// ============================================================
// Sync About Stats
// ============================================================

test('guests cannot sync about stats', function () {
    $response = $this->post('/admin-cms/global-settings/sync-about-stats');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('sync about stats updates projects and technologies count correctly', function () {
    $user = User::factory()->create();

    // Create a project category for the foreign key
    $categoryId = DB::table('project_categories')->insertGetId([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'order' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create published and draft projects via DB
    for ($i = 1; $i <= 5; $i++) {
        DB::table('projects')->insert([
            'title' => "Published Project {$i}",
            'slug' => "published-project-{$i}",
            'short_description' => 'Test',
            'category_id' => $categoryId,
            'status' => 'published',
            'is_featured' => false,
            'order' => $i,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    for ($i = 1; $i <= 2; $i++) {
        DB::table('projects')->insert([
            'title' => "Draft Project {$i}",
            'slug' => "draft-project-{$i}",
            'short_description' => 'Test',
            'category_id' => $categoryId,
            'status' => 'draft',
            'is_featured' => false,
            'order' => 10 + $i,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // Create technologies directly via DB
    for ($i = 1; $i <= 8; $i++) {
        DB::table('technologies')->insert([
            'name' => "Tech {$i}",
            'slug' => "tech-{$i}",
            'category' => 'Backend',
            'is_featured' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    SiteSetting::updateOrCreate(['id' => 1], [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
        'about_stats' => [
            ['value' => '0+', 'label' => 'Projects Completed', 'emoji' => '📁'],
            ['value' => '3+', 'label' => 'Years Learning', 'emoji' => '🎓'],
            ['value' => '0+', 'label' => 'Technologies Used', 'emoji' => ''],
        ],
    ]);

    Cache::forget('site_settings');

    $response = $this->actingAs($user)->post('/admin-cms/global-settings/sync-about-stats');

    $response->assertRedirect();

    $settings = SiteSetting::find(1);
    $stats = collect($settings->about_stats)->keyBy('label');

    // Projects should be updated to published count (5)
    expect($stats['Projects Completed']['value'])->toBe('5+');

    // Technologies should be updated to total count (8)
    expect($stats['Technologies Used']['value'])->toBe('8+');

    // Years Learning should remain unchanged (manual stat)
    expect($stats['Years Learning']['value'])->toBe('3+');
});

// ============================================================
// API Resource
// ============================================================

test('public api settings endpoint includes about fields', function () {
    $apiKey = 'gc_test_api_key_12345';

    SiteSetting::updateOrCreate(['id' => 1], [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
        'api_key' => $apiKey,
        'about_bio' => '<p>My bio</p>',
        'about_location' => 'Indonesia',
        'about_specialities' => ['Web Dev', 'Backend Dev'],
        'about_stats' => [
            ['value' => '10+', 'label' => 'Projects Completed', 'emoji' => '📁'],
        ],
    ]);

    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    $response = $this->withHeader('X-API-Key', $apiKey)->getJson('/api/v1/settings');

    $response->assertStatus(200);
    $response->assertJsonPath('data.about_bio', '<p>My bio</p>');
    $response->assertJsonPath('data.about_location', 'Indonesia');
    $response->assertJsonPath('data.about_specialities.0', 'Web Dev');
    $response->assertJsonPath('data.about_stats.0.value', '10+');
    $response->assertJsonPath('data.about_stats.0.label', 'Projects Completed');
});

test('public api settings returns empty arrays for null about fields', function () {
    $apiKey = 'gc_test_api_key_empty';

    SiteSetting::updateOrCreate(['id' => 1], [
        'owner_full_name' => 'Test User',
        'hero_headline' => 'Headline',
        'site_name' => 'growthcoder.id',
        'api_key' => $apiKey,
        'about_specialities' => null,
        'about_stats' => null,
    ]);

    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    $response = $this->withHeader('X-API-Key', $apiKey)->getJson('/api/v1/settings');

    $response->assertStatus(200);
    $response->assertJsonPath('data.about_specialities', []);
    $response->assertJsonPath('data.about_stats', []);
});
