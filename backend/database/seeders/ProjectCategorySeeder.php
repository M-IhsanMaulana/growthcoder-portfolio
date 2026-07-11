<?php

namespace Database\Seeders;

use App\Models\ProjectCategory;
use Illuminate\Database\Seeder;

class ProjectCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectCategory::query()->delete();

        $categories = [
            [
                'name' => 'Web Application',
                'slug' => 'web-application',
                'description' => 'Full-featured web applications built with modern frameworks and robust backends.',
                'icon' => IconHelper::getSvg('Globe'),
                'order' => 1,
            ],
            [
                'name' => 'Mobile Application',
                'slug' => 'mobile-application',
                'description' => 'Cross-platform and native mobile apps designed for seamless user experience.',
                'icon' => IconHelper::getSvg('Smartphone'),
                'order' => 2,
            ],
            [
                'name' => 'Telegram Bot / Integration',
                'slug' => 'telegram-bot',
                'description' => 'Custom Telegram bots and automated workflows to streamline operations and communication.',
                'icon' => IconHelper::getSvg('Bot'),
                'order' => 3,
            ],
            [
                'name' => 'Open Source / Tools',
                'slug' => 'open-source',
                'description' => 'Developer utility tools, packages, and open source contributions.',
                'icon' => IconHelper::getSvg('Code'),
                'order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            ProjectCategory::create($category);
        }
    }
}
