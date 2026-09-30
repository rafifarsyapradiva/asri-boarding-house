# FACT SHEET (IMMUTABLE FACTS)
## Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House

Dokumen ini memuat seluruh fakta empiris, parameter teknis, aturan bisnis, dan angka hasil pengujian yang berstatus **immutable** (tidak boleh diubah atau direduksi nilainya) dalam proses penyuntingan dan humanisasi naskah skripsi.

---

### 1. IDENTITAS PENELITIAN
* **Judul Skripsi**: Sistem Informasi Manajemen Kost Terintegrasi Payment Gateway pada Asri Boarding House
* **Penulis**: Rafif Arsya Pradiva
* **NIM**: 22.N4.0014
* **Program Studi**: Sistem Informasi
* **Fakultas**: Ilmu Komputer
* **Perguruan Tinggi**: Universitas Katolik Soegijapranata Semarang
* **Tahun Kelulusan / Sidang**: 2026
* **Dosen Pembimbing**: Ir. Andre Kurniawan Pamudji, S.Kom, M.Ling. (NPP: 058.1.1994.161)
* **Objek / Studi Kasus**: Asri Boarding House, Tembalang, Kota Semarang
* **Narasumber / Pengelola**: Bapak Asep (Usia 48 tahun, pengalaman mengelola kos 20 tahun)

---

### 2. PARAMETER OPERASIONAL & PROPERTI KOST
* **Total Kapasitas Kamar**: 32 unit kamar
* **Distribusi Tipe Kamar & Tarif Sewa Bulanan**:
  1. Tipe VIP: 6 unit @ Rp1.400.000 = Rp8.400.000 / bulan
  2. Tipe Deluxe: 3 unit @ Rp950.000 = Rp2.850.000 / bulan
  3. Tipe Standar: 23 unit @ Rp750.000 = Rp17.250.000 / bulan
* **Potensi Pendapatan Maksimal (Gross Potential Revenue)**: Rp28.500.000 per bulan (pada okupansi 100%)
* **Buku Catatan Sebelumnya**: Manual berbasis buku besar fisik dan kuitansi sobek, membutuhkan waktu rekapitulasi 3–5 hari setiap akhir bulan.

---

### 3. LOGIKA BISNIS & ATURAN PENAGIHAN
* **Siklus Pembangkitan Tagihan Bulanan**: Diterbitkan otomatis oleh *Auto-Billing Engine* via Cron Job setiap tanggal 1 awal bulan.
* **Batas Jatuh Tempo (*Due Date*)**: Tanggal 10 setiap bulan berjalan.
* **Masa Tenggang Bebas Denda**: Mulai tanggal 11 hingga akhir bulan berjalan (penyewa hanya menerima pengingat berkala ramah via WhatsApp tanpa pembebanan denda finansial).
* **Kebijakan Denda Keterlambatan**:
  * Denda dikenakan sebesar flat 5% dari nominal pokok sewa kamar.
  * Dikenakan tepat satu kali pada bulan kalender berikutnya (tanggal 1 bulan berikutnya saat tagihan menyeberang bulan).
  * Bersifat idempoten melalui *Idempotency Guard* (`nominal_denda == 0`), tidak berakumulasi secara harian (tidak berbunga-berbunga), dan tidak memiliki batas atas karena hanya dikenakan satu kali per tagihan bulanan.
* **Eskalasi Penunggakan**:
  * Notifikasi penagihan ditujukan langsung kepada penyewa pada bulan pertama.
  * Pada bulan kalender kedua keterlambatan, pesan notifikasi secara otomatis dieskalasikan ke nomor kontak WhatsApp wali/orang tua penyewa.
* **Alur Kerja Hibrida (*Hybrid Workflow*)**:
  * Jalur daring (*online self-service*): Calon penghuni mereservasi mandiri lewat website dengan skema DP 30% atau Pelunasan 100% via Midtrans Snap.
  * Jalur luring/konvensional (*walk-in*): Tamu datang langsung ke lokasi kost dan didaftarkan manual oleh administrator ke dalam sistem, mendukung pembayaran kas tunai yang divalidasi dengan satu kali klik "Konfirmasi Tunai".
* **Kebijakan Isolasi Kamar Pasca-Checkout**:
  * Ketika penyewa keluar (*checkout*), sistem sengaja mengunci kamar pada status `terisi` (indikator merah).
  * Pelepasan status menjadi `tersedia` (indikator hijau) wajib dilakukan secara manual oleh administrator setelah dilakukan inspeksi fisik kebersihan dan kelayakan sarana kamar, guna mengeliminasi insiden kamar dipesan saat belum siap huni.
* **Kanal Komunikasi Pra-Pembayaran**:
  * *Guest Chat* publik berbasis token sesi lokal (SHA-256).
  * *Live Chat* interaktif antara calon penyewa dan admin yang aktif saat status reservasi *pending*, memanfaatkan AJAX polling per 4 detik dengan optimasi kueri relasi `latestChatMessage`.

---

