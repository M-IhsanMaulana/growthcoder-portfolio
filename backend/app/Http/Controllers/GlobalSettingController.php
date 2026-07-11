<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class GlobalSettingController extends Controller
{
    /**
     * Show the global settings edit form.
     */
    public function edit(): Response
    {
        $settings = SiteSetting::firstOrCreate([
            'id' => 1,
        ], [
            'owner_full_name' => 'Muhammad Ihsan Maulana',
            'owner_title' => 'Full-Stack Developer',
            'hero_headline' => 'Crafting High-Performance Web Solutions & Intelligent Automations',
            'hero_subheadline' => 'Specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.',
            'site_name' => 'growthcoder.id',
        ]);

        $settings->load(['profilePhoto', 'defaultOgImage']);

        return Inertia::render('global-settings/Edit', [
            'settings' => $settings,
        ]);
    }

    /**
     * Update the global site settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $settings = SiteSetting::findOrFail(1);

        $validated = $request->validate([
            'owner_full_name' => 'required|string|max:255',
            'owner_title' => 'nullable|string|max:255',
            'profile_photo_id' => 'nullable|exists:media,id',
            'hero_headline' => 'required|string|max:255',
            'hero_subheadline' => 'nullable|string',
            'hero_cta_text' => 'nullable|string|max:100',
            'hero_cta_url' => 'nullable|string|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_github' => 'nullable|url|max:255',
            'social_telegram' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'site_name' => 'required|string|max:255',
            'meta_title_suffix' => 'nullable|string|max:100',
            'default_meta_desc' => 'nullable|string|max:160',
            'default_og_image_id' => 'nullable|exists:media,id',
            'google_analytics_id' => 'nullable|string|max:50',
            'google_site_verification' => 'nullable|string|max:100',
            'api_key' => 'nullable|string|max:255',

            // About fields
            'about_bio' => 'nullable|string',
            'about_location' => 'nullable|string|max:255',
            'about_specialities' => 'nullable|array',
            'about_specialities.*' => 'string|max:100',
            'about_stats' => 'nullable|array',
            'about_stats.*.value' => 'required|string|max:50',
            'about_stats.*.label' => 'required|string|max:100',
            'about_stats.*.emoji' => 'nullable|string|max:10',
        ], [
            'owner_full_name.required' => 'Nama lengkap wajib diisi.',
            'hero_headline.required' => 'Headline wajib diisi.',
            'site_name.required' => 'Nama situs wajib diisi.',
            'social_linkedin.url' => 'Tautan LinkedIn harus berupa URL yang valid (menggunakan https://).',
            'social_github.url' => 'Tautan GitHub harus berupa URL yang valid (menggunakan https://).',
            'social_telegram.url' => 'Tautan Telegram harus berupa URL yang valid (menggunakan https://).',
            'social_instagram.url' => 'Tautan Instagram harus berupa URL yang valid (menggunakan https://).',
            'social_twitter.url' => 'Tautan Twitter harus berupa URL yang valid (menggunakan https://).',
            'contact_email.email' => 'Email kontak harus berupa alamat email yang valid.',
        ]);

        $settings->update($validated);

        // Clear settings cache for public API
        Cache::forget('site_settings');
        Cache::forget('site_api_key');

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Pengaturan global berhasil diperbarui.'),
        ]);

        return redirect()->back();
    }

    /**
     * Upload or update the CV file.
     */
    public function uploadCv(Request $request): RedirectResponse
    {
        $settings = SiteSetting::findOrFail(1);

        $request->validate([
            'cv_file' => 'required|file|mimes:pdf|max:5120',
        ], [
            'cv_file.required' => 'File CV wajib dipilih.',
            'cv_file.mimes' => 'File CV harus berupa dokumen format PDF.',
            'cv_file.max' => 'Ukuran file CV tidak boleh melebihi 5 MB.',
        ]);

        // Delete old file if exists
        if ($settings->cv_file_path && Storage::disk('public')->exists($settings->cv_file_path)) {
            Storage::disk('public')->delete($settings->cv_file_path);
        }

        // Store new file with consistent directory and name structure
        $path = $request->file('cv_file')->storeAs('cv', 'cv-latest.pdf', 'public');

        $settings->update([
            'cv_file_path' => $path,
        ]);

        // Clear settings cache for public API
        Cache::forget('site_settings');
        Cache::forget('site_api_key');

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('File CV berhasil diunggah.'),
        ]);

        return redirect()->back();
    }

    /**
     * Sync about stats from real database counts.
     *
     * Only syncs project and technology counts automatically.
     * Other stats (years learning, passion) remain unchanged.
     */
    public function syncAboutStats(): RedirectResponse
    {
        $settings = SiteSetting::findOrFail(1);

        /** @var array<int, array{value: string, label: string, emoji: string}> $currentStats */
        $currentStats = $settings->about_stats ?? $this->defaultAboutStats();

        $projectsCount = Project::where('status', 'published')->count();
        $technologiesCount = Technology::count();

        // Update only the syncable stats, keep manual ones intact
        $updatedStats = collect($currentStats)->map(function (array $stat) use ($projectsCount, $technologiesCount): array {
            if (str_contains(strtolower($stat['label']), 'project')) {
                $stat['value'] = $projectsCount.'+';
            } elseif (str_contains(strtolower($stat['label']), 'technolog') || str_contains(strtolower($stat['label']), 'teknologi')) {
                $stat['value'] = $technologiesCount.'+';
            }

            return $stat;
        })->values()->all();

        $settings->update([
            'about_stats' => $updatedStats,
        ]);

        // Clear settings cache for public API
        Cache::forget('site_settings');

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Statistik berhasil disinkronisasi dari data nyata.'),
        ]);

        return redirect()->back();
    }

    /**
     * Get default about stats structure.
     *
     * @return array<int, array{value: string, label: string, emoji: string}>
     */
    private function defaultAboutStats(): array
    {
        return [
            ['value' => '0+', 'label' => 'Projects Completed', 'emoji' => '📁'],
            ['value' => '3+', 'label' => 'Years Learning', 'emoji' => '🎓'],
            ['value' => '0+', 'label' => 'Technologies Used', 'emoji' => ''],
            ['value' => '100%', 'label' => 'Passion for Learning', 'emoji' => '⭐'],
        ];
    }
}
