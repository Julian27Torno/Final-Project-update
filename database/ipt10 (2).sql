-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2024 at 08:53 PM
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
  `attending_physician` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('admitted','discharged') DEFAULT 'admitted',
  `date_discharged` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `specialization` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `outpatient_findings`
--

CREATE TABLE `outpatient_findings` (
  `id` int(11) NOT NULL,
  `case_number` int(11) NOT NULL,
  `date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `quality` varchar(255) DEFAULT NULL,
  `severity` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `diagnosis` varchar(255) DEFAULT NULL,
  `blood_pressure` varchar(50) DEFAULT NULL,
  `respiratory_rate` varchar(50) DEFAULT NULL,
  `temperature` varchar(50) DEFAULT NULL,
  `oxygen_saturation` varchar(50) DEFAULT NULL,
  `medication_treatment` text DEFAULT NULL,
  `attending_physician` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 101, 'available'),
(2, 102, 'available'),
(3, 103, 'available'),
(4, 104, 'available'),
(5, 105, 'available'),
(6, 106, 'available'),
(7, 107, 'available'),
(8, 108, 'available'),
(9, 109, 'available'),
(10, 110, 'available'),
(11, 201, 'available'),
(12, 202, 'available'),
(13, 203, 'available'),
(14, 204, 'available'),
(15, 205, 'available'),
(16, 206, 'available'),
(17, 207, 'available'),
(18, 208, 'available'),
(19, 209, 'available'),
(20, 210, 'available'),
(21, 301, 'available'),
(22, 302, 'available'),
(23, 303, 'available'),
(24, 304, 'available'),
(25, 305, 'available'),
(26, 306, 'available'),
(27, 307, 'available'),
(28, 308, 'available'),
(29, 309, 'available'),
(30, 310, 'available');

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
(9, 'try', 'juliancarlostorno@yahoo.com', '$2y$10$kHAoDGv29hfYrMQNhRre4.ojLy7.cRjCtkLz6n5.WZZNSBCk20ice', 'Julian', 'Torno', '2024-11-28 15:39:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_logs`
--

CREATE TABLE `user_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logs`
--

INSERT INTO `user_logs` (`id`, `user_id`, `action`, `module`, `timestamp`, `details`) VALUES
(101, 9, 'ADD', 'admission', '2024-11-30 03:33:19', 'Admitted patient with case number 12 to room 103'),
(102, 9, 'DISCHARGE', 'admission', '2024-11-30 03:33:26', 'Discharged patient with case number 12 and released room 101'),
(103, 9, 'DELETE', 'Outpatient', '2024-11-30 03:34:39', 'Deleted outpatient record with ID 7'),
(104, 9, 'ADD', 'patient', '2024-11-30 03:40:13', 'Added a new patient: Daved Addoro');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admission_records`
--
ALTER TABLE `admission_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attending_physician` (`attending_physician`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `outpatient_findings`
--
ALTER TABLE `outpatient_findings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_number` (`case_number`);

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
-- Indexes for table `user_logs`
--
ALTER TABLE `user_logs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admission_records`
--
ALTER TABLE `admission_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `outpatient_findings`
--
ALTER TABLE `outpatient_findings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `case_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_logs`
--
ALTER TABLE `user_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admission_records`
--
ALTER TABLE `admission_records`
  ADD CONSTRAINT `fk_attending_physician` FOREIGN KEY (`attending_physician`) REFERENCES `doctors` (`doctor_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `outpatient_findings`
--
ALTER TABLE `outpatient_findings`
  ADD CONSTRAINT `outpatient_findings_ibfk_1` FOREIGN KEY (`case_number`) REFERENCES `patients` (`case_no`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
