<?php

namespace App\Models;

use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $company
 * @property string $title_position
 * @property string|null $location
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string|null $description
 * @property string|null $website_url
 * @property int|null $logo_media_id
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Media|null $logo
 */
#[Fillable([
    'company',
    'title_position',
    'location',
    'start_date',
    'end_date',
    'description',
    'website_url',
    'logo_media_id',
    'order',
])]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'order' => 'integer',
        ];
    }

    /**
     * Get the logo media associated with the experience.
     */
    public function logo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }
}
