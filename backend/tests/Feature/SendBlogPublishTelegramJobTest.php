<?php

use App\Enums\PostStatus;
use App\Jobs\SendBlogPublishTelegramJob;
use App\Models\Category;
use App\Models\IntegrationSetting;
use App\Models\Post;
use App\Models\User;
use App\Services\TelegramNotifierService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    IntegrationSetting::setValue('telegram_enabled', '1');
    IntegrationSetting::setValue('telegram_bot_token', 'mock_token');
    IntegrationSetting::setValue('telegram_chat_id', '123456');
});

test('job skips sending if post already notified', function () {
    Http::fake();

    $post = Post::create([
        'title' => 'Already Notified Post',
        'slug' => 'already-notified',
        'content' => 'Some content',
        'status' => PostStatus::Published,
        'published_at' => now(),
        'telegram_notified_at' => now()->subMinute(), // already notified
    ]);

    $job = new SendBlogPublishTelegramJob($post);
    $job->handle(new TelegramNotifierService);

    Http::assertNothingSent();
    expect($post->fresh()->telegram_notified_at)->not->toBeNull();
});

test('job sends notification and updates telegram_notified_at', function () {
    Http::fake([
        'api.telegram.org/*' => Http::response(['ok' => true], 200),
    ]);

    $category = Category::create(['name' => 'Tech', 'slug' => 'tech']);

    $post = Post::create([
        'title' => 'New Blog Post',
        'slug' => 'new-blog-post',
        'content' => 'Content here',
        'status' => PostStatus::Published,
        'published_at' => now(),
        'telegram_notified_at' => null,
    ]);
    $post->categories()->attach($category);

    $job = new SendBlogPublishTelegramJob($post);
    $job->handle(new TelegramNotifierService);

    Http::assertSent(fn ($request) => str_contains($request->url(), 'api.telegram.org/botmock_token/sendMessage')
        && $request['chat_id'] === '123456'
        && str_contains($request['text'], 'New Blog Post')
    );

    expect($post->fresh()->telegram_notified_at)->not->toBeNull();
});

test('job skips quietly when telegram is disabled', function () {
    Http::fake();
    IntegrationSetting::setValue('telegram_enabled', '0');

    $post = Post::create([
        'title' => 'Test Post',
        'slug' => 'test-post-disabled',
        'content' => 'Content',
        'status' => PostStatus::Published,
        'published_at' => now(),
        'telegram_notified_at' => null,
    ]);

    $job = new SendBlogPublishTelegramJob($post);
    $job->handle(new TelegramNotifierService);

    Http::assertNothingSent();
});

test('publishing a post directly dispatches SendBlogPublishTelegramJob', function () {
    Queue::fake();

    $user = User::factory()->create();
    $category = Category::create(['name' => 'Dev', 'slug' => 'dev']);

    $this->actingAs($user)->post('/admin-cms/posts', [
        'title' => 'Directly Published Post',
        'content' => 'Some content here for the post',
        'status' => 'published',
        'category_ids' => [$category->id],
    ]);

    Queue::assertPushed(SendBlogPublishTelegramJob::class);
});

test('scheduling a post does not dispatch SendBlogPublishTelegramJob', function () {
    Queue::fake();

    $user = User::factory()->create();
    $category = Category::create(['name' => 'Dev2', 'slug' => 'dev2']);

    $this->actingAs($user)->post('/admin-cms/posts', [
        'title' => 'Scheduled Post',
        'content' => 'Some content here for the post',
        'status' => 'scheduled',
        'scheduled_at' => now()->addDay()->toDateTimeString(),
        'category_ids' => [$category->id],
    ]);

    Queue::assertNotPushed(SendBlogPublishTelegramJob::class);
});

test('blog template variables are replaced correctly', function () {
    $service = new TelegramNotifierService;

    $template = "📝 *{title}*\n{excerpt}\nKategori: {categories}\n{published_at}\n{url}";
    $result = $service->buildMessage($template, [
        'title' => 'Hello World',
        'excerpt' => 'A short intro',
        'categories' => 'Tech, Dev',
        'published_at' => '27 Jul 2026 23:00',
        'url' => 'https://example.com/blog/hello-world',
    ]);

    expect($result)->toContain('Hello World')
        ->toContain('A short intro')
        ->toContain('Tech, Dev')
        ->toContain('https://example.com/blog/hello-world');
});
