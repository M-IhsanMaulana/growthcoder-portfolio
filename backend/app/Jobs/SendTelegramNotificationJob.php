<?php

namespace App\Jobs;

use App\Models\ContactMessage;
use App\Services\TelegramNotifierService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SendTelegramNotificationJob implements ShouldQueue
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
    public function __construct(public ContactMessage $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TelegramNotifierService $service): void
    {
        $botToken = config('services.telegram.bot_token');
        $chatId = config('services.telegram.chat_id');

        if (empty($botToken) || empty($chatId)) {
            $service->sendContactMessage($this->message);

            return;
        }

        $service->sendContactMessage($this->message);

        $this->message->update([
            'telegram_notified_at' => now(),
        ]);
    }
}
