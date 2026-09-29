# Asri Boarding House - Sistem Informasi Manajemen Kost Terintegrasi

<p align="center">
  <a href="https://asriboardinghouse.weatso.id/" target="_blank">
    <img src="public/images/logo/logo-asri.png" alt="Asri Boarding House Logo" width="160" onerror="this.src='https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg'; this.width=260;">
  </a>
</p>

<p align="center">
  <strong>Solusi Digital Terpadu Pengelolaan Kost Modern, Reservasi Online, Billing Otomatis, & Notifikasi Terintegrasi</strong>
</p>

<p align="center">
  <a href="https://asriboardinghouse.weatso.id/"><img src="https://img.shields.io/badge/Production_Live-asriboardinghouse.weatso.id-0070f3?style=for-the-badge&logo=googlechrome&logoColor=white" alt="Production Website"></a>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12"></a>
  <a href="https://www.php.net/"><img src="https://img.shields.io/badge/PHP-%5E8.2-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+"></a>
  <a href="https://www.mysql.com/"><img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.x"></a>
  <a href="https://tailwindcss.com/"><img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS"></a>
  <a href="https://alpinejs.dev/"><img src="https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=black" alt="Alpine.js"></a>
  <a href="https://midtrans.com/"><img src="https://img.shields.io/badge/Payment-Midtrans_Snap-002D62?style=for-the-badge" alt="Midtrans"></a>
  <a href="https://fonnte.com/"><img src="https://img.shields.io/badge/Gateway-Fonnte_WhatsApp-25D366?style=for-the-badge&logo=whatsapp&logoColor=white" alt="Fonnte"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License"></a>
</p>

---

## 📌 Tautan Penting Aplikasi (Live Links)

