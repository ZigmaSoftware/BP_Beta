-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 12, 2021 at 10:07 AM
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
-- Table structure for table `daily_attendance`
--

CREATE TABLE `daily_attendance` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `employee_id` varchar(50) NOT NULL,
  `entry_date` date NOT NULL,
  `entry_time` time NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `attendance_type` varchar(50) NOT NULL,
  `today_status` varchar(50) NOT NULL,
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
-- Indexes for table `daily_attendance`
--
ALTER TABLE `daily_attendance`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_attendance`
--
ALTER TABLE `daily_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
