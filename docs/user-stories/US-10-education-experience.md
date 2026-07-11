# US-10 — Modul Riwayat Pendidikan & Pengalaman / Education & Experience

> **Referensi PRD:** Section 4.10
> **Tabel Database Utama:** `experiences` (Pengalaman Kerja) & `educations` (Pendidikan)

---

## Gambaran Modul / Module Overview

**ID:** Modul ini mengelola data portofolio karir dan akademis secara terstruktur untuk membangun resume digital. Menggunakan skema terpisah di mana riwayat Pengalaman Kerja disimpan di tabel `experiences` dan Pendidikan di tabel `educations`. Pengelolaan di CMS dilakukan pada satu halaman terpadu yang sama menggunakan antarmuka Tab (Tab Pengalaman Kerja & Tab Pendidikan). Masing-masing tab mengelola datanya dengan form/Sheet laci yang terpisah dan spesifik. Modul ini mendukung pengunggahan logo institusi melalui integrasi dengan Media Library.

**EN:** This module manages career and academic portfolio data in a structured way to build a digital resume. It uses a separated schema where Work Experience is stored in the `experiences` table and Education is stored in the `educations` table. Management in CMS is performed on the same unified page using a Tabbed interface (Work Experience Tab & Education Tab). Each tab manages its data with separate and specific form drawers/Sheets. This module supports uploading institution logos through integration with the Media Library.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Mengelola riwayat pendidikan dan pengalaman kerja | Manages education and work experience history |
| **Recruiter** | Menganalisis latar belakang akademis dan karir developer | Analyzes developer's academic and career background |
| **Pengunjung Umum** | Melihat resume digital developer di halaman About | Views developer's digital resume on the About page |

---

## Daftar User Story / User Story List

---

### US-10-001: Menambahkan Riwayat Pengalaman Kerja Baru / Add New Work Experience Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menambahkan riwayat pengalaman kerja baru dengan informasi perusahaan, jabatan, periode kerja, deskripsi kontribusi, dan logo perusahaan / add a new work experience entry with company information, position title, work period, contribution description, and company logo
**Agar / So that:** recruiter dan calon klien dapat melihat rekam jejak karir profesional saya secara kronologis dan terstruktur / recruiters and potential clients can see my professional career track record chronologically and in a structured way

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tambah Riwayat Pengalaman Kerja / Add Work Experience Entry

  Scenario: Admin berhasil menambahkan pengalaman kerja baru
  / Admin successfully adds a new work experience entry
    Given Admin berada di halaman "Education & Experience" di CMS
    / Admin is on the "Education & Experience" page in CMS
    When Admin mengklik "Tambah Pengalaman"
    / Admin clicks "Add Experience"
    And Admin mengisi field wajib:
    / Admin fills in required fields:
      - Company: "PT. Solusi Digital Indonesia"
      - Title/Position: "Full-Stack Developer"
      - Start Date: "2024-01" (bulan/tahun)
      / Start Date: "2024-01" (month/year)
    And Admin mengisi field opsional:
    / Admin fills in optional fields:
      - End Date: (dikosongkan → berarti "Sekarang" / Present)
      / End Date: (left empty → means "Present")
      - Location: "Jakarta (Remote)"
      - Description: poin-poin kontribusi dalam format rich text atau plain text
      / Description: contribution points in rich text or plain text format
      - Website URL: "https://solusidigital.id"
      - Logo: dipilih dari Media Library / Logo: selected from Media Library
      - Order: 1
    And Admin mengklik "Simpan" / Admin clicks "Save"
    Then Record tersimpan ke tabel `experiences`
    / Record is saved to the `experiences` table
    And Pengalaman kerja baru tampil di section "Pengalaman Kerja" pada halaman /about publik
    / New work experience appears in the "Work Experience" section on the public /about page

  Scenario: End Date dikosongkan = "Sekarang / Present"
  / Empty End Date = "Present"
    Given Admin mengosongkan field "End Date" / Admin leaves "End Date" field empty
    When Admin menyimpan / Admin saves
    Then `end_date = NULL` tersimpan di database
    / `end_date = NULL` is saved in the database
    And Halaman publik menampilkan "2024 — Sekarang" atau "2024 — Present"
    / Public page displays "2024 — Present" or "2024 — Now"
