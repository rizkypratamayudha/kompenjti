-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 15, 2024 at 10:23 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kompenjti`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_dosen`
--

CREATE TABLE `detail_dosen` (
  `detail_dosen_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_jam_kompen`
--

CREATE TABLE `detail_jam_kompen` (
  `detail_jam_kompen_id` bigint NOT NULL,
  `jam_kompen_id` bigint DEFAULT NULL,
  `matkul_id` bigint DEFAULT NULL,
  `jam` bigint DEFAULT NULL,
  `jumlah_jam` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_kaprodi`
--

CREATE TABLE `detail_kaprodi` (
  `detail_kaprodi_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prodi_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_mahasiswa`
--

CREATE TABLE `detail_mahasiswa` (
  `detail_mahasiswa_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `angkatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prodi_id` int NOT NULL,
  `periode_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `detail_pekerjaan`
--

CREATE TABLE `detail_pekerjaan` (
  `detail_pekerjaan_id` bigint NOT NULL,
  `pekerjaan_id` bigint DEFAULT NULL,
  `jumlah_anggota` bigint DEFAULT NULL,
  `deskripsi_tugas` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jam_kompen`
--

CREATE TABLE `jam_kompen` (
  `jam_kompen_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `periode_id` bigint DEFAULT NULL,
  `akumulasi_jam` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kompetensi`
--

CREATE TABLE `kompetensi` (
  `kompetensi_id` bigint NOT NULL,
  `kompetensi_admin_id` int NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `pengalaman` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bukti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kompetensi_admin`
--

CREATE TABLE `kompetensi_admin` (
  `kompetensi_admin_id` int NOT NULL,
  `kompetensi_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kompetensi_dosen`
--

CREATE TABLE `kompetensi_dosen` (
  `kompetensi_dosen_id` int NOT NULL,
  `detail_pekerjaan_id` bigint NOT NULL,
  `kompetensi_admin_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matkul`
--

CREATE TABLE `matkul` (
  `matkul_id` bigint NOT NULL,
  `matkul_kode` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `matkul_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_10_27_132144_create_table_level', 1),
(6, '2024_10_27_132433_create_table_user', 2),
(7, '2024_10_29_013237_create_table_pending_register', 3),
(8, '2024_12_03_181656_create_jobs_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `m_level`
--

CREATE TABLE `m_level` (
  `level_id` bigint NOT NULL,
  `kode_level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `m_level`
--

INSERT INTO `m_level` (`level_id`, `kode_level`, `level_nama`, `created_at`, `updated_at`) VALUES
(1, 'ADM', 'Admin', '2024-10-29 09:30:28', '2024-10-29 09:30:28'),
(2, 'DSN', 'Dosen/Tendik', '2024-10-29 09:30:28', '2024-10-29 09:30:28'),
(3, 'MHS', 'Mahasiswa', '2024-10-29 09:30:28', '2024-10-29 09:30:28'),
(4, 'KPD', 'Kaprodi', '2024-10-29 09:30:28', '2024-10-29 09:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `m_user`
--

CREATE TABLE `m_user` (
  `user_id` bigint NOT NULL,
  `level_id` bigint DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `m_user`
--

INSERT INTO `m_user` (`user_id`, `level_id`, `username`, `nama`, `password`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', 'Gelbiasa', '$2y$12$c0Cl7XiQrebOa7Gpi0iem.Jl9NaMeuBqXCVXR.ePv96nXieDcu2D2', NULL, '2024-10-29 02:30:43', '2024-12-15 10:10:18');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `notifikasi_id` int NOT NULL,
  `user_id` bigint NOT NULL,
  `pekerjaan_id` bigint NOT NULL,
  `pesan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('dibaca','belum') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id_kap` bigint DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pekerjaan`
--

CREATE TABLE `pekerjaan` (
  `pekerjaan_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `jenis_pekerjaan` enum('teknis','pengabdian','penelitian') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pekerjaan_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_jam_kompen` bigint DEFAULT NULL,
  `status` enum('open','close','done') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `akumulasi_deadline` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengumpulan`
--

CREATE TABLE `pengumpulan` (
  `pengumpulan_id` bigint NOT NULL,
  `progres_id` bigint NOT NULL,
  `user_id` bigint NOT NULL,
  `bukti_pengumpulan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `namaoriginal` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','accept','decline') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periode`
--

CREATE TABLE `periode` (
  `periode_id` bigint NOT NULL,
  `periode_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periode`
--

INSERT INTO `periode` (`periode_id`, `periode_nama`, `created_at`, `updated_at`) VALUES
(20221, '2022/2023 Ganjil', '2024-11-22 01:58:19', '2024-11-22 01:58:19'),
(20222, '2022/2023 Genap', '2024-11-22 01:58:19', '2024-11-22 01:58:19'),
(20241, '2024/2025 Ganjil', '2024-11-22 01:58:19', '2024-11-22 01:58:19'),
(20242, '2024/2025 Genap', '2024-11-22 01:58:19', '2024-11-22 01:58:19'),
(20251, '2025/2026 Ganjil', '2024-12-15 10:13:43', '2024-12-15 10:13:43'),
(20252, '2025/2026 Genap', '2024-12-15 10:13:43', '2024-12-15 10:13:43'),
(20261, '2026/2027 Ganjil', '2024-12-15 10:14:20', '2024-12-15 10:14:20'),
(20262, '2026/2027 Genap', '2024-12-15 10:14:20', '2024-12-15 10:14:20');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `persyaratan`
--

CREATE TABLE `persyaratan` (
  `persyaratan_id` int NOT NULL,
  `detail_pekerjaan_id` bigint NOT NULL,
  `persyaratan_nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `prodi_id` int NOT NULL,
  `prodi_nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`prodi_id`, `prodi_nama`, `created_at`, `updated_at`) VALUES
(1, 'D4 Sistem Informasi Bisnis', '2024-11-21 06:58:54', '2024-11-21 06:58:54'),
(2, 'D4 Teknik Informatika', '2024-11-21 06:58:54', '2024-11-21 06:58:54'),
(3, 'D2 PPLS', '2024-11-21 06:58:54', '2024-11-21 06:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `profil`
--

CREATE TABLE `profil` (
  `profil_id` int NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint DEFAULT NULL,
  `kompetensi_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `progres`
--

CREATE TABLE `progres` (
  `progres_id` bigint NOT NULL,
  `pekerjaan_id` bigint DEFAULT NULL,
  `judul_progres` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jam_kompen` bigint DEFAULT NULL,
  `hari` int DEFAULT NULL,
  `status` enum('pending','done') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_approve_cetak`
--

CREATE TABLE `t_approve_cetak` (
  `t_approve_cetak_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `pekerjaan_id` bigint DEFAULT NULL,
  `user_id_kap` bigint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_approve_pekerjaan`
--

CREATE TABLE `t_approve_pekerjaan` (
  `t_approve_pekerjaan_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `pekerjaan_id` bigint DEFAULT NULL,
  `status` enum('belum','selesai') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_pending_cetak`
--

CREATE TABLE `t_pending_cetak` (
  `t_pending_cetak_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `pekerjaan_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_pending_pekerjaan`
--

CREATE TABLE `t_pending_pekerjaan` (
  `t_pending_pekerjaan_id` bigint NOT NULL,
  `user_id` bigint DEFAULT NULL,
  `pekerjaan_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `t_pending_register`
--

CREATE TABLE `t_pending_register` (
  `user_id` bigint NOT NULL,
  `level_id` bigint DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_hp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `angkatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prodi_id` int DEFAULT NULL,
  `periode_id` bigint DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_dosen`
--
ALTER TABLE `detail_dosen`
  ADD PRIMARY KEY (`detail_dosen_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `detail_jam_kompen`
--
ALTER TABLE `detail_jam_kompen`
  ADD PRIMARY KEY (`detail_jam_kompen_id`),
  ADD KEY `jam_kompen_id` (`jam_kompen_id`),
  ADD KEY `matkul_id` (`matkul_id`);

--
-- Indexes for table `detail_kaprodi`
--
ALTER TABLE `detail_kaprodi`
  ADD PRIMARY KEY (`detail_kaprodi_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_prodi_id` (`prodi_id`);

--
-- Indexes for table `detail_mahasiswa`
--
ALTER TABLE `detail_mahasiswa`
  ADD PRIMARY KEY (`detail_mahasiswa_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `prodi` (`prodi_id`),
  ADD KEY `fk_detailm_period` (`periode_id`);

--
-- Indexes for table `detail_pekerjaan`
--
ALTER TABLE `detail_pekerjaan`
  ADD PRIMARY KEY (`detail_pekerjaan_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jam_kompen`
--
ALTER TABLE `jam_kompen`
  ADD PRIMARY KEY (`jam_kompen_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `periode_id` (`periode_id`) USING BTREE;

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `kompetensi`
--
ALTER TABLE `kompetensi`
  ADD PRIMARY KEY (`kompetensi_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kompetensi_admin_id` (`kompetensi_admin_id`);

--
-- Indexes for table `kompetensi_admin`
--
ALTER TABLE `kompetensi_admin`
  ADD PRIMARY KEY (`kompetensi_admin_id`);

--
-- Indexes for table `kompetensi_dosen`
--
ALTER TABLE `kompetensi_dosen`
  ADD PRIMARY KEY (`kompetensi_dosen_id`),
  ADD KEY `detail_pekerjaan_id` (`detail_pekerjaan_id`),
  ADD KEY `komptensi_admin_id` (`kompetensi_admin_id`);

--
-- Indexes for table `matkul`
--
ALTER TABLE `matkul`
  ADD PRIMARY KEY (`matkul_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `m_level`
--
ALTER TABLE `m_level`
  ADD PRIMARY KEY (`level_id`);

--
-- Indexes for table `m_user`
--
ALTER TABLE `m_user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `level_id` (`level_id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`notifikasi_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_kap` (`user_id_kap`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD PRIMARY KEY (`pekerjaan_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pengumpulan`
--
ALTER TABLE `pengumpulan`
  ADD PRIMARY KEY (`pengumpulan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `progres_id` (`progres_id`);

--
-- Indexes for table `periode`
--
ALTER TABLE `periode`
  ADD PRIMARY KEY (`periode_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `persyaratan`
--
ALTER TABLE `persyaratan`
  ADD PRIMARY KEY (`persyaratan_id`),
  ADD KEY `detail_pekerjaan_id` (`detail_pekerjaan_id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`prodi_id`);

--
-- Indexes for table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`profil_id`),
  ADD KEY `fk_profil_user` (`user_id`),
  ADD KEY `fk_profil_kompetensi` (`kompetensi_id`);

--
-- Indexes for table `progres`
--
ALTER TABLE `progres`
  ADD PRIMARY KEY (`progres_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`);

--
-- Indexes for table `t_approve_cetak`
--
ALTER TABLE `t_approve_cetak`
  ADD PRIMARY KEY (`t_approve_cetak_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`),
  ADD KEY `user_id_kap` (`user_id_kap`);

--
-- Indexes for table `t_approve_pekerjaan`
--
ALTER TABLE `t_approve_pekerjaan`
  ADD PRIMARY KEY (`t_approve_pekerjaan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`);

--
-- Indexes for table `t_pending_cetak`
--
ALTER TABLE `t_pending_cetak`
  ADD PRIMARY KEY (`t_pending_cetak_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`);

--
-- Indexes for table `t_pending_pekerjaan`
--
ALTER TABLE `t_pending_pekerjaan`
  ADD PRIMARY KEY (`t_pending_pekerjaan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pekerjaan_id` (`pekerjaan_id`);

--
-- Indexes for table `t_pending_register`
--
ALTER TABLE `t_pending_register`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `level_id` (`level_id`),
  ADD KEY `prodi` (`prodi_id`),
  ADD KEY `fk_pendingreg_period` (`periode_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_dosen`
--
ALTER TABLE `detail_dosen`
  MODIFY `detail_dosen_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_jam_kompen`
--
ALTER TABLE `detail_jam_kompen`
  MODIFY `detail_jam_kompen_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_kaprodi`
--
ALTER TABLE `detail_kaprodi`
  MODIFY `detail_kaprodi_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_mahasiswa`
--
ALTER TABLE `detail_mahasiswa`
  MODIFY `detail_mahasiswa_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `detail_pekerjaan`
--
ALTER TABLE `detail_pekerjaan`
  MODIFY `detail_pekerjaan_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jam_kompen`
--
ALTER TABLE `jam_kompen`
  MODIFY `jam_kompen_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `kompetensi`
--
ALTER TABLE `kompetensi`
  MODIFY `kompetensi_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kompetensi_admin`
--
ALTER TABLE `kompetensi_admin`
  MODIFY `kompetensi_admin_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kompetensi_dosen`
--
ALTER TABLE `kompetensi_dosen`
  MODIFY `kompetensi_dosen_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matkul`
--
ALTER TABLE `matkul`
  MODIFY `matkul_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `m_level`
--
ALTER TABLE `m_level`
  MODIFY `level_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `m_user`
--
ALTER TABLE `m_user`
  MODIFY `user_id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `notifikasi_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  MODIFY `pekerjaan_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengumpulan`
--
ALTER TABLE `pengumpulan`
  MODIFY `pengumpulan_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persyaratan`
--
ALTER TABLE `persyaratan`
  MODIFY `persyaratan_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `prodi_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `profil`
--
ALTER TABLE `profil`
  MODIFY `profil_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `progres`
--
ALTER TABLE `progres`
  MODIFY `progres_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_approve_cetak`
--
ALTER TABLE `t_approve_cetak`
  MODIFY `t_approve_cetak_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_approve_pekerjaan`
--
ALTER TABLE `t_approve_pekerjaan`
  MODIFY `t_approve_pekerjaan_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_pending_cetak`
--
ALTER TABLE `t_pending_cetak`
  MODIFY `t_pending_cetak_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_pending_pekerjaan`
--
ALTER TABLE `t_pending_pekerjaan`
  MODIFY `t_pending_pekerjaan_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `t_pending_register`
--
ALTER TABLE `t_pending_register`
  MODIFY `user_id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_dosen`
--
ALTER TABLE `detail_dosen`
  ADD CONSTRAINT `detail_dosen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`);

--
-- Constraints for table `detail_jam_kompen`
--
ALTER TABLE `detail_jam_kompen`
  ADD CONSTRAINT `detail_jam_kompen_ibfk_1` FOREIGN KEY (`jam_kompen_id`) REFERENCES `jam_kompen` (`jam_kompen_id`),
  ADD CONSTRAINT `detail_jam_kompen_ibfk_2` FOREIGN KEY (`matkul_id`) REFERENCES `matkul` (`matkul_id`);

--
-- Constraints for table `detail_kaprodi`
--
ALTER TABLE `detail_kaprodi`
  ADD CONSTRAINT `detail_kaprodi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `fk_prodi_id` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`prodi_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_mahasiswa`
--
ALTER TABLE `detail_mahasiswa`
  ADD CONSTRAINT `detail_mahasiswa_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `detail_mahasiswa_ibfk_2` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`prodi_id`),
  ADD CONSTRAINT `fk_detailm_period` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`);

--
-- Constraints for table `detail_pekerjaan`
--
ALTER TABLE `detail_pekerjaan`
  ADD CONSTRAINT `detail_pekerjaan_ibfk_1` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`);

--
-- Constraints for table `jam_kompen`
--
ALTER TABLE `jam_kompen`
  ADD CONSTRAINT `fk_jam_period` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`),
  ADD CONSTRAINT `jam_kompen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `jam_kompen_ibfk_2` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`);

--
-- Constraints for table `kompetensi`
--
ALTER TABLE `kompetensi`
  ADD CONSTRAINT `kompetensi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `kompetensi_ibfk_2` FOREIGN KEY (`kompetensi_admin_id`) REFERENCES `kompetensi_admin` (`kompetensi_admin_id`);

--
-- Constraints for table `kompetensi_dosen`
--
ALTER TABLE `kompetensi_dosen`
  ADD CONSTRAINT `kompetensi_dosen_ibfk_1` FOREIGN KEY (`detail_pekerjaan_id`) REFERENCES `detail_pekerjaan` (`detail_pekerjaan_id`),
  ADD CONSTRAINT `kompetensi_dosen_ibfk_2` FOREIGN KEY (`kompetensi_admin_id`) REFERENCES `kompetensi_admin` (`kompetensi_admin_id`);

--
-- Constraints for table `m_user`
--
ALTER TABLE `m_user`
  ADD CONSTRAINT `m_user_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `m_level` (`level_id`);

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifikasi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `notifikasi_ibfk_3` FOREIGN KEY (`user_id_kap`) REFERENCES `m_user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `pekerjaan`
--
ALTER TABLE `pekerjaan`
  ADD CONSTRAINT `pekerjaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`);

--
-- Constraints for table `pengumpulan`
--
ALTER TABLE `pengumpulan`
  ADD CONSTRAINT `pengumpulan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `pengumpulan_ibfk_2` FOREIGN KEY (`progres_id`) REFERENCES `progres` (`progres_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `persyaratan`
--
ALTER TABLE `persyaratan`
  ADD CONSTRAINT `persyaratan_ibfk_1` FOREIGN KEY (`detail_pekerjaan_id`) REFERENCES `detail_pekerjaan` (`detail_pekerjaan_id`);

--
-- Constraints for table `profil`
--
ALTER TABLE `profil`
  ADD CONSTRAINT `fk_profil_kompetensi` FOREIGN KEY (`kompetensi_id`) REFERENCES `kompetensi` (`kompetensi_id`),
  ADD CONSTRAINT `fk_profil_user` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`);

--
-- Constraints for table `progres`
--
ALTER TABLE `progres`
  ADD CONSTRAINT `progres_ibfk_1` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`);

--
-- Constraints for table `t_approve_cetak`
--
ALTER TABLE `t_approve_cetak`
  ADD CONSTRAINT `t_approve_cetak_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `t_approve_cetak_ibfk_2` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`),
  ADD CONSTRAINT `t_approve_cetak_ibfk_3` FOREIGN KEY (`user_id_kap`) REFERENCES `m_user` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `t_approve_pekerjaan`
--
ALTER TABLE `t_approve_pekerjaan`
  ADD CONSTRAINT `t_approve_pekerjaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `t_approve_pekerjaan_ibfk_2` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`);

--
-- Constraints for table `t_pending_cetak`
--
ALTER TABLE `t_pending_cetak`
  ADD CONSTRAINT `t_pending_cetak_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `t_pending_cetak_ibfk_2` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`);

--
-- Constraints for table `t_pending_pekerjaan`
--
ALTER TABLE `t_pending_pekerjaan`
  ADD CONSTRAINT `t_pending_pekerjaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `m_user` (`user_id`),
  ADD CONSTRAINT `t_pending_pekerjaan_ibfk_2` FOREIGN KEY (`pekerjaan_id`) REFERENCES `pekerjaan` (`pekerjaan_id`);

--
-- Constraints for table `t_pending_register`
--
ALTER TABLE `t_pending_register`
  ADD CONSTRAINT `fk_pendingreg_period` FOREIGN KEY (`periode_id`) REFERENCES `periode` (`periode_id`),
  ADD CONSTRAINT `t_pending_register_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `m_level` (`level_id`),
  ADD CONSTRAINT `t_pending_register_ibfk_2` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`prodi_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
