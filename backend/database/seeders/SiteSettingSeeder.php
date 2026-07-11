<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSetting::firstOrCreate([
            'id' => 1,
        ], [
            'owner_full_name' => 'Muhammad Ihsan Maulana',
            'owner_title' => 'Full-Stack Developer',
            'profile_photo_id' => null,
            'hero_headline' => 'Crafting High-Performance Web Solutions & Intelligent Automations',
            'hero_subheadline' => 'Specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.',
            'hero_cta_text' => 'Lihat Proyek',
            'hero_cta_url' => '/projects',
            'cv_file_path' => null,
            'api_key' => Str::random(40),
            'social_linkedin' => 'https://linkedin.com',
            'social_github' => 'https://github.com',
            'social_telegram' => 'https://t.me',
            'social_instagram' => 'https://instagram.com',
            'social_twitter' => null,
            'contact_email' => 'contact@growthcoder.id',
            'site_name' => 'growthcoder.id',
            'meta_title_suffix' => ' | growthcoder.id',
            'default_meta_desc' => 'Portofolio Profesional & Blog Muhammad Ihsan Maulana.',
            'default_og_image_id' => null,
        ]);
    }
}