```

#### Referensi Teknis / Technical References

**Tabel:** `experiences` (Work Experiences)
```
id, company (varchar 255, not null), title_position (varchar 255, not null),
location (varchar 255, nullable), start_date (date, not null),
end_date (date, nullable — null = Present), description (text, nullable),
website_url (varchar 255, nullable), logo_media_id (FK → media.id, nullable, set null on delete),
order (integer, not null, default: 0), created_at, updated_at
```

---

### US-10-002: Menambahkan Riwayat Pendidikan Baru / Add New Education Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menambahkan riwayat pendidikan dengan informasi institusi, jurusan/gelar, periode studi, deskripsi pencapaian akademis, dan logo universitas / add an education entry with institution information, major/degree, study period, academic achievement description, and university logo
**Agar / So that:** profil akademis saya tercatat secara formal dan dapat dinilai oleh recruiter yang mempertimbangkan latar belakang pendidikan sebagai kriteria seleksi / my academic profile is formally recorded and can be assessed by recruiters who consider educational background as a selection criterion

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Tambah Riwayat Pendidikan / Add Education Entry

  Scenario: Admin menambahkan pendidikan universitas
  / Admin adds university education
    Given Admin berada di halaman "Education & Experience" di CMS
    / Admin is on the "Education & Experience" page in CMS
    When Admin mengklik "Tambah Pendidikan"
    / Admin clicks "Add Education"
    And Admin mengisi:
    / Admin fills in:
      - Institution: "Universitas Brawijaya"
      - Degree: "S1"
      - Major: "Teknik Informatika"
      - GPA: "3.85"
      - Start Date: "2020-09"
      - End Date: "2024-07"
      - Location: "Malang, Jawa Timur"
      - Description: "Aktif dalam organisasi ..."
      / Description: "Active in organizations ..."
      - Logo: Logo universitas dari Media Library
      / Logo: University logo from Media Library
    And Admin mengklik "Simpan" / Admin clicks "Save"
    Then Record tersimpan ke tabel `educations`
    / Record is saved to `educations` table
    And Pendidikan baru tampil di section "Pendidikan" pada halaman /about publik
    / New education appears in the "Education" section on the public /about page

  Scenario: Admin yang masih kuliah mengosongkan End Date
  / Admin who is still studying leaves End Date empty
    Given Admin masih aktif sebagai mahasiswa / Admin is still an active student
    When Admin mengosongkan field "End Date" / Admin leaves "End Date" empty
    Then `end_date = NULL` dan halaman publik menampilkan "2020 — Sekarang / Present"
    / `end_date = NULL` and public page displays "2020 — Present"
```

#### Referensi Teknis / Technical References

**Tabel:** `educations` (Educations)
```
id, institution (varchar 255, not null), degree (varchar 255, nullable — misal: S1),
major (varchar 255, not null — Jurusan), gpa (varchar 50, nullable — IPK),
location (varchar 255, nullable), start_date (date, not null),
end_date (date, nullable — null = Present), description (text, nullable),
logo_media_id (FK → media.id, nullable, set null on delete),
order (integer, not null, default: 0), created_at, updated_at
```

---

### US-10-003: Mengedit Riwayat Pendidikan atau Pengalaman / Edit Education or Experience Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengedit informasi pada entri riwayat yang sudah ada — termasuk memperbarui deskripsi, mengubah tanggal, atau memperbarui logo institusi / edit information on existing history entries — including updating the description, changing dates, or updating the institution logo
**Agar / So that:** resume digital saya selalu akurat dan mencerminkan informasi terbaru / my digital resume is always accurate and reflects the latest information

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Edit Riwayat Pendidikan / Pengalaman / Edit Education/Experience Entry

  Scenario: Admin memperbarui logo institusi
  / Admin updates institution logo
    Given Admin membuka form edit untuk entri riwayat universitas
    / Admin opens the edit form for a university history entry
    When Admin mengklik "Ganti Logo" dan memilih logo baru dari Media Library
    / Admin clicks "Change Logo" and selects a new logo from Media Library
    And Admin menyimpan / Admin saves
    Then `logo_media_id` diperbarui / `logo_media_id` is updated
    And Logo baru langsung tampil di timeline halaman /about publik
    / New logo immediately appears on the timeline of the public /about page

  Scenario: Admin mengubah End Date ketika sudah menyelesaikan studi
  / Admin changes End Date when studies are completed
    Given Entri pendidikan masih memiliki `end_date = NULL` (masih kuliah)
    / Education entry still has `end_date = NULL` (still studying)
    When Admin mengisi End Date dengan tanggal kelulusan dan menyimpan
    / Admin fills in End Date with graduation date and saves
    Then Halaman publik berubah dari "2020 — Sekarang" menjadi "2020 — 2024"
    / Public page changes from "2020 — Present" to "2020 — 2024"
