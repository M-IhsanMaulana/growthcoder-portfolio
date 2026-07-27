<?php

namespace App\Http\Controllers;

use App\Models\IntegrationSetting;
use App\Services\TelegramNotifierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationController extends Controller
{
    /**
     * Show the integration settings edit form.
     */
    public function edit(): Response
    {
        $settings = IntegrationSetting::getMany([
            'telegram_enabled',
            'telegram_bot_token',
            'telegram_chat_id',
            'telegram_template_contact',
            'telegram_template_blog_publish',
        ]);

        return Inertia::render('integrations/Edit', [
            'telegram' => [
                'enabled' => (bool) ($settings['telegram_enabled'] ?? false),
                'bot_token' => $settings['telegram_bot_token'] ? '••••••••' : '',
                'bot_token_set' => ! empty($settings['telegram_bot_token']),
                'chat_id' => $settings['telegram_chat_id'] ?? '',
                'template_contact' => $settings['telegram_template_contact'] ?? TelegramNotifierService::DEFAULT_TEMPLATE_CONTACT,
                'template_blog_publish' => $settings['telegram_template_blog_publish'] ?? TelegramNotifierService::DEFAULT_TEMPLATE_BLOG_PUBLISH,
            ],
        ]);
    }

    /**
     * Update Telegram integration settings.
     */
    public function updateTelegram(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'bot_token' => 'nullable|string|max:500',
            'chat_id' => 'nullable|string|max:255',
            'template_contact' => 'nullable|string|max:4000',
            'template_blog_publish' => 'nullable|string|max:4000',
        ], [
            'enabled.required' => 'Status aktif wajib diisi.',
        ]);

        IntegrationSetting::setValue('telegram_enabled', $validated['enabled'] ? '1' : '0');
        IntegrationSetting::setValue('telegram_chat_id', $validated['chat_id'] ?? null);
        IntegrationSetting::setValue('telegram_template_contact', $validated['template_contact'] ?? TelegramNotifierService::DEFAULT_TEMPLATE_CONTACT);
        IntegrationSetting::setValue('telegram_template_blog_publish', $validated['template_blog_publish'] ?? TelegramNotifierService::DEFAULT_TEMPLATE_BLOG_PUBLISH);

        // Only update bot_token if a new value was provided (not the masked placeholder)
        if (! empty($validated['bot_token']) && $validated['bot_token'] !== '••••••••') {
            IntegrationSetting::setValue('telegram_bot_token', $validated['bot_token']);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Pengaturan integrasi Telegram berhasil disimpan.'),
        ]);

        return redirect()->back();
    }

    /**
     * Send a test Telegram message to verify configuration.
     */
    public function testTelegram(Request $request): JsonResponse
    {
        $service = new TelegramNotifierService;

        if (! $service->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram belum dikonfigurasi atau tidak aktif. Pastikan Bot Token, Chat ID sudah diisi dan integrasi diaktifkan.',
            ], 422);
        }

        try {
            $service->sendTestMessage();

            return response()->json([
                'success' => true,
                'message' => 'Pesan tes berhasil dikirim ke Telegram! Cek bot Telegram Anda.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan tes: '.$e->getMessage(),
            ], 500);
        }
    }
}
