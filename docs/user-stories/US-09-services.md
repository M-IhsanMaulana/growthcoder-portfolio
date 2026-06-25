# US-09 — Modul Manajemen Layanan / Services Management

> **Referensi PRD:** Section 4.9
> **Tabel Database Utama:** `services`

---

## Gambaran Modul / Module Overview

**ID:** Modul ini digunakan untuk mengelola layanan profesional yang ditawarkan oleh pemilik produk (misal: Full-Stack Web Development, API Integration, Telegram Bot Development, Performance Optimization). Informasi ini berfungsi sebagai sarana promosi langsung (sales landing) bagi calon klien, menampilkan katalog layanan dengan deskripsi detail dan harga mulai dari.

**EN:** This module manages the professional services offered by the product owner (e.g., Full-Stack Web Development, API Integration, Telegram Bot Development, Performance Optimization). This information serves as a direct sales landing tool for potential clients, displaying a service catalog with detailed descriptions and starting prices.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Administrator** | Mengelola katalog layanan yang ditawarkan | Manages the service catalog offered |
| **Calon Klien** | Menjelajahi dan membaca detail layanan sebelum menghubungi | Browses and reads service details before making contact |
| **Pengunjung Umum** | Melihat layanan yang tersedia di halaman publik | Views available services on the public page |

---

## Daftar User Story / User Story List

---

### US-09-001: Membuat Layanan Baru / Create New Service

**Sebagai / As a:** Administrator
**Saya ingin / I want:** membuat entri layanan profesional baru dengan judul, deskripsi singkat, deskripsi detail (rich text), ikon representatif, harga awal (opsional), dan status aktif / create a new professional service entry with title, short description, detailed description (rich text), representative icon, starting price (optional), and active status
**Agar / So that:** calon klien dapat memahami cakupan dan nilai dari setiap layanan yang saya tawarkan sebelum memutuskan untuk menghubungi / potential clients can understand the scope and value of each service I offer before deciding to make contact

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Membuat Layanan Baru / Create New Service

  Scenario: Admin berhasil membuat layanan baru
  / Admin successfully creates a new service
    Given Admin berada di halaman "Services Management" di CMS
    / Admin is on the "Services Management" page in CMS
    When Admin mengklik "Tambah Layanan" / Admin clicks "Add Service"
    And Admin mengisi field wajib: Judul, Short Description (maks 200 karakter), Status (aktif)
    / Admin fills in required fields: Title, Short Description (max 200 chars), Status (active)
    And Admin mengisi field opsional: Long Description (rich text), Ikon, Harga Mulai Dari, Urutan
    / Admin fills in optional fields: Long Description (rich text), Icon, Price Starting From, Order
    And Admin mengklik "Simpan" / Admin clicks "Save"
    Then Slug otomatis di-generate dari judul
    / Slug is auto-generated from the title
    And Record tersimpan ke tabel `services` dengan `is_active = true`
    / Record is saved to the `services` table with `is_active = true`
    And Layanan baru muncul di section "Layanan" pada halaman publik (Beranda atau halaman Layanan)
    / New service appears in the "Services" section on the public page (Home or Services page)

  Scenario: Karakter short description melebihi batas
  / Short description character limit exceeded
    Given Admin mengisi "Short Description" dengan lebih dari 200 karakter
    / Admin fills "Short Description" with more than 200 characters
    When Admin menyimpan / Admin saves
    Then Sistem menampilkan error: "Deskripsi singkat maksimal 200 karakter."
    / System displays error: "Short description maximum 200 characters."

  Scenario: Slug otomatis dari judul
  / Slug auto-generated from title
    Given Admin mengisi judul "Telegram Bot Development"
    / Admin fills in title "Telegram Bot Development"
    When Admin berpindah ke field berikutnya / Admin moves to the next field
    Then Slug terisi otomatis: "telegram-bot-development"
    / Slug is auto-filled: "telegram-bot-development"
