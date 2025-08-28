-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 04:31 PM
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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
