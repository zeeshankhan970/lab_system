-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2026 at 12:42 PM
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
-- Database: `lab_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `doctor_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `doctor_name`) VALUES
(2, 'Dr Imran Ullah'),
(3, 'Dr Kamran Khan'),
(4, 'Dr Saad'),
(5, 'Dr Asad Ullah'),
(8, 'Dr Atiq Rehman'),
(9, 'Dr Faisal Shah'),
(10, 'Dr Aftab Alam'),
(11, 'dr Adnan khan Gandapurr');

-- --------------------------------------------------------

--
-- Table structure for table `parameters`
--

CREATE TABLE `parameters` (
  `id` int(11) NOT NULL,
  `test_id` int(11) DEFAULT NULL,
  `parameter_name` varchar(100) DEFAULT NULL,
  `normal_range` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parameters`
--

INSERT INTO `parameters` (`id`, `test_id`, `parameter_name`, `normal_range`, `unit`) VALUES
(6, 1, 'Total Bilirubin', '0.3 - 1.2', 'mg/dL'),
(7, 1, 'Direct Bilirubin', '0.0 - 0.3', 'mg/dL'),
(8, 1, 'Indirect Bilirubin', '0.2 - 0.9', 'mg/dL'),
(9, 1, 'ALT (SGPT)', '7 - 56', 'U/L'),
(10, 1, 'Alkaline Phosphatase (ALP', '44 - 147', 'U/L'),
(11, 2, 'fasting blood suger', '70-99', 'mg/dL'),
(12, 3, 'Random Blood Sugar', '70-140', 'mg/dL'),
(17, 6, 'Hemoglobin (Male)', '13.0 - 17.0', 'g/dL'),
(18, 6, 'Hemoglobin (Female)', '12.0 - 15.0', 'g/dL'),
(19, 6, 'WBC Count', '4,000 - 11,000', '/µL'),
(20, 6, 'RBC Count (Male)', '4.5 - 5.9', 'million/µL'),
(21, 6, 'RBC Count (Female)', '4.1 - 5.1', 'million/µL'),
(22, 6, 'Platelets', '150,000 - 450,000', '/µL'),
(23, 6, 'MCV', '80 - 100', 'fL'),
(24, 6, 'MCH', '27 - 33', 'pg'),
(25, 6, 'MCHC', '32 - 36', 'g/dL'),
(30, 7, 'Blood Urea', '15 - 40', 'mg/dL'),
(31, 7, 'Creatinine', '0.5-1.3', 'mg/dL'),
(33, 8, 'Rheumatoid Factor', 'Negative', ''),
(36, 10, 'Uric Acid', '3.0-7.0', 'mg/dL'),
(39, 12, 'Prothrombin Time (PT)', '0.8-1.1', 'Seconds');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_name` varchar(100) NOT NULL,
  `age` varchar(30) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `doctor_reference` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `invoice_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_name`, `age`, `gender`, `doctor_reference`, `phone`, `subtotal`, `discount`, `grand_total`, `created_at`, `invoice_no`) VALUES
(12, 'rtrttrt', '20', 'Female', 'Dr Imran Ullah', '5656565', 1800.00, 100.00, 1700.00, '2026-06-13 13:49:53', 'INV-1781358593');

-- --------------------------------------------------------

--
-- Table structure for table `patient_tests`
--

CREATE TABLE `patient_tests` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `test_name` varchar(255) NOT NULL,
  `test_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_tests`
--

INSERT INTO `patient_tests` (`id`, `patient_id`, `test_name`, `test_price`, `created_at`) VALUES
(79, 12, 'LFT (Liver Function Test)', 800.00, '2026-06-13 13:50:38'),
(80, 12, 'RBS (Random Blood Sugar)', 200.00, '2026-06-13 13:50:38'),
(81, 12, 'LFT (Liver Function Test)', 800.00, '2026-06-13 13:50:38');

-- --------------------------------------------------------

--
-- Table structure for table `patient_test_results`
--

CREATE TABLE `patient_test_results` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_test_id` int(11) NOT NULL DEFAULT 0,
  `parameter_id` int(11) NOT NULL,
  `result_value` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_test_results`
--

INSERT INTO `patient_test_results` (`id`, `patient_id`, `patient_test_id`, `parameter_id`, `result_value`, `created_at`) VALUES
(24, 12, 79, 6, '', '2026-06-13 13:53:58'),
(25, 12, 79, 7, '', '2026-06-13 13:53:58'),
(26, 12, 79, 8, '', '2026-06-13 13:53:58'),
(27, 12, 79, 9, '33', '2026-06-13 13:53:58'),
(28, 12, 79, 10, '233', '2026-06-13 13:53:58'),
(29, 12, 80, 12, '205', '2026-06-13 13:53:58'),
(30, 12, 81, 6, '', '2026-06-13 13:53:58'),
(31, 12, 81, 7, '', '2026-06-13 13:53:58'),
(32, 12, 81, 8, '', '2026-06-13 13:53:58'),
(33, 12, 81, 9, '34', '2026-06-13 13:53:58'),
(34, 12, 81, 10, '234', '2026-06-13 13:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(100) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `currency` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `phone`, `address`, `currency`) VALUES
(1, 'My Laboratory', '03000000000', 'Dera Ismail Khan', 'Rs');

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `test_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `test_name`, `test_price`) VALUES
(1, 'LFT (Liver Function Test)', 800.00),
(2, 'FBS (Fasting Blood Sugar)', 200.00),
(3, 'RBS (Random Blood Sugar)', 200.00),
(6, 'CBC (Complete Blood Count)', 700.00),
(7, 'RFT (Renal Function Test)', 800.00),
(8, '(RA) Factor Qualitative', 750.00),
(10, 'Uric Acid (UA)', 400.00),
(12, 'PT', 600.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 0),
(2, 'admin2', 'c84258e9c39059a89ab77d846ddab909', 1),
(4, 'mansoor', 'd42d4b104afa23e3083fc2a153191936', 1),
(5, 'zeeshankhan1212', 'e10adc3949ba59abbe56e057f20f883e', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parameters`
--
ALTER TABLE `parameters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_tests`
--
ALTER TABLE `patient_tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_test_results`
--
ALTER TABLE `patient_test_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `parameters`
--
ALTER TABLE `parameters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `patient_tests`
--
ALTER TABLE `patient_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `patient_test_results`
--
ALTER TABLE `patient_test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
