/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chat_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reservasi_id` bigint unsigned NOT NULL,
  `sender_id` int unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `chat_messages_sender_id_foreign` (`sender_id`),
  KEY `chat_messages_reservasi_id_id_index` (`reservasi_id`,`id`),
  KEY `chat_messages_reservasi_read_status_index` (`reservasi_id`,`is_read`,`sender_id`),
  CONSTRAINT `chat_messages_reservasi_id_foreign` FOREIGN KEY (`reservasi_id`) REFERENCES `reservasi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customer_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_reviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pekerjaan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bintang` tinyint unsigned NOT NULL DEFAULT '5',
  `ulasan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pertanyaan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `jawaban` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` tinyint NOT NULL DEFAULT '0',
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fasilitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fasilitas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ikon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fasilitas_nama_unique` (`nama`),
  KEY `fasilitas_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guest_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest_chat_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `guest_chat_thread_id` bigint unsigned NOT NULL,
  `sender_type` enum('guest','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sender_id` int unsigned DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_chat_messages_sender_id_foreign` (`sender_id`),
  KEY `idx_guest_chat_messages_thread_id_id` (`guest_chat_thread_id`,`id`),
  CONSTRAINT `guest_chat_messages_guest_chat_thread_id_foreign` FOREIGN KEY (`guest_chat_thread_id`) REFERENCES `guest_chat_threads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `guest_chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `guest_chat_threads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guest_chat_threads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guest_chat_threads_session_token_unique` (`session_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kamar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kamar` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nomor_kamar` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_nomor_kamar` varchar(50) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`deleted_at` is null) then `nomor_kamar` else NULL end)) VIRTUAL,
  `lantai` tinyint NOT NULL DEFAULT '1',
  `tipe` enum('standar','deluxe','vip') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standar',
  `luas_m2` decimal(5,2) NOT NULL,
  `harga_bulan` decimal(12,2) NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('tersedia','terisi','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kamar_active_nomor` (`active_nomor_kamar`),
  KEY `kamar_deleted_at_index` (`deleted_at`),
  KEY `idx_kamar_status_deleted_at` (`status`,`deleted_at`),
  KEY `idx_kamar_tipe_deleted_at` (`tipe`,`deleted_at`),
  KEY `idx_kamar_lantai_deleted_at` (`lantai`,`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kamar_fasilitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kamar_fasilitas` (
  `kamar_id` int unsigned NOT NULL,
  `fasilitas_id` int unsigned NOT NULL,
  PRIMARY KEY (`kamar_id`,`fasilitas_id`),
  KEY `kamar_fasilitas_fasilitas_id_foreign` (`fasilitas_id`),
  CONSTRAINT `kamar_fasilitas_fasilitas_id_foreign` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kamar_fasilitas_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keluhan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `keluhan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `penyewa_id` int unsigned NOT NULL,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('kamar','fasilitas_bersama','kebersihan','keamanan','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','diproses','selesai') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `tanggapan_admin` text COLLATE utf8mb4_unicode_ci,
  `tanggal_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_keluhan_penyewa_status` (`penyewa_id`,`status`),
  CONSTRAINT `keluhan_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `log_notifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_notifikasi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `penyewa_id` int unsigned NOT NULL,
  `tagihan_id` int unsigned DEFAULT NULL,
  `channel` enum('whatsapp','email','system') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('sukses','gagal') COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci,
  `error_msg` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `log_notifikasi_penyewa_id_foreign` (`penyewa_id`),
  KEY `idx_log_notifikasi_tagihan_id` (`tagihan_id`),
  CONSTRAINT `log_notifikasi_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_notifikasi_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifikasi_khusus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifikasi_khusus` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_aktivitas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `user_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifikasi_khusus_sumber_index` (`sumber`),
  KEY `notifikasi_khusus_tipe_aktivitas_index` (`tipe_aktivitas`),
  KEY `idx_notifikasi_khusus_user_created` (`user_id`,`created_at`),
  CONSTRAINT `notifikasi_khusus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `notifikasi_khusus_chk_1` CHECK (json_valid(`data_detail`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `tagihan_id` int unsigned NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_type` varchar(50) DEFAULT NULL,
  `bank` varchar(20) DEFAULT NULL,
  `va_number` varchar(30) DEFAULT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `status_midtrans` varchar(50) DEFAULT NULL,
  `dikonfirmasi_oleh` int unsigned DEFAULT NULL,
  `signature_key` varchar(255) DEFAULT NULL,
  `response_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `tanggal_bayar` datetime DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pembayaran_transaction_id_unique` (`transaction_id`),
  KEY `pembayaran_tagihan_id_foreign` (`tagihan_id`),
  KEY `pembayaran_dikonfirmasi_oleh_foreign` (`dikonfirmasi_oleh`),
  KEY `idx_pembayaran_tanggal_status` (`status_midtrans`,`tanggal_bayar`),
  CONSTRAINT `pembayaran_dikonfirmasi_oleh_foreign` FOREIGN KEY (`dikonfirmasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pembayaran_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`),
  CONSTRAINT `pembayaran_chk_1` CHECK (json_valid(`response_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengeluaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengeluaran` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama_pengeluaran` varchar(150) NOT NULL,
  `kategori` enum('maintenance','utilitas','operasional','lainnya') NOT NULL,
  `nominal` decimal(12,2) NOT NULL,
  `tanggal_pengeluaran` date NOT NULL,
  `bukti_nota` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pengeluaran_tanggal` (`tanggal_pengeluaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengumuman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengumuman` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penyewa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyewa` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `kamar_id` int unsigned NOT NULL,
  `harga_sewa` decimal(12,2) DEFAULT NULL,
  `nik` varchar(50) NOT NULL,
  `active_nik` varchar(20) GENERATED ALWAYS AS ((case when (`deleted_at` is null) then `nik` else NULL end)) VIRTUAL,
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `tanggal_keluar_seharusnya` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `tanggal_billing` tinyint NOT NULL DEFAULT '1',
  `tipe_sewa` enum('harian','mingguan','bulanan') NOT NULL DEFAULT 'bulanan',
  `durasi` tinyint NOT NULL DEFAULT '1',
  `deposit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `no_wali` varchar(20) NOT NULL,
  `nama_wali` varchar(100) NOT NULL,
  `catatan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `penyewa_deleted_at_index` (`deleted_at`),
  KEY `idx_penyewa_status_deleted_at` (`status`,`deleted_at`),
  KEY `idx_penyewa_kamar_deleted_at` (`kamar_id`,`deleted_at`),
  KEY `idx_penyewa_overlap_check` (`kamar_id`,`status`,`tanggal_masuk`,`tanggal_keluar`),
  KEY `penyewa_user_id_index` (`user_id`),
  KEY `penyewa_nik_index` (`nik`),
  CONSTRAINT `penyewa_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`),
  CONSTRAINT `penyewa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `peraturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `peraturan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `judul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ikon` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `urutan` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `reservasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reservasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `kamar_id` int unsigned NOT NULL,
  `dikonfirmasi_oleh` int unsigned DEFAULT NULL,
  `penyewa_id` int unsigned DEFAULT NULL,
  `tipe_sewa` enum('harian','mingguan','bulanan') NOT NULL DEFAULT 'bulanan',
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date NOT NULL,
  `durasi` tinyint NOT NULL DEFAULT '1',
  `total_harga` decimal(12,2) NOT NULL,
  `status` enum('pending','dp','lunas','dikonfirmasi','batal') NOT NULL DEFAULT 'pending',
  `metode_pembayaran` enum('midtrans','cash') DEFAULT NULL,
  `is_dp` tinyint(1) NOT NULL DEFAULT '0',
  `nominal_dp` decimal(12,2) NOT NULL DEFAULT '0.00',
  `nominal_sisa` decimal(12,2) NOT NULL DEFAULT '0.00',
  `snap_token` varchar(255) DEFAULT NULL,
  `order_id` varchar(100) DEFAULT NULL,
  `active_order_id` varchar(100) GENERATED ALWAYS AS ((case when (`deleted_at` is null) then `order_id` else NULL end)) VIRTUAL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `catatan_user` text,
  `catatan_admin` text,
  `tanggal_konfirmasi` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_reservasi_active_order_id` (`active_order_id`),
  KEY `reservasi_dikonfirmasi_oleh_foreign` (`dikonfirmasi_oleh`),
  KEY `reservasi_penyewa_id_foreign` (`penyewa_id`),
  KEY `reservasi_tanggal_mulai_tanggal_selesai_index` (`tanggal_mulai`,`tanggal_selesai`),
  KEY `reservasi_deleted_at_index` (`deleted_at`),
  KEY `idx_reservasi_status_deleted_at` (`status`,`deleted_at`),
  KEY `idx_reservasi_kamar_deleted_at` (`kamar_id`,`deleted_at`),
  KEY `idx_reservasi_user_deleted_at` (`user_id`,`deleted_at`),
  KEY `idx_reservasi_overlap_check` (`kamar_id`,`status`,`tanggal_mulai`,`tanggal_selesai`),
  CONSTRAINT `reservasi_dikonfirmasi_oleh_foreign` FOREIGN KEY (`dikonfirmasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservasi_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`),
  CONSTRAINT `reservasi_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`) ON DELETE SET NULL,
  CONSTRAINT `reservasi_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tagihan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tagihan` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `penyewa_id` int unsigned NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `periode_bulan` tinyint NOT NULL,
  `periode_tahun` smallint NOT NULL,
  `tanggal_tagihan` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `nominal_pokok` decimal(12,2) NOT NULL,
  `nominal_denda` decimal(12,2) NOT NULL DEFAULT '0.00',
  `nominal_total` decimal(12,2) NOT NULL,
  `bulan_keterlambatan` tinyint NOT NULL DEFAULT '0',
  `status` enum('pending','lunas','gagal','kadaluarsa','terlambat') NOT NULL DEFAULT 'pending',
  `metode_pembayaran` enum('midtrans','cash','') DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_periode` (`penyewa_id`,`periode_bulan`,`periode_tahun`),
  UNIQUE KEY `tagihan_order_id_unique` (`order_id`),
  KEY `idx_tagihan_cron_keterlambatan` (`status`,`tanggal_jatuh_tempo`),
  KEY `tagihan_penyewa_id_status_index` (`penyewa_id`,`status`),
  CONSTRAINT `tagihan_penyewa_id_foreign` FOREIGN KEY (`penyewa_id`) REFERENCES `penyewa` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_email` varchar(150) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`deleted_at` is null) then `email` else NULL end)) VIRTUAL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_no_hp` varchar(20) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`deleted_at` is null) then `no_hp` else NULL end)) VIRTUAL,
  `nik` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active_nik` varchar(20) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS ((case when (`deleted_at` is null) then `nik` else NULL end)) VIRTUAL,
  `nama_wali` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_wali` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('admin','penyewa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'penyewa',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `require_password_change` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_active_email` (`active_email`),
  UNIQUE KEY `uq_users_active_no_hp` (`active_no_hp`),
  UNIQUE KEY `uq_users_active_nik` (`active_nik`),
  KEY `users_deleted_at_index` (`deleted_at`),
  KEY `idx_users_role_deleted_at` (`role`,`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `whatsapp_clicks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `whatsapp_clicks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kamar_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `whatsapp_clicks_kamar_id_foreign` (`kamar_id`),
  KEY `idx_whatsapp_clicks_created` (`created_at`),
  CONSTRAINT `whatsapp_clicks_kamar_id_foreign` FOREIGN KEY (`kamar_id`) REFERENCES `kamar` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2026_01_01_000001_create_kamar_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2026_01_01_000002_create_fasilitas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2026_01_01_000003_create_kamar_fasilitas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2026_01_01_000004_create_penyewa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2026_01_01_000005_create_tagihan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2026_01_01_000006_create_pembayaran_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2026_01_01_000007_create_log_notifikasi_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2026_01_01_000008_create_reservasi_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2026_01_01_000009_create_chat_messages_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2026_01_01_000010_add_fields_to_fasilitas_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2026_06_04_081826_add_require_password_change_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2026_06_04_085600_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2026_06_04_091000_create_customer_reviews_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2026_06_07_110145_create_pengeluaran_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2026_06_07_182742_create_faqs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2026_06_07_183800_create_keluhan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2026_06_09_000000_create_peraturan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2026_06_09_064203_create_galleries_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2026_06_12_000000_create_guest_chats_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2026_06_12_054323_add_harga_sewa_to_penyewa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2026_06_17_000000_create_pengumuman_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2026_06_17_062439_create_notifikasi_khusus_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2026_06_21_074556_add_soft_deletes_to_essential_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2026_06_21_150000_increase_nomor_kamar_length',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2026_06_21_160000_add_indexes_to_chat_messages_and_tagihan_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2026_06_23_100000_add_tanggal_keluar_seharusnya_to_penyewa_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2026_06_29_100000_optimize_chat_messages_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2026_06_29_101500_add_composite_indexes_for_soft_deletes_and_performance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2026_07_11_100325_create_whatsapp_clicks_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2026_07_11_103429_add_indexes_for_overlap_check',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2026_07_11_134300_add_profile_fields_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2026_07_11_160000_optimize_database_performance',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_07_12_130000_add_composite_index_to_keluhan_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_07_12_170000_increase_unique_fields_length_for_soft_deletes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_07_13_080000_rename_pertanyaaan_to_pertanyaan_in_faqs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_07_20_102144_fix_penyewa_unique_constraints',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_07_20_102435_add_terlambat_to_tagihan_status',1);
