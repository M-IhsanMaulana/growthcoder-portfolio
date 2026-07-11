<?php

namespace Database\Seeders;

use App\Enums\TechnologyCategory;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    use CreatesMedia;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing records safely
        // To avoid foreign key constraints during truncate/delete, we delete dependencies first or simply delete technologies
        // (the DB runs migrations, so we can clean dependencies in our seeders as well)
        Technology::query()->delete();

        $techs = [
            // Frontend
            [
                'name' => 'Vue.js',
                'slug' => 'vuejs',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://vuejs.org',
                'description' => 'The Progressive JavaScript Framework for building user interfaces.',
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://developer.mozilla.org/en-US/docs/Web/JavaScript',
                'description' => 'JavaScript is a lightweight, interpreted, or just-in-time compiled programming language with first-class functions.',
            ],
            [
                'name' => 'Nuxt',
                'slug' => 'nuxt',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://nuxt.com',
                'description' => 'The Intuitive Vue Framework for building production-grade web applications.',
            ],
            [
                'name' => 'Tailwind CSS',
                'slug' => 'tailwindcss',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://tailwindcss.com',
                'description' => 'A utility-first CSS framework for rapid UI development.',
            ],
            [
                'name' => 'TypeScript',
                'slug' => 'typescript',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://www.typescriptlang.org',
                'description' => 'TypeScript is a strongly typed programming language that builds on JavaScript.',
            ],
            [
                'name' => 'React.js',
                'slug' => 'react',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => false,
                'url' => 'https://react.dev',
                'description' => 'The library for web and native user interfaces.',
            ],
            [
                'name' => 'HTML5',
                'slug' => 'html5',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://developer.mozilla.org/en-US/docs/Web/HTML',
                'description' => 'HyperText Markup Language, the standard markup language for documents designed to be displayed in a web browser.',
            ],
            [
                'name' => 'CSS',
                'slug' => 'css',
                'category' => TechnologyCategory::Frontend,
                'is_featured' => true,
                'url' => 'https://developer.mozilla.org/en-US/docs/Web/CSS',
                'description' => 'Cascading Style Sheets is a style sheet language used for describing the presentation of a document written in a markup language.',
            ],

            // Backend
            [
                'name' => 'PHP',
                'slug' => 'php',
                'category' => TechnologyCategory::Backend,
                'is_featured' => true,
                'url' => 'https://www.php.net',
                'description' => 'A popular general-purpose scripting language that is especially suited to web development.',
            ],
            [
                'name' => 'Laravel',
                'slug' => 'laravel',
                'category' => TechnologyCategory::Backend,
                'is_featured' => true,
                'url' => 'https://laravel.com',
                'description' => 'The PHP Web Application Framework for Web Artisans.',
            ],
            [
                'name' => 'Node.js',
                'slug' => 'nodejs',
                'category' => TechnologyCategory::Backend,
                'is_featured' => true,
                'url' => 'https://nodejs.org',
                'description' => 'Node.js® is a free, open-source, cross-platform JavaScript runtime environment.',
            ],
            [
                'name' => 'Express.js',
                'slug' => 'expressjs',
                'category' => TechnologyCategory::Backend,
                'is_featured' => false,
                'url' => 'https://expressjs.com',
                'description' => 'Fast, unopinionated, minimalist web framework for Node.js.',
            ],

            // Database
            [
                'name' => 'PostgreSQL',
                'slug' => 'postgresql',
                'category' => TechnologyCategory::Database,
                'is_featured' => true,
                'url' => 'https://www.postgresql.org',
                'description' => 'A powerful, open-source object-relational database system.',
            ],
            [
                'name' => 'MySQL',
                'slug' => 'mysql',
                'category' => TechnologyCategory::Database,
                'is_featured' => false,
                'url' => 'https://www.mysql.com',
                'description' => 'MySQL is the world\'s most popular open-source database.',
            ],
            [
                'name' => 'Redis',
                'slug' => 'redis',
                'category' => TechnologyCategory::Database,
                'is_featured' => true,
                'url' => 'https://redis.io',
                'description' => 'The open source, in-memory data store used by millions of developers.',
            ],
            [
                'name' => 'MongoDB',
                'slug' => 'mongodb',
                'category' => TechnologyCategory::Database,
                'is_featured' => false,
                'url' => 'https://www.mongodb.com',
                'description' => 'MongoDB is a source-available cross-platform document-oriented database program.',
            ],

            // DevOps
            [
                'name' => 'Docker',
                'slug' => 'docker',
                'category' => TechnologyCategory::DevOps,
                'is_featured' => true,
                'url' => 'https://www.docker.com',
                'description' => 'Docker helps developers build, share, run, and verify applications in containers.',
            ],
            [
                'name' => 'Nginx',
                'slug' => 'nginx',
                'category' => TechnologyCategory::DevOps,
                'is_featured' => false,
                'url' => 'https://nginx.org',
                'description' => 'Nginx is an HTTP and reverse proxy server, a mail proxy server, and a generic TCP/UDP proxy server.',
            ],
            [
                'name' => 'GitHub Actions',
                'slug' => 'github-actions',
                'category' => TechnologyCategory::DevOps,
                'is_featured' => true,
                'url' => 'https://github.com/features/actions',
                'description' => 'Automate, customize, and execute your software development workflows right in your repository.',
            ],

            // Tools
            [
                'name' => 'Git',
                'slug' => 'git',
                'category' => TechnologyCategory::Tools,
                'is_featured' => true,
                'url' => 'https://git-scm.com',
                'description' => 'Git is a free and open source distributed version control system.',
            ],
            [
                'name' => 'Postman',
                'slug' => 'postman',
                'category' => TechnologyCategory::Tools,
                'is_featured' => false,
                'url' => 'https://www.postman.com',
                'description' => 'Postman is an API platform for building and using APIs.',
            ],
            [
                'name' => 'Linux',
                'slug' => 'linux',
                'category' => TechnologyCategory::Tools,
                'is_featured' => true,
                'url' => 'https://www.linux.org',
                'description' => 'Linux is a family of open-source Unix-like operating systems based on the Linux kernel.',
            ],
        ];

        foreach ($techs as $tech) {
            $logo = $this->createDummyMedia($tech['slug'].'-logo.png', $tech['name'].' Logo');
            Technology::create(array_merge($tech, [
                'logo_media_id' => $logo->id,
            ]));
        }
    }
}
