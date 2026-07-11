<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Technology;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    use CreatesMedia;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Safe clear
        DB::table('project_images')->delete();
        DB::table('project_technology')->delete();
        Project::query()->delete();

        // Get category IDs
        $webCat = ProjectCategory::where('slug', 'web-application')->first();
        $mobileCat = ProjectCategory::where('slug', 'mobile-application')->first();
        $botCat = ProjectCategory::where('slug', 'telegram-bot')->first();
        $toolCat = ProjectCategory::where('slug', 'open-source')->first();

        // Get technologies
        $laravel = Technology::where('slug', 'laravel')->first();
        $vuejs = Technology::where('slug', 'vuejs')->first();
        $nuxt = Technology::where('slug', 'nuxt')->first();
        $tailwindcss = Technology::where('slug', 'tailwindcss')->first();
        $typescript = Technology::where('slug', 'typescript')->first();
        $nodejs = Technology::where('slug', 'nodejs')->first();
        $expressjs = Technology::where('slug', 'expressjs')->first();
        $postgresql = Technology::where('slug', 'postgresql')->first();
        $mysql = Technology::where('slug', 'mysql')->first();
        $redis = Technology::where('slug', 'redis')->first();
        $docker = Technology::where('slug', 'docker')->first();

        // Project 1: growthcoder.id
        $p1 = Project::create([
            'title' => 'growthcoder.id - Personal Portfolio & Blog',
            'slug' => 'growthcoder-portfolio-blog',
            'short_description' => 'A high-performance personal portfolio website and developer blog featuring an Inertia-based admin dashboard and a Nuxt SSR frontend.',
            'full_description' => '<p>A comprehensive monorepo project showcasing personal portfolios, services, education/experience timelines, and articles. The application features a secure admin dashboard powered by Laravel 13, Inertia.js 3.0, and Tailwind CSS v4, and a public-facing website built with Nuxt 4 (SSR) for optimal performance and SEO.</p><p>Key features include dynamic content updates, media management, unified settings, contact forms, and structured JSON-LD schemas.</p>',
            'category_id' => $webCat->id,
            'cover_image_id' => $this->createDummyMedia('growthcoder-cover.webp', 'growthcoder.id Homepage Mockup')->id,
            'cover_image_caption' => 'growthcoder.id homepage preview',
            'role' => 'Full-Stack Developer',
            'key_features' => [
                ['title' => 'High Performance', 'description' => 'Optimized for speed with Nuxt 4, Nitro Engine, and server-side rendering (SSR).', 'icon' => 'Zap'],
                ['title' => 'SEO Friendly', 'description' => 'Built with best practices for SEO including structured JSON-LD schema markup.', 'icon' => 'Search'],
                ['title' => 'Clean Code', 'description' => 'Well-structured, maintainable, and scalable monorepo codebase using modern design patterns.', 'icon' => 'Code2'],
                ['title' => 'Fully Responsive', 'description' => 'Perfectly responsive user interface, looking stunning on all devices from mobile to desktop.', 'icon' => 'Smartphone'],
            ],
            'status' => 'published',
            'is_featured' => true,
            'order' => 1,
            'live_url' => 'https://growthcoder.id',
            'github_url' => 'https://github.com/M-IhsanMaulana/growthcoder-portfolio',
            'published_at' => now(),
        ]);
        $p1->technologies()->sync([$laravel->id, $nuxt->id, $tailwindcss->id, $typescript->id, $postgresql->id, $docker->id]);

        // Gallery images
        $img1 = $this->createDummyMedia('growthcoder-gallery1.webp', 'growthcoder.id Admin Dashboard');
        $img2 = $this->createDummyMedia('growthcoder-gallery2.webp', 'growthcoder.id Blog Page');
        $p1->galleryImages()->attach($img1->id, ['order' => 1, 'caption' => 'Inertia.js Admin Panel Dashboard']);
        $p1->galleryImages()->attach($img2->id, ['order' => 2, 'caption' => 'Blog section with search and category filter']);

        // Project 2: Auto-Billing Bot
        $p2 = Project::create([
            'title' => 'Auto-Billing Telegram Bot',
            'slug' => 'auto-billing-telegram-bot',
            'short_description' => 'A transaction monitoring and auto-billing Telegram bot integrated with local payment gateways (Xendit) for automated SaaS subscriptions.',
            'full_description' => '<p>A customized Telegram bot built with Node.js and Express.js to automate user registration, subscription invoice creation, payment status webhook handling, and user permission updates. Uses Redis for task queuing and PostgreSQL for long-term database storage.</p>',
            'category_id' => $botCat->id,
            'cover_image_id' => $this->createDummyMedia('telegram-bot-cover.webp', 'Auto-Billing Bot Interface')->id,
            'cover_image_caption' => 'Telegram Bot user interaction flow',
            'role' => 'Backend & Automation Developer',
            'key_features' => [
                ['title' => 'Instant Response', 'description' => 'Fast webhook handling with immediate user state updates and notifications.', 'icon' => 'Zap'],
                ['title' => 'Secure Checkout', 'description' => 'Integrated with Xendit payment gateway API for automatic invoice generation and verification.', 'icon' => 'ShieldCheck'],
                ['title' => 'Queue Management', 'description' => 'Robust message queue handling powered by Redis to handle high-frequency events.', 'icon' => 'Layers'],
            ],
            'status' => 'published',
            'is_featured' => true,
            'order' => 2,
            'telegram_url' => 'https://t.me/example_billing_bot',
            'published_at' => now(),
        ]);
        $p2->technologies()->sync([$nodejs->id, $expressjs->id, $postgresql->id, $redis->id, $docker->id]);

        // Project 3: Headless API
        $p3 = Project::create([
            'title' => 'E-Commerce Headless API',
            'slug' => 'ecommerce-headless-api',
            'short_description' => 'A headless e-commerce backend platform offering RESTful APIs, cart/checkout management, and an Inertia.js admin panel.',
            'full_description' => '<p>A robust RESTful API built on Laravel, featuring product catalog caching via Redis, checkout processing with automatic tax calculations, payment gateway integrations, and order tracking dashboards. The admin system is managed using Vue.js and Tailwind CSS.</p>',
            'category_id' => $webCat->id,
            'cover_image_id' => $this->createDummyMedia('ecommerce-cover.webp', 'E-Commerce Dashboard Mockup')->id,
            'cover_image_caption' => 'Admin dashboard showing analytics',
            'role' => 'Backend Developer',
            'key_features' => [
                ['title' => 'RESTful API Design', 'description' => 'Clean API interfaces with comprehensive versioning, authentication, and standard resources.', 'icon' => 'Code2'],
                ['title' => 'Cache Optimization', 'description' => 'Product catalog query caching via Redis to achieve sub-millisecond response times.', 'icon' => 'Layers'],
                ['title' => 'Robust Checkout', 'description' => 'Reliable checkout transaction workflows supporting concurrent order inventory calculations.', 'icon' => 'ShieldCheck'],
            ],
            'status' => 'published',
            'is_featured' => false,
            'order' => 3,
            'github_url' => 'https://github.com/example/ecommerce-api',
            'published_at' => now(),
        ]);
        $p3->technologies()->sync([$laravel->id, $vuejs->id, $tailwindcss->id, $mysql->id, $redis->id]);

        // Project 4: Glassmorphism Tool
        $p4 = Project::create([
            'title' => 'Tailwind Glassmorphism Generator',
            'slug' => 'tailwind-glassmorphism-generator',
            'short_description' => 'An interactive web utility tool that allows developers to design and generate CSS/Tailwind classes for glassmorphic UI components.',
            'full_description' => '<p>An open-source developer tool written in Vue 3 and Tailwind CSS. It features an interactive UI playground with sliders for backdrop blur, opacity, border width, and background gradient configuration, instantly producing production-ready Tailwind utility classes.</p>',
            'category_id' => $toolCat->id,
            'cover_image_id' => $this->createDummyMedia('glassmorphism-cover.webp', 'Glassmorphism Generator Interface')->id,
            'cover_image_caption' => 'Interactive UI mockup editor',
            'role' => 'Frontend Developer',
            'key_features' => [
                ['title' => 'Interactive Playground', 'description' => 'Real-time custom CSS sliders that update component styling instantly in the browser.', 'icon' => 'Sparkles'],
                ['title' => 'Code Output', 'description' => 'Generates clean Tailwind CSS classes ready for copying and dropping into web projects.', 'icon' => 'Code'],
                ['title' => 'Lightweight Design', 'description' => 'Zero external styling dependencies, resulting in lightning-fast load times.', 'icon' => 'Rocket'],
            ],
            'status' => 'published',
            'is_featured' => false,
            'order' => 4,
            'live_url' => 'https://glassmorphism.example.com',
            'github_url' => 'https://github.com/example/tailwind-glassmorphism',
            'published_at' => now(),
        ]);
        $p4->technologies()->sync([$vuejs->id, $tailwindcss->id, $typescript->id]);
    }
}
