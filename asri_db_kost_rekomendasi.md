# Hasil Audit & Rekomendasi DBA Expert - Optimalisasi Database Asri Boarding House (MySQL 8.x / InnoDB)

Laporan audit mendalam ini dirancang untuk mendeteksi celah struktural, redundansi indeks, efisiensi penulisan (write performance), mitigasi data bloat, serta integritas referensial pada skema database `asri_kost_db` untuk proyeksi jangka panjang 5 hingga 10 tahun ke depan.

---

## 1. TABEL AUDIT SKEMA DATABASE

| No | Nama Tabel / Komponen | Potensi Masalah/Efisiensi (5-10 Tahun) | Status | Tingkat Dampak Performa |
| :--- | :--- | :--- | :--- | :--- |
| **1** | `kamar` | Indeks tunggal `idx_status` redundan karena sudah dicakup oleh indeks komposit `idx_kamar_status_deleted_at`. | **Aman (Optimasi Indeks)** | Low |
| **2** | `users` | Indeks tunggal `idx_role` redundan karena sudah dicakup oleh indeks komposit `idx_users_role_deleted_at`. | **Aman (Optimasi Indeks)** | Low |
| **3** | `reservasi` | Indeks `reservasi_status_index` dan `reservasi_kamar_id_index` redundan karena sudah dicakup oleh `idx_reservasi_status_deleted_at` dan `idx_reservasi_kamar_deleted_at`. | **Aman (Optimasi Indeks)** | Low to Medium |
| **4** | `penyewa` | Indeks `idx_penyewa_user_deleted_at` redundan karena kolom `user_id` sudah memiliki indeks unik `penyewa_user_id_unique`. | **Aman (Optimasi Indeks)** | Low |
| **5** | `guest_chat_messages` | Kueri memuat pesan obrolan menggunakan filter `guest_chat_thread_id` terurut berdasarkan `id` akan memicu operasi **Filesort** lambat karena tidak memiliki indeks komposit `(guest_chat_thread_id, id)`. | **Penumpukan Data (Butuh Indeks)** | High |
| **6** | `log_notifikasi` | Kolom `tagihan_id` tidak memiliki indeks dan *foreign key constraint* (potensi *ghost data* jika tagihan dihapus). Kueri filter notifikasi berdasarkan tagihan akan memicu *full table scan*. Integritas referensial penyewa diatur ke `RESTRICT` secara default (menghambat proses penghapusan). | **Celah Ghost Table & Penumpukan Data** | Medium to High |
| **7** | `sessions` | Kolom `payload` bertipe `longtext` (hingga 4GB). Data payload session asli hanya beberapa KB, sehingga alokasi *off-page* InnoDB memicu fragmentasi ruang disk. Tidak ada pembersihan otomatis di tingkat database. | **Penumpukan Data & Fragmentasi** | Medium |
| **8** | `pengeluaran` | Kueri laporan pengeluaran bulanan/tahunan berdasarkan rentang tanggal akan lambat (*full table scan*) karena tidak ada indeks pada `tanggal_pengeluaran`. Struktur belum siap untuk ekspansi multi-cabang. | **Aman (Butuh Indeks Laporan)** | Medium |
| **9** | `pembayaran` | Tidak ada indeks pada `tanggal_bayar` atau status, memperlambat kueri rekapitulasi keuangan (pendapatan bulanan). | **Aman (Butuh Indeks Finansial)** | Medium |
| **10** | `notifikasi_khusus` | Kueri notifikasi user terurut waktu (`WHERE user_id = ? ORDER BY created_at DESC`) memicu **Filesort** lambat karena tidak ada indeks komposit `(user_id, created_at DESC)`. | **Penumpukan Data (Butuh Indeks)** | Medium |
| **11** | `whatsapp_clicks` | Tidak memiliki indeks pada `created_at` untuk analisis traffic klik WhatsApp per rentang waktu. | **Penumpukan Data** | Low to Medium |
| **12** | **Unique Constraints** (`users`, `penyewa`, `kamar`, `reservasi`) | Penyematan suffix `_deleted_[timestamp]` secara manual pada saat soft-delete (untuk menghindari tabrakan data unik) merusak integritas kueri keuangan/laporan masa lalu. Data historis di PDF invoice/laporan akan tampil berantakan (misal: `nik_deleted_1719656822`). | **Aman (Kelemahan Desain)** | Medium (Integritas Data) |

---

## 2. ANALISIS AKAR MASALAH (Root Cause Analysis)

