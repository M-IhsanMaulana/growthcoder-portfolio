# Gambaran Desain & Arsitektur UI — Nuxt 4 Frontend

Dokumen ini menjelaskan konsep desain, tipografi, struktur grid responsif, mikro-interaksi, dan transisi UI yang akan diterapkan pada **Public Frontend** **growthcoder.id** agar menyajikan pengalaman premium, bersih, modern, dan sangat responsif.

---

## 1. Konsep Estetika & Visual

Platform ini mengusung tema **Modern Developer Portfolio** dengan pendekatan *Clean Minimalist & Subtle Glassmorphism*.
* **Aura Premium:** Menggunakan kombinasi warna gelap navy (`#2d2a6f`) sebagai jangkar (anchor) utama, hijau emerald (`#2bb673`) sebagai warna aksen interaktif, dan latar belakang ultra-bersih (light mode) atau ultra-gelap (dark mode).
* **Glassmorphism:** Navbar dan kartu-kartu konten tertentu akan menggunakan efek border tipis transparan (`rgba(229, 231, 235, 0.4)`) dengan filter `backdrop-blur-md` untuk memberikan kedalaman (depth) visual.
* **Micro-Shadows:** Shadow sangat tipis pada card element untuk memberi efek melayang (floating effect) tanpa merusak kebersihan tata letak.

---

## 2. Tipografi & Hierarki Font

Untuk menjaga konsistensi dengan panel admin CMS dan menjamin keterbacaan yang sangat baik, kita menetapkan konfigurasi font global:

* **Font Utama (Body):** **Instrument Sans** (atau alternatif Google Font **Inter / Outfit**)
  * Menawarkan keterbacaan tinggi pada layar mobile maupun desktop untuk artikel panjang.
* **Font Judul (Headings):** **Outfit** (atau **Instrument Sans** dengan bobot tebal/bold)
  * Memiliki karakter geometri yang modern, memberikan kesan solid dan tegas pada teks headline.

### Standar Ukuran Font & Spasi (Scale)
* **Hero Headline (H1):** `text-4xl md:text-6xl font-bold tracking-tight leading-tight`
* **Section Heading (H2):** `text-2xl md:text-3.5xl font-bold tracking-tight`
* **Card Title (H3):** `text-xl font-semibold`
* **Body Text:** `text-base md:text-lg leading-relaxed text-muted-foreground`
* **Metadata/Muted:** `text-sm font-medium text-muted`

---

## 3. Tata Letak Halaman (Wireframe Konseptual)

### A. Halaman Beranda (Home)
```
+-------------------------------------------------------------+
| [GC] Logo (Left)                 Menu (Center)  [Toggle L/D]|  <- Glassmorphism Navbar (Fixed)
+-------------------------------------------------------------+
|                                                             |
|   [Foto Profil Bulat dengan Efek Gradient Border]            |
|   Selamat Datang! Saya Muhammad Ihsan Maulana               |
|   FULL-STACK DEVELOPER & AUTOMATION SPECIALIST              |
|                                                             |
|   [Unduh CV (Primary Button)]    [Lihat Proyek (Secondary)]  |
|                                                             |
+-------------------------------------------------------------+
|   KEUNGGULAN TECH STACK (Featured Tech Stack Grid)          |
|   [ Laravel ]   [ Nuxt 3/4 ]   [ Vue 3 ]   [ Tailwind ]     | <- Hover scale & glow effects
+-------------------------------------------------------------+
|   LAYANAN JASA (Services Card Grid)                         |
|   +-------------------+  +-------------------+              |
|   | Web Development   |  | Bot Integration   |              |
|   | Detail...         |  | Detail...         |              |
|   +-------------------+  +-------------------+              |
+-------------------------------------------------------------+
|   STUDI KASUS UNGGULAN (Featured Projects Cards)            |
|   +-----------------------------------------------+         |
|   | [Image Cover WebP Varian Thumbnail]            |         |
|   | Kategori | Judul Proyek                       |         |
|   | Ringkasan Singkat...                          |         |
|   | [Badge Laravel] [Badge Vue]                   |         |
|   +-----------------------------------------------+         |
+-------------------------------------------------------------+
|   FORM KONTAK & OTOMASI                                    |
|   Nama:   [______________]                                  |
|   Email:  [______________]                                  |
|   Pesan:  [_______________________________________]         |
|   [ Kirim Pesan (Kirim ke Telegram Bot Instan) ]            |
+-------------------------------------------------------------+
```

