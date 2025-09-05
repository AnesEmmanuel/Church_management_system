-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 03:55 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `church_cms`
--

-- --------------------------------------------------------

--
-- Table structure for table `communities`
--

CREATE TABLE `communities` (
  `id` int(11) NOT NULL,
  `community_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `communities`
--

INSERT INTO `communities` (`id`, `community_name`) VALUES
(5, 'MT.ANNA'),
(6, 'MT.ANNA'),
(7, 'MT.GASPER'),
(8, 'MT.SECILIA'),
(9, 'MT.APRONI'),
(10, 'MT.MARAGRARETHA'),
(11, 'MT.ANNASTASIA');

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `member_name` varchar(100) DEFAULT NULL,
  `month_paid` varchar(20) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `phone` varchar(15) DEFAULT NULL,
  `community_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `member_name`, `month_paid`, `amount`, `payment_method`, `payment_date`, `phone`, `community_id`, `unit_id`) VALUES
(15, 'anesius emmanuel', '2025', '200.00', 'Cash', '2025-09-03 16:20:33', '', 6, 10);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(150) DEFAULT NULL,
  `event_description` text DEFAULT NULL,
  `event_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `event_description`, `event_date`) VALUES
(2, 'WAGENI', 'PAROKO WA RUNAZI ANAKUJA KUTEMBELEA', '2025-09-27');

-- --------------------------------------------------------

--
-- Table structure for table `kipaimara`
--

CREATE TABLE `kipaimara` (
  `id` int(11) NOT NULL,
  `community_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `full_name` varchar(150) NOT NULL,
  `date_of_birth` date NOT NULL,
  `parent_name` varchar(150) NOT NULL,
  `parent_phone` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `year_registered` year(4) NOT NULL DEFAULT year(curdate()),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kipaimara`
--

INSERT INTO `kipaimara` (`id`, `community_id`, `unit_id`, `full_name`, `date_of_birth`, `parent_name`, `parent_phone`, `start_date`, `end_date`, `year_registered`, `created_at`) VALUES
(5, 6, 8, 'ROSE GEORGE LUGANO', '2025-09-02', 'LUGANO SYLIVESTER', '0787829229', '2025-09-25', '2025-09-30', 2025, '2025-09-02 19:21:04');

-- --------------------------------------------------------

--
-- Table structure for table `leaders`
--

CREATE TABLE `leaders` (
  `id` int(11) NOT NULL,
  `community_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `unit` varchar(150) DEFAULT NULL,
  `confirmation_no` varchar(20) DEFAULT NULL,
  `Baptism_no` varchar(20) DEFAULT NULL,
  `communial_no` varchar(20) DEFAULT NULL,
  `marriage_no` varchar(20) DEFAULT NULL,
  `sacrament5` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `community_name` varchar(20) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `leaders`
--

