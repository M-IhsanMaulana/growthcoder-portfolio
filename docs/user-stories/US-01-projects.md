# US-01 — Modul Proyek & Studi Kasus / Projects & Case Studies

> **Referensi PRD:** Section 4.1
> **Tabel Database Utama:** `projects`, `project_technology` (pivot), `project_images` (pivot), `media`, `technologies`, `project_categories`

---

## Gambaran Modul / Module Overview

**ID:** Modul ini adalah etalase utama kapabilitas teknis pemilik produk. Setiap proyek disajikan sebagai studi kasus naratif lengkap — mencakup konteks masalah, solusi teknis, arsitektur sistem, galeri visual, dan tautan eksternal. Berbeda dari portofolio konvensional yang hanya menampilkan daftar link.

**EN:** This module is the primary showcase of the product owner's technical capabilities. Each project is presented as a complete narrative case study — including problem context, technical solution, system architecture, visual gallery, and external links. Unlike conventional portfolios that merely list links.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Pemilik platform, akses penuh ke seluruh fitur CMS | Platform owner with full access to all CMS features |
| **Pengunjung Umum** | Mengakses halaman portofolio publik via Nuxt.js | Accesses the public portfolio pages via Nuxt.js |
| **Calon Klien** | Pengunjung yang mengevaluasi kemampuan teknis developer | Visitor evaluating the developer's technical capabilities |
| **Recruiter** | Pengunjung dari perusahaan teknologi yang mencari kandidat | Visitor from tech companies looking for candidates |

---

## Daftar User Story / User Story List

---

### US-01-001: Membuat Entri Proyek Baru / Create New Project Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** membuat entri proyek baru dengan mengisi seluruh informasi studi kasus secara lengkap di panel CMS / create a new project entry by filling in the complete case study information in the CMS panel
**Agar / So that:** setiap proyek dapat dipublikasikan sebagai studi kasus naratif yang profesional di halaman portofolio publik / each project can be published as a professional narrative case study on the public portfolio page

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 8 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Membuat Entri Proyek Baru / Create New Project Entry

  Scenario: Admin berhasil membuat proyek baru dengan status draft
  / Admin successfully creates a new project with draft status
    Given Admin telah login ke panel CMS / Admin is logged into the CMS panel
    And Admin berada di halaman daftar proyek / Admin is on the project list page
    When Admin mengklik tombol "Tambah Proyek Baru" / Admin clicks "Add New Project" button
    And Admin mengisi field wajib: judul, slug, short_description, kategori, status (draft)
    / Admin fills in required fields: title, slug, short_description, category, status (draft)
    And Admin mengklik tombol "Simpan" / Admin clicks "Save" button
    Then Sistem menyimpan entri proyek baru dengan status 'draft' ke database
    / System saves the new project entry with 'draft' status to the database
    And Admin diredirect ke halaman edit proyek yang baru dibuat
    / Admin is redirected to the edit page of the newly created project
    And Pesan sukses "Proyek berhasil disimpan" ditampilkan
    / Success message "Project saved successfully" is displayed

  Scenario: Admin gagal menyimpan karena field wajib kosong
  / Admin fails to save due to empty required fields
    Given Admin sedang mengisi form proyek baru / Admin is filling in the new project form
    When Admin mengklik "Simpan" tanpa mengisi judul atau kategori
    / Admin clicks "Save" without filling in the title or category
    Then Sistem menampilkan pesan validasi di bawah field yang kosong
    / System displays validation message below the empty fields
    And Data tidak disimpan ke database / Data is not saved to the database

  Scenario: Slug otomatis di-generate dari judul
  / Slug is auto-generated from the title
    Given Admin mengisi field judul proyek / Admin fills in the project title field
    When Admin berpindah ke field berikutnya (blur dari field judul)
    / Admin moves to the next field (blur from title field)
    Then Field slug otomatis terisi dengan versi URL-friendly dari judul
    / The slug field is automatically populated with a URL-friendly version of the title
    And Admin masih dapat mengedit slug secara manual jika diperlukan
    / Admin can still manually edit the slug if needed
