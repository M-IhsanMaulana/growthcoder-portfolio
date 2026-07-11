<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
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
            'institution' => $this->institution,
            'degree' => $this->degree,
            'major' => $this->major,
            'gpa' => $this->gpa,
            'location' => $this->location,
            'start_date' => $this->start_date?->format('Y-m'),
            'end_date' => $this->end_date?->format('Y-m'),
            'description' => $this->description,
            'logo_media_id' => $this->logo_media_id,
            'logo' => $this->logo ? [
                'id' => $this->logo->id,
                'original_filename' => $this->logo->original_filename,
                'alt_text' => $this->logo->alt_text,
                'urls' => $this->logo->urls,
            ] : null,
            'order' => (int) $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