INSERT INTO `leaders` (`id`, `community_id`, `name`, `profile_pic`, `phone`, `unit`, `confirmation_no`, `Baptism_no`, `communial_no`, `marriage_no`, `sacrament5`, `role`, `community_name`, `unit_id`, `created_at`) VALUES
(26, 11, 'ANESIUS EMMANUEL GORODIAN', NULL, '0718137639', 'KYEBITEMBE', '123', '4445', '354656', '454656', '556', 'Chairman', NULL, NULL, '2025-09-02 21:37:17'),
(27, 10, 'JUSTINA KIKWE NZIILE', NULL, '0763446822', 'KYEBITEMBE', '123', '4445', '354656', '454656', '556', 'Ass_Chairman', NULL, NULL, '2025-09-02 21:37:40'),
(28, 10, 'JOSHU', NULL, '9393338', 'KASINDAGA', '123', '4445', '321', '67', '766', 'Chairman', NULL, NULL, '2025-09-02 21:38:19'),
(29, 8, 'kelvin', NULL, '67787', 'KYEBITEMBE', '123', '4445', '9890', '3345', '65667', 'Secretary', NULL, NULL, '2025-09-02 21:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `recipient` varchar(100) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `recipient`, `title`, `content`, `created_at`) VALUES
(10, 'anesius emmanuel', 'Tithe Payment Received', 'Tithe payment received for 2025-04: 2,000.00', '2025-09-02 12:21:30'),
(11, 'leaders', 'New Event: WAGENI', 'New event: WAGENI on 2025-09-27. Details: PAROKO WA RUNAZI ANAKUJA KUTEMBELEA', '2025-09-02 12:22:09'),
(12, 'members', 'malipo', 'malipo yamepokelewa', '2025-09-03 11:14:07'),
(13, 'JUMA', 'Donation Received', 'Donation received for 2025-03: 40,000.00', '2025-09-03 11:32:27'),
(14, 'MKIZA', 'Donation Received', 'Donation received for 2025-08: 1,234.00', '2025-09-03 12:00:24'),
(15, 'JUMA JUX', 'Donation Received', 'Donation received for 2025-12: 4,000.00', '2025-09-03 12:08:05'),
(16, 'koku', 'Donation Received', 'Donation received for 2025-06: 2,000.00', '2025-09-03 12:08:55'),
(17, 'anesius emmanuel', 'Donation Received', 'Donation received for 2025-04: 40,000.00', '2025-09-03 12:21:48'),
(18, 'MKIZA', 'Donation Received', 'Donation received for 2025-08: 900,000.00', '2025-09-03 12:30:59'),
(19, 'JUMA JUX', 'Donation Received', 'Donation received for 2025-02: 10,000.00', '2025-09-03 12:32:57'),
(20, 'JUMA', 'Donation Received', 'Donation received for 2025-11: 90,000.00', '2025-09-03 13:06:01'),
(21, 'MKIZA', 'Donation Received', 'Donation received for 2025-11: 9,999.00', '2025-09-03 13:09:25'),
(22, 'MKIZA', 'Donation Received', 'Donation received for 2025-11: 9,999.00', '2025-09-03 13:12:32'),
(23, 'DONALD TRUMP', 'Donation Received', 'Donation received for 2025-12: 200,000.00', '2025-09-03 15:38:36'),
(24, 'ANESIUS EMMANUEL GORODIAN', 'GRADUATION', 'LSDFNSDYUFSDHFYSFHSHFHSJFKJSJKFSJFSNVSBVJKSKFUFFIOOIFJKDFKSDSFYSFYWYFLKDQPODIOFGYBVHHKFHDTUFYUEIUVSTFWIYHFFKFDOQOBVBSDTFJCJWN', '2025-09-03 16:08:13'),
(25, 'members', 'GRADUATION', 'JHIDFGYUGEYFUIEHERUIFYS', '2025-09-03 16:09:11'),
(26, 'anesius emmanuel', 'Donation Received', 'Donation received for 2025-06: 200.00', '2025-09-03 16:20:33'),
(27, 'ANESIUS EMMANUEL GORODIAN', 'hghjghjgh', 'hfghrhtrhy', '2025-09-04 13:41:19');

-- --------------------------------------------------------

--
-- Table structure for table `prayers`
--

CREATE TABLE `prayers` (
  `id` int(11) NOT NULL,
  `prayer_text` text DEFAULT NULL,
  `day_of_week` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `prayers`
--

INSERT INTO `prayers` (`id`, `prayer_text`, `day_of_week`) VALUES
(3, 'Mathayo 2', 'Wednesday');

-- --------------------------------------------------------

--
-- Table structure for table `ubatizo`
--

CREATE TABLE `ubatizo` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `date_of_birth` date NOT NULL,
  `parent_name` varchar(150) NOT NULL,
  `parent_phone` varchar(20) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `community_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `year_registered` year(4) NOT NULL DEFAULT year(curdate()),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ubatizo`
--

INSERT INTO `ubatizo` (`id`, `full_name`, `date_of_birth`, `parent_name`, `parent_phone`, `start_date`, `end_date`, `community_id`, `unit_id`, `year_registered`, `created_at`) VALUES
(5, 'EMMANUEL GORODIAN', '2005-12-31', 'JUMA BAKARY', '0787829229', '2025-09-03', '2027-04-03', 6, 10, 2025, '2025-09-03 06:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `unit_name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `unit_name`, `description`) VALUES
(8, 'KISANA', 'idadi ya waumini ni 200'),
(9, 'NSHANJE', 'idadi ya waumini ni 200'),
(10, 'KASINDAGA', 'idadi ya waumini ni 200'),
(11, 'NYAMILANDA', 'idadi ya waumini ni 200'),
(12, 'KYEBITEMBE', 'idadi ya waumini ni 2000 Ambayo pia ndo Parokia yetu'),
(13, 'KANAZI', 'idadi ya waumini ni 200');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`) VALUES
(5, 'admin', '$2y$10$rsyTrUrDSI4sv0yBX3E5P.Cdjnj70aonzssQPxdvN/iHXE6T4yse.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kipaimara`
--
ALTER TABLE `kipaimara`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_kipaimara_community` (`community_id`),
  ADD KEY `fk_kipaimara_unit` (`unit_id`);

--
-- Indexes for table `leaders`
--
ALTER TABLE `leaders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `community_id` (`community_name`),
  ADD KEY `unit_id` (`unit_id`),
  ADD KEY `fk_community` (`community_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prayers`
--
ALTER TABLE `prayers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ubatizo`
--
ALTER TABLE `ubatizo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
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
-- AUTO_INCREMENT for table `communities`
--
ALTER TABLE `communities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kipaimara`
--
ALTER TABLE `kipaimara`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `leaders`
--
ALTER TABLE `leaders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `prayers`
--
ALTER TABLE `prayers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ubatizo`
--
ALTER TABLE `ubatizo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kipaimara`
--
ALTER TABLE `kipaimara`
  ADD CONSTRAINT `fk_kipaimara_community` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_kipaimara_unit` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `leaders`
--
ALTER TABLE `leaders`
  ADD CONSTRAINT `fk_community` FOREIGN KEY (`community_id`) REFERENCES `communities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leaders_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
