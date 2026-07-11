<?php

namespace App\Services;

use App\Models\ContactMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifierService
{
    protected ?string $botToken;

    protected ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * Send a contact message notification to Telegram.
     */
    public function sendContactMessage(ContactMessage $message): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram Bot Token or Chat ID is not configured. Telegram notification was skipped.', [
                'message_id' => $message->id,
            ]);

            return true;
        }

        $truncatedMessage = strlen($message->message) > 500
            ? substr($message->message, 0, 500).'...'
            : $message->message;

        $text = "📬 *Pesan Kontak Baru!*\n\n"
            .'👤 *Nama:* '.$this->escapeMarkdown($message->name)."\n"
            .'📧 *Email:* '.$this->escapeMarkdown($message->email)."\n"
            .'📌 *Subjek:* '.$this->escapeMarkdown($message->subject ?? '(tidak ada)')."\n\n"
            ."💬 *Pesan:*\n".$this->escapeMarkdown($truncatedMessage)."\n\n"
            .'⏰ *Diterima:* '.$message->created_at->format('Y-m-d H:i:s');

        try {
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->failed()) {
                Log::error('Failed to send Telegram notification', [
                    'message_id' => $message->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Telegram API returned error status: '.$response->status());
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Exception caught while sending Telegram notification', [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Escape special characters for Markdown formatting in Telegram.
     */
    protected function escapeMarkdown(string $text): string
    {
        return str_replace(['_', '*', '`', '['], ['\\_', '\\*', '\\`', '\\['], $text);
    }
}
