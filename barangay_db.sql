-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2026 at 05:13 PM
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
-- Database: `barangay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `status` enum('scheduled','completed') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `user_id`, `appointment_date`, `purpose`, `status`) VALUES
(1, 3, '2026-03-07 15:10:00', 'Barangay Residency Verification', 'scheduled'),
(2, 4, '2026-03-07 15:30:00', 'Barangay Residency Verification', 'scheduled'),
(3, 4, '2026-03-07 15:30:00', 'Barangay Residency Verification', 'scheduled'),
(4, 5, '2026-03-24 20:43:00', 'Barangay Residency Verification', 'scheduled'),
(5, 10, '2026-04-04 21:46:00', 'Barangay Residency Verification', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `complainant_id` int(11) DEFAULT NULL,
  `assigned_staff_id` int(11) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `staff_comment` text DEFAULT NULL,
  `status` enum('Pending','In Progress','Resolved') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`complaint_id`, `complainant_id`, `assigned_staff_id`, `subject`, `description`, `staff_comment`, `status`, `created_at`) VALUES
(1, 2, 4, 'Ace Azcona sa Qith\'s Dorm', 'Banha kaayu sir, permig ungol kag lulu, bahog utot sir kay bulan na way libang2', 'Okay na sir ngayo daw sya pasensya.', 'Resolved', '2026-03-07 08:26:30'),
(2, 2, NULL, 'Rode', 'sigeg tagay banha kaayu rba sir tas wa nay limpyo iyang lote hugaw way panilhig', NULL, 'Pending', '2026-03-22 05:38:11'),
(3, 2, NULL, 'LJ Saavedra', 'Sag asa mo butang basiwa sa coke daghan nag case diri nanga tibulaag kay sag asa ra neya e butang, sahay sa dalan pana.', NULL, 'Pending', '2026-03-22 15:35:56');

-- --------------------------------------------------------

--
-- Table structure for table `developer_profile`
--

CREATE TABLE `developer_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `developer_profile`
--

