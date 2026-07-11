<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;

test('public api post show increments view count for real users', function () {
    $post = Post::create([
        'title' => 'Analytics Article',
        'slug' => 'analytics-article',
        'content' => 'Content here',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->assertDatabaseCount('post_views', 0);

    // Hit the public API endpoint as a regular visitor
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Referer' => 'https://github.com/someuser',
    ])->postJson('/api/v1/posts/analytics-article/view');

    $response->assertStatus(200);
    $this->assertDatabaseCount('post_views', 1);

    $this->assertDatabaseHas('post_views', [
        'post_id' => $post->id,
        'device' => 'desktop',
        'referrer' => 'github.com',
    ]);
});

test('public api post show filters out bot visits from views counting', function () {
    $post = Post::create([
        'title' => 'Bot Test Article',
        'slug' => 'bot-test-article',
        'content' => 'Content here',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->assertDatabaseCount('post_views', 0);

    // Hit with bot User Agent
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
    ])->postJson('/api/v1/posts/bot-test-article/view');

    $response->assertStatus(200);

    // View count should remain 0
    $this->assertDatabaseCount('post_views', 0);
});

test('public api post show cooldown prevents spam views within 1 hour', function () {
    $post = Post::create([
        'title' => 'Cooldown Article',
        'slug' => 'cooldown-article',
        'content' => 'Content here',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->assertDatabaseCount('post_views', 0);

    $headers = [
        'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        'REMOTE_ADDR' => '127.0.0.1',
    ];

    // First view
    $this->withHeaders($headers)->postJson('/api/v1/posts/cooldown-article/view');
    $this->assertDatabaseCount('post_views', 1);

    // Second view from same IP within 1 hour (immediate)
    $this->withHeaders($headers)->postJson('/api/v1/posts/cooldown-article/view');
    $this->assertDatabaseCount('post_views', 1); // Should not increase

    // Third view after 1 hour and 1 minute
    Carbon::setTestNow(now()->addMinutes(61));

    $this->withHeaders($headers)->postJson('/api/v1/posts/cooldown-article/view');
    $this->assertDatabaseCount('post_views', 2); // Should increase to 2

    Carbon::setTestNow(); // Reset test time
});

test('guest cannot access admin article show and preview', function () {
    $post = Post::create([
        'title' => 'Secret Article',
        'slug' => 'secret-article',
        'content' => 'Content here',
        'status' => 'published',
    ]);

    $this->get(route('posts.show', $post->id))->assertRedirect(route('login'));
    $this->get(route('posts.preview', $post->id))->assertRedirect(route('login'));
});

test('administrator can access admin article show and preview', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'title' => 'Public Analytics Article',
        'slug' => 'public-analytics-article',
        'content' => 'Content here',
        'status' => 'published',
    ]);

    // Admin access Show
    $responseShow = $this->actingAs($user)->get(route('posts.show', $post->id));
    $responseShow->assertStatus(200);

    // Admin access Preview
    $responsePreview = $this->actingAs($user)->get(route('posts.preview', $post->id));
    $responsePreview->assertStatus(200);
});
