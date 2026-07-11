<?php

namespace Database\Seeders;

use App\Models\DevelopmentPhilosophy;
use App\Models\Workflow;
use Illuminate\Database\Seeder;

class WorkflowAndPhilosophySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Workflows Seed
        $workflows = [
            [
                'title' => 'Discovery & Analysis',
                'slug' => 'discovery-analysis',
                'short_description' => 'Menganalisis kebutuhan bisnis, menentukan cakupan proyek, dan menyusun spesifikasi teknis.',
                'icon' => IconHelper::getSvg('Search'),
                'is_active' => true,
                'order' => 0,
            ],
            [
                'title' => 'Design & Architecture',
                'slug' => 'design-architecture',
                'short_description' => 'Merancang arsitektur database, desain API, serta memilih tech stack yang paling sesuai.',
                'icon' => IconHelper::getSvg('Layers'),
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Clean Coding & Dev',
                'slug' => 'clean-coding-dev',
                'short_description' => 'Proses coding menggunakan arsitektur bersih, reusable components, dan integrasi API terstruktur.',
                'icon' => IconHelper::getSvg('Code2'),
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Testing & CI/CD Deploy',
                'slug' => 'testing-cicd-deploy',
                'short_description' => 'Melakukan testing menyeluruh kemudian deployment otomatis menggunakan pipeline CI/CD.',
                'icon' => IconHelper::getSvg('Rocket'),
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($workflows as $wf) {
            Workflow::updateOrCreate(['slug' => $wf['slug']], $wf);
        }

        // Philosophies Seed
        $philosophies = [
            [
                'title' => 'Clean & Scalable Code',
                'slug' => 'clean-scalable-code',
                'description' => 'Menulis kode yang bersih, mudah dibaca, serta arsitektur yang dirancang untuk skala besar.',
                'icon' => IconHelper::getSvg('Code'),
                'is_active' => true,
                'order' => 0,
            ],
            [
                'title' => 'Security & Privacy First',
                'slug' => 'security-privacy-first',
                'description' => 'Menerapkan proteksi keamanan terbaik untuk data pengguna dan integritas sistem sejak baris kode pertama.',
                'icon' => IconHelper::getSvg('ShieldCheck'),
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Continuous Innovation',
                'slug' => 'continuous-innovation',
                'description' => 'Terus bereksplorasi dengan teknologi mutakhir untuk memberikan solusi digital yang melampaui ekspektasi.',
                'icon' => IconHelper::getSvg('Sparkles'),
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Product-Centric Mindset',
                'slug' => 'product-centric-mindset',
                'description' => 'Fokus pada nilai bisnis produk dan kenyamanan pengalaman pengguna akhir (UX) di setiap fitur.',
                'icon' => IconHelper::getSvg('Star'),
                'is_active' => true,
                'order' => 3,
            ],
        ];

        foreach ($philosophies as $phil) {
            DevelopmentPhilosophy::updateOrCreate(['slug' => $phil['slug']], $phil);
        }
    }
}