```

#### Referensi Teknis / Technical References

**Tabel:** `projects`
```
id, title, slug, short_description, full_description (rich text), 
category_id (FK → project_categories), status (enum: draft|published),
is_featured (boolean), order (integer), live_url, github_url, 
telegram_url, published_at, created_at, updated_at
```

---

### US-01-002: Mengedit Konten Naratif Studi Kasus / Edit Case Study Narrative Content

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menulis dan mengedit konten naratif studi kasus menggunakan rich text editor yang mendukung heading, list, blockquote, dan code snippet / write and edit case study narrative content using a rich text editor that supports headings, lists, blockquotes, and code snippets
**Agar / So that:** detail teknis proyek dapat disajikan dengan struktur yang rapi, mudah dibaca, dan informatif bagi pengunjung teknikal maupun non-teknikal / technical project details can be presented in a clean, readable, and informative structure for both technical and non-technical visitors

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Edit Konten Naratif dengan Rich Text Editor
/ Edit Narrative Content with Rich Text Editor

  Scenario: Admin memformat konten dengan berbagai elemen rich text
  / Admin formats content with various rich text elements
    Given Admin sedang mengedit halaman proyek yang sudah ada
    / Admin is editing an existing project page
    When Admin membuka bagian "Full Description" / Admin opens the "Full Description" section
    Then Rich text editor ditampilkan dengan toolbar yang mendukung:
    / Rich text editor is displayed with a toolbar supporting:
      - Heading H2, H3, H4
      - Bold, Italic, Underline
      - Ordered list dan Unordered list / Ordered and Unordered list
      - Blockquote
      - Code block (inline dan block) dengan syntax highlighting
      / Code block (inline and block) with syntax highlighting
      - Sisipkan gambar dari Media Library / Insert image from Media Library
    And Konten tersimpan dalam format HTML yang aman (sanitized)
    / Content is saved in sanitized HTML format

  Scenario: Admin menyisipkan gambar dari Media Library ke dalam konten
  / Admin inserts an image from Media Library into content
    Given Admin sedang menulis konten di rich text editor
    / Admin is writing content in the rich text editor
    When Admin mengklik tombol "Sisipkan Gambar" pada toolbar editor
    / Admin clicks the "Insert Image" button on the editor toolbar
    Then Modal Media Library terbuka / Media Library modal opens
    And Admin dapat memilih gambar yang sudah ada atau mengunggah baru
    / Admin can select an existing image or upload a new one
    When Admin memilih gambar dan mengklik "Sisipkan" / Admin selects image and clicks "Insert"
    Then Gambar disisipkan ke posisi kursor dalam editor dengan alt text yang sudah ada
    / Image is inserted at the cursor position in the editor with existing alt text
```

#### Referensi Teknis / Technical References

**Tabel:** `projects` (kolom `full_description` — tipe Text/HTML)
**Library:** Rich Text Editor (TipTap / Quill / ProseMirror) terintegrasi dengan Media Library

---

