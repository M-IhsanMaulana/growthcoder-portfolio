<?php

namespace Database\Seeders;

use App\Enums\SkillLevel;
use App\Models\Skill;
use App\Models\SkillItem;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Safe clear
        SkillItem::query()->delete();
        Skill::query()->delete();

        // Get technologies
        $html5 = Technology::where('slug', 'html5')->first();
        $css = Technology::where('slug', 'css')->first();
        $javascript = Technology::where('slug', 'javascript')->first();
        $react = Technology::where('slug', 'react')->first();
        $tailwindcss = Technology::where('slug', 'tailwindcss')->first();

        $laravel = Technology::where('slug', 'laravel')->first();
        $nodejs = Technology::where('slug', 'nodejs')->first();
        $postgresql = Technology::where('slug', 'postgresql')->first();
        $mysql = Technology::where('slug', 'mysql')->first();

        $redis = Technology::where('slug', 'redis')->first();
        $mongodb = Technology::where('slug', 'mongodb')->first();

        $git = Technology::where('slug', 'git')->first();
        $docker = Technology::where('slug', 'docker')->first();
        $linux = Technology::where('slug', 'linux')->first();

        // 1. Frontend Development
        $frontend = Skill::create([
            'name' => 'Frontend Development',
            'order' => 1,
        ]);

        SkillItem::create([
            'skill_id' => $frontend->id,
            'name' => 'HTML5',
            'technology_id' => $html5?->id,
            'level' => SkillLevel::Expert,
            'years_of_experience' => 5.0,
            'is_featured' => true,
            'order' => 1,
        ]);

        SkillItem::create([
            'skill_id' => $frontend->id,
            'name' => 'CSS',
            'technology_id' => $css?->id,
            'level' => SkillLevel::Expert,
            'years_of_experience' => 5.0,
            'is_featured' => true,
            'order' => 2,
        ]);

        SkillItem::create([
            'skill_id' => $frontend->id,
            'name' => 'JavaScript',
            'technology_id' => $javascript?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 4.0,
            'is_featured' => true,
            'order' => 3,
        ]);

        SkillItem::create([
            'skill_id' => $frontend->id,
            'name' => 'React.js',
            'technology_id' => $react?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 3.5,
            'is_featured' => true,
            'order' => 4,
        ]);

        SkillItem::create([
            'skill_id' => $frontend->id,
            'name' => 'Tailwind CSS',
            'technology_id' => $tailwindcss?->id,
            'level' => SkillLevel::Expert,
            'years_of_experience' => 4.0,
            'is_featured' => true,
            'order' => 5,
        ]);

        // 2. Backend Development
        $backend = Skill::create([
            'name' => 'Backend Development',
            'order' => 2,
        ]);

        SkillItem::create([
            'skill_id' => $backend->id,
            'name' => 'Laravel (PHP)',
            'technology_id' => $laravel?->id,
            'level' => SkillLevel::Expert,
            'years_of_experience' => 4.0,
            'is_featured' => true,
            'order' => 1,
        ]);

        SkillItem::create([
            'skill_id' => $backend->id,
            'name' => 'Node.js',
            'technology_id' => $nodejs?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 3.0,
            'is_featured' => true,
            'order' => 2,
        ]);

        SkillItem::create([
            'skill_id' => $backend->id,
            'name' => 'RESTful API',
            'technology_id' => null,
            'level' => SkillLevel::Expert,
            'years_of_experience' => 4.5,
            'is_featured' => true,
            'order' => 3,
        ]);

        SkillItem::create([
            'skill_id' => $backend->id,
            'name' => 'MySQL',
            'technology_id' => $mysql?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 4.0,
            'is_featured' => true,
            'order' => 4,
        ]);

        SkillItem::create([
            'skill_id' => $backend->id,
            'name' => 'PostgreSQL',
            'technology_id' => $postgresql?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 3.0,
            'is_featured' => true,
            'order' => 5,
        ]);

        // 3. Database & Storage
        $database = Skill::create([
            'name' => 'Database & Storage',
            'order' => 3,
        ]);

        SkillItem::create([
            'skill_id' => $database->id,
            'name' => 'MySQL',
            'technology_id' => $mysql?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 4.0,
            'is_featured' => true,
            'order' => 1,
        ]);

        SkillItem::create([
            'skill_id' => $database->id,
            'name' => 'PostgreSQL',
            'technology_id' => $postgresql?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 3.0,
            'is_featured' => true,
            'order' => 2,
        ]);

        SkillItem::create([
            'skill_id' => $database->id,
            'name' => 'Redis',
            'technology_id' => $redis?->id,
            'level' => SkillLevel::Intermediate,
            'years_of_experience' => 2.5,
            'is_featured' => true,
            'order' => 3,
        ]);

        SkillItem::create([
            'skill_id' => $database->id,
            'name' => 'MongoDB',
            'technology_id' => $mongodb?->id,
            'level' => SkillLevel::Intermediate,
            'years_of_experience' => 2.0,
            'is_featured' => true,
            'order' => 4,
        ]);

        // 4. DevOps & Tools
        $devops = Skill::create([
            'name' => 'DevOps & Tools',
            'order' => 4,
        ]);

        SkillItem::create([
            'skill_id' => $devops->id,
            'name' => 'Git & GitHub',
            'technology_id' => $git?->id,
            'level' => SkillLevel::Expert,
            'years_of_experience' => 5.0,
            'is_featured' => true,
            'order' => 1,
        ]);

        SkillItem::create([
            'skill_id' => $devops->id,
            'name' => 'Docker',
            'technology_id' => $docker?->id,
            'level' => SkillLevel::Intermediate,
            'years_of_experience' => 2.0,
            'is_featured' => true,
            'order' => 2,
        ]);

        SkillItem::create([
            'skill_id' => $devops->id,
            'name' => 'CI / CD',
            'technology_id' => null,
            'level' => SkillLevel::Intermediate,
            'years_of_experience' => 2.0,
            'is_featured' => true,
            'order' => 3,
        ]);

        SkillItem::create([
            'skill_id' => $devops->id,
            'name' => 'Linux',
            'technology_id' => $linux?->id,
            'level' => SkillLevel::Advanced,
            'years_of_experience' => 3.0,
            'is_featured' => true,
            'order' => 4,
        ]);

        // 5. Other Skills
        $other = Skill::create([
            'name' => 'Other Skills',
            'order' => 5,
        ]);

        $otherSkillsList = [
            'REST API Design',
            'Problem Solving',
            'UI/UX Understanding',
            'Clean Code',
            'API Integration',
        ];

        foreach ($otherSkillsList as $index => $name) {
            SkillItem::create([
                'skill_id' => $other->id,
                'name' => $name,
                'technology_id' => null,
                'level' => SkillLevel::Expert,
                'years_of_experience' => null,
                'is_featured' => true,
                'order' => $index + 1,
            ]);
        }

        // 6. Soft Skills
        $soft = Skill::create([
            'name' => 'Soft Skills',
            'order' => 6,
        ]);

        $softSkillsList = [
            'Komunikasi',
            'Kerja Tim',
            'Manajemen Waktu',
            'Adaptif',
            'Inisiatif',
            'Detail Oriented',
        ];

        foreach ($softSkillsList as $index => $name) {
            SkillItem::create([
                'skill_id' => $soft->id,
                'name' => $name,
                'technology_id' => null,
                'level' => SkillLevel::Advanced,
                'years_of_experience' => null,
                'is_featured' => true,
                'order' => $index + 1,
            ]);
        }
    }
}