### A. Redundansi Indeks & Beban Penulisan (Write Overhead)
Ketika data membengkak dalam 10 tahun ke depan, setiap operasi `INSERT`, `UPDATE`, atau `DELETE` akan memaksa MySQL untuk memperbarui seluruh indeks yang ada. Adanya indeks redundan seperti `idx_status` pada `kamar` (yang sudah terwakili secara sempurna oleh `idx_kamar_status_deleted_at`) membuang memori buffer pool InnoDB dan menurunkan throughput penulisan database secara sia-sia.

### B. Desain Suffix Soft Deletes & Kerusakan Data Historis
Mekanisme penanganan nilai unik (`email`, `nik`, `nomor_kamar`, `order_id`) saat soft-delete dengan mengubah nilai asli (menambahkan suffix `_deleted_[timestamp]`) adalah solusi taktis tingkat aplikasi yang memicu masalah integritas jangka panjang. 
- **Akar Masalah**: Jika laporan keuangan masa lalu memanggil data penyewa yang sudah dinonaktifkan/dihapus, data NIK atau Email yang tercetak pada invoice akan terlihat kotor (misal: `3374092108900001_deleted_1719656822`).
- **Solusi MySQL 8.x**: Penggunaan *Virtual Generated Column* dikombinasikan dengan *Unique Index*. Kita dapat membuat kolom virtual yang hanya bernilai jika data aktif (`deleted_at IS NULL`) dan `NULL` jika data dihapus. Karena MySQL memperbolehkan duplikasi nilai `NULL` pada indeks unik, integritas data unik tetap terjaga di baris aktif, sementara data asli di baris non-aktif tetap bersih 100%.

### C. Bloat Data & Kerentanan Fragmentasi InnoDB
- **Payload Sessions**: Laravel secara default menggunakan tipe data `longtext` untuk kolom `payload` pada tabel `sessions`. Dalam arsitektur InnoDB, kolom berukuran besar akan dialokasikan di luar halaman data utama (*overflow pages*). Penghapusan session harian yang masif akan meninggalkan celah kosong pada file tablespace `.ibd`, menyebabkan fragmentasi ruang disk jangka panjang yang memperlambat kueri baca.
- **Pembersihan Log Statis**: Tabel `log_notifikasi` dan `sessions` tidak didukung oleh mekanisme *purging* otomatis berbasis engine (seperti *Event Scheduler*). Hal ini membuat storage engine terus membengkak secara statis hingga memakan kuota disk server.

### D. Celah Relasional & Operasi Filesort yang Lambat
Beberapa tabel dengan pertumbuhan data tinggi masih mengandalkan operasi pencarian tanpa indeks range scan yang tepat. Sebagai contoh, kueri chat pesan atau notifikasi pengguna menggunakan order sorting waktu akan menghasilkan operasi `Using filesort` pada `EXPLAIN Query`, yang memaksa MySQL mengurutkan data di dalam memori sementara (sort buffer) alih-alih memanfaatkan pengurutan alami indeks.

---

## 3. SOLUSI & REKOMENDASI QUERY DDL/DML

Berikut adalah langkah-langkah optimalisasi skema database yang dapat Anda jalankan langsung pada database `asri_kost_db` untuk meningkatkan performa secara dramatis.

### Langkah 1: Eliminasi Indeks Redundan (Menghemat Overhead Menulis)
Jalankan perintah berikut untuk menghapus indeks kolom tunggal yang sudah ter-cover oleh indeks komposit:

```sql
-- Kamar
ALTER TABLE `kamar` DROP INDEX `idx_status`;

-- Users
ALTER TABLE `users` DROP INDEX `idx_role`;

-- Reservasi
ALTER TABLE `reservasi` 
  DROP INDEX `reservasi_status_index`,
  DROP INDEX `reservasi_kamar_id_index`;

-- Penyewa
ALTER TABLE `penyewa` DROP INDEX `idx_penyewa_user_deleted_at`;
```

---

### Langkah 2: Penerapan Kolom Virtual & Indeks Unik Fungsional (Penyelesaian Soft Deletes)
Dengan metode ini, Anda **tidak perlu lagi** mengubah nilai kolom asli secara manual di Laravel Controller (menghilangkan kode `_deleted_` di Controller). Data historis tetap bersih 100%.