* 🌐 **Website Utama (Produksi)**: [https://asriboardinghouse.weatso.id/](https://asriboardinghouse.weatso.id/)
* 🔐 **Portal Masuk (Login)**: [https://asriboardinghouse.weatso.id/login](https://asriboardinghouse.weatso.id/login)
* 🛏️ **Katalog & Booking Kamar**: [https://asriboardinghouse.weatso.id/#kamar](https://asriboardinghouse.weatso.id/#kamar)
* 📖 **Spesifikasi Lengkap Produk (PRD)**: [Blueprint/product_requirement_document_kost.md](Blueprint/product_requirement_document_kost.md)
* 📐 **Master Blueprint & Kebijakan Bisnis Final**: [Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md](Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md)

---

## 📑 Daftar Isi

1. [Tentang Asri Boarding House](#-tentang-asri-boarding-house)
2. [Aturan Bisnis Utama & Kebijakan Operasional](#-aturan-bisnis-utama--kebijakan-operasional)
3. [Fitur-Fitur Utama Sistem](#-fitur-fitur-utama-sistem)
   - [Halaman Publik & Landing Page](#1-halaman-publik--landing-page)
   - [Reservasi & Onboarding Calon Penyewa](#2-reservasi--onboarding-calon-penyewa)
   - [Portal Penyewa Aktif (Tenant Hub)](#3-portal-penyewa-aktif-tenant-hub)
   - [Portal Admin & Pengelola (Management Hub)](#4-portal-admin--pengelola-management-hub)
4. [Sistem Pembayaran Ganda (Dual Payment Gateway)](#-sistem-pembayaran-ganda-dual-payment-gateway)
5. [Otomasi Scheduler & Background Tasks](#-otomasi-scheduler--background-tasks)
6. [Arsitektur & Tumpukan Teknologi (Tech Stack)](#-arsitektur--tumpukan-teknologi-tech-stack)
7. [Matriks Skema Basis Data (21 Entitas Model)](#-matriks-skema-basis-data-21-entitas-model)
8. [Struktur Direktori Repositori](#-struktur-direktori-repositori)
9. [Panduan Instalasi & Menjalankan di Lingkungan Lokal](#-panduan-instalasi--menjalankan-di-lingkungan-lokal)
10. [Konfigurasi Environment (.env)](#-konfigurasi-environment-env)
11. [Kredensial Default untuk Pengujian](#-kredensial-default-untuk-pengujian)
12. [Indeks Lengkap Dokumentasi Blueprint (14 Berkas Perancangan)](#-indeks-lengkap-dokumentasi-blueprint-14-berkas-perancangan)
13. [Lisensi & Hak Cipta](#-lisensi--hak-cipta)

---

## 🏢 Tentang Asri Boarding House

**Asri Boarding House** adalah aplikasi web manajemen rumah kost komprehensif berbasis **Laravel 12** yang dirancang untuk mengatasi inefisiensi pencatatan manual, risiko *double booking*, keterlambatan penagihan sewa, dan keterbatasan transparansi pelaporan operasional.

Sistem ini mengintegrasikan seluruh siklus hidup penghunian (*Tenant Lifecycle*) dalam satu ekosistem:
* **Bagi Calon Penyewa**: Menghadirkan kemudahan memilih kamar, berkonsultasi via *Live Chat*, dan reservasi online instan dengan opsi durasi sewa harian, mingguan, maupun bulanan.
* **Bagi Penyewa Aktif**: Memberikan transparansi tagihan, pembayaran mandiri otomatis (Midtrans Snap & Transfer Manual), riwayat kuitansi PDF resmi, pengajuan tiket keluhan fasilitas berfoto, serta notifikasi WhatsApp pengingat sewa.
* **Bagi Pengelola / Pemilik Kost**: Menyajikan dashboard monitoring okupansi real-time, visual kalender keterisian kamar, verifikasi pembayaran bertingkat, otomatisasi penerbitan tagihan bulanan, penghitungan denda keterlambatan flat kalender, serta pembukuan arus kas (*cash flow*) komprehensif.

---

## ⚖️ Aturan Bisnis Utama & Kebijakan Operasional

Sesuai konsensus dokumen [Blueprint_Projek_Web_Asri_Boarding_House.md](Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md) dan [PRD Bab 4](Blueprint/product_requirement_document_kost.md), sistem memberlakukan aturan bisnis terstandarisasi:

1. **Siklus Penagihan Seragam**:
   * Tagihan sewa bulanan rutin selalu diterbitkan otomatis setiap tanggal **1** awal bulan untuk seluruh penyewa.
2. **Jatuh Tempo Pembayaran & Grace Period**:
   * Batas waktu pelunasan jatuh tempo pada tanggal **10** setiap bulan (*grace period* 10 hari kalender).
3. **Kebijakan Denda Flat Kalender (Idempotency Guard)**:
   * **Bulan Berjalan**: Bebas denda dan hanya menerima notifikasi pengingat ramah.
   * **Bulan Berikutnya**: Dikenakan denda flat 5% dari tagihan pokok tepat satu kali (dilindungi *Idempotency Guard* agar denda tidak berlipat ganda secara keliru).
4. **Jaminan Deposit Sewa & Klaim Kerusakan**:
   * Setiap kontrak baru mewajibkan uang jaminan (*security deposit*) di awal.
   * Uang deposit dikembalikan penuh saat keluar (*check-out*), atau dipotong sesuai nilai klaim kerusakan inventaris kamar setelah inspeksi fisik.
5. **Status Kamar Pasca Check-Out (*Manual Inspection Hold*)**:
   * Kamar tidak langsung berstatus *Tersedia* (*Available*) saat penyewa dinonaktifkan, melainkan berstatus *Hold/Maintenance*.
   * Admin wajib melakukan inspeksi fisik kamar terlebih dahulu sebelum mengubah status kamar kembali menjadi *Tersedia*.
6. **Fleksibilitas Durasi Sewa**:
   * Menghapus aturan kontrak kaku minimal 3 bulan. Sistem mendukung sewa **Harian**, **Mingguan**, dan **Bulanan** baik untuk reservasi online maupun offline.
7. **Pengaturan Rekening Bank Dinamis**:
   * Nomor rekening, nama bank, dan atas nama pemilik tidak di-hardcode, melainkan dikelola langsung oleh Admin via model `Setting` di Admin Panel.
8. **Standar Desain Neo-Brutalism & Aksesibilitas WCAG 2.1**:
   * Mengusung gaya visual *Neo-Brutalism* modern (border kontras tegas, bayangan solid, sudut tajam).
   * Kepatuhan aksesibilitas WCAG 2.1 (*touch target* minimum 44px, adaptif terhadap tema gelap *OLED Dark Mode*).
   * Dialog konfirmasi tindakan kritis menggunakan modal kustom (*zero native browser confirm dialog*).

---

## 🚀 Fitur-Fitur Utama Sistem

### 1. Halaman Publik & Landing Page
* **Hero Showcase & Brand Identity**: Desain visual atraktif, informatif, dan responsif lintas perangkat.
* **Katalog Kamar Dinamis**: Informasi detail tipe kamar, inventaris fasilitas (AC, WiFi, Kamar Mandi Dalam, Kasur, Lemari), galeri foto, dimensi, tarif sewa, dan status ketersediaan (*Tersedia / Terisi*).
* **Guest Live Chat**: Widget obrolan langsung bagi calon penyewa untuk berkonsultasi secara interaktif tanpa kewajiban memiliki akun terlebih dahulu.
* **WhatsApp Direct Tracker**: Pelacakan analitik pengunjung yang mengeklik tautan konsultasi WhatsApp resmi admin.
* **Testimoni, FAQ & Tata Tertib**: Ulasan penghuni terverifikasi, akordeon tanya-jawab kebijakan reservasi berbasis Alpine.js, dan peraturan hunian.

### 2. Reservasi & Onboarding Calon Penyewa
* **Opsi Sewa Fleksibel**: Pilihan durasi sewa harian, mingguan, atau bulanan.
* **Otentikasi Ganda**: Registrasi manual (Email & Sandi) serta integrasi **Google OAuth 2.0 (Socialite)**.
* **Penapisan Kelengkapan Profil (`EnsureProfileIsComplete`)**: Form pengisian identitas resmi (NIK KTP 16 digit, nomor HP WhatsApp, nama & nomor kontak wali darurat, serta dokumen KTP).
* **Idempotent Reservation Holding**: Penguncian kamar sementara selama proses transaksi reservasi aktif agar terhindar dari benturan pemesanan ganda.

### 3. Portal Penyewa Aktif (Tenant Hub)
* **Tenant Dashboard**: Informasi kamar yang dihuni, sisa hari masa kontrak, tagihan aktif, serta pengumuman operasional kost.
* **Billing Mandiri & Kuitansi PDF**: Transparansi rincian sewa berjalan, prorata sewa awal, riwayat pembayaran, serta fitur unduh **Kuitansi Resmi Berformat PDF**.
* **Pengingat Kontrak & Renewal**: Notifikasi otomatis via WhatsApp menjelang akhir kontrak (H-14 dan H-7) serta form pengajuan perpanjangan masa kontrak (*contract renewal*).
* **Sistem Tiket Keluhan Fasilitas**: Pelaporan kerusakan fasilitas kamar/bersama disertai unggah foto bukti kendala, dengan status penanganan real-time (*Pending*, *Diproses*, *Selesai*).
* **Tenant Live Chat**: Kanal komunikasi pesan privat antara penyewa terdaftar dan pengelola kost.
* **Penegakan Sandi Pertama (`EnsurePasswordChanged`)**: Kebijakan wajib mengganti kata sandi awal untuk akun yang diinisialisasi oleh pengelola.

### 4. Portal Admin & Pengelola (Management Hub)
* **Executive Dashboard**: Ringkasan okupansi kamar (*Occupancy Rate*), total kamar kosong/terisi/hold, pendapatan kotor, piutang sewa berjalan, dan grafik arus kas.
* **Kalender Okupansi Visual (`CalendarController`)**: Matriks jadwal keterisian kamar secara visual untuk memantau durasi tinggal dan ketersediaan kamar di masa mendatang.
* **Manajemen Kamar & Fasilitas**: Inventarisasi nomor kamar, penetapan tarif sewa, serta kontrol status inspeksi pasca *check-out* (*Hold/Maintenance*).
* **Verifikasi Pembayaran & Rekonsiliasi**:
  * Pengecekan transaksi otomatis Midtrans (*settlement*, *pending*, *expire*).
  * Panel verifikasi transfer bank manual (pengecekan foto bukti transfer, persetujuan/penolakan, dan penerbitan kuitansi pelunasan).
* **Manajemen Deposit & Klaim Kerusakan**: Tata kelola uang jaminan, potongan biaya perbaikan aset fasilitas pasca inspeksi, dan pencairan *refund* sisa deposit.
* **Multi-Channel Notification Dispatcher**: Pengiriman pengumuman broadcast dan notifikasi WhatsApp personalisasi otomatis via **Fonnte API**.
* **Pembukuan Keuangan & Pelaporan**: Pencatatan pengeluaran operasional (listrik, air, internet, perbaikan, gaji) serta ekspor laporan neraca laba-rugi ke PDF (**Dompdf**).

---

## 💳 Sistem Pembayaran Ganda (Dual Payment Gateway)

Platform Asri Boarding House mendukung dua mekanisme pembayaran yang terintegrasi:

```
                          ┌─────────────────────────────┐
                          │   Pilihan Jalur Pembayaran  │
                          └──────────────┬──────────────┘
                                         │
                 ┌───────────────────────┴───────────────────────┐
                 ▼                                               ▼
   ┌───────────────────────────┐                   ┌───────────────────────────┐
   │    Midtrans Snap Engine   │                   │    Transfer Bank Manual   │
   ├───────────────────────────┤                   ├───────────────────────────┤
   │ • QRIS (GoPay/ShopeePay)  │                   │ • Rekening Resmi Pemilik  │
   │ • Virtual Account Bank    │                   │ • Upload Foto Bukti Bayar │
   │ • Idempotent Webhook      │                   │ • Verifikasi Manual Admin │
   │ • Status Berubah Instan   │                   │ • Audit Trail Pembayaran  │
   └───────────────────────────┘                   └───────────────────────────┘
```

1. **Jalur Otomatis (Midtrans Snap)**:
   * Calon penyewa membayar melalui popup Snap (QRIS, VA Bank Mandiri, BCA, BNI, BRI, Permata).
   * Webhook controller memproses notifikasi Midtrans secara *idempotent* dengan verifikasi `signature_key` SHA512.
   * Status transaksi, tagihan, dan aktivasi penyewa langsung diperbarui secara *real-time*.
2. **Jalur Manual (Transfer Bank)**:
   * Alternatif bagi pengguna yang memilih transfer ATM/m-Banking konvensional ke rekening dinamis kost.
   * Pengguna mengunggah struk/screenshot mutasi transfer.
   * Admin menerima notifikasi dan melakukan verifikasi validitas untuk menerbitkan kuitansi pelunasan resmi.

---

## ⏰ Otomasi Scheduler & Background Tasks

Sistem mengandalkan **Laravel Task Scheduler** yang berjalan secara berkala di server produksi (`routes/console.php`):

| Perintah Artisan | Jadwal Eksekusi | Keterangan & Tujuan |
| :--- | :--- | :--- |
| `tagihan:generate-bulanan` | Bulanan (Tgl 1, pukul 00:05 WIB) | Menerbitkan tagihan sewa bulanan baru secara otomatis untuk seluruh penyewa aktif. |
| `tagihan:proses-keterlambatan` | Harian (pukul 01:00 WIB) | Memeriksa invoice yang melewati tanggal jatuh tempo dan menetapkan denda flat kalender secara idempotent. |
| `kontrak:reminder-habis` | Harian (pukul 08:00 WIB) | Memindai kontrak sewa yang akan berakhir pada H-14 dan H-7, lalu mengirim pesan WhatsApp pengingat perpanjangan. |
| `reservasi:cancel-expired` | Setiap Jam (*Hourly*) | Membatalkan reservasi *pending* yang telah melewati batas tenggat waktu pembayaran (*timeout*). |
| `log-notifikasi:clear` | Harian (pukul 02:00 WIB) | Membersihkan data log notifikasi berumur > 90 hari agar performa basis data tetap prima. |
| `chat-guest:prune` | Harian (pukul 03:00 WIB) | Menghapus thread pesan konsultasi tamu (*guest*) yang telah ditutup melewati masa retensi. |
| `session:cleanup` | Harian (pukul 01:30 WIB) | Membersihkan sesi pengguna kedaluwarsa pada tabel basis data. |
| `log:truncate` | Mingguan | Merotasi berkas catatan sistem (`storage/logs/laravel.log`) untuk mencegah beban kapasitas penyimpanan. |
| `abh:purge-trash` | Bulanan | Menghapus data *soft deleted* lama yang telah melewati masa retensi legal secara permanen. |

---

## 🛠️ Arsitektur & Tumpukan Teknologi (Tech Stack)

### Core Technologies
* **Framework**: [Laravel 12.x](https://laravel.com) (PHP 8.2+) dengan arsitektur *Service Layer Pattern*.
* **Database**: [MySQL 8.x](https://www.mysql.com/) (Skema 21 entitas tabel berelasi, indeks performa, dan Soft Deletes).
* **Frontend Template**: Blade Templating Engine terstruktur & modular.
* **CSS Styling**: [Tailwind CSS 3.x / 4.x](https://tailwindcss.com/) dengan plugin `@tailwindcss/forms` bertema *Neo-Brutalism*.
* **Interaktivitas UI**: [Alpine.js 3.x](https://alpinejs.dev/) untuk reaktivitas ringan bebas dependensi berat.
* **Build Tooling**: [Vite 7.x](https://vitejs.dev/) & [Laravel Vite Plugin](https://laravel.com/docs/vite).

### Integrasi Pihak Ketiga & Layanan Tambahan
* **Payment Gateway**: Midtrans Snap API & Idempotent Webhook Handler (`MidtransService`).
* **WhatsApp Notification Gateway**: [Fonnte API](https://fonnte.com/) (`FonnteService`).
* **Single Sign-On (SSO)**: Google OAuth 2.0 via `laravel/socialite`.
* **PDF Generator**: `dompdf/dompdf` (Laporan Keuangan Eksekutif) & `html2pdf.js` (Kuitansi Pembayaran Sisi Klien).
* **Visualisasi & Diagram UML**: Mermaid CLI (`@mermaid-js/mermaid-cli`).

---

## 🗄️ Matriks Skema Basis Data (21 Entitas Model)

Struktur data sistem selaras 100% dengan [PRD Bab 9 (Database Entity Mapping)](Blueprint/product_requirement_document_kost.md):

| No | Model Eloquent | Nama Tabel | Deskripsi & Fungsi Entitas |
| :---: | :--- | :--- | :--- |
| 1 | `User` | `users` | Entitas akun otentikasi utama (admin, penyewa, google oauth, role, status aktif). |
| 2 | `Penyewa` | `penyewas` | Profil data penyewa aktif, kamar dihuni, tarif sewa personal, kontrak, dan status. |
| 3 | `Kamar` | `kamars` | Master kamar kost, nomor kamar, tipe, tarif sewa, dimensi, dan status ketersediaan. |
| 4 | `Fasilitas` | `fasilitas` | Master fasilitas kamar dan fasilitas umum kost (AC, WiFi, kamar mandi dalam, dll.). |
| 5 | `Reservasi` | `reservasis` | Data transaksi pemesanan kamar awal, tanggal masuk, durasi sewa, dan status reservasi. |
| 6 | `Tagihan` | `tagihans` | Invoice penagihan sewa berkala, tanggal jatuh tempo, denda flat, dan status pelunasan. |
| 7 | `Pembayaran` | `pembayarans` | Riwayat transaksi bayar (Midtrans / Transfer Manual), nomor referensi, dan bukti bayar. |
| 8 | `Keluhan` | `keluhans` | Tiket pengaduan kerusakan fasilitas kamar/umum, lampiran foto, dan status resolusi. |
| 9 | `Pengeluaran` | `pengeluarans` | Pencatatan pengeluaran operasional kost (listrik, internet, perawatan, gaji staf). |
| 10 | `Pengumuman` | `pengumumans` | Informasi dan pengumuman umum dari pengelola kost kepada seluruh penghuni. |
| 11 | `CustomerReview` | `customer_reviews`| Ulasan dan testimoni pengalaman menginap dari penyewa untuk landing page publik. |
| 12 | `Faq` | `faqs` | Daftar tanya-jawab interaktif seputar kebijakan dan operasional kost. |
| 13 | `Gallery` | `galleries` | Koleksi dokumentasi visual properti kost, kamar, dan area komunal. |
| 14 | `Peraturan` | `peraturans` | Tata tertib dan regulasi hunian kost yang wajib dipatuhi penghuni. |
| 15 | `Setting` | `settings` | Konfigurasi sistem dinamis (nomor rekening bank, nama pemilik, kontak WA admin). |
| 16 | `ChatMessage` | `chat_messages` | Riwayat pesan komunikasi internal antara penyewa terverifikasi dan admin. |
| 17 | `GuestChatThread` | `guest_chat_threads` | Sesi percakapan obrolan langsung pengunjung website (*Guest Live Chat*). |
| 18 | `GuestChatMessage` | `guest_chat_messages`| Isi pesan obrolan antara pengunjung umum dan admin pengelola kost. |
| 19 | `NotifikasiKhusus` | `notifikasi_khusus` | Notifikasi in-app terarah kepada pengguna tertentu mengenai invoice atau kontrak. |
| 20 | `LogNotifikasi` | `log_notifikasis` | Audit trail pengiriman notifikasi broadcast & WhatsApp via Fonnte Gateway. |
| 21 | `WhatsappClick` | `whatsapp_clicks` | Pencatatan log analitik konversi klik tombol WhatsApp oleh pengunjung web. |

---

## 📁 Struktur Direktori Repositori

```text
asri-boarding-house/
├── app/
│   ├── Console/Commands/        # 11 Perintah Artisan untuk otomatisasi & cron scheduler
│   ├── Http/
│   │   ├── Controllers/         # Controller Publik, Tenant Portal, Admin, & Webhook
│   │   └── Middleware/          # EnsureProfileIsComplete, EnsurePasswordChanged, dll.
│   ├── Models/                  # 21 Model Eloquent terelasi
│   └── Services/                # Service Layer (Billing, Midtrans, Fonnte, Notifikasi)
├── Blueprint/                   # PRD, Use Case, Activity, Sequence, ERD, & 14 Dokumen Perancangan
├── config/                      # Berkas konfigurasi sistem Laravel
├── database/
│   ├── factories/               # Model factory untuk seeding
│   ├── migrations/              # 21 migrasi skema tabel database
│   └── seeders/                 # Seeder user, kamar, fasilitas, dan peraturan
├── public/                      # Asset publik (gambar kamar, logo, favicon, kuitansi)
├── resources/
│   ├── css/ & js/               # Sumber asset Tailwind CSS & script Alpine.js
│   └── views/                   # Template Blade (Admin, Penyewa, Landing Page, Auth)
├── routes/
│   ├── web.php                  # Seluruh routing aplikasi web
│   └── console.php              # Definisi jadwal tugas otomatis (Scheduler)
├── storage/                     # Upload bukti transfer, dokumen KTP, dan log sistem
├── tests/                       # Unit & Feature Test (PHPUnit)
├── .env.example                 # Contoh template konfigurasi environment
├── composer.json                # Dependensi paket PHP & skrip automasi Composer
└── package.json                 # Dependensi paket JavaScript & skrip Vite
```

---

## 💻 Panduan Instalasi & Menjalankan di Lingkungan Lokal

Ikuti langkah-langkah di bawah ini untuk memasang dan menjalankan proyek ini pada komputer lokal:

### 1. Prasyarat Sistem
* **PHP** versi `8.2` atau lebih tinggi dengan ekstensi aktif: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `gd`, `fileinfo`.
* **Composer** versi `2.x`.
* **Node.js** versi `18.x` / `20.x` & **NPM**.
* **MySQL Server** versi `8.0` atau MariaDB setara.

### 2. Langkah Instalasi

```bash
# 1. Kloning repositori proyek
git clone https://github.com/rafifarsyapradiva/asri-boarding-house.git
cd asri-boarding-house

# 2. Instal dependensi PHP (Backend)
composer install

# 3. Instal dependensi Node.js (Frontend)
npm install

# 4. Buat berkas konfigurasi lingkungan
cp .env.example .env

# 5. Generate Application Encryption Key
php artisan key:generate

# 6. Konfigurasikan koneksi database pada berkas .env (lihat seksi di bawah)
# Lalu jalankan migrasi dan isi data awal (seeding):
php artisan migrate:fresh --seed

# 7. Hubungkan storage folder publik untuk file upload
php artisan storage:link

# 8. Kompilasi asset frontend
npm run build
```

### 3. Menjalankan Server Lokal Terpadu

Proyek ini telah dilengkapi skrip *concurrent runner* untuk menjalankan HTTP Server, Queue Listener, dan Vite secara simultan:

```bash
# Opsi 1: Menjalankan seluruh proses sekaligus (Rekomendasi)
composer run dev

# Opsi 2: Menjalankan secara terpisah di terminal berbeda
php artisan serve
npm run dev
php artisan queue:listen
```

Setelah server aktif, buka peramban dan akses:
👉 `http://127.0.0.1:8000`

---

## ⚙️ Konfigurasi Environment (`.env`)

Pastikan variabel-variabel kunci berikut telah disesuaikan di dalam berkas `.env` Anda:

```env
APP_NAME="Asri Boarding House"
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Basis Data
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=asri_boarding_house
DB_USERNAME=root
DB_PASSWORD=

# Konfigurasi Queue & Sesi
QUEUE_CONNECTION=database
SESSION_DRIVER=database

# Gateway Pembayaran Midtrans
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

# Gateway WhatsApp Fonnte
FONNTE_TOKEN=your_fonnte_device_token_here

# Google OAuth 2.0 (Socialite)
GOOGLE_CLIENT_ID=your_google_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

---

## 🔑 Kredensial Default untuk Pengujian

Setelah menjalankan perintah `php artisan migrate --seed`, akun demonstrasi bawaan yang dapat digunakan meliputi:

| Peran (Role) | Email | Password | Hak Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@asriboardinghouse.com` | `password` | Mengelola kamar, verifikasi pembayaran, analitik, broadcast WA, dan laporan. |
| **Penyewa Demo** | `penyewa@asriboardinghouse.com` | `password` | Mengakses dashboard sewa, riwayat tagihan, keluhan, dan perpanjangan kontrak. |

> *Catatan: Untuk keamanan instalasi di server produksi, segera perbarui email dan kata sandi akun administratif default.*

---

## 📚 Indeks Lengkap Dokumentasi Blueprint (14 Berkas Perancangan)

Seluruh rancangan arsitektur, diagram UML, skenario cerita, dan dokumen spesifikasi formal tersedia lengkap di direktori [`Blueprint/`](Blueprint):

| Dokumen Perancangan | Format / Tautan Berkas | Deskripsi & Cakupan Teknis |
| :--- | :--- | :--- |
| **Master Blueprint Proyek** | 📘 [Blueprint_Projek_Web_Asri_Boarding_House.md](Blueprint/Blueprint_Projek_Web_Asri_Boarding_House.md) | Dokumen master harmonisasi sistem, ringkasan 36 kebijakan bisnis, dan arsitektur v248.0. |
| **Product Requirement Document** | 📋 [product_requirement_document_kost.md](Blueprint/product_requirement_document_kost.md) | Spesifikasi lengkap PRD: Personas, SLA, Acceptance Criteria Given-When-Then, dan 15 alur visual. |
| **Entity Relationship Diagram (ERD)** | 📊 [Entity_Relationship_Diagram_Kost.md](Blueprint/Entity_Relationship_Diagram_Kost.md) | Diagram perancangan relasi 21 tabel database, foreign keys, indeks komposit, dan aturan cascade. |
| **Class Diagram** | 🏛️ [Class_Diagram_Kost.md](Blueprint/Class_Diagram_Kost.md) | Struktur kelas berorientasi objek (OOP), controller, model Eloquent, service layer, dan relasi. |
| **Component Diagram** | 📦 [Component_Diagram_Kost.md](Blueprint/Component_Diagram_Kost.md) | Dekomposisi modul aplikasi, interaksi antar komponen sistem, dan API gateway eksternal. |
| **Deployment Diagram** | 🚀 [Deployment_Diagram_Kost.md](Blueprint/Deployment_Diagram_Kost.md) | Topologi infrastruktur server produksi, web server Nginx, database MySQL, DNS, dan HTTPS. |
| **State Machine Diagram** | 🔄 [State_Machine_Diagram_Kost.md](Blueprint/State_Machine_Diagram_Kost.md) | Transisi status transaksi Midtrans, siklus hidup kamar (Tersedia/Terisi/Hold), dan status sewa. |
| **Sequence Diagram** | ⏱️ [Sequence_Diagram_Kost.md](Blueprint/Sequence_Diagram_Kost.md) | Urutan interaksi antar objek pada alur reservasi online, pembayaran Midtrans, dan broadcast WA. |
| **Activity Diagram** | 🧭 [Activity_Diagram_Kost.md](Blueprint/Activity_Diagram_Kost.md) | Alur aktivitas pengguna dan sistem dari pendaftaran, booking, billing denda, hingga checkout. |
| **Flowchart Sistem** | 🔀 [Flowchart_Kost.md](Blueprint/Flowchart_Kost.md) | Diagram alir logika algoritma pemrograman, validasi kelayakan sewa, dan penanganan webhook. |
| **Use Case Diagram** | 🎯 [Use_Case_Diagram_Kost.md](Blueprint/Use_Case_Diagram_Kost.md) | Pemetaan hak akses dan fungsionalitas sistem terhadap 3 aktor: Calon Penyewa, Penyewa, dan Admin. |
| **Skenario Reservasi Calon Penyewa** | 📖 [Skenario_Cerita_Reservasi.md](Blueprint/Skenario_Cerita_Reservasi.md) | Narasi skenario perjalanan calon penghuni mulai dari survei kamar web hingga proses check-in. |
| **Skenario Reservasi Admin** | 📖 [Skenario_Cerita_Reservasi_Admin.md](Blueprint/Skenario_Cerita_Reservasi_Admin.md) | Narasi skenario operasional pengelola dalam menangani booking, verifikasi transfer, dan keluhan. |
| **Panduan Uji Coba Demo & QA** | 🧪 [uji_coba_demo.md](Blueprint/uji_coba_demo.md) | Panduan langkah-demi-langkah skenario demonstrasi end-to-end fitur untuk pengujian komprehensif. |

---

## 📄 Lisensi & Hak Cipta

Proyek aplikasi web **Asri Boarding House** dilisensikan di bawah lisensi terbuka [MIT License](LICENSE).
Hak Cipta © 2026 **Asri Boarding House Team**. Seluruh hak cipta dilindungi.
