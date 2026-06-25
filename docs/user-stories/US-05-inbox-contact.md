# US-05 — Modul Inbox Form Kontak & Otomasi Telegram Bot / Inbox, Contact Form & Telegram Bot Automation

> **Referensi PRD:** Section 4.5
> **Tabel Database Utama:** `contact_messages`

---

## Gambaran Modul / Module Overview

**ID:** Modul ini adalah jembatan konversi antara pengunjung situs dengan pemilik produk. Setiap submission formulir kontak disimpan ke database (sebagai Inbox) dan secara otomatis memicu notifikasi instan ke Telegram pribadi admin melalui Bot API — memastikan tidak ada lead yang terlewat. Pengiriman Telegram berjalan secara asynchronous agar tidak memperlambat pengalaman pengunjung.

**EN:** This module is the conversion bridge between site visitors and the product owner. Every contact form submission is saved to the database (as an Inbox) and automatically triggers an instant notification to the admin's personal Telegram via the Bot API — ensuring no leads are missed. Telegram delivery runs asynchronously to avoid slowing down the visitor's experience.

---

## Aktor / Roles

| Aktor | Deskripsi (ID) | Description (EN) |
|---|---|---|
| **Pengunjung Umum** | Mengirim pesan melalui formulir kontak di halaman publik | Sends messages via the contact form on public pages |
| **Calon Klien** | Mengirim inquiry layanan atau pertanyaan bisnis | Sends service inquiries or business questions |
| **Administrator** | Menerima notifikasi Telegram dan meninjau Inbox di CMS | Receives Telegram notifications and reviews Inbox in CMS |

---

## Daftar User Story / User Story List

---

### US-05-001: Pengunjung Mengirim Pesan via Formulir Kontak / Visitor Sends Message via Contact Form

**Sebagai / As a:** Pengunjung Umum / Calon Klien
**Saya ingin / I want:** mengisi dan mengirimkan formulir kontak dengan nama, email, dan pesan saya dengan cara yang mudah dan cepat / fill out and submit a contact form with my name, email, and message in an easy and fast way
**Agar / So that:** saya dapat menghubungi developer untuk mendiskusikan kebutuhan proyek atau kerja sama tanpa perlu membuka aplikasi email / I can contact the developer to discuss project needs or collaboration without needing to open an email application

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 5 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Pengiriman Formulir Kontak Publik / Public Contact Form Submission

  Scenario: Pengunjung berhasil mengirim pesan kontak
  / Visitor successfully sends a contact message
    Given Pengunjung berada di halaman Beranda atau halaman Kontak
    / Visitor is on the Home page or Contact page
    When Pengunjung mengisi field: Nama (wajib), Email (wajib), Subjek (opsional), Pesan (wajib)
    / Visitor fills in fields: Name (required), Email (required), Subject (optional), Message (required)
    And Honeypot field tersembunyi dibiarkan kosong (kondisi normal manusia)
    / Hidden honeypot field is left empty (normal human condition)
    And Pengunjung mengklik tombol "Kirim Pesan" / Visitor clicks "Send Message" button
    Then Request dikirim ke `POST /api/v1/contact`
    / Request is sent to `POST /api/v1/contact`
    And Setelah validasi server berhasil, pesan tersimpan ke tabel `contact_messages`
    / After successful server validation, message is saved to `contact_messages` table
    And Response HTTP 201 dikembalikan ke frontend Nuxt
    / HTTP 201 response is returned to the Nuxt frontend
    And Nuxt menampilkan pesan sukses: "Pesan Anda berhasil terkirim! Saya akan segera menghubungi Anda."
    / Nuxt displays success message: "Your message was sent successfully! I will contact you shortly."
    And Form direset ke kondisi kosong / Form is reset to empty state

  Scenario: Validasi field wajib gagal
  / Required field validation fails
    Given Pengunjung membuka formulir kontak / Visitor opens the contact form
    When Pengunjung mengosongkan field "Nama" dan mengklik "Kirim"
    / Visitor leaves the "Name" field empty and clicks "Send"
    Then Error validasi ditampilkan di bawah field yang kosong
    / Validation error is displayed below the empty field
    And Request tidak dikirim ke server / Request is not sent to the server

  Scenario: Proteksi honeypot mendeteksi bot otomatis
  / Honeypot protection detects automated bot
    Given Ada bot yang mengisi formulir kontak secara otomatis
    / There is a bot automatically filling out the contact form
    When Bot juga mengisi honeypot field tersembunyi
    / Bot also fills in the hidden honeypot field
    Then Server mengembalikan HTTP 422 (Unprocessable Entity)
    / Server returns HTTP 422 (Unprocessable Entity)
    And Pesan TIDAK tersimpan ke database / Message is NOT saved to the database
    And Tidak ada notifikasi Telegram yang dikirim / No Telegram notification is sent

  Scenario: Rate limiting mencegah spam berulang
  / Rate limiting prevents repeated spam
    Given IP yang sama sudah mengirim 5 pesan dalam 1 jam
    / The same IP has already sent 5 messages within 1 hour
    When IP tersebut mencoba mengirim pesan ke-6 / The IP tries to send a 6th message
    Then Server mengembalikan HTTP 429 (Too Many Requests)
    / Server returns HTTP 429 (Too Many Requests)
    And Frontend menampilkan: "Terlalu banyak percobaan. Coba lagi dalam beberapa menit."
    / Frontend displays: "Too many attempts. Please try again in a few minutes."
