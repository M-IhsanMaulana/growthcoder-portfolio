# Rencana Langkah Implementasi — Nuxt 4 Frontend

Dokumen ini mendefinisikan langkah-langkah sistematis untuk mengimplementasikan **Public Frontend** berbasis **Nuxt 4** pada platform **growthcoder.id**. Seluruh langkah diselaraskan dengan backend API Laravel 13 yang sudah siap dikonsumsi.

---

## 1. Persiapan & Konfigurasi Dasar

Sebelum mulai menulis komponen halaman, kita perlu mengonfigurasi environment dan dependencies agar siap pakai.

### A. Environment Variables (`.env`)
Buat file `frontend/.env` untuk menyimpan URL API backend:
```env
# URL API Laravel Backend
NUXT_PUBLIC_API_BASE=http://localhost:8000/api/v1
# Mode lingkungan
NODE_ENV=development
```

### B. Konfigurasi `nuxt.config.ts`
Sesuaikan konfigurasi Nuxt 4 untuk mendukung rendering SSR, setup module gambar, optimasi font, dan konfigurasi head SEO global.
* Memasang modul pendukung yang direkomendasikan:
  * `@nuxt/image` (Optimasi gambar otomatis)
  * `@nuxt/fonts` (Pemuatan font berkinerja tinggi tanpa FOIT)
  * `@vueuse/nuxt` (Reactive utilities)
  * Ikon Lucide (`lucide-vue-next`)
  * `@hypernym/nuxt-gsap` (Modul integrasi GSAP untuk animasi premium)
* Menambahkan konfigurasi modul GSAP pada `nuxt.config.ts`:
  ```typescript
  export default defineNuxtConfig({
    modules: [
      '@nuxt/image',
      '@nuxt/fonts',
      '@vueuse/nuxt',
      '@hypernym/nuxt-gsap'
    ],
    gsap: {
      extraPlugins: {
        scrollTrigger: true,
        motionPath: true
      }
    }
  })
  ```


### C. Struktur Folder Nuxt 4
Struktur folder di dalam `/frontend` akan dirapikan menjadi:
```
frontend/
├── app/
│   ├── app.vue                # Entrypoint aplikasi utama
│   ├── router.options.ts      # Kustomisasi router jika diperlukan
│   ├── assets/
│   │   └── css/
│   │       └── main.css       # File CSS global dengan CSS Variables & Reset
│   ├── components/            # Komponen Vue reusable
│   │   ├── global/            # Komponen yang auto-imported secara global
│   │   ├── ui/                # Komponen dasar (Button, Card, Input, dll.)
│   │   └── sections/          # Komponen layout per bagian (Navbar, Footer, Hero)
│   ├── composables/           # Reactivity helpers (useSettings, useFetchAPI)
│   ├── layouts/               # Master layouts (default.vue)
│   ├── pages/                 # File-based routing pages
│   └── public/                # Aset statis (robots.txt, favicon.ico)
└── server/
    └── routes/
        └── sitemap.xml.ts     # Dynamic sitemap generation
```

---

## 2. Tahap 1: Setup Layout & Integrasi Global

Langkah awal adalah memastikan navigasi utama, footer, dan data global terintegrasi dengan baik.

### A. Global Stylesheet (`app/assets/css/main.css`)
* Menetapkan CSS Variables untuk tema warna (Light & Dark mode) yang merujuk pada dokumentasi warna CMS.
* Mengatur CSS Reset dan tipografi dasar menggunakan Google Font **Instrument Sans** atau **Outfit**.

### B. Composable Fetching API (`app/composables/useFetchAPI.ts`)
* Membuat utility pembungkus `useFetch` bawaan Nuxt agar secara otomatis menambahkan header `Accept: application/json` dan membaca base URL dari `runtimeConfig.public.apiBase`.

### C. Composable Site Settings (`app/composables/useSettings.ts`)
* Mengambil data global setting via `GET /settings` sekali saja di layout tingkat atas, menyimpan datanya menggunakan `useState` agar bisa diakses oleh komponen Navbar, Footer, dan Hero secara instant tanpa fetch berulang.

### D. Default Layout (`app/layouts/default.vue`)
* Membuat kontainer utama dengan Header/Navbar transparan ber-blur (glassmorphism).
* Membuat Footer dinamis dengan link sosial media dari Site Settings.
* Menyediakan toggle *Theme Mode* (Light/Dark).

### E. Splash Screen & Animasi GSAP Global (`app/components/ui/SplashScreen.vue` & `app.vue`)
* **Komponen Splash Screen (`SplashScreen.vue`)**:
  * Menampilkan overlay layar penuh (`fixed inset-0 z-50 bg-brand-navy`) dengan logo/teks brand `growthcoder.id` di bagian tengah.
  * Menggunakan GSAP timeline untuk menggambar/memudarkan teks dengan efek stagger (`stagger: 0.05`), lalu menganimasikan overlay tersebut meluncur ke atas (`translate-y` atau clip-path) hingga hilang.
