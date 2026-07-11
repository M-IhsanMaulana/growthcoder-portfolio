<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish scheduled blog posts whose publish time has arrived';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $posts = Post::where('status', PostStatus::Scheduled)
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts to publish.');

            return 0;
        }

        $count = 0;
        foreach ($posts as $post) {
            $post->status = PostStatus::Published;
            // Set published_at to the original scheduled_at time
            $post->published_at = $post->scheduled_at;
            $post->save();
            $count++;
            $this->info("Published: {$post->title} (ID: {$post->id})");
        }

        $this->info("Successfully published {$count} scheduled post(s).");

        return 0;
    }
}