### 4. ARSITEKTUR & STACK TEKNOLOGI
* **Metode Pengembangan**: *Research and Development* (R&D) dengan model siklus hidup *Waterfall* (Analisis Kebutuhan, Desain Sistem, Implementasi, Pengujian, dan Pemeliharaan — pemeliharaan di luar linimasa riset 6 bulan).
* **Pola Arsitektur**: 3-Tier Architecture dipadukan dengan *Service Layer Decoupling* (`BillingService`, `MidtransService`, `ReservasiService`, `FonnteService`, `TransisiPenyewaService`).
* **Bahasa & Framework Backend**: PHP versi 8.2.12 dan Laravel versi 11.x.
* **Sistem Manajemen Basis Data**: MySQL versi 8.0 InnoDB, ternormalisasi hingga Bentuk Normal Ketiga (3NF), terdiri atas 22 tabel relasional.
* **Integritas Basis Data**:
  * *Virtual Generated Columns* (`active_email`, `active_no_hp`, `active_nik`, `active_nomor_kamar`, `active_order_id`) berdampingan dengan *Soft Deletes* (`deleted_at`).
  * Integritas referensial kunci asing `ON DELETE RESTRICT` pada transaksi finansial dan kamar.
* **Antarmuka & Pengalaman Pengguna (UI/UX)**:
  * Desain: Neo-Brutalisme (tipografi Space Grotesk, bingkai tebal 4px, bayangan keras/hard shadow, efek tekan tombol).
  * Aksesibilitas: Kepatuhan WCAG 2.1 (target sentuh minimum 44 piksel, tombol aksi utama melayang WhatsApp sebesar 56 piksel).
  * Panel Admin: *OLED Black Dark Mode* untuk kenyamanan visual pengelola senior.
* **Integrasi Layanan Pihak Ketiga**:
  * Gerbang Pembayaran: Midtrans Snap API v2 (Mode Sandbox dan Live Produksi, verifikasi tanda tangan digital SHA-512, saluran utama Bank BCA Virtual Account).
  * Notifikasi Pesan: Fonnte WhatsApp Gateway API v2 (RESTful API terintegrasi tabel `log_notifikasi`).
  * Layanan Surel: Hostinger SMTP Mail Server (port 587, enkripsi TLS).
  * Autentikasi Eksternal: Google OAuth 2.0 via Laravel Socialite.
  * Mesin PDF: `html2pdf.js` untuk kuitansi A5 instan sisi peramban klien (*zero server overhead*) dan Dompdf untuk laporan keuangan manajerial sisi peladen.
  * Visualisasi & Media: Chart.js, Google Maps Embed API, YouTube Embed API.
* **Pengontrolan Konkurensi**:
  * Menggunakan penguncian tingkat baris (*pessimistic row locking*) `lockForUpdate()` di dalam transaksi basis data `DB::transaction` untuk mencegah *race condition* dan *double booking*.
* **Lingkungan Penerapan (*Deployment*)**:
  * Cloud Shared Hosting Hostinger Enterprise (Data Center Jakarta, Indonesia).
  * Peladen Web LiteSpeed Enterprise, PHP 8.2, sertifikat SSL HTTPS Grade A.
  * Domain Resmi Produksi: `https://asriboardinghouse.weatso.id`

---

### 5. HASIL & METRIK PENGUJIAN SISTEM
* **Black Box Testing**:
  * 60 butir skenario pengujian fungsionalitas kotak hitam.
  * Terbagi ke dalam 6 domain fungsional.
  * Tingkat kelulusan: 100% (*all passed*).
* **Automated Feature Test (PHPUnit)**:
  * 510 tests passed.
  * 2.211 assertions.
  * Tingkat kelulusan: 100%.
* **Pengujian Hak Akses (RBAC & IDOR)**:
  * Pengujian isolasi peran Administrator, Penyewa Aktif, dan Publik.
  * Berhasil memvalidasi isolasi rute dan mencegah manipulasi otorisasi.
* **Pengujian Transaksi Midtrans Sandbox**:
  * 6 skenario transaksi (pending, settlement, expire, cancel, double webhook notification, invalid signature).
  * Seluruh skenario lolos verifikasi tanda tangan digital dan idempotensi.
* **Pengujian Lingkungan Live**:
  * Uji keamanan SSL Grade A, HTTP Security Headers, proteksi berkas sensitif `.env`.
* **User Acceptance Testing (UAT)**:
  * Diuji bersama pengelola operasional (Bapak Asep) menggunakan kuesioner Skala Likert.
  * Meliputi 6 dimensi evaluasi (Kesesuaian Fungsional 96,0%, Kinerja & Efisiensi 93,3%, Kegunaan Antarmuka 92,0%, Keandalan Transaksi 94,7%, Keamanan & Akses 93,3%, Dampak Operasional 92,0%).
  * Rata-rata Skor Kelayakan UAT: 93,5% (Kategori: Sangat Layak).
* **Wawancara Evaluasi Operasional**:
  * Bapak Asep memberikan skor penilaian subjektif 9,5 dari 10.
  * Waktu rekapitulasi laporan bulanan terpangkas dari 3–5 hari menjadi instan (dalam hitungan detik).
