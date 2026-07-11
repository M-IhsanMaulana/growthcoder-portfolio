<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@growthcoder.id')],
            [
                'name' => env('ADMIN_NAME', 'Muhammad Ihsan Maulana'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'email_verified_at' => now(),
            ]
        );

        $this->call([
            SiteSettingSeeder::class,
            TechnologySeeder::class,
            ProjectCategorySeeder::class,
            ProjectSeeder::class,
            SkillSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
            ServiceSeeder::class,
            WorkflowAndPhilosophySeeder::class,
            EducationSeeder::class,
            ExperienceSeeder::class,
            ContactMessageSeeder::class,
        ]);
    }
}