### US-01-003: Mengatur Status Publikasi Proyek / Manage Project Publication Status

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengubah status publikasi proyek antara 'draft' dan 'published' dengan mudah / change the project publication status between 'draft' and 'published' easily
**Agar / So that:** saya dapat mengerjakan konten proyek secara bertahap tanpa langsung mempublikasikannya ke halaman publik sebelum siap / I can work on project content incrementally without publishing it to the public page before it's ready

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Manajemen Status Publikasi / Publication Status Management

  Scenario: Admin mempublikasikan proyek yang sedang draft
  / Admin publishes a project that is currently a draft
    Given Admin melihat daftar proyek dengan status "Draft"
    / Admin views the project list with "Draft" status
    When Admin membuka proyek draft tersebut dan mengubah status ke "Published"
    / Admin opens the draft project and changes status to "Published"
    And Admin menyimpan perubahan / Admin saves the changes
    Then Status proyek berubah menjadi 'published' di database
    / Project status changes to 'published' in the database
    And Kolom `published_at` diisi dengan timestamp saat ini
    / The `published_at` column is filled with the current timestamp
    And Proyek kini dapat diakses melalui endpoint API publik
    / The project is now accessible through the public API endpoint
    And Proyek muncul di halaman listing portofolio publik
    / The project appears on the public portfolio listing page

  Scenario: Admin mengubah proyek published kembali ke draft
  / Admin reverts a published project back to draft
    Given Proyek saat ini berstatus "Published" / Project is currently "Published"
    When Admin mengubah status ke "Draft" dan menyimpan
    / Admin changes status to "Draft" and saves
    Then Proyek tidak lagi muncul di endpoint API publik
    / Project no longer appears in the public API endpoint
    And Proyek tidak tampil di halaman listing portofolio publik
    / Project does not appear on the public portfolio listing page
```

#### Referensi Teknis / Technical References

**Tabel:** `projects` (kolom `status` — enum: `draft|published`, kolom `published_at` — timestamp nullable)
**API:** `GET /api/v1/projects` hanya mengembalikan data dengan `status = 'published'`

---

### US-01-004: Menandai Proyek sebagai Featured / Mark Project as Featured

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menandai proyek tertentu sebagai "Featured" / mark specific projects as "Featured"
**Agar / So that:** proyek terbaik saya ditampilkan secara prioritas di halaman Beranda untuk membuat kesan pertama yang kuat kepada pengunjung baru / my best projects are displayed with priority on the Home page to make a strong first impression on new visitors

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tandai Proyek sebagai Featured / Mark Project as Featured

  Scenario: Admin mengaktifkan flag featured pada proyek
  / Admin activates the featured flag on a project
    Given Admin sedang mengedit atau melihat daftar proyek
    / Admin is editing or viewing the project list
    When Admin mengaktifkan toggle/checkbox "Featured Project"
    / Admin activates the "Featured Project" toggle/checkbox
    And Admin menyimpan perubahan / Admin saves changes
    Then Kolom `is_featured` pada record proyek tersebut diset menjadi `true`
    / The `is_featured` column on the project record is set to `true`
    And Proyek tersebut muncul di section "Proyek Unggulan" pada halaman Beranda publik
    / The project appears in the "Featured Projects" section on the public Home page

  Scenario: Batasan jumlah proyek featured (opsional guideline)
  / Limit on number of featured projects (optional guideline)
    Given Sudah ada lebih dari 3 proyek yang ditandai featured
    / There are already more than 3 projects marked as featured
    When Admin menandai proyek ke-4 sebagai featured
    / Admin marks a 4th project as featured
    Then Sistem tetap mengizinkan (tidak ada hard limit) namun menampilkan
    pesan informatif "Saat ini terdapat X proyek featured. Disarankan maksimal 3."
    / System still allows (no hard limit) but displays informational message
    "Currently X featured projects. Recommended maximum is 3."
```

#### Referensi Teknis / Technical References

**Tabel:** `projects` (kolom `is_featured` — boolean, default: false)
**API:** `GET /api/v1/projects?featured=true` — endpoint khusus untuk section Beranda

---

