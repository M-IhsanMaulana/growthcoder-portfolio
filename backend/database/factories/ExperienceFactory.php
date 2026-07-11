<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Experience>
 */
class ExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company' => $this->faker->company(),
            'title_position' => $this->faker->jobTitle(),
            'location' => $this->faker->city().' (Remote)',
            'start_date' => $this->faker->date('Y-m-d'),
            'end_date' => $this->faker->boolean(70) ? $this->faker->date('Y-m-d') : null,
            'description' => '<p>'.$this->faker->paragraph().'</p>',
            'website_url' => $this->faker->url(),
            'logo_media_id' => null,
            'order' => 0,
        ];
    }
}
