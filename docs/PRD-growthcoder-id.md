PRODUCT REQUIREMENT DOCUMENT

growthcoder.id

Personal Portfolio & Dynamic Blog Platform

Headless Jamstack Architecture — Laravel 13 + Inertia.js 3.0 (Admin) & Nuxt 4 (Public SSR)

CONFIDENTIAL — INTERNAL DEVELOPMENT REFERENCE

DAFTAR ISI

1. EXECUTIVE SUMMARY & OBJECTIVES

1.1 Ringkasan Proyek

Dokumen ini mendefinisikan kebutuhan produk untuk pembangunan growthcoder.id, sebuah platform personal branding berbasis web yang berfungsi sebagai portofolio profesional, ruang pamer studi kasus proyek teknis, dan media publikasi (blog) bagi Muhammad Ihsan Maulana — mahasiswa akhir Teknik Informatika dan Full-Stack Developer yang berfokus pada pengembangan web modern, integrasi API, serta otomasi berbasis ekosistem Telegram Bot.

Platform ini dibangun dengan arsitektur Headless / Jamstack dalam satu repositori (monorepo), memisahkan secara tegas antara lapisan manajemen data (backend & admin CMS) dan lapisan presentasi publik (frontend). Pemisahan ini memungkinkan frontend publik mencapai performa rendering tercepat yang mungkin dicapai, sekaligus memberikan pengalaman administrasi konten yang modern dan efisien bagi pemilik produk sebagai satu-satunya administrator.

1.2 Visi Produk

growthcoder.id diposisikan sebagai representasi digital utama (digital flagship) dari identitas profesional Muhammad Ihsan Maulana, dengan tiga pilar fungsi utama:

Personal Branding — Membangun citra profesional yang konsisten, modern, dan kredibel sebagai Full-Stack Developer yang ahli dalam ekosistem Laravel, Vue.js, dan Nuxt.js, serta spesialis integrasi API dan otomasi Telegram Bot.

Showcase Proyek Full-Stack — Menyajikan studi kasus proyek secara mendalam (bukan sekadar daftar tautan), mencakup konteks masalah, pendekatan teknis, arsitektur sistem, dan hasil yang dicapai, untuk membuktikan kapabilitas teknis secara nyata kepada audiens teknikal maupun non-teknikal.

Media Edukasi & Blog — Menjadi kanal berbagi pengetahuan teknis (tutorial, studi kasus, opini industri) yang sekaligus berfungsi sebagai mesin pertumbuhan SEO organik jangka panjang melalui konten yang terstruktur dan saling terhubung (internal linking).

1.3 Tujuan Bisnis & Profesional

Tujuan akhir dari platform ini bukan sekadar eksistensi digital, melainkan sebagai aset akuisisi peluang karier dan bisnis. Tujuan utama yang ingin dicapai:

Menarik Klien Freelance Kelas Atas — Studi kasus proyek yang detail dan profesional dirancang untuk membangun kepercayaan klien potensial (terutama startup atau bisnis yang membutuhkan pengembang yang dapat dipercaya untuk proyek bernilai tinggi), sehingga mengurangi friksi negosiasi dan meningkatkan rate proyek.

Menarik Perhatian Recruiter Perusahaan Teknologi — Portofolio yang terindeks dengan baik di mesin pencari dan menampilkan kedalaman teknis (arsitektur, tech stack, problem-solving) berfungsi sebagai "CV hidup" yang melengkapi profil LinkedIn/GitHub, meningkatkan peluang dilirik oleh tim talent acquisition perusahaan teknologi skala menengah-besar.

Membangun Otoritas Teknis (Thought Leadership) — Melalui konten blog teknis yang konsisten dan informatif, platform ini membangun reputasi sebagai praktisi yang kompeten dalam niche web development dan otomasi, yang pada akhirnya menciptakan peluang inbound (kolaborasi, undangan menulis, tawaran kerja) tanpa perlu pencarian aktif.

Menjadi Saluran Konversi Leads Terotomasi — Setiap calon klien atau recruiter yang menghubungi melalui formulir kontak akan langsung diteruskan secara real-time ke Telegram pribadi pemilik melalui bot otomasi, memastikan tidak ada peluang (lead) yang terlewat atau terlambat direspons.

1.4 Target Audiens

1.5 Indikator Keberhasilan (Success Metrics)

Keberhasilan proyek ini diukur bukan hanya dari sisi penyelesaian teknis, melainkan dari dampaknya terhadap tujuan profesional pemilik. Indikator kunci yang akan dipantau pasca-peluncuran:

Skor Core Web Vitals (LCP, INP, CLS) konsisten di kategori "Good" pada Google PageSpeed Insights / Search Console, dengan target skor performa mobile dan desktop di atas 90.

Pertumbuhan trafik organik (organic search traffic) yang terukur melalui Google Search Console, didorong oleh konten blog yang terindeks dengan baik.

Jumlah dan kualitas leads (pesan masuk) yang tercatat melalui modul Inbox & notifikasi Telegram.

Waktu muat halaman (Time to First Byte & Largest Contentful Paint) yang sangat cepat berkat strategi Server-Side Rendering (SSR) pada Nuxt.js.

2. SYSTEM ARCHITECTURE & TECH STACK OVERVIEW

2.1 Filosofi Arsitektur: Headless / Jamstack

Sistem dibangun menggunakan pendekatan headless, yaitu memisahkan secara total antara lapisan manajemen data/konten (backend) dan lapisan presentasi (frontend). Backend tidak bertanggung jawab untuk merender HTML yang dilihat publik; tugasnya murni sebagai penyedia data terstruktur melalui REST API. Frontend publik mengonsumsi API tersebut secara independen dan bertanggung jawab penuh atas rendering, SEO, dan optimasi performa di sisi pengunjung.

Pendekatan ini dipilih karena menggabungkan dua kebutuhan yang sebelumnya sulit dipenuhi sekaligus oleh satu framework monolitik: (1) kemudahan dan kematangan ekosistem Laravel untuk membangun panel admin yang kaya fitur dan aman, serta (2) kecepatan rendering dan optimasi SEO kelas atas yang hanya bisa dicapai oleh framework frontend modern berbasis Vue/Nuxt dengan dukungan SSR native.

2.2 Komponen Tech Stack

Platform ini menggunakan pustaka dan modul modern dengan versi spesifik berikut (sesuai yang terpasang dalam repositori proyek):

2.2.1 Backend (Laravel API & Admin CMS)
- **Runtime Environment:** PHP `^8.3`
- **Framework Utama:** Laravel Framework `^13.7`
- **Frontend Integration (Admin):** Inertia.js (Laravel adapter `^3.0`, JavaScript Vue 3 adapter `@inertiajs/vue3 ^3.0.0` & `@inertiajs/vite ^3.0.0`)
- **UI & Layout (Admin Panel):**
  - Vue `^3.5.13`
  - Tailwind CSS `^4.1.1` (Vite Integration: `@tailwindcss/vite ^4.1.11`)
  - Reka UI `^2.9.8`
  - Lucide Vue `^1.17.0` (Set ikon)
  - VueUse `^12.8.2` (Reactive utility helpers)
  - Class Variance Authority `^0.7.1` & Clsx `^2.1.1`
  - Tailwind Merge `^3.2.0`
