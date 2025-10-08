-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2021 at 09:43 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ascent`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_holidays`
--

DROP TABLE IF EXISTS `attendance_holidays`;
CREATE TABLE `attendance_holidays` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `attendance_set_unique_id` varchar(50) NOT NULL,
  `holiday_date` date NOT NULL,
  `remarks` longtext NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_year` varchar(50) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `sess_user_type` varchar(50) NOT NULL,
  `sess_user_id` varchar(50) NOT NULL,
  `sess_company_id` varchar(50) NOT NULL,
  `sess_branch_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_late_permission`
--

DROP TABLE IF EXISTS `attendance_late_permission`;
CREATE TABLE `attendance_late_permission` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `attendance_set_unique_id` varchar(50) NOT NULL,
  `late_permission_type` varchar(10) NOT NULL,
  `late_count` int(10) NOT NULL,
  `permission_leave_type` varchar(10) NOT NULL,
  `permission_count` int(10) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_year` varchar(50) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `sess_user_type` varchar(50) NOT NULL,
  `sess_user_id` varchar(50) NOT NULL,
  `sess_company_id` varchar(50) NOT NULL,
  `sess_branch_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_leave_type`
--

DROP TABLE IF EXISTS `attendance_leave_type`;
CREATE TABLE `attendance_leave_type` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `attendance_set_unique_id` varchar(50) NOT NULL,
  `leave_type` varchar(10) NOT NULL,
  `leave_days` int(10) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_year` varchar(50) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `sess_user_type` varchar(50) NOT NULL,
  `sess_user_id` varchar(50) NOT NULL,
  `sess_company_id` varchar(50) NOT NULL,
  `sess_branch_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_setting`
--

DROP TABLE IF EXISTS `attendance_setting`;
CREATE TABLE `attendance_setting` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `attendance_shift_name` varchar(50) NOT NULL,
  `attendance_shift_hr` varchar(10) NOT NULL,
  `working_time_from` time NOT NULL,
  `working_time_to` time NOT NULL,
  `late_hrs` varchar(10) NOT NULL,
  `permission_hrs` varchar(10) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT 1,
  `is_delete` int(11) NOT NULL DEFAULT 0,
  `updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `acc_year` varchar(50) NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `sess_user_type` varchar(50) NOT NULL,
  `sess_user_id` varchar(50) NOT NULL,
  `sess_company_id` varchar(50) NOT NULL,
  `sess_branch_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_holidays`
--
ALTER TABLE `attendance_holidays`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_late_permission`
--
ALTER TABLE `attendance_late_permission`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_leave_type`
--
ALTER TABLE `attendance_leave_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance_setting`
--
ALTER TABLE `attendance_setting`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_holidays`
--
ALTER TABLE `attendance_holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_late_permission`
--
ALTER TABLE `attendance_late_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_leave_type`
--
ALTER TABLE `attendance_leave_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attendance_setting`
--
ALTER TABLE `attendance_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
