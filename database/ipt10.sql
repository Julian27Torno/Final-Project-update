-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 12:23 PM
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
-- Database: `ipt10`
--

-- --------------------------------------------------------

--
-- Table structure for table `admission_records`
--

CREATE TABLE `admission_records` (
  `id` int(11) NOT NULL,
  `case_number` varchar(255) NOT NULL,
  `date_admitted` date NOT NULL,
  `reason` text NOT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `attending_physician` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admission_records`
--

INSERT INTO `admission_records` (`id`, `case_number`, `date_admitted`, `reason`, `room_number`, `attending_physician`, `created_at`) VALUES
(4, 'P-1', '2024-11-17', 'sick', '9', 'asdas', '2024-11-17 14:35:58'),
(5, 'P-5', '2024-11-17', 'sick', '9', 'asdfa', '2024-11-17 14:36:25'),
(6, 'P-4', '2024-11-18', 'sadas', '9', 'asda', '2024-11-17 14:40:15');

-- --------------------------------------------------------

--
-- Table structure for table `outpatient_findings`
--

CREATE TABLE `outpatient_findings` (
  `id` int(11) NOT NULL,
  `case_number` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `history_present_illness` text DEFAULT NULL,
  `physical_examination` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `blood_pressure` varchar(50) DEFAULT NULL,
  `respiratory_rate` varchar(50) DEFAULT NULL,
  `capillary_refill` varchar(50) DEFAULT NULL,
  `temperature` varchar(50) DEFAULT NULL,
  `weight` varchar(50) DEFAULT NULL,
  `pulse_rate` varchar(50) DEFAULT NULL,
  `medication_treatment` text DEFAULT NULL,
  `attending_physician` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `outpatient_findings`
--

INSERT INTO `outpatient_findings` (`id`, `case_number`, `date`, `chief_complaint`, `history_present_illness`, `physical_examination`, `diagnosis`, `blood_pressure`, `respiratory_rate`, `capillary_refill`, `temperature`, `weight`, `pulse_rate`, `medication_treatment`, `attending_physician`, `created_at`) VALUES
(1, 'P-1', '2024-11-18', 'Sore muscles', 'none', 'Bruises', 'asdsa', '120/80', '18', '100', '36', '78', '18', 'asdasd', 'asdas', '2024-11-16 14:16:58'),
(2, 'fsd', '2024-11-14', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', '2024-11-16 14:18:09');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `case_no` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `birthday` date NOT NULL,
  `birthplace` varchar(100) DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed') DEFAULT 'Single',
  `gender` enum('Male','Female') NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`case_no`, `last_name`, `first_name`, `middle_name`, `address`, `age`, `birthday`, `birthplace`, `civil_status`, `gender`, `contact_no`, `religion`, `occupation`, `date_added`) VALUES
(1, 'Torno', 'Julian ', 'Jimenez', 'asdasdas', 25, '2024-11-21', 'Angeles', 'Single', 'Male', '12312312', 'asdasdas', 'asdas', '2024-11-13 15:47:09'),
(2, 'sdafsad', 'dsafsdaf', 'sadfasd', 'dasfsda', 5, '2024-11-12', 'fdsafdsa', 'Single', 'Male', 'asdfdasdf', 'sadfasd', 'sadfasdf', '2024-11-13 15:58:07'),
(3, 'Jordan', 'Michael', 'J', 'asdasdas', 25, '2024-11-20', 'Angeles', 'Single', 'Male', '12312', 'sadas', 'asdas', '2024-11-16 13:05:47'),
(4, 'Elmickey', 'Carlos', 'Louise', 'asdasdas', 25, '2024-11-20', '12312', 'Married', 'Male', '123123', '123123', 'ASDASD', '2024-11-16 13:15:45'),
(5, 'Torno', 'sadas', 'sadfasd', 'asdsa', 234, '2024-11-17', 'asdas', 'Single', 'Male', '21312', 'asd', 'asda', '2024-11-17 13:48:25');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_number` int(11) NOT NULL,
  `status` enum('available','occupied') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_number`, `status`) VALUES
(1, 1, 'available'),
(2, 2, 'available'),
(3, 3, 'available'),
(4, 4, 'available'),
(5, 5, 'available'),
(6, 6, 'available'),
(7, 7, 'available'),
(8, 8, 'available'),
(9, 9, ''),
(10, 10, 'available'),
(11, 11, 'available'),
(12, 12, 'available'),
(13, 13, 'available'),
(14, 14, 'available'),
(15, 15, 'available'),
(16, 16, 'available'),
(17, 17, 'available'),
(18, 18, 'available'),
(19, 19, 'available'),
(20, 20, 'available'),
(21, 21, 'available'),
(22, 22, 'available'),
(23, 23, 'available'),
(24, 24, 'available'),
(25, 25, 'available'),
(26, 26, 'available'),
(27, 27, 'available'),
(28, 28, 'available'),
(29, 29, 'available'),
(30, 30, 'available'),
(31, 31, 'available'),
(32, 32, 'available'),
(33, 33, 'available'),
(34, 34, 'available'),
(35, 35, 'available'),
(36, 36, 'available'),
(37, 37, 'available'),
(38, 38, 'available'),
(39, 39, 'available'),
(40, 40, 'available'),
(41, 41, 'available'),
(42, 42, 'available'),
(43, 43, 'available'),
(44, 44, 'available'),
(45, 45, 'available'),
(46, 46, 'available'),
(47, 47, 'available'),
(48, 48, 'available'),
(49, 49, 'available'),
(50, 50, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, 'try', 'torno.juliancarlos@auf.edu.ph', '$2y$10$IdWSpOK3KiqcMSBEVmYWIOm7NS67A1.JYcSMJhxmN1PgpMqM9ZHSu', 'Julian', 'Torno', '2024-11-11 16:23:00', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admission_records`
--
ALTER TABLE `admission_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `outpatient_findings`
--
ALTER TABLE `outpatient_findings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`case_no`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_number` (`room_number`);

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
-- AUTO_INCREMENT for table `admission_records`
--
ALTER TABLE `admission_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `outpatient_findings`
--
ALTER TABLE `outpatient_findings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `case_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
