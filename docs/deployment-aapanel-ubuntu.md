# Panduan Deployment Monorepo (Laravel + Nuxt) Menggunakan aaPanel di Ubuntu

Dokumen ini menjelaskan langkah-langkah lengkap untuk men-deploy aplikasi monorepo **GrowthCoder Portfolio** ke server Ubuntu menggunakan **aaPanel**.

Aplikasi ini menggunakan arsitektur monorepo:
1. **Backend (Laravel 13 + Inertia 3.0 + Vue 3)**: Berfungsi sebagai API & Admin CMS. Di-deploy sebagai PHP Site.
2. **Frontend (Nuxt 4)**: Berfungsi sebagai website publik SSR. Di-deploy sebagai Node.js Project (Reverse Proxy ke Port Node).

---

## 1. Persiapan Server & Software di aaPanel

Pastikan Anda telah menginstal aaPanel di OS Ubuntu Server Anda. Setelah masuk ke dasbor aaPanel, instal software berikut melalui **App Store**:

*   **Nginx** (Versi 1.22 atau terbaru)
*   **MySQL** (Versi 8.0)
*   **PHP-8.3**
    *   Setelah PHP terinstal, masuk ke **PHP-8.3 Settings** -> **Install extensions** dan instal:
        *   `fileinfo` (Wajib untuk upload media Laravel)
        *   `redis` (Wajib untuk queue dan caching)
        *   `opcache` (Opsional, disarankan untuk optimasi performa production)
    *   Masuk ke **PHP-8.3 Settings** -> **Disabled functions** dan hapus fungsi berikut agar Artisan command dapat berjalan lancar:
        *   `putenv`
        *   `proc_open`
        *   `proc_get_status`
        *   `symlink`
*   **Redis** (Versi terbaru dari App Store)
*   **Node.js Version Manager** (Pilih Node.js v18 atau v20 LTS)
*   **Supervisor Manager** (Untuk menjalankan Queue Worker Laravel secara background dan otomatis restart)

---

## 2. Kloning Source Code

Masuk ke terminal server Anda (bisa lewat aaPanel Terminal atau SSH biasa), lalu klon repositori monorepo Anda ke direktori `/www/wwwroot`:

```bash
cd /www/wwwroot
git clone https://github.com/username/growthcoder-portfolio.git growthcoder-portfolio
```

Pastikan struktur foldernya adalah:
- `/www/wwwroot/growthcoder-portfolio/backend`
- `/www/wwwroot/growthcoder-portfolio/frontend`

---

## 3. Konfigurasi Database

1.  Buka menu **Database** di aaPanel.
2.  Klik **Add Database**.
3.  Masukkan nama database dan username (contoh: `gcportfolio`).
4.  Salin password database yang digenerate oleh aaPanel.
5.  Klik **Submit**.

---

## 4. Deployment Backend (Laravel 13 CMS)

### A. Tambahkan PHP Site di aaPanel
1.  Buka menu **Website** -> **PHP project** -> klik **Add site**.
2.  **Domain**: Masukkan domain untuk admin CMS (contoh: `cms.growthcoder.id`).
3.  **Site Directory**: Pilih `/www/wwwroot/growthcoder-portfolio/backend`.
4.  **URL rewrite**: Pilih `laravel` dari dropdown.
5.  **PHP Version**: Pilih `PHP-8.3`.
6.  Klik **Submit**.

### B. Konfigurasi Document Root
1.  Setelah situs dibuat, klik nama website tersebut untuk membuka pengaturan.
2.  Masuk ke menu **Site directory**.
3.  Ubah **Running directory** menjadi `/public`.
4.  Klik **Save**.

