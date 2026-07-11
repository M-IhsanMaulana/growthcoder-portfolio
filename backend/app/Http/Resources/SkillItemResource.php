<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillItemResource extends JsonResource
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
            'skill_id' => $this->skill_id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'technology_id' => $this->technology_id,
            'level' => $this->level->value,
            'level_label' => $this->level->label(),
            'years_of_experience' => $this->years_of_experience,
            'is_featured' => (bool) $this->is_featured,
            'order' => (int) $this->order,
            'technology' => new TechnologyResource($this->whenLoaded('technology')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
