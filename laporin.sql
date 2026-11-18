-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2026 at 05:00 PM
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
-- Database: `laporin`
--

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int(11) NOT NULL,
  `nama_fasilitas` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama_fasilitas`, `lokasi`) VALUES
(13, 'AC', 'Pattimura lll 3-D'),
(14, 'Colokan', 'Distrik'),
(15, 'Kursi', 'Pattimura II 2-A'),
(16, 'Lampu', 'Mushola FTI'),
(17, 'Proyektor', 'Pattimura lll 3-D'),
(18, 'Gazebo', 'Depan jurusan Informatika'),
(19, 'Kursi Soekarno', 'Pattimura lll'),
(20, 'Water Refil', 'Poniman'),
(21, 'Kursi panjang', 'Pattimura l');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `fasilitas_id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('menunggu','diterima','diproses','selesai') NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `user_id`, `fasilitas_id`, `judul`, `deskripsi`, `foto`, `status`, `created_at`) VALUES
(13, 16, 13, 'AC tidak dingin', 'AC bocor dan tidak dingin', 'lap_6afdbb800790a.jpeg', 'selesai', '2026-11-18 14:51:44'),
(14, 16, 14, 'Colokan rusak', 'Colokan tidak konek', 'lap_6afdbc1b87256.jpeg', 'diproses', '2026-11-18 14:54:19'),
(15, 16, 15, 'Kursi Patah', 'Bagian keranjang bawah kursi copott', NULL, 'diterima', '2026-11-18 14:56:09'),
(16, 16, 16, 'Tempat wudhu gelap', 'Tempat wudhu tidak ada lampu, jadi misal malam-malam gelap wudhunya', NULL, 'selesai', '2026-11-18 14:58:16'),
(17, 16, 17, 'Proyektor error', 'Proyektor hanya menampilkan warna biru', NULL, 'menunggu', '2026-11-18 14:59:12'),
(18, 18, 18, 'Gazebo ambles', 'Kayu buat duduknya ambles', 'lap_6afdc4e36b54c.jpeg', 'diterima', '2026-11-18 15:31:47'),
(19, 19, 19, 'Kursi fosil', 'kursi tidaak nyaman untuk belajar', NULL, 'menunggu', '2026-11-18 15:38:46'),
(20, 20, 20, 'Refil cepat habis', 'Water Refil airnya kecil karena terlalu banyak yang refil', 'lap_6afdc75091418.jpeg', 'selesai', '2026-11-18 15:42:08'),
(21, 21, 21, 'Kursi rusak', 'Kursi mirig karena kakinya patah', NULL, 'diproses', '2026-11-18 15:45:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','admin') NOT NULL DEFAULT 'mahasiswa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`, `foto`) VALUES
(16, 'Reni Aulia', 'reni1@gmail.com', '$2y$10$FWkCM4vXOZO.GQcjQ4ElE.P7dLx.qRhdVOvdqWVmTQj/uor35HSA6', 'mahasiswa', '2026-11-18 14:48:49', '6afdbdd7c5941.jpeg'),
(17, 'Admin Lapor.in', 'admin@lapor.in', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-11-18 15:07:57', NULL),
(18, 'Ury Eghy', 'ury1@gmail.com', '$2y$10$LPGwJGk5G/iImj6hylQEpOXcVwu.MgzUzK6s3H1nP3GO7Keqhdj1e', 'mahasiswa', '2026-11-18 15:23:40', NULL),
(19, 'Dilla Aceh', 'cutgus@gmail.com', '$2y$10$a/NycuZAJmCh6481SiG/w.3Jk0wWiCGaz3qJYSDxu1ersxtf692SW', 'mahasiswa', '2026-11-18 15:33:01', NULL),
(20, 'Wulan Indramayu', 'wulan@gmail.com', '$2y$10$JOm7F2sWWVgpKP5NwX8IFu64xlYuK1.1xPuGrO200mrtCgNpZK2nS', 'mahasiswa', '2026-11-18 15:39:31', NULL),
(21, 'Anisya Bekasi', 'anisya@gmail.com', '$2y$10$Ick9r43fa1tcU6DSgnSVk.RIB4PSwKE6elLi8hWk06PPI9QIrSiJy', 'mahasiswa', '2026-11-18 15:43:13', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fasilitas_id` (`fasilitas_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `laporan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_ibfk_2` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