### B. Halaman Resume & Riwayat (About & Resume)
* **Layout Kolom Dua:**
  * **Kiri (1/3 lebar):** Informasi Biografi Singkat, Foto Profil, tombol sosial media, status ketersediaan kerja (*"Available for Freelance Projects"*).
  * **Kanan (2/3 lebar):**
    * **Tab Menu:** [Pengalaman Kerja] | [Pendidikan]
    * **Timeline Antarmuka:** Garis vertikal dengan dot berwarna hijau aksen (`--brand-accent`). Setiap entri berisi Logo Institusi di sebelah kiri, Jabatan/Jurusan di atas, dan Poin Pencapaian di bawah.
    * **Skills Section:** Bilah kemajuan horizontal (*progress bar*) yang memiliki animasi pengisian dari kiri ke kanan saat masuk ke viewport (*scroll-triggered*).

### C. Halaman Detail Studi Kasus Proyek (Project Detail)
* **Header:** Full-width dengan latar belakang gelap/redup, menampilkan judul studi kasus berukuran besar, tag kategori, dan tautan langsung (Live Link / GitHub).
* **Multimedia Showcase:** Grid kolaboratif yang menggabungkan mockup desktop dan mobile dengan fitur *lightbox zoom*.
* **Naratif Studi Kasus:** Kontainer berukuran maksimal `max-w-3xl` (optimal untuk kenyamanan membaca), menggunakan font styling tipografi premium `.prose`.

---

## 4. Mikro-Interaksi & Animasi Bertenaga GSAP (Aesthetic Wow-Factor)

Faktor utama yang membedakan UI premium dari template biasa adalah kedalaman transisi dan interaksi halus yang diimplementasikan menggunakan **GSAP (GreenSock Animation Platform)**:

1. **Splash Screen Intro (Initial Load):**
   * Saat pertama kali web dibuka, overlay full-screen (`#0b0f19` - dark navy) menutupi layar.
   * Logo/teks `growthcoder.id` di tengah beranimasi: stroke-drawing (jika menggunakan SVG) atau pengetikan/stagger-reveal per karakter menggunakan GSAP.
   * Setelah selesai, overlay bergeser ke atas (`yPercent: -100` dengan easing `power4.inOut`) untuk memperlihatkan beranda. Konten homepage kemudian dipicu masuk (fade-in + slide-up) secara bertingkat (staggered).

2. **Loading Transition (Route-to-Route Load):**
   * Di antara rute navigasi Nuxt, loader transisi berupa garis progress bar premium di bagian atas layar atau overlay transisi transparan meluncur melintasi layar menggunakan GSAP timeline. Ini disematkan pada middleware/plugin navigasi Nuxt untuk memastikan transisi terasa "hidup" pada setiap pergantian halaman.

3. **Scroll-Triggered Reveals (Section & Card Entrance):**
   * Seluruh seksi (Hero, Tech Stack, Services, Projects, Timeline, Skills) tidak muncul kaku, melainkan menggunakan `gsap.from()` dengan `ScrollTrigger`.
   * **Staggered Entrance**: Elemen grid (kartu layanan, baris teknologi, dan detail blog) muncul satu demi satu dengan selisih waktu (`stagger: 0.08` detik), naik 30px dari bawah, dan opacity naik dari 0 ke 1.
   * **Timeline Draw**: Pada halaman about, garis vertikal timeline digambar secara dinamis mengikuti pergerakan scroll.

4. **Efek Hover Kartu & Tombol Utama:**
   * **Card Hover**: Transisi CSS `duration-300 ease-out` dikombinasikan dengan sentuhan GSAP (jika diperlukan efek magnetik/rotasi 3D ringan pada kartu-kartu unggulan).
   * **Magnetic Button Effect**: Tombol CTA utama (misal "Unduh CV" dan "Kirim Pesan") mengikuti posisi kursor secara halus saat kursor berada dekat tombol tersebut, memberikan kesan magnetis yang responsif.
   * **Progress Bar Skill**: Mengisi bar kemahiran dari `scaleX: 0` ke target kemahiran ketika seksi board skill masuk area pandang, dikendalikan oleh GSAP ScrollTrigger.


---

## 5. Strategi Layout Responsif

Menerapkan pendekatan *Mobile-First Grid System*:

* **Grid Proyek/Blog:**
  * Mobile (`< 640px`): 1 kolom (fokus pada gambar besar dan tombol CTA lebar penuh).
  * Tablet (`640px - 1024px`): 2 kolom dengan jarak elemen (*gap*) moderat.
  * Desktop (`> 1024px`): 3 kolom untuk memaksimalkan area visual di layar lebar.
* **Bilah Konten Naratif:**
  * Mengunci lebar konten artikel blog dan studi kasus pada rentang `max-w-3xl` (sekitar 750px) dengan margin kiri-kanan otomatis (`mx-auto`) untuk mencegah kelelahan mata akibat baris teks yang terlalu panjang.
* **Menu Mobile:**
  * Di layar mobile, menu navigasi akan disembunyikan di balik tombol hamburger yang jika diklik akan memicu menu *slide-over drawer* yang halus dari arah kanan layar.