- **Autentikasi & Keamanan:** Laravel Fortify `^1.37.2` & Laravel Passkeys `^0.2.0`
- **Sistem Routing & Tooling Developer:**
  - Laravel Chisel `^0.1.0`
  - Laravel Wayfinder `^0.1.14` (dengan `@laravel/vite-plugin-wayfinder ^0.1.3`)
  - Laravel Tinker `^3.0`
  - Vite `^8.0.0` (Build tool)
- **Database:** MySQL

2.2.2 Frontend (Public SSR)
- **Framework Utama:** Nuxt `^4.4.8` (Public SSR & Nitro Engine)
- **Library Vue Core:** Vue `^3.5.35`
- **Routing:** Vue Router `^5.1.0`
- **Compatibility Date:** `2025-07-15`

2.3 Alur Data: Laravel API → Nuxt.js SSR

Alur data dirancang agar setiap permintaan dari pengunjung publik (atau dari crawler Google) selalu mendapatkan HTML yang sudah ter-render penuh di sisi server, bukan halaman kosong yang baru diisi oleh JavaScript di sisi klien (client-side rendering murni). Berikut adalah penjelasan alur end-to-end:

2.3.1 Skenario: Pengunjung Membuka Halaman Detail Blog

Pengunjung (atau Googlebot) mengakses URL, misalnya https://growthcoder.id/blog/optimasi-performa-laravel.

Request diterima oleh Nuxt Server (Nitro engine) yang berjalan di sisi server (Node.js runtime), bukan langsung dikirim ke browser sebagai file statis kosong.

Di dalam lifecycle server-side Nuxt (menggunakan composable seperti useFetch atau $fetch dalam asyncData/setup), Nuxt mengirim HTTP request ke Laravel REST API, contoh: GET https://api.growthcoder.id/api/v1/posts/optimasi-performa-laravel.

Laravel API menerima request, melakukan query ke database (mengambil data post, relasi kategori, relasi media, dan meta SEO terkait), lalu mengembalikan response dalam format JSON terstruktur (umumnya melalui API Resource Laravel untuk konsistensi struktur data).

Nuxt Server menerima JSON tersebut, lalu merender komponen Vue (termasuk konten blog, gambar, breadcrumb, related posts) menjadi HTML lengkap di sisi server.

Nuxt Server mengirimkan HTML yang sudah jadi (fully rendered) beserta payload data (untuk hidrasi) ke browser/crawler.

Browser menampilkan halaman secara instan (karena HTML sudah lengkap), kemudian JavaScript Vue melakukan proses hidrasi di belakang layar agar halaman menjadi interaktif (misalnya tombol like, filter kategori) tanpa perlu reload.

Untuk Googlebot, langkah hidrasi tidak relevan — crawler langsung membaca HTML lengkap pada langkah 6-7, memastikan seluruh konten (judul, isi artikel, meta tag, structured data) dapat diindeks secara sempurna tanpa kendala rendering JavaScript.

2.3.2 Skenario: Admin Mengelola Konten via CMS

Berbeda dengan alur publik, panel admin tidak melalui Nuxt sama sekali. Admin mengakses subdomain/path terpisah (misalnya admin.growthcoder.id), yang sepenuhnya dilayani oleh Laravel + Inertia + Vue. Ketika admin menyimpan data (misalnya menambah proyek baru), Inertia mengirim request langsung ke Controller Laravel, yang menyimpan data ke database yang sama — database inilah yang kemudian dibaca oleh API publik dan ditampilkan di Nuxt. Dengan demikian, kedua sisi (admin & publik) berbagi satu sumber data tunggal (single source of truth).

2.3.3 Diagram Alur Sistem (Ringkasan Tekstual)

2.4 Struktur Folder Monorepo

Seluruh kode sumber proyek (backend dan frontend) disimpan dalam satu repositori Git (monorepo) untuk memudahkan koordinasi versi, meskipun keduanya dideploy sebagai dua aplikasi yang berjalan secara independen. Struktur direktori tingkat atas adalah sebagai berikut:

2.4.1 Catatan Penting Struktur Folder

Pemisahan namespace Controller — Folder Controllers/Admin/ menangani logic untuk Inertia (mengembalikan render Vue Page), sedangkan Controllers/Api/V1/ murni mengembalikan JSON. Pemisahan versi API (V1/) penting untuk antisipasi perubahan kontrak data di masa depan tanpa merusak frontend yang sudah berjalan.

API Resource sebagai kontrak data — Setiap response API publik wajib melalui kelas Resource (mis. ProjectResource, PostResource) agar struktur JSON konsisten, terkontrol, dan tidak membocorkan kolom database yang tidak relevan (mis. timestamps internal, foreign key mentah).

Deployment independen — Meski berada dalam satu repo, folder backend/ dan frontend/ memiliki proses build & deploy terpisah (masing-masing punya .env, dependency, dan server/proses sendiri). Disarankan menggunakan dua subdomain: growthcoder.id (Nuxt) dan api.growthcoder.id (Laravel).

3. USER ROLES & ACCESS CONTROL

Mengingat skala proyek ini adalah platform personal (single-owner), model hak akses dirancang sederhana namun tetap mengikuti prinsip keamanan least privilege. Terdapat dua kategori aktor utama yang berinteraksi dengan sistem.

3.1 Role: Public Visitor

Mencakup dua sub-tipe aktor yang keduanya hanya berinteraksi dengan Public Frontend (Nuxt.js) dan endpoint API publik (read-only):

3.1.1 Pengunjung Umum (Human Visitor)

3.1.2 Search Engine Crawler Bot (Googlebot, Bingbot, dst.)

3.2 Role: Administrator

Administrator adalah role tunggal yang dipegang oleh pemilik produk (Muhammad Ihsan Maulana), dengan akses penuh (full access / superuser) terhadap seluruh modul CMS. Mengingat sifat platform personal, sistem tidak memerlukan hierarki role multi-level (mis. Editor, Contributor) pada versi awal — namun struktur database dirancang agar role tambahan dapat ditambahkan di masa depan tanpa migrasi besar (lihat skema users di bawah).

3.3 Matriks Hak Akses Modul

4. DETAILED FUNCTIONAL REQUIREMENTS (MODULES & FEATURES)

Bagian ini menjabarkan lima modul fungsional inti dari sistem. Setiap modul disertai dengan deskripsi kebutuhan, user story dari sudut pandang Administrator (sebagai satu-satunya pengguna CMS), serta skema field data esensial yang menjadi acuan langsung untuk perancangan migration database dan API Resource.

4.1 Modul Proyek & Studi Kasus (Projects / Case Studies)

Modul ini adalah etalase utama kapabilitas teknis pemilik produk. Berbeda dari portofolio konvensional yang hanya menampilkan daftar link, modul ini dirancang untuk menyajikan setiap proyek sebagai sebuah studi kasus naratif — lengkap dengan konteks masalah, pendekatan solusi, stack teknologi, dan bukti visual (gambar, diagram arsitektur, bahkan demo embed).

4.1.1 Kebutuhan Fungsional Utama

Admin dapat membuat, mengedit, menghapus, dan mengatur status publikasi (draft/published) sebuah entri proyek.

Setiap proyek wajib dikaitkan dengan satu kategori utama yang dikelola secara dinamis melalui Modul Kategori Proyek (lihat Section 4.6) untuk memudahkan filtering di sisi publik.