```

#### Referensi Teknis / Technical References

**Tabel:** `services`
```
id, title (varchar 255, not null), slug (varchar 255, not null, unique),
short_description (text, not null — max 200 chars),
long_description (text, nullable — HTML rich text),
icon (varchar 255, nullable — nama Lucide icon atau path SVG),
price_starts_from (decimal(12,2), nullable — misal: 1500000.00),
is_active (boolean, not null, default: true),
order (integer, not null, default: 0),
created_at, updated_at
```

---

### US-09-002: Mengedit Layanan / Edit Service

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengedit informasi layanan yang sudah ada — termasuk memperbarui harga, mengubah deskripsi, atau mengganti ikon / edit existing service information — including updating the price, changing the description, or replacing the icon
**Agar / So that:** informasi layanan di halaman publik selalu akurat dan relevan dengan penawaran terbaru saya / service information on the public page is always accurate and relevant to my latest offerings

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Edit Layanan / Edit Service

  Scenario: Admin memperbarui harga layanan
  / Admin updates service price
    Given Admin membuka form edit untuk layanan "Telegram Bot Development"
    / Admin opens the edit form for "Telegram Bot Development" service
    When Admin mengubah nilai "Harga Mulai Dari" dari 1.500.000 menjadi 2.000.000
    / Admin changes "Price Starting From" value from 1,500,000 to 2,000,000
    And Admin menyimpan / Admin saves
    Then `price_starts_from = 2000000.00` tersimpan di database
    / `price_starts_from = 2000000.00` is saved in the database
    And Halaman publik langsung menampilkan harga yang diperbarui
    / Public page immediately displays the updated price

  Scenario: Admin memperbarui deskripsi detail layanan
  / Admin updates service detailed description
    Given Admin membuka form edit layanan / Admin opens the service edit form
    When Admin mengedit konten di rich text editor "Long Description"
    / Admin edits content in the "Long Description" rich text editor
    And Admin menyimpan / Admin saves
    Then Deskripsi yang diperbarui tersimpan dan tampil di halaman detail layanan publik
    / Updated description is saved and displayed on the public service detail page
```

---

### US-09-003: Menonaktifkan Layanan Sementara / Temporarily Deactivate a Service

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menonaktifkan layanan tertentu sementara tanpa menghapusnya dari database, jika kapasitas pengerjaan saya sedang penuh / temporarily deactivate a specific service without deleting it from the database when my work capacity is full
**Agar / So that:** saya tidak menerima inquiry baru untuk layanan yang sedang tidak saya terima, sambil tetap menjaga data layanan untuk diaktifkan kembali nanti / I don't receive new inquiries for services I'm currently not accepting, while keeping the service data for reactivation later

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Nonaktifkan Layanan Sementara / Temporarily Deactivate Service

  Scenario: Admin menonaktifkan layanan
  / Admin deactivates a service
    Given Layanan "Full-Stack Web Development" saat ini aktif (`is_active = true`)
    / Service "Full-Stack Web Development" is currently active (`is_active = true`)
    When Admin mengubah toggle "Status Aktif" menjadi tidak aktif
    / Admin changes the "Active Status" toggle to inactive
    And Admin menyimpan / Admin saves
    Then `is_active = false` tersimpan di database
    / `is_active = false` is saved in the database
    And Layanan TIDAK muncul di halaman publik
    / Service does NOT appear on the public page
    And Layanan tetap ada di daftar CMS (dengan label "Nonaktif" / "Inactive")
    / Service still exists in the CMS list (with "Inactive" label)

  Scenario: Admin mengaktifkan kembali layanan
  / Admin reactivates a service
    Given Layanan dalam kondisi nonaktif / Service is in inactive state
    When Admin mengubah toggle "Status Aktif" menjadi aktif kembali
    / Admin changes "Active Status" toggle back to active
    And Admin menyimpan / Admin saves
    Then `is_active = true` tersimpan / `is_active = true` is saved
    And Layanan muncul kembali di halaman publik
    / Service appears again on the public page
```

---

### US-09-004: Mengatur Urutan Tampil Layanan / Set Service Display Order

**Sebagai / As a:** Administrator
**Saya ingin / I want:** mengatur urutan tampil layanan di halaman publik agar layanan yang paling populer atau paling ingin saya promosikan tampil lebih dulu / set the service display order on the public page so the most popular or most promoted services appear first
**Agar / So that:** pengunjung langsung melihat layanan terbaik dan paling relevan di posisi pertama / visitors immediately see the best and most relevant services in the first position

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Urutan Tampil Layanan / Service Display Order

  Scenario: Admin mengatur urutan layanan
  / Admin sets service order
    Given Admin berada di daftar Services di CMS / Admin is on the Services list in CMS
    When Admin mengubah nilai field "Urutan" pada setiap layanan (atau via drag-and-drop)
    / Admin changes the "Order" field value for each service (or via drag-and-drop)
    And Admin menyimpan / Admin saves
    Then Halaman publik menampilkan layanan sesuai urutan baru (berdasarkan `order` ascending)
    / Public page displays services in the new order (based on `order` ascending)
```

