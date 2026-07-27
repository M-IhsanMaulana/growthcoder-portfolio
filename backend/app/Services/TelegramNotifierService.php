<?php

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\IntegrationSetting;
use App\Models\Post;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifierService
{
    protected ?string $botToken;

    protected ?string $chatId;

    protected bool $isEnabled;

    /**
     * Default template for contact message notifications.
     */
    public const DEFAULT_TEMPLATE_CONTACT = "🌐 *Notifikasi dari Growthcode Web Portfolio*\n\n📬 *Pesan Kontak Baru\!*\n\n👤 *Nama:* {name}\n📧 *Email:* {email}\n📌 *Subjek:* {subject}\n\n💬 *Pesan:*\n{message}\n\n⏰ *Diterima:* {received_at}";

    /**
     * Default template for blog post publish notifications.
     */
    public const DEFAULT_TEMPLATE_BLOG_PUBLISH = "🌐 *Notifikasi dari Growthcode Web Portfolio*\n\n📝 *Artikel Baru Dipublish\!*\n\n📌 *{title}*\n\n{excerpt}\n\n🏷️ *Kategori:* {categories}\n📅 *Dipublish:* {published_at}\n\n🔗 Baca selengkapnya: {url}";

    public function __construct()
    {
        $settings = IntegrationSetting::getMany([
            'telegram_bot_token',
            'telegram_chat_id',
            'telegram_enabled',
        ]);

        $this->botToken = $settings['telegram_bot_token'] ?: null;
        $this->chatId = $settings['telegram_chat_id'] ?: null;
        $this->isEnabled = (bool) ($settings['telegram_enabled'] ?? false);
    }

    /**
     * Send a contact message notification to Telegram.
     */
    public function sendContactMessage(ContactMessage $message): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('Telegram not configured or disabled. Contact message notification skipped.', [
                'message_id' => $message->id,
            ]);

            return true;
        }

        $truncatedMessage = strlen($message->message) > 500
            ? substr($message->message, 0, 500) . '...'
            : $message->message;

        $template = IntegrationSetting::getValue('telegram_template_contact', self::DEFAULT_TEMPLATE_CONTACT);

        $text = $this->buildMessage((string) $template, [
            'name' => $message->name,
            'email' => $message->email,
            'subject' => $message->subject ?? '(tidak ada)',
            'message' => $truncatedMessage,
            'received_at' => $message->created_at->format('Y-m-d H:i:s'),
        ]);

        return $this->sendMessage($text, ['message_id' => $message->id]);
    }

    /**
     * Send a blog post publish notification to Telegram.
     */
    public function sendBlogPublishNotification(Post $post): bool
    {
        if (! $this->isConfigured()) {
            Log::warning('Telegram not configured or disabled. Blog publish notification skipped.', [
                'post_id' => $post->id,
            ]);

            return true;
        }

        $post->load(['categories']);

        $template = IntegrationSetting::getValue('telegram_template_blog_publish', self::DEFAULT_TEMPLATE_BLOG_PUBLISH);

        $categories = $post->categories->pluck('name')->join(', ');
        $excerpt = $post->excerpt ? (strlen($post->excerpt) > 300 ? substr($post->excerpt, 0, 300) . '...' : $post->excerpt) : '';
        $frontendUrl = config('app.frontend_url', config('app.url'));
        $url = rtrim($frontendUrl, '/') . '/blog/' . $post->slug;

        $text = $this->buildMessage((string) $template, [
            'title' => $post->title,
            'url' => $url,
            'excerpt' => $excerpt,
            'categories' => $categories ?: '(tidak ada kategori)',
            'published_at' => ($post->published_at ?? now())->format('d M Y H:i'),
        ]);

        return $this->sendMessage($text, ['post_id' => $post->id]);
    }

    /**
     * Send a test message to verify the Telegram configuration.
     */
    public function sendTestMessage(): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            return false;
        }

        $text = "🌐 *Notifikasi dari Growthcode Web Portfolio*\n\n✅ *Tes Koneksi Berhasil\!*\n\nIntegrasi Telegram Anda sudah dikonfigurasi dengan benar dan siap digunakan\.\n\n⏰ _Dikirim pada: " . now()->format('d M Y H:i:s') . '_';

        return $this->sendMessage($text, []);
    }

    /**
     * Build a message from a template by replacing placeholders.
     *
     * @param  array<string, string>  $variables
     */
    public function buildMessage(string $template, array $variables): string
    {
        $search = array_map(fn(string $key) => '{' . $key . '}', array_keys($variables));
        $replace = array_values(array_map(
            fn(string $value) => $this->escapeMarkdown($value),
            $variables
        ));

        return str_replace($search, $replace, $template);
    }

    /**
     * Check whether Telegram is properly configured and enabled.
     */
    public function isConfigured(): bool
    {
        return $this->isEnabled && ! empty($this->botToken) && ! empty($this->chatId);
    }

    /**
     * Send a raw message to Telegram API.
     *
     * @param  array<string, mixed>  $logContext
     */
    protected function sendMessage(string $text, array $logContext = []): bool
    {
        try {
            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
                'chat_id' => $this->chatId,
                'text' => $text,
                'parse_mode' => 'Markdown',
            ]);

            if ($response->failed()) {
                Log::error('Failed to send Telegram notification', array_merge($logContext, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]));
                throw new \Exception('Telegram API returned error status: ' . $response->status());
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Exception caught while sending Telegram notification', array_merge($logContext, [
                'error' => $e->getMessage(),
            ]));
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
