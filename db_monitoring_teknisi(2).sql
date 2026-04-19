-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 08:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_monitoring_teknisi`
--

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `tugas_id` int(11) NOT NULL,
  `teknisi_id` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `tugas_id`, `teknisi_id`, `catatan`, `foto`, `tanggal_selesai`, `created_at`) VALUES
(1, 4, 1, 'Semua kamera dalam kondisi baik, lensa sudah dibersihkan. DVR berfungsi normal. Tidak ada kerusakan ditemukan.', NULL, '2026-04-10', '2026-04-11 14:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `no_hp`, `alamat`, `email`, `created_at`) VALUES
(1, 'PT. Maju Bersama', '021-1234567', 'Jl. Industri No. 10, Jakarta', 'info@majubersama.com', '2026-04-11 14:24:58'),
(2, 'Toko Elektronik Jaya', '021-2345678', 'Jl. Pasar No. 3, Bekasi', 'jaya@tokojaya.com', '2026-04-11 14:24:58'),
(3, 'Rumah Sakit Sehat', '021-3456789', 'Jl. Kesehatan No. 7, Bogor', 'admin@rssehat.com', '2026-04-11 14:24:58'),
(4, 'Mall Nusantara', '021-4567890', 'Jl. Raya Serpong No. 15, Tangerang', 'cs@mallnusantara.com', '2026-04-11 14:24:58'),
(5, 'Klien Baru', NULL, 'ci', NULL, '2026-04-17 07:49:17'),
(6, 'pt sanjaya', '081231313100000', 'kp.bojong\r\n', NULL, '2026-04-17 09:46:09');

-- --------------------------------------------------------

--
-- Table structure for table `teknisi`
--

CREATE TABLE `teknisi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `spesialisasi` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teknisi`
--

INSERT INTO `teknisi` (`id`, `user_id`, `nama`, `no_hp`, `alamat`, `spesialisasi`, `status`, `created_at`) VALUES
(1, 2, 'Budi Santoso', '081234567890', 'Jl. Merdeka No. 1, Jakarta', 'Instalasi CCTV', 'nonaktif', '2026-04-11 14:24:58'),
(2, 3, 'Andi Prasetyo', '082345678901', 'Jl. Sudirman No. 5, Tangerang', 'Maintenance & Perbaikan', 'aktif', '2026-04-11 14:24:58'),
(3, 4, 'Rizky Firmansyah', '083456789012', 'Jl. Gatot Subroto No. 9, Depok', 'Jaringan & Konfigurasi', 'aktif', '2026-04-11 14:24:58'),
(4, 5, 'matel', NULL, NULL, 'cctv', 'aktif', '2026-04-17 06:57:59'),
(6, 7, 'mufin', NULL, NULL, 'network', 'nonaktif', '2026-04-17 08:49:42');

-- --------------------------------------------------------

--
-- Table structure for table `tugas`
--

CREATE TABLE `tugas` (
  `id` int(11) NOT NULL,
  `teknisi_id` int(11) NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` enum('pending','proses','selesai','batal') NOT NULL DEFAULT 'pending',
  `prioritas` enum('rendah','sedang','tinggi') NOT NULL DEFAULT 'sedang',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tugas`
--

INSERT INTO `tugas` (`id`, `teknisi_id`, `pelanggan_id`, `judul`, `deskripsi`, `tanggal`, `status`, `prioritas`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Instalasi CCTV Gedung A', 'Pasang 8 kamera CCTV di area gedung A lantai 1 dan 2', '2026-04-12', 'pending', 'tinggi', '2026-04-11 14:24:58', '2026-04-11 14:24:58'),
(2, 2, 2, 'Perbaikan DVR Rusak', 'DVR tidak bisa merekam, perlu pengecekan dan perbaikan', '2026-04-11', 'proses', 'tinggi', '2026-04-11 14:24:58', '2026-04-11 14:24:58'),
(3, 3, 3, 'Konfigurasi Remote Access', 'Setting akses CCTV dari jarak jauh via aplikasi', '2026-04-13', 'pending', 'sedang', '2026-04-11 14:24:58', '2026-04-11 14:24:58'),
(4, 1, 4, 'Maintenance Bulanan', 'Cek kondisi semua kamera dan DVR, bersihkan lensa', '2026-04-10', 'selesai', 'rendah', '2026-04-11 14:24:58', '2026-04-11 14:24:58'),
(5, 2, 1, 'Tambah Kamera Area Parkir', 'Instalasi 4 kamera baru di area parkir basement', '2026-04-14', 'selesai', 'sedang', '2026-04-11 14:24:58', '2026-04-17 15:59:43'),
(6, 4, 5, 'Perbaikan di ci', NULL, '2026-04-17', 'proses', 'sedang', '2026-04-17 07:49:17', '2026-04-17 15:59:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teknisi') NOT NULL DEFAULT 'teknisi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(2, 'Budi Santoso', 'budi', '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'teknisi', '2026-04-11 14:24:58'),
(3, 'Andi Prasetyo', 'andi', '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'teknisi', '2026-04-11 14:24:58'),
(4, 'Rizky Firmansyah', 'rizky', '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'teknisi', '2026-04-11 14:24:58'),
(5, 'matel', 'matel48', '$2y$10$PHlVKzDFsZnB0FjfGPmL.OikbF5PeZh0hpPr8EEBLmvSJXm//aXYy', 'teknisi', '2026-04-17 06:57:59'),
(7, 'mufin', 'mufin39', '$2y$10$o9DkgrzFfl1CnDTiD7A5KeLrsgB9Q2C8sXTWOEpqph2FWGfTG5iH.', 'teknisi', '2026-04-17 08:49:42'),
(8, 'Administrator', 'admin', '$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36WQoeG6Lruj3vjPGga31lW', 'admin', '2026-04-19 05:57:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_id` (`tugas_id`),
  ADD KEY `teknisi_id` (`teknisi_id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teknisi`
--
ALTER TABLE `teknisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tugas`
--
ALTER TABLE `tugas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teknisi_id` (`teknisi_id`),
  ADD KEY `pelanggan_id` (`pelanggan_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teknisi`
--
ALTER TABLE `teknisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tugas`
--
ALTER TABLE `tugas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`teknisi_id`) REFERENCES `teknisi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teknisi`
--
ALTER TABLE `teknisi`
  ADD CONSTRAINT `teknisi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tugas`
--
ALTER TABLE `tugas`
  ADD CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`teknisi_id`) REFERENCES `teknisi` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tugas_ibfk_2` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
