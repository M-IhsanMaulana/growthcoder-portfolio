<?php

namespace App\Models;

use App\Services\HashidsHelper;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $original_filename
 * @property string $storage_path
 * @property string $mime_type
 * @property int $file_size
 * @property int $width
 * @property int $height
 * @property string|null $alt_text
 * @property array|null $variants
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read string $encoded_id
 * @property-read array<string, string> $urls
 */
#[Fillable([
    'original_filename',
    'filename',
    'storage_path',
    'mime_type',
    'file_size',
    'width',
    'height',
    'alt_text',
    'variants',
])]
class Media extends Model
{
    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['encoded_id', 'urls'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /**
     * Get the encoded HashID for the media.
     */
    public function getEncodedIdAttribute(): string
    {
        return HashidsHelper::encode($this->id);
    }

    /**
     * Get URLs for all available variants.
     *
     * @return array<string, string>
     */
    public function getUrlsAttribute(): array
    {
        $slugId = $this->filename.'-'.$this->encoded_id;

        return [
            'original' => route('media.show', ['slug_id' => $slugId, 'variant' => 'original']),
            'webp' => route('media.show', ['slug_id' => $slugId, 'variant' => 'webp']),
            'thumbnail' => route('media.show', ['slug_id' => $slugId, 'variant' => 'thumbnail']),
            'medium' => route('media.show', ['slug_id' => $slugId, 'variant' => 'medium']),
            'large' => route('media.show', ['slug_id' => $slugId, 'variant' => 'large']),
        ];
    }
}
