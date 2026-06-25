# US-06 — Modul Kategori Proyek / Project Categories

> **Referensi PRD:** Section 4.6
> **Tabel Database Utama:** `project_categories`

---

## Gambaran Modul / Module Overview

**ID:** Modul ini mengelola pengelompokan proyek secara dinamis. Administrator dapat menentukan kategori baru (seperti "Web Application", "Mobile App", "Telegram Bot", "API/Backend Service", "Automation Tool") sehingga pengunjung dapat memfilter proyek berdasarkan kategori tersebut. Sistem mencegah penghapusan kategori yang masih digunakan oleh proyek aktif.

**EN:** This module manages dynamic project grouping. The administrator can define new categories (such as "Web Application", "Mobile App", "Telegram Bot", "API/Backend Service", "Automation Tool") so visitors can filter projects by category. The system prevents deletion of categories still used by active projects.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Mengelola master data kategori proyek | Manages project category master data |
| **Pengunjung Umum** | Memfilter proyek berdasarkan kategori di halaman portofolio publik | Filters projects by category on the public portfolio page |

---

## Daftar User Story / User Story List

---

### US-06-001: Membuat Kategori Proyek Baru / Create New Project Category

**Sebagai / As a:** Administrator
**Saya ingin / I want:** membuat kategori proyek baru dengan nama, slug otomatis, deskripsi, ikon, dan urutan tampil / create a new project category with a name, auto-generated slug, description, icon, and display order
**Agar / So that:** proyek-proyek saya dapat dikelompokkan secara dinamis dan filter kategori di halaman portofolio publik selalu relevan dan terkini / my projects can be dynamically grouped and the category filter on the public portfolio page is always relevant and up-to-date

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Membuat Kategori Proyek Baru / Create New Project Category

  Scenario: Admin berhasil membuat kategori baru
  / Admin successfully creates a new category
    Given Admin berada di halaman "Kategori Proyek" di CMS
    / Admin is on the "Project Categories" page in CMS
    When Admin mengklik "Tambah Kategori" / Admin clicks "Add Category"
    And Admin mengisi: nama kategori, deskripsi (opsional), ikon (opsional), urutan tampil
    / Admin fills in: category name, description (optional), icon (optional), display order
    And Admin mengklik "Simpan" / Admin clicks "Save"
    Then Slug otomatis di-generate dari nama (contoh: "Telegram Bot" → "telegram-bot")
    / Slug is auto-generated from the name (e.g., "Telegram Bot" → "telegram-bot")
    And Record tersimpan ke tabel `project_categories`
    / Record is saved to the `project_categories` table
    And Kategori baru tersedia di dropdown saat membuat/mengedit proyek
    / New category is available in the dropdown when creating/editing a project
    And Filter kategori di halaman portofolio publik diperbarui otomatis
    / Category filter on the public portfolio page is automatically updated

  Scenario: Nama kategori harus unik
  / Category name must be unique
    Given Kategori "Web Application" sudah ada / Category "Web Application" already exists
    When Admin mencoba membuat kategori baru dengan nama yang sama
    / Admin tries to create a new category with the same name
    Then Sistem menampilkan error: "Nama kategori sudah digunakan."
    / System displays error: "Category name is already in use."
    And Record tidak disimpan / Record is not saved
```

#### Referensi Teknis / Technical References

**Tabel:** `project_categories`
```
id, name (varchar 255, not null, unique), slug (varchar 255, not null, unique),
description (text, nullable), icon (varchar 255, nullable — nama Lucide icon),
order (integer, not null, default: 0), created_at, updated_at
```

---

### US-06-002: Mengedit Kategori Proyek / Edit Project Category

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengedit nama, deskripsi, ikon, dan urutan tampil kategori proyek yang sudah ada / edit the name, description, icon, and display order of an existing project category
**Agar / So that:** tampilan dan informasi kategori di sisi publik selalu akurat dan menarik / the appearance and information of categories on the public side is always accurate and attractive

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Mengedit Kategori Proyek / Edit Project Category

  Scenario: Admin mengedit nama dan ikon kategori
  / Admin edits the name and icon of a category
    Given Admin membuka form edit untuk kategori yang sudah ada
    / Admin opens the edit form for an existing category
    When Admin mengubah nama dan ikon kategori / Admin changes the category name and icon
    And Admin menyimpan perubahan / Admin saves changes
    Then Perubahan tersimpan di `project_categories`
    / Changes are saved in `project_categories`
    And Slug diperbarui otomatis jika nama diubah (dengan konfirmasi peringatan jika slug digunakan di URL publik)
    / Slug is auto-updated if name changes (with a warning confirmation if the slug is used in public URLs)
    And Perubahan tampil di halaman publik (filter kategori dan halaman detail proyek)
    / Changes appear on the public page (category filter and project detail page)

  Scenario: Admin mengubah slug secara manual
  / Admin changes slug manually
    Given Admin sedang mengedit kategori / Admin is editing a category
    When Admin mengubah slug secara manual / Admin changes the slug manually
    Then Sistem memperingatkan: "Mengubah slug akan memengaruhi URL publik kategori ini. Pastikan redirect sudah dikonfigurasi."
    / System warns: "Changing the slug will affect this category's public URL. Ensure redirects are configured."
```

---

