<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostDetailResource extends JsonResource
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
            'content' => $this->content,
            'status' => $this->status,
            'reading_time' => (int) $this->reading_time,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
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
            'related_posts' => PostResource::collection($this->whenLoaded('relatedPosts')),
            'views_count' => (int) $this->views_count,
            'previous_post' => $this->previous_post ? [
                'title' => $this->previous_post->title,
                'slug' => $this->previous_post->slug,
            ] : null,
            'next_post' => $this->next_post ? [
                'title' => $this->next_post->title,
                'slug' => $this->next_post->slug,
            ] : null,
        ];
    }
}
