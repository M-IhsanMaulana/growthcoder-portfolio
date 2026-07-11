<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $owner_full_name
 * @property string|null $owner_title
 * @property int|null $profile_photo_id
 * @property string $hero_headline
 * @property string|null $hero_subheadline
 * @property string|null $hero_cta_text
 * @property string|null $hero_cta_url
 * @property string|null $cv_file_path
 * @property string|null $api_key
 * @property string|null $social_linkedin
 * @property string|null $social_github
 * @property string|null $social_telegram
 * @property string|null $social_instagram
 * @property string|null $social_twitter
 * @property string|null $contact_email
 * @property string $site_name
 * @property string|null $meta_title_suffix
 * @property string|null $default_meta_desc
 * @property int|null $default_og_image_id
 * @property string|null $google_analytics_id
 * @property string|null $google_site_verification
 * @property string|null $about_bio
 * @property string|null $about_location
 * @property array<int, string>|null $about_specialities
 * @property array<int, array{value: string, label: string, emoji: string}>|null $about_stats
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'owner_full_name',
    'owner_title',
    'profile_photo_id',
    'hero_headline',
    'hero_subheadline',
    'hero_cta_text',
    'hero_cta_url',
    'cv_file_path',
    'api_key',
    'social_linkedin',
    'social_github',
    'social_telegram',
    'social_instagram',
    'social_twitter',
    'contact_email',
    'site_name',
    'meta_title_suffix',
    'default_meta_desc',
    'default_og_image_id',
    'google_analytics_id',
    'google_site_verification',
    'about_bio',
    'about_location',
    'about_specialities',
    'about_stats',
])]
class SiteSetting extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'about_specialities' => 'array',
        'about_stats' => 'array',
    ];

    /**
     * Get the profile photo associated with the settings.
     */
    public function profilePhoto(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'profile_photo_id');
    }

    /**
     * Get the default Open Graph image associated with the settings.
     */
    public function defaultOgImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'default_og_image_id');
    }
}