### C. Konfigurasi File Environment (.env)
1.  Buka menu **Files** di aaPanel, navigasikan ke `/www/wwwroot/growthcoder-portfolio/backend`.
2.  Rename file `.env.example` menjadi `.env` (atau buat file `.env` baru).
3.  Edit file `.env` dan sesuaikan nilainya:
    ```env
    APP_NAME="GrowthCoder CMS"
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://cms.growthcoder.id  # URL CMS Anda

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=gcportfolio            # Sesuai database aaPanel
    DB_USERNAME=gcportfolio            # Sesuai username aaPanel
    DB_PASSWORD=password_database_anda # Sesuai password aaPanel

    QUEUE_CONNECTION=redis
    CACHE_STORE=redis
    REDIS_HOST=127.0.0.1
    REDIS_PORT=6379

    # Telegram Notification (jika digunakan)
    TELEGRAM_BOT_TOKEN=token_bot_anda
    TELEGRAM_CHAT_ID=chat_id_anda
    ```

### D. Menjalankan Instalasi Backend
Masuk ke terminal server di direktori backend:

```bash
cd /www/wwwroot/growthcoder-portfolio/backend
```

1.  **Instal PHP Dependencies**:
    ```bash
    composer install --no-dev --optimize-autoloader
    ```
2.  **Generate Application Key**:
    ```bash
    php artisan key:generate
    ```
3.  **Jalankan Database Migration & Seeders**:
    ```bash
    php artisan migrate --force
    # Jika instalasi pertama kali dan butuh seeder data awal:
    # php artisan db:seed --force
    ```
4.  **Membuat Symbolic Link untuk Storage**:
    ```bash
    php artisan storage:link
    ```
5.  **Optimasi Config & Route Cache**:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

### E. Compile Asset Backend (Vite + Inertia Vue)
Karena backend Laravel menggunakan Inertia.js dan Vue 3, Anda perlu melakukan kompilasi file frontend admin:

1.  Instal Node dependencies di folder backend:
    ```bash
    npm install
    ```
2.  Jalankan build asset:
    ```bash
    npm run build
    ```

### F. Atur Izin Folder (Permission)
Kembali ke terminal, jalankan perintah berikut untuk memastikan web server (`www`) dapat membaca dan menulis ke folder cache & storage Laravel:

```bash
chown -R www:www /www/wwwroot/growthcoder-portfolio/backend/storage
chown -R www:www /www/wwwroot/growthcoder-portfolio/backend/bootstrap/cache
chmod -R 775 /www/wwwroot/growthcoder-portfolio/backend/storage
chmod -R 775 /www/wwwroot/growthcoder-portfolio/backend/bootstrap/cache
```

### G. Konfigurasi SSL untuk Backend
1.  Di menu **Website** aaPanel, klik situs `cms.growthcoder.id`.
2.  Masuk ke menu **SSL**.
3.  Pilih tab **Let's Encrypt**.
4.  Centang domain Anda, lalu klik **Apply**.
5.  Nyalakan opsi **Force HTTPS** setelah SSL berhasil dipasang.

---

## 5. Deployment Frontend (Nuxt 4 SSR)

Aplikasi frontend dibangun menggunakan **Nuxt 4** dan membutuhkan Node.js server yang terus berjalan. Kita akan mengkompilasi aplikasi dan menjalankannya menggunakan Node.js Project manager aaPanel.

### A. Compile Nuxt Frontend
Masuk ke direktori frontend melalui terminal:

```bash
cd /www/wwwroot/growthcoder-portfolio/frontend
```

1.  **Instal Node Dependencies**:
    ```bash
    npm install
    ```
2.  **Build Aplikasi untuk Production**:
    ```bash
    npm run build
    ```
    *Proses ini akan menghasilkan direktori `.output` di dalam `/frontend` yang berisi server web mandiri.*

