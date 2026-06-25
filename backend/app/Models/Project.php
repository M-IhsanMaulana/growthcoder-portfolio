<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $short_description
 * @property string|null $full_description
 * @property int $category_id
 * @property int|null $cover_image_id
 * @property string $status
 * @property bool $is_featured
 * @property int $order
 * @property string|null $live_url
 * @property string|null $github_url
 * @property string|null $telegram_url
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
#[Fillable([
    'title',
    'slug',
    'short_description',
    'full_description',
    'category_id',
    'cover_image_id',
    'cover_image_caption',
    'status',
    'is_featured',
    'order',
    'live_url',
    'github_url',
    'telegram_url',
    'published_at',
])]
class Project extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Relation to ProjectCategory.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'category_id');
    }

    /**
     * Relation to Media (Cover Image).
     */
    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    /**
     * Relation to Technologies (pivot).
     */
    public function technologies(): BelongsToMany
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    /**
     * Relation to Media (Gallery Images).
     */
    public function galleryImages(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'project_images', 'project_id', 'media_id')
            ->withPivot('id', 'order', 'caption')
            ->withTimestamps()
            ->orderBy('project_images.order', 'asc');
    }
}
