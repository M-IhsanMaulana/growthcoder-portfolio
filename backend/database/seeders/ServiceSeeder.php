<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Service::query()->delete();

        $services = [
            [
                'title' => 'Full-Stack Web Development',
                'slug' => 'full-stack-web-development',
                'short_description' => 'End-to-end custom web applications built with modern architectures for maximum speed, security, and scalability.',
                'long_description' => '<p>I design and build dynamic web applications tailored specifically to your business requirements. Using standard workflows and modern tech stacks like Laravel, Vue 3, and Nuxt, I handle both the backend logic and frontend interactivity.</p><p>Features include responsive layouts, role-based access control, state management, and real-time features.</p>',
                'icon' => IconHelper::getSvg('Code'),
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'API Design & Integration',
                'slug' => 'api-design-integration',
                'short_description' => 'Robust, documented RESTful APIs and integration of third-party platforms like payment gateways and CRM systems.',
                'long_description' => '<p>Connecting systems together is critical for business efficiency. I build clean, RESTful APIs according to OpenAPI specifications, secure them with JWT/OAuth or Sanctum, and integrate external services such as Xendit/Midtrans for payments, mail clients, and CRM platforms.</p>',
                'icon' => IconHelper::getSvg('Server'),
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Telegram Bot Ecosystems',
                'slug' => 'telegram-bot-ecosystems',
                'short_description' => 'Custom Telegram bots for automated notifications, billing systems, customer interactions, and community management.',
                'long_description' => '<p>Telegram is a powerful channel for automation. I create custom, responsive bots that integrate with your databases and external webhooks to manage customer inquiries, generate invoice links, send system alerts, or manage user group permissions dynamically.</p>',
                'icon' => IconHelper::getSvg('Bot'),
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'DevOps & Automation',
                'slug' => 'devops-automation',
                'short_description' => 'Containerization of applications, automated deployment setups (CI/CD), and script scripting for routine server operations.',
                'long_description' => '<p>Ensure fast delivery and minimal downtime. I write Docker container definitions, configure Nginx reverse proxies, setup SSL certificates, and build CI/CD automation pipelines using GitHub Actions to deploy applications automatically to VPS or cloud services.</p>',
                'icon' => IconHelper::getSvg('Zap'),
                'is_active' => true,
                'order' => 4,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
