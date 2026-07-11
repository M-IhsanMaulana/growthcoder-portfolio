<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'reading_time' => (int) $this->reading_time,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cover_image' => $this->coverImage ? [
                'id' => $this->coverImage->id,
                'original_filename' => $this->coverImage->original_filename,
                'alt_text' => $this->coverImage->alt_text,
                'urls' => $this->coverImage->urls,
            ] : null,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        ];
    }
}