Setiap proyek mendukung galeri multimedia (multiple screenshot/gambar) yang diambil dari Media Library terpusat (lihat Modul 4.3), bukan upload langsung per proyek, untuk menjaga reusabilitas aset.

Setiap proyek dapat ditandai (tagging) dengan satu atau lebih teknologi dari Master Tech Stack yang dikelola secara dinamis melalui Modul Manajemen Tech Stack (lihat Section 4.7) dan akan ditampilkan sebagai badge visual di frontend.

Konten naratif studi kasus (deskripsi masalah, solusi, proses) ditulis menggunakan rich text editor (WYSIWYG) di admin, mendukung heading, list, blockquote, code snippet, dan penyisipan gambar dari Media Library.

Mendukung penyisipan diagram arsitektur sistem — baik sebagai gambar statis (diupload via Media Library) maupun, sebagai opsi pengembangan lanjutan, embed diagram berbasis teks (mis. Mermaid.js) yang dirender di sisi frontend.

Setiap proyek dapat memiliki link eksternal terkait: Live Demo URL, Source Code (GitHub) URL, dan untuk tipe Telegram Bot khususnya, link langsung ke bot (t.me/...).

Admin dapat menandai proyek sebagai "Featured" agar ditampilkan secara prioritas di halaman Beranda.

Admin dapat mengatur urutan tampil (custom ordering) proyek pada halaman listing, tidak hanya berdasarkan tanggal pembuatan.

4.1.2 User Story

4.1.3 Skema Field Data Esensial

Tabel berikut merepresentasikan struktur kolom utama pada tabel projects beserta tabel relasi pendukungnya.

Tabel: projects

Tabel: project_tech_stacks (Relasi Many-to-Many dengan Master Tag Teknologi)

Untuk mendukung tag tech stack yang reusable dan dapat difilter (misalnya menampilkan "semua proyek yang menggunakan Laravel"), tech stack disimpan sebagai entitas master tersendiri (technologies), dihubungkan ke proyek melalui tabel pivot.

Tabel: project_images (Relasi Many-to-Many Galeri ke Media Library)

4.2 Modul Blog & Manajemen Kategori

Modul blog dirancang sebagai mesin pertumbuhan SEO organik jangka panjang. Selain fitur penulisan standar, fokus utama modul ini adalah relasi dinamis antara post dan kategori yang mendukung strategi internal linking — memastikan setiap artikel saling terhubung secara tematik sehingga mesin pencari dapat memahami struktur topik (topic cluster) situs secara keseluruhan, sekaligus mendorong pengunjung untuk membaca lebih banyak artikel terkait (meningkatkan dwell time).

4.2.1 Kebutuhan Fungsional Utama

Admin dapat membuat, mengedit, menghapus, dan mengatur status publikasi (draft/published/scheduled) sebuah post blog.

Setiap post wajib terhubung ke minimal satu kategori, dan dapat memiliki banyak kategori (relasi many-to-many) untuk fleksibilitas taksonomi konten.

Setiap kategori memiliki halaman listing tersendiri di frontend (mis. /blog/kategori/laravel) yang menampilkan seluruh post terkait — mendukung internal linking terstruktur dan breadcrumb SEO.

Sistem secara otomatis menyarankan/menampilkan "Artikel Terkait" (related posts) pada halaman detail blog berdasarkan kesamaan kategori, untuk memperkuat internal linking tanpa perlu input manual setiap saat.

Admin dapat secara opsional menambahkan related posts secara manual (override) jika ingin mengarahkan pembaca ke artikel spesifik yang relevan secara strategis.

Konten post ditulis menggunakan rich text editor (WYSIWYG) yang mendukung heading (H2-H4 untuk struktur SEO on-page), blockquote, list, code block dengan syntax highlighting, dan penyisipan gambar dari Media Library.

Sistem menghitung estimasi waktu baca (reading time) secara otomatis berdasarkan jumlah kata dalam konten.

Setiap post mendukung override meta title & meta description khusus untuk SEO, terpisah dari judul tampilan.

Mendukung tag tambahan (selain kategori) sebagai metadata pelengkap untuk pencarian internal, bersifat opsional.

4.2.2 User Story

4.2.3 Skema Field Data Esensial

Tabel: posts

Tabel: categories

Tabel: post_category (Pivot Many-to-Many)

Tabel: post_related (Self-Referencing Many-to-Many, Opsional/Manual Override)

4.3 Modul Media Manajemen Terpusat (Centralized Media Library)

Modul ini berfungsi sebagai pustaka aset gambar tunggal yang digunakan bersama oleh seluruh modul lain (Proyek, Blog, Pengaturan Global). Pendekatan terpusat ini menghindari duplikasi file fisik di server, memudahkan penggantian aset secara global, dan menjadi titik kendali tunggal untuk optimasi performa gambar (kompresi dan konversi WebP) di seluruh situs.

4.3.1 Kebutuhan Fungsional Utama

Admin dapat mengunggah satu atau beberapa gambar sekaligus (bulk upload) ke dalam Media Library melalui antarmuka drag-and-drop pada panel admin.

Setiap gambar yang diunggah secara otomatis dikonversi ke format WebP (dengan tetap menyimpan format asli sebagai fallback jika diperlukan) untuk memastikan ukuran file minimal tanpa penurunan kualitas visual yang signifikan.

Sistem secara otomatis men-generate beberapa varian ukuran (responsive image sizes / thumbnails) dari setiap gambar yang diunggah, agar frontend dapat menyajikan ukuran gambar yang sesuai dengan kebutuhan tampilan (mis. thumbnail kecil untuk card, ukuran penuh untuk halaman detail) — mengurangi beban bandwidth yang tidak perlu.

Admin dapat melihat seluruh pustaka gambar dalam tampilan grid, mencari berdasarkan nama file, dan memfilter berdasarkan modul yang menggunakan gambar tersebut.

Saat memilih gambar untuk proyek/post/pengaturan, admin memilih dari Media Library yang sudah ada (reuse) atau mengunggah baru — tidak ada upload duplikat khusus per modul.

Setiap entri media menyimpan metadata wajib alt text untuk mendukung aksesibilitas dan SEO gambar (Google Image Search).

Sistem mencegah penghapusan gambar yang masih digunakan oleh entitas lain (referential integrity check), atau menampilkan peringatan jumlah penggunaan sebelum admin mengonfirmasi penghapusan.

4.3.2 User Story

4.3.3 Skema Field Data Esensial

Tabel: media

4.4 Modul Pengaturan Global & SEO

Modul ini menyediakan satu titik kendali terpusat (single settings record) bagi admin untuk mengelola konten yang bersifat global/tidak terikat pada satu entitas spesifik — seperti teks hero di Beranda, file CV, tautan media sosial, dan meta tag default untuk seluruh situs. Pendekatan ini menghindari hard-coded content di sisi frontend, sehingga admin dapat mengubah teks-teks krusial tanpa perlu deployment ulang kode.

4.4.1 Kebutuhan Fungsional Utama

Admin dapat mengubah teks hero dinamis di halaman Beranda (headline utama, sub-headline/tagline, dan teks Call-to-Action) melalui satu form pengaturan tunggal.

Admin dapat mengunggah dan memperbarui file CV (PDF) yang dapat diunduh langsung oleh pengunjung dari halaman Beranda — penting bagi recruiter yang ingin akses cepat ke dokumen formal.