INSERT INTO `developer_profile` (`id`, `user_id`, `name`, `email`, `address`, `about`, `image`) VALUES
(1, 4, 'Johnie Niel Derubio', 'johniedy2003@gmail.com', 'Aguada, Recto St. Ozamiz City', 'Continue studying in BS information Technology at Northwestern Mindanao State College of Science and Technology', 'dev.png');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `log_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`log_id`, `user_id`, `action`, `log_time`) VALUES
(1, 1, 'Logged in successfully with 2FA', '2026-03-06 06:53:07'),
(2, 1, 'Logged in successfully with 2FA', '2026-03-06 07:29:40'),
(3, 1, 'Logged in successfully with 2FA', '2026-03-06 07:30:37'),
(4, 1, 'Logged in successfully with 2FA', '2026-03-07 06:38:37'),
(5, 1, 'Logged in successfully with 2FA', '2026-03-07 06:43:41'),
(6, 1, 'Approved user ID 2', '2026-03-07 06:43:52'),
(7, 2, 'Logged in successfully with 2FA', '2026-03-07 06:59:03'),
(8, 1, 'Logged in successfully with 2FA', '2026-03-07 07:07:35'),
(9, 1, 'Scheduled residency appointment for user ID 3', '2026-03-07 07:08:42'),
(10, 1, 'Logged in successfully with 2FA', '2026-03-07 07:12:45'),
(11, 1, 'Rejected user ID 3', '2026-03-07 07:25:03'),
(12, 1, 'Logged in successfully with 2FA', '2026-03-07 07:26:03'),
(13, 1, 'Logged in successfully with 2FA', '2026-03-07 07:28:55'),
(14, 1, 'Scheduled residency appointment for user ID 4', '2026-03-07 07:29:54'),
(15, 1, 'Scheduled residency appointment for user ID 4', '2026-03-07 07:29:58'),
(16, 1, 'Approved user ID 4', '2026-03-07 07:31:27'),
(17, 2, 'Logged in successfully with 2FA', '2026-03-07 08:05:17'),
(18, 2, 'Created a complaint', '2026-03-07 08:26:30'),
(19, 4, 'Logged in successfully with 2FA', '2026-03-07 08:27:40'),
(20, 1, 'Logged in successfully with 2FA', '2026-03-07 08:29:09'),
(21, 1, 'Assigned staff to complaint ID 1', '2026-03-07 08:30:05'),
(22, 1, 'Assigned staff to complaint ID 1', '2026-03-07 08:30:10'),
(23, 1, 'Assigned staff to complaint ID 1', '2026-03-07 08:30:51'),
(24, 4, 'Logged in successfully with 2FA', '2026-03-07 08:32:40'),
(25, 2, 'Logged in successfully with 2FA', '2026-03-07 08:34:24'),
(26, 1, 'Logged in successfully with 2FA', '2026-03-21 04:36:17'),
(27, 1, 'Logged in successfully with 2FA', '2026-03-21 05:15:30'),
(28, 4, 'Logged in successfully with 2FA', '2026-03-21 05:17:22'),
(29, 2, 'Logged in successfully with 2FA', '2026-03-21 05:20:00'),
(30, 1, 'Logged in successfully with 2FA', '2026-03-21 05:31:27'),
(31, 1, 'Assigned staff to complaint ID 1', '2026-03-21 05:31:39'),
(32, 4, 'Logged in successfully with 2FA', '2026-03-21 06:04:04'),
(33, 4, 'Resolved complaint ID 1 with comment', '2026-03-21 06:12:06'),
(34, 2, 'Logged in successfully with 2FA', '2026-03-21 06:12:51'),
(35, 4, 'Logged in successfully with 2FA', '2026-03-21 06:18:24'),
(36, 2, 'Logged in successfully with 2FA', '2026-03-21 06:19:19'),
(37, 1, 'Logged in successfully with 2FA', '2026-03-21 06:25:23'),
(38, 1, 'Logged in successfully with 2FA', '2026-03-22 04:32:25'),
(39, 2, 'Logged in successfully with 2FA', '2026-03-22 04:59:51'),
(40, 2, 'Logged in successfully with 2FA', '2026-03-22 05:01:12'),
(41, 1, 'Logged in successfully with 2FA', '2026-03-22 05:05:57'),
(42, 1, 'Logged in successfully with 2FA', '2026-03-22 05:35:12'),
(43, 2, 'Logged in successfully with 2FA', '2026-03-22 05:37:13'),
(44, 2, 'Created a complaint', '2026-03-22 05:38:11'),
(45, 2, 'Logged in successfully with 2FA', '2026-03-22 15:34:37'),
(46, 2, 'Created a complaint', '2026-03-22 15:35:56'),
(47, 1, 'Logged in successfully with 2FA', '2026-03-22 15:41:34'),
(48, 1, 'Logged in successfully with 2FA', '2026-03-24 12:39:33'),
(49, 1, 'Logged in successfully with 2FA', '2026-03-24 12:42:18'),
(50, 1, 'Scheduled residency appointment for user ID 5', '2026-03-24 12:42:53'),
(51, 1, 'Rejected user ID 5', '2026-03-24 12:43:19'),
(52, 1, 'Logged in successfully with 2FA', '2026-03-24 13:56:14'),
(53, 2, 'Logged in successfully with 2FA', '2026-03-24 14:49:24'),
(54, 4, 'Logged in successfully with 2FA', '2026-03-24 14:50:31'),
(55, 2, 'Logged in successfully with 2FA', '2026-03-24 14:52:31'),
(56, 4, 'Logged in successfully with 2FA', '2026-03-27 13:53:41'),
(57, 1, 'Logged in successfully with 2FA', '2026-03-27 13:55:29'),
(58, 4, 'Logged in successfully with 2FA', '2026-03-27 13:58:16'),
(59, 4, 'Logged in successfully with 2FA', '2026-03-27 14:46:56'),
(60, 4, 'Logged in successfully with 2FA', '2026-03-27 14:48:17'),
(61, 4, 'Logged in successfully with 2FA', '2026-03-28 07:59:46'),
(62, 4, 'Resolved complaint ID 1 with comment', '2026-03-28 08:00:13'),
(63, 4, 'Logged in successfully with 2FA', '2026-03-28 08:32:30'),
(64, 4, 'Logged in successfully with 2FA', '2026-03-28 09:50:18'),
(65, 1, 'Logged in successfully with 2FA', '2026-03-28 10:14:48'),
(66, 4, 'Logged in successfully with 2FA', '2026-03-30 09:50:14'),
(67, 4, 'Opened staff dashboard', '2026-03-30 09:50:14'),
(68, 4, 'Opened staff dashboard', '2026-03-30 09:50:31'),
(69, 4, 'Viewed assigned complaints', '2026-03-30 09:50:33'),
(70, 4, 'Opened staff dashboard', '2026-03-30 09:50:35'),
(71, 1, 'Logged in successfully with 2FA', '2026-03-30 09:58:43'),
(72, 1, 'Logged in successfully with 2FA', '2026-04-03 08:47:37'),
(73, 1, 'Logged in successfully with 2FA', '2026-04-04 03:08:37'),
(74, 1, 'Logged in successfully with 2FA', '2026-04-04 04:00:21'),
(75, 9, 'System created default superadmin account', '2026-04-04 05:31:19'),
(76, 9, 'Logged in successfully with 2FA', '2026-04-04 05:33:21'),
(77, 9, 'Logged in successfully with 2FA', '2026-04-04 06:02:36'),
(78, 1, 'Logged in successfully with 2FA', '2026-04-04 07:02:49'),
(79, 1, 'Logged in successfully with 2FA', '2026-04-04 10:41:52'),
(80, 1, 'Approved user ID 10', '2026-04-04 13:41:45'),
(81, 1, 'Updated user ID 10', '2026-04-04 13:44:50'),
(82, 1, 'Scheduled residency appointment for user ID 10', '2026-04-04 13:45:20'),
(83, 1, 'Rejected user ID 10', '2026-04-04 13:47:44'),
(84, 1, 'Updated user ID 10', '2026-04-04 13:48:11'),
(85, 10, 'Logged in successfully with 2FA', '2026-04-04 13:50:12'),
(86, 4, 'Logged in successfully with 2FA', '2026-04-04 13:51:01'),
(87, 4, 'Opened staff dashboard', '2026-04-04 13:51:01'),
(88, 4, 'Viewed assigned complaints', '2026-04-04 13:51:06'),
(89, 4, 'Opened staff dashboard', '2026-04-04 13:51:30'),
(90, 4, 'Viewed assigned complaints', '2026-04-04 13:51:42'),
(91, 3, 'Reset password via email', '2026-04-04 14:00:42'),
(92, 10, 'Logged in successfully with 2FA', '2026-04-04 14:01:39'),
(93, 10, 'Logged in successfully with 2FA', '2026-04-04 14:02:44'),
(94, 10, 'Reset password via email', '2026-04-04 14:03:34'),
(95, 10, 'Logged in successfully with 2FA', '2026-04-04 14:04:09'),
(96, 3, 'Reset password via email', '2026-04-04 14:05:07'),
(97, 10, 'Logged in successfully with 2FA', '2026-04-04 14:05:48'),
(98, 10, 'Logged in successfully with 2FA', '2026-04-04 14:12:39'),
(99, 10, 'Reset password via email', '2026-04-04 14:13:58'),
(100, 10, 'Logged in successfully with 2FA', '2026-04-04 14:14:53'),
(101, 4, 'Logged in successfully with 2FA', '2026-04-04 14:16:06'),
(102, 4, 'Opened staff dashboard', '2026-04-04 14:16:06'),
(103, 4, 'Viewed assigned complaints', '2026-04-04 14:16:10'),
(104, 4, 'Viewed assigned complaints', '2026-04-04 14:16:15'),
(105, 4, 'Opened staff dashboard', '2026-04-04 14:16:17'),
(106, 4, 'Opened staff dashboard', '2026-04-04 14:16:19'),
(107, 4, 'Viewed assigned complaints', '2026-04-04 14:16:20'),
(108, 4, 'Opened staff dashboard', '2026-04-04 14:16:21'),
(109, 10, 'Logged in successfully with 2FA', '2026-04-04 14:16:50'),
(110, 1, 'Logged in successfully with 2FA', '2026-04-04 14:17:33'),
(111, 1, 'Updated user ID 3', '2026-04-04 14:23:14'),
(112, 1, 'Updated user ID 3', '2026-04-04 14:23:31'),
(113, 1, 'Verified residency for user ID 3', '2026-04-04 14:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `reset_token`, `reset_expiry`) VALUES
(1, 1, NULL, NULL),
(2, 2, NULL, NULL),
(4, 4, 'a2ca502c274c3838d5108e93b3d80c7f5448decc04f500d8c0179508fe480a12b4e8e2870b51a98a2bb2818911161f7af1ad', '2026-03-28 11:02:54'),
(5, 5, NULL, NULL),
(6, 6, NULL, NULL),
(7, 7, NULL, NULL),
(8, 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `residency`
--

CREATE TABLE `residency` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('pending','verified','none') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `residency`
--

INSERT INTO `residency` (`id`, `user_id`, `status`) VALUES
(1, 1, 'verified'),
(2, 2, 'verified'),
(3, 3, 'verified'),
(4, 4, 'verified'),
(5, 5, 'pending'),
(6, 6, 'pending'),
(7, 7, 'pending'),
(8, 8, 'pending'),
(9, 9, 'verified'),
(10, 10, 'verified');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','admin','staff','complainant') NOT NULL,
  `account_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `firstname`, `lastname`, `email`, `password`, `role`, `account_status`, `created_at`) VALUES
