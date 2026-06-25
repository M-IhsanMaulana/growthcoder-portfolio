# US-08 — Modul Manajemen Skill / Skills Management

> **Referensi PRD:** Section 4.8
> **Tabel Database Utama:** `skills` (relasi ke `technologies`)

---

## Gambaran Modul / Module Overview

**ID:** Modul ini memfasilitasi pengelolaan informasi keterampilan pengembang yang akan dipamerkan secara visual di halaman publik (About/Resume). Modul ini secara cerdas menggunakan data dari modul `technologies` (Modul 07) untuk menghindari pengisian berulang dan memastikan keselarasan logo. Setiap skill memiliki level kemahiran (persentase) dan estimasi tahun pengalaman.

**EN:** This module facilitates the management of developer skill information to be visually showcased on public pages (About/Resume). The module intelligently uses data from the `technologies` module (Module 07) to avoid duplicate data entry and ensure logo consistency. Each skill has a proficiency level (percentage) and estimated years of experience.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Mengelola data keahlian teknis | Manages technical skill data |
| **Pengunjung Umum** | Melihat visualisasi keahlian di halaman About/Resume publik | Views skill visualization on the public About/Resume page |
| **Recruiter** | Menilai kesesuaian keahlian teknis dengan spesifikasi pekerjaan | Assesses technical skill fit with job specifications |

---

## Daftar User Story / User Story List

---

### US-08-001: Menambahkan Keahlian Baru / Add New Skill

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menambahkan keahlian baru dengan memilih teknologi dari master data, mengisi tingkat kemahiran (0-100%), dan lama pengalaman / add a new skill by selecting a technology from the master data, filling in the proficiency level (0-100%), and years of experience
**Agar / So that:** profil keahlian teknis saya di halaman publik selalu mencerminkan kemampuan saya yang sesungguhnya secara akurat / my technical skills profile on the public page always accurately reflects my actual capabilities

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Menambahkan Keahlian Baru / Add New Skill

  Scenario: Admin berhasil menambahkan keahlian baru
  / Admin successfully adds a new skill
    Given Admin berada di halaman "Skills Management" di CMS
    / Admin is on the "Skills Management" page in CMS
    And Master Tech Stack (Modul 07) sudah memiliki data teknologi
    / Master Tech Stack (Module 07) already has technology data
    When Admin mengklik "Tambah Keahlian" / Admin clicks "Add Skill"
    And Admin memilih teknologi dari dropdown (misal: "Vue.js")
    / Admin selects a technology from the dropdown (e.g., "Vue.js")
    And Admin mengisi:
    / Admin fills in:
      - Proficiency Level: 90 (dalam persen, slider 0-100)
      / Proficiency Level: 90 (in percent, slider 0-100)
      - Years of Experience: 3.5 (dalam tahun, desimal 1 digit)
      / Years of Experience: 3.5 (in years, 1 decimal digit)
      - Is Featured: true / false (checkbox)
      - Order: 1 (urutan manual)
      / Order: 1 (manual order)
    And Admin menyimpan / Admin saves
    Then Record tersimpan ke tabel `skills` dengan relasi ke `technology_id = [id Vue.js]`
    / Record is saved to the `skills` table with relation to `technology_id = [Vue.js id]`
    And Keahlian "Vue.js" dengan bar progress 90% muncul di halaman About publik
    / "Vue.js" skill with 90% progress bar appears on the public About page

  Scenario: Tidak dapat menambahkan skill untuk teknologi yang sama dua kali
  / Cannot add skill for the same technology twice
    Given "Laravel" sudah ada sebagai entry skill / "Laravel" already exists as a skill entry
    When Admin mencoba menambahkan "Laravel" lagi sebagai skill
    / Admin tries to add "Laravel" again as a skill
    Then Sistem menampilkan error: "Keahlian untuk teknologi 'Laravel' sudah terdaftar."
    / System displays error: "Skill for technology 'Laravel' is already registered."

  Scenario: Proficiency level harus antara 0 dan 100
  / Proficiency level must be between 0 and 100
    Given Admin mengisi form skill baru / Admin fills in the new skill form
    When Admin mengisi Proficiency Level dengan nilai 150
    / Admin fills in Proficiency Level with value 150
    Then Sistem menampilkan error validasi: "Tingkat kemahiran harus antara 0 dan 100."
    / System displays validation error: "Proficiency level must be between 0 and 100."
