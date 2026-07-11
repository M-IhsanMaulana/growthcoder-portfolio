<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Education>
 */
class EducationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'institution' => $this->faker->company().' University',
            'degree' => $this->faker->randomElement(['S1', 'D3', 'SMK']),
            'major' => $this->faker->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Rekayasa Perangkat Lunak']),
            'gpa' => (string) $this->faker->randomFloat(2, 3, 4),
            'location' => $this->faker->city(),
            'start_date' => $this->faker->date('Y-m-d'),
            'end_date' => $this->faker->boolean(70) ? $this->faker->date('Y-m-d') : null,
            'description' => '<p>'.$this->faker->paragraph().'</p>',
            'logo_media_id' => null,
            'order' => 0,
        ];
    }
}
