<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    use CreatesMedia;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Safe clear
        DB::table('post_views')->delete();
        DB::table('post_related')->delete();
        DB::table('post_category')->delete();
        Post::query()->delete();

        // Get categories
        $programming = Category::where('slug', 'programming')->first();
        $webDev = Category::where('slug', 'web-development')->first();
        $automation = Category::where('slug', 'automation')->first();
        $career = Category::where('slug', 'career-and-tips')->first();

        // Seed posts
        // Post 1
        $p1 = Post::create([
            'title' => 'Building a Scalable Telegram Bot with Node.js and Redis',
            'slug' => 'building-scalable-telegram-bot-nodejs-redis',
            'excerpt' => 'Learn how to use Redis queueing and connection pooling to build a Telegram bot capable of handling thousands of concurrent requests.',
            'content' => '<h2>Introduction</h2><p>Telegram bots are easy to build, but scaling them to handle thousands of messages per minute requires a sound architecture. In this tutorial, we will walk through setting up a Node.js Telegram bot that delegates heavy tasks to a Redis queue.</p><h3>Why Redis?</h3><p>Redis is incredibly fast and provides robust data structures like lists and sets that are perfect for task queuing. By offloading tasks from the main bot polling thread, we ensure the bot remains highly responsive to user commands.</p>',
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(10),
            'cover_image_id' => $this->createDummyMedia('telegram-bot-post.webp', 'Telegram Bot Architecture Diagram')->id,
            'meta_title' => 'Scalable Telegram Bot with Node.js & Redis',
            'meta_description' => 'Step-by-step tutorial on building a high-performance Telegram bot utilizing Node.js, Express, and Redis task queues.',
        ]);
        $p1->categories()->sync([$programming->id, $automation->id]);

        // Seed views for post 1
        for ($i = 0; $i < 45; $i++) {
            $p1->views()->create([
                'ip_hash' => md5('ip-'.$i),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'device' => 'desktop',
                'referrer' => 'google.com',
            ]);
        }

        // Post 2
        $p2 = Post::create([
            'title' => 'Why We Migrated to Nuxt 4 for Our Public Frontend',
            'slug' => 'why-migrated-to-nuxt-4-public-frontend',
            'excerpt' => 'A deep dive into Nuxt 4\'s new features, SSR performance gains, directory structures, and why it is a game changer for SEO.',
            'content' => '<h2>The Transition</h2><p>Nuxt 4 introduces a modern app structure, improved Nitro engine caching, and out-of-the-box performance optimizations. In this article, we explain how migrating our public portfolio website resulted in a 40% speed increase and improved Core Web Vitals.</p><h3>Major Highlights</h3><ul><li>New application directory layouts (`app/` namespace)</li><li>Smarter data fetching with advanced caching</li><li>Built-in compatibility with Vue 3 features</li></ul>',
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(5),
            'cover_image_id' => $this->createDummyMedia('nuxt-post.webp', 'Nuxt 4 Logo and Speed Dashboard')->id,
            'meta_title' => 'Migrating to Nuxt 4: Benefits and SEO Impact',
            'meta_description' => 'Discover the performance enhancements, SEO improvements, and code cleanliness when migrating to Nuxt 4.',
        ]);
        $p2->categories()->sync([$webDev->id]);

        // Seed views for post 2
        for ($i = 0; $i < 80; $i++) {
            $p2->views()->create([
                'ip_hash' => md5('ip2-'.$i),
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                'device' => 'mobile',
                'referrer' => 'linkedin.com',
            ]);
        }

        // Post 3
        $p3 = Post::create([
            'title' => 'Mastering Eloquent Relationships in Laravel',
            'slug' => 'mastering-eloquent-relationships-laravel',
            'excerpt' => 'Get a clear understanding of advanced relationship techniques in Laravel Eloquent, including polymorphic relations and lazy loading optimization.',
            'content' => '<h2>Unlocking Eloquent</h2><p>Eloquent is one of Laravel\'s strongest pillars. However, database performance can drop quickly due to N+1 query problems. Learn how to leverage eager loading, polymorphic relations, and advanced constraints to write clean, optimized queries.</p>',
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(2),
            'cover_image_id' => $this->createDummyMedia('laravel-eloquent-post.webp', 'Laravel Database Schema Pattern')->id,
            'meta_title' => 'Advanced Laravel Eloquent Relationships Guide',
            'meta_description' => 'Optimize your Laravel database interactions by mastering eager loading, polymorphic relations, and performance query tuning.',
        ]);
        $p3->categories()->sync([$programming->id, $webDev->id]);

        // Seed views for post 3
        for ($i = 0; $i < 30; $i++) {
            $p3->views()->create([
                'ip_hash' => md5('ip3-'.$i),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'device' => 'desktop',
                'referrer' => 'direct',
            ]);
        }

        // Post 4
        $p4 = Post::create([
            'title' => 'How to Setup Docker for Laravel and Vue Development',
            'slug' => 'setup-docker-laravel-vue-development',
            'excerpt' => 'Step-by-step guide to containerizing your local Laravel backend and Vue frontend with hot reloading enabled.',
            'content' => '<h2>Introduction to Containerization</h2><p>Setting up local development environments across multiple team members can be challenging. Docker ensures everyone runs the exact same software stack. We will set up a PHP 8.3 FPM service, Nginx, PostgreSQL, Redis, and a Node container for hot-reloading Vue components.</p>',
            'status' => PostStatus::Published,
            'published_at' => now()->subDay(),
            'cover_image_id' => $this->createDummyMedia('docker-post.webp', 'Docker Containers Graphic')->id,
            'meta_title' => 'Dockerize Laravel & Vue Development Environment',
            'meta_description' => 'A complete docker-compose guide for containerizing Laravel API and Vue 3 frontend projects for local development.',
        ]);
        $p4->categories()->sync([$webDev->id]);

        // Seed views for post 4
        for ($i = 0; $i < 15; $i++) {
            $p4->views()->create([
                'ip_hash' => md5('ip4-'.$i),
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'device' => 'desktop',
                'referrer' => 'google.com',
            ]);
        }

        // Post 5
        $p5 = Post::create([
            'title' => 'My Journey: From Self-Taught to Full-Stack Developer',
            'slug' => 'journey-self-taught-to-full-stack-developer',
            'excerpt' => 'Sharing the resources, habits, and mindset shifts that helped me transition into a professional software engineer.',
            'content' => '<h2>The Beginning</h2><p>Learning to code can feel overwhelming. With so many libraries, languages, and opinions, where do you start? In this personal log, I map out my self-taught path, from basic HTML/CSS files to managing complex production cloud deployments, highlighting the key resources that made the difference.</p>',
            'status' => PostStatus::Draft,
            'cover_image_id' => $this->createDummyMedia('journey-post.webp', 'Developer workspace with multiple screens')->id,
            'meta_title' => 'My Self-Taught Full-Stack Developer Journey',
            'meta_description' => 'A transparent guide and checklist detailing my journey to becoming a full-stack web developer from scratch.',
        ]);
        $p5->categories()->sync([$career->id]);

        // Setup related posts relations
        $p1->relatedPosts()->sync([$p2->id, $p4->id]);
        $p2->relatedPosts()->sync([$p1->id]);
        $p3->relatedPosts()->sync([$p4->id]);
        $p4->relatedPosts()->sync([$p3->id]);
    }
}
