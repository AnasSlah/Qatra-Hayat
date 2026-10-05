-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2026 at 06:41 PM
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
-- Database: `qatra_hayat`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin') DEFAULT 'super_admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES
(1, 'أحمد محمد علي', 'admin@qatra.com', '123', '123456', 'super_admin', '2026-06-11 18:16:19');

-- --------------------------------------------------------

--
-- Table structure for table `centers`
--

CREATE TABLE `centers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('hospital','blood_bank') DEFAULT 'hospital',
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'مفتوح'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `centers`
--

INSERT INTO `centers` (`id`, `name`, `description`, `type`, `phone`, `created_at`, `latitude`, `longitude`, `status`) VALUES
(1, 'مستشفى الشعب التعليمي', 'كويس', 'hospital', '22222222222', '2026-06-04 20:56:28', 15.50115065, 32.56007106, 'مغلق'),
(2, 'مستشفى أم درمان', '', 'blood_bank', '', '2026-06-04 20:56:28', 15.64195527, 32.48773993, 'مفتوح'),
(3, 'بنك الدم المركزي', NULL, 'hospital', NULL, '2026-06-04 20:56:28', 15.59723907, 32.52984783, 'مفتوح'),
(4, 'مستشفى بحري التعليمي', NULL, 'hospital', NULL, '2026-06-04 20:56:28', 15.62488600, 32.52823407, 'مفتوح'),
(15, 'النو', 'كعبه', 'hospital', '209333333', '2026-06-12 13:15:22', 99.99999999, 123.00000000, 'مفتوح');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `sender_name` varchar(150) NOT NULL,
  `contact_info` varchar(150) NOT NULL,
  `subject` enum('general','technical','urgent','suggestion') NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','resolved') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `user_id`, `sender_name`, `contact_info`, `subject`, `message`, `status`, `created_at`) VALUES
(1, NULL, 'سندس', '0991006752', 'urgent', 'شكرا', 'unread', '2026-06-05 23:30:07'),
(2, NULL, 'سندس', '0991006752', 'urgent', 'شكرا', 'unread', '2026-06-05 23:30:11'),
(3, NULL, 'سندس', '0991006752', 'general', 'شكرا علي الموقع الجميل', 'unread', '2026-06-05 23:41:20'),
(4, NULL, 'سندس', '0991006752', 'general', 'شكرا علي الموقع الجميل', 'unread', '2026-06-05 23:42:37'),
(5, NULL, 'انس احمد', '0999509162', 'technical', 'تمام', 'unread', '2026-06-13 20:45:42');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `donation_status` varchar(50) NOT NULL DEFAULT 'pending',
  `weight` int(11) DEFAULT NULL,
  `blood_pressure` varchar(50) DEFAULT NULL,
  `hemoglobin` varchar(20) DEFAULT NULL,
  `donation_date` date DEFAULT NULL,
  `center_id` varchar(50) DEFAULT NULL,
  `health_status` varchar(20) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `donation_status`, `weight`, `blood_pressure`, `hemoglobin`, `donation_date`, `center_id`, `health_status`, `created_at`) VALUES
(2, 11, 'successful', 79, '130/80', '14', '2026-06-13', 'omdurman', 'no', '2026-06-13 19:54:46'),
(3, 15, 'successful', 76, '130/80', '14', '2026-06-13', 'omdurman', 'no', '2026-06-13 20:44:16');

-- --------------------------------------------------------

--
-- Table structure for table `donation_requests`
--

CREATE TABLE `donation_requests` (
  `id` int(11) NOT NULL,
  `center_id` int(11) NOT NULL,
  `blood_type` varchar(10) NOT NULL,
  `case_description` text NOT NULL,
  `priority` enum('critical','warning','stable','normal') NOT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_requests`
--