```

#### Referensi Teknis / Technical References

**Tabel:** `skills`
```
id, technology_id (FK → technologies.id, Cascade Delete),
proficiency_level (integer, not null — 0 to 100),
years_of_experience (decimal(3,1), nullable — misal: 3.5),
is_featured (boolean, not null, default: false),
order (integer, not null, default: 0),
created_at, updated_at

UNIQUE CONSTRAINT: (technology_id) — satu teknologi = satu skill entry
```

---

### US-08-002: Mengedit Data Keahlian / Edit Skill Data

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengedit tingkat kemahiran, lama pengalaman, dan pengaturan featured dari keahlian yang sudah ada / edit the proficiency level, years of experience, and featured setting of an existing skill
**Agar / So that:** profil keahlian selalu mencerminkan level saat ini, terutama setelah saya memperoleh pengalaman lebih banyak dalam teknologi tertentu / the skills profile always reflects the current level, especially after gaining more experience in a specific technology

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Edit Data Keahlian / Edit Skill Data

  Scenario: Admin memperbarui proficiency level sebuah skill
  / Admin updates the proficiency level of a skill
    Given Admin membuka form edit untuk keahlian "PHP"
    / Admin opens the edit form for the "PHP" skill
    When Admin menggeser slider Proficiency Level dari 85 ke 92
    / Admin slides the Proficiency Level slider from 85 to 92
    And Admin menyimpan / Admin saves
    Then `proficiency_level = 92` tersimpan di database
    / `proficiency_level = 92` is saved in the database
    And Bar progress untuk "PHP" di halaman publik menampilkan 92%
    / Progress bar for "PHP" on the public page shows 92%
```

---

### US-08-003: Mengatur Urutan Tampil Keahlian / Set Skill Display Order

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur urutan tampil keahlian di halaman About/Resume secara manual / manually set the display order of skills on the About/Resume page
**Agar / So that:** keahlian utama saya (yang paling relevan untuk calon klien/recruiter) ditampilkan di posisi teratas / my main skills (most relevant to potential clients/recruiters) are displayed at the top

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Urutan Tampil Keahlian / Skill Display Order

  Scenario: Admin mengatur urutan keahlian melalui drag-and-drop atau input order
  / Admin sets skill order via drag-and-drop or order input
    Given Admin berada di daftar Skills di CMS / Admin is on the Skills list in CMS
    When Admin mengubah urutan skill melalui drag-and-drop atau mengubah nilai field "Order"
    / Admin changes skill order via drag-and-drop or by changing the "Order" field value
    And Admin menyimpan / Admin saves
    Then Halaman About publik menampilkan keahlian sesuai urutan yang baru
    / Public About page displays skills in the new order
    And Keahlian dengan `order` terendah tampil paling atas
    / Skill with the lowest `order` value appears at the top
```

---

### US-08-004: Menandai Keahlian sebagai "Featured" / Mark Skill as "Featured"

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menandai beberapa keahlian utama sebagai "Featured" agar ditampilkan di section highlight di halaman Beranda / mark some main skills as "Featured" to be displayed in the highlight section on the Home page
**Agar / So that:** pengunjung halaman Beranda langsung melihat keahlian teknis inti saya tanpa perlu navigasi ke halaman About / Home page visitors immediately see my core technical skills without needing to navigate to the About page

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Skill Featured di Halaman Beranda / Featured Skill on Home Page

  Scenario: Admin menandai keahlian sebagai featured
  / Admin marks a skill as featured
    Given Admin sedang mengedit atau melihat daftar skill
    / Admin is editing or viewing the skill list
    When Admin mengaktifkan checkbox "Featured" untuk skill "Laravel" dan "Nuxt.js"
    / Admin activates the "Featured" checkbox for "Laravel" and "Nuxt.js" skills
    And Admin menyimpan / Admin saves
    Then Logo + nama + proficiency bar skill featured ditampilkan di section "Keahlian Utama" di halaman Beranda
    / Featured skill logo + name + proficiency bar are displayed in the "Main Skills" section on the Home page
```

---