### B. Konfigurasi Node Project di aaPanel
1.  Buka menu **Website** -> tab **Node project** -> klik **Add Node Project** (atau gunakan **PM2 Manager** dari App Store jika versi aaPanel Anda belum mendukung tab Node Project secara native).
2.  Isi konfigurasi proyek:
    *   **Path**: Pilih file `/www/wwwroot/growthcoder-portfolio/frontend/.output/server/index.mjs`.
    *   **Project Name**: `growthcoder-frontend`
    *   **Port**: `3000` (atau port kosong lainnya, misal `3005`)
    *   **Run Command**: `node /www/wwwroot/growthcoder-portfolio/frontend/.output/server/index.mjs`
    *   **Environment Variables**: Tambahkan variabel berikut untuk menghubungkan frontend ke API backend Laravel Anda:
        *   `PORT=3000`
        *   `NODE_ENV=production`
        *   `NUXT_PUBLIC_API_BASE=https://cms.growthcoder.id/api/v1`
        *   `NUXT_PUBLIC_API_KEY=API_KEY_YANG_SAMA_DENGAN_SETTING_CMS`
3.  Klik **Submit**. Proyek Node akan mulai berjalan dan memonitor port `3000`.

### C. Konfigurasi Domain Utama & Reverse Proxy
Sekarang kita perlu memetakan domain utama Anda (misalnya `growthcoder.id`) agar mengarah ke port internal Node (`3000`).

1.  Di tab **Node project**, klik tombol **Web service / Map** pada baris project `growthcoder-frontend`.
2.  Masukkan domain utama Anda (contoh: `growthcoder.id` dan `www.growthcoder.id`).
3.  aaPanel akan otomatis membuat entri di Nginx sebagai Reverse Proxy yang mengarah ke `http://127.0.0.1:3000`.
4.  Buka menu **Website** -> **PHP project** (situs map proxy ini akan muncul di sini).
5.  Klik nama domain tersebut, masuk ke menu **SSL**, pasang **Let's Encrypt** dan aktifkan **Force HTTPS**.

---

## 6. Konfigurasi Services Pendukung (Queue & Scheduler)

Untuk memastikan notifikasi Telegram dan background job Laravel berjalan otomatis di server, Anda perlu mengonfigurasi Scheduler dan Supervisor.

### A. Laravel Scheduler (Cron Job)
1.  Buka menu **Cron** di aaPanel.
2.  **Type of Task**: Pilih **Shell Script**.
3.  **Name of Task**: `Laravel Scheduler - GrowthCoder`.
4.  **Execution Cycle**: Pilih **N Minutes** -> `1` Minute.
5.  **Script Content**:
    ```bash
    cd /www/wwwroot/growthcoder-portfolio/backend && php artisan schedule:run >> /dev/null 2>&1
    ```
6.  Klik **Add Task**.

### B. Laravel Queue Worker (Supervisor)
Notifikasi Telegram diproses secara asynchronous menggunakan Redis queue. Kita perlu mengaktifkan Supervisor untuk memprosesnya secara real-time.

1.  Buka aplikasi **Supervisor Manager** dari halaman App Store / Installed apps aaPanel.
2.  Klik **Add Daemon**.
3.  **Name**: `laravel-worker`.
4.  **Run User**: `www`.
5.  **Run Dir**: `/www/wwwroot/growthcoder-portfolio/backend`.
6.  **Start Command**: `php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600`.
7.  **Processes**: `1` (atau sesuaikan dengan kebutuhan spesifikasi server Anda).
8.  Klik **Confirm**.
9.  Pastikan status daemon berubah menjadi **Running / Green**.

---

## 7. Pemeliharaan dan Update (Maintenance Checklist)

Setiap kali Anda melakukan update code dari Git (misalnya setelah push fitur baru):

### Update Backend:
```bash
cd /www/wwwroot/growthcoder-portfolio/backend
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
npm install
npm run build
chown -R www:www storage bootstrap/cache
```

### Update Frontend:
```bash
cd /www/wwwroot/growthcoder-portfolio/frontend
git pull origin main
npm install
npm run build
```
Setelah selesai mem-build Nuxt, buka **Website** -> **Node project** di aaPanel dan klik **Restart** pada project frontend Anda untuk menerapkan perubahan server-side.