---

### US-09-005: Menghapus Layanan / Delete Service

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menghapus layanan yang sudah benar-benar tidak ditawarkan lagi dan tidak ada kemungkinan akan diaktifkan kembali / delete a service that is no longer offered at all and there is no possibility of reactivation
**Agar / So that:** database tetap bersih dari data yang sudah tidak relevan / the database remains clean from irrelevant data

| Atribut | Nilai |
|---|---|
| **Priority** | 🟡 Should Have |
| **Story Points** | 1 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Hapus Layanan / Delete Service

  Scenario: Admin menghapus layanan dengan konfirmasi
  / Admin deletes a service with confirmation
    Given Admin berada di daftar atau halaman edit layanan
    / Admin is on the services list or edit page
    When Admin mengklik "Hapus" / Admin clicks "Delete"
    Then Dialog konfirmasi muncul: "Hapus layanan ini secara permanen?"
    / Confirmation dialog appears: "Permanently delete this service?"
    When Admin mengonfirmasi / Admin confirms
    Then Record dihapus dari tabel `services`
    / Record is deleted from the `services` table
    And Layanan tidak lagi muncul di halaman publik
    / Service no longer appears on the public page
```

---

### US-09-006: Calon Klien Melihat dan Membaca Detail Layanan / Potential Client Views and Reads Service Details

**Sebagai / As a:** Calon Klien
**Saya ingin / I want:** melihat katalog layanan yang tersedia dan membaca deskripsi detail setiap layanan beserta informasi harganya / see the available service catalog and read the detailed description of each service along with pricing information
**Agar / So that:** saya dapat menentukan apakah layanan yang ditawarkan sesuai dengan kebutuhan dan anggaran proyek saya sebelum menghubungi developer / I can determine if the services offered match my project needs and budget before contacting the developer

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Melihat Katalog Layanan di Halaman Publik / Viewing Service Catalog on Public Page

  Scenario: Pengunjung melihat daftar layanan di halaman Beranda
  / Visitor views service list on the Home page
    Given Pengunjung membuka halaman Beranda / Visitor opens the Home page
    Then Section "Layanan" menampilkan card untuk setiap layanan yang aktif (`is_active = true`)
    / "Services" section displays a card for each active service (`is_active = true`)
    And Setiap card menampilkan: ikon layanan, judul, deskripsi singkat, harga mulai dari (jika ada)
    / Each card displays: service icon, title, short description, price starting from (if any)
    And Layanan diurutkan berdasarkan field `order` / Services are sorted by the `order` field
    And Layanan dengan `is_active = false` TIDAK muncul / Services with `is_active = false` do NOT appear

  Scenario: Pengunjung membaca deskripsi detail layanan (jika ada halaman detail layanan)
  / Visitor reads service detailed description (if there is a service detail page)
    Given Layanan memiliki `long_description` yang diisi
    / Service has a filled `long_description`
    When Pengunjung mengklik "Pelajari Lebih Lanjut" / Visitor clicks "Learn More"
    Then Modal atau halaman baru menampilkan deskripsi detail layanan (rendered HTML)
    / Modal or new page displays the detailed service description (rendered HTML)

  Scenario: Harga tidak ditampilkan jika null
  / Price is not displayed if null
    Given Layanan tidak memiliki harga (price_starts_from = null)
    / Service has no price (price_starts_from = null)
    Then Text harga tidak ditampilkan di card atau halaman detail layanan
    / Price text is not displayed on the card or service detail page
```

#### Referensi Teknis / Technical References

**API Endpoint:** `GET /api/v1/services` — hanya mengembalikan layanan dengan `is_active = true`, diurutkan `order ASC`

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-09-001 | Buat Layanan Baru | Must Have | 5 |
| US-09-002 | Edit Layanan | Must Have | 2 |
| US-09-003 | Nonaktifkan Layanan Sementara | Must Have | 2 |
| US-09-004 | Atur Urutan Tampil Layanan | Should Have | 2 |
| US-09-005 | Hapus Layanan | Should Have | 1 |
| US-09-006 | Calon Klien Lihat Katalog Layanan | Must Have | 2 |
| | **Total** | | **14** |
