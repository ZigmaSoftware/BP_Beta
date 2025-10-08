-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2021 at 06:56 AM
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
-- Table structure for table `attendance_setting`
--

CREATE TABLE `attendance_setting` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(50) NOT NULL,
  `attendance_shift_name` varchar(50) NOT NULL,
  `attendance_shift_hr` varchar(10) NOT NULL,
  `working_time_from` time NOT NULL,
  `working_time_to` time NOT NULL,
  `late_hrs` varchar(10) NOT NULL,
  `permission_hrs` varchar(10) NOT NULL,
  `late_time` varchar(10) NOT NULL,
  `permission_time` varchar(10) NOT NULL,
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
-- Dumping data for table `attendance_setting`
--

INSERT INTO `attendance_setting` (`id`, `unique_id`, `attendance_shift_name`, `attendance_shift_hr`, `working_time_from`, `working_time_to`, `late_hrs`, `permission_hrs`, `late_time`, `permission_time`, `is_active`, `is_delete`, `updated`, `created`, `acc_year`, `session_id`, `sess_user_type`, `sess_user_id`, `sess_company_id`, `sess_branch_id`) VALUES
(1, '611a3c296be3d47802', 'Morning Shift', '1', '09:30:00', '19:00:00', '13:00', '13:00', '', '', 1, 1, '2021-08-17 07:42:00', '2021-08-16 10:21:29', '2021-2022', 'k2fbj7hfhubjedadploemel5jv', '5f97fc3257f2525529', '5ff562ed542d625323', 'comp5fa3b1c2a3bab70290', 'bran5fa3b1dced5d363322');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_setting`
--
ALTER TABLE `attendance_setting`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_setting`
--
ALTER TABLE `attendance_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