```

---

### US-10-004: Menghapus Riwayat Pendidikan atau Pengalaman / Delete Education or Experience Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus entri riwayat yang sudah tidak relevan atau dimasukkan secara keliru / delete history entries that are no longer relevant or were entered incorrectly
**Agar / So that:** resume digital saya bersih dari data yang tidak akurat atau tidak profesional untuk ditampilkan / my digital resume is free from inaccurate or unprofessional data to display

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 1 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Riwayat Pendidikan / Pengalaman / Delete Education/Experience Entry

  Scenario: Admin menghapus entri riwayat dengan konfirmasi
  / Admin deletes a history entry with confirmation
    Given Admin berada di daftar atau halaman edit entri riwayat
    / Admin is on the list or edit page of a history entry
    When Admin mengklik "Hapus" dan mengonfirmasi
    / Admin clicks "Delete" and confirms
    Then Record dihapus dari tabel `experiences` atau `educations` sesuai entri yang dipilih
    / Record is deleted from the `experiences` or `educations` table according to selected entry
    And Entri tidak lagi tampil di halaman /about publik
    / Entry no longer appears on the public /about page
    And File logo di Media Library TIDAK ikut terhapus (hanya referensi FK yang hilang)
    / Logo file in Media Library is NOT deleted (only the FK reference is removed)
```

---

### US-10-005: Mengatur Urutan Tampil Riwayat / Set History Display Order

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur urutan tampil entri riwayat di halaman About secara manual atau memastikan sistem menampilkan dalam urutan kronologis terbalik / manually set the display order of history entries on the About page or ensure the system displays them in reverse chronological order
**Agar / So that:** recruiter langsung melihat posisi dan pendidikan terbaru di bagian atas timeline / recruiters immediately see the latest position and education at the top of the timeline

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Urutan Tampil Riwayat / History Display Order

  Scenario: Admin mengatur urutan manual via field "Order"
  / Admin sets manual order via "Order" field
    Given Admin berada di daftar entri riwayat / Admin is on the history entry list
    When Admin mengubah nilai "Order" untuk setiap entri
    / Admin changes the "Order" value for each entry
    And Admin menyimpan / Admin saves
    Then Timeline halaman /about menampilkan entri sesuai urutan yang diatur
    / Timeline on the /about page displays entries in the set order

  Scenario: Default ordering berdasarkan start_date terbaru
  / Default ordering based on latest start_date
    Given Admin tidak mengubah nilai `order` (semua = 0)
    / Admin does not change the `order` value (all = 0)
    Then Entri diurutkan berdasarkan `start_date DESC` (terbaru di atas)
    / Entries are sorted by `start_date DESC` (latest at the top)