INSERT INTO `donation_requests` (`id`, `center_id`, `blood_type`, `case_description`, `priority`, `status`, `created_at`) VALUES
(1, 1, 'O-', 'حالة طارئة - عملية قلب', 'critical', 'active', '2026-06-04 20:56:28'),
(2, 2, 'A+', 'تأمين فصيلة لعملية جراحية', 'warning', 'active', '2026-06-04 20:56:28'),
(3, 3, 'B+', 'تجديد المخزون الدوري', 'stable', 'active', '2026-06-04 20:56:28'),
(4, 4, 'AB+', 'تبرع روتيني لزيادة الاحتياطي', 'normal', 'active', '2026-06-04 20:56:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `member_id` varchar(20) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `national_id` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `primary_phone` varchar(20) DEFAULT NULL,
  `alternate_phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `blood_type` varchar(5) NOT NULL,
  `weight` int(11) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `chronic_diseases` varchar(255) DEFAULT 'لا يوجد',
  `profile_photo` varchar(255) DEFAULT NULL,
  `last_checkup_date` date DEFAULT NULL,
  `is_eligible` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `member_id`, `full_name`, `national_id`, `birth_date`, `primary_phone`, `alternate_phone`, `email`, `password`, `address`, `blood_type`, `weight`, `blood_pressure`, `chronic_diseases`, `profile_photo`, `last_checkup_date`, `is_eligible`, `created_at`) VALUES
(3, 'DON-003', 'خالد مصطفى', '11223344553', '1988-12-05', '0912345673', NULL, 'khaled@example.com', '123456', 'بحري', 'O+', 90, '130/85', 'لا يوجد', NULL, NULL, 1, '2026-06-11 22:54:57'),
(4, 'DON-004', 'ريم عبدالله', '11223344554', '1998-03-10', '0912345674', NULL, 'reem@example.com', '123456', 'الخرطوم', 'AB+', 58, '115/70', 'لا يوجد', NULL, NULL, 1, '2026-06-11 22:54:57'),
(5, 'DON-005', 'حاتم عبدالرحمن', '11223344555', '1985-11-25', '0912345675', NULL, 'hatem@example.com', '123456', 'أم درمان', 'O-', 78, '120/80', 'لا يوجد', NULL, NULL, 1, '2026-06-11 22:54:57'),
(6, 'DON-006', 'سعاد أحمد', NULL, NULL, '0999100', NULL, NULL, '123456', '', 'O+', 62, '118/78', '', NULL, NULL, 1, '2026-06-11 22:54:57'),
(7, 'DON-007', 'بدر الدين محمد', '11223344557', '1980-02-14', '0912345677', NULL, 'badr@example.com', '123456', 'الخرطوم', 'B+', 85, '125/82', 'لا يوجد', NULL, NULL, 1, '2026-06-11 22:54:57'),
(8, 'DON-008', 'هالة يوسف', NULL, NULL, NULL, NULL, NULL, '123456', 'بحري', 'O+', 55, '110/70', '', NULL, NULL, 1, '2026-06-11 22:54:57'),
(9, 'DON-009', 'عصام حسن', NULL, NULL, NULL, NULL, NULL, '123456', '', 'A+', 75, '120/80', '', NULL, NULL, 1, '2026-06-11 22:54:57'),
(10, 'DON-010', 'شيماء طارق', '11223344560', '1997-10-05', '0912345680', NULL, 'shimaa@example.com', '123456', 'بحري', 'A+', 60, '115/75', 'لا يوجد', NULL, NULL, 1, '2026-06-11 22:54:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `centers`
--
ALTER TABLE `centers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donation_requests`
--
ALTER TABLE `donation_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `center_id` (`center_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_id` (`member_id`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `centers`
--
ALTER TABLE `centers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `donation_requests`
--
ALTER TABLE `donation_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `donation_requests`
--
ALTER TABLE `donation_requests`
  ADD CONSTRAINT `donation_requests_ibfk_1` FOREIGN KEY (`center_id`) REFERENCES `centers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
