# US-02 — Modul Blog & Manajemen Kategori / Blog & Category Management

> **Referensi PRD:** Section 4.2
> **Tabel Database Utama:** `posts`, `categories`, `post_category` (pivot), `post_related` (self-ref pivot), `media`

---

## Gambaran Modul / Module Overview

**ID:** Modul blog dirancang sebagai mesin pertumbuhan SEO organik jangka panjang. Fokus utama adalah relasi dinamis antara post dan kategori yang mendukung strategi internal linking — memastikan setiap artikel saling terhubung secara tematik. Sistem menghitung estimasi waktu baca otomatis dan mendukung override meta SEO per artikel.

**EN:** The blog module is designed as a long-term organic SEO growth engine. The main focus is the dynamic relationship between posts and categories supporting an internal linking strategy — ensuring every article is thematically connected. The system calculates estimated reading time automatically and supports per-article SEO meta override.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Pemilik platform, menulis dan mengelola seluruh konten blog | Platform owner, writes and manages all blog content |
| **Pengunjung Umum** | Membaca artikel blog di halaman publik | Reads blog articles on the public pages |

---

## Daftar User Story / User Story List

---

### US-02-001: Membuat Artikel Blog Baru / Create New Blog Post

**Sebagai / As a:** Administrator
**Saya ingin / I want:** membuat artikel blog baru dengan judul, konten, kategori, dan meta SEO melalui panel CMS / create a new blog post with title, content, categories, and SEO meta through the CMS panel
**Agar / So that:** saya dapat mempublikasikan konten teknis berkualitas yang membantu pertumbuhan trafik organik / I can publish quality technical content that helps grow organic traffic

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 8 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Membuat Artikel Blog Baru / Create New Blog Post

  Scenario: Admin berhasil membuat post dengan status draft
  / Admin successfully creates a post with draft status
    Given Admin telah login ke panel CMS / Admin is logged into the CMS panel
    When Admin membuka halaman "Tambah Artikel Baru" / Admin opens "Add New Article" page
    And Admin mengisi: judul, pilih minimal 1 kategori, tulis konten di rich text editor
    / Admin fills: title, selects at least 1 category, writes content in rich text editor
    And Admin menyimpan dengan status "Draft" / Admin saves with "Draft" status
    Then Post tersimpan ke tabel `posts` dengan `status = 'draft'`
    / Post is saved to `posts` table with `status = 'draft'`
    And Relasi kategori tersimpan di tabel `post_category`
    / Category relations are saved in `post_category` table
    And Sistem otomatis menghitung dan mengisi `reading_time` berdasarkan jumlah kata
    / System automatically calculates and fills `reading_time` based on word count
    And Slug otomatis di-generate dari judul
    / Slug is auto-generated from the title

  Scenario: Validasi — Post tidak dapat disimpan tanpa judul dan kategori
  / Validation — Post cannot be saved without title and category
    Given Admin membuka form artikel baru / Admin opens new article form
    When Admin mengklik "Simpan" tanpa mengisi judul atau kategori
    / Admin clicks "Save" without filling in title or category
    Then Sistem menampilkan pesan validasi untuk field yang kosong
    / System displays validation messages for empty fields
    And Post tidak tersimpan / Post is not saved