```

#### Referensi Teknis / Technical References

**Tabel:** `contact_messages`
```
id, name (varchar 255, not null), email (varchar 255, not null),
subject (varchar 255, nullable), message (text, not null),
status (enum: unread|read|replied, default: unread),
sender_ip (varchar 45, nullable — untuk rate limiting log),
telegram_notified_at (timestamp, nullable),
created_at, updated_at
```
**API Endpoint:** `POST /api/v1/contact`
**Validasi:** `StoreContactMessageRequest` — rules: nama required|max:255, email required|email, pesan required|max:5000, honeypot must be empty
**Rate Limiting:** Laravel `throttle:5,60` per IP (5 requests per 60 minutes)

---

### US-05-002: Notifikasi Telegram Real-time saat Ada Pesan Baru / Real-time Telegram Notification on New Message

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menerima notifikasi real-time di Telegram pribadi saya setiap kali ada pengunjung yang mengirim pesan melalui formulir kontak / receive real-time notifications on my personal Telegram whenever a visitor sends a message through the contact form
**Agar / So that:** saya dapat merespons setiap lead atau inquiry dengan cepat tanpa harus membuka panel CMS secara berkala untuk memeriksa Inbox / I can respond to every lead or inquiry quickly without having to periodically open the CMS panel to check the Inbox

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 8 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Notifikasi Telegram untuk Pesan Kontak Baru / Telegram Notification for New Contact Message

  Scenario: Admin menerima notifikasi Telegram setelah pesan tersimpan
  / Admin receives Telegram notification after message is saved
    Given Konfigurasi `TELEGRAM_BOT_TOKEN` dan `TELEGRAM_CHAT_ID` sudah diatur di `.env`
    / `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID` configuration is set in `.env`
    And Queue worker Laravel sedang berjalan / Laravel queue worker is running
    When Pengunjung berhasil mengirim pesan kontak / Visitor successfully sends a contact message
    Then Controller men-dispatch `SendTelegramNotificationJob` ke queue
    / Controller dispatches `SendTelegramNotificationJob` to the queue
    And Queue worker memproses Job tersebut secara asynchronous
    / Queue worker processes the Job asynchronously
    And `TelegramNotifierService` mengirim HTTP POST ke Telegram Bot API:
    / `TelegramNotifierService` sends HTTP POST to Telegram Bot API:
    `https://api.telegram.org/bot{TOKEN}/sendMessage`
    And Admin menerima pesan Telegram dengan format:
    / Admin receives Telegram message with format:
    ```
    📬 Pesan Kontak Baru! / New Contact Message!
    
    👤 Nama / Name: [nama pengirim]
    📧 Email: [email pengirim]
    📌 Subjek / Subject: [subjek atau "(tidak ada)"]
    
    💬 Pesan / Message:
    [isi pesan, dibatasi 500 karakter jika terlalu panjang]
    
    ⏰ Diterima / Received: [timestamp]
    ```
    And Kolom `telegram_notified_at` pada record pesan diperbarui
    / The `telegram_notified_at` column on the message record is updated

  Scenario: Pengiriman Telegram gagal — tidak memengaruhi penyimpanan pesan
  / Telegram delivery fails — does not affect message saving
    Given Token Telegram tidak valid atau koneksi Telegram API timeout
    / Telegram token is invalid or Telegram API connection times out
    When Queue worker mencoba mengirim notifikasi / Queue worker tries to send notification
    Then Kegagalan dicatat ke Laravel log (`Log::error`)
    / Failure is logged to Laravel log (`Log::error`)
    And Pesan di database TETAP tersimpan dan Inbox TETAP berfungsi sebagai fallback
    / Message in the database REMAINS saved and Inbox REMAINS functional as fallback
    And `telegram_notified_at` tetap NULL / `telegram_notified_at` remains NULL
    And Job di-retry otomatis sesuai konfigurasi (misal: 3x dengan delay)
    / Job is automatically retried according to configuration (e.g., 3 times with delay)
