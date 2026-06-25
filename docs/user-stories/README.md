# User Stories — CMS growthcoder.id

> **Tanggal Dibuat:** 2026-06-21
> **Dibuat dari:** [PRD-growthcoder-id.md](../PRD-growthcoder-id.md)
> **Arsitektur:** Laravel 13 + Inertia.js (Admin CMS) | Nuxt 4 SSR (Public Frontend)

---

## Panduan Membaca / Reading Guide

Setiap file user story mengikuti konvensi berikut:
- **ID Format:** `US-[Nomor Modul]-[Nomor Story]` (contoh: `US-01-003`)
- **Bahasa:** Bilingual (Indonesia + Inggris)
- **Acceptance Criteria:** Format Gherkin (Given-When-Then)
- **Estimasi:** Story Points Fibonacci (1, 2, 3, 5, 8, 13)
- **Prioritas:** MoSCoW (`Must Have / Should Have / Could Have / Won't Have`)

---

## Aktor Sistem / System Actors

| Aktor | Tipe | Deskripsi |
|---|---|---|
| **Administrator** | Internal | Pemilik platform, akses penuh ke seluruh CMS |
| **Pengunjung Umum** | External | Mengakses halaman publik via Nuxt.js |
| **Calon Klien** | External | Mengunjungi untuk mengevaluasi layanan developer |
| **Recruiter** | External | Mengunjungi untuk menilai kualifikasi akademis & karir |

---

## Daftar Modul & File / Module & File List

| No | Modul | File | Total Story | Total Points | PRD Ref |
|---|---|---|---|---|---|
| 01 | Projects & Case Studies | [US-01-projects.md](./US-01-projects.md) | 12 stories | 42 pts | Section 4.1 |
| 02 | Blog & Category Management | [US-02-blog.md](./US-02-blog.md) | 9 stories | 36 pts | Section 4.2 |
| 03 | Centralized Media Library | [US-03-media-library.md](./US-03-media-library.md) | 5 stories | 21 pts | Section 4.3 |
| 04 | Global Settings & SEO | [US-04-global-settings.md](./US-04-global-settings.md) | 5 stories | 14 pts | Section 4.4 |
| 05 | Inbox & Contact + Telegram Bot | [US-05-inbox-contact.md](./US-05-inbox-contact.md) | 5 stories | 21 pts | Section 4.5 |
| 06 | Project Categories | [US-06-project-categories.md](./US-06-project-categories.md) | 5 stories | 12 pts | Section 4.6 |
| 07 | Tech Stack Management | [US-07-tech-stack.md](./US-07-tech-stack.md) | 5 stories | 11 pts | Section 4.7 |
| 08 | Skills Management | [US-08-skills.md](./US-08-skills.md) | 6 stories | 13 pts | Section 4.8 |
| 09 | Services Management | [US-09-services.md](./US-09-services.md) | 6 stories | 14 pts | Section 4.9 |
| 10 | Education & Experience | [US-10-education-experience.md](./US-10-education-experience.md) | 7 stories | 16 pts | Section 4.10 |
| | **TOTAL** | | **65 stories** | **200 pts** | |

---

## Distribusi Prioritas MoSCoW (Estimasi)

| Priority | Deskripsi | Estimasi Stories |
|---|---|---|
| 🔴 **Must Have** | Fitur inti yang wajib ada untuk launch | ~45 stories |
| 🟡 **Should Have** | Penting tapi tidak critical untuk v1 | ~15 stories |
| 🟢 **Could Have** | Nice-to-have, bisa dijadwalkan ke sprint berikutnya | ~4 stories |
| ⚪ **Won't Have** | Tidak dalam scope v1 | 0 stories |

---

## Ketergantungan Antar Modul / Inter-Module Dependencies

```
Modul 03 (Media Library)
    ↓ digunakan oleh
    ├── Modul 01 (Projects) — cover & galeri gambar proyek
    ├── Modul 02 (Blog) — cover gambar artikel
    ├── Modul 04 (Global Settings) — foto profil, OG image
    ├── Modul 07 (Tech Stack) — logo teknologi
    └── Modul 10 (Education) — logo institusi

Modul 06 (Project Categories)
    ↓ digunakan oleh
    └── Modul 01 (Projects) — kategori proyek

Modul 07 (Tech Stack)
    ↓ digunakan oleh
    ├── Modul 01 (Projects) — tagging tech stack
    └── Modul 08 (Skills) — referensi master teknologi
```

---

## Catatan Implementasi / Implementation Notes

> [!IMPORTANT]
> **Urutan Implementasi yang Disarankan (Backend First):**
> 1. Modul 03 (Media Library) — fondasi untuk semua modul lain
> 2. Modul 06 (Project Categories) & Modul 07 (Tech Stack) — master data
> 3. Modul 01 (Projects) — modul utama yang bergantung pada 03, 06, 07
> 4. Modul 08 (Skills) — bergantung pada Modul 07
> 5. Modul 02 (Blog) — bergantung pada Modul 03
> 6. Modul 04 (Global Settings) — bergantung pada Modul 03
> 7. Modul 09 (Services) & Modul 10 (Education) — independen setelah Modul 03
> 8. Modul 05 (Inbox & Telegram) — terakhir, tidak bergantung pada modul lain

> [!NOTE]
> File-file ini adalah **living documents**. Update sesuai perkembangan requirement.