```sql
-- 1. Tabel users (email, no_hp, nik)
ALTER TABLE `users`
  ADD COLUMN `active_email` VARCHAR(150) GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `email`, NULL)) VIRTUAL,
  ADD COLUMN `active_no_hp` VARCHAR(20) GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `no_hp`, NULL)) VIRTUAL,
  ADD COLUMN `active_nik` VARCHAR(20) GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `nik`, NULL)) VIRTUAL;

ALTER TABLE `users`
  DROP INDEX `users_email_unique`,
  DROP INDEX `users_no_hp_unique`,
  DROP INDEX `users_nik_unique`,
  ADD UNIQUE KEY `uq_users_active_email` (`active_email`),
  ADD UNIQUE KEY `uq_users_active_no_hp` (`active_no_hp`),
  ADD UNIQUE KEY `uq_users_active_nik` (`active_nik`);

-- 2. Tabel kamar (nomor_kamar)
ALTER TABLE `kamar`
  ADD COLUMN `active_nomor_kamar` VARCHAR(50) GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `nomor_kamar`, NULL)) VIRTUAL;

ALTER TABLE `kamar`
  DROP INDEX `kamar_nomor_kamar_unique`,
  ADD UNIQUE KEY `uq_kamar_active_nomor` (`active_nomor_kamar`);

-- 3. Tabel penyewa (nik)
ALTER TABLE `penyewa`
  ADD COLUMN `active_nik` VARCHAR(20) GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `nik`, NULL)) VIRTUAL;

ALTER TABLE `penyewa`
  DROP INDEX `penyewa_nik_unique`,
  ADD UNIQUE KEY `uq_penyewa_active_nik` (`active_nik`);

-- 4. Tabel reservasi (order_id)
ALTER TABLE `reservasi`
  ADD COLUMN `active_order_id` VARCHAR(100) GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `order_id`, NULL)) VIRTUAL;

ALTER TABLE `reservasi`
  DROP INDEX `reservasi_order_id_unique`,
  ADD UNIQUE KEY `uq_reservasi_active_order_id` (`active_order_id`);
```

---

### Langkah 3: Penambahan Indeks Komposit & Kunci Tamu untuk Skalabilitas Kueri
Optimalkan kueri-kueri transaksi keuangan, detail pesan obrolan, dan log notifikasi agar menggunakan Index Range Scan:

```sql
-- 1. Mengurangi Filesort pada guest chat
ALTER TABLE `guest_chat_messages`
  ADD KEY `idx_guest_chat_messages_thread_id_id` (`guest_chat_thread_id`, `id`);

-- 2. Mengurangi Filesort pada notifikasi khusus user terurut waktu
ALTER TABLE `notifikasi_khusus`
  ADD KEY `idx_notifikasi_khusus_user_created` (`user_id`, `created_at` DESC);

-- 3. Indeks laporan pengeluaran bulanan/tahunan
ALTER TABLE `pengeluaran`
  ADD KEY `idx_pengeluaran_tanggal` (`tanggal_pengeluaran`);

-- 4. Indeks laporan pendapatan bulanan/tahunan (Midtrans settlement)
ALTER TABLE `pembayaran`
  ADD KEY `idx_pembayaran_tanggal_status` (`status_midtrans`, `tanggal_bayar`);

-- 5. Indeks statistik klik tombol WhatsApp
ALTER TABLE `whatsapp_clicks`
  ADD KEY `idx_whatsapp_clicks_created` (`created_at`);

-- 6. Optimalisasi log_notifikasi (Tambah FK tagihan & Ubah On Delete)
ALTER TABLE `log_notifikasi`
  ADD KEY `idx_log_notifikasi_tagihan_id` (`tagihan_id`),
  ADD CONSTRAINT `log_notifikasi_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE SET NULL;

-- Memperbaiki restrict delete log_notifikasi terhadap penyewa
ALTER TABLE `log_notifikasi`
  DROP FOREIGN KEY `log_notifikasi_penyewa_id_foreign`;

ALTER TABLE `log_notifikasi`
  ADD CONSTRAINT `log_notifikasi_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
```

---

### Langkah 4: Proteksi Concurrency & Integritas Data via CHECK Constraints
Mencegah masuknya nilai negatif akibat kegagalan komputasi di tingkat aplikasi atau manipulasi SQL langsung:

