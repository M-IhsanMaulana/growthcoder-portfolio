<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lucideIcons = ['Code', 'Globe', 'Zap', 'Bot', 'Server', 'Database', 'Layers', 'Cpu', 'Wrench', 'Rocket'];
        $title = fake()->unique()->randomElement([
            'Full-Stack Web Development',
            'API Integration',
            'Telegram Bot Development',
            'Performance Optimization',
            'Mobile-First UI Development',
            'Backend Architecture',
            'Database Design',
            'DevOps & Deployment',
        ]);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->realText(150),
            'long_description' => '<p>'.implode('</p><p>', fake()->paragraphs(3)).'</p>',
            'icon' => fake()->randomElement($lucideIcons),
            'is_active' => true,
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate that the service is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the service is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