Admin dapat mengatur tautan ke seluruh profil media sosial dan platform profesional yang relevan (LinkedIn, GitHub, Telegram pribadi/channel, Instagram, X/Twitter, email kontak) yang akan ditampilkan secara konsisten di Navbar/Footer seluruh halaman.

Admin dapat mengatur Global Meta Tags default (site name, default meta title suffix, default meta description, default Open Graph image) yang digunakan sebagai fallback pada halaman mana pun yang belum memiliki override meta spesifik.

Admin dapat mengatur informasi identitas dasar yang tampil di berbagai bagian situs: nama lengkap, jabatan/peran (mis. "Full-Stack Developer"), dan foto profil (direferensikan dari Media Library).

Perubahan pada modul ini langsung tercermin di seluruh halaman publik tanpa perlu mengedit individual content lain, karena bersifat global setting yang diquery satu kali oleh layout/composable Nuxt.

4.4.2 User Story

4.4.3 Skema Field Data Esensial

Modul ini direpresentasikan sebagai single-row table (atau dapat pula diimplementasikan sebagai key-value settings table untuk fleksibilitas lebih tinggi). Berikut skema pendekatan single-row yang lebih sederhana untuk diimplementasikan pada skala proyek ini:

Tabel: site_settings (Single Row Record)

4.5 Modul Inbox Form Kontak & Otomasi Telegram Bot

Modul ini adalah jembatan konversi antara pengunjung situs dengan pemilik produk. Selain menyimpan setiap submission ke dalam database (sebagai Inbox yang dapat ditinjau di admin panel), sistem secara otomatis memicu notifikasi instan ke akun Telegram pribadi admin melalui Bot API setiap kali ada pesan baru — memastikan respons cepat terhadap peluang bisnis yang masuk.

4.5.1 Kebutuhan Fungsional Utama

Pengunjung publik dapat mengirimkan pesan melalui formulir kontak yang tersedia di halaman publik (umumnya pada halaman Beranda atau halaman Kontak terpisah), berisi field nama, email, subjek/topik (opsional), dan isi pesan.

Setiap submission disimpan secara permanen ke dalam tabel database (contact_messages) sehingga admin dapat meninjau riwayat lengkap kapan saja melalui panel admin, tidak hanya bergantung pada notifikasi Telegram yang sifatnya sekali lewat.

Segera setelah submission berhasil disimpan, backend Laravel memicu HTTP request ke Telegram Bot API (endpoint sendMessage) untuk mengirim notifikasi real-time ke chat ID pribadi admin, berisi ringkasan pesan (nama, email, dan isi pesan).

Pengiriman notifikasi Telegram dijalankan secara asynchronous (melalui Laravel Queue/Job) agar kegagalan atau lambatnya koneksi ke Telegram API tidak memperlambat response time yang diterima pengunjung saat submit form.

Form kontak publik dilindungi oleh validasi server-side ketat dan mekanisme anti-spam (rate limiting per IP dan/atau honeypot field) untuk mencegah penyalahgunaan.

Admin dapat melihat daftar seluruh pesan masuk di Inbox panel admin, menandai status (mis. "Belum Dibaca", "Sudah Dibaca", "Sudah Direspons"), dan menghapus pesan yang tidak relevan/spam.

Jika pengiriman ke Telegram API gagal (mis. token invalid, koneksi timeout), kegagalan tersebut dicatat ke log sistem namun tidak menggagalkan proses penyimpanan pesan ke database — Inbox tetap menjadi sumber data utama yang andal.

4.5.2 User Story

4.5.3 Skema Field Data Esensial

Tabel: contact_messages

4.5.4 Alur Teknis Otomasi Telegram (Sequence Flow)

Pengunjung mengisi dan mengirimkan form kontak pada halaman publik (Nuxt). Request dikirim sebagai HTTP POST ke endpoint Laravel API, contoh: POST /api/v1/contact.

Laravel Form Request (mis. StoreContactMessageRequest) memvalidasi seluruh input (nama, email, pesan wajib; honeypot field harus kosong; rate limit per IP belum terlampaui).

Jika valid, Controller menyimpan data ke tabel contact_messages dengan status default unread.

Controller men-dispatch sebuah Job (mis. SendTelegramNotificationJob) ke dalam queue, lalu segera mengembalikan response sukses (HTTP 201) ke Nuxt — pengunjung tidak menunggu proses Telegram selesai.

Queue Worker memproses Job tersebut secara asynchronous: memanggil TelegramNotifierService yang melakukan HTTP request ke endpoint Telegram Bot API: https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage dengan payload chat_id dan text berisi ringkasan pesan.

Jika request ke Telegram API berhasil (HTTP 200 dari Telegram), kolom telegram_notified_at pada record terkait diperbarui dengan timestamp saat itu.

Jika request gagal (mis. timeout, token tidak valid), sistem mencatat error ke log Laravel (Log::error) dan—opsional—melakukan retry otomatis sesuai konfigurasi retry pada Job, tanpa memengaruhi data yang sudah tersimpan di Inbox.

4.6 Modul Kategori Proyek (Project Categories)

Modul Kategori Proyek digunakan untuk mengelola pengelompokan proyek secara dinamis. Administrator dapat menentukan kategori baru (seperti "Web Application", "Mobile App", "Telegram Bot", "API/Backend Service", "Automation Tool") sehingga pengunjung dapat memfilter proyek berdasarkan kategori tersebut di halaman portofolio publik.

4.6.1 Kebutuhan Fungsional Utama
- Admin dapat melakukan operasi CRUD (Create, Read, Update, Delete) pada kategori proyek melalui panel CMS.
- Setiap kategori memiliki nama unik, slug otomatis, deskripsi, urutan prioritas (order), dan ikon visual untuk antarmuka.
- Sistem mencegah penghapusan kategori jika masih ada proyek aktif yang menggunakannya (referential integrity), atau memindahkan proyek tersebut ke kategori fallback default.
- API publik menyediakan endpoint read-only untuk mengambil daftar kategori beserta proyek terkait.

4.6.2 User Story
- Sebagai Admin, saya ingin membuat kategori proyek baru agar saya dapat mengelompokkan studi kasus proyek yang sejenis.
- Sebagai Admin, saya ingin mengedit deskripsi dan ikon kategori agar tampilan pengelompokan di sisi publik selalu up-to-date dan menarik.
- Sebagai Pengunjung, saya ingin menyaring portofolio berdasarkan kategori proyek agar saya hanya melihat tipe proyek yang menarik bagi kebutuhan bisnis saya.

4.6.3 Skema Field Data Esensial

Tabel: project_categories

| Nama Kolom | Tipe Data | Atribut/Constraint | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Primary Key, Auto Increment | ID unik kategori |
| `name` | VarChar(255) | Not Null | Nama kategori (misal: "Web Application") |
| `slug` | VarChar(255) | Not Null, Unique | URL-friendly name (misal: "web-application") |
| `description` | Text | Nullable | Deskripsi singkat mengenai kategori proyek |
| `icon` | VarChar(255) | Nullable | Kode class ikon (misal: Lucide Icon name) atau markup SVG |
| `order` | Integer | Not Null, Default: 0 | Urutan tampilan kategori pada filter menu |
| `created_at` | Timestamp | Nullable | Waktu pembuatan baris data |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan baris data |

4.7 Modul Manajemen Tech Stack (Technologies Master Data)

