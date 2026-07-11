<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteSettingResource;
use App\Models\Media;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettingController extends Controller
{
    /**
     * Get the global site settings.
     */
    public function index(): SiteSettingResource
    {
        $settingsData = Cache::rememberForever('site_settings', function () {
            $siteSetting = SiteSetting::firstOrCreate([
                'id' => 1,
            ], [
                'owner_full_name' => 'Muhammad Ihsan Maulana',
                'owner_title' => 'Full-Stack Developer',
                'hero_headline' => 'Crafting High-Performance Web Solutions & Intelligent Automations',
                'hero_subheadline' => 'Specializing in Laravel, Vue.js, Nuxt, and Telegram Bot Ecosystems.',
                'site_name' => 'growthcoder.id',
            ]);

            $siteSetting->loadMissing(['profilePhoto', 'defaultOgImage']);

            return [
                'id' => $siteSetting->id,
                'owner_full_name' => $siteSetting->owner_full_name,
                'owner_title' => $siteSetting->owner_title,
                'profile_photo' => $siteSetting->profilePhoto ? $siteSetting->profilePhoto->getAttributes() : null,
                'hero_headline' => $siteSetting->hero_headline,
                'hero_subheadline' => $siteSetting->hero_subheadline,
                'hero_cta_text' => $siteSetting->hero_cta_text,
                'hero_cta_url' => $siteSetting->hero_cta_url,
                'cv_file_path' => $siteSetting->cv_file_path,
                'social_linkedin' => $siteSetting->social_linkedin,
                'social_github' => $siteSetting->social_github,
                'social_telegram' => $siteSetting->social_telegram,
                'social_instagram' => $siteSetting->social_instagram,
                'social_twitter' => $siteSetting->social_twitter,
                'contact_email' => $siteSetting->contact_email,
                'site_name' => $siteSetting->site_name,
                'meta_title_suffix' => $siteSetting->meta_title_suffix,
                'default_meta_desc' => $siteSetting->default_meta_desc,
                'google_analytics_id' => $siteSetting->google_analytics_id,
                'google_site_verification' => $siteSetting->google_site_verification,
                'default_og_image' => $siteSetting->defaultOgImage ? $siteSetting->defaultOgImage->getAttributes() : null,
                // About page fields
                'about_bio' => $siteSetting->about_bio,
                'about_location' => $siteSetting->about_location,
                'about_specialities' => $siteSetting->about_specialities ?? [],
                'about_stats' => $siteSetting->about_stats ?? [],
            ];
        });

        $siteSetting = new SiteSetting;
        $siteSetting->forceFill(collect($settingsData)->except(['profile_photo', 'default_og_image'])->toArray());
        $siteSetting->exists = true;

        if (! empty($settingsData['profile_photo'])) {
            $media = new Media;
            $media->forceFill($settingsData['profile_photo']);
            $media->exists = true;
            $siteSetting->setRelation('profilePhoto', $media);
        }

        if (! empty($settingsData['default_og_image'])) {
            $media = new Media;
            $media->forceFill($settingsData['default_og_image']);
            $media->exists = true;
            $siteSetting->setRelation('defaultOgImage', $media);
        }

        return new SiteSettingResource($siteSetting);
    }
}