* **Integrasi `app.vue`**:
  * Menghidupkan `SplashScreen` pada event `onMounted()` untuk menghindari isu hydration (client-side only).
  * Menggunakan state global `isLoaded` via `useState` agar splash screen intro penuh hanya diputar satu kali per sesi kunjungan awal.
* **Animasi Transisi Halaman (Setiap Load / Pindah Halaman)**:
  * Mengintegrasikan transisi antar halaman (Nuxt Page Transition) berbasis GSAP.
  * Menyediakan loading indicator dinamis (mis. top progress bar atau overlay transisi cepat) yang dipicu saat perpindahan rute (menggunakan lifecycle router hook `beforeEach` dan `afterEach` di plugin Nuxt).


---

## 3. Tahap 2: Implementasi Halaman Utama (Homepage)

Halaman Beranda (`app/pages/index.vue`) dirancang sebagai landing page terintegrasi yang interaktif dengan animasi premium bertenaga **GSAP**.

### A. Hero Section
* Menampilkan foto profil, headline, sub-headline, dan tombol CTA.
* Mengintegrasikan link download CV PDF secara dinamis dari API settings.
* **GSAP Animation**: Animasi masuk (intro) menggunakan timeline GSAP untuk stagger-reveal pada avatar, headline, sub-headline, dan tombol CTA (arah pergeseran dari bawah ke atas dengan efek easing `power3.out`).

### B. Featured Tech Stack Section
* Mengambil data via `GET /technologies?featured=1`.
* Menampilkan grid logo teknologi dengan efek hover interaktif.
* **GSAP ScrollTrigger**: Menggunakan ScrollTrigger untuk mendeteksi elemen masuk viewport, lalu merender logo secara stagger-reveal dari skala `0.5` ke `1` dengan rotasi kecil untuk menambah kesan organik.

### C. Services Highlight Section
* Mengambil data via `GET /services`.
* Menampilkan kartu-kartu layanan (maksimal 4) dengan tata letak menarik dan micro-animations.
* **GSAP ScrollTrigger**: Menampilkan kartu layanan secara berurutan (stagger) dari kiri ke kanan (atau bawah ke atas) dengan kemiringan (skew) tipis yang mengendur secara halus (`elastic.out`).

### D. Featured Projects Section
* Mengambil data via `GET /projects?featured=1` (limit 3).
* Menampilkan studi kasus utama dalam format grid card dengan info kategori, ringkasan, dan badge tech stack.
* **GSAP ScrollTrigger**: Menggerakkan kartu proyek dari samping (slide-in) saat pengguna scroll ke bawah dengan properti `scrub` ringan atau snap trigger sederhana.

### E. Latest Blog Section
* Mengambil data via `GET /posts` (limit 3, terurut by tanggal rilis terbaru).
* Menampilkan preview artikel blog terbaru dengan estimasi waktu baca.
* **GSAP ScrollTrigger**: Animasi fade-in halus pada baris grid artikel.

### F. Contact Form Section
* Implementasi form kontak (Nama, Email, Pesan).
* Mengirimkan data via `POST /contact` ke API Laravel.
* Menangani state loading, pesan sukses, error validasi, proteksi honeypot, dan rate limit.
* **GSAP Micro-Interactions**: Menggunakan GSAP untuk efek getar halus (shake) pada tombol kirim jika ada error validasi, dan transisi smooth untuk pesan sukses/gagal.


---

## 4. Tahap 3: Halaman Resume & Riwayat (`app/pages/about.vue`)

Halaman ini berfokus menyajikan rekam jejak akademis, profesional, dan skill set lengkap dengan transisi interaktif.

### A. Profil Naratif & CV
* Menampilkan foto profil besar, deskripsi biodata profesional, dan statistik singkat.
* **GSAP Animation**: Slide-in dari samping untuk profil naratif dan stagger-fade-in untuk badge-badge statistik singkat pada pemuatan halaman.

### B. Timeline Pendidikan & Pengalaman
* Mengambil data via `GET /educations` dan `GET /experiences`.
* Menggabungkan atau memilah data secara kronologis (dari tahun terbaru ke terlama).
* Menyajikan timeline interaktif dengan logo instansi yang diambil dari Media Library.
* **GSAP ScrollTrigger**: Menggunakan ScrollTrigger untuk menggambar garis vertikal timeline (`scaleY: 0` ke `scaleY: 1` seiring scroll), serta stagger-fade-in untuk setiap entri timeline (pendidikan/pengalaman) yang muncul dari sisi kiri/kanan.

### C. Skills Board (Papan Keahlian)
* Mengambil data via `GET /skills`.
* Mengelompokkan skill berdasarkan kategori teknologi (Frontend, Backend, DevOps, Database, dll.).
* Menampilkan progress bar kemahiran (0-100%) dan estimasi tahun pengalaman secara estetis.
* **GSAP ScrollTrigger**: Memicu animasi pengisian progress bar horizontal dari `0%` ke persentase target secara dinamis ketika section skill mulai terlihat di layar.


