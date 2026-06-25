# US-07 — Modul Manajemen Tech Stack / Tech Stack Management

> **Referensi PRD:** Section 4.7
> **Tabel Database Utama:** `technologies`, `project_technology` (pivot)

---

## Gambaran Modul / Module Overview

**ID:** Modul ini adalah pusat data master teknologi yang dikuasai dan digunakan dalam proyek. Entitas teknologi dikelola secara independen sehingga dapat dihubungkan ke modul Proyek dan modul Skills secara terpusat, menghindari inkonsistensi data. Teknologi yang ditandai "Featured" tampil secara menonjol di halaman Beranda.

**EN:** This module is the master technology data hub for technologies mastered and used in projects. Technology entities are managed independently so they can be linked to the Projects and Skills modules in a centralized way, avoiding data inconsistencies. Technologies marked "Featured" appear prominently on the Home page.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Mengelola master data teknologi | Manages technology master data |
| **Pengunjung Umum** | Melihat badge teknologi di halaman proyek dan Tech Stack section di Beranda | Views technology badges on the project page and Tech Stack section on the Home page |

---

## Daftar User Story / User Story List

---

### US-07-001: Menambahkan Teknologi Baru ke Master Data / Add New Technology to Master Data

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menambahkan teknologi baru yang baru saja saya pelajari atau gunakan ke dalam master data, lengkap dengan nama, kategori, logo dari Media Library, dan link dokumentasi resminya / add a new technology I have just learned or used to the master data, complete with name, category, logo from Media Library, and link to its official documentation
**Agar / So that:** teknologi tersebut tersedia untuk di-tag ke proyek dan ditampilkan sebagai bagian dari profil keahlian teknis saya / the technology is available to be tagged to projects and displayed as part of my technical skills profile

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tambah Teknologi Baru ke Master Data / Add New Technology to Master Data

  Scenario: Admin berhasil menambahkan teknologi baru
  / Admin successfully adds a new technology
    Given Admin berada di halaman "Tech Stack Management" di CMS
    / Admin is on the "Tech Stack Management" page in CMS
    When Admin mengklik "Tambah Teknologi" / Admin clicks "Add Technology"
    And Admin mengisi: Nama (wajib), Kategori (wajib — pilih dari: Frontend/Backend/DevOps/Database/Tools),
    Logo (opsional — dipilih dari Media Library), Link Dokumentasi (opsional), Deskripsi (opsional)
    / Admin fills in: Name (required), Category (required — select from: Frontend/Backend/DevOps/Database/Tools),
    Logo (optional — selected from Media Library), Documentation Link (optional), Description (optional)
    And Admin menyimpan / Admin saves
    Then Slug otomatis di-generate dari nama (contoh: "Vue.js" → "vue-js")
    / Slug is auto-generated from the name (e.g., "Vue.js" → "vue-js")
    And Record tersimpan ke tabel `technologies`
    / Record is saved to the `technologies` table
    And Teknologi baru tersedia di multi-select saat mengedit proyek (Modul 01)
    / New technology is available in multi-select when editing a project (Module 01)
    And Teknologi baru tersedia di dropdown saat menambahkan skill (Modul 08)
    / New technology is available in the dropdown when adding a skill (Module 08)

  Scenario: Nama teknologi harus unik
  / Technology name must be unique
    Given "Laravel" sudah ada di master data / "Laravel" already exists in master data
    When Admin mencoba menambahkan "Laravel" lagi / Admin tries to add "Laravel" again
    Then Error: "Nama teknologi ini sudah terdaftar dalam sistem."
    / Error: "This technology name is already registered in the system."
```

#### Referensi Teknis / Technical References

**Tabel:** `technologies`
```
id, name (varchar 255, not null, unique), slug (varchar 255, not null, unique),
logo_media_id (FK → media.id, nullable, set null on delete),
category (varchar 100, not null — enum: frontend|backend|devops|database|tools),
description (text, nullable), url (varchar 255, nullable — docs/official site),
is_featured (boolean, not null, default: false),
created_at, updated_at
```

---

### US-07-002: Mengedit Data Teknologi / Edit Technology Data

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengedit informasi teknologi yang sudah ada — termasuk memperbarui logo, deskripsi, dan kategorinya / edit existing technology information — including updating its logo, description, and category
**Agar / So that:** data teknologi selalu akurat dan logo teknologi yang ditampilkan di seluruh situs (badge proyek, halaman Beranda) selalu up-to-date / technology data is always accurate and the technology logo displayed throughout the site (project badges, Home page) is always up-to-date

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Edit Data Teknologi / Edit Technology Data

  Scenario: Admin memperbarui logo dan deskripsi teknologi
  / Admin updates technology logo and description
    Given Admin membuka form edit untuk teknologi "Laravel"
    / Admin opens the edit form for technology "Laravel"
    When Admin memilih logo baru dari Media Library dan mengisi deskripsi
    / Admin selects a new logo from Media Library and fills in the description
    And Admin menyimpan / Admin saves
    Then Logo baru tersimpan (referensi `logo_media_id` diperbarui)
    / New logo is saved (reference `logo_media_id` is updated)
    And Logo terbaru langsung tampil di seluruh tempat teknologi ini ditampilkan
    / Latest logo immediately appears everywhere this technology is displayed
    (halaman detail proyek, section Tech Stack di Beranda)
    / (project detail page, Tech Stack section on the Home page)
```

---

