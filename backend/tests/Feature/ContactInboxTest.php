<?php

use App\Jobs\SendTelegramNotificationJob;
use App\Models\ContactMessage;
use App\Models\User;
use App\Services\TelegramNotifierService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

test('guests can submit contact form successfully', function () {
    Queue::fake();

    $response = $this->postJson('/api/v1/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Business Inquiry',
        'message' => 'Hello, I would like to build a website with you.',
        // 'website' is left blank (honeypot)
    ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Pesan Anda berhasil terkirim! Saya akan segera menghubungi Anda.',
        ]);

    $this->assertDatabaseHas('contact_messages', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Business Inquiry',
        'message' => 'Hello, I would like to build a website with you.',
        'status' => 'unread',
        'sender_ip' => '127.0.0.1',
    ]);

    Queue::assertPushed(SendTelegramNotificationJob::class);
});

test('submissions with honeypot field filled are rejected', function () {
    Queue::fake();

    $response = $this->postJson('/api/v1/contact', [
        'name' => 'Spam Bot',
        'email' => 'bot@spammer.com',
        'subject' => 'Get rich quick',
        'message' => 'Visit my spam link.',
        'website' => 'http://spamlink.com', // Honeypot filled
    ]);

    $response->assertStatus(422);

    $this->assertDatabaseMissing('contact_messages', [
        'name' => 'Spam Bot',
    ]);

    Queue::assertNotPushed(SendTelegramNotificationJob::class);
});

test('contact form rate limiting prevents spamming', function () {
    Queue::fake();

    // Hit the rate limit (5 attempts allowed)
    for ($i = 0; $i < 5; $i++) {
        $response = $this->postJson('/api/v1/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Hello',
            'message' => 'Message '.$i,
        ]);
        $response->assertStatus(201);
    }

    // 6th attempt should be blocked with 429
    $response = $this->postJson('/api/v1/contact', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'subject' => 'Hello',
        'message' => 'Blocked message',
    ]);
    $response->assertStatus(429);
});

test('telegram notification job sends request to api and updates timestamp', function () {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    config(['services.telegram.bot_token' => 'mock_token']);
    config(['services.telegram.chat_id' => 'mock_chat_id']);

    $message = ContactMessage::factory()->create([
        'telegram_notified_at' => null,
    ]);

    $job = new SendTelegramNotificationJob($message);
    $job->handle(new TelegramNotifierService);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.telegram.org/botmock_token/sendMessage')
            && $request['chat_id'] === 'mock_chat_id';
    });

    $this->assertNotNull($message->fresh()->telegram_notified_at);
});

test('telegram notification job handles missing credentials gracefully', function () {
    Log::shouldReceive('warning')
        ->once()
        ->withArgs(fn ($message, $context) => str_contains($message, 'Telegram Bot Token or Chat ID is not configured') && $context['message_id'] > 0);

    config(['services.telegram.bot_token' => null]);
    config(['services.telegram.chat_id' => null]);

    $message = ContactMessage::factory()->create([
        'telegram_notified_at' => null,
    ]);

    $job = new SendTelegramNotificationJob($message);
    $job->handle(new TelegramNotifierService);

    $this->assertNull($message->fresh()->telegram_notified_at);
});

test('guests cannot access CMS inbox', function () {
    $response = $this->get('/admin-cms/inbox');

    $response->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('admin can view CMS inbox and mark message as read', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create([
        'status' => 'unread',
    ]);

    $response = $this->actingAs($user)
        ->get('/admin-cms/inbox');

    $response->assertStatus(200);

    // Mark as read
    $responsePatch = $this->actingAs($user)
        ->patch("/admin-cms/inbox/{$message->id}/read");

    $responsePatch->assertRedirect();
    $this->assertEquals('read', $message->fresh()->status);
});

test('admin can mark message as replied', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create([
        'status' => 'read',
    ]);

    $response = $this->actingAs($user)
        ->patch("/admin-cms/inbox/{$message->id}/replied");

    $response->assertRedirect();
    $this->assertEquals('replied', $message->fresh()->status);
});

test('admin can delete message', function () {
    $user = User::factory()->create();
    $message = ContactMessage::factory()->create();

    $response = $this->actingAs($user)
        ->delete("/admin-cms/inbox/{$message->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('contact_messages', [
        'id' => $message->id,
    ]);
});
