# US-04 — Modul Pengaturan Global & SEO / Global Settings & SEO

> **Referensi PRD:** Section 4.4
> **Tabel Database Utama:** `site_settings` (single-row record)

---

## Gambaran Modul / Module Overview

**ID:** Modul ini menyediakan satu titik kendali terpusat bagi admin untuk mengelola konten yang bersifat global/tidak terikat pada satu entitas spesifik — seperti teks hero di Beranda, file CV, tautan media sosial, dan meta tag default untuk seluruh situs. Pendekatan ini menghindari hard-coded content di sisi frontend, sehingga admin dapat mengubah teks-teks krusial tanpa perlu deployment ulang kode.

**EN:** This module provides a single centralized control point for the admin to manage global content not tied to a specific entity — such as hero text on the Home page, CV file, social media links, and default meta tags for the entire site. This approach avoids hard-coded content on the frontend side, allowing the admin to change crucial text without redeploying code.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Mengelola pengaturan global situs | Manages global site settings |
| **Pengunjung Umum** | Mengonsumsi data global di seluruh halaman publik (via Nuxt layout) | Consumes global data across all public pages (via Nuxt layout) |
| **Recruiter** | Mengunduh CV dari halaman publik | Downloads CV from the public page |

---

## Daftar User Story / User Story List

---

### US-04-001: Mengubah Teks Hero Dinamis Halaman Beranda / Update Dynamic Hero Text on Home Page

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengubah teks hero (headline utama, sub-headline/tagline, dan teks tombol Call-to-Action) di halaman Beranda melalui form pengaturan di CMS tanpa menyentuh kode / update the hero text (main headline, sub-headline/tagline, and Call-to-Action button text) on the Home page through the settings form in the CMS without touching code
**Agar / So that:** saya dapat menyesuaikan pesan personal branding secara fleksibel sesuai target audiens saat ini tanpa memerlukan bantuan developer / I can flexibly adjust my personal branding message according to the current target audience without needing developer assistance

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Update Teks Hero Beranda / Update Home Page Hero Text

  Scenario: Admin mengubah headline dan tagline Beranda
  / Admin updates the Home page headline and tagline
    Given Admin berada di halaman "Pengaturan Global" / Admin is on the "Global Settings" page
    When Admin mengubah field "Headline" menjadi teks baru
    / Admin changes the "Headline" field to new text
    And Admin mengubah field "Sub-headline / Tagline" / Admin changes the "Sub-headline / Tagline" field
    And Admin mengubah field "Teks Tombol CTA" / Admin changes the "CTA Button Text" field
    And Admin mengklik "Simpan Pengaturan" / Admin clicks "Save Settings"
    Then Perubahan tersimpan ke record `site_settings` di database
    / Changes are saved to the `site_settings` record in the database
    And Halaman Beranda publik langsung menampilkan teks hero yang baru
    / The public Home page immediately displays the new hero text
    (karena Nuxt mengambil data dari API setiap kali halaman di-render via SSR)
    / (because Nuxt fetches data from the API every time the page is rendered via SSR)

  Scenario: Field headline tidak boleh dikosongkan
  / Headline field cannot be left empty
    Given Admin menghapus isi field "Headline" / Admin clears the "Headline" field
    When Admin mengklik "Simpan Pengaturan" / Admin clicks "Save Settings"
    Then Sistem menampilkan error validasi: "Headline wajib diisi."
    / System displays validation error: "Headline is required."
    And Perubahan tidak disimpan / Changes are not saved
```

#### Referensi Teknis / Technical References

**Tabel:** `site_settings` (single row)
```
hero_headline (varchar 255, not null)
hero_subheadline (text, nullable)
hero_cta_text (varchar 100, nullable)
hero_cta_url (varchar 255, nullable)
```
**API:** `GET /api/v1/settings` — dikonsumsi oleh Nuxt layout/composable

---

### US-04-002: Mengunggah dan Memperbarui File CV / Upload and Update CV File

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengunggah file CV dalam format PDF dan memperbaruinya kapan saja melalui panel CMS / upload a CV file in PDF format and update it anytime through the CMS panel
**Agar / So that:** recruiter dan calon klien selalu memiliki akses ke versi CV terbaru saya langsung dari halaman Beranda / recruiters and potential clients always have access to my latest CV version directly from the Home page

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Upload dan Update CV / Upload and Update CV

  Scenario: Admin mengunggah CV PDF baru
  / Admin uploads a new CV PDF
    Given Admin berada di halaman "Pengaturan Global" / Admin is on "Global Settings" page
    When Admin mengklik "Upload CV Baru" dan memilih file PDF
    / Admin clicks "Upload New CV" and selects a PDF file
    Then Sistem memvalidasi bahwa file adalah PDF dengan ukuran maksimal 5MB
    / System validates that the file is PDF with maximum 5MB size
    And File PDF disimpan ke storage dengan nama yang konsisten (misal: cv-latest.pdf)
    / PDF file is saved to storage with a consistent name (e.g., cv-latest.pdf)
    And Path file tersimpan di kolom `cv_file_path` pada `site_settings`
    / File path is saved in the `cv_file_path` column in `site_settings`
    And Tombol "Download CV" di halaman Beranda publik mengarah ke file yang baru
    / "Download CV" button on the public Home page points to the new file

  Scenario: Admin memperbarui CV yang sudah ada
  / Admin updates an existing CV
    Given CV sudah ada di sistem / CV already exists in the system
    When Admin mengunggah file PDF baru / Admin uploads a new PDF file
    Then File lama dihapus dari storage / Old file is deleted from storage
    And File baru menggantikannya (overwrite dengan path yang sama atau update path)
    / New file replaces it (overwrite with same path or update path)

  Scenario: Pengunjung mengunduh CV dari halaman Beranda
  / Visitor downloads CV from Home page
    Given CV sudah diunggah / CV has been uploaded
    When Pengunjung mengklik tombol "Download CV" / Visitor clicks "Download CV" button
    Then Browser memulai download file PDF / Browser starts downloading the PDF file
    And File terunduh dengan nama yang deskriptif (misal: CV-Muhammad-Ihsan-Maulana.pdf)
    / File downloads with a descriptive name (e.g., CV-Muhammad-Ihsan-Maulana.pdf)
```

