<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::query()->delete();

        $categories = [
            [
                'name' => 'Programming',
                'slug' => 'programming',
                'description' => 'Articles and guides about software engineering, clean code, and algorithms.',
                'meta_title' => 'Programming Tutorials & Guides',
                'meta_description' => 'Learn programming best practices, code formatting, patterns, and tips.',
            ],
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Modern frontend and backend techniques and framework deep-dives.',
                'meta_title' => 'Web Development Tips & Insights',
                'meta_description' => 'Step-by-step guides on Laravel, Vue.js, Nuxt 4, CSS/Tailwind, and databases.',
            ],
            [
                'name' => 'Automation',
                'slug' => 'automation',
                'description' => 'Telegram bots, server scripting, and automated workflow integrations.',
                'meta_title' => 'Task Automation & Telegram Bots',
                'meta_description' => 'Build high-performance Telegram bots, server tasks automation, and web scraping utilities.',
            ],
            [
                'name' => 'Career & Tips',
                'slug' => 'career-and-tips',
                'description' => 'Advice, resources, and learnings from a developer\'s professional journey.',
                'meta_title' => 'Developer Career Advice & Mindsets',
                'meta_description' => 'Learn how to transition into coding, grow your career, and level up as a software developer.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