### US-01-005: Mengelola Galeri Gambar Proyek / Manage Project Image Gallery

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menambahkan, mengatur urutan, dan menghapus gambar pada galeri proyek dengan memilih dari Media Library terpusat / add, reorder, and remove images in the project gallery by selecting from the centralized Media Library
**Agar / So that:** setiap studi kasus proyek memiliki dokumentasi visual yang kaya dan terorganisir (screenshot, diagram arsitektur, mockup UI) / each project case study has rich and organized visual documentation (screenshots, architecture diagrams, UI mockups)

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Manajemen Galeri Gambar Proyek / Project Image Gallery Management

  Scenario: Admin menambahkan gambar ke galeri proyek dari Media Library
  / Admin adds images to the project gallery from Media Library
    Given Admin sedang mengedit halaman proyek / Admin is editing a project page
    When Admin membuka tab "Galeri" dan mengklik "Tambah Gambar"
    / Admin opens the "Gallery" tab and clicks "Add Image"
    Then Modal Media Library muncul menampilkan seluruh gambar yang tersedia
    / Media Library modal appears showing all available images
    When Admin memilih satu atau lebih gambar dan mengklik "Tambahkan ke Galeri"
    / Admin selects one or more images and clicks "Add to Gallery"
    Then Gambar yang dipilih ditambahkan ke galeri proyek
    / Selected images are added to the project gallery
    And Relasi tersimpan di tabel `project_images` (pivot)
    / Relations are saved in the `project_images` (pivot) table

  Scenario: Admin mengatur urutan tampil gambar di galeri
  / Admin reorders images in the gallery
    Given Galeri proyek sudah memiliki minimal 2 gambar
    / Project gallery already has at least 2 images
    When Admin melakukan drag-and-drop untuk mengubah urutan gambar
    / Admin performs drag-and-drop to change image order
    Then Urutan baru tersimpan di kolom `order` tabel `project_images`
    / New order is saved in the `order` column of the `project_images` table
    And Frontend publik menampilkan gambar sesuai urutan yang baru
    / Public frontend displays images in the new order

  Scenario: Admin menghapus gambar dari galeri proyek
  / Admin removes an image from the project gallery
    Given Galeri proyek memiliki setidaknya 1 gambar
    / Project gallery has at least 1 image
    When Admin mengklik ikon hapus pada thumbnail gambar di galeri
    / Admin clicks the delete icon on an image thumbnail in the gallery
    Then Sistem menampilkan konfirmasi "Hapus gambar ini dari galeri proyek?"
    / System displays confirmation "Remove this image from the project gallery?"
    When Admin mengkonfirmasi penghapusan / Admin confirms deletion
    Then Relasi di tabel `project_images` dihapus (baris pivot dihapus)
    / Relation in `project_images` table is deleted (pivot row removed)
    And File gambar di Media Library TIDAK dihapus (hanya relasi yang hilang)
    / Image file in Media Library is NOT deleted (only the relation is removed)
```

#### Referensi Teknis / Technical References

**Tabel:** `project_images` (pivot)
```
project_id (FK → projects.id, Cascade Delete)
media_id   (FK → media.id, Set Null on Delete)
order      (integer, default: 0)
caption    (varchar, nullable) — keterangan gambar opsional
```

---

### US-01-006: Mengelola Tagging Tech Stack pada Proyek / Manage Tech Stack Tags on Project

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menandai setiap proyek dengan satu atau lebih teknologi dari daftar Master Tech Stack / tag each project with one or more technologies from the Master Tech Stack list
**Agar / So that:** pengunjung dapat melihat badge teknologi yang digunakan di setiap studi kasus dan memfilter proyek berdasarkan teknologi tertentu / visitors can see technology badges used in each case study and filter projects by specific technology

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tagging Tech Stack pada Proyek / Tech Stack Tagging on Project

  Scenario: Admin menambahkan tech stack tag ke proyek
  / Admin adds tech stack tags to a project
    Given Admin sedang mengedit proyek / Admin is editing a project
    And Master Tech Stack (Modul 07) sudah memiliki data teknologi
    / Master Tech Stack (Module 07) already has technology data
    When Admin membuka section "Tech Stack" pada form proyek
    / Admin opens the "Tech Stack" section on the project form
    Then Ditampilkan multi-select dropdown berisi daftar teknologi dari master data
    / Multi-select dropdown is displayed containing technology list from master data
    When Admin memilih beberapa teknologi (misal: Laravel, Vue.js, Inertia)
    / Admin selects several technologies (e.g., Laravel, Vue.js, Inertia)
    And Admin menyimpan proyek / Admin saves the project
    Then Relasi tersimpan di tabel pivot `project_technology`
    / Relations are saved in the `project_technology` pivot table
    And Badge teknologi ditampilkan di halaman detail proyek publik
    / Technology badges are displayed on the public project detail page

  Scenario: Admin menghapus tech stack tag dari proyek
  / Admin removes a tech stack tag from a project
    Given Proyek sudah memiliki tag teknologi yang terpasang
    / Project already has technology tags attached
    When Admin menghapus salah satu tag dari multi-select
    / Admin removes one tag from the multi-select
    And Admin menyimpan perubahan / Admin saves changes
    Then Relasi yang sesuai dihapus dari tabel `project_technology`
    / Corresponding relation is deleted from `project_technology` table
```

