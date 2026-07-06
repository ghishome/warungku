-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 06, 2026 at 08:05 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db-warungku`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `harga_beli` int NOT NULL,
  `harga_jual` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `stok_minimum` int NOT NULL DEFAULT '5',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `nama_barang`, `satuan`, `harga_beli`, `harga_jual`, `stok`, `stok_minimum`, `created_at`) VALUES
(2, 'Minyak Goreng Sania 1L', 'Pouch', 14000, 17500, 12, 4, '2026-06-20 13:59:01'),
(3, 'Beras Ramos 5kg', 'Karung', 60000, 72000, 2, 2, '2026-06-20 13:59:01'),
(4, 'coca cola', 'pcs', 4000, 5000, 40, 5, '2026-06-20 14:08:02'),
(5, 'roma sari gandum', '1 pack', 1500, 2000, 47, 10, '2026-06-20 14:10:44'),
(6, 'malkist abon', '1 pcs', 500, 1000, 4, 5, '2026-06-20 14:11:38'),
(7, 'rokok surya 12', 'pcs', 23000, 25000, 24, 5, '2026-06-21 00:22:27'),
(8, 'tepung sajiku', 'kg', 1400, 2000, 30, 5, '2026-06-21 00:23:21'),
(9, 'ladaku', 'pcs', 500, 1000, 36, 5, '2026-06-21 00:25:10'),
(10, 'indomie goreng aceh', '1 dus', 2500, 3000, 36, 5, '2026-06-21 00:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int NOT NULL,
  `jenis` enum('masuk','keluar') NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `nominal` int NOT NULL,
  `keterangan` text,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `jenis`, `kategori`, `nominal`, `keterangan`, `tanggal`) VALUES
(7, 'masuk', 'penjualan snack', 450000, '', '2026-06-21 00:16:09'),
(8, 'masuk', 'penjualan kopi bubuk', 356000, 'alhamdulillah', '2026-06-21 00:19:33'),
(9, 'keluar', 'bayar sales sosis', 120000, '', '2026-06-21 00:20:06'),
(10, 'keluar', 'kulak surya 12', 552000, '', '2026-06-21 00:27:04'),
(11, 'masuk', 'untung hari ini', 776000, '', '2026-06-21 00:27:50'),
(12, 'keluar', 'bayar sales', 350000, 'hmmm', '2026-06-23 04:22:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(2, 'admin', '$2y$10$3eYv/O02B8ZgIExIuB6B0OKFk6CqL8R3Xl1Z/3W6aRkaD4.4nleG.', '2026-06-20 13:25:21'),
(4, 'aghis', '$2y$10$LNEo8Zxt.mX0UAgld8tZP.7/uYlHrNwegNLilL/CFOb8ESGXT2Ch6', '2026-06-20 13:38:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
