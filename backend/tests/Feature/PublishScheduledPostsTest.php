<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Support\Carbon;

test('artisan command publishes scheduled posts whose publish time has arrived', function () {
    Carbon::setTestNow(now());

    $pastPost = Post::create([
        'title' => 'Past Scheduled Post',
        'slug' => 'past-scheduled-post',
        'content' => 'Content of past post',
        'status' => PostStatus::Scheduled,
        'scheduled_at' => now()->subMinute(),
    ]);

    $futurePost = Post::create([
        'title' => 'Future Scheduled Post',
        'slug' => 'future-scheduled-post',
        'content' => 'Content of future post',
        'status' => PostStatus::Scheduled,
        'scheduled_at' => now()->addHour(),
    ]);

    // Execute the command
    $this->artisan('posts:publish-scheduled')
        ->expectsOutput("Published: {$pastPost->title} (ID: {$pastPost->id})")
        ->expectsOutput('Successfully published 1 scheduled post(s).')
        ->assertExitCode(0);

    // Refresh model data
    $pastPost->refresh();
    $futurePost->refresh();

    // Check assertions
    $this->assertEquals(PostStatus::Published, $pastPost->status);
    $this->assertEquals($pastPost->scheduled_at->toDateTimeString(), $pastPost->published_at->toDateTimeString());

    $this->assertEquals(PostStatus::Scheduled, $futurePost->status);
    $this->assertNull($futurePost->published_at);

    Carbon::setTestNow(); // Reset time test
});
