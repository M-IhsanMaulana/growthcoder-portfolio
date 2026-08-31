# GrowthCoder Portfolio & CMS

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Laravel](https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel&logoColor=white)](https://laravel.com)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-v3.0-9553E9?logo=inertia&logoColor=white)](https://inertiajs.com)
[![Nuxt](https://img.shields.io/badge/Nuxt-4.x-00DC82?logo=nuxt&logoColor=white)](https://nuxt.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?logo=vuedotjs&logoColor=white)](https://vuejs.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4.x-06B6D4?logo=tailwindcss&logoColor=white)](https://tailwindcss.com)

A modern, high-performance personal portfolio, developer blog, and headless content management system (CMS) built with a decoupled monorepo architecture: **Laravel 13 + Inertia.js v3** for the robust admin management dashboard and API, and **Nuxt 4 (SSR)** for the blazing-fast, SEO-optimized public website.

---

## 🏛️ Monorepo Architecture

```
growthcoder-portfolio/
├── backend/            # Laravel 13 Headless CMS & Admin Dashboard (Inertia.js v3 + Vue 3)
├── frontend/           # Nuxt 4 Public SSR Application (Nitro Engine + Vue 3)
├── docs/               # Production deployment guides
└── LICENSE             # MIT License
```

### 1. Backend & Admin CMS (`/backend`)
- **Framework**: Laravel 13, PHP 8.3+
- **Admin Dashboard**: Inertia.js v3, Vue 3, Tailwind CSS v4, Reka UI, Lucide Vue
- **Authentication**: Laravel Fortify & Passkeys
- **API Engine**: Eloquent API Resources with versioning (`/api/v1/`)
- **Image Processing**: Intervention Image v4 with automatic thumbnailing & webp conversion
- **Testing & Code Quality**: Pest v4, PHPUnit, Laravel Pint, Larastan

### 2. Public Frontend (`/frontend`)
- **Framework**: Nuxt 4 (Nitro SSR Engine), Vue 3
- **Styling & UI**: Tailwind CSS v4, PrimeVue / PrimeUI, Lucide Icons
- **Animation & Interactivity**: GSAP & VueUse
- **SEO & Performance**: Pre-rendered SSR meta tags, JSON-LD structured data, dynamic OpenGraph previews, and Core Web Vitals optimization

---

## ✨ Key Features

- 💼 **Project & Case Study Showcase**: Categorized portfolio items with detailed project overviews, challenges, solutions, and tech tags.
- 📝 **Developer Blog System**: Markdown/rich-text publishing, categories, read-time calculation, and SEO tags.
- 🖼️ **Media Library Manager**: Centralized asset upload and management with drag-and-drop support.
- 🛠️ **Tech Stack & Skills Matrix**: Grouped proficiencies (Frontend, Backend, DevOps, Tools).
- 🎓 **Education & Work Experience Timeline**: Structured chronology of academic background and career milestones.
- 📬 **Interactive Contact & Inbox**: Contact inquiries with real-time validation and admin inbox triage.
- ⚙️ **Global Site Settings**: Manage site metadata, branding, avatar, bio, and social profiles directly from the admin panel.

---

## 🚀 Getting Started

### Prerequisites
- **PHP**: `>= 8.3` (with `pdo`, `mbstring`, `openssl`, `gd` or `imagick`, `curl`)
- **Composer**: `>= 2.7`
- **Node.js**: `>= 20.x` & **npm**: `>= 10.x`
- **Database**: MySQL 8.0+ / MariaDB 10.5+

---

### Backend Setup (Admin & API)

1. Navigate to the backend directory:
   ```bash
   cd backend
   ```

2. Install PHP and Node dependencies:
   ```bash
   composer install
   npm install
   ```

3. Environment configuration:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database connection in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=growthcoder_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Run migrations and database seeders:
   ```bash
   php artisan migrate --seed
   ```

6. Create storage symbolic link:
   ```bash
   php artisan storage:link
   ```

7. Start the backend development servers:
   ```bash
   composer run dev
   # or run individually:
   # php artisan serve
   # npm run dev
   ```
   Admin panel will be accessible at: `http://localhost:8000`

---

### Frontend Setup (Public Website)

1. Navigate to the frontend directory:
   ```bash
   cd ../frontend
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Configure environment variables:
   ```bash
   cp .env.example .env
   ```
   Ensure `NUXT_PUBLIC_API_BASE` points to your Laravel API (e.g., `http://localhost:8000/api/v1`).

4. Start Nuxt development server:
   ```bash
   npm run dev
   ```
   Public frontend will be accessible at: `http://localhost:3000`

---

## 📖 Deployment Guide

For a step-by-step production deployment guide using **aaPanel on Ubuntu 24.04/22.04 LTS** (configuring Nginx reverse proxy, Node.js PM2 runtime, PHP 8.3-FPM, SSL, and background queues), please refer to:

- 📄 [`docs/deployment-aapanel-ubuntu.md`](docs/deployment-aapanel-ubuntu.md)

---

## 📄 License

This project is open-source software licensed under the [MIT License](LICENSE).
