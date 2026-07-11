<?php

use App\Models\Media;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    // Clear cache
    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    // Seed initial setting record (id: 1)
    SiteSetting::firstOrCreate(['id' => 1], [
        'owner_full_name' => 'Muhammad Ihsan Maulana',
        'owner_title' => 'Full-Stack Developer',
        'hero_headline' => 'Crafting High-Performance Web Solutions & Intelligent Automations',
        'hero_subheadline' => 'Specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.',
        'site_name' => 'growthcoder.id',
    ]);
});

// ============================================================
// Access Control
// ============================================================

test('guests cannot access global settings page', function () {
    $response = $this->get('/admin-cms/global-settings');

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot update global settings', function () {
    $response = $this->put('/admin-cms/global-settings', [
        'owner_full_name' => 'John Doe',
        'hero_headline' => 'Welcome to my portfolio',
        'site_name' => 'johndoe.com',
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('guests cannot upload CV', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('cv.pdf', 500, 'application/pdf');

    $response = $this->post('/admin-cms/global-settings/cv', [
        'cv_file' => $file,
    ]);

    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

// ============================================================
// Administration (CMS Edit & Update)
// ============================================================

test('administrator can view global settings page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin-cms/global-settings');

    $response->assertStatus(200);
});

test('administrator can update global settings', function () {
    $user = User::factory()->create();

    $media1 = Media::create([
        'original_filename' => 'profile.jpg',
        'filename' => 'profile',
        'storage_path' => 'media/profile.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'width' => 150,
        'height' => 150,
    ]);

    $media2 = Media::create([
        'original_filename' => 'og.jpg',
        'filename' => 'og',
        'storage_path' => 'media/og.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 2048,
        'width' => 1200,
        'height' => 630,
    ]);

    // Ensure cache has some state
    Cache::put('site_settings', 'cached_settings_data');

    $response = $this->actingAs($user)->put('/admin-cms/global-settings', [
        'owner_full_name' => 'Muhammad Ihsan',
        'owner_title' => 'Laravel & Vue Developer',
        'profile_photo_id' => $media1->id,
        'hero_headline' => 'Next-Gen Portfolio Headline',
        'hero_subheadline' => 'Detailed subheadline text.',
        'hero_cta_text' => 'Get in touch',
        'hero_cta_url' => '/contact-me',
        'social_linkedin' => 'https://linkedin.com/in/ihsan',
        'social_github' => 'https://github.com/ihsan',
        'social_telegram' => 'https://t.me/ihsan',
        'social_instagram' => 'https://instagram.com/ihsan',
        'social_twitter' => 'https://x.com/ihsan',
        'contact_email' => 'contact@ihsan.com',
        'site_name' => 'myportfolio.id',
        'meta_title_suffix' => ' | Personal Site',
        'default_meta_desc' => 'Welcome to my professional space.',
        'default_og_image_id' => $media2->id,
        'google_analytics_id' => 'G-TEST123456',
        'google_site_verification' => 'google-verification-test-code',
        'api_key' => 'secret-test-key-123',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('site_settings', [
        'id' => 1,
        'owner_full_name' => 'Muhammad Ihsan',
        'owner_title' => 'Laravel & Vue Developer',
        'profile_photo_id' => $media1->id,
        'hero_headline' => 'Next-Gen Portfolio Headline',
        'site_name' => 'myportfolio.id',
        'contact_email' => 'contact@ihsan.com',
        'google_analytics_id' => 'G-TEST123456',
        'google_site_verification' => 'google-verification-test-code',
        'api_key' => 'secret-test-key-123',
    ]);

    // Cache should be cleared/forgotten
    $this->assertFalse(Cache::has('site_settings'));
});

test('validation errors for invalid inputs', function () {
    $user = User::factory()->create();

    // Required fields cannot be empty
    $response = $this->actingAs($user)->put('/admin-cms/global-settings', [
        'owner_full_name' => '',
        'hero_headline' => '',
        'site_name' => '',
    ]);

    $response->assertSessionHasErrors(['owner_full_name', 'hero_headline', 'site_name']);

    // Invalid URLs
    $response = $this->actingAs($user)->put('/admin-cms/global-settings', [
        'owner_full_name' => 'John Doe',
        'hero_headline' => 'Headline',
        'site_name' => 'johndoe.com',
        'social_linkedin' => 'not-a-valid-url',
        'contact_email' => 'not-an-email',
    ]);

    $response->assertSessionHasErrors(['social_linkedin', 'contact_email']);
});

test('administrator can upload PDF CV file', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    Cache::put('site_settings', 'cached_settings_data');

    $file = UploadedFile::fake()->create('my-cv.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($user)->post('/admin-cms/global-settings/cv', [
        'cv_file' => $file,
    ]);

    $response->assertRedirect();

    // File stored as cv/cv-latest.pdf on public disk
    Storage::disk('public')->assertExists('cv/cv-latest.pdf');

    $this->assertDatabaseHas('site_settings', [
        'id' => 1,
        'cv_file_path' => 'cv/cv-latest.pdf',
    ]);

    // Cache should be cleared
    $this->assertFalse(Cache::has('site_settings'));
});

// ============================================================
// Public API
// ============================================================

test('anyone can view site settings via public API with valid key and it is cached', function () {
    $media = Media::create([
        'original_filename' => 'profile.jpg',
        'filename' => 'profile',
        'storage_path' => 'media/profile.jpg',
        'mime_type' => 'image/jpeg',
        'file_size' => 1024,
        'width' => 150,
        'height' => 150,
    ]);

    $setting = SiteSetting::findOrFail(1);
    $setting->update([
        'owner_full_name' => 'Public Name',
        'hero_headline' => 'Public Headline',
        'site_name' => 'public-site.com',
        'profile_photo_id' => $media->id,
        'api_key' => 'secret-test-key-123',
    ]);

    // Clear cache first
    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    $response = $this->withHeader('X-API-Key', 'secret-test-key-123')->getJson('/api/v1/settings');

    $response->assertStatus(200);
    $response->assertJsonPath('data.owner_full_name', 'Public Name');
    $response->assertJsonPath('data.hero_headline', 'Public Headline');
    $response->assertJsonPath('data.profile_photo.original_filename', 'profile.jpg');
    // Ensure api_key is hidden and not leaked in resource response
    $response->assertJsonMissing(['api_key' => 'secret-test-key-123']);
    $response->assertJsonMissing(['data.api_key' => 'secret-test-key-123']);

    // It should now be in the cache
    $this->assertTrue(Cache::has('site_settings'));

    // Update settings in database directly (without clearing cache) to verify cache is returned
    $setting->update(['owner_full_name' => 'Name Changed But Cached']);

    $response = $this->withHeader('X-API-Key', 'secret-test-key-123')->getJson('/api/v1/settings');
    $response->assertJsonPath('data.owner_full_name', 'Public Name'); // Still cached name
});

test('public API rejects request without API Key', function () {
    $setting = SiteSetting::findOrFail(1);
    $setting->update(['api_key' => 'secret-test-key-123']);
    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    $response = $this->getJson('/api/v1/settings');

    $response->assertStatus(401);
    $response->assertJsonPath('message', 'Unauthorized: Invalid or missing API Key.');
});

test('public API rejects request with invalid API Key', function () {
    $setting = SiteSetting::findOrFail(1);
    $setting->update(['api_key' => 'secret-test-key-123']);
    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    $response = $this->withHeader('X-API-Key', 'wrong-key')->getJson('/api/v1/settings');

    $response->assertStatus(401);
});

test('public API accepts request with Authorization Bearer token', function () {
    $setting = SiteSetting::findOrFail(1);
    $setting->update(['api_key' => 'secret-test-key-123']);
    Cache::forget('site_settings');
    Cache::forget('site_api_key');

    $response = $this->withToken('secret-test-key-123')->getJson('/api/v1/settings');

    $response->assertStatus(200);
});
