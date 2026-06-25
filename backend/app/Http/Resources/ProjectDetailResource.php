<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectDetailResource extends JsonResource
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
            'short_description' => $this->short_description,
            'full_description' => $this->full_description,
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'order' => (int) $this->order,
            'live_url' => $this->live_url,
            'github_url' => $this->github_url,
            'telegram_url' => $this->telegram_url,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new ProjectCategoryResource($this->category),
            'cover_image_caption' => $this->cover_image_caption,
            'cover_image' => $this->coverImage ? [
                'id' => $this->coverImage->id,
                'original_filename' => $this->coverImage->original_filename,
                'alt_text' => $this->coverImage->alt_text,
                'urls' => $this->coverImage->urls,
            ] : null,
            'technologies' => TechnologyResource::collection($this->technologies),
            'gallery' => $this->galleryImages->map(function ($image) {
                return [
                    'id' => $image->id,
                    'original_filename' => $image->original_filename,
                    'alt_text' => $image->alt_text,
                    'urls' => $image->urls,
                    'order' => (int) $image->pivot->order,
                    'caption' => $image->pivot->caption,
                ];
            }),
        ];
    }
}