```

#### Referensi Teknis / Technical References

**Tabel:** `posts`
```
id, title, slug, excerpt (auto/manual), content (rich text HTML),
status (enum: draft|published|scheduled), published_at (timestamp),
scheduled_at (timestamp nullable), cover_image_id (FK → media.id),
reading_time (integer, menit — auto calculated), 
meta_title (varchar nullable), meta_description (text nullable),
created_at, updated_at
```

---

### US-02-002: Menjadwalkan Publikasi Artikel / Schedule Article Publication

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menjadwalkan tanggal dan waktu publikasi artikel di masa depan / schedule a future publication date and time for an article
**Agar / So that:** saya dapat mempersiapkan dan mengatur konten terlebih dahulu tanpa harus online di saat artikel harus dipublikasikan / I can prepare and organize content in advance without needing to be online when the article should be published

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Penjadwalan Publikasi Artikel / Article Publication Scheduling

  Scenario: Admin menjadwalkan artikel untuk dipublikasikan di masa depan
  / Admin schedules an article for future publication
    Given Admin sedang mengedit artikel baru atau yang sudah ada
    / Admin is editing a new or existing article
    When Admin memilih status "Scheduled" dan memilih tanggal/waktu di masa depan
    / Admin selects "Scheduled" status and picks a future date/time
    And Admin menyimpan perubahan / Admin saves changes
    Then Post tersimpan dengan `status = 'scheduled'` dan `scheduled_at` = waktu yang dipilih
    / Post is saved with `status = 'scheduled'` and `scheduled_at` = selected time
    And Artikel tidak muncul di halaman publik hingga waktu jadwal tiba
    / Article does not appear on public pages until the scheduled time arrives

  Scenario: Sistem otomatis mempublikasikan artikel sesuai jadwal
  / System automatically publishes the article according to schedule
    Given Ada artikel dengan `status = 'scheduled'` dan `scheduled_at` yang sudah lewat
    / There is an article with `status = 'scheduled'` and a `scheduled_at` in the past
    When Laravel Scheduler (Cron) berjalan / Laravel Scheduler (Cron) runs
    Then Artikel diperbarui menjadi `status = 'published'` dan `published_at` = `scheduled_at`
    / Article is updated to `status = 'published'` and `published_at` = `scheduled_at`
    And Artikel muncul di halaman listing blog publik
    / Article appears on the public blog listing page
```

#### Referensi Teknis / Technical References

**Tabel:** `posts` (kolom `status` enum: `draft|published|scheduled`, `scheduled_at`)
**Implementasi:** Laravel Scheduler dengan Command `PublishScheduledPosts` — dijalankan tiap menit via `php artisan schedule:run`

---

