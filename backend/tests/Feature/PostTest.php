<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

test('guests cannot access posts index', function () {
    $response = $this->get(route('posts.index'));
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('administrator can view posts index', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('posts.index'));
    $response->assertStatus(200);
});

test('reading time and excerpt are auto-calculated on model saving', function () {
    $post = Post::create([
        'title' => 'Sample Article',
        'slug' => 'sample-article',
        'content' => '<p>One two three four five six seven eight nine ten.</p>',
        'status' => 'draft',
    ]);

    // Word count is 10. reading_time = ceil(10/200) = 1 minute.
    $this->assertEquals(1, $post->reading_time);

    // Excerpt should be auto generated from content (10 words)
    $this->assertEquals('One two three four five six seven eight nine ten.', $post->excerpt);
});

test('administrator can create a blog post and sync relations', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $postA = Post::create([
        'title' => 'Post A',
        'slug' => 'post-a',
        'content' => 'Content of A',
        'status' => 'published',
    ]);

    $response = $this->actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'New Article Title',
            'slug' => 'new-article-slug',
            'content' => 'This is a long test article content with words.',
            'status' => 'draft',
            'category_ids' => [$category->id],
            'related_post_ids' => [$postA->id],
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('posts', [
        'title' => 'New Article Title',
        'slug' => 'new-article-slug',
        'status' => 'draft',
    ]);

    $createdPost = Post::where('slug', 'new-article-slug')->first();
    $this->assertTrue($createdPost->categories->contains($category));
    $this->assertTrue($createdPost->relatedPosts->contains($postA));
});

test('public api lists only published posts', function () {
    $publishedPost = Post::create([
        'title' => 'Published Post',
        'slug' => 'published-post',
        'content' => 'Visible to public',
        'status' => 'published',
    ]);

    $draftPost = Post::create([
        'title' => 'Draft Post',
        'slug' => 'draft-post',
        'content' => 'Hidden to public',
        'status' => 'draft',
    ]);

    $response = $this->getJson('/api/v1/posts');
    $response->assertStatus(200)
        ->assertJsonFragment(['slug' => 'published-post'])
        ->assertJsonMissing(['slug' => 'draft-post']);
});

test('public api show returns related posts with automatic fallback', function () {
    $category = Category::create([
        'name' => 'General',
        'slug' => 'general',
    ]);

    $post1 = Post::create([
        'title' => 'Main Post',
        'slug' => 'main-post',
        'content' => 'This is the main post content.',
        'status' => 'published',
    ]);
    $post1->categories()->attach($category);

    $post2 = Post::create([
        'title' => 'Another Post in Category',
        'slug' => 'another-post-in-category',
        'content' => 'This shares the category.',
        'status' => 'published',
    ]);
    $post2->categories()->attach($category);

    // Get detail of post1 (which has no manual related posts configured)
    $response = $this->getJson('/api/v1/posts/main-post');
    $response->assertStatus(200)
        ->assertJsonPath('data.related_posts.0.slug', 'another-post-in-category');
});

test('public api show returns views count and adjacent posts', function () {
    $postOlder = Post::create([
        'title' => 'Older Post',
        'slug' => 'older-post',
        'content' => 'Content older',
        'status' => 'published',
        'published_at' => now()->subDay(),
    ]);

    $postCurrent = Post::create([
        'title' => 'Current Post',
        'slug' => 'current-post',
        'content' => 'Content current',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $postNewer = Post::create([
        'title' => 'Newer Post',
        'slug' => 'newer-post',
        'content' => 'Content newer',
        'status' => 'published',
        'published_at' => now()->addDay(),
    ]);

    // Record view first so that views_count is 1
    $this->postJson('/api/v1/posts/current-post/view');

    $response = $this->getJson('/api/v1/posts/current-post');
    $response->assertStatus(200)
        ->assertJsonPath('data.views_count', 1)
        ->assertJsonPath('data.previous_post.slug', 'older-post')
        ->assertJsonPath('data.next_post.slug', 'newer-post');
});
