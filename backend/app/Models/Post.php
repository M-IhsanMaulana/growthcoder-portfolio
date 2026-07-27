<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $content
 * @property string $status
 * @property Carbon|null $published_at
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $telegram_notified_at
 * @property int|null $cover_image_id
 * @property int $reading_time
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'title',
    'slug',
    'excerpt',
    'content',
    'status',
    'published_at',
    'scheduled_at',
    'telegram_notified_at',
    'cover_image_id',
    'reading_time',
    'meta_title',
    'meta_description',
])]
class Post extends Model
{
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'telegram_notified_at' => 'datetime',
            'status' => PostStatus::class,
        ];
    }

    /**
     * Get the categories for the post.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }

    /**
     * Get the cover image associated with the post.
     */
    public function coverImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_image_id');
    }

    /**
     * Get the manual related posts.
     */
    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_related', 'post_id', 'related_post_id');
    }

    /**
     * Get the view records for the post.
     */
    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (Post $post) {
            // Strip HTML tags for clean calculations
            $cleanContent = strip_tags($post->content);

            // Auto calculate reading time (assuming 200 words per minute)
            $words = preg_split('/\s+/u', trim($cleanContent));
            $wordCount = (! empty($words) && $words[0] !== '') ? count($words) : 0;
            $post->reading_time = (int) ceil($wordCount / 200);

            // Auto generate excerpt if empty
            if (empty($post->excerpt)) {
                $trimmedContent = preg_replace('/\s+/', ' ', $cleanContent);
                $post->excerpt = mb_strimwidth(trim($trimmedContent), 0, 150, '...');
            }

            // Sync published_at if status becomes published and it's not set
            if ($post->status === PostStatus::Published && is_null($post->published_at)) {
                $post->published_at = now();
            }
        });
    }
}
