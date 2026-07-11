<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SiteSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner_full_name' => $this->owner_full_name,
            'owner_title' => $this->owner_title,

            'profile_photo' => $this->profilePhoto ? [
                'id' => $this->profilePhoto->id,
                'original_filename' => $this->profilePhoto->original_filename,
                'alt_text' => $this->profilePhoto->alt_text,
                'urls' => $this->profilePhoto->urls,
            ] : null,

            'hero_headline' => $this->hero_headline,
            'hero_subheadline' => $this->hero_subheadline,
            'hero_cta_text' => $this->hero_cta_text,
            'hero_cta_url' => $this->hero_cta_url,

            'cv_file_url' => $this->cv_file_path ? Storage::disk('public')->url($this->cv_file_path) : null,

            'social_linkedin' => $this->social_linkedin,
            'social_github' => $this->social_github,
            'social_telegram' => $this->social_telegram,
            'social_instagram' => $this->social_instagram,
            'social_twitter' => $this->social_twitter,
            'contact_email' => $this->contact_email,

            'site_name' => $this->site_name,
            'meta_title_suffix' => $this->meta_title_suffix,
            'default_meta_desc' => $this->default_meta_desc,
            'google_analytics_id' => $this->google_analytics_id,
            'google_site_verification' => $this->google_site_verification,

            'default_og_image' => $this->defaultOgImage ? [
                'id' => $this->defaultOgImage->id,
                'original_filename' => $this->defaultOgImage->original_filename,
                'alt_text' => $this->defaultOgImage->alt_text,
                'urls' => $this->defaultOgImage->urls,
            ] : null,

            // About page fields
            'about_bio' => $this->about_bio,
            'about_location' => $this->about_location,
            'about_specialities' => $this->about_specialities ?? [],
            'about_stats' => $this->about_stats ?? [],
        ];
    }
}