```

#### Referensi Teknis / Technical References

**Pseudocode Alur Teknis Telegram:**
```
[Pengunjung Submit Form]
        ↓
[StoreContactMessageRequest — Validasi Server]
        ↓ (valid)
[ContactController@store]
        ↓
[Simpan ke DB: contact_messages (status: 'unread')]
        ↓
[Dispatch: SendTelegramNotificationJob → Queue]
        ↓                    ↓
[Return HTTP 201]    [Queue Worker (async)]
[Nuxt: Tampilkan      ↓
 pesan sukses]   [TelegramNotifierService]
                      ↓
                 [HTTP POST → api.telegram.org]
                      ↓ (sukses)
                 [Update: telegram_notified_at = now()]
                      ↓ (gagal)
                 [Log::error + Auto Retry (max 3x)]
```

**Config:** `.env`
```
TELEGRAM_BOT_TOKEN=xxxxx
TELEGRAM_CHAT_ID=xxxxx
```
**Service:** `App\Services\TelegramNotifierService`
**Job:** `App\Jobs\SendTelegramNotificationJob`

---

### US-05-003: Melihat Daftar Pesan di Inbox CMS / View Message List in CMS Inbox

**Sebagai / As a:** Administrator
**Saya ingin / I want:** melihat daftar seluruh pesan masuk di Inbox panel CMS dengan informasi ringkas dan status baca / see a list of all incoming messages in the CMS Inbox panel with summary information and read status
**Agar / So that:** saya memiliki arsip lengkap semua komunikasi yang masuk dan dapat meninjau kembali pesan kapan saja, tidak hanya bergantung pada notifikasi Telegram / I have a complete archive of all incoming communications and can review messages anytime, not relying solely on Telegram notifications

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Inbox CMS — Daftar Pesan / CMS Inbox — Message List

  Scenario: Admin melihat daftar pesan masuk di Inbox
  / Admin views the list of incoming messages in Inbox
    Given Admin berada di halaman "Inbox" di panel CMS
    / Admin is on the "Inbox" page in the CMS panel
    Then Ditampilkan tabel berisi pesan-pesan, diurutkan dari terbaru ke terlama
    / A table is displayed containing messages, sorted newest to oldest
    And Setiap baris tabel menampilkan: Nama, Email, Subjek (truncated), Status (badge warna), Waktu Terima, Aksi
    / Each table row displays: Name, Email, Subject (truncated), Status (color badge), Received Time, Actions
    And Pesan dengan status "Belum Dibaca" / "Unread" ditampilkan dengan highlight visual berbeda
    / Messages with "Unread" status are displayed with a different visual highlight

  Scenario: Admin memfilter pesan berdasarkan status
  / Admin filters messages by status
    Given Admin berada di halaman Inbox / Admin is on the Inbox page
    When Admin memilih filter "Status: Belum Dibaca" / Admin selects filter "Status: Unread"
    Then Hanya pesan dengan status 'unread' yang ditampilkan
    / Only messages with 'unread' status are displayed

  Scenario: Indikator jumlah pesan belum dibaca di navigasi CMS
  / Unread message count indicator in CMS navigation
    Given Ada 3 pesan dengan status 'unread' / There are 3 messages with 'unread' status
    When Admin login ke panel CMS / Admin logs into the CMS panel
    Then Badge angka "3" muncul pada item navigasi "Inbox" di sidebar CMS
    / Badge number "3" appears on the "Inbox" navigation item in the CMS sidebar
```

---

### US-05-004: Mengelola Status Pesan di Inbox / Manage Message Status in Inbox