Modul ini adalah pusat data teknologi yang dikuasai dan digunakan dalam proyek. Dibandingkan dengan sistem tag statis biasa, modul ini mengelola entitas teknologi secara independen sehingga dapat dihubungkan ke modul Proyek dan modul Skills secara terpusat, menghindari inkonsistensi data.

4.7.1 Kebutuhan Fungsional Utama
- Admin dapat melakukan operasi CRUD untuk data teknologi (misal: Laravel, Vue.js, Tailwind CSS, Docker, PostgreSQL, dll.).
- Setiap entitas teknologi memiliki nama, slug, kategori (Frontend, Backend, DevOps, Database, dll.), link dokumentasi resmi, dan logo visual (menggunakan media_id dari Media Library terpusat).
- Admin dapat menandai teknologi sebagai "Featured" untuk ditampilkan di halaman Beranda sebagai "Keunggulan Tech Stack".
- Relasi Many-to-Many terjalin dengan tabel `projects` menggunakan tabel pivot.

4.7.2 User Story
- Sebagai Admin, saya ingin menambahkan teknologi baru yang baru saja saya pelajari ke dalam CMS lengkap dengan logonya agar bisa saya gunakan untuk tagging proyek.
- Sebagai Admin, saya ingin menandai Laravel dan Nuxt sebagai "Featured Tech Stack" agar tampil menonjol di halaman Beranda.
- Sebagai Pengunjung, saya ingin mengklik badge teknologi di studi kasus proyek untuk melihat detail proyek lain yang menggunakan teknologi yang sama.

4.7.3 Skema Field Data Esensial

Tabel: technologies

| Nama Kolom | Tipe Data | Atribut/Constraint | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Primary Key, Auto Increment | ID unik teknologi |
| `name` | VarChar(255) | Not Null | Nama teknologi (misal: "Laravel") |
| `slug` | VarChar(255) | Not Null, Unique | URL-friendly slug (misal: "laravel") |
| `logo_media_id` | BigInt | Foreign Key ke `media.id`, Nullable, Set Null on Delete | Referensi logo dari Media Library terpusat |
| `category` | VarChar(100) | Not Null | Kategori (frontend, backend, devops, database, tools) |
| `description` | Text | Nullable | Deskripsi singkat mengenai teknologi tersebut |
| `url` | VarChar(255) | Nullable | Tautan ke situs web resmi teknologi |
| `is_featured` | Boolean | Not Null, Default: false | Tampilkan di halaman utama |
| `created_at` | Timestamp | Nullable | Waktu pembuatan baris data |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan baris data |

Tabel: project_technology (Pivot Many-to-Many)

| Nama Kolom | Tipe Data | Atribut/Constraint | Deskripsi |
| :--- | :--- | :--- | :--- |
| `project_id` | BigInt | Foreign Key ke `projects.id`, Cascade on Delete | Referensi ID proyek |
| `technology_id` | BigInt | Foreign Key ke `technologies.id`, Cascade on Delete | Referensi ID teknologi |

4.8 Modul Manajemen Skill (Skills Management)

Modul ini memfasilitasi pengelolaan informasi keterampilan pengembang yang akan dipamerkan secara visual di frontend. Modul ini secara cerdas menggunakan data dari modul `technologies` untuk menghindari pengisian berulang dan memastikan keselarasan logo.

4.8.1 Kebutuhan Fungsional Utama
- Admin dapat menambahkan keahlian baru dengan memilih teknologi yang sudah terdaftar di Master Tech Stack.
- Admin dapat menentukan tingkat kemahiran (proficiency_level) dalam format persentase (0 hingga 100).
- Admin dapat mencatat lama pengalaman menggunakan teknologi tersebut dalam satuan tahun (`years_of_experience`).
- Admin dapat mengatur urutan penampilan secara kustom (order) dan menandai keahlian sebagai "Featured".

4.8.2 User Story
- Sebagai Admin, saya ingin menambahkan keahlian "Vue.js" dengan tingkat kemahiran 90% dan mempublikasikannya ke halaman resume.
- Sebagai Admin, saya ingin menyusun urutan keahlian agar keahlian utama saya diletakkan di posisi teratas.
- Sebagai Pengunjung, saya ingin melihat diagram atau visualisasi kemahiran teknis pengembang untuk mengukur apakah ia cocok dengan spesifikasi pekerjaan saya.

4.8.3 Skema Field Data Esensial

Tabel: skills

| Nama Kolom | Tipe Data | Atribut/Constraint | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Primary Key, Auto Increment | ID unik keahlian |
| `technology_id` | BigInt | Foreign Key ke `technologies.id`, Cascade on Delete | Hubungan ke master teknologi |
| `proficiency_level` | Integer | Not Null | Persentase tingkat penguasaan (0 - 100) |
| `years_of_experience`| Decimal(3,1)| Nullable | Estimasi durasi penggunaan dalam tahun (misal: 3.5) |
| `is_featured` | Boolean | Not Null, Default: false | Tampilkan secara khusus di homepage/resume highlight |
| `order` | Integer | Not Null, Default: 0 | Urutan sorting manual |
| `created_at` | Timestamp | Nullable | Waktu pembuatan baris data |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan baris data |

4.9 Modul Manajemen Layanan (Service Management)

Modul ini digunakan untuk mengelola layanan profesional yang ditawarkan oleh pemilik produk (misal: Full-Stack Web Development, API Integration, Telegram Bot Development, Performance Optimization). Informasi ini berfungsi sebagai sarana promosi langsung (sales landing) bagi klien potensial.

4.9.1 Kebutuhan Fungsional Utama
- Admin dapat melakukan operasi CRUD untuk setiap jenis layanan.
- Setiap layanan memiliki judul, deskripsi singkat (untuk card layout), deskripsi panjang (penjelasan detail layanan menggunakan format Rich Text/HTML), ikon representatif, status aktif, dan harga awal (optional).
- Sistem memaparkan endpoint API bagi frontend untuk menampilkan katalog layanan di halaman beranda atau halaman khusus.

4.9.2 User Story
- Sebagai Admin, saya ingin menulis deskripsi layanan "Telegram Bot Development" beserta harga awalnya agar calon klien mengetahui cakupan jasa yang saya tawarkan.
- Sebagai Admin, saya ingin menonaktifkan sementara layanan tertentu jika kapasitas pengerjaan saya sedang penuh tanpa harus menghapusnya dari database.
- Sebagai Pengunjung, saya ingin membaca deskripsi detail layanan untuk memahami alur kerja dan apa yang akan saya dapatkan dari kerja sama tersebut.

4.9.3 Skema Field Data Esensial

Tabel: services

| Nama Kolom | Tipe Data | Atribut/Constraint | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Primary Key, Auto Increment | ID unik layanan |
| `title` | VarChar(255) | Not Null | Nama layanan (misal: "Telegram Bot Development") |
| `slug` | VarChar(255) | Not Null, Unique | URL-friendly slug (misal: "telegram-bot-development") |
| `short_description` | Text | Not Null | Deskripsi singkat (maksimal 200 karakter) |
| `long_description` | Text | Nullable | Rincian lengkap layanan dalam format HTML/Rich Text |
| `icon` | VarChar(255) | Nullable | Nama ikon (Lucide) atau path visual untuk render |
| `price_starts_from` | Decimal(12,2)| Nullable | Harga mulai dari (misal: 1500000.00) |
| `is_active` | Boolean | Not Null, Default: true | Menentukan apakah layanan aktif & dipublikasikan |
| `order` | Integer | Not Null, Default: 0 | Urutan sorting visual |
| `created_at` | Timestamp | Nullable | Waktu pembuatan baris data |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan baris data |

