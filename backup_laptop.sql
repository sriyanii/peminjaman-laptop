/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.13-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: peminjaman_laptop
-- ------------------------------------------------------
-- Server version	10.11.13-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alat_backup`
--

DROP TABLE IF EXISTS `alat_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alat_backup` (
  `id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `kode_alat` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_alat` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori_id` bigint(20) unsigned DEFAULT NULL,
  `merk` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_produksi` year(4) DEFAULT NULL,
  `spesifikasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`spesifikasi`)),
  `serial_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi` enum('baik','rusak_ringan','rusak_berat','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `status` enum('tersedia','dipinjam','maintenance','hapus') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `harga_sewa_perhari` decimal(12,2) NOT NULL,
  `denda_perhari` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 1,
  `dipinjam` int(11) NOT NULL DEFAULT 0,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alat_backup`
--

LOCK TABLES `alat_backup` WRITE;
/*!40000 ALTER TABLE `alat_backup` DISABLE KEYS */;
/*!40000 ALTER TABLE `alat_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrowings`
--

DROP TABLE IF EXISTS `borrowings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `borrowings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `laptop_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal_pinjam` datetime NOT NULL,
  `tanggal_kembali` datetime NOT NULL,
  `tanggal_dikembalikan` datetime DEFAULT NULL,
  `keperluan` text NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('pending','dipinjam','selesai','dibatalkan','terlambat') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `returned_by` bigint(20) unsigned DEFAULT NULL,
  `kondisi_kembali` enum('baik','sedang','buruk') DEFAULT NULL,
  `catatan_kembali` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `borrowings_user_id_foreign` (`user_id`),
  KEY `borrowings_approved_by_foreign` (`approved_by`),
  KEY `borrowings_rejected_by_foreign` (`rejected_by`),
  KEY `borrowings_returned_by_foreign` (`returned_by`),
  KEY `borrowings_laptop_id_foreign` (`laptop_id`),
  CONSTRAINT `borrowings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `borrowings_laptop_id_foreign` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE SET NULL,
  CONSTRAINT `borrowings_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `borrowings_returned_by_foreign` FOREIGN KEY (`returned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `borrowings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrowings`
--

LOCK TABLES `borrowings` WRITE;
/*!40000 ALTER TABLE `borrowings` DISABLE KEYS */;
/*!40000 ALTER TABLE `borrowings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kategori_alat`
--

DROP TABLE IF EXISTS `kategori_alat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `kategori_alat` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_kategori` varchar(10) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kategori_alat_kode_kategori_unique` (`kode_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kategori_alat`
--

LOCK TABLES `kategori_alat` WRITE;
/*!40000 ALTER TABLE `kategori_alat` DISABLE KEYS */;
INSERT INTO `kategori_alat` VALUES
(6,'LP','Laptop Bisnis','dummy','2026-01-21 22:20:36','2026-01-21 23:58:34'),
(7,'PRJ','Laptop Gaming','dummy','2026-01-21 23:39:52','2026-01-21 23:58:26'),
(8,'LPU','Laptop Ultrabook','dummy','2026-01-22 00:09:38','2026-01-22 00:09:38');
/*!40000 ALTER TABLE `kategori_alat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laptops`
--

DROP TABLE IF EXISTS `laptops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `laptops` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `merk` varchar(50) NOT NULL,
  `model` varchar(100) NOT NULL,
  `processor` varchar(100) NOT NULL,
  `ram` varchar(20) NOT NULL,
  `storage` varchar(50) NOT NULL,
  `serial_number` varchar(255) NOT NULL,
  `status` enum('tersedia','dipinjam','rusak','maintenance') NOT NULL DEFAULT 'tersedia',
  `kondisi` enum('baik','rusak_ringan','rusak_berat') NOT NULL DEFAULT 'baik',
  `tahun_pembelian` year(4) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `warna` varchar(30) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `baterai_kondisi` varchar(20) DEFAULT NULL,
  `garansi` varchar(50) DEFAULT NULL,
  `garansi_berakhir` date DEFAULT NULL,
  `harga_beli` decimal(15,2) DEFAULT NULL,
  `harga_sewa_harian` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `laptops_serial_number_unique` (`serial_number`),
  KEY `laptops_status_index` (`status`),
  KEY `laptops_merk_index` (`merk`),
  KEY `laptops_serial_number_index` (`serial_number`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laptops`
--

LOCK TABLES `laptops` WRITE;
/*!40000 ALTER TABLE `laptops` DISABLE KEYS */;
INSERT INTO `laptops` VALUES
(4,'Dell','Latitude 7490','Intel Core i5-8350U','8GB','256GB SSD','LTP004','dipinjam','baik',2020,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-03-10 13:38:18','2026-03-10 13:38:18',NULL),
(5,'HP','EliteBook 840 G6','Intel Core i7-8665U','16GB','512GB SSD','LTP005','tersedia','baik',2021,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-03-10 13:38:18','2026-03-10 13:38:18',NULL),
(6,'Lenovo','ThinkPad T490','Intel Core i5-8350U','8GB','256GB SSD','LTP006','tersedia','baik',2020,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-03-10 13:38:18','2026-03-10 13:38:18',NULL),
(7,'Asus','ZenBook 14','Intel Core i7-10510U','16GB','512GB SSD','LTP007','tersedia','baik',2022,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-03-10 13:38:18','2026-03-10 13:38:18',NULL),
(8,'Acer','Swift 3','AMD Ryzen 5 4500U','8GB','512GB SSD','LTP008','tersedia','baik',2022,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-03-10 13:38:18','2026-03-10 13:38:18',NULL);
/*!40000 ALTER TABLE `laptops` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2014_10_12_100000_create_password_resets_table',1),
(2,'2026_01_20_020129_create_users_table',1),
(3,'2026_01_20_020339_create_kategori_alat_table',1),
(4,'2026_01_20_020848_create_alat_table',1),
(5,'2026_01_20_041139_fix_users_role_enum',2),
(6,'2026_01_20_074946_create_laptops_table',3),
(8,'2026_01_20_074952_create_peminjaman_table',4),
(9,'2026_01_22_035550_create_tools_table',5),
(10,'2026_01_22_073438_create_borrowings_table',6),
(11,'2026_01_23_024735_create_transactions_table',7),
(12,'2026_01_23_024741_add_saldo_to_users_table',7),
(13,'2026_01_23_032325_create_settings_table',8),
(14,'2026_01_27_015759_create_transaksi_denda_table',9),
(15,'2026_02_12_020853_add_foto_to_tools_table',10),
(16,'2026_03_10_013229_create_cache_table',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pemberitahuan`
--

DROP TABLE IF EXISTS `pemberitahuan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pemberitahuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `konten` varchar(1000) NOT NULL,
  `status` enum('terima','tolak') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pemberitahuan`
--

LOCK TABLES `pemberitahuan` WRITE;
/*!40000 ALTER TABLE `pemberitahuan` DISABLE KEYS */;
INSERT INTO `pemberitahuan` VALUES
(15,'budi_kun','Permintaan Peminjaman Barang Anda Telah di Terima. 1 buah LCD. Username: budi_kun. Silahkan ke bagian Sarpras untuk mengampil barang','terima','2018-11-10 15:13:01'),
(17,'adlubagus94','Permintaan Peminjaman Barang Anda Telah di Terima. 1 buah LCD. Username: adlubagus94. Silahkan ke bagian Sarpras untuk mengampil barang','terima','2018-11-11 01:42:05'),
(19,'adlubagus94','Permintaan Peminjaman Barang Anda Telah di Terima. 2 buah Speaker kecil. Username: adlubagus94. Silahkan ke bagian Sarpras untuk mengampil barang','terima','2018-11-11 01:55:54'),
(20,'adlubagus94','Permintaan Pengembalian Barang Anda Telah di Terima.  buah . Username: ','','2018-11-11 01:56:40'),
(21,'usertest123','Permintaan Peminjaman Barang Anda Telah di Terima. 1 buah LCD. Username: usertest123. Silahkan ke bagian Sarpras untuk mengampil barang','terima','2018-11-11 05:30:46'),
(22,'usertest123','Permintaan Pengembalian Barang Anda Telah di Terima.  buah . Username: ','','2018-11-11 05:31:51');
/*!40000 ALTER TABLE `pemberitahuan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman`
--

DROP TABLE IF EXISTS `peminjaman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `peminjaman` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `laptop_id` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('pending','approved','aktif','selesai','ditolak','batal','terlambat') NOT NULL DEFAULT 'pending',
  `tujuan` enum('meeting','presentasi','training','work_from_home','proyek','lainnya') NOT NULL DEFAULT 'lainnya',
  `keterangan` text DEFAULT NULL,
  `alasan_ditolak` text DEFAULT NULL,
  `catatan_pengembalian` text DEFAULT NULL,
  `denda` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_denda_dibayar` tinyint(1) NOT NULL DEFAULT 0,
  `waktu_approve` datetime DEFAULT NULL,
  `waktu_pengambilan` datetime DEFAULT NULL,
  `waktu_pengembalian` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `peminjaman_approved_by_foreign` (`approved_by`),
  KEY `peminjaman_status_index` (`status`),
  KEY `peminjaman_tanggal_pinjam_index` (`tanggal_pinjam`),
  KEY `peminjaman_user_id_index` (`user_id`),
  KEY `peminjaman_laptop_id_index` (`laptop_id`),
  CONSTRAINT `peminjaman_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peminjaman_laptop_id_foreign` FOREIGN KEY (`laptop_id`) REFERENCES `laptops` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman`
--

LOCK TABLES `peminjaman` WRITE;
/*!40000 ALTER TABLE `peminjaman` DISABLE KEYS */;
INSERT INTO `peminjaman` VALUES
(7,1,4,2,'2026-01-26','2026-02-18','2026-02-18','selesai','presentasi','s\n[Disetujui: ya]',NULL,'Kondisi: rusak_ringan | Catatan: k',0.00,1,'2026-01-26 04:38:45',NULL,'2026-01-27 01:13:58','2026-01-25 20:50:49','2026-01-26 18:18:35','2026-01-26 18:18:35'),
(8,1,5,2,'2026-01-27','2026-02-03','2026-02-03','selesai','presentasi','s\n[Disetujui: s]',NULL,'Kondisi: baik | Catatan: w',0.00,1,'2026-01-27 06:30:39',NULL,'2026-01-27 06:31:08','2026-01-26 23:30:14','2026-01-26 23:30:39',NULL),
(9,1,4,2,'2026-01-27','2026-02-03',NULL,'approved','meeting','alasan\n[Disetujui: ya]',NULL,NULL,0.00,0,'2026-01-27 07:20:51',NULL,NULL,'2026-01-27 00:19:43','2026-03-02 19:12:08','2026-03-02 19:12:08'),
(10,2,4,NULL,'2026-02-21','2026-02-28','2026-02-28','selesai','lainnya',NULL,NULL,NULL,0.00,1,NULL,NULL,NULL,'2026-02-21 02:34:20','2026-02-21 02:34:20',NULL),
(11,2,4,NULL,'2026-02-22','2026-03-01','2026-03-01','selesai','lainnya',NULL,NULL,NULL,0.00,1,NULL,NULL,NULL,'2026-02-22 02:34:20','2026-02-22 02:34:20',NULL),
(12,2,4,NULL,'2026-02-23','2026-03-02','2026-03-02','selesai','lainnya',NULL,NULL,NULL,0.00,1,NULL,NULL,NULL,'2026-02-23 02:34:20','2026-02-23 02:34:20',NULL),
(13,2,4,NULL,'2026-02-24','2026-03-03',NULL,'approved','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-02-24 02:34:20','2026-02-24 02:34:20',NULL),
(14,2,4,NULL,'2026-02-25','2026-03-04',NULL,'approved','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-02-25 02:34:20','2026-02-25 02:34:20',NULL),
(15,2,4,NULL,'2026-02-26','2026-03-05',NULL,'approved','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-02-26 02:34:20','2026-02-26 02:34:20',NULL),
(16,2,4,NULL,'2026-02-27','2026-03-06',NULL,'aktif','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-02-27 02:34:20','2026-02-27 02:34:20',NULL),
(17,2,4,NULL,'2026-02-28','2026-03-07',NULL,'aktif','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-02-28 02:34:20','2026-02-28 02:34:20',NULL),
(18,2,4,NULL,'2026-03-01','2026-03-08',NULL,'aktif','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-03-01 02:34:20','2026-03-10 00:57:54','2026-03-10 00:57:54'),
(19,2,4,NULL,'2026-03-02','2026-03-09',NULL,'pending','lainnya',NULL,NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-03-02 02:34:20','2026-03-02 02:34:20',NULL),
(20,1,4,2,'2026-03-04','2026-03-14',NULL,'approved','presentasi','dfd',NULL,NULL,0.00,0,'2026-03-04 03:16:42',NULL,NULL,'2026-03-03 19:32:02','2026-03-03 20:16:42',NULL),
(21,1,6,2,'2026-03-04','2026-03-20',NULL,'approved','training','dd',NULL,NULL,0.00,0,'2026-03-04 03:00:58',NULL,NULL,'2026-03-03 19:39:14','2026-03-03 20:00:58',NULL),
(22,4,7,NULL,'2026-03-10','2026-03-11',NULL,'pending','meeting','ggggg',NULL,NULL,0.00,0,NULL,NULL,NULL,'2026-03-09 23:07:49','2026-03-09 23:07:49',NULL),
(24,4,8,2,'2026-03-10','2026-03-11',NULL,'approved','meeting','pinjaman',NULL,NULL,0.00,0,'2026-03-10 06:19:11',NULL,NULL,'2026-03-09 23:14:24','2026-03-09 23:19:11',NULL);
/*!40000 ALTER TABLE `peminjaman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_barang`
--

DROP TABLE IF EXISTS `tbl_barang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) NOT NULL,
  `gambar_barang` varchar(100) NOT NULL,
  `stok_barang` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_barang`
--

LOCK TABLES `tbl_barang` WRITE;
/*!40000 ALTER TABLE `tbl_barang` DISABLE KEYS */;
INSERT INTO `tbl_barang` VALUES
(1,'LCD','projektor2.jpeg',30),
(2,'Sapu','sapu.jpg',45),
(3,'Cikrak','cikrak.jpg',40),
(4,'Speaker kecil','spiker.jpg',25),
(6,'Terminal','5000799_0445182c-0725-49a5-81a7-trminal.jpg',20);
/*!40000 ALTER TABLE `tbl_barang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_pinjam`
--

DROP TABLE IF EXISTS `tbl_pinjam`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_pinjam` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(50) NOT NULL,
  `peminjam` varchar(100) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jml_barang` int(50) NOT NULL,
  `tgl_pinjam` varchar(50) NOT NULL,
  `tgl_kembali` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_pinjam`
--

LOCK TABLES `tbl_pinjam` WRITE;
/*!40000 ALTER TABLE `tbl_pinjam` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_pinjam` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_req_kembali`
--

DROP TABLE IF EXISTS `tbl_req_kembali`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_req_kembali` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(50) NOT NULL,
  `peminjam` varchar(50) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jml_barang` int(11) NOT NULL,
  `tgl_pinjam` varchar(50) NOT NULL,
  `tgl_kembali` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_req_kembali`
--

LOCK TABLES `tbl_req_kembali` WRITE;
/*!40000 ALTER TABLE `tbl_req_kembali` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_req_kembali` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_request`
--

DROP TABLE IF EXISTS `tbl_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(50) NOT NULL,
  `peminjam` varchar(50) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jml_barang` int(11) NOT NULL,
  `tgl_pinjam` varchar(50) NOT NULL,
  `tgl_kembali` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_request`
--

LOCK TABLES `tbl_request` WRITE;
/*!40000 ALTER TABLE `tbl_request` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_transaksi`
--

DROP TABLE IF EXISTS `tbl_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tbl_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(50) NOT NULL,
  `peminjam` varchar(100) NOT NULL,
  `level` varchar(50) NOT NULL,
  `jml_barang` int(11) NOT NULL,
  `tgl_pinjam` varchar(50) NOT NULL,
  `tgl_kembali` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_transaksi`
--

LOCK TABLES `tbl_transaksi` WRITE;
/*!40000 ALTER TABLE `tbl_transaksi` DISABLE KEYS */;
INSERT INTO `tbl_transaksi` VALUES
(1,'Terminal','budi_kun','XI RPL 2',10,'12 November 2018 - 07:30 ','12 November 2018 - 16:00 '),
(2,'Terminal','adlubagus94','XII RPL 1',2,'10 November 2018 - 15:00 ','10 November 2018 - 16:00 '),
(3,'Terminal','bagusi','X TKJ 3',2,'12 November 2018 - 12:35 ','12 November 2018 - 16:10 '),
(4,'LCD','bagusi','X TKJ 3',1,'14 November 2018 - 09:00 ','14 November 2018 - 11:30 '),
(5,'LCD','budi_kun','XI RPL 2',1,'14 November 2018 - 09:00 ','14 November 2018 - 11:30 '),
(6,'LCD','adlubagus94','XII RPL 1',1,'12 November 2018 - 07:30 ','12 November 2018 - 10:00 '),
(7,'Speaker kecil','adlubagus94','XII RPL 1',2,'13 November 2018 - 10:00 ','13 November 2018 - 12:00 '),
(8,'LCD','usertest123','xii rpl',1,'20 November 2018 - 16:00 ','21 November 2018 - 13:25 ');
/*!40000 ALTER TABLE `tbl_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tools_backup`
--

DROP TABLE IF EXISTS `tools_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tools_backup` (
  `id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `kode_alat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_alat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `merk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serial_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `status` enum('tersedia','dipinjam','rusak','maintenance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tersedia',
  `kondisi` enum('baik','sedang','buruk') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'baik',
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_pembelian` date DEFAULT NULL,
  `harga` decimal(15,2) DEFAULT NULL,
  `masa_garansi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spesifikasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tools_backup`
--

LOCK TABLES `tools_backup` WRITE;
/*!40000 ALTER TABLE `tools_backup` DISABLE KEYS */;
INSERT INTO `tools_backup` VALUES
(4,'LP001','Laptop','HP','xps','1234',NULL,6,'dipinjam','baik','Rak A1',NULL,500000.00,'1 Tahun','intel 17','baik','2026-01-22 00:14:42','2026-03-03 19:32:02'),
(5,'LP002','Laptop','hs','ks','233',NULL,6,'dipinjam','baik','Rak A1','2026-01-14',800000.00,'1 Tahun','d','d','2026-01-26 22:52:55','2026-01-26 23:30:14'),
(6,'LP003','Laptop 1','HP','Laptop 3','9454','1772500125_a6d4ac85_c793_4e4f_89e0_ecf475d7db59.jpeg',7,'tersedia','baik','Rak A1',NULL,600000.00,'1 Tahun','bagus','bagus','2026-03-02 18:08:45','2026-03-03 20:26:49'),
(7,'LP004','Laptop','HP','baru','2343',NULL,6,'tersedia','baik','Rak A1','2026-03-04',800000.00,'1 Tahun','s','s','2026-03-03 20:29:01','2026-03-03 20:29:01'),
(8,'LP005','Laptop','HP','baru','43ui','1772594962_1728055356826.JPG',6,'dipinjam','baik','Rak A1','2026-03-04',800000.00,'1 Tahun','s','s','2026-03-03 20:29:22','2026-03-09 23:19:11');
/*!40000 ALTER TABLE `tools_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi_denda`
--

DROP TABLE IF EXISTS `transaksi_denda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi_denda` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `peminjaman_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `petugas_id` bigint(20) unsigned DEFAULT NULL,
  `total_denda` decimal(10,2) NOT NULL DEFAULT 0.00,
  `denda_dibayar` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` varchar(255) NOT NULL DEFAULT 'belum_lunas',
  `status_transaksi` varchar(255) NOT NULL DEFAULT 'pending',
  `kondisi_barang` varchar(255) DEFAULT NULL,
  `catatan_cek` text DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `waktu_cek` datetime DEFAULT NULL,
  `waktu_pembayaran` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_denda_peminjaman_id_index` (`peminjaman_id`),
  KEY `transaksi_denda_user_id_index` (`user_id`),
  KEY `transaksi_denda_status_pembayaran_index` (`status_pembayaran`),
  KEY `transaksi_denda_status_transaksi_index` (`status_transaksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi_denda`
--

LOCK TABLES `transaksi_denda` WRITE;
/*!40000 ALTER TABLE `transaksi_denda` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaksi_denda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `level` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
(2,'admin','admin','21232f297a57a5a743894a0e4a801fc3','admin'),
(3,'Adlu Bagus I.','adlubagus94','a193df56eb6d42b05bfdba808eb2de35','XII RPL 1'),
(4,'Budi Serizawa','budi_kun','e10adc3949ba59abbe56e057f20f883e','XI RPL 2'),
(5,'Bagus Irawan','bagusi','e10adc3949ba59abbe56e057f20f883e','X TKJ 3'),
(6,'user test','usertest123','e10adc3949ba59abbe56e057f20f883e','xii rpl ');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas','user') DEFAULT 'user',
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `saldo` decimal(15,2) NOT NULL DEFAULT 0.00,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'yani','yani@gmail.com',NULL,1,'$2y$12$ArciTKC5sOoRmSigHrncvO7/k4vbfu0Gu8gSswdV/9PedPbpd3Fjy','user',NULL,NULL,'active',0.00,NULL,'2026-01-19 20:27:47','2026-01-19 20:27:47'),
(2,'admin','admin@gmail.com',NULL,1,'$2y$12$SwrUe5P.Zn4GMQPfhAzV2ug98.T.4T8qXXTgvTJ6rJ28hWYra8cQy','petugas',NULL,NULL,'active',0.00,NULL,'2026-01-19 20:40:19','2026-03-10 06:57:00'),
(3,'yaya','yaya@gmail.com',NULL,1,'$2y$12$ceFdhuneg1v.pVtOcsRyuuQZYecYLN4nZpgMVUc2xUxCEmQLX1L2y','admin',NULL,NULL,'active',0.00,'L21cV3v1IOWfGlQe0spXD2HWJ31FbJeUf2iQ70VxOJEv2iRBgF3i5dm2ktsd','2026-01-19 21:14:47','2026-01-19 21:14:47'),
(4,'manda','manda@gmail.com',NULL,1,'$2y$12$f/WZNsaNl7UAwOvv38FftuSPCmWcgXIZJLM0ppNLSnbC5FSOp/fCu','user',NULL,NULL,'active',0.00,NULL,'2026-03-09 23:01:46','2026-03-09 23:01:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-11 13:05:59
