-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2024 at 05:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amfc`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderprod`
--

CREATE TABLE `orderprod` (
  `ORDER_ID` int(9) NOT NULL,
  `USER_ID` int(5) NOT NULL,
  `PROD_ID` int(5) NOT NULL,
  `GROUP_ID` int(9) NOT NULL,
  `ORDER_QUANTITY` int(9) NOT NULL,
  `ORDER_STATUS` int(2) NOT NULL,
  `ORDER_TOTALAMOUNT` double(9,2) NOT NULL,
  `ORDER_DATETIME` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderprod`
--

INSERT INTO `orderprod` (`ORDER_ID`, `USER_ID`, `PROD_ID`, `GROUP_ID`, `ORDER_QUANTITY`, `ORDER_STATUS`, `ORDER_TOTALAMOUNT`, `ORDER_DATETIME`) VALUES
(1, 30002, 20001, 1, 1, 1, 99.00, '2024-06-05 03:20:01'),
(3, 30002, 20003, 1, 1, 1, 50.00, '2024-06-05 03:20:01'),
(4, 30002, 20003, 2, 1, 1, 50.00, '2024-06-05 05:38:23'),
(5, 30002, 20002, 3, 1, 1, 99.00, '2024-06-05 05:50:47'),
(8, 30002, 20001, 4, 1, 1, 99.00, '2024-06-05 08:50:28'),
(9, 30002, 20002, 4, 1, 1, 99.00, '2024-06-05 08:50:28'),
(13, 30002, 20001, 5, 1, 1, 99.00, '2024-06-11 11:48:33'),
(14, 30002, 20002, 5, 1, 1, 99.00, '2024-06-11 11:48:33'),
(15, 30002, 20001, 6, 1, 1, 99.00, '2024-06-11 11:57:21'),
(16, 30002, 20002, 6, 1, 1, 99.00, '2024-06-11 11:57:21'),
(17, 30002, 20001, 7, 1, 1, 99.00, '2024-06-11 12:00:31'),
(18, 30002, 20002, 7, 1, 1, 99.00, '2024-06-11 12:00:31'),
(19, 30002, 20001, 8, 1, 1, 99.00, '2024-06-11 12:05:22'),
(20, 30002, 20004, 8, 1, 1, 99.00, '2024-06-11 12:05:22'),
(28, 30003, 20002, 9, 1, 1, 99.00, '2024-06-11 12:58:13'),
(29, 30003, 20003, 9, 1, 1, 50.00, '2024-06-11 12:58:13'),
(30, 30003, 20001, 10, 1, 1, 99.00, '2024-06-11 13:00:15'),
(31, 30003, 20004, 10, 1, 1, 99.00, '2024-06-11 13:00:15'),
(32, 30002, 20001, 11, 1, 1, 99.00, '2024-06-17 01:29:48'),
(33, 30002, 20002, 11, 1, 1, 99.00, '2024-06-17 01:29:48'),
(34, 30002, 20001, 12, 1, 1, 99.00, '2024-06-17 01:33:39'),
(35, 30002, 20003, 13, 1, 1, 50.00, '2024-06-17 01:44:28'),
(36, 30002, 20004, 13, 1, 1, 99.00, '2024-06-17 01:44:28'),
(37, 30002, 20004, 14, 1, 1, 99.00, '2024-06-17 01:52:11'),
(38, 30002, 20002, 14, 1, 1, 99.00, '2024-06-17 01:52:11'),
(39, 30002, 20001, 15, 1, 1, 99.00, '2024-06-17 01:57:14'),
(40, 30002, 20004, 15, 1, 1, 99.00, '2024-06-17 01:57:14'),
(41, 30002, 20001, 16, 1, 1, 99.00, '2024-06-17 01:58:45'),
(42, 30002, 20002, 16, 1, 1, 99.00, '2024-06-17 01:58:45'),
(43, 30002, 20003, 16, 1, 1, 50.00, '2024-06-17 01:58:45'),
(44, 30002, 20004, 16, 1, 1, 99.00, '2024-06-17 01:58:45'),
(45, 30002, 20002, 17, 2, 1, 198.00, '2024-06-17 02:18:37'),
(46, 30002, 20001, 17, 2, 1, 198.00, '2024-06-17 02:18:37'),
(47, 30002, 20003, 18, 2, 1, 100.00, '2024-06-17 02:29:49'),
(48, 30002, 20001, 18, 1, 1, 99.00, '2024-06-17 02:29:49'),
(49, 30002, 20002, 18, 2, 1, 198.00, '2024-06-17 02:29:49'),
(50, 30003, 20001, 19, 2, 1, 198.00, '2024-06-17 02:30:33'),
(51, 30003, 20003, 19, 2, 1, 100.00, '2024-06-17 02:30:33'),
(55, 30003, 20002, 20, 1, 1, 99.00, '2024-06-17 03:34:31'),
(56, 30003, 20003, 20, 1, 1, 50.00, '2024-06-17 03:34:31'),
(57, 30003, 20004, 20, 1, 1, 99.00, '2024-06-17 03:34:31'),
(58, 30003, 20006, 21, 2, 1, 198.00, '2024-06-17 03:40:23');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `PAY_ID` int(9) NOT NULL,
  `ORDER_ID` int(9) NOT NULL,
  `GROUP_ID` int(9) NOT NULL,
  `PAY_AMOUNT` double(9,2) NOT NULL,
  `PAY_METHOD` int(2) NOT NULL,
  `PAY_STATUS` int(2) NOT NULL,
  `PAY_DATETIME` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`PAY_ID`, `ORDER_ID`, `GROUP_ID`, `PAY_AMOUNT`, `PAY_METHOD`, `PAY_STATUS`, `PAY_DATETIME`) VALUES
(1, 1, 1, 149.00, 1, 1, '2024-06-05 03:20:01'),
(2, 3, 1, 149.00, 1, 1, '2024-06-05 03:20:01'),
(3, 4, 2, 50.00, 1, 1, '2024-06-05 05:38:23'),
(4, 5, 3, 99.00, 1, 1, '2024-06-05 05:50:47'),
(7, 8, 4, 198.00, 1, 1, '2024-06-05 08:50:28'),
(8, 9, 4, 198.00, 1, 1, '2024-06-05 08:50:28'),
(9, 13, 5, 198.00, 1, 1, '2024-06-11 11:56:32'),
(11, 14, 5, 198.00, 1, 1, '2024-06-11 11:50:17'),
(12, 16, 6, 198.00, 1, 1, '2024-06-11 11:57:21'),
(13, 15, 6, 198.00, 1, 1, '2024-06-11 12:00:27'),
(14, 17, 7, 198.00, 1, 1, '2024-06-11 12:00:31'),
(15, 18, 7, 198.00, 1, 1, '2024-06-11 12:00:31'),
(16, 19, 8, 198.00, 1, 1, '2024-06-11 12:05:22'),
(17, 20, 8, 198.00, 1, 1, '2024-06-11 12:05:22'),
(23, 28, 9, 149.00, 2, 1, '2024-06-11 12:58:13'),
(24, 29, 9, 149.00, 2, 1, '2024-06-11 12:58:13'),
(25, 30, 10, 198.00, 1, 1, '2024-06-11 13:00:15'),
(26, 31, 10, 198.00, 1, 1, '2024-06-11 13:00:15'),
(27, 32, 11, 198.00, 2, 1, '2024-06-17 01:29:48'),
(28, 33, 11, 198.00, 2, 1, '2024-06-17 01:29:48'),
(29, 34, 12, 129.00, 2, 1, '2024-06-17 01:33:39'),
(30, 35, 13, 179.00, 2, 1, '2024-06-17 01:44:28'),
(31, 36, 13, 179.00, 2, 1, '2024-06-17 01:44:28'),
(32, 37, 14, 228.00, 1, 1, '2024-06-17 01:54:20'),
(33, 38, 14, 228.00, 1, 1, '2024-06-17 01:54:24'),
(34, 39, 15, 228.00, 1, 1, '2024-06-17 01:57:14'),
(35, 40, 15, 228.00, 1, 1, '2024-06-17 01:57:14'),
(36, 41, 16, 377.00, 1, 1, '2024-06-17 01:58:45'),
(37, 42, 16, 377.00, 1, 1, '2024-06-17 01:58:45'),
(38, 43, 16, 377.00, 1, 1, '2024-06-17 01:58:45'),
(39, 44, 16, 377.00, 1, 1, '2024-06-17 01:58:45'),
(40, 45, 17, 426.00, 2, 1, '2024-06-17 02:18:37'),
(41, 46, 17, 426.00, 2, 1, '2024-06-17 02:18:37'),
(42, 47, 18, 427.00, 2, 1, '2024-06-17 02:29:49'),
(43, 48, 18, 427.00, 2, 1, '2024-06-17 02:29:49'),
(44, 49, 18, 427.00, 2, 1, '2024-06-17 02:29:49'),
(45, 50, 19, 328.00, 2, 1, '2024-06-17 02:30:33'),
(46, 51, 19, 328.00, 2, 1, '2024-06-17 02:30:33'),
(47, 52, 20, 129.00, 1, 2, '2024-06-17 02:41:34'),
(48, 55, 20, 278.00, 1, 1, '2024-06-17 03:34:31'),
(49, 56, 20, 278.00, 1, 1, '2024-06-17 03:34:31'),
(50, 57, 20, 278.00, 1, 1, '2024-06-17 03:34:31'),
(51, 58, 21, 228.00, 2, 1, '2024-06-17 03:40:23');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `PROD_ID` int(9) NOT NULL,
  `PROD_NAME` varchar(30) NOT NULL,
  `PROD_PRICE` double(9,2) NOT NULL,
  `PROD_AVAIL` int(2) NOT NULL,
  `PROD_CATEGORY` varchar(30) NOT NULL,
  `PROD_DATETIME` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`PROD_ID`, `PROD_NAME`, `PROD_PRICE`, `PROD_AVAIL`, `PROD_CATEGORY`, `PROD_DATETIME`) VALUES
(20001, 'Roast Beef', 99.00, 1, 'Dish', '2024-06-17 03:04:40'),
(20002, 'Hamonado', 99.00, 1, 'Dish', '2024-06-17 03:04:45'),
(20003, 'Halo Halo', 50.00, 1, 'Dessert', '2024-06-17 03:04:50'),
(20004, 'Chicken w/ Mushroom', 99.00, 1, 'Dish', '2024-06-17 03:04:55');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `USER_ID` int(9) NOT NULL,
  `USER_FNAME` varchar(30) NOT NULL,
  `USER_LNAME` varchar(30) NOT NULL,
  `USER_EMAIL` varchar(30) NOT NULL,
  `USER_PASS` varchar(256) NOT NULL,
  `USER_ADDRESS` varchar(255) NOT NULL,
  `USER_PHONE` varchar(11) DEFAULT NULL,
  `USER_LEVEL` int(2) NOT NULL,
  `USER_DATETIME` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`USER_ID`, `USER_FNAME`, `USER_LNAME`, `USER_EMAIL`, `USER_PASS`, `USER_ADDRESS`, `USER_PHONE`, `USER_LEVEL`, `USER_DATETIME`) VALUES
(30001, 'Admin', 'Admin', 'admin@gmail.com', '$2y$10$DibPeYM5.6dpYQa6H4EFDeNc6yFxKJuXfdCjzHZzTZtBOjKVBq6ma', '', '09123456789', 1, '2024-05-31 14:14:51'),
(30002, 'Luigi', 'Clavio', 'luigi@gmail.com', '$2y$10$C1hZ.wRLnwDCpkRUAcAbM.DdLLuSkmzK9DE3KTE.rRNVV0ZfR4lGC', '356 Sevilla St., Atlag, Malolos, Bulacan', '09566295058', 2, '2024-05-31 15:01:57'),
(30003, 'Yoshie', 'Ochiai', 'yoshie@gmail.com', '$2y$10$oNBjZp4qHpFxI93wl0sizuUQkEKVt5GnIXE1vIqdS.Rs.0hw6pnY2', '355 Sevilla St., Atlag, Malolos, Bulacan', '09566295059', 2, '2024-05-31 14:11:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderprod`
--
ALTER TABLE `orderprod`
  ADD PRIMARY KEY (`ORDER_ID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PAY_ID`),
  ADD KEY `ORDER_ID` (`ORDER_ID`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`PROD_ID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`USER_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderprod`
--
ALTER TABLE `orderprod`
  MODIFY `ORDER_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `PAY_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `PROD_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20005;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `USER_ID` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30004;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