4.10 Modul Riwayat Pendidikan & Pengalaman (Education & Experience)

Modul ini mengelola data portofolio karir dan akademis secara terstruktur untuk membangun resume digital. Memiliki skema terpadu yang memisahkan tipe entitas antara Pendidikan dan Pengalaman Kerja.

4.10.1 Kebutuhan Fungsional Utama
- Admin dapat melakukan operasi CRUD riwayat hidup profesional.
- Data dipisahkan berdasarkan tipe: 'education' (Pendidikan) atau 'experience' (Pengalaman Kerja).
- Mendukung pencatatan institusi/perusahaan, jabatan/jurusan, periode waktu (tanggal mulai hingga selesai, mendukung status "Masih Bekerja/Belajar"), deskripsi pencapaian/tugas, serta tautan web resmi.
- Mendukung pengunggahan logo institusi melalui integrasi dengan Media Library.

4.10.2 User Story
- Sebagai Admin, saya ingin menambahkan riwayat pekerjaan baru sebagai "Full-Stack Developer" di sebuah software house, lengkap dengan durasi dan poin-poin kontribusi saya.
- Sebagai Admin, saya ingin memperbarui logo institusi universitas saya agar riwayat pendidikan saya terlihat lebih formal dan kredibel.
- Sebagai Recruiter, saya ingin melihat urutan kronologis riwayat kerja dan akademis pengembang untuk menganalisis latar belakang karirnya secara cepat.

4.10.3 Skema Field Data Esensial

Tabel: experiences

| Nama Kolom | Tipe Data | Atribut/Constraint | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | BigInt | Primary Key, Auto Increment | ID unik riwayat |
| `type` | Enum | Not Null, Pilihan: `education`, `experience` | Kategori entri riwayat |
| `company_institution` | VarChar(255) | Not Null | Nama perusahaan atau lembaga pendidikan |
| `title_position` | VarChar(255) | Not Null | Jabatan pekerjaan atau gelar/jurusan pendidikan |
| `location` | VarChar(255) | Nullable | Lokasi fisik (kota, negara) atau status 'Remote' |
| `start_date` | Date | Not Null | Tanggal mulai periode |
| `end_date` | Date | Nullable | Tanggal berakhir (Null berarti "Sekarang" / "Present") |
| `description` | Text | Nullable | Poin pencapaian, tanggung jawab, atau nilai IPK |
| `website_url` | VarChar(255) | Nullable | Link ke website resmi lembaga |
| `logo_media_id` | BigInt | Foreign Key ke `media.id`, Nullable, Set Null on Delete | Logo resmi institusi dari Media Library |
| `order` | Integer | Not Null, Default: 0 | Manual override sorting order |
| `created_at` | Timestamp | Nullable | Waktu pembuatan baris data |
| `updated_at` | Timestamp | Nullable | Waktu pembaruan baris data |

5. NON-FUNCTIONAL REQUIREMENTS

Bagian ini mendefinisikan kebutuhan kualitas sistem yang bersifat lintas-modul — mencakup SEO, performa, dan keamanan — yang menjadi fokus utama proyek ini sesuai dengan tujuan profesional yang ingin dicapai (lihat Bagian 1.3).

5.1 SEO Requirements

Strategi SEO platform ini bertumpu pada empat pilar teknis yang seluruhnya diimplementasikan di sisi Nuxt.js (Public Frontend), memastikan konten dapat ditemukan, dipahami, dan diberi peringkat tinggi oleh mesin pencari.

5.1.1 Server-Side Rendering (SSR) Wajib di Seluruh Halaman Publik

Seluruh halaman publik (Beranda, List Proyek, Detail Proyek, List Blog, Detail Blog) WAJIB dirender menggunakan mode SSR Nuxt (target: ssr: true pada nuxt.config.ts, bukan mode SPA/static murni), agar setiap request dari crawler menerima HTML yang sudah berisi konten penuh.

Pengambilan data dari Laravel API pada setiap halaman dilakukan melalui composable useFetch atau useAsyncData (bukan onMounted dengan fetch client-side biasa), karena keduanya dieksekusi pada server lifecycle saat SSR berlangsung.

Hindari penggunaan watch/client-only fetch untuk data utama yang harus terindeks (boleh digunakan untuk elemen non-kritis seperti widget interaktif tambahan).

5.1.2 Dynamic Sitemap.xml

Sitemap dibangun secara dinamis (bukan file statis yang ditulis manual) menggunakan server route Nuxt (server/routes/sitemap.xml.ts) atau modul resmi @nuxtjs/sitemap.

Sitemap wajib menyertakan seluruh URL proyek dan post blog yang berstatus is_published = true, diambil secara real-time dari Laravel API setiap kali sitemap diminta (atau dengan caching singkat, mis. 1 jam, untuk efisiensi).

Setiap entri sitemap menyertakan tag <lastmod> berdasarkan kolom updated_at konten terkait, membantu crawler memprioritaskan re-crawl konten yang baru diperbarui.

URL yang berstatus draft, halaman admin, dan endpoint API tidak pernah dimasukkan ke dalam sitemap.

5.1.3 Robots.txt

File robots.txt ditempatkan secara statis di folder public/ frontend Nuxt, secara eksplisit men-disallow rute admin (jika admin pernah diakses melalui proxy path yang sama) dan endpoint API, sambil mengarahkan crawler ke lokasi sitemap.

5.1.4 useSeoMeta pada Setiap Halaman

Setiap halaman publik wajib memanggil composable useSeoMeta (bawaan Nuxt 4) untuk menyuntikkan title, description, ogTitle, ogDescription, ogImage, dan twitterCard secara dinamis berdasarkan data yang diambil dari API (override per-halaman jika tersedia, fallback ke Global Settings jika tidak).

Setiap halaman juga wajib menyertakan canonical URL (melalui useHead dengan link rel="canonical") untuk mencegah masalah duplicate content, khususnya pada halaman listing dengan parameter filter/paginasi.

5.1.5 JSON-LD Structured Data Schema

Halaman Detail Blog menyuntikkan schema BlogPosting (atau Article) berisi headline, datePublished, dateModified, author, dan image, untuk mendukung tampilan rich snippet pada hasil pencarian Google.

Halaman Beranda menyuntikkan schema Person untuk merepresentasikan identitas profesional pemilik (nama, jabatan, sameAs berisi tautan media sosial), mendukung Google Knowledge Panel di masa depan.

Halaman Detail Proyek dapat menyuntikkan schema CreativeWork atau SoftwareApplication sesuai relevansi tipe proyek.

Implementasi dilakukan melalui useHead dengan tag <script type="application/ld+json"> yang berisi objek JSON terstruktur sesuai spesifikasi schema.org.

5.2 Performance & Core Web Vitals

Performa tinggi adalah salah satu nilai jual utama platform ini, baik sebagai bukti kapabilitas teknis pemilik kepada calon klien/recruiter, maupun sebagai faktor ranking langsung di Google. Target dan strategi performa adalah sebagai berikut:

5.2.1 Target Skor

5.2.2 Strategi Lazy Loading & Optimasi Gambar

