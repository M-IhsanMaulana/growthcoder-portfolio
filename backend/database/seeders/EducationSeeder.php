<?php

namespace Database\Seeders;

use App\Models\Education;
use Illuminate\Database\Seeder;

class EducationSeeder extends Seeder
{
    use CreatesMedia;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Education::query()->delete();

        // Vocational High School
        $logo1 = $this->createDummyMedia('smk-logo.png', 'SMK Negeri 1 Jakarta Logo');
        Education::create([
            'institution' => 'SMK Negeri 1 Jakarta',
            'degree' => 'SMK (Vocational High School)',
            'major' => 'Software Engineering',
            'location' => 'Jakarta, Indonesia',
            'start_date' => '2017-07-15',
            'end_date' => '2020-05-20',
            'description' => '<p>Completed core software engineering classes covering programming algorithms, basic web development (HTML, CSS, JavaScript, PHP), database design, and software lifecycle methods.</p>',
            'logo_media_id' => $logo1->id,
            'order' => 1,
        ]);

        // Bachelor Degree
        $logo2 = $this->createDummyMedia('binus-logo.png', 'Universitas Bina Nusantara Logo');
        Education::create([
            'institution' => 'Universitas Bina Nusantara',
            'degree' => 'Bachelor of Science (S.Kom)',
            'major' => 'Computer Science / Informatics',
            'gpa' => '3.82 / 4.00',
            'location' => 'Jakarta, Indonesia (Hybrid)',
            'start_date' => '2020-09-01',
            'end_date' => '2024-08-30',
            'description' => '<p>Specialized in Software Engineering. Key coursework: Object-Oriented Programming, Database Systems, Web Application Development, Distributed Systems, Software Design & Architecture, and Cloud Computing.</p>',
            'logo_media_id' => $logo2->id,
            'order' => 2,
        ]);
    }
}