#### Referensi Teknis / Technical References

**Tabel:** `site_settings` (kolom `cv_file_path` — varchar 255, nullable)
**Storage:** File disimpan di `storage/cv/` — diakses via URL publik
**Validasi:** `mimes:pdf|max:5120` (5MB)

---

### US-04-003: Mengatur Tautan Media Sosial dan Platform Profesional / Manage Social Media and Professional Platform Links

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur dan memperbarui tautan ke semua profil media sosial dan platform profesional saya (LinkedIn, GitHub, Telegram, Instagram, email) melalui satu form pengaturan / set and update links to all my social media profiles and professional platforms (LinkedIn, GitHub, Telegram, Instagram, email) through a single settings form
**Agar / So that:** tautan sosial yang konsisten dan terkini tampil di Navbar dan Footer seluruh halaman publik tanpa perlu mengubah kode template / consistent and up-to-date social links appear in the Navbar and Footer of all public pages without needing to change template code

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Pengaturan Tautan Media Sosial / Social Media Links Settings

  Scenario: Admin mengisi semua tautan media sosial
  / Admin fills in all social media links
    Given Admin berada di halaman "Pengaturan Global" / Admin is on "Global Settings" page
    When Admin mengisi field-field tautan yang tersedia:
    / Admin fills in the available link fields:
      - LinkedIn URL
      - GitHub URL
      - Telegram Channel/Personal URL
      - Instagram URL
      - Twitter/X URL
      - Email Kontak / Contact Email
    And Admin mengklik "Simpan" / Admin clicks "Save"
    Then Semua URL tersimpan di record `site_settings`
    / All URLs are saved in the `site_settings` record
    And Ikon media sosial di Navbar dan Footer halaman publik aktif dan mengarah ke URL yang benar
    / Social media icons in the Navbar and Footer of public pages are active and point to the correct URLs

  Scenario: Field yang tidak diisi tidak menampilkan ikon di frontend
  / Unfilled fields do not show icons on the frontend
    Given Admin mengosongkan field "Instagram URL"
    / Admin clears the "Instagram URL" field
    When Admin menyimpan / Admin saves
    Then Ikon Instagram TIDAK muncul di Navbar/Footer halaman publik
    / Instagram icon does NOT appear in the Navbar/Footer of public pages

  Scenario: Validasi format URL
  / URL format validation
    Given Admin mengisi field URL dengan format yang tidak valid (tanpa https://)
    / Admin fills in a URL field with invalid format (without https://)
    When Admin mencoba menyimpan / Admin tries to save
    Then Sistem menampilkan error validasi untuk field yang bermasalah
    / System displays validation error for the problematic field
```

#### Referensi Teknis / Technical References

**Tabel:** `site_settings`
```
social_linkedin (varchar 255, nullable)
social_github   (varchar 255, nullable)
social_telegram (varchar 255, nullable)
social_instagram (varchar 255, nullable)
social_twitter  (varchar 255, nullable)
contact_email   (varchar 255, nullable)
```

---

### US-04-004: Mengatur Global Meta Tags Default / Set Default Global Meta Tags

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur global meta tags default (site name, default meta title suffix, default meta description, default Open Graph image) yang digunakan sebagai fallback di halaman yang belum memiliki override meta spesifik / set default global meta tags (site name, default meta title suffix, default meta description, default Open Graph image) used as fallback on pages that don't have specific meta overrides
**Agar / So that:** setiap halaman situs selalu memiliki meta tag yang bermakna untuk SEO dan tampilan social sharing, bahkan jika halaman tersebut belum memiliki konfigurasi meta individual / every site page always has meaningful meta tags for SEO and social sharing appearance, even if the page doesn't have individual meta configuration

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Pengaturan Global Meta Tags / Global Meta Tags Settings

  Scenario: Admin mengatur meta tags default
  / Admin sets default meta tags
    Given Admin berada di section "SEO & Meta" pada halaman Pengaturan Global
    / Admin is in the "SEO & Meta" section on the Global Settings page
    When Admin mengisi:
    / Admin fills in:
      - "Site Name" (misal: "growthcoder.id")
      / "Site Name" (e.g., "growthcoder.id")
      - "Meta Title Suffix" (misal: " | growthcoder.id")
      / "Meta Title Suffix" (e.g., " | growthcoder.id")
      - "Default Meta Description" (max 160 karakter)
      / "Default Meta Description" (max 160 characters)
      - "Default OG Image" (dipilih dari Media Library)
      / "Default OG Image" (selected from Media Library)
    And Admin menyimpan / Admin saves
    Then Nilai tersimpan di `site_settings`
    / Values are saved in `site_settings`
    And Halaman publik yang tidak punya meta override menggunakan nilai ini
    / Public pages without meta override use these values

  Scenario: Fallback meta tags diterapkan di halaman tanpa override
  / Fallback meta tags applied on pages without override
    Given Halaman "Tentang Saya" tidak memiliki meta title & description khusus
    / The "About Me" page has no custom meta title & description
    When Googlebot mengakses /about / Googlebot accesses /about
    Then `<title>` berisi: "Tentang Saya {Meta Title Suffix}" = "Tentang Saya | growthcoder.id"
    / `<title>` contains: "About Me {Meta Title Suffix}" = "About Me | growthcoder.id"
    And `<meta name="description">` menggunakan "Default Meta Description" dari settings
    / `<meta name="description">` uses "Default Meta Description" from settings
    And `<meta property="og:image">` menggunakan Default OG Image dari settings
    / `<meta property="og:image">` uses Default OG Image from settings
```

#### Referensi Teknis / Technical References

**Tabel:** `site_settings`
```
site_name              (varchar 255, not null, default: 'growthcoder.id')
meta_title_suffix      (varchar 100, nullable)
default_meta_desc      (text, nullable)
default_og_image_id    (FK → media.id, nullable, set null on delete)
```
**Nuxt:** Composable `useGlobalSettings()` dipanggil di `app.vue` atau `layouts/default.vue` untuk menyediakan fallback ke seluruh halaman

---

### US-04-005: Mengatur Informasi Identitas Pribadi / Manage Personal Identity Information

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur informasi identitas dasar yang tampil di berbagai bagian situs: nama lengkap, jabatan/peran profesional, dan foto profil / manage the basic identity information that appears in various parts of the site: full name, professional title/role, and profile photo
**Agar / So that:** representasi visual dan teks identitas profesional saya konsisten di seluruh halaman — Beranda, Tentang Saya, Navbar — tanpa perlu mengubah template kode satu per satu / my professional identity's visual and text representation is consistent across all pages — Home, About Me, Navbar — without needing to change code templates one by one

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Pengaturan Identitas Pribadi / Personal Identity Settings

  Scenario: Admin mengubah nama, jabatan, dan foto profil
  / Admin changes name, title, and profile photo
    Given Admin berada di section "Profil" pada halaman Pengaturan Global
    / Admin is in the "Profile" section on the Global Settings page
    When Admin mengubah field "Nama Lengkap", "Jabatan/Peran", dan memilih foto profil baru dari Media Library
    / Admin changes "Full Name", "Title/Role" fields, and selects new profile photo from Media Library
    And Admin menyimpan / Admin saves
    Then Perubahan tersimpan di `site_settings`
    / Changes are saved in `site_settings`
    And Nama dan jabatan yang baru tampil di Hero Beranda, About page, dan JSON-LD Person schema
    / New name and title appear in Home Hero, About page, and JSON-LD Person schema
    And Foto profil baru tampil di Hero Beranda dan bagian Tentang Saya
    / New profile photo appears in Home Hero and About Me section

  Scenario: JSON-LD Person schema di-update otomatis
  / JSON-LD Person schema is automatically updated
    Given Admin telah menyimpan nama dan tautan sosial terbaru
    / Admin has saved the latest name and social links
    When Googlebot mengakses halaman Beranda / Googlebot accesses the Home page
    Then Tag `<script type="application/ld+json">` berisi schema `Person` yang akurat:
    / The `<script type="application/ld+json">` tag contains an accurate `Person` schema:
    ```json
    {
      "@type": "Person",
      "name": "[Nama dari site_settings]",
      "jobTitle": "[Jabatan dari site_settings]",
      "sameAs": ["[LinkedIn URL]", "[GitHub URL]"]
    }
    ```
```

#### Referensi Teknis / Technical References

**Tabel:** `site_settings`
```
owner_full_name    (varchar 255, not null)
owner_title        (varchar 255, nullable — misal: "Full-Stack Developer")
profile_photo_id   (FK → media.id, nullable, set null on delete)
```

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-04-001 | Update Teks Hero Beranda | Must Have | 3 |
| US-04-002 | Upload dan Update File CV | Must Have | 3 |
| US-04-003 | Kelola Tautan Media Sosial | Must Have | 2 |
| US-04-004 | Atur Global Meta Tags Default | Must Have | 3 |
| US-04-005 | Kelola Informasi Identitas Pribadi | Must Have | 3 |
| | **Total** | | **14** |