Seluruh komponen <img> pada frontend menggunakan komponen <NuxtImg> atau <NuxtPicture> (dari modul @nuxt/image) yang secara otomatis menerapkan lazy loading (loading="lazy") untuk gambar di luar viewport awal (below-the-fold).

Gambar pada area above-the-fold (mis. hero image, thumbnail proyek pertama yang terlihat) menggunakan strategi preload/eager loading dan fetchpriority="high" untuk mempercepat LCP.

Frontend selalu meminta varian ukuran gambar yang paling sesuai dengan konteks tampilan (thumbnail untuk card, ukuran besar untuk halaman detail) — bukan selalu menggunakan gambar resolusi penuh, memanfaatkan struktur variants JSON pada tabel media.

Format WebP digunakan sebagai default pengiriman gambar ke browser modern, dengan fallback otomatis (melalui tag <picture>) untuk browser lawas yang belum mendukung WebP.

5.2.3 Optimasi Bundle & Rendering Tambahan

Komponen Nuxt UI yang digunakan dibatasi pada yang benar-benar diperlukan (tree-shaking otomatis Tailwind CSS memastikan hanya utility class yang dipakai yang masuk ke bundle akhir).

Font kustom (jika digunakan) dimuat melalui strategi font-display: swap atau modul @nuxt/fonts untuk menghindari Flash of Invisible Text (FOIT) yang memperlambat perceived performance.

Data dari Laravel API yang jarang berubah (mis. daftar kategori, site settings) dapat menerapkan caching pada level Nuxt (useFetch dengan opsi cache/key yang konsisten) maupun pada level Laravel (Cache::remember) untuk mengurangi beban query database berulang.

Komponen yang berat secara komputasi namun non-kritis untuk render awal (mis. widget komentar pihak ketiga, jika ada di masa depan) dimuat secara client-only menggunakan <ClientOnly> untuk tidak membebani waktu SSR.

5.3 Security Requirements

Mengingat sistem memisahkan layer admin dan publik, kebutuhan keamanan dibagi menjadi tiga area fokus: autentikasi admin, proteksi API, dan validasi input publik.

5.3.1 Autentikasi CMS Admin

Autentikasi panel admin menggunakan mekanisme session-based standar Laravel (cookie httpOnly, terenkripsi), karena admin panel dirender melalui Inertia secara server-side — bukan aplikasi SPA terpisah yang memerlukan token API.

Seluruh rute admin (web.php yang menangani Inertia Pages) dilindungi oleh middleware auth bawaan Laravel, dengan redirect otomatis ke halaman login jika sesi tidak valid/kedaluwarsa.

Rute login dilindungi oleh rate limiting (Laravel throttle middleware) untuk mencegah upaya brute-force terhadap kredensial admin.

Tidak terdapat rute registrasi publik (/register) yang terbuka; akun admin dibuat secara eksklusif melalui seeder/CLI saat instalasi awal sistem.

CSRF protection bawaan Laravel + Inertia aktif secara default untuk seluruh form submission di admin panel.

5.3.2 Proteksi API Routes (Konsumsi Nuxt)

Endpoint API publik (GET /api/v1/projects, GET /api/v1/posts, dst.) yang hanya mengembalikan data published bersifat terbuka tanpa autentikasi, karena memang ditujukan untuk dikonsumsi publik melalui Nuxt SSR maupun langsung oleh crawler bila diperlukan.

Jika di kemudian hari dibutuhkan endpoint khusus yang bersifat privat (misalnya preview konten draft dari Nuxt sebelum dipublikasikan, atau endpoint administratif lain yang ingin diakses dari luar konteks Inertia), endpoint tersebut dilindungi oleh Laravel Sanctum menggunakan token-based authentication, bukan session, agar tetap kompatibel dengan konsumsi cross-origin dari Nuxt server.

Endpoint mutasi data yang tersedia untuk publik dibatasi secara ketat hanya pada POST /api/v1/contact (form kontak), dengan validasi input dan rate limiting per IP (mis. maksimal 5 submission per jam per IP) untuk mencegah spam/abuse.

Seluruh response API publik melewati Laravel API Resource untuk memastikan tidak ada kolom sensitif (mis. token internal, email admin pribadi yang tidak dimaksudkan untuk publik) yang terekspos secara tidak sengaja.

