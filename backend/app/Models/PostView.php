<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $post_id
 * @property string $ip_hash
 * @property string|null $user_agent
 * @property string $device
 * @property string $referrer
 * @property Carbon $created_at
 */
#[Fillable([
    'post_id',
    'ip_hash',
    'user_agent',
    'device',
    'referrer',
])]
class PostView extends Model
{
    /**
     * Disable updated_at timestamp.
     */
    const UPDATED_AT = null;

    /**
     * Get the post that owns the view record.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