### US-07-003: Menandai Teknologi sebagai "Featured" / Mark Technology as "Featured"

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menandai teknologi-teknologi utama saya sebagai "Featured" agar tampil secara menonjol di section Tech Stack halaman Beranda / mark my main technologies as "Featured" so they appear prominently in the Tech Stack section of the Home page
**Agar / So that:** pengunjung baru langsung dapat melihat kemampuan inti saya tanpa harus menjelajahi halaman Tentang Saya / new visitors can immediately see my core capabilities without having to browse to the About Me page

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tandai Teknologi sebagai Featured / Mark Technology as Featured

  Scenario: Admin mengaktifkan flag featured pada teknologi
  / Admin activates the featured flag on a technology
    Given Admin berada di daftar atau form edit teknologi
    / Admin is on the technology list or edit form
    When Admin mengaktifkan toggle "Featured" pada teknologi "Laravel"
    / Admin activates the "Featured" toggle on the "Laravel" technology
    And Admin menyimpan / Admin saves
    Then `is_featured = true` tersimpan untuk teknologi tersebut
    / `is_featured = true` is saved for that technology
    And Logo + nama "Laravel" muncul di section "Tech Stack Unggulan" di halaman Beranda publik
    / "Laravel" logo + name appears in the "Featured Tech Stack" section on the public Home page

  Scenario: Admin melihat daftar teknologi dengan filter "Featured Only"
  / Admin views technology list with "Featured Only" filter
    Given Admin berada di daftar Tech Stack / Admin is on the Tech Stack list
    When Admin mengaktifkan filter "Hanya Tampilkan Featured"
    / Admin activates the "Show Featured Only" filter
    Then Hanya teknologi dengan `is_featured = true` yang ditampilkan
    / Only technologies with `is_featured = true` are displayed
```

---

### US-07-004: Menghapus Data Teknologi / Delete Technology Data

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus data teknologi yang sudah tidak relevan dari master data / delete technology data that is no longer relevant from the master data
**Agar / So that:** daftar tech stack tetap bersih dan hanya berisi teknologi yang aktif saya gunakan / the tech stack list remains clean and only contains technologies I actively use

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Data Teknologi / Delete Technology Data

  Scenario: Admin menghapus teknologi yang tidak terhubung ke proyek maupun skill
  / Admin deletes a technology not linked to any project or skill
    Given Teknologi tidak digunakan oleh proyek manapun dan tidak ada entry skill terkait
    / Technology is not used by any project and there is no related skill entry
    When Admin mengklik "Hapus" dan mengonfirmasi / Admin clicks "Delete" and confirms
    Then Record dihapus dari tabel `technologies`
    / Record is deleted from the `technologies` table

  Scenario: Sistem memperingatkan saat menghapus teknologi yang masih digunakan
  / System warns when deleting a technology that is still in use
    Given Teknologi "Vue.js" digunakan oleh 5 proyek dan 1 skill entry
    / Technology "Vue.js" is used by 5 projects and 1 skill entry
    When Admin mencoba menghapus "Vue.js" / Admin tries to delete "Vue.js"
    Then Sistem menampilkan peringatan:
    "Teknologi ini masih digunakan oleh 5 Proyek dan 1 Skill. Menghapusnya akan menghilangkan semua relasi tersebut. Lanjutkan?"
    / System displays warning:
    "This technology is still used by 5 Projects and 1 Skill. Deleting it will remove all those relations. Continue?"
    When Admin mengonfirmasi / Admin confirms
    Then Teknologi dihapus dan relasi di `project_technology` serta `skills` ikut dihapus (Cascade)
    / Technology is deleted and relations in `project_technology` and `skills` are also deleted (Cascade)
```

---

### US-07-005: Pengunjung Melihat Badge Teknologi di Halaman Proyek / Visitor Views Technology Badges on Project Page

**Sebagai / As a:** Pengunjung Umum
**Saya ingin / I want:** melihat badge teknologi yang digunakan di setiap proyek secara visual / see the technology badges used in each project visually
**Agar / So that:** saya dapat dengan cepat menilai relevansi tech stack proyek dengan kebutuhan teknis saya tanpa perlu membaca seluruh teks narasi / I can quickly assess the relevance of a project's tech stack to my technical needs without reading the entire narrative text

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Badge Teknologi di Halaman Proyek Publik / Technology Badges on Public Project Page

  Scenario: Pengunjung melihat badge tech stack di card proyek pada halaman listing
  / Visitor sees tech stack badges on project cards on the listing page
    Given Pengunjung berada di halaman /proyek / Visitor is on the /proyek page
    Then Setiap project card menampilkan logo + nama teknologi sebagai badge visual (maksimal 3-4 badge, sisanya "+N more")
    / Each project card displays logo + technology name as visual badges (max 3-4 badges, rest as "+N more")

  Scenario: Pengunjung melihat daftar lengkap tech stack di halaman detail proyek
  / Visitor sees full tech stack list on the project detail page
    Given Pengunjung berada di halaman /proyek/{slug}
    / Visitor is on the /proyek/{slug} page
    Then Semua badge teknologi proyek ditampilkan secara lengkap dengan logo
    / All project technology badges are displayed completely with logos
```

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-07-001 | Tambah Teknologi Baru ke Master Data | Must Have | 3 |
| US-07-002 | Edit Data Teknologi | Must Have | 2 |
| US-07-003 | Tandai Teknologi sebagai Featured | Must Have | 2 |
| US-07-004 | Hapus Data Teknologi | Should Have | 2 |
| US-07-005 | Badge Teknologi di Halaman Proyek (Publik) | Must Have | 2 |
| | **Total** | | **11** |