**Sebagai / As a:** Administrator
**Saya ingin / I want:** menandai status pesan (Belum Dibaca, Sudah Dibaca, Sudah Direspons) dan menghapus pesan yang tidak relevan / mark message status (Unread, Read, Replied) and delete irrelevant messages
**Agar / So that:** Inbox tetap terorganisir dan saya dapat melacak pesan mana yang sudah ditindaklanjuti / the Inbox stays organized and I can track which messages have been acted upon

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 3 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Manajemen Status Pesan Inbox / Inbox Message Status Management

  Scenario: Admin membaca detail pesan dan status otomatis berubah ke "Read"
  / Admin reads message detail and status automatically changes to "Read"
    Given Ada pesan dengan status "unread" / There is a message with "unread" status
    When Admin membuka halaman detail pesan tersebut / Admin opens the message detail page
    Then Status pesan otomatis berubah dari 'unread' ke 'read'
    / Message status automatically changes from 'unread' to 'read'
    And Badge counter di navigasi Inbox berkurang 1
    / Badge counter on the Inbox navigation decreases by 1

  Scenario: Admin menandai pesan sebagai "Sudah Direspons"
  / Admin marks a message as "Replied"
    Given Admin sudah merespons pesan secara manual (via Telegram/email)
    / Admin has manually responded to the message (via Telegram/email)
    When Admin mengklik tombol "Tandai Sudah Direspons" / Admin clicks "Mark as Replied"
    Then Status pesan berubah menjadi 'replied'
    / Message status changes to 'replied'
    And Badge status pada baris pesan di daftar berubah warna menjadi hijau
    / Status badge on the message row in the list changes to green color

  Scenario: Admin menghapus pesan spam
  / Admin deletes spam messages
    Given Admin melihat pesan yang jelas merupakan spam
    / Admin sees a message that is clearly spam
    When Admin mengklik "Hapus" dan mengonfirmasi / Admin clicks "Delete" and confirms
    Then Record pesan dihapus permanen dari tabel `contact_messages`
    / Message record is permanently deleted from the `contact_messages` table
    And Pesan tidak lagi muncul di daftar Inbox / Message no longer appears in the Inbox list
```

#### Referensi Teknis / Technical References

**Tabel:** `contact_messages` (kolom `status` — enum: `unread|read|replied`)
**Auto-read:** Dipicu saat `ContactController@show` dipanggil — `$message->update(['status' => 'read'])`

---

### US-05-005: Melihat Detail Pesan di Inbox / View Message Detail in Inbox

**Sebagai / As a:** Administrator
**Saya ingin / I want:** membaca isi lengkap sebuah pesan dari halaman detail pesan di CMS / read the complete content of a message from the message detail page in the CMS
**Agar / So that:** saya dapat memahami konteks dan kebutuhan pengirim secara penuh sebelum memutuskan bagaimana merespons / I can fully understand the sender's context and needs before deciding how to respond

| Atribut | Nilai |
|---|---|
| **Priority** | 🔴 Must Have |
| **Story Points** | 2 |

#### Kriteria Penerimaan / Acceptance Criteria (Gherkin)

```gherkin
Feature: Detail Pesan Inbox / Inbox Message Detail

  Scenario: Admin membuka halaman detail pesan
  / Admin opens the message detail page
    Given Admin berada di daftar Inbox dan mengklik sebuah pesan
    / Admin is on the Inbox list and clicks a message
    Then Halaman detail menampilkan semua informasi pesan:
    / Detail page displays all message information:
      - Nama pengirim / Sender name
      - Email pengirim (sebagai link mailto:) / Sender email (as mailto: link)
      - Subjek / Subject
      - Isi pesan lengkap / Full message content
      - Waktu terima / Received time
      - Status notifikasi Telegram (terkirim/gagal) / Telegram notification status (sent/failed)
      - Status pesan (badge) / Message status (badge)
    And Tombol aksi tersedia: "Tandai Sudah Direspons", "Hapus"
    / Action buttons available: "Mark as Replied", "Delete"
    And Link "mailto:[email]" tersedia untuk membuka email client langsung
    / "mailto:[email]" link is available to open email client directly
```

---

## Ringkasan Story Points / Story Points Summary

| Story ID | Judul | Priority | Points |
|---|---|---|---|
| US-05-001 | Pengunjung Kirim Pesan via Formulir | Must Have | 5 |
| US-05-002 | Notifikasi Telegram Real-time | Must Have | 8 |
| US-05-003 | Lihat Daftar Pesan di Inbox CMS | Must Have | 3 |
| US-05-004 | Kelola Status Pesan Inbox | Must Have | 3 |
| US-05-005 | Lihat Detail Pesan | Must Have | 2 |
| | **Total** | | **21** |
