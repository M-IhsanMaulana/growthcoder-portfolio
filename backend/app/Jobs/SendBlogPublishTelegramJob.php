<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\TelegramNotifierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SendBlogPublishTelegramJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public Post $post)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramNotifierService $service): void
    {
        // Skip if already notified (prevent duplicate notifications)
        if ($this->post->telegram_notified_at !== null) {
            return;
        }

        $service->sendBlogPublishNotification($this->post);

        $this->post->update([
            'telegram_notified_at' => now(),
        ]);
    }
}
