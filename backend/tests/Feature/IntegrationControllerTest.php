<?php

use App\Models\IntegrationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Ensure Telegram is configured as enabled by default for tests that need it
    IntegrationSetting::setValue('telegram_enabled', '1');
    IntegrationSetting::setValue('telegram_bot_token', 'mock_token_123');
    IntegrationSetting::setValue('telegram_chat_id', 'mock_chat_id');
});

test('guests cannot access integration settings', function () {
    $response = $this->get('/admin-cms/integrations');

    $response->assertStatus(302)->assertRedirect(route('login'));
});

test('admin can view integration settings page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin-cms/integrations');

    $response->assertStatus(200)->assertInertia(fn ($page) => $page->component('integrations/Edit'));
});

test('integration settings page returns correct telegram data structure', function () {
    $user = User::factory()->create();
    IntegrationSetting::setValue('telegram_chat_id', '123456789');
    IntegrationSetting::setValue('telegram_template_contact', 'Contact: {name}');
    IntegrationSetting::setValue('telegram_template_blog_publish', 'Blog: {title}');

    $response = $this->actingAs($user)->get('/admin-cms/integrations');

    $response->assertInertia(fn ($page) => $page
        ->has('telegram')
        ->where('telegram.enabled', true)
        ->where('telegram.bot_token_set', true)
        ->where('telegram.bot_token', '••••••••') // masked
        ->where('telegram.chat_id', '123456789')
        ->where('telegram.template_contact', 'Contact: {name}')
        ->where('telegram.template_blog_publish', 'Blog: {title}')
    );
});

test('admin can update telegram settings', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put('/admin-cms/integrations/telegram', [
        'enabled' => true,
        'bot_token' => 'new_bot_token_xyz',
        'chat_id' => '987654321',
        'template_contact' => 'New contact template {name}',
        'template_blog_publish' => 'New blog template {title}',
    ]);

    $response->assertRedirect();

    expect(IntegrationSetting::getValue('telegram_enabled'))->toBe('1');
    expect(IntegrationSetting::getValue('telegram_chat_id'))->toBe('987654321');
    expect(IntegrationSetting::getValue('telegram_bot_token'))->toBe('new_bot_token_xyz');
    expect(IntegrationSetting::getValue('telegram_template_contact'))->toBe('New contact template {name}');
    expect(IntegrationSetting::getValue('telegram_template_blog_publish'))->toBe('New blog template {title}');
});

test('admin can disable telegram integration', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->put('/admin-cms/integrations/telegram', [
        'enabled' => false,
        'chat_id' => '123',
    ]);

    expect(IntegrationSetting::getValue('telegram_enabled'))->toBe('0');
});

test('masked bot token placeholder is not stored as new token', function () {
    $user = User::factory()->create();
    IntegrationSetting::setValue('telegram_bot_token', 'original_token_abc');

    $this->actingAs($user)->put('/admin-cms/integrations/telegram', [
        'enabled' => true,
        'bot_token' => '••••••••', // send masked placeholder
        'chat_id' => '123',
    ]);

    // Original token should remain unchanged
    expect(IntegrationSetting::getValue('telegram_bot_token'))->toBe('original_token_abc');
});

test('test telegram endpoint returns error when not configured', function () {
    $user = User::factory()->create();
    IntegrationSetting::setValue('telegram_enabled', '0');

    $response = $this->actingAs($user)->postJson('/admin-cms/integrations/telegram/test');

    $response->assertStatus(422)->assertJson(['success' => false]);
});

test('test telegram endpoint sends message when configured', function () {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/admin-cms/integrations/telegram/test');

    $response->assertStatus(200)->assertJson(['success' => true]);
});