### US-02-003: Menulis Konten Blog dengan Rich Text Editor / Write Blog Content with Rich Text Editor

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menulis konten artikel menggunakan rich text editor yang mendukung struktur heading SEO, code block dengan syntax highlighting, dan penyisipan gambar dari Media Library / write article content using a rich text editor that supports SEO heading structure, code blocks with syntax highlighting, and image insertion from Media Library
**Agar / So that:** artikel blog saya terstruktur dengan baik untuk SEO on-page sekaligus nyaman dibaca oleh audiens teknikal / my blog articles are well-structured for on-page SEO while being comfortable to read for a technical audience

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Rich Text Editor untuk Konten Blog / Rich Text Editor for Blog Content

  Scenario: Editor menyediakan elemen formatting yang diperlukan
  / Editor provides required formatting elements
    Given Admin membuka form artikel blog / Admin opens the blog article form
    Then Rich text editor menampilkan toolbar dengan elemen:
    / Rich text editor displays toolbar with elements:
      - Heading H2, H3, H4 (H1 tidak tersedia karena sudah menjadi judul artikel)
      / Heading H2, H3, H4 (H1 is unavailable as it's already the article title)
      - Bold, Italic, Strikethrough
      - Ordered list, Unordered list
      - Blockquote
      - Code block dengan pilihan bahasa (PHP, JS, Bash, JSON, dll.) + syntax highlighting
      / Code block with language selection (PHP, JS, Bash, JSON, etc.) + syntax highlighting
      - Inline code
      - Hyperlink dengan opsi open in new tab
      / Hyperlink with open in new tab option
      - Sisipkan gambar dari Media Library / Insert image from Media Library
      - Divider / Horizontal Rule

  Scenario: Konten disimpan dalam format HTML yang bersih
  / Content is saved in clean HTML format
    Given Admin selesai menulis konten / Admin finishes writing content
    When Admin menyimpan artikel / Admin saves the article
    Then Konten tersimpan sebagai HTML yang disanitasi (bebas script berbahaya)
    / Content is saved as sanitized HTML (free of harmful scripts)
    And Frontend publik me-render HTML tersebut dengan styling yang sesuai
    / Public frontend renders the HTML with appropriate styling
```

---

### US-02-004: Mengatur Kategori Artikel Blog / Manage Blog Article Categories

**Sebagai / As a:** Administrator
**Saya ingin / I want:** melakukan CRUD pada kategori blog dan menghubungkan setiap artikel ke satu atau lebih kategori (many-to-many) / perform CRUD on blog categories and link each article to one or more categories (many-to-many)
**Agar / So that:** konten blog terorganisir dalam taksonomi yang mendukung internal linking dan navigasi pengunjung berdasarkan topik / blog content is organized in a taxonomy that supports internal linking and visitor navigation by topic

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Manajemen Kategori Blog / Blog Category Management

  Scenario: Admin membuat kategori blog baru
  / Admin creates a new blog category
    Given Admin berada di halaman manajemen kategori blog
    / Admin is on the blog category management page
    When Admin mengklik "Tambah Kategori" dan mengisi nama kategori
    / Admin clicks "Add Category" and fills in the category name
    Then Slug otomatis di-generate / Slug is auto-generated
    And Kategori tersimpan ke tabel `categories`
    / Category is saved to the `categories` table

  Scenario: Admin menghubungkan artikel ke beberapa kategori
  / Admin links an article to multiple categories
    Given Admin sedang mengedit artikel blog / Admin is editing a blog article
    When Admin memilih beberapa kategori dari multi-select / Admin selects multiple categories from multi-select
    And Admin menyimpan / Admin saves
    Then Relasi tersimpan di tabel pivot `post_category`
    / Relations are saved in the `post_category` pivot table

  Scenario: Sistem mencegah penghapusan kategori yang masih digunakan
  / System prevents deletion of a category that is still in use
    Given Kategori "Laravel" masih digunakan oleh 5 post
    / Category "Laravel" is still used by 5 posts
    When Admin mencoba menghapus kategori "Laravel"
    / Admin tries to delete the "Laravel" category
    Then Sistem menampilkan peringatan: "Kategori ini digunakan oleh 5 artikel. Hapus semua relasi terlebih dahulu sebelum menghapus kategori."
    / System displays warning: "This category is used by 5 articles. Remove all relations first before deleting the category."
    And Penghapusan dibatalkan / Deletion is cancelled
```

#### Referensi Teknis / Technical References

**Tabel:** `categories`
```
id, name, slug, description (text nullable), 
meta_title (varchar nullable), meta_description (text nullable),
created_at, updated_at
```
**Tabel:** `post_category` (pivot): `post_id`, `category_id`

---

### US-02-005: Mengatur Meta SEO per Artikel / Set Per-Article SEO Meta

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur meta title dan meta description khusus untuk setiap artikel blog, terpisah dari judul tampilan / set a custom meta title and meta description for each blog article, separate from the display title
**Agar / So that:** setiap artikel dapat dioptimalkan secara individual untuk target keyword tertentu di mesin pencari / each article can be individually optimized for specific target keywords in search engines

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Pengaturan Meta SEO per Artikel / Per-Article SEO Meta Settings

  Scenario: Admin mengisi override meta SEO untuk artikel
  / Admin fills in SEO meta override for an article
    Given Admin sedang mengedit artikel blog / Admin is editing a blog article
    When Admin membuka section "SEO Settings" / Admin opens the "SEO Settings" section
    Then Section menampilkan field: Meta Title (varchar, max 60 char), Meta Description (text, max 160 char), dan preview snippet Google Search
    / Section displays fields: Meta Title (varchar, max 60 chars), Meta Description (text, max 160 chars), and Google Search snippet preview
    When Admin mengisi Meta Title dan Meta Description
    / Admin fills in Meta Title and Meta Description
    And Admin menyimpan artikel / Admin saves the article
    Then Nilai tersimpan di kolom `meta_title` dan `meta_description` pada tabel `posts`
    / Values are saved in `meta_title` and `meta_description` columns in the `posts` table
    And Frontend Nuxt menggunakan nilai override ini untuk `useSeoMeta` pada halaman detail artikel
    / Nuxt frontend uses these override values for `useSeoMeta` on the article detail page

  Scenario: Fallback ke judul dan excerpt jika meta SEO tidak diisi
  / Fallback to title and excerpt if SEO meta is not filled
    Given Admin tidak mengisi `meta_title` dan `meta_description`
    / Admin does not fill in `meta_title` and `meta_description`
    When Halaman detail artikel diakses oleh pengunjung/crawler
    / Article detail page is accessed by a visitor/crawler
    Then Nuxt menggunakan `title` sebagai `meta title` dan `excerpt` sebagai `meta description`
    / Nuxt uses `title` as `meta title` and `excerpt` as `meta description`
```

#### Referensi Teknis / Technical References

**Tabel:** `posts` (kolom `meta_title`, `meta_description`)
**Nuxt:** `useSeoMeta({ title: post.meta_title || post.title, description: post.meta_description || post.excerpt })`

---

### US-02-006: Mengelola Related Posts Manual / Manage Manual Related Posts

**Sebagai / As a:** Administrator
**Saya ingin / I want:** secara manual menentukan artikel-artikel terkait untuk artikel tertentu sebagai override dari rekomendasi otomatis berdasarkan kategori / manually specify related articles for a particular article as an override from automatic category-based recommendations
**Agar / So that:** saya dapat secara strategis mengarahkan pembaca ke artikel spesifik yang paling relevan untuk mendukung konversi atau pendalaman topik tertentu / I can strategically direct readers to specific articles most relevant to support conversions or topic deep-dives

| Atribut | Nilai |
|---|---|
| **Priority** | 🟢 Could Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Related Posts Manual Override / Manual Related Posts Override

  Scenario: Admin menambahkan related posts secara manual
  / Admin adds related posts manually
    Given Admin sedang mengedit artikel / Admin is editing an article
    When Admin membuka section "Artikel Terkait" / Admin opens the "Related Articles" section
    Then Sistem menampilkan artikel-artikel terkait otomatis (berdasarkan kategori yang sama) sebagai default
    / System displays auto-related articles (based on same category) as default
    And Admin dapat memilih tambahan artikel spesifik via search/multi-select
    / Admin can select additional specific articles via search/multi-select
    When Admin menyimpan pilihan related posts / Admin saves the related posts selection
    Then Relasi tersimpan di tabel `post_related`
    / Relations are saved in the `post_related` table
    And Frontend menampilkan related posts manual tersebut (bukan yang otomatis) di halaman detail artikel
    / Frontend displays the manual related posts (not the automatic ones) on the article detail page

  Scenario: Sistem menampilkan related posts otomatis jika tidak ada override
  / System displays automatic related posts if there is no override
    Given Artikel tidak memiliki related posts manual
    / Article has no manual related posts
    When Pengunjung membuka halaman detail artikel / Visitor opens the article detail page
    Then Frontend menampilkan 3 artikel terkait otomatis berdasarkan kesamaan kategori
    / Frontend displays 3 automatic related articles based on category similarity
```

#### Referensi Teknis / Technical References

**Tabel:** `post_related` (self-referencing M2M)
```
post_id         (FK → posts.id, Cascade Delete)
related_post_id (FK → posts.id, Cascade Delete)
```

---

### US-02-007: Kalkulasi Otomatis Estimasi Waktu Baca / Automatic Reading Time Calculation

**Sebagai / As a:** Administrator
**Saya ingin / I want:** sistem secara otomatis menghitung dan menampilkan estimasi waktu baca setiap artikel berdasarkan jumlah kata konten / have the system automatically calculate and display the estimated reading time for each article based on word count
**Agar / So that:** pembaca dapat memutuskan apakah mereka memiliki cukup waktu untuk membaca artikel sebelum membukanya (mengurangi bounce rate) / readers can decide if they have enough time to read the article before opening it (reducing bounce rate)

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Kalkulasi Waktu Baca Otomatis / Automatic Reading Time Calculation

  Scenario: Waktu baca dikalkulasi saat menyimpan artikel
  / Reading time is calculated when saving the article
    Given Admin selesai menulis konten artikel / Admin finishes writing article content
    When Admin menyimpan artikel / Admin saves the article
    Then Sistem menghitung jumlah kata dalam konten (stripped dari HTML tags)
    / System counts the number of words in the content (stripped from HTML tags)
    And Kalkulasi: `reading_time = ceil(word_count / 200)` (asumsi 200 kata/menit)
    / Calculation: `reading_time = ceil(word_count / 200)` (assuming 200 words/minute)
    And Nilai tersimpan di kolom `reading_time` (dalam satuan menit) pada tabel `posts`
    / Value is saved in `reading_time` column (in minutes) in the `posts` table

  Scenario: Waktu baca tampil di halaman publik
  / Reading time appears on the public page
    Given Artikel sudah dipublikasikan / Article is published
    When Pengunjung membuka halaman listing blog atau detail artikel
    / Visitor opens the blog listing page or article detail page
    Then Estimasi waktu baca ditampilkan, contoh: "5 menit membaca" / "5 min read"
    / Estimated reading time is displayed, e.g., "5 menit membaca" / "5 min read"
```

#### Referensi Teknis / Technical References

**Tabel:** `posts` (kolom `reading_time` — integer, dalam menit)
**Logic:** Dihitung di `PostObserver` atau di `StorePostRequest` sebelum menyimpan ke DB

---

### US-02-008: Menghapus Artikel Blog / Delete Blog Article

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus artikel blog yang sudah tidak relevan atau keliru / delete blog articles that are no longer relevant or were created in error
**Agar / So that:** konten blog tetap berkualitas dan tidak membingungkan pengunjung maupun mesin pencari / blog content remains high quality and doesn't confuse visitors or search engines

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Artikel Blog / Delete Blog Article

  Scenario: Admin menghapus artikel dengan konfirmasi
  / Admin deletes an article with confirmation
    Given Admin berada di daftar atau halaman edit artikel
    / Admin is on the article list or edit page
    When Admin mengklik "Hapus" / Admin clicks "Delete"
    Then Dialog konfirmasi muncul: "Hapus artikel ini secara permanen?"
    / Confirmation dialog appears: "Permanently delete this article?"
    When Admin mengonfirmasi / Admin confirms
    Then Record post dihapus dari tabel `posts`
    / Post record is deleted from the `posts` table
    And Relasi di `post_category` dan `post_related` ikut dihapus (Cascade)
    / Relations in `post_category` and `post_related` are also deleted (Cascade)
    And URL artikel mengembalikan 404 di frontend publik
    / Article URL returns 404 on the public frontend
```

---

### US-02-009: Pengunjung Membaca Artikel Blog / Visitor Reads Blog Article

**Sebagai / As a:** Pengunjung Umum
**Saya ingin / I want:** membaca artikel blog secara nyaman dengan konten yang ter-render dengan baik, dilengkapi informasi waktu baca dan artikel terkait / read blog articles comfortably with well-rendered content, equipped with reading time information and related articles
**Agar / So that:** saya mendapatkan nilai dari konten teknis yang dibagikan dan terdorong untuk menjelajahi lebih banyak artikel / I get value from the shared technical content and am encouraged to explore more articles

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Membaca Artikel Blog Publik / Reading Public Blog Article

  Scenario: Pengunjung membuka halaman detail artikel
  / Visitor opens the article detail page
    Given Pengunjung mengakses /blog/{slug} yang valid
    / Visitor accesses a valid /blog/{slug}
    Then Halaman di-render via SSR dengan HTML lengkap
    / Page is rendered via SSR with full HTML
    And Halaman menampilkan: judul, cover image, tanggal publikasi,
    estimasi waktu baca, daftar kategori, konten lengkap (HTML rendered),
    dan section "Artikel Terkait" (3 artikel)
    / Page displays: title, cover image, publication date,
    estimated reading time, category list, full content (rendered HTML),
    and "Related Articles" section (3 articles)
    And Meta tag SEO (title, description, og:image) diisi dari data artikel
    / SEO meta tags (title, description, og:image) are filled from article data
    And JSON-LD schema `BlogPosting` disuntikkan ke halaman
    / JSON-LD `BlogPosting` schema is injected into the page

  Scenario: Pengunjung memfilter artikel berdasarkan kategori
  / Visitor filters articles by category
    Given Pengunjung berada di halaman /blog / Visitor is on the /blog page
    When Pengunjung mengklik label kategori pada sebuah artikel
    / Visitor clicks a category label on an article
    Then Pengunjung diarahkan ke /blog/kategori/{slug}
    / Visitor is directed to /blog/kategori/{slug}
    And Halaman menampilkan hanya artikel dengan kategori tersebut
    / Page displays only articles with that category
```

#### Referensi Teknis / Technical References

**API Endpoint:** `GET /api/v1/posts/{slug}` — hanya artikel `published`
**Nuxt Pages:** `pages/blog/[slug].vue`, `pages/blog/kategori/[slug].vue`
**SEO:** JSON-LD `BlogPosting` schema via `useHead`

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-02-001 | Membuat Artikel Blog Baru | Must Have | 8 |
| US-02-002 | Menjadwalkan Publikasi | Should Have | 5 |
| US-02-003 | Rich Text Editor Konten | Must Have | 5 |
| US-02-004 | Manajemen Kategori Blog | Must Have | 5 |
| US-02-005 | Meta SEO per Artikel | Must Have | 3 |
| US-02-006 | Related Posts Manual | Could Have | 3 |
| US-02-007 | Kalkulasi Waktu Baca | Should Have | 2 |
| US-02-008 | Hapus Artikel Blog | Must Have | 2 |
| US-02-009 | Pengunjung Baca Artikel | Must Have | 3 |
| | **Total** | | **36** |