#### Referensi Teknis / Technical References

**Tabel:** `project_technology` (pivot)
```
project_id    (FK → projects.id, Cascade Delete)
technology_id (FK → technologies.id, Cascade Delete)
```

---

### US-01-007: Mengatur Urutan Tampil Proyek / Set Custom Project Display Order

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur urutan tampil proyek secara manual di halaman listing portofolio / manually set the display order of projects on the portfolio listing page
**Agar / So that:** proyek yang paling relevan atau terbaru dapat ditampilkan di posisi teratas tanpa bergantung pada urutan tanggal pembuatan / the most relevant or latest projects can be displayed at the top regardless of creation date order

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Custom Ordering Proyek / Custom Project Ordering

  Scenario: Admin mengubah urutan proyek melalui drag-and-drop di daftar CMS
  / Admin changes project order via drag-and-drop in the CMS list
    Given Admin berada di halaman daftar proyek CMS
    / Admin is on the CMS project list page
    When Admin melakukan drag-and-drop pada baris proyek untuk mengubah posisinya
    / Admin performs drag-and-drop on a project row to change its position
    Then Nilai kolom `order` pada record proyek yang terdampak diperbarui secara otomatis
    / The `order` column value on affected project records is automatically updated
    And Halaman listing portofolio publik menampilkan proyek sesuai urutan baru
    / Public portfolio listing page displays projects in the new order

  Scenario: Admin mengubah urutan dengan input angka langsung
  / Admin changes order with direct number input
    Given Admin sedang mengedit sebuah proyek / Admin is editing a project
    When Admin mengubah nilai field "Urutan Tampil" ke angka tertentu
    / Admin changes the "Display Order" field value to a specific number
    And Admin menyimpan / Admin saves
    Then Nilai `order` tersimpan dan proyek menempati posisi yang sesuai
    / The `order` value is saved and project occupies the corresponding position
```

#### Referensi Teknis / Technical References

**Tabel:** `projects` (kolom `order` — integer, default: 0)
**API:** `GET /api/v1/projects` mengembalikan data diurutkan berdasarkan `order ASC, published_at DESC`

---

### US-01-008: Menambahkan Link Eksternal Proyek / Add External Project Links

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menambahkan tautan eksternal untuk setiap proyek: Live Demo URL, GitHub repository URL, dan (khusus Telegram Bot) link bot Telegram / add external links for each project: Live Demo URL, GitHub repository URL, and (for Telegram Bots) Telegram bot link
**Agar / So that:** pengunjung dan calon klien dapat langsung melihat demo langsung dan kode sumber proyek untuk memvalidasi klaim teknis / visitors and potential clients can directly view the live demo and source code to validate technical claims

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tautan Eksternal Proyek / External Project Links

  Scenario: Admin mengisi link eksternal proyek
  / Admin fills in external project links
    Given Admin sedang mengedit proyek / Admin is editing a project
    When Admin mengisi field "Live Demo URL", "GitHub URL", dan/atau "Telegram Bot URL"
    / Admin fills in "Live Demo URL", "GitHub URL", and/or "Telegram Bot URL" fields
    And Admin menyimpan proyek / Admin saves the project
    Then Link tersimpan di kolom yang sesuai di tabel `projects`
    / Links are saved in the corresponding columns in the `projects` table
    And Di halaman detail proyek publik, tombol link yang terisi ditampilkan
    / On the public project detail page, filled link buttons are displayed
    And Tombol link yang kosong (null) TIDAK ditampilkan di frontend
    / Empty (null) link buttons are NOT displayed on the frontend

  Scenario: Validasi format URL
  / URL format validation
    Given Admin mengisi field URL dengan format yang tidak valid
    / Admin fills in a URL field with an invalid format
    When Admin mencoba menyimpan / Admin tries to save
    Then Sistem menampilkan error "Format URL tidak valid, gunakan format https://..."
    / System displays error "Invalid URL format, use https://... format"
```