### US-08-005: Menghapus Entry Keahlian / Delete Skill Entry

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus entry keahlian dari profil jika teknologi tersebut sudah tidak relevan atau saya tidak lagi menggunakannya secara aktif / delete a skill entry from the profile if the technology is no longer relevant or I no longer actively use it
**Agar / So that:** profil keahlian tetap akurat dan tidak membingungkan recruiter / calon klien / the skills profile remains accurate and doesn't confuse recruiters / potential clients

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 1 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Entry Keahlian / Delete Skill Entry

  Scenario: Admin menghapus entry skill
  / Admin deletes a skill entry
    Given Admin berada di daftar skill / Admin is on the skill list
    When Admin mengklik "Hapus" pada baris skill dan mengonfirmasi
    / Admin clicks "Delete" on a skill row and confirms
    Then Record dihapus dari tabel `skills`
    / Record is deleted from the `skills` table
    And Keahlian tersebut tidak lagi tampil di halaman About publik
    / The skill no longer appears on the public About page
    And Data teknologi terkait di tabel `technologies` TIDAK ikut terhapus
    / Related technology data in the `technologies` table is NOT deleted
```

---

### US-08-006: Pengunjung Melihat Visualisasi Keahlian di Halaman Publik / Visitor Views Skill Visualization on Public Page

**Sebagai / As a:** Recruiter / Calon Klien
**Saya ingin / I want:** melihat visualisasi diagram atau progress bar keahlian teknis developer secara jelas di halaman About/Resume / see a clear visualization of the developer's technical skills as a diagram or progress bar on the About/Resume page
**Agar / So that:** saya dapat dengan cepat menilai apakah kedalaman dan luas keahlian developer sesuai dengan spesifikasi proyek atau pekerjaan yang saya miliki / I can quickly assess whether the breadth and depth of the developer's skills match the project or job specifications I have

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Visualisasi Keahlian di Halaman Publik / Skill Visualization on Public Page

  Scenario: Pengunjung melihat section keahlian di halaman About
  / Visitor views the skills section on the About page
    Given Pengunjung membuka halaman /about / Visitor opens the /about page
    Then Section "Keahlian Teknis" menampilkan setiap skill dengan:
    / "Technical Skills" section displays each skill with:
      - Logo teknologi dari Media Library / Technology logo from Media Library
      - Nama teknologi / Technology name
      - Progress bar visual yang menunjukkan persentase kemahiran (0-100%)
      / Visual progress bar showing proficiency percentage (0-100%)
      - Tulisan persentase (misal: "90%") / Percentage text (e.g., "90%")
      - Estimasi tahun pengalaman (misal: "3.5 tahun") / Estimated years of experience (e.g., "3.5 years")
    And Skill dikelompokkan berdasarkan kategori teknologi (Frontend, Backend, DevOps, dst.)
    / Skills are grouped by technology category (Frontend, Backend, DevOps, etc.)
    And Urutan tampil mengikuti field `order` yang diatur admin
    / Display order follows the `order` field set by the admin

  Scenario: Animasi muncul saat progress bar masuk viewport
  / Animation appears when progress bar enters viewport
    Given Pengunjung menggulir halaman hingga section keahlian terlihat
    / Visitor scrolls the page until the skills section is visible
    Then Progress bar beranimasi dari 0% ke nilai kemahiran yang sebenarnya
    / Progress bar animates from 0% to the actual proficiency value
    (menggunakan CSS transition atau Intersection Observer)
    / (using CSS transition or Intersection Observer)
```

#### Referensi Teknis / Technical References

**API Endpoint:** `GET /api/v1/skills` — relasi ke `technologies` (eager load: name, logo, category)
**Nuxt Page:** `pages/about.vue` — SSR dengan `useFetch`

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-08-001 | Tambah Keahlian Baru | Must Have | 3 |
| US-08-002 | Edit Data Keahlian | Must Have | 2 |
| US-08-003 | Atur Urutan Tampil Keahlian | Should Have | 2 |
| US-08-004 | Tandai Keahlian sebagai Featured | Should Have | 2 |
| US-08-005 | Hapus Entry Keahlian | Should Have | 1 |
| US-08-006 | Visualisasi Keahlian di Halaman Publik | Must Have | 3 |
| | **Total** | | **13** |