---

## 5. Tahap 4: Modul Studi Kasus Proyek (`app/pages/proyek`)

Menampilkan showcase portofolio dengan navigasi studi kasus yang mendalam.

### A. Listing Page (`app/pages/proyek/index.vue`)
* Mengambil kategori proyek via `GET /project-categories`.
* Mengambil daftar proyek via `GET /projects`.
* Menyediakan bilah filter kategori dinamis tanpa reload halaman. Jika filter diklik, URL ditambahkan query parameter (`?category=slug`) agar tetap crawlable, dan data diperbarui secara asinkronus.

### B. Detail Page (`app/pages/proyek/[slug].vue`)
* Mengambil detail studi kasus via `GET /projects/{slug}` berdasarkan parameter slug.
* **Galeri Gambar:** Grid gambar interaktif yang jika diklik akan membuka lightbox untuk resolusi penuh.
* **Tautan Aksi:** Tombol akses langsung "Live Demo", "GitHub Repository", atau "Telegram Bot URL".
* **Rich Content Render:** Merender narasi studi kasus yang aman dengan tag `v-html`, dilengkapi style CSS khusus (`.prose` / `.ck-content`) agar format paragraf, list, heading, dan blockquote tertata rapi.
* **Diagram Arsitektur:** Menampilkan gambar arsitektur sistem di bagian penjelas teknis.
* **Rekomendasi Proyek:** Slider/list proyek terkait di bagian bawah halaman.

---

## 6. Tahap 5: Modul Blog & Artikel (`app/pages/blog`)

Mesin pertumbuhan SEO untuk publikasi artikel teknis.

### A. Listing Page (`app/pages/blog/index.vue`)
* Mengambil kategori blog via `GET /blog-categories`.
* Mengambil seluruh post via `GET /posts`.
* Menyediakan navigasi kategori berbentuk pill/chip yang mengubah rute ke `/blog/kategori/[slug]`.

### B. Kategori Page (`app/pages/blog/kategori/[slug].vue`)
* Halaman khusus SEO-friendly yang menyajikan daftar artikel yang difilter berdasarkan slug kategori tertentu.

### C. Detail Page (`app/pages/blog/[slug].vue`)
* Mengambil data artikel via `GET /posts/{slug}`.
* Menampilkan header artikel lengkap dengan tanggal rilis, kategori, dan estimasi waktu baca.
* Render konten blog dengan styling tipografi optimal (font size, line height nyaman dibaca, code block dengan styling syntax highlighting).
* Menyajikan "Artikel Terkait" (Related Posts) di akhir artikel untuk meningkatkan *dwell time* pengunjung.

---

## 7. Tahap 6: Optimalisasi SEO & Performa (Wajib)

Tahap pemolesan non-fungsional agar web ramah search engine dan berkinerja tinggi.

### A. SSR Verification
* Memastikan semua fetch data utama menggunakan `useAsyncData` atau `useFetch` di server-side, sehingga hasil *View Source* di browser memuat seluruh HTML tulisan, bukan script kosong.

### B. useSeoMeta & Canonical Tags
* Menambahkan composable meta SEO di setiap halaman dinamis untuk menyuntikkan Meta Title, Meta Description, Open Graph (OG) Tags, dan Twitter Card.
* Menyisipkan tag canonical URL (`<link rel="canonical">`) di header setiap halaman.

### C. JSON-LD Structured Data
* **Halaman Beranda:** Menyuntikkan skema `Person` (menautkan data diri dan media sosial).
* **Halaman Detail Blog:** Menyuntikkan skema `BlogPosting` (penulis, tanggal publis, cover gambar).
* **Halaman Detail Proyek:** Menyuntikkan skema `CreativeWork`.

### D. Dynamic Sitemap (`server/routes/sitemap.xml.ts`)
* Membuat route server untuk men-generate file `sitemap.xml` secara dinamis.
* Mengambil daftar seluruh proyek aktif dan postingan blog aktif dari API backend Laravel secara berkala (atau caching 1 jam), kemudian merender XML sitemap dengan `<lastmod>` yang valid.

---

## 8. Rencana Verifikasi Akhir

Sebelum deploy ke produksi, lakukan checklist verifikasi:
1. **Lighthouse / PageSpeed Audit:** Memastikan skor Performa, Aksesibilitas, Best Practices, dan SEO di atas 90 untuk mobile dan desktop.
2. **SEO Scraping Simulation:** Menggunakan tool seperti `curl` atau bot scraper untuk memeriksa apakah tag title, meta description, dan markup JSON-LD ter-render sempurna dalam response mentah HTML.
3. **Responsive Check:** Uji tampilan di berbagai ukuran layar (Mobile 360px, Tablet 768px, Desktop 1200px+).
4. **Form Submission Test:** Mengirim pesan lewat form kontak dan memastikan notifikasi Telegram diterima instan (< 3 detik) serta data tersimpan di Inbox Admin.
