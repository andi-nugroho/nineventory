-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: nineventory
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `departemen` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,2,'Budi Santoso','Software Engineer','IT','budi.santoso@nineventory.com','081234567890','2026-02-02 06:31:00'),(2,3,'Ani Lestari','Project Manager','IT','ani.lestari@nineventory.com','081234567891','2026-02-02 06:31:00'),(3,4,'Rudi Hartono','UI/UX Designer','Design','rudi.hartono@nineventory.com','081234567892','2026-02-02 06:31:00');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventaris`
--

DROP TABLE IF EXISTS `inventaris`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventaris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `stok_total` int(11) NOT NULL DEFAULT 0,
  `stok_tersedia` int(11) NOT NULL DEFAULT 0,
  `kondisi` enum('baik','rusak ringan','rusak berat') NOT NULL DEFAULT 'baik',
  `lokasi` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Image filename for the product',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_stok_tersedia` (`stok_tersedia`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventaris`
--

LOCK TABLES `inventaris` WRITE;
/*!40000 ALTER TABLE `inventaris` DISABLE KEYS */;
INSERT INTO `inventaris` VALUES (1,'Laptop Dell Latitude 5420','Elektronik',10,10,'baik','Gudang A - Rak 1','Laptop untuk keperluan kantor dengan spesifikasi Intel Core i5, RAM 8GB, SSD 256GB','m4-macbook-pro.webp','2026-02-02 06:29:32','2026-02-04 09:18:25'),(2,'Mouse Wireless Logitech M185','Aksesoris',25,25,'baik','Gudang A - Rak 2','Mouse wireless dengan koneksi USB receiver','mouse-apple.jpg','2026-02-02 06:29:32','2026-02-04 09:18:25'),(3,'Keyboard Mechanical Keychron K2','Aksesoris',15,15,'baik','Gudang A - Rak 2','Keyboard mechanical dengan switch Gateron Brown','keyboard-apple.webp','2026-02-02 06:29:32','2026-02-04 09:18:25'),(4,'Monitor LG 24 Inch','Elektronik',8,8,'baik','Gudang B - Rak 1','Monitor LED 24 inch Full HD 1080p','monitor-apple.webp','2026-02-02 06:29:32','2026-02-04 09:18:25'),(5,'Proyektor Epson EB-X06','Elektronik',3,3,'baik','Ruang Meeting','Proyektor untuk presentasi dengan brightness 3600 lumens','projector.jpg','2026-02-02 06:29:32','2026-02-04 09:18:25'),(6,'Webcam Logitech C920','Elektronik',12,12,'baik','Gudang A - Rak 3','Webcam HD 1080p untuk video conference','webcam.jpg','2026-02-02 06:29:32','2026-02-04 09:18:25'),(7,'Headset Sony WH-1000XM4','Aksesoris',20,20,'baik','Gudang B - Rak 2','Headset wireless dengan noise cancelling','headset.png','2026-02-02 06:29:32','2026-02-04 09:18:25'),(8,'Printer HP LaserJet Pro','Elektronik',5,5,'baik','Ruang Administrasi','Printer laser monochrome untuk dokumen','printer.jpg','2026-02-02 06:29:32','2026-02-04 09:18:25'),(9,'Scanner Canon LiDE 300','Elektronik',4,4,'baik','Ruang Administrasi','Scanner flatbed untuk dokumen A4','scanner.webp','2026-02-02 06:29:32','2026-02-04 09:18:25'),(10,'Kabel HDMI 2 Meter','Aksesoris',30,29,'baik','Gudang A - Rak 4','Kabel HDMI untuk koneksi display','kabel.webp','2026-02-02 06:29:32','2026-02-04 09:18:25');
/*!40000 ALTER TABLE `inventaris` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman`
--

DROP TABLE IF EXISTS `peminjaman`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_peminjaman` varchar(20) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date DEFAULT NULL COMMENT 'Expected return date set by admin',
  `tanggal_kembali` date DEFAULT NULL COMMENT 'Actual return date',
  `status` enum('pending','approved','rejected','returned') NOT NULL DEFAULT 'pending',
  `keterangan` text DEFAULT NULL,
  `alasan_reject` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_tanggal_pinjam` (`tanggal_pinjam`),
  KEY `fk_employee_id` (`employee_id`),
  CONSTRAINT `fk_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman`
--

LOCK TABLES `peminjaman` WRITE;
/*!40000 ALTER TABLE `peminjaman` DISABLE KEYS */;
INSERT INTO `peminjaman` VALUES (1,'LOAN-20260202-979',4,3,'2026-02-01','2026-02-08',NULL,'approved','untuk presntasi',NULL,'2026-02-02 06:32:13','2026-02-02 06:34:29'),(2,'LOAN-20260202-841',3,2,'2026-02-02',NULL,NULL,'pending','untuk meeting',NULL,'2026-02-02 06:33:16','2026-02-02 06:33:16'),(3,'LOAN-20260202-304',2,1,'2026-02-03',NULL,NULL,'pending','untuk keperluan meeting',NULL,'2026-02-02 06:34:05','2026-02-02 06:34:05'),(4,'LOAN-20260202-289',4,3,'2026-02-01',NULL,NULL,'pending','untuk keprlun meeting',NULL,'2026-02-02 06:41:37','2026-02-02 06:41:37'),(5,'LOAN-20260204-411',4,3,'2026-02-04',NULL,NULL,'pending','',NULL,'2026-02-04 08:36:03','2026-02-04 08:36:03');
/*!40000 ALTER TABLE `peminjaman` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_backup`
--

DROP TABLE IF EXISTS `peminjaman_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `inventaris_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_rencana` date DEFAULT NULL COMMENT 'Expected return date set by admin',
  `tanggal_kembali` date DEFAULT NULL COMMENT 'Actual return date',
  `status` enum('pending','approved','rejected','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alasan_reject` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reason for rejection if status is rejected',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_backup`
--

LOCK TABLES `peminjaman_backup` WRITE;
/*!40000 ALTER TABLE `peminjaman_backup` DISABLE KEYS */;
INSERT INTO `peminjaman_backup` VALUES (1,2,1,1,'2026-01-15',NULL,NULL,'approved','Untuk keperluan presentasi klien',NULL,'2026-02-02 06:29:32','2026-02-02 06:29:32'),(2,2,3,1,'2026-01-18',NULL,NULL,'pending','Butuh keyboard untuk workstation baru',NULL,'2026-02-02 06:29:32','2026-02-02 06:29:32'),(3,2,5,1,'2026-01-20',NULL,NULL,'pending','Presentasi meeting bulanan',NULL,'2026-02-02 06:29:32','2026-02-02 06:29:32');
/*!40000 ALTER TABLE `peminjaman_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `peminjaman_detail`
--

DROP TABLE IF EXISTS `peminjaman_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `peminjaman_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `peminjaman_id` int(11) NOT NULL,
  `inventaris_id` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_peminjaman` (`peminjaman_id`),
  KEY `idx_inventaris` (`inventaris_id`),
  CONSTRAINT `peminjaman_detail_ibfk_1` FOREIGN KEY (`peminjaman_id`) REFERENCES `peminjaman` (`id`) ON DELETE CASCADE,
  CONSTRAINT `peminjaman_detail_ibfk_2` FOREIGN KEY (`inventaris_id`) REFERENCES `inventaris` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `peminjaman_detail`
--

LOCK TABLES `peminjaman_detail` WRITE;
/*!40000 ALTER TABLE `peminjaman_detail` DISABLE KEYS */;
INSERT INTO `peminjaman_detail` VALUES (1,1,10,1,NULL,'2026-02-02 06:32:13','2026-02-02 06:32:13'),(2,2,1,1,NULL,'2026-02-02 06:33:16','2026-02-02 06:33:16'),(3,3,4,1,NULL,'2026-02-02 06:34:05','2026-02-02 06:34:05'),(4,4,1,1,NULL,'2026-02-02 06:41:37','2026-02-02 06:41:37'),(5,4,10,1,NULL,'2026-02-02 06:41:37','2026-02-02 06:41:37'),(6,5,7,1,'','2026-02-04 08:36:03','2026-02-04 08:36:03');
/*!40000 ALTER TABLE `peminjaman_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Bcrypt hashed password',
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_email` (`email`),
  KEY `idx_role` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','admin@nineventory.com','$2y$10$f6epKe4z4aX1Pp73fXeWKuVRUx4RHBOu.dNTEYe/iyCLHHxeZRLEG','admin','2026-02-02 06:29:32'),(2,'budi.santoso','budi.santoso@nineventory.com','$2y$10$Sd01A2RUlxbPrmPJN.zaSe/JVdoUt3dk.qVnBLwutruOG55viGnva','user','2026-02-02 06:29:32'),(3,'ani.lestari','ani.lestari@nineventory.com','$2y$10$Sd01A2RUlxbPrmPJN.zaSe/JVdoUt3dk.qVnBLwutruOG55viGnva','user','2026-02-02 06:29:32'),(4,'rudi.hartono','rudi.hartono@nineventory.com','$2y$10$Sd01A2RUlxbPrmPJN.zaSe/JVdoUt3dk.qVnBLwutruOG55viGnva','user','2026-02-02 06:29:32');
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

-- Dump completed on 2026-02-10  0:52:28
