-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 04:29 PM
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
-- Database: `expense_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `demo_trans`
--

CREATE TABLE `demo_trans` (
  `id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(50) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL DEFAULT curdate(),
  `user_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demo_trans`
--

INSERT INTO `demo_trans` (`id`, `username`, `type`, `category`, `description`, `amount`, `date`, `user_id`) VALUES
(1, 'Soumili Ghosh', 'expense', 'Food', 'ice cream', 100.00, '2025-08-15', 1),
(2, 'Soumili Ghosh', 'expense', 'Shopping', 'shoes', 705.00, '2025-08-15', 1),
(3, 'Soumili Ghosh', 'expense', 'Food', 'pizza', 330.00, '2025-08-14', 1);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL DEFAULT curdate(),
  `user_id` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `username`, `type`, `category`, `description`, `amount`, `date`, `user_id`) VALUES
(1, 'Soumili Ghosh', 'expense', 'Shopping ', 'Shoes', 705.00, '2025-08-15', 1),
(2, 'Soumili Ghosh', 'expense', 'Food', 'pizza', 330.00, '2025-08-14', 1),
(3, 'Soumili Ghosh', 'expense', 'Transport', 'Going to college', 120.00, '2025-08-16', 1),
(4, 'Soumili Ghosh', 'expense', 'Bills', 'electric bill', 5000.00, '2025-08-13', 1),
(8, 'Soumili Ghosh', 'expense', 'Health', 'medicine', 1000.00, '2025-08-16', 1),
(9, 'Soumili Ghosh', 'expense', 'Shopping', 'flowers and fruits', 350.00, '2025-08-16', 1),
(10, 'Soumili Ghosh', 'expense', 'Health', 'medicine', 500.00, '2025-08-09', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_verified`, `created_at`, `role`) VALUES
(1, 'Soumili Ghosh', 'ghoshsoumili58@gmail.com', '$2y$10$pM/y1NGn5lJ8laRWNOKqMecHwRVWaGtW0ZcPqfupWngzW5ylKnFN.', 1, '2025-07-29 04:48:12', 'user'),
(2, 'Pousali ghosh', 'pousali@gmail.com', '$2y$10$bcNT6FYhyxuGB0XU2XTpHuqs3D9SXvewQDYi3FVusoxxg7qorJV2u', 1, '2025-07-29 04:54:02', 'user'),
(5, 'admin', 'admin@protonmail.com', '$2y$10$XIvqUFYjEuPr1BMgulbE9.PKSDPiTkbYDMSKmkrD8crBQPh33697e', 1, '2025-08-03 07:58:43', 'admin'),
(6, 'Abhijnan Mallick', 'abhijnanmallick@gmail.com', '$2y$10$LuAMhMd3V.Ql.gkXkQur/ez1tA1m.YnM9/22cnEbWH0a4jiBDshQe', 1, '2025-08-16 07:31:01', 'user'),
(7, 'Biswajit', 'bh97.mail@gmail.com', '$2y$10$8/pt2u.ATdL0kRyMeORS9OfgEnH2fL1saCGWm3dsjqNMGzZ.8.5vi', 1, '2025-08-24 12:40:07', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `demo_trans`
--
ALTER TABLE `demo_trans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
