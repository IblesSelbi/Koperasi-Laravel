/*
SQLyog Community v13.3.0 (64 bit)
MySQL - 10.4.32-MariaDB : Database - koperasi
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`koperasi` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `koperasi`;

/*Table structure for table `bayar_angsuran` */

DROP TABLE IF EXISTS `bayar_angsuran`;

CREATE TABLE `bayar_angsuran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_bayar` varchar(20) NOT NULL,
  `pinjaman_id` bigint(20) unsigned NOT NULL COMMENT 'Referensi ke pinjaman',
  `angsuran_ke` int(11) NOT NULL COMMENT 'Angsuran ke berapa',
  `tanggal_jatuh_tempo` date NOT NULL COMMENT 'Tanggal jatuh tempo pembayaran',
  `tanggal_bayar` datetime DEFAULT NULL COMMENT 'Tanggal aktual pembayaran',
  `jumlah_angsuran` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Angsuran pokok + bunga',
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Jumlah yang dibayarkan',
  `denda` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Denda keterlambatan',
  `ke_kas_id` bigint(20) DEFAULT NULL COMMENT 'Masuk ke kas mana',
  `status_bayar` enum('Belum','Lunas') NOT NULL DEFAULT 'Belum',
  `keterangan` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User yang memproses pembayaran',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bayar_angsuran_kode_bayar_unique` (`kode_bayar`),
  KEY `bayar_angsuran_pinjaman_id_foreign` (`pinjaman_id`),
  KEY `bayar_angsuran_ke_kas_id_foreign` (`ke_kas_id`),
  KEY `bayar_angsuran_user_id_foreign` (`user_id`),
  KEY `bayar_angsuran_angsuran_ke_index` (`angsuran_ke`),
  KEY `bayar_angsuran_tanggal_jatuh_tempo_index` (`tanggal_jatuh_tempo`),
  KEY `bayar_angsuran_status_bayar_index` (`status_bayar`),
  CONSTRAINT `bayar_angsuran_ke_kas_id_foreign` FOREIGN KEY (`ke_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `bayar_angsuran_pinjaman_id_foreign` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjaman` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bayar_angsuran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2005 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bayar_angsuran` */

insert  into `bayar_angsuran`(`id`,`kode_bayar`,`pinjaman_id`,`angsuran_ke`,`tanggal_jatuh_tempo`,`tanggal_bayar`,`jumlah_angsuran`,`jumlah_bayar`,`denda`,`ke_kas_id`,`status_bayar`,`keterangan`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1001,'BYR00001',108,1,'2026-02-22','2026-01-22 09:04:00',787500.00,787500.00,0.00,1,'Lunas',NULL,1,'2026-01-22 09:02:58','2026-01-22 09:04:55',NULL),
(1002,'BYR00002',108,2,'2026-03-22','2026-01-22 09:05:00',787500.00,787500.00,0.00,1,'Lunas',NULL,1,'2026-01-22 09:02:59','2026-01-22 09:05:55',NULL),
(1003,'BYR00003',108,3,'2026-04-22','2026-01-22 09:05:00',787500.00,787500.00,0.00,1,'Lunas',NULL,1,'2026-01-22 09:02:59','2026-01-22 09:06:01',NULL),
(1004,'BYR00004',108,4,'2026-05-22','2026-01-22 09:06:00',787500.00,787500.00,0.00,3,'Lunas',NULL,1,'2026-01-22 09:02:59','2026-01-22 09:06:07',NULL),
(1005,'BYR00005',109,1,'2026-02-22',NULL,262500.00,0.00,0.00,NULL,'Belum',NULL,NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(1006,'BYR00006',109,2,'2026-03-22',NULL,262500.00,0.00,0.00,NULL,'Belum',NULL,NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(1007,'BYR00007',109,3,'2026-04-22',NULL,262500.00,0.00,0.00,NULL,'Belum',NULL,NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(1008,'BYR00008',109,4,'2026-05-22',NULL,262500.00,0.00,0.00,NULL,'Belum',NULL,NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(2001,'BYR02001',109,5,'2026-02-15',NULL,262500.00,0.00,0.00,NULL,'Belum','Angsuran ke-5',NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(2002,'BYR02002',109,6,'2026-02-28',NULL,262500.00,0.00,0.00,NULL,'Belum','Angsuran ke-6',NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(2003,'BYR02003',109,7,'2026-01-10',NULL,262500.00,0.00,0.00,NULL,'Belum','Angsuran ke-7',NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL),
(2004,'BYR02004',109,8,'2026-01-25',NULL,262500.00,0.00,0.00,NULL,'Belum','Angsuran ke-8',NULL,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL);

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

insert  into `cache`(`key`,`value`,`expiration`) values 
('laravel-cache-adsa@gmail.com|127.0.0.1','i:1;',1767846410),
('laravel-cache-adsa@gmail.com|127.0.0.1:timer','i:1767846410;',1767846410),
('laravel-cache-ibles@gmail.com|127.0.0.1','i:1;',1769131600),
('laravel-cache-ibles@gmail.com|127.0.0.1:timer','i:1769131600;',1769131600),
('laravel-cache-ibless@gmail.com|127.0.0.1','i:2;',1769131586),
('laravel-cache-ibless@gmail.com|127.0.0.1:timer','i:1769131586;',1769131586),
('laravel-cache-test@gmail.com1|127.0.0.1','i:1;',1767856701),
('laravel-cache-test@gmail.com1|127.0.0.1:timer','i:1767856701;',1767856701);

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `data_anggota` */

DROP TABLE IF EXISTS `data_anggota`;

CREATE TABLE `data_anggota` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) DEFAULT 'assets/images/profile/user-1.jpg',
  `id_anggota` varchar(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `tempat_lahir` varchar(225) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `status` enum('Belum Kawin','Kawin','Cerai Hidup','Cerai Mati','Lainnya') DEFAULT NULL,
  `departement` varchar(100) DEFAULT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `agama` varchar(50) DEFAULT NULL,
  `alamat` text NOT NULL,
  `kota` varchar(255) NOT NULL,
  `no_telp` varchar(12) DEFAULT NULL,
  `tanggal_registrasi` date NOT NULL,
  `jabatan` enum('Anggota','Pengurus') NOT NULL DEFAULT 'Anggota',
  `aktif` enum('Aktif','Non Aktif') NOT NULL DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_anggota` (`id_anggota`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_id_anggota` (`id_anggota`),
  KEY `idx_username` (`username`),
  KEY `idx_aktif` (`aktif`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `data_anggota` */

insert  into `data_anggota`(`id`,`photo`,`id_anggota`,`username`,`password`,`nama`,`jenis_kelamin`,`tempat_lahir`,`tanggal_lahir`,`status`,`departement`,`pekerjaan`,`agama`,`alamat`,`kota`,`no_telp`,`tanggal_registrasi`,`jabatan`,`aktif`,`created_at`,`updated_at`) values 
(1,'assets/images/profile/user-1.jpg','AG0001','good_joob123','$2y$12$5c/TkYViOrPKeJIH7I5FX.LuBpQCDocyKYGOz2DBonVUHn35M29S6','iam ibless','Laki-laki','sad','2025-12-29','Belum Kawin','Produksi Slitting','Lainnya','Islam','fasfaafafdaf','sad','04578987654','2026-01-09','Anggota','Aktif','2026-01-09 07:24:46','2026-01-09 07:24:46'),
(5,'anggota/anggota_1768203624.png','AG0002','adssss','$2y$12$gsdGrN0v7Khqpl2xO1nd/ONVwgtpIcq8Ou2NMQx8/I/VhBGoB.WtO','sadsss','Perempuan','sad','2026-01-09','Belum Kawin','Produksi Slitting','PNS','Islam','asda','sad','04578987654','2026-01-12','Pengurus','Aktif','2026-01-12 07:40:26','2026-01-12 07:40:26'),
(6,'assets/images/profile/user-1.jpg','AG0003','adssssadsad','$2y$12$ISMo72y3qQtebmJm/JFV5eqGRcQeVEDpDP9QPgH0Rqszqik4m/vLC','sadsssasda','Laki-laki','sad','2026-01-09','Kawin','WH','Buruh','Islam','Villa Pajajaran Permai Blok F-16','sad','04578987654','2026-01-12','Anggota','Aktif','2026-01-12 07:40:50','2026-01-21 07:31:17'),
(7,'assets/images/profile/user-1.jpg','AG0004','user@gmail.com','$2y$12$hSWSS0QO7.1JrCfd3dmlfOnvOQtMN4rriHmPneKzMvKAzKr1SX/O2','User Koperasi','Laki-laki','Jakartaaaaa','1990-01-01','Belum Kawin','Produksi Slitting','Karyawan Swasta','Islam','Jakarta','Jakarta','081234567890','2026-01-13','Pengurus','Aktif','2026-01-13 16:49:08','2026-01-21 03:47:27');

/*Table structure for table `data_barang` */

DROP TABLE IF EXISTS `data_barang`;

CREATE TABLE `data_barang` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nama_barang` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jumlah` int(11) NOT NULL DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `data_barang` */

insert  into `data_barang`(`id`,`nama_barang`,`type`,`merk`,`harga`,`jumlah`,`keterangan`,`created_at`,`updated_at`) values 
(1,'sadasda','Uang','sada',111111.00,6,'s','2026-01-09 07:06:14','2026-01-09 07:06:31');

/*Table structure for table `data_kas` */

DROP TABLE IF EXISTS `data_kas`;

CREATE TABLE `data_kas` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nama_kas` varchar(225) NOT NULL,
  `aktif` enum('Y','T') NOT NULL DEFAULT 'Y',
  `simpanan` enum('Y','T') NOT NULL DEFAULT 'Y',
  `penarikan` enum('Y','T') NOT NULL DEFAULT 'Y',
  `pinjaman` enum('Y','T') NOT NULL DEFAULT 'Y',
  `angsuran` enum('Y','T') NOT NULL DEFAULT 'Y',
  `pemasukan_kas` enum('Y','T') NOT NULL DEFAULT 'Y',
  `pengeluaran_kas` enum('Y','T') NOT NULL DEFAULT 'Y',
  `transfer_kas` enum('Y','T') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `data_kas` */

insert  into `data_kas`(`id`,`nama_kas`,`aktif`,`simpanan`,`penarikan`,`pinjaman`,`angsuran`,`pemasukan_kas`,`pengeluaran_kas`,`transfer_kas`,`created_at`,`updated_at`) values 
(1,'Kas Tunai','Y','Y','Y','Y','Y','Y','Y','T','2026-01-09 06:55:07','2026-01-09 06:55:16'),
(3,'zzz','Y','Y','Y','Y','Y','Y','Y','Y','2026-01-12 02:42:46','2026-01-12 02:42:46');

/*Table structure for table `data_pengguna` */

DROP TABLE IF EXISTS `data_pengguna`;

CREATE TABLE `data_pengguna` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('admin','operator','pinjaman') NOT NULL DEFAULT 'operator',
  `status` enum('Y','N') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `data_pengguna_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `data_pengguna` */

insert  into `data_pengguna`(`id`,`username`,`password`,`level`,`status`,`created_at`,`updated_at`) values 
(1,'f','$2y$12$Kg7zWlt8H6xFkcbVwUj/SeBgY70vkd13JcKgW43nzU9tTMDvfKA1e','operator','Y','2026-01-09 08:32:30','2026-01-09 08:33:29');

/*Table structure for table `detail_bayar_angsuran` */

DROP TABLE IF EXISTS `detail_bayar_angsuran`;

CREATE TABLE `detail_bayar_angsuran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_bayar` varchar(20) NOT NULL COMMENT 'TBY00001, TBY00002, dst',
  `bayar_angsuran_id` bigint(20) unsigned NOT NULL COMMENT 'Referensi ke jadwal angsuran',
  `pinjaman_id` bigint(20) unsigned NOT NULL COMMENT 'Referensi ke pinjaman',
  `angsuran_ke` int(11) NOT NULL COMMENT 'Angsuran ke berapa',
  `tanggal_bayar` datetime NOT NULL COMMENT 'Tanggal aktual pembayaran',
  `jumlah_bayar` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Jumlah yang dibayarkan',
  `denda` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Denda keterlambatan',
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'jumlah_bayar + denda',
  `ke_kas_id` bigint(20) DEFAULT NULL COMMENT 'Masuk ke kas mana',
  `keterangan` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User yang memproses pembayaran',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `detail_bayar_angsuran_kode_bayar_unique` (`kode_bayar`),
  KEY `detail_bayar_angsuran_bayar_angsuran_id_foreign` (`bayar_angsuran_id`),
  KEY `detail_bayar_angsuran_pinjaman_id_foreign` (`pinjaman_id`),
  KEY `detail_bayar_angsuran_ke_kas_id_foreign` (`ke_kas_id`),
  KEY `detail_bayar_angsuran_user_id_foreign` (`user_id`),
  KEY `detail_bayar_angsuran_kode_bayar_index` (`kode_bayar`),
  KEY `detail_bayar_angsuran_tanggal_bayar_index` (`tanggal_bayar`),
  KEY `detail_bayar_angsuran_angsuran_ke_index` (`angsuran_ke`),
  CONSTRAINT `detail_bayar_angsuran_bayar_angsuran_id_foreign` FOREIGN KEY (`bayar_angsuran_id`) REFERENCES `bayar_angsuran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_bayar_angsuran_ke_kas_id_foreign` FOREIGN KEY (`ke_kas_id`) REFERENCES `data_kas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `detail_bayar_angsuran_pinjaman_id_foreign` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjaman` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detail_bayar_angsuran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `detail_bayar_angsuran` */

insert  into `detail_bayar_angsuran`(`id`,`kode_bayar`,`bayar_angsuran_id`,`pinjaman_id`,`angsuran_ke`,`tanggal_bayar`,`jumlah_bayar`,`denda`,`total_bayar`,`ke_kas_id`,`keterangan`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(17,'TBY00001',1001,108,1,'2026-01-22 09:04:00',787500.00,0.00,787500.00,1,NULL,1,'2026-01-22 09:04:55','2026-01-22 09:04:55',NULL),
(18,'TBY00002',1002,108,2,'2026-01-22 09:05:00',787500.00,0.00,787500.00,1,NULL,1,'2026-01-22 09:05:55','2026-01-22 09:05:55',NULL),
(19,'TBY00003',1003,108,3,'2026-01-22 09:05:00',787500.00,0.00,787500.00,1,NULL,1,'2026-01-22 09:06:01','2026-01-22 09:06:01',NULL),
(20,'TBY00004',1004,108,4,'2026-01-22 09:06:00',787500.00,0.00,787500.00,3,NULL,1,'2026-01-22 09:06:07','2026-01-22 09:06:07',NULL);

/*Table structure for table `detail_pinjaman_lunas` */

DROP TABLE IF EXISTS `detail_pinjaman_lunas`;

CREATE TABLE `detail_pinjaman_lunas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_bayar` varchar(20) NOT NULL COMMENT 'TBY00001, TBY00002 - Auto Generate',
  `pinjaman_lunas_id` bigint(20) unsigned NOT NULL COMMENT 'FK ke pinjaman_lunas',
  `angsuran_ke` int(11) NOT NULL COMMENT 'Angsuran ke berapa (1,2,3...)',
  `tanggal_bayar` datetime NOT NULL COMMENT 'Tanggal pembayaran',
  `angsuran_pokok` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Angsuran pokok per bulan',
  `biaya_bunga` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya bunga per bulan',
  `biaya_admin` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya admin (jika ada)',
  `jumlah_angsuran` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total angsuran (pokok+bunga+admin)',
  `denda` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Denda keterlambatan',
  `total_bayar` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total yang dibayar (angsuran+denda)',
  `status_bayar` enum('Lunas','Belum') NOT NULL DEFAULT 'Lunas' COMMENT 'Status pembayaran',
  `user_nama` varchar(100) DEFAULT NULL COMMENT 'Nama user yang input',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `detail_pinjaman_lunas_kode_bayar_unique` (`kode_bayar`),
  KEY `detail_pinjaman_lunas_pinjaman_lunas_id_foreign` (`pinjaman_lunas_id`),
  KEY `detail_pinjaman_lunas_angsuran_ke_index` (`angsuran_ke`),
  KEY `detail_pinjaman_lunas_kode_bayar_index` (`kode_bayar`),
  CONSTRAINT `detail_pinjaman_lunas_pinjaman_lunas_id_foreign` FOREIGN KEY (`pinjaman_lunas_id`) REFERENCES `pinjaman_lunas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `detail_pinjaman_lunas` */

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `identitas_koperasi` */

DROP TABLE IF EXISTS `identitas_koperasi`;

CREATE TABLE `identitas_koperasi` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_lembaga` varchar(255) NOT NULL,
  `nama_ketua` varchar(255) NOT NULL,
  `hp_ketua` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(255) DEFAULT NULL,
  `kota` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `web` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT 'assets/images/logos/logo-placeholder.png',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `identitas_koperasi` */

insert  into `identitas_koperasi`(`id`,`nama_lembaga`,`nama_ketua`,`hp_ketua`,`alamat`,`telepon`,`kota`,`email`,`web`,`logo`,`created_at`,`updated_at`) values 
(1,'Koperasi Akeno','Riski Juanda','085212345678','AKENO MULTIMEDIA SOLUTION, Vila Bandung Indah 40393 Jawa West Java','0821-2135-5234','Bandung','master@akeno-ms.com','www.koperasi.akeno.id','assets/images/logos/logo-koperasi-1769137392.png','2026-01-23 02:52:33','2026-01-23 03:03:12');

/*Table structure for table `jenis_akun` */

DROP TABLE IF EXISTS `jenis_akun`;

CREATE TABLE `jenis_akun` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `kd_aktiva` varchar(10) DEFAULT NULL,
  `jns_transaksi` varchar(100) DEFAULT NULL,
  `akun` varchar(50) DEFAULT NULL,
  `pemasukan` enum('Y','T') DEFAULT 'Y',
  `pengeluaran` enum('Y','T') DEFAULT 'Y',
  `aktif` enum('Y','T') DEFAULT 'Y',
  `laba_rugi` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jenis_akun` */

insert  into `jenis_akun`(`id`,`kd_aktiva`,`jns_transaksi`,`akun`,`pemasukan`,`pengeluaran`,`aktif`,`laba_rugi`,`created_at`,`updated_at`) values 
(1,'001','transaksi','Aktiva','Y','Y','Y','BIAYA','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(3,'A4','Piutang Usaha','Aktiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(4,'A5','Piutang Karyawan','Aktiva','T','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(5,'A6','Pinjaman Anggota','Aktiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(6,'A7','Piutang Anggota','Aktiva','Y','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(7,'A8','Persediaan Barang','Aktiva','T','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(8,'A9','Biaya Dibayar Dimuka','Aktiva','T','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(9,'A10','Perlengkapan Usaha','Aktiva','T','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(10,'A11','Permisalan','Aktiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(11,'C','Aktiva Tetap Berwujud','Aktiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(12,'C1','Peralatan Kantor','Aktiva','T','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(13,'C2','Inventaris Kendaraan','Aktiva','T','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(14,'C3','Mesin','Aktiva','T','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(15,'C4','Aktiva Tetap Lainnya','Aktiva','Y','T','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(16,'E','Modal Pribadi','Aktiva','Y','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(17,'E1','Prive','Aktiva','Y','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(18,'F','Utang','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(19,'F1','Utang Usaha','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(20,'F4','Simpanan Sukarela','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(21,'F5','Utang Pajak','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(22,'H','Utang Jangka Panjang','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(23,'H1','Utang Bank','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(24,'H2','Obligasi','Pasiva','Y','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(25,'I','Modal','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(26,'I1','Simpanan Pokok','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 09:25:27'),
(27,'I2','Simpanan Wajib','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(28,'I3','Modal Awal','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(29,'I4','Modal Penyertaan','Pasiva','Y','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(30,'I5','Modal Sumbangan','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(31,'I6','Modal Cadangan','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(32,'J','Pendapatan','Pasiva','Y','Y','Y','PENDAPATAN','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(33,'J1','Pembayaran Angsuran','Pasiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(34,'J2','Pendapatan Lainnya','Pasiva','Y','T','Y','PENDAPATAN','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(35,'K','Beban','Aktiva','Y','Y','Y',NULL,'2026-01-22 15:40:30','2026-01-22 09:24:45'),
(36,'K2','Beban Gaji Karyawan','Aktiva','T','Y','Y','BIAYA','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(37,'K3','Pengeluaran Lainnya','Aktiva','T','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30'),
(38,'K3','Biaya Listrik dan Air','Aktiva','T','Y','Y','BIAYA','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(39,'K4','Biaya Transportasi','Aktiva','T','Y','Y','BIAYA','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(40,'K10','Biaya Lainnya','Aktiva','T','Y','Y','BIAYA','2026-01-22 15:40:30','2026-01-22 15:40:30'),
(41,'TRF','Transfer Antar Kas',NULL,'Y','Y','T',NULL,'2026-01-22 15:40:30','2026-01-22 15:40:30');

/*Table structure for table `jenis_simpanan` */

DROP TABLE IF EXISTS `jenis_simpanan`;

CREATE TABLE `jenis_simpanan` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `jenis_simpanan` varchar(100) DEFAULT NULL,
  `jumlah` int(11) DEFAULT 0,
  `tampil` enum('Y','T') DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `jenis_simpanan` */

insert  into `jenis_simpanan`(`id`,`jenis_simpanan`,`jumlah`,`tampil`,`created_at`,`updated_at`) values 
(13,'Simpanan Sukarela',0,'Y','2026-01-08 09:25:51','2026-01-08 09:26:31'),
(14,'Simpanan Pokok',100000,'Y','2026-01-08 09:26:03','2026-01-08 09:26:03'),
(15,'Simpanan Wajib',50000,'Y','2026-01-08 09:26:18','2026-01-08 09:26:18');

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

/*Table structure for table `lama_angsuran` */

DROP TABLE IF EXISTS `lama_angsuran`;

CREATE TABLE `lama_angsuran` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `lama_angsuran` int(11) NOT NULL,
  `aktif` enum('Y','T') NOT NULL DEFAULT 'Y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `lama_angsuran` */

insert  into `lama_angsuran`(`id`,`lama_angsuran`,`aktif`,`created_at`,`updated_at`) values 
(1,12,'Y','2026-01-09 07:01:31','2026-01-09 07:01:31'),
(2,4,'Y','2026-01-09 07:01:39','2026-01-09 07:01:39');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `pemasukan` */

DROP TABLE IF EXISTS `pemasukan`;

CREATE TABLE `pemasukan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `uraian` text NOT NULL,
  `untuk_kas_id` bigint(20) NOT NULL,
  `dari_akun_id` bigint(20) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pemasukan_kode_transaksi_unique` (`kode_transaksi`),
  KEY `pemasukan_untuk_kas_id_index` (`untuk_kas_id`),
  KEY `pemasukan_dari_akun_id_index` (`dari_akun_id`),
  KEY `pemasukan_kode_transaksi_index` (`kode_transaksi`),
  KEY `pemasukan_tanggal_transaksi_index` (`tanggal_transaksi`),
  KEY `pemasukan_user_id_foreign` (`user_id`),
  CONSTRAINT `pemasukan_dari_akun_id_foreign` FOREIGN KEY (`dari_akun_id`) REFERENCES `jenis_akun` (`id`),
  CONSTRAINT `pemasukan_untuk_kas_id_foreign` FOREIGN KEY (`untuk_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `pemasukan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pemasukan` */

insert  into `pemasukan`(`id`,`kode_transaksi`,`tanggal_transaksi`,`uraian`,`untuk_kas_id`,`dari_akun_id`,`jumlah`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'TKD00001','2026-01-22 08:41:00','sssss',1,34,2600000.00,1,'2026-01-22 08:41:25','2026-01-22 08:41:25',NULL);

/*Table structure for table `penarikan_tunai` */

DROP TABLE IF EXISTS `penarikan_tunai`;

CREATE TABLE `penarikan_tunai` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `jenis_simpanan_id` bigint(20) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  `dari_kas_id` bigint(20) NOT NULL,
  `nama_penarik` varchar(255) DEFAULT NULL,
  `no_identitas` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `penarikan_tunai_kode_transaksi_unique` (`kode_transaksi`),
  KEY `penarikan_tunai_anggota_id_foreign` (`anggota_id`),
  KEY `penarikan_tunai_jenis_simpanan_id_foreign` (`jenis_simpanan_id`),
  KEY `penarikan_tunai_dari_kas_id_foreign` (`dari_kas_id`),
  KEY `penarikan_tunai_user_id_foreign` (`user_id`),
  KEY `penarikan_tunai_kode_transaksi_index` (`kode_transaksi`),
  KEY `penarikan_tunai_tanggal_transaksi_index` (`tanggal_transaksi`),
  CONSTRAINT `penarikan_tunai_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `data_anggota` (`id`),
  CONSTRAINT `penarikan_tunai_dari_kas_id_foreign` FOREIGN KEY (`dari_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `penarikan_tunai_jenis_simpanan_id_foreign` FOREIGN KEY (`jenis_simpanan_id`) REFERENCES `jenis_simpanan` (`id`),
  CONSTRAINT `penarikan_tunai_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `penarikan_tunai` */

insert  into `penarikan_tunai`(`id`,`kode_transaksi`,`tanggal_transaksi`,`anggota_id`,`jenis_simpanan_id`,`jumlah`,`dari_kas_id`,`nama_penarik`,`no_identitas`,`alamat`,`keterangan`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'PNR00001','2026-01-13 02:45:00',1,14,100000.00,1,'sda','da','dsadsa','adads',1,'2026-01-13 02:45:57','2026-01-22 08:34:00','2026-01-22 08:34:00'),
(2,'PNR00002','2026-01-13 02:46:00',1,14,100000.00,1,'ads','das','da','sdad',1,'2026-01-13 02:46:45','2026-01-22 08:33:59','2026-01-22 08:33:59'),
(3,'TRK00003','2026-01-13 02:47:00',1,13,323232.00,1,'dsada','dsada','dsada','sda',1,'2026-01-13 02:47:54','2026-01-22 08:34:00','2026-01-22 08:34:00'),
(4,'TRK00004','2026-01-13 02:48:00',6,14,1000002.00,1,'dasda','dsadas','sdada','sda',1,'2026-01-13 02:48:18','2026-01-13 04:11:20','2026-01-13 04:11:20'),
(5,'TRK00005','2026-01-22 08:43:00',5,14,100000.00,1,'dasda','sada','ssssssssssss','ssssssssssss',1,'2026-01-22 08:43:32','2026-01-22 08:43:32',NULL);

/*Table structure for table `pengajuan_pinjaman` */

DROP TABLE IF EXISTS `pengajuan_pinjaman`;

CREATE TABLE `pengajuan_pinjaman` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_ajuan` varchar(20) NOT NULL,
  `tanggal_pengajuan` datetime NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `jenis_pinjaman` enum('Biasa','Darurat','Barang') NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  `lama_angsuran_id` bigint(20) NOT NULL,
  `keterangan` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=pending, 1=disetujui, 2=ditolak, 3=terlaksana, 4=batal',
  `tanggal_cair` date DEFAULT NULL,
  `alasan` text DEFAULT NULL COMMENT 'Alasan penolakan/catatan',
  `approved_by` bigint(20) unsigned DEFAULT NULL COMMENT 'Admin yang approve',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'User yang buat pengajuan',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengajuan_pinjaman_id_ajuan_unique` (`id_ajuan`),
  KEY `pengajuan_pinjaman_anggota_id_index` (`anggota_id`),
  KEY `pengajuan_pinjaman_lama_angsuran_id_index` (`lama_angsuran_id`),
  KEY `pengajuan_pinjaman_status_index` (`status`),
  KEY `pengajuan_pinjaman_tanggal_pengajuan_index` (`tanggal_pengajuan`),
  KEY `pengajuan_pinjaman_user_id_foreign` (`user_id`),
  KEY `pengajuan_pinjaman_approved_by_foreign` (`approved_by`),
  CONSTRAINT `pengajuan_pinjaman_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `data_anggota` (`id`),
  CONSTRAINT `pengajuan_pinjaman_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengajuan_pinjaman_lama_angsuran_id_foreign` FOREIGN KEY (`lama_angsuran_id`) REFERENCES `lama_angsuran` (`id`),
  CONSTRAINT `pengajuan_pinjaman_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pengajuan_pinjaman` */

insert  into `pengajuan_pinjaman`(`id`,`id_ajuan`,`tanggal_pengajuan`,`anggota_id`,`jenis_pinjaman`,`jumlah`,`lama_angsuran_id`,`keterangan`,`status`,`tanggal_cair`,`alasan`,`approved_by`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'B.26.01.001','2026-01-13 09:55:28',7,'Biasa',1000000.00,2,'1adads',2,NULL,'sad',1,2,'2026-01-13 09:55:28','2026-01-19 07:49:12','2026-01-19 07:49:12'),
(2,'G.26.01.001','2026-01-14 06:53:55',7,'Barang',3000000.00,2,'beli hp',3,'2026-01-14','gas',1,2,'2026-01-14 06:53:55','2026-01-22 09:03:09','2026-01-22 09:03:09'),
(3,'G.26.01.002','2026-01-19 03:16:32',7,'Barang',5000000.00,2,'sdad',3,'2026-01-19',NULL,1,2,'2026-01-19 03:16:32','2026-01-22 09:03:16','2026-01-22 09:03:16'),
(4,'G.26.01.003','2026-01-20 02:40:49',7,'Barang',1000000.00,2,'asd',3,'2026-01-20',NULL,1,2,'2026-01-20 02:40:49','2026-01-22 09:03:25',NULL),
(100,'T.26.01.100','2025-11-01 10:00:00',1,'Biasa',2000000.00,2,'Testing jatuh tempo - sudah lewat',3,'2025-11-01',NULL,1,1,'2026-01-21 14:54:31','2026-01-22 09:03:04','2026-01-22 09:03:04'),
(101,'G.26.01.004','2026-01-22 09:00:06',7,'Barang',3000000.00,2,'beli hp',3,'2026-01-22','oke',1,2,'2026-01-22 09:00:06','2026-01-22 09:02:59',NULL);

/*Table structure for table `pengeluaran` */

DROP TABLE IF EXISTS `pengeluaran`;

CREATE TABLE `pengeluaran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `uraian` text NOT NULL,
  `dari_kas_id` bigint(20) NOT NULL,
  `untuk_akun_id` bigint(20) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pengeluaran_kode_transaksi_unique` (`kode_transaksi`),
  KEY `pengeluaran_dari_kas_id_index` (`dari_kas_id`),
  KEY `pengeluaran_untuk_akun_id_index` (`untuk_akun_id`),
  KEY `pengeluaran_kode_transaksi_index` (`kode_transaksi`),
  KEY `pengeluaran_tanggal_transaksi_index` (`tanggal_transaksi`),
  KEY `pengeluaran_user_id_foreign` (`user_id`),
  CONSTRAINT `pengeluaran_dari_kas_id_foreign` FOREIGN KEY (`dari_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `pengeluaran_untuk_akun_id_foreign` FOREIGN KEY (`untuk_akun_id`) REFERENCES `jenis_akun` (`id`),
  CONSTRAINT `pengeluaran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pengeluaran` */

insert  into `pengeluaran`(`id`,`kode_transaksi`,`tanggal_transaksi`,`uraian`,`dari_kas_id`,`untuk_akun_id`,`jumlah`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'TKK00001','2026-01-22 08:41:00','ssssssssssss',1,40,1000000.00,1,'2026-01-22 08:41:47','2026-01-22 08:42:45',NULL);

/*Table structure for table `pinjaman` */

DROP TABLE IF EXISTS `pinjaman`;

CREATE TABLE `pinjaman` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_pinjaman` varchar(20) NOT NULL,
  `pengajuan_id` bigint(20) unsigned NOT NULL COMMENT 'Referensi ke pengajuan_pinjaman',
  `tanggal_pinjam` datetime NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `barang_id` bigint(20) DEFAULT NULL COMMENT 'Null jika pinjaman tunai',
  `jenis_pinjaman` enum('Biasa','Darurat','Barang') NOT NULL,
  `pokok_pinjaman` decimal(15,2) NOT NULL DEFAULT 0.00,
  `lama_angsuran_id` bigint(20) NOT NULL,
  `angsuran_pokok` decimal(15,2) NOT NULL,
  `bunga_persen` decimal(5,2) NOT NULL DEFAULT 5.00 COMMENT 'Persen bunga per angsuran',
  `biaya_bunga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `biaya_admin` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jumlah_angsuran` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total yang harus dibayar',
  `dari_kas_id` bigint(20) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status_lunas` enum('Belum','Lunas') NOT NULL DEFAULT 'Belum',
  `user_id` bigint(20) unsigned NOT NULL COMMENT 'User yang memproses',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `deleted_by` bigint(20) unsigned DEFAULT NULL,
  `alasan_hapus` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinjaman_kode_pinjaman_unique` (`kode_pinjaman`),
  KEY `pinjaman_pengajuan_id_foreign` (`pengajuan_id`),
  KEY `pinjaman_anggota_id_foreign` (`anggota_id`),
  KEY `pinjaman_barang_id_foreign` (`barang_id`),
  KEY `pinjaman_lama_angsuran_id_foreign` (`lama_angsuran_id`),
  KEY `pinjaman_dari_kas_id_foreign` (`dari_kas_id`),
  KEY `pinjaman_user_id_foreign` (`user_id`),
  KEY `pinjaman_kode_pinjaman_index` (`kode_pinjaman`),
  KEY `pinjaman_tanggal_pinjam_index` (`tanggal_pinjam`),
  KEY `pinjaman_status_lunas_index` (`status_lunas`),
  KEY `pinjaman_deleted_at_index` (`deleted_at`),
  KEY `pinjaman_deleted_by_foreign` (`deleted_by`),
  CONSTRAINT `pinjaman_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `data_anggota` (`id`),
  CONSTRAINT `pinjaman_barang_id_foreign` FOREIGN KEY (`barang_id`) REFERENCES `data_barang` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pinjaman_dari_kas_id_foreign` FOREIGN KEY (`dari_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `pinjaman_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pinjaman_lama_angsuran_id_foreign` FOREIGN KEY (`lama_angsuran_id`) REFERENCES `lama_angsuran` (`id`),
  CONSTRAINT `pinjaman_pengajuan_id_foreign` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_pinjaman` (`id`),
  CONSTRAINT `pinjaman_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pinjaman` */

insert  into `pinjaman`(`id`,`kode_pinjaman`,`pengajuan_id`,`tanggal_pinjam`,`anggota_id`,`barang_id`,`jenis_pinjaman`,`pokok_pinjaman`,`lama_angsuran_id`,`angsuran_pokok`,`bunga_persen`,`biaya_bunga`,`biaya_admin`,`jumlah_angsuran`,`dari_kas_id`,`keterangan`,`status_lunas`,`user_id`,`created_at`,`updated_at`,`deleted_at`,`deleted_by`,`alasan_hapus`) values 
(108,'PJ00001',101,'2025-12-24 16:02:00',7,NULL,'Barang',3000000.00,2,750000.00,5.00,37500.00,0.00,3150000.00,1,'ssss','Lunas',1,'2026-01-22 09:02:58','2026-01-22 09:06:07',NULL,NULL,NULL),
(109,'PJ00002',4,'2026-01-22 16:03:00',7,NULL,'Barang',1000000.00,2,250000.00,5.00,12500.00,0.00,1050000.00,1,NULL,'Belum',1,'2026-01-22 09:03:25','2026-01-22 09:03:25',NULL,NULL,NULL),
(110,'PJ0001',1,'2025-01-10 00:00:00',1,NULL,'Biasa',5000000.00,1,500000.00,5.00,250000.00,50000.00,575000.00,1,NULL,'Belum',1,'2026-01-22 16:54:49','2026-01-23 01:19:50','2026-01-23 01:19:50',1,NULL);

/*Table structure for table `pinjaman_lunas` */

DROP TABLE IF EXISTS `pinjaman_lunas`;

CREATE TABLE `pinjaman_lunas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_lunas` varchar(20) NOT NULL COMMENT 'TPJ00001, TPJ00002, dst - Auto Generate',
  `pinjaman_id` bigint(20) unsigned NOT NULL COMMENT 'Referensi ke tabel pinjaman',
  `tanggal_lunas` datetime NOT NULL COMMENT 'Tanggal pelunasan terakhir',
  `total_pokok` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total pokok pinjaman',
  `total_bunga` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total bunga yang dibayar',
  `total_denda` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total denda yang dibayar',
  `total_dibayar` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Total yang sudah dibayarkan',
  `lama_cicilan` int(11) NOT NULL COMMENT 'Jumlah bulan cicilan',
  `total_angsuran` int(11) NOT NULL COMMENT 'Total angsuran yang dibayar',
  `keterangan` text DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL COMMENT 'User yang validasi lunas',
  `deleted_by` bigint(20) unsigned DEFAULT NULL COMMENT 'User yang membatalkan pelunasan',
  `alasan_batal` text DEFAULT NULL COMMENT 'Alasan pembatalan pelunasan',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pinjaman_lunas_kode_lunas_unique` (`kode_lunas`),
  KEY `pinjaman_lunas_kode_lunas_index` (`kode_lunas`),
  KEY `pinjaman_lunas_tanggal_lunas_index` (`tanggal_lunas`),
  KEY `pinjaman_lunas_pinjaman_id_index` (`pinjaman_id`),
  KEY `pinjaman_lunas_user_id_foreign` (`user_id`),
  KEY `pinjaman_lunas_deleted_at_index` (`deleted_at`),
  KEY `pinjaman_lunas_deleted_by_index` (`deleted_by`),
  CONSTRAINT `pinjaman_lunas_deleted_by_foreign` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pinjaman_lunas_pinjaman_id_foreign` FOREIGN KEY (`pinjaman_id`) REFERENCES `pinjaman` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pinjaman_lunas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pinjaman_lunas` */

insert  into `pinjaman_lunas`(`id`,`kode_lunas`,`pinjaman_id`,`tanggal_lunas`,`total_pokok`,`total_bunga`,`total_denda`,`total_dibayar`,`lama_cicilan`,`total_angsuran`,`keterangan`,`user_id`,`deleted_by`,`alasan_batal`,`deleted_at`,`created_at`,`updated_at`) values 
(7,'TPJ00001',108,'2026-01-22 09:06:00',3000000.00,150000.00,0.00,3150000.00,4,4,'Validasi pelunasan pinjaman PJ00001',1,NULL,NULL,NULL,'2026-01-22 09:06:32','2026-01-22 09:06:32');

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nama` enum('admin','user') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama` (`nama`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`nama`,`created_at`,`updated_at`) values 
(1,'admin','2026-01-08 09:18:14','2026-01-08 09:18:14'),
(2,'user','2026-01-08 09:18:18','2026-01-08 09:18:18');

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values 
('aY1Gf9uSbRDNpKfM8dD8jtUV4kivrroi83rCd2TW',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMlFZMTJHZ3VUZ3hubGtnSDZhdkZZV29CZlR6Q1dHbDBONDU4NjhEOCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9pZGVudGl0YXMiO3M6NToicm91dGUiO3M6MTc6InNldHRpbmcuaWRlbnRpdGFzIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1769137458);

/*Table structure for table `setoran_tunai` */

DROP TABLE IF EXISTS `setoran_tunai`;

CREATE TABLE `setoran_tunai` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `anggota_id` bigint(20) unsigned NOT NULL,
  `jenis_simpanan_id` bigint(20) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  `untuk_kas_id` bigint(20) NOT NULL,
  `nama_penyetor` varchar(255) DEFAULT NULL,
  `no_identitas` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setoran_tunai_kode_transaksi_unique` (`kode_transaksi`),
  KEY `setoran_tunai_kode_transaksi_index` (`kode_transaksi`),
  KEY `setoran_tunai_tanggal_transaksi_index` (`tanggal_transaksi`),
  KEY `setoran_tunai_anggota_id_index` (`anggota_id`),
  KEY `setoran_tunai_jenis_simpanan_id_index` (`jenis_simpanan_id`),
  KEY `setoran_tunai_untuk_kas_id_index` (`untuk_kas_id`),
  KEY `setoran_tunai_user_id_foreign` (`user_id`),
  CONSTRAINT `setoran_tunai_anggota_id_foreign` FOREIGN KEY (`anggota_id`) REFERENCES `data_anggota` (`id`),
  CONSTRAINT `setoran_tunai_jenis_simpanan_id_foreign` FOREIGN KEY (`jenis_simpanan_id`) REFERENCES `jenis_simpanan` (`id`),
  CONSTRAINT `setoran_tunai_untuk_kas_id_foreign` FOREIGN KEY (`untuk_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `setoran_tunai_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `setoran_tunai` */

insert  into `setoran_tunai`(`id`,`kode_transaksi`,`tanggal_transaksi`,`anggota_id`,`jenis_simpanan_id`,`jumlah`,`untuk_kas_id`,`nama_penyetor`,`no_identitas`,`alamat`,`keterangan`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'TRD00001','2026-01-13 01:50:00',1,13,100000.00,1,'sada','sada','asda','sad',1,'2026-01-13 01:52:19','2026-01-22 08:33:52','2026-01-22 08:33:52'),
(2,'TRD00002','2026-01-13 02:25:00',5,14,100000.00,1,'sad',NULL,'dasda','daasd',1,'2026-01-13 02:26:10','2026-01-13 02:27:22','2026-01-13 02:27:22'),
(3,'TRD00003','2026-01-22 08:43:00',1,14,100000.00,1,'sad','sada','sssssssss','sssss',1,'2026-01-22 08:43:15','2026-01-22 08:43:15',NULL),
(4,'TRD00004','2026-01-23 01:56:00',7,14,10000000.00,1,'sad','sada','sssssss','sss',1,'2026-01-23 01:56:43','2026-01-23 01:56:43',NULL);

/*Table structure for table `suku_bunga` */

DROP TABLE IF EXISTS `suku_bunga`;

CREATE TABLE `suku_bunga` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pinjaman_bunga_tipe` enum('A','B') NOT NULL DEFAULT 'B' COMMENT 'A: Persen dikali angsuran bln, B: Persen dikali total pinjaman',
  `bg_pinjam` decimal(5,2) NOT NULL DEFAULT 5.00 COMMENT 'Suku bunga pinjaman dalam persen',
  `biaya_adm` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya administrasi dalam rupiah',
  `denda` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya denda keterlambatan dalam rupiah',
  `denda_hari` int(11) NOT NULL DEFAULT 15 COMMENT 'Tanggal jatuh tempo pembayaran setiap bulan',
  `dana_cadangan` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase dana cadangan dari SHU',
  `jasa_usaha` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase jasa usaha dari SHU',
  `jasa_anggota` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase jasa anggota dari SHU',
  `jasa_modal` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase jasa modal anggota dari SHU',
  `dana_pengurus` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase dana pengurus dari SHU',
  `dana_karyawan` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase dana karyawan dari SHU',
  `dana_pend` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase dana pendidikan dari SHU',
  `dana_sosial` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase dana sosial dari SHU',
  `pjk_pph` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Persentase pajak PPh dari SHU',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `suku_bunga` */

insert  into `suku_bunga`(`id`,`pinjaman_bunga_tipe`,`bg_pinjam`,`biaya_adm`,`denda`,`denda_hari`,`dana_cadangan`,`jasa_usaha`,`jasa_anggota`,`jasa_modal`,`dana_pengurus`,`dana_karyawan`,`dana_pend`,`dana_sosial`,`pjk_pph`,`created_at`,`updated_at`) values 
(1,'B',5.00,0.00,0.00,15,40.00,70.00,40.00,30.00,5.00,5.00,5.00,5.00,5.00,'2026-01-14 10:50:28','2026-01-20 09:06:50');

/*Table structure for table `transfer` */

DROP TABLE IF EXISTS `transfer`;

CREATE TABLE `transfer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(20) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `uraian` text NOT NULL,
  `dari_kas_id` bigint(20) NOT NULL,
  `untuk_kas_id` bigint(20) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL DEFAULT 0.00,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transfer_kode_transaksi_unique` (`kode_transaksi`),
  KEY `transfer_dari_kas_id_index` (`dari_kas_id`),
  KEY `transfer_untuk_kas_id_index` (`untuk_kas_id`),
  KEY `transfer_kode_transaksi_index` (`kode_transaksi`),
  KEY `transfer_tanggal_transaksi_index` (`tanggal_transaksi`),
  KEY `transfer_user_id_foreign` (`user_id`),
  CONSTRAINT `transfer_dari_kas_id_foreign` FOREIGN KEY (`dari_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `transfer_untuk_kas_id_foreign` FOREIGN KEY (`untuk_kas_id`) REFERENCES `data_kas` (`id`),
  CONSTRAINT `transfer_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transfer` */

insert  into `transfer`(`id`,`kode_transaksi`,`tanggal_transaksi`,`uraian`,`dari_kas_id`,`untuk_kas_id`,`jumlah`,`user_id`,`created_at`,`updated_at`,`deleted_at`) values 
(1,'TRF00001','2026-01-22 08:41:00','sssssssss',3,3,2000000.00,1,'2026-01-22 08:42:04','2026-01-22 08:42:57',NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`role_id`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(1,'Admin Koperasi',1,'admin@gmail.com',NULL,'$2y$12$rK0GcJJ6xgqWxbVLCIIcwOLi0sLJ5qEdn.fJKXlicKjoWN5tDIC6q',NULL,'2026-01-08 03:05:11','2026-01-08 03:05:11'),
(2,'User Koperasi',2,'user@gmail.com',NULL,'$2y$12$hSWSS0QO7.1JrCfd3dmlfOnvOQtMN4rriHmPneKzMvKAzKr1SX/O2',NULL,'2026-01-08 03:19:57','2026-01-08 03:19:57');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