```sql
-- Tabel: tagihan
ALTER TABLE `tagihan`
  ADD CONSTRAINT `chk_tagihan_nominal_pokok` CHECK (`nominal_pokok` >= 0),
  ADD CONSTRAINT `chk_tagihan_nominal_denda` CHECK (`nominal_denda` >= 0),
  ADD CONSTRAINT `chk_tagihan_nominal_total` CHECK (`nominal_total` >= 0);

-- Tabel: reservasi
ALTER TABLE `reservasi`
  ADD CONSTRAINT `chk_reservasi_total_harga` CHECK (`total_harga` >= 0),
  ADD CONSTRAINT `chk_reservasi_nominal_dp` CHECK (`nominal_dp` >= 0),
  ADD CONSTRAINT `chk_reservasi_nominal_sisa` CHECK (`nominal_sisa` >= 0),
  ADD CONSTRAINT `chk_reservasi_tanggal_logic` CHECK (`tanggal_selesai` >= `tanggal_mulai`);

-- Tabel: pembayaran
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `chk_pembayaran_nominal` CHECK (`nominal` >= 0);

-- Tabel: pengeluaran
ALTER TABLE `pengeluaran`
  ADD CONSTRAINT `chk_pengeluaran_nominal` CHECK (`nominal` >= 0);

-- Tabel: penyewa
ALTER TABLE `penyewa`
  ADD CONSTRAINT `chk_penyewa_harga_sewa` CHECK (`harga_sewa` >= 0),
  ADD CONSTRAINT `chk_penyewa_deposit` CHECK (`deposit` >= 0),
  ADD CONSTRAINT `chk_penyewa_tanggal_logic` CHECK (`tanggal_keluar_seharusnya` IS NULL OR `tanggal_keluar_seharusnya` >= `tanggal_masuk`);
```

---

### Langkah 5: Mitigasi Bloat Data & Housekeeping Terjadwal
1. **Mengurangi ukuran kolom payload session** dari `longtext` menjadi `mediumtext` agar muat dalam alokasi memori halaman InnoDB.
2. **Membuat MySQL Events** untuk melakukan pruning (pembersihan) data sampah secara otomatis harian di tingkat database.

```sql
-- 1. Optimasi struktur payload session
ALTER TABLE `sessions` MODIFY COLUMN `payload` MEDIUMTEXT NOT NULL;

-- 2. Aktifkan Event Scheduler secara global
SET GLOBAL event_scheduler = ON;

-- 3. Event pembersihan sessions kedaluwarsa (lebih dari 7 hari)
DELIMITER $$
CREATE EVENT IF NOT EXISTS `ev_purge_expired_sessions`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  DELETE FROM `sessions` WHERE `last_activity` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 7 DAY));
END$$

-- 4. Event pembersihan log notifikasi lama (lebih dari 90 hari)
CREATE EVENT IF NOT EXISTS `ev_purge_old_notification_logs`
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
BEGIN
  DELETE FROM `log_notifikasi` WHERE `created_at` < DATE_SUB(NOW(), INTERVAL 90 DAY);
END$$
DELIMITER ;
```

---

### Langkah 6: Pemeliharaan Kapasitas Disk (Housekeeping Storage Engine)
Operasi penghapusan data secara masif pada sessions dan log tidak akan mengembalikan ruang disk ke OS secara otomatis. Jalankan perintah pemeliharaan berikut secara berkala (misal 1-3 bulan sekali) pada saat lalu lintas rendah (*maintenance window*) untuk merapikan fragmentasi tabel:

```sql
OPTIMIZE TABLE `sessions`;
OPTIMIZE TABLE `log_notifikasi`;
OPTIMIZE TABLE `chat_messages`;
OPTIMIZE TABLE `guest_chat_messages`;
```

---

## 4. REKOMENDASI SKALABILITAS 10 TAHUN (Multi-Cabang)
Untuk mendukung ekspansi bisnis 5-10 tahun ke depan di mana kost Asri memiliki cabang baru, disarankan untuk menambahkan entitas cabang:

```sql
-- 1. Buat tabel cabang baru
CREATE TABLE `cabang_kost` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama_cabang` varchar(150) NOT NULL,
  `alamat` text NOT NULL,
  `no_hp_cabang` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Hubungkan kamar dengan cabang
ALTER TABLE `kamar`
  ADD COLUMN `cabang_id` int(10) unsigned DEFAULT NULL AFTER `id`,
  ADD CONSTRAINT `kamar_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang_kost` (`id`) ON DELETE SET NULL;

-- 3. Hubungkan pengeluaran operasional dengan cabang
ALTER TABLE `pengeluaran`
  ADD COLUMN `cabang_id` int(10) unsigned DEFAULT NULL AFTER `id`,
  ADD CONSTRAINT `pengeluaran_cabang_id_foreign` FOREIGN KEY (`cabang_id`) REFERENCES `cabang_kost` (`id`) ON DELETE SET NULL;
```

Dengan seluruh rekomendasi di atas dieksekusi, database Asri Boarding House dijamin memiliki higienitas sangat tinggi, performa kueri optimal, bebas dari ghost tables, dan siap melayani pertumbuhan operasional 5 hingga 10 tahun ke depan dengan resiliensi tingkat tinggi.