### US-06-003: Menghapus Kategori Proyek / Delete Project Category

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus kategori proyek yang sudah tidak relevan / delete project categories that are no longer relevant
**Agar / So that:** daftar filter kategori di halaman publik tetap bersih dan relevan / the category filter list on the public page remains clean and relevant

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Menghapus Kategori Proyek / Delete Project Category

  Scenario: Admin berhasil menghapus kategori yang tidak digunakan
  / Admin successfully deletes a category that is not in use
    Given Kategori tidak digunakan oleh proyek manapun
    / Category is not used by any project
    When Admin mengklik "Hapus" dan mengonfirmasi
    / Admin clicks "Delete" and confirms
    Then Record dihapus dari `project_categories`
    / Record is deleted from `project_categories`
    And Kategori tidak lagi muncul di filter halaman publik maupun dropdown CMS
    / Category no longer appears in the public page filter or CMS dropdown

  Scenario: Sistem mencegah penghapusan kategori yang masih digunakan proyek
  / System prevents deletion of a category still used by projects
    Given Kategori "Telegram Bot" digunakan oleh 3 proyek
    / Category "Telegram Bot" is used by 3 projects
    When Admin mencoba menghapus kategori ini / Admin tries to delete this category
    Then Sistem menampilkan pesan error:
    "Kategori ini tidak dapat dihapus karena masih digunakan oleh 3 proyek.
    Pindahkan proyek-proyek tersebut ke kategori lain terlebih dahulu."
    / System displays error message:
    "This category cannot be deleted because it is still used by 3 projects.
    Move those projects to another category first."
    And Penghapusan dibatalkan / Deletion is cancelled
```

---

### US-06-004: Mengatur Urutan Tampil Kategori / Set Category Display Order

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur urutan tampil kategori pada tombol filter di halaman portofolio publik / set the display order of categories on the filter buttons on the public portfolio page
**Agar / So that:** kategori yang paling penting atau sering difilter ditampilkan lebih menonjol (di posisi pertama) / the most important or frequently filtered categories are displayed more prominently (in the first position)

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Urutan Tampil Kategori / Category Display Order

  Scenario: Admin mengatur urutan kategori di CMS
  / Admin sets category order in CMS
    Given Admin berada di daftar kategori proyek / Admin is on the project categories list
    When Admin mengubah nilai "Urutan" pada setiap kategori
    / Admin changes the "Order" value for each category
    And Admin menyimpan perubahan / Admin saves changes
    Then Tombol filter kategori di halaman /proyek publik diurutkan sesuai nilai `order` (ascending)
    / Category filter buttons on the public /proyek page are sorted by `order` value (ascending)
    And Kategori dengan `order = 0` muncul paling pertama
    / Category with `order = 0` appears first
```

---

### US-06-005: Pengunjung Memfilter Proyek Berdasarkan Kategori / Visitor Filters Projects by Category

**Sebagai / As a:** Pengunjung Umum / Calon Klien
**Saya ingin / I want:** menyaring tampilan proyek di halaman portofolio berdasarkan kategori yang saya pilih / filter the project display on the portfolio page based on the category I select
**Agar / So that:** saya hanya melihat tipe proyek yang relevan dengan kebutuhan bisnis saya tanpa harus menggulir seluruh halaman / I only see the type of projects relevant to my business needs without having to scroll through the entire page

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Filter Proyek Berdasarkan Kategori (Publik) / Filter Projects by Category (Public)

  Scenario: Pengunjung memilih filter kategori
  / Visitor selects a category filter
    Given Pengunjung berada di halaman /proyek / Visitor is on the /proyek page
    And Ada tombol filter untuk setiap kategori yang tersedia
    / There are filter buttons for each available category
    When Pengunjung mengklik tombol "Telegram Bot"
    / Visitor clicks the "Telegram Bot" button
    Then Hanya proyek ber-kategori "Telegram Bot" yang ditampilkan
    / Only projects with "Telegram Bot" category are displayed
    And URL diperbarui menjadi /proyek?kategori=telegram-bot
    / URL is updated to /proyek?kategori=telegram-bot
    And Tombol "Telegram Bot" mendapatkan style aktif (highlight)
    / "Telegram Bot" button gets active style (highlight)

  Scenario: Pengunjung mengakses halaman portofolio dengan parameter kategori dari URL
  / Visitor accesses portfolio page with category parameter from URL
    Given Seseorang membagikan URL /proyek?kategori=web-application kepada calon klien
    / Someone shares URL /proyek?kategori=web-application to a potential client
    When Calon klien membuka URL tersebut / Potential client opens the URL
    Then Halaman langsung menampilkan hanya proyek "Web Application"
    / Page immediately displays only "Web Application" projects
    And Tombol filter "Web Application" dalam kondisi aktif
    / "Web Application" filter button is in active state
```

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-06-001 | Membuat Kategori Proyek Baru | Must Have | 3 |
| US-06-002 | Mengedit Kategori Proyek | Must Have | 2 |
| US-06-003 | Menghapus Kategori (referential check) | Must Have | 3 |
| US-06-004 | Mengatur Urutan Tampil Kategori | Should Have | 2 |
| US-06-005 | Pengunjung Filter Proyek (Publik) | Must Have | 2 |
| | **Total** | | **12** |