#### Referensi Teknis / Technical References

**Tabel:** `projects`
```
live_url      (varchar 255, nullable)
github_url    (varchar 255, nullable)
telegram_url  (varchar 255, nullable)
```

---

### US-01-009: Menghapus Entri Proyek / Delete Project Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus entri proyek yang sudah tidak relevan dari database / delete project entries that are no longer relevant from the database
**Agar / So that:** daftar portofolio tetap bersih dan hanya menampilkan proyek yang representatif / the portfolio list stays clean and only shows representative projects

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Entri Proyek / Delete Project Entry

  Scenario: Admin menghapus proyek dengan konfirmasi
  / Admin deletes a project with confirmation
    Given Admin berada di daftar proyek atau halaman edit proyek
    / Admin is on the project list or project edit page
    When Admin mengklik tombol "Hapus" / Admin clicks the "Delete" button
    Then Sistem menampilkan dialog konfirmasi:
    "Hapus proyek ini secara permanen? Tindakan ini tidak dapat dibatalkan."
    / System displays confirmation dialog:
    "Permanently delete this project? This action cannot be undone."
    When Admin mengklik "Ya, Hapus" / Admin clicks "Yes, Delete"
    Then Record proyek dihapus dari tabel `projects`
    / Project record is deleted from the `projects` table
    And Semua relasi pivot (project_technology, project_images) ikut dihapus (Cascade)
    / All pivot relations (project_technology, project_images) are also deleted (Cascade)
    And Admin diredirect ke halaman daftar proyek
    / Admin is redirected to the project list page
    And Proyek tidak lagi muncul di halaman publik
    / Project no longer appears on the public page

  Scenario: Admin membatalkan penghapusan
  / Admin cancels deletion
    Given Dialog konfirmasi hapus sedang terbuka
    / Deletion confirmation dialog is open
    When Admin mengklik "Batal" / Admin clicks "Cancel"
    Then Dialog ditutup dan proyek tidak dihapus / Dialog closes and project is not deleted
```

#### Referensi Teknis / Technical References

**Tabel:** `projects` — Cascade delete ke `project_technology` dan `project_images`
**Catatan:** File gambar di `media` TIDAK ikut terhapus — hanya relasi pivot yang dihapus.

---

### US-01-010: Melihat Daftar & Mencari Proyek di CMS / View & Search Projects in CMS

**Sebagai / As a:** Administrator
**Saya ingin / I want:** melihat daftar semua proyek di CMS dengan informasi ringkas dan kemampuan pencarian/filter / view a list of all projects in the CMS with summary information and search/filter capabilities
**Agar / So that:** saya dapat dengan cepat menemukan dan mengelola proyek tertentu meski jumlah proyek sudah banyak / I can quickly find and manage specific projects even when there are many projects

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Daftar & Pencarian Proyek di CMS / CMS Project List & Search

  Scenario: Admin melihat daftar proyek dengan informasi ringkas
  / Admin views project list with summary information
    Given Admin berada di halaman daftar proyek CMS
    / Admin is on the CMS project list page
    Then Tabel menampilkan kolom: Judul, Kategori, Status (badge warna), Featured, Urutan, Tanggal Dibuat, Aksi
    / Table displays columns: Title, Category, Status (color badge), Featured, Order, Created Date, Actions

  Scenario: Admin mencari proyek berdasarkan judul
  / Admin searches for a project by title
    Given Admin berada di halaman daftar proyek / Admin is on the project list page
    When Admin mengetik kata kunci di field pencarian
    / Admin types a keyword in the search field
    Then Daftar proyek difilter secara real-time (atau on-submit) berdasarkan judul yang mengandung kata kunci tersebut
    / Project list is filtered in real-time (or on-submit) based on titles containing the keyword

  Scenario: Admin memfilter proyek berdasarkan status dan kategori
  / Admin filters projects by status and category
    Given Admin berada di halaman daftar proyek / Admin is on the project list page
    When Admin memilih filter "Status: Draft" dan "Kategori: Web Application"
    / Admin selects filter "Status: Draft" and "Category: Web Application"
    Then Hanya proyek dengan kriteria tersebut yang ditampilkan
    / Only projects matching those criteria are displayed
```

