# US-03 — Modul Media Library Terpusat / Centralized Media Library

> **Referensi PRD:** Section 4.3
> **Tabel Database Utama:** `media`

---

## Gambaran Modul / Module Overview

**ID:** Modul ini berfungsi sebagai pustaka aset gambar tunggal yang digunakan bersama oleh seluruh modul lain (Proyek, Blog, Pengaturan Global, Layanan, Riwayat). Pendekatan terpusat menghindari duplikasi file, memudahkan penggantian aset secara global, dan menjadi titik kendali tunggal untuk optimasi performa gambar (konversi WebP dan generate thumbnail responsif).

**EN:** This module serves as a single image asset library shared by all other modules (Projects, Blog, Global Settings, Services, History). The centralized approach avoids file duplication, simplifies global asset replacement, and is the single control point for image performance optimization (WebP conversion and responsive thumbnail generation).

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Satu-satunya pengguna yang dapat mengunggah, mengedit, dan menghapus media | The only user who can upload, edit, and delete media |

---

## Daftar User Story / User Story List

---

### US-03-001: Mengunggah Gambar ke Media Library / Upload Images to Media Library

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengunggah satu atau beberapa gambar sekaligus ke Media Library menggunakan antarmuka drag-and-drop / upload one or multiple images at once to the Media Library using a drag-and-drop interface
**Agar / So that:** semua aset gambar tersedia di satu tempat dan dapat digunakan kembali di seluruh modul CMS tanpa duplikasi file / all image assets are available in one place and can be reused across all CMS modules without file duplication

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 8 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Upload Gambar ke Media Library / Upload Images to Media Library

  Scenario: Admin berhasil mengunggah satu gambar via drag-and-drop
  / Admin successfully uploads a single image via drag-and-drop
    Given Admin berada di halaman Media Library / Admin is on the Media Library page
    When Admin men-drag file gambar dari komputer ke area drop zone
    / Admin drags an image file from their computer to the drop zone
    Then File mulai diunggah dengan progress indicator yang terlihat
    / File starts uploading with a visible progress indicator
    And Setelah upload selesai, sistem otomatis:
    / After upload completes, the system automatically:
      - Menyimpan file asli ke storage / Saves the original file to storage
      - Mengkonversi gambar ke format WebP / Converts the image to WebP format
      - Meng-generate varian ukuran (thumbnail: 300px, medium: 800px, large: 1200px)
      / Generates size variants (thumbnail: 300px, medium: 800px, large: 1200px)
      - Menyimpan metadata ke tabel `media` (filename, path, size, dimensions, variants JSON)
      / Saves metadata to `media` table (filename, path, size, dimensions, variants JSON)
    And Gambar baru muncul di grid Media Library
    / New image appears in the Media Library grid

  Scenario: Admin mengunggah beberapa gambar sekaligus (bulk upload)
  / Admin uploads multiple images at once (bulk upload)
    Given Admin berada di halaman Media Library / Admin is on the Media Library page
    When Admin memilih lebih dari satu file gambar sekaligus melalui dialog file
    / Admin selects more than one image file at once via file dialog
    Then Semua file diunggah dan diproses secara berurutan/paralel
    / All files are uploaded and processed sequentially/in parallel
    And Setiap gambar muncul di grid Media Library setelah berhasil diproses
    / Each image appears in the Media Library grid after being successfully processed

  Scenario: Sistem menolak file yang bukan gambar atau terlalu besar
  / System rejects non-image files or files that are too large
    Given Admin mencoba mengunggah file non-gambar (misal: .pdf, .docx)
    / Admin tries to upload a non-image file (e.g., .pdf, .docx)
    When File di-drop ke drop zone / File is dropped onto the drop zone
    Then Sistem menampilkan pesan error: "Hanya file gambar (JPG, PNG, GIF, WebP, SVG) yang diizinkan."
    / System displays error: "Only image files (JPG, PNG, GIF, WebP, SVG) are allowed."
    And File tidak disimpan / File is not saved

  Scenario: Upload gambar dengan ukuran terlalu besar
  / Upload image with size too large
    Given Admin mencoba mengunggah gambar lebih besar dari batas maksimal (misal: 10MB)
    / Admin tries to upload an image larger than the maximum limit (e.g., 10MB)
    Then Sistem menampilkan pesan error: "Ukuran file melebihi batas maksimal 10MB."
    / System displays error: "File size exceeds maximum limit of 10MB."
