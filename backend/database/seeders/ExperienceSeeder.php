<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    use CreatesMedia;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Experience::query()->delete();

        // Junior Web Developer
        $logo1 = $this->createDummyMedia('techindo-logo.png', 'TechIndo Solutions Logo');
        Experience::create([
            'company' => 'TechIndo Solutions',
            'title_position' => 'Junior Web Developer',
            'location' => 'Jakarta, Indonesia (Hybrid)',
            'start_date' => '2020-06-01',
            'end_date' => '2022-03-31',
            'description' => '<p>Assisted in building custom corporate web sites and custom administrative backend databases using native PHP and simple MVC principles.</p><p>Optimized MySQL query execution times, cleaned legacy Javascript codes, and designed responsive HTML pages for various browser viewports.</p>',
            'website_url' => 'https://example-techindo.com',
            'logo_media_id' => $logo1->id,
            'order' => 1,
        ]);

        // Lead Full-Stack Developer
        $logo2 = $this->createDummyMedia('growthcoder-logo.png', 'GrowthCoder Agency Logo');
        Experience::create([
            'company' => 'GrowthCoder Agency',
            'title_position' => 'Lead Full-Stack Developer',
            'location' => 'Remote',
            'start_date' => '2022-04-01',
            'end_date' => null, // Present
            'description' => '<p>Responsible for architecting clean, secure, and performant web systems from inception to deployment. Lead client workshops to refine system requirements.</p><p>Key achievements include containerizing agency development structures using Docker, deploying robust API endpoints for Nuxt and mobile consumers, and automating user billing systems via custom Telegram bots.</p>',
            'website_url' => 'https://growthcoder.id',
            'logo_media_id' => $logo2->id,
            'order' => 2,
        ]);
    }
}
