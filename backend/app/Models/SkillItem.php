<?php

namespace App\Models;

use App\Enums\SkillLevel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $skill_id
 * @property string|null $name
 * @property int|null $technology_id
 * @property SkillLevel $level
 * @property float|null $years_of_experience
 * @property bool $is_featured
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Skill $skill
 * @property-read Technology|null $technology
 */
#[Fillable([
    'skill_id',
    'name',
    'technology_id',
    'level',
    'years_of_experience',
    'is_featured',
    'order',
])]
class SkillItem extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => SkillLevel::class,
            'is_featured' => 'boolean',
            'years_of_experience' => 'float',
            'order' => 'integer',
        ];
    }

    /**
     * Get the group/category this item belongs to.
     */
    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    /**
     * Get the technology associated with the skill item.
     */
    public function technology(): BelongsTo
    {
        return $this->belongsTo(Technology::class);
    }

    /**
     * Get the display name of the skill item.
     */
    public function getDisplayNameAttribute(): string
    {
        if (! empty($this->name)) {
            return $this->name;
        }

        if ($this->technology_id && $this->relationLoaded('technology') && $this->technology) {
            return $this->technology->name;
        }

        return '';
    }
}