```

#### Referensi Teknis / Technical References

**Tabel:** `media`
```
id, original_filename, storage_path (path ke file WebP utama), 
mime_type, file_size (bytes), width, height,
alt_text (varchar nullable),
variants (JSON — struktur: {thumbnail: {path, width, height}, medium: {...}, large: {...}})
created_at, updated_at
```

**Pseudocode Proses Upload:**
```
1. Terima file dari request
2. Validasi: mime type (image/*), ukuran max 10MB
3. Generate unique filename (UUID + timestamp)
4. Simpan file original ke storage/media/originals/
5. Jalankan ImageProcessingJob (async via Queue):
   a. Konversi ke WebP → simpan ke storage/media/webp/
   b. Resize → thumbnail (300px width), medium (800px), large (1200px)
   c. Simpan setiap varian di storage/media/variants/{size}/
6. Insert record ke tabel `media` dengan data variants JSON
7. Return response JSON dengan URL dan metadata
```

---

### US-03-002: Melihat dan Menelusuri Media Library / View and Browse Media Library

**Sebagai / As a:** Administrator
**Saya ingin / I want:** melihat seluruh gambar yang tersimpan dalam tampilan grid yang rapi dan dapat menelusuri serta memfilter berdasarkan nama file / view all stored images in a neat grid layout and browse/filter by filename
**Agar / So that:** saya dapat dengan mudah menemukan aset gambar yang tepat saat ingin menggunakannya di modul lain / I can easily find the right image asset when I want to use it in another module

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Melihat dan Menelusuri Media Library / View and Browse Media Library

  Scenario: Admin melihat semua gambar dalam tampilan grid
  / Admin views all images in grid layout
    Given Admin berada di halaman Media Library / Admin is on the Media Library page
    Then Semua gambar ditampilkan dalam tampilan grid (thumbnail berukuran konsisten)
    / All images are displayed in a grid layout (consistent thumbnail size)
    And Setiap thumbnail menampilkan: nama file (truncated), dimensi, ukuran file
    / Each thumbnail displays: filename (truncated), dimensions, file size
    And Gambar diurutkan dari yang terbaru ke yang terlama (default)
    / Images are sorted newest to oldest (default)

  Scenario: Admin mencari gambar berdasarkan nama file
  / Admin searches for an image by filename
    Given Admin berada di halaman Media Library / Admin is on the Media Library page
    When Admin mengetik nama file di field pencarian / Admin types a filename in the search field
    Then Grid difilter untuk hanya menampilkan gambar yang nama file-nya mengandung kata kunci
    / Grid is filtered to only show images whose filename contains the keyword

  Scenario: Admin memilih gambar untuk digunakan di modul lain
  / Admin selects an image for use in another module
    Given Modal Media Library terbuka dari modul lain (misal: saat mengedit proyek)
    / Media Library modal is open from another module (e.g., when editing a project)
    When Admin mengklik gambar yang diinginkan / Admin clicks the desired image
    Then Gambar yang dipilih dikembalikan ke form modul yang memanggil
    / Selected image is returned to the form of the calling module
    And Modal tertutup otomatis / Modal closes automatically
```

---

### US-03-003: Mengedit Alt Text Gambar / Edit Image Alt Text

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengedit alt text untuk setiap gambar di Media Library / edit the alt text for each image in the Media Library
**Agar / So that:** semua gambar yang dipublikasikan di situs memenuhi standar aksesibilitas web dan terindeks dengan baik oleh Google Image Search / all images published on the site meet web accessibility standards and are well-indexed by Google Image Search

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Edit Alt Text Gambar / Edit Image Alt Text

  Scenario: Admin mengedit alt text gambar yang sudah ada
  / Admin edits alt text of an existing image
    Given Admin mengklik detail atau ikon edit pada gambar di Media Library
    / Admin clicks detail or edit icon on an image in the Media Library
    Then Panel detail gambar muncul menampilkan preview dan form edit
    / Image detail panel appears showing preview and edit form
    And Form berisi field: Alt Text (varchar, wajib diisi untuk akses), dan Original Filename (read-only)
    / Form contains fields: Alt Text (varchar, required for accessibility), and Original Filename (read-only)
    When Admin mengisi alt text yang deskriptif dan mengklik "Simpan"
    / Admin fills in descriptive alt text and clicks "Save"
    Then Alt text tersimpan di kolom `alt_text` pada tabel `media`
    / Alt text is saved in the `alt_text` column in the `media` table
    And Alt text ini digunakan sebagai atribut `alt` pada tag `<img>` di seluruh tempat gambar ini digunakan
    / This alt text is used as the `alt` attribute on `<img>` tags everywhere this image is used
```

---

### US-03-004: Menghapus Gambar dari Media Library / Delete Image from Media Library

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus gambar dari Media Library yang sudah tidak dibutuhkan, dengan sistem yang memperingatkan saya jika gambar tersebut masih digunakan / delete images from the Media Library that are no longer needed, with the system warning me if the image is still in use
**Agar / So that:** storage server tetap bersih dari aset yang tidak terpakai namun tidak ada gambar aktif yang terhapus secara tidak sengaja / the server storage stays clean from unused assets but no active images are accidentally deleted

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Gambar dari Media Library / Delete Image from Media Library

  Scenario: Admin menghapus gambar yang tidak sedang digunakan
  / Admin deletes an image that is not currently in use
    Given Gambar tidak sedang digunakan oleh entitas manapun
    / Image is not currently used by any entity
    When Admin mengklik ikon hapus pada gambar / Admin clicks the delete icon on the image
    Then Dialog konfirmasi muncul: "Hapus gambar ini secara permanen dari Media Library?"
    / Confirmation dialog appears: "Permanently delete this image from the Media Library?"
    When Admin mengonfirmasi / Admin confirms
    Then File gambar (original + semua varian) dihapus dari storage
    / Image file (original + all variants) is deleted from storage
    And Record dihapus dari tabel `media`
    / Record is deleted from the `media` table

  Scenario: Sistem menampilkan peringatan saat menghapus gambar yang sedang digunakan
  / System displays warning when deleting an image currently in use
    Given Gambar digunakan oleh 2 proyek dan 1 artikel blog
    / Image is used by 2 projects and 1 blog article
    When Admin mengklik ikon hapus / Admin clicks the delete icon
    Then Dialog konfirmasi menampilkan peringatan khusus:
    "Gambar ini sedang digunakan oleh 2 Proyek dan 1 Artikel Blog.
    Menghapus gambar ini akan menyebabkan referensi gambar tersebut rusak.
    Apakah Anda yakin ingin menghapus?"
    / Confirmation dialog displays specific warning:
    "This image is currently used by 2 Projects and 1 Blog Article.
    Deleting this image will break those image references.
    Are you sure you want to delete?"
    When Admin mengonfirmasi / Admin confirms
    Then Gambar dihapus dan referensi yang ada menjadi null (FK set null)
    / Image is deleted and existing references become null (FK set null)
```

#### Referensi Teknis / Technical References

**Tabel:** `media` — FK dari: `projects.cover_image_id`, `project_images.media_id`, `posts.cover_image_id`, `technologies.logo_media_id`, `experiences.logo_media_id`, `site_settings.profile_photo_id`
**Strategi FK:** `ON DELETE SET NULL` pada semua kolom yang mereferensi `media.id`
**Storage:** Hapus file dengan `Storage::delete([path_original, path_webp, path_thumbnail, path_medium, path_large])`

---

### US-03-005: Menggunakan Gambar dari Media Library di Modul Lain / Use Images from Media Library in Other Modules

**Sebagai / As a:** Administrator
**Saya ingin / I want:** memilih gambar yang sudah ada di Media Library saat mengedit proyek, blog, atau pengaturan — tanpa perlu mengunggah ulang file yang sama / select an image that already exists in the Media Library when editing a project, blog, or settings — without needing to re-upload the same file
**Agar / So that:** tidak ada duplikasi aset di storage dan semua referensi gambar menggunakan sumber yang sama / there is no asset duplication in storage and all image references use the same source

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Penggunaan Gambar dari Media Library di Modul Lain
/ Using Images from Media Library in Other Modules

  Scenario: Admin memilih gambar cover untuk artikel blog dari Media Library
  / Admin selects a cover image for a blog article from Media Library
    Given Admin sedang mengedit form artikel blog
    / Admin is editing a blog article form
    When Admin mengklik tombol "Pilih Gambar Cover" / Admin clicks "Select Cover Image"
    Then Modal Media Library terbuka dalam mode pilih / Media Library modal opens in select mode
    When Admin mengklik gambar yang diinginkan / Admin clicks the desired image
    Then Gambar terpilih ditampilkan sebagai preview cover di form artikel
    / Selected image is displayed as cover preview in the article form
    And `media_id` disimpan di kolom `cover_image_id` pada tabel `posts`
    / `media_id` is saved in the `cover_image_id` column in the `posts` table

  Scenario: Admin mengunggah gambar baru langsung dari modal Media Library
  / Admin uploads a new image directly from the Media Library modal
    Given Modal Media Library sedang terbuka / Media Library modal is open
    When Admin mengklik "Upload Baru" di dalam modal / Admin clicks "Upload New" inside the modal
    Then Area upload muncul di dalam modal / Upload area appears inside the modal
    And Admin dapat mengunggah gambar baru dan langsung memilihnya
    / Admin can upload a new image and immediately select it
```

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-03-001 | Upload Gambar (Drag & Drop + Bulk) | Must Have | 8 |
| US-03-002 | Lihat & Telusuri Media Library | Must Have | 3 |
| US-03-003 | Edit Alt Text Gambar | Must Have | 2 |
| US-03-004 | Hapus Gambar (dengan referential check) | Must Have | 5 |
| US-03-005 | Gunakan Gambar dari Library di Modul Lain | Must Have | 3 |
| | **Total** | | **21** |
