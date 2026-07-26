<?php

namespace App\Models;

use App\Enums\TechnologyCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int|null $logo_media_id
 * @property TechnologyCategory $category
 * @property string|null $description
 * @property string|null $url
 * @property bool $is_featured
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Media|null $logo
 */
#[Fillable([
    'name',
    'slug',
    'logo_media_id',
    'category',
    'description',
    'url',
    'is_featured',
])]
class Technology extends Model
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
            'category' => TechnologyCategory::class,
        ];
    }

    /**
     * Get the logo media associated with the technology.
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }
}