```

---

### US-10-006: Recruiter Melihat Timeline Riwayat Karir & Pendidikan / Recruiter Views Career & Education Timeline

**Sebagai / As a:** Recruiter
**Saya ingin / I want:** melihat timeline riwayat kerja dan pendidikan developer secara kronologis dan terstruktur di halaman About / see the developer's work and education history in a chronological and structured timeline on the About page
**Agar / So that:** saya dapat dengan cepat menilai latar belakang karir, pengalaman kerja relevan, dan kualifikasi akademis developer dalam waktu singkat saat melakukan initial screening / I can quickly assess the developer's career background, relevant work experience, and academic qualifications in a short time during initial screening

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Timeline Riwayat Karir & Pendidikan di Halaman Publik
/ Career & Education Timeline on Public Page

  Scenario: Recruiter melihat section Experience & Education di halaman About
  / Recruiter views Experience & Education section on the About page
    Given Recruiter membuka halaman /about / Recruiter opens the /about page
    Then Section "Pengalaman Kerja" menampilkan entri dari tabel `experiences`
    / "Work Experience" section displays entries from the `experiences` table
    And Section "Pendidikan" menampilkan entri dari tabel `educations`
    / "Education" section displays entries from the `educations` table
    And Setiap entri di kedua section menampilkan detail data masing-masing:
    / Each entry in both sections displays their respective detail data:
      - Logo institusi / perusahaan (dari Media Library, jika ada)
      / Institution / company logo (from Media Library, if available)
      - Nama perusahaan / institusi / Company / institution name
      - Jabatan / Jurusan & Gelar / Position / Major & Degree
      - Periode (Start — End atau Start — Sekarang)
      / Period (Start — End or Start — Present)
      - Lokasi (jika ada) / Location (if available)
      - Deskripsi kontribusi / pencapaian / Contribution / achievement description
      - IPK / GPA (khusus Pendidikan jika ada)
    And Halaman di-render via SSR untuk SEO / Page is rendered via SSR for SEO

  Scenario: Entri dengan end_date = NULL ditampilkan sebagai "Sekarang / Present"
  / Entry with end_date = NULL is displayed as "Present"
    Given Entri pengalaman kerja terbaru memiliki `end_date = NULL`
    / Latest work experience entry has `end_date = NULL`
    When Recruiter melihat halaman /about / Recruiter views the /about page
    Then Periode ditampilkan sebagai "Jan 2024 — Sekarang" (atau dalam bahasa Inggris: "Jan 2024 — Present")
    / Period is displayed as "Jan 2024 — Present"
```

#### Referensi Teknis / Technical References

**API Endpoints:** 
- `GET /api/v1/experiences` — mengembalikan daftar pengalaman kerja (tabel `experiences`)
- `GET /api/v1/educations` — mengembalikan daftar pendidikan (tabel `educations`)

**Nuxt Page:** `pages/about.vue` — SSR dengan `useFetch`
**Eager Load:** Relasi ke `media` (logo) via `with('logo')`

---

### US-10-007: Admin Melihat Daftar Semua Riwayat di CMS / Admin Views All History Entries in CMS

**Sebagai / As a:** Administrator
**Saya ingin / I want:** melihat semua entri riwayat (pendidikan dan pengalaman) dalam satu tampilan daftar yang dapat difilter berdasarkan tipe / see all history entries (education and experience) in a single list view that can be filtered by type
**Agar / So that:** saya dapat mengelola seluruh data resume dari satu tempat dengan efisien / I can manage all resume data from one place efficiently

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Daftar Riwayat di CMS / History List in CMS

  Scenario: Admin melihat semua entri dalam tampilan tabel di tab masing-masing
  / Admin views all entries in table views inside their respective tabs
    Given Admin berada di halaman "Education & Experience" di CMS
    / Admin is on the "Education & Experience" page in CMS
    Then Terdapat dua tab: "Pengalaman Kerja" dan "Pendidikan"
    / There are two tabs: "Work Experience" and "Education"
    And Tab "Pengalaman Kerja" menampilkan tabel pengalaman kerja (kolom: Perusahaan, Jabatan, Periode, Lokasi, Aksi)
    And Tab "Pendidikan" menampilkan tabel pendidikan (kolom: Institusi, Gelar/Jurusan, IPK, Periode, Aksi)

  Scenario: Admin menambah data menggunakan form laci (Sheet) yang berbeda
    Given Admin berada di tab "Pengalaman Kerja"
    When Admin mengklik "Tambah Pengalaman"
    Then Laci (Sheet) form pengalaman kerja muncul dengan field khusus pengalaman

    Given Admin berada di tab "Pendidikan"
    When Admin mengklik "Tambah Pendidikan"
    Then Laci (Sheet) form pendidikan muncul dengan field khusus pendidikan (Jurusan, Gelar, IPK)
```

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-10-001 | Tambah Riwayat Pengalaman Kerja | Must Have | 3 |
| US-10-002 | Tambah Riwayat Pendidikan | Must Have | 3 |
| US-10-003 | Edit Entri Riwayat | Must Have | 2 |
| US-10-004 | Hapus Entri Riwayat | Should Have | 1 |
| US-10-005 | Atur Urutan Tampil Riwayat | Should Have | 2 |
| US-10-006 | Recruiter Lihat Timeline (Publik) | Must Have | 3 |
| US-10-007 | Admin Lihat Daftar Riwayat di CMS | Must Have | 2 |
| | **Total** | | **16** |