(1, 'System', 'Administrator', 'admin@barangay.com', '$2y$10$KAMo90XDjDfAEszw8.6BAOZrFGgmH1vli0LZvHRmcyH.WZuDj2F0m', 'admin', 'approved', '2026-03-06 06:33:01'),
(2, 'Rj', 'Rj', 'argydy2003@gmail.com', '$2y$10$2SZOth.0mHdCEyfBmXqUquczRAkso6QzhQCyBerMhyPlDdlqxJBEK', 'complainant', 'approved', '2026-03-07 06:42:51'),
(3, 'Venzoy', 'Venzoy', 'rjdy2003@gmail.com', '$2y$10$ErfbD8D.jQBrQ5rDa6gpE./vo3lYrUqnGlUwzZDTNL428IBStCDdG', 'staff', 'approved', '2026-03-07 07:06:48'),
(4, 'Arjay', 'Arjay', 'johniedy2003@gmail.com', '$2y$10$Mfw08cTjdFm7vINIhCFxIuMEH4ZxndAZEA.hdW.4nYZQzVOKy43Ta', 'staff', 'approved', '2026-03-07 07:28:26'),
(5, 'Jonah', 'Derubio', 'jonahdyderubio@gmail.com', '$2y$10$VNHK0YldHmZhc0Cl3DaeguLcFP2YRWZ89eozeFXU/d3VWg12s.qey', 'complainant', 'rejected', '2026-03-22 04:57:50'),
(6, 'Louie Jay', 'Fortuna', 'louiejay.fortuna@nmsc.edu.ph', '$2y$10$etUiq6u0iEJvjAfjqfPnduAgPa63UdNtIpITwMyVE9u4rsb5nZUVy', 'complainant', 'pending', '2026-03-24 12:02:20'),
(7, 'Neil Martin', 'Molina', 'neilmartin.molina@nmsc.edu.ph', '$2y$10$RwprlWHigUmRjXtwc1LKcu65BMU4EGxEqzI68RZPkO2F9/DmV37yC', 'complainant', 'pending', '2026-03-24 12:53:12'),
(8, 'Argy', 'Derubio', 'derubiojohnie@gmail.com', '$2y$10$QcySt4FZZScXMP0u8GBCZeT0zLd6iL.9eUYE6oV5dPsjN/B7nIo6W', 'staff', 'pending', '2026-03-24 13:17:41'),
(9, 'Super', 'Admin', 'superadmin@barangay.com', '$2y$10$nJNLeJ.dzBiH7DmgD/z6oe/eFjhffW1QPGn3kAwVsLS6ihjuMUUM2', 'superadmin', 'approved', '2026-04-04 05:31:19'),
(10, 'Kennard', 'Derubio', 'kennarddyderubio@gmail.com', '$2y$10$TXDJPXMR7N0QxZZlrsAYU.P6sQJFTeZt6EF0dsbC2OWsngLVmxTeS', 'complainant', 'approved', '2026-04-04 10:39:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE `user_auth` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_auth`
--

INSERT INTO `user_auth` (`id`, `user_id`, `email_verified`, `verification_token`, `otp_code`, `otp_expiry`) VALUES
(1, 1, 1, NULL, NULL, NULL),
(2, 2, 1, NULL, '377311', '2026-03-24 15:57:13'),
(3, 3, 0, NULL, NULL, NULL),
(4, 4, 1, NULL, NULL, NULL),
(5, 5, 0, NULL, NULL, NULL),
(6, 6, 0, NULL, NULL, NULL),
(7, 7, 0, '416eb5ba3a87fb2469396648494e7064', NULL, NULL),
(8, 8, 1, NULL, NULL, NULL),
(9, 9, 1, NULL, NULL, NULL),
(10, 10, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `address`, `phone`, `about`, `profile_image`) VALUES
(1, 1, NULL, NULL, NULL, NULL),
(2, 2, NULL, NULL, NULL, NULL),
(3, 3, NULL, NULL, NULL, NULL),
(4, 4, 'Aguada, Recto St. Ozamiz City', '9754629572', 'Third year college student at Northwestern Mindanao State College of Science and Technology.', 'dev.png'),
(5, 5, NULL, NULL, NULL, NULL),
(6, 6, NULL, NULL, NULL, NULL),
(7, 7, NULL, NULL, NULL, NULL),
(8, 8, NULL, NULL, NULL, NULL),
(9, 9, NULL, NULL, NULL, NULL),
(10, 10, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD KEY `complainant_id` (`complainant_id`),
  ADD KEY `assigned_staff_id` (`assigned_staff_id`);

--
-- Indexes for table `developer_profile`
--
ALTER TABLE `developer_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `residency`
--
ALTER TABLE `residency`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `developer_profile`
--
ALTER TABLE `developer_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `residency`
--
ALTER TABLE `residency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_auth`
--
ALTER TABLE `user_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`complainant_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `developer_profile`
--
ALTER TABLE `developer_profile`
  ADD CONSTRAINT `developer_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `residency`
--
ALTER TABLE `residency`
  ADD CONSTRAINT `residency_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD CONSTRAINT `user_auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