#### Referensi Teknis / Technical References

**Tabel:** `projects` JOIN `project_categories`
**Query:** Filter berdasarkan `status`, `category_id`; Search berdasarkan `title LIKE %keyword%`

---

### US-01-011: Pengunjung Melihat Daftar Proyek di Halaman Portofolio Publik / Visitor Views Project List on Public Portfolio Page

**Sebagai / As a:** Pengunjung Umum (Calon Klien / Recruiter)
**Saya ingin / I want:** melihat daftar studi kasus proyek yang tersusun rapi dengan kemampuan filter berdasarkan kategori / see a well-organized list of project case studies with the ability to filter by category
**Agar / So that:** saya dapat dengan cepat menemukan proyek yang relevan dengan kebutuhan atau minat saya tanpa harus menggulir seluruh halaman / I can quickly find projects relevant to my needs or interests without scrolling through the entire page

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Halaman Listing Proyek Publik / Public Project Listing Page

  Scenario: Pengunjung melihat semua proyek yang dipublikasikan
  / Visitor views all published projects
    Given Pengunjung membuka halaman /proyek / Visitor opens the /proyek page
    Then Halaman menampilkan card proyek yang berstatus 'published' saja
    / Page displays only project cards with 'published' status
    And Setiap card menampilkan: judul, cover image, kategori, deskripsi singkat, badge tech stack
    / Each card displays: title, cover image, category, short description, tech stack badges
    And Proyek diurutkan berdasarkan field `order` (custom ordering admin)
    / Projects are sorted by the `order` field (admin's custom ordering)

  Scenario: Pengunjung memfilter proyek berdasarkan kategori
  / Visitor filters projects by category
    Given Pengunjung berada di halaman /proyek / Visitor is on the /proyek page
    When Pengunjung mengklik tombol filter kategori (misal: "Telegram Bot")
    / Visitor clicks the category filter button (e.g., "Telegram Bot")
    Then Hanya proyek dengan kategori tersebut yang ditampilkan
    / Only projects with that category are displayed
    And URL diperbarui (misal: /proyek?kategori=telegram-bot) untuk SEO & shareable link
    / URL is updated (e.g., /proyek?kategori=telegram-bot) for SEO & shareable links

  Scenario: Pengunjung mengklik proyek untuk melihat detail studi kasus
  / Visitor clicks a project to view the case study details
    Given Pengunjung melihat daftar proyek / Visitor is viewing the project list
    When Pengunjung mengklik card proyek / Visitor clicks a project card
    Then Pengunjung diarahkan ke halaman detail proyek: /proyek/{slug}
    / Visitor is directed to the project detail page: /proyek/{slug}
    And Halaman detail di-render via SSR (Nuxt) dengan HTML lengkap
    / Detail page is rendered via SSR (Nuxt) with full HTML
```

#### Referensi Teknis / Technical References

**API Endpoint:** `GET /api/v1/projects` (query params: `category`, `featured`)
**Nuxt Page:** `pages/proyek/index.vue` — SSR dengan `useFetch`

---

### US-01-012: Pengunjung Membaca Studi Kasus Proyek Lengkap / Visitor Reads Full Project Case Study

**Sebagai / As a:** Calon Klien / Recruiter
**Saya ingin / I want:** membaca studi kasus proyek secara lengkap — termasuk narasi masalah, solusi teknis, arsitektur, galeri visual, dan tautan eksternal / read a complete project case study — including problem narrative, technical solution, architecture, visual gallery, and external links
**Agar / So that:** saya dapat menilai secara mendalam kemampuan teknis dan pendekatan problem-solving developer sebelum mengambil keputusan untuk menghubungi / I can deeply evaluate the developer's technical capabilities and problem-solving approach before deciding to make contact

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Halaman Detail Studi Kasus Proyek / Project Case Study Detail Page

  Scenario: Pengunjung membuka halaman detail proyek yang valid
  / Visitor opens a valid project detail page
    Given Pengunjung mengakses URL /proyek/{slug} yang valid
    / Visitor accesses a valid /proyek/{slug} URL
    Then Halaman di-render via SSR dengan HTML lengkap (untuk SEO)
    / Page is rendered via SSR with full HTML (for SEO)
    And Halaman menampilkan: judul proyek, kategori, tanggal publikasi,
    deskripsi singkat, konten naratif lengkap (HTML rendered), galeri gambar,
    badge tech stack, dan tombol link eksternal (Live Demo, GitHub, Telegram)
    / Page displays: project title, category, publication date,
    short description, full narrative content (rendered HTML), image gallery,
    tech stack badges, and external link buttons (Live Demo, GitHub, Telegram)

  Scenario: Pengunjung mengakses halaman proyek yang tidak ada
  / Visitor accesses a non-existent project page
    Given Pengunjung mengakses URL /proyek/slug-yang-tidak-ada
    / Visitor accesses URL /proyek/non-existent-slug
    Then Sistem menampilkan halaman 404 Not Found
    / System displays 404 Not Found page
    And HTTP status code yang dikembalikan adalah 404
    / The returned HTTP status code is 404

  Scenario: Pengunjung mengakses halaman proyek berstatus draft
  / Visitor accesses a draft project page
    Given Admin memiliki proyek dengan status 'draft' / Admin has a project with 'draft' status
    When Pengunjung publik mencoba mengakses URL proyek tersebut
    / Public visitor tries to access the project URL
    Then Sistem mengembalikan 404 (seolah proyek tidak ada)
    / System returns 404 (as if the project doesn't exist)
```

#### Referensi Teknis / Technical References

**API Endpoint:** `GET /api/v1/projects/{slug}` — hanya mengembalikan proyek `published`
**Nuxt Page:** `pages/proyek/[slug].vue` — SSR dengan `useFetch`
**SEO:** `useSeoMeta` dengan data dari API (title, description, og:image dari cover proyek)

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-01-001 | Membuat Entri Proyek Baru | Must Have | 8 |
| US-01-002 | Edit Konten Naratif Rich Text | Must Have | 5 |
| US-01-003 | Mengatur Status Publikasi | Must Have | 3 |
| US-01-004 | Tandai Proyek sebagai Featured | Must Have | 2 |
| US-01-005 | Kelola Galeri Gambar | Must Have | 5 |
| US-01-006 | Kelola Tagging Tech Stack | Must Have | 3 |
| US-01-007 | Atur Urutan Tampil Proyek | Should Have | 3 |
| US-01-008 | Tambah Link Eksternal | Should Have | 2 |
| US-01-009 | Hapus Entri Proyek | Must Have | 2 |
| US-01-010 | Lihat Daftar & Cari Proyek (CMS) | Must Have | 3 |
| US-01-011 | Lihat Listing Proyek (Publik) | Must Have | 3 |
| US-01-012 | Baca Studi Kasus Lengkap (Publik) | Must Have | 3 |
| | **Total** | | **42** |
