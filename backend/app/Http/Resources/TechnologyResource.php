<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnologyResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'description' => $this->description,
            'url' => $this->url,
            'is_featured' => (bool) $this->is_featured,
            'logo' => $this->logo ? [
                'id' => $this->logo->id,
                'original_filename' => $this->logo->original_filename,
                'alt_text' => $this->logo->alt_text,
                'urls' => $this->logo->urls,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