Konfigurasi CORS pada Laravel (config/cors.php) dibatasi hanya mengizinkan origin domain frontend resmi (https://growthcoder.id), mencegah konsumsi API dari domain yang tidak dikenal.

5.3.3 Validasi Form & Input Sanitization

Seluruh input dari form publik (terutama Form Kontak) divalidasi di sisi server menggunakan Laravel Form Request, mencakup validasi tipe data, panjang maksimal karakter, dan format (mis. email).

Validasi sisi klien (Nuxt UI form validation) tetap diimplementasikan untuk pengalaman pengguna yang responsif, namun TIDAK PERNAH dianggap sebagai satu-satunya lapisan keamanan — validasi server-side bersifat final dan wajib.

Form kontak menyertakan honeypot field tersembunyi (field yang tidak terlihat manusia namun sering diisi oleh bot otomatis) sebagai lapisan anti-spam tambahan yang ringan tanpa mengganggu UX (alternatif/pelengkap dari CAPTCHA visual).

Konten rich text yang diinput admin melalui WYSIWYG editor (case study, post blog) disanitasi sebelum disimpan/ditampilkan untuk mencegah potensi XSS, meskipun risiko relatif rendah karena hanya admin tunggal yang memiliki akses tulis ke editor tersebut.

6. PUBLIC FRONTEND PAGES & ROUTING STRUCTURE

Bagian ini mendetailkan struktur rute (file-based routing Nuxt 4), kebutuhan tampilan, dan komponen Nuxt UI yang relevan untuk setiap halaman publik utama.

6.1 Peta Routing Utama

Berikut adalah pemetaan rute halaman publik (file-based routing) di sisi Nuxt:

| Rute URL | Path File Nuxt | Metode Render | Deskripsi |
| :--- | :--- | :--- | :--- |
| `/` | `pages/index.vue` | SSR | Halaman Beranda (Hero, Tech Stack, Layanan, Proyek Featured, Blog Terbaru) |
| `/about` | `pages/about.vue` | SSR | Halaman Tentang Saya & Resume (Pendidikan, Pengalaman, Keahlian Lengkap) |
| `/proyek` | `pages/proyek/index.vue` | SSR | Listing studi kasus proyek dengan filter kategori dinamis |
| `/proyek/[slug]` | `pages/proyek/[slug].vue` | SSR | Detail studi kasus proyek (narasi, galeri, diagram arsitektur) |
| `/blog` | `pages/blog/index.vue` | SSR | Listing artikel blog dengan kategori taksonomi |
| `/blog/kategori/[slug]`| `pages/blog/kategori/[slug].vue`| SSR | Listing artikel berdasarkan kategori terpilih (SEO-friendly URL) |
| `/blog/[slug]` | `pages/blog/[slug].vue` | SSR | Detail artikel blog dengan related posts dan estimasi waktu baca |

6.2 Halaman: Beranda (Home)

6.2.1 Struktur Tampilan

Hero Section — Menampilkan foto profil, headline & sub-headline dinamis (dari Site Settings), tombol CTA utama ("Lihat Proyek" / "Hubungi Saya"), dan tombol unduh CV.

Tech Stack Highlight — Ringkasan visual berupa logo dan badge teknologi utama yang dikuasai yang ditandai `is_featured = true` di database, diambil secara dinamis melalui API `GET /api/v1/technologies?featured=1`.

Services Highlight Section — Menampilkan kartu-kartu layanan profesional yang ditawarkan secara dinamis (diambil melalui API `GET /api/v1/services?active=1` dan diurutkan berdasarkan kolom `order`).

Featured Projects Section — Menampilkan 3-4 proyek yang ditandai is_featured = true dalam bentuk card grid, dengan tautan "Lihat Semua Proyek" mengarah ke /proyek.

Latest Blog Posts Section — Menampilkan 3 artikel terbaru (berdasarkan published_at) dalam bentuk card, dengan tautan "Lihat Semua Artikel" mengarah ke /blog.

Contact Section / CTA — Ringkasan form kontak singkat atau tombol mengarah ke halaman kontak, beserta ikon tautan media sosial.

6.2.2 Komponen Nuxt UI yang Relevan

6.3 Halaman: List Proyek

6.3.1 Struktur Tampilan

Page Header — Judul halaman ("Proyek & Studi Kasus") beserta deskripsi singkat tujuan halaman.

Filter Bar — Filter berdasarkan project_type (Web App, Mobile App, Telegram Bot, dst.) dan/atau tech stack tertentu, diimplementasikan sebagai client-side filter atau query parameter (mis. ?type=telegram_bot) yang memicu refetch data dari API.

Grid Card Proyek — Menampilkan seluruh proyek published dalam grid responsif (mis. 3 kolom desktop, 1 kolom mobile), masing-masing card menampilkan thumbnail, judul, badge tipe proyek, ringkasan singkat, dan badge beberapa tech stack utama.

Paginasi — Jika jumlah proyek cukup banyak, gunakan paginasi (page-based) atau infinite scroll, dengan parameter URL yang tetap crawlable (mis. ?page=2) dan disertai canonical/pagination meta tag yang sesuai.

6.3.2 Komponen Nuxt UI yang Relevan

6.4 Halaman: Detail Proyek (Studi Kasus)

6.4.1 Struktur Tampilan

Header Studi Kasus — Judul proyek, badge tipe proyek, badge seluruh tech stack yang digunakan, serta tautan aksi (Live Demo, Source Code, atau link Bot Telegram sesuai tipe proyek).

Galeri Multimedia — Carousel atau grid gambar dari project_images, menggunakan komponen gambar dengan lazy loading dan dapat diperbesar (lightbox) untuk melihat detail visual antarmuka proyek.

Konten Studi Kasus Naratif — Render rich HTML dari case_study_content (masalah, solusi, proses pengerjaan), terstruktur dengan heading yang jelas untuk keterbacaan dan SEO on-page.

Diagram Arsitektur Sistem — Ditampilkan sebagai gambar (dari architecture_diagram_media_id) pada bagian yang relevan dalam narasi, membantu audiens teknikal memahami pendekatan sistem yang dibangun.

Navigasi Proyek Lain — Tautan "Proyek Sebelumnya/Selanjutnya" atau rekomendasi proyek lain dengan tipe/tech stack serupa di bagian akhir halaman (mendukung engagement & internal linking).

CTA Penutup — Mengarahkan pembaca (terutama calon klien) untuk menghubungi melalui form kontak setelah membaca studi kasus yang meyakinkan.

6.4.2 Komponen Nuxt UI yang Relevan

6.5 Halaman: List Blog (dengan Filter Kategori)

6.5.1 Struktur Tampilan

Page Header — Judul halaman ("Blog & Artikel") beserta deskripsi singkat tema konten yang dibahas.

Filter/Navigasi Kategori — Daftar kategori (chip/tab) yang dapat diklik untuk memfilter artikel; mengarah ke rute taksonomi /blog/kategori/[slug] secara terpisah untuk keperluan SEO (URL unik per kategori, bukan hanya query parameter di sisi klien).

Grid/List Card Artikel — Menampilkan seluruh artikel published, masing-masing card menampilkan featured image, judul, excerpt, badge kategori, estimasi waktu baca, dan tanggal publikasi.

Paginasi — Sama seperti List Proyek, menggunakan paginasi crawlable untuk artikel yang jumlahnya bertambah seiring waktu.

6.5.2 Komponen Nuxt UI yang Relevan

6.6 Halaman: Detail Blog

6.6.1 Struktur Tampilan

Header Artikel — Judul lengkap, badge kategori (dapat diklik, mengarah ke halaman taksonomi kategori tersebut), nama penulis, tanggal publikasi, dan estimasi waktu baca.

Featured Image — Gambar utama artikel, ditampilkan dengan prioritas loading tinggi (above-the-fold) karena turut memengaruhi LCP.

Konten Artikel — Render rich HTML dari field content, dengan styling tipografi yang nyaman dibaca (line-height, max-width kontainer teks, syntax highlighting untuk code block teknis).

Share Buttons (opsional) — Tombol berbagi cepat ke media sosial/Telegram untuk mendorong distribusi organik konten.

Related Posts Section — Menampilkan 3-4 artikel terkait (berdasarkan kategori atau override manual admin) di bagian akhir halaman, krusial untuk strategi internal linking SEO (lihat Modul 4.2).

Breadcrumb — Navigasi (Beranda > Blog > [Kategori] > [Judul Artikel]) untuk membantu pengguna dan crawler memahami struktur hierarki konten.

6.6.2 Komponen Nuxt UI yang Relevan

6.7 Halaman: Tentang Saya & Riwayat (About & Resume)

6.7.1 Struktur Tampilan
- Profil Detail — Menampilkan foto profil formal/semi-formal, biografi ringkas profesional, serta tautan unduh CV (PDF) terintegrasi dari Modul Pengaturan Global.
- Timeline Riwayat Pendidikan & Pengalaman (Education & Experience) — Rangkaian kronologis riwayat hidup, memisahkan secara jelas antara institusi pendidikan dan karir pekerjaan yang diambil dinamis melalui API `GET /api/v1/experiences` (diurutkan berdasarkan tanggal mulai terbaru). Menampilkan logo institusi, rentang tahun, jabatan, dan deskripsi singkat pencapaian.
- Papan Keahlian (Skills Board) — Menampilkan seluruh data keterampilan teknis yang dikuasai dikelompokkan berdasarkan kategori (Frontend, Backend, Database, DevOps, Tools). Masing-masing dilengkapi visual progress bar persentase tingkat kemahiran (proficiency_level) dan estimasi tahun pengalaman (`years_of_experience`), diambil dinamis melalui API `GET /api/v1/skills`.

6.7.2 Komponen Nuxt UI yang Relevan

PENUTUP

Dokumen Product Requirement Document (PRD) ini disusun sebagai acuan teknis dan fungsional tunggal untuk pengembangan platform growthcoder.id. Seluruh skema data, alur sistem, dan kebutuhan non-fungsional yang dijabarkan di atas dirancang agar dapat langsung diterjemahkan menjadi migration database, struktur API Resource Laravel, serta komponen halaman Nuxt 4 tanpa perlu interpretasi tambahan yang signifikan.

Dokumen ini bersifat hidup (living document) dan dapat diperbarui seiring berjalannya proses development apabila ditemukan kebutuhan baru atau penyesuaian teknis di lapangan, selama perubahan tersebut tetap selaras dengan visi dan tujuan utama yang telah ditetapkan pada Bagian 1.

— Akhir Dokumen —