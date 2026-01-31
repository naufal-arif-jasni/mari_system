-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 31, 2026 at 07:27 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mari_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `log_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `admin_id` int DEFAULT NULL COMMENT 'Admin who performed the action',
  `application_id` int DEFAULT NULL,
  `activity_type` enum('Login','Logout','Create','Update','Delete','View','Download') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `user_id`, `admin_id`, `application_id`, `activity_type`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, NULL, 8, 'Create', 'New application submitted', '::1', '2026-01-31 04:25:51'),
(2, NULL, NULL, 8, 'Update', 'Status changed from Pending to Approved by MasterAdmin', '::1', '2026-01-31 04:27:27'),
(3, 1, NULL, 9, 'Create', 'New application submitted', '::1', '2026-01-31 05:25:23'),
(4, NULL, NULL, 9, 'Update', 'Status changed from Pending to Under Review by MasterAdmin', '::1', '2026-01-31 07:02:59'),
(5, NULL, NULL, 9, 'Update', 'Status changed from Under Review to Rejected by MasterAdmin', '::1', '2026-01-31 07:03:06');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int NOT NULL,
  `admin_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_username`, `password_hash`, `full_name`, `email`, `is_active`, `last_login_at`, `created_at`, `updated_at`) VALUES
(4, 'nopal', '$2y$10$cn3tftW2cHzH4CyUX8Q1NO7Yx/WslT9S4.pabTGqpPCeGP3k40NPy', 'nopal', 'nopal@gmail.com', 1, NULL, '2026-01-31 07:18:34', '2026-01-31 07:18:34');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_details`
--

CREATE TABLE `applicant_details` (
  `applicant_id` int NOT NULL,
  `application_id` int NOT NULL COMMENT 'Foreign key to application_history',
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mykad` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_birth` date NOT NULL,
  `age` int DEFAULT NULL COMMENT 'Calculated age - updated via trigger',
  `gender` enum('Male','Female') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nationality` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Malaysian',
  `oku_card_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residential_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zip_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `marital_status` enum('Single','Married','Divorced','Widowed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Single',
  `education_level` enum('None','Primary','SPM','STPM','Diploma','Degree','Masters','PhD') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'None',
  `guardian_required` tinyint(1) DEFAULT '0',
  `guardian_full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_ic_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_relationship` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `guardian_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_authority_declaration` tinyint(1) DEFAULT '0',
  `primary_category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sub_category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diagnosis_date` date DEFAULT NULL,
  `severity_level` enum('Mild','Moderate','Severe','Profound') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `diagnosed_by` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hospital_clinic` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disability_additional_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `mobility_mode` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assistive_devices` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `adl_independence` enum('Fully Independent','Needs Some Assistance','Needs Full Assistance') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `communication_method` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT '0.00',
  `special_requirements` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `document_medical_report_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_specialist_form_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_oku_card_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_ic_copy_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_photo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_other_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accuracy_confirmed` tinyint(1) DEFAULT '0',
  `consent_given` tinyint(1) DEFAULT '0',
  `digital_signature` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_date` timestamp NULL DEFAULT NULL,
  `signature_ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `terms_accepted` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applicant_details`
--

INSERT INTO `applicant_details` (`applicant_id`, `application_id`, `full_name`, `mykad`, `date_of_birth`, `age`, `gender`, `nationality`, `oku_card_number`, `phone_number`, `email_address`, `residential_address`, `state`, `zip_code`, `marital_status`, `education_level`, `guardian_required`, `guardian_full_name`, `guardian_ic_number`, `guardian_relationship`, `guardian_phone_number`, `guardian_email`, `legal_authority_declaration`, `primary_category`, `sub_category`, `diagnosis_date`, `severity_level`, `diagnosed_by`, `hospital_clinic`, `disability_additional_notes`, `mobility_mode`, `assistive_devices`, `adl_independence`, `communication_method`, `employment_status`, `monthly_income`, `special_requirements`, `document_medical_report_path`, `document_specialist_form_path`, `document_oku_card_path`, `document_ic_copy_path`, `document_photo_path`, `document_other_path`, `accuracy_confirmed`, `consent_given`, `digital_signature`, `signature_date`, `signature_ip_address`, `user_agent`, `terms_accepted`, `created_at`, `updated_at`) VALUES
(1, 8, 'naufal', '041122070295', '2004-11-22', 21, '', 'Malaysian', 'OKU123456789', '0109094907', 'naufal@gmail.com', 'TELUK KUMBAR', 'Pulau Pinang', '11920', 'Single', 'Degree', 0, '', '', '', '', '', 0, 'Physical', '', '2025-01-01', 'Moderate', '', '', '', 'Unassisted', 'Oxygen Tank', '', 'Verbal', 'Student', '0.00', '', 'uploads/documents/1769833551_med_786.jpg', 'uploads/documents/1769833551_spec_660.pdf', NULL, NULL, NULL, NULL, 1, 1, 'Naufal', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-01-31 04:25:51', '2026-01-31 04:25:51'),
(2, 9, 'naufal', '041122070295', '2004-11-22', 21, '', 'Malaysian', 'OKU123456789', '0109094907', 'naufal@gmail.com', 'TELUK KUMBAR', 'Pulau Pinang', '11920', 'Single', 'Degree', 0, '', '', '', '', '', 0, 'Physical', '', '2025-01-01', 'Moderate', '', '', '', 'Unassisted', '', '', 'Verbal', 'Student', '0.00', '', 'uploads/documents/1769837123_med_943.jpg', 'uploads/documents/1769837123_spec_538.pdf', NULL, NULL, NULL, NULL, 1, 1, 'Naufal', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 1, '2026-01-31 05:25:23', '2026-01-31 05:25:23');

--
-- Triggers `applicant_details`
--
DELIMITER $$
CREATE TRIGGER `calculate_age_insert` BEFORE INSERT ON `applicant_details` FOR EACH ROW BEGIN
    SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.date_of_birth, CURDATE());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `calculate_age_update` BEFORE UPDATE ON `applicant_details` FOR EACH ROW BEGIN
    SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.date_of_birth, CURDATE());
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `applications`
-- (See below for the actual view)
--
CREATE TABLE `applications` (
`application_id` int
,`user_id` int
,`application_number` varchar(50)
,`application_status` enum('Pending','Approved','Rejected','Under Review')
,`submission_date` timestamp
,`last_updated` timestamp
,`reviewed_by` varchar(100)
,`reviewed_at` timestamp
,`admin_remarks` text
,`full_name` varchar(255)
,`mykad` varchar(20)
,`date_of_birth` date
,`age` int
,`gender` enum('Male','Female')
,`nationality` varchar(100)
,`oku_card_number` varchar(50)
,`phone_number` varchar(20)
,`email_address` varchar(255)
,`residential_address` text
,`state` varchar(50)
,`zip_code` varchar(10)
,`marital_status` enum('Single','Married','Divorced','Widowed')
,`education_level` enum('None','Primary','SPM','STPM','Diploma','Degree','Masters','PhD')
,`guardian_required` tinyint(1)
,`guardian_full_name` varchar(255)
,`guardian_ic_number` varchar(20)
,`guardian_relationship` varchar(100)
,`guardian_phone_number` varchar(20)
,`guardian_email` varchar(255)
,`legal_authority_declaration` tinyint(1)
,`primary_category` varchar(100)
,`sub_category` varchar(100)
,`diagnosis_date` date
,`severity_level` enum('Mild','Moderate','Severe','Profound')
,`diagnosed_by` varchar(255)
,`hospital_clinic` varchar(255)
,`disability_additional_notes` text
,`mobility_mode` varchar(100)
,`assistive_devices` text
,`adl_independence` enum('Fully Independent','Needs Some Assistance','Needs Full Assistance')
,`communication_method` varchar(100)
,`employment_status` varchar(100)
,`monthly_income` decimal(10,2)
,`special_requirements` text
,`document_medical_report_path` varchar(255)
,`document_specialist_form_path` varchar(255)
,`document_oku_card_path` varchar(255)
,`document_ic_copy_path` varchar(255)
,`document_photo_path` varchar(255)
,`document_other_path` varchar(255)
,`accuracy_confirmed` tinyint(1)
,`consent_given` tinyint(1)
,`digital_signature` varchar(255)
,`signature_date` timestamp
,`signature_ip_address` varchar(45)
,`user_agent` text
,`terms_accepted` tinyint(1)
);

-- --------------------------------------------------------

--
-- Table structure for table `application_history`
--

CREATE TABLE `application_history` (
  `application_id` int NOT NULL,
  `user_id` int DEFAULT NULL COMMENT 'Foreign key to users table',
  `application_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Auto-generated: MARI-YYYY-XXXXXX',
  `status` enum('Pending','Approved','Rejected','Under Review') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `submission_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reviewed_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin username who reviewed',
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `admin_remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Current admin remarks'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `application_history`
--

INSERT INTO `application_history` (`application_id`, `user_id`, `application_number`, `status`, `submission_date`, `last_updated`, `reviewed_by`, `reviewed_at`, `admin_remarks`) VALUES
(8, 1, 'MARI-2026-000001', 'Approved', '2026-01-31 04:25:51', '2026-01-31 04:27:27', 'MasterAdmin', '2026-01-31 04:27:27', ''),
(9, 1, 'MARI-2026-000002', 'Rejected', '2026-01-31 05:25:23', '2026-01-31 07:03:06', 'MasterAdmin', '2026-01-31 07:03:06', '');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `history_id` int NOT NULL,
  `application_id` int NOT NULL COMMENT 'Foreign key to application_history',
  `admin_id` int DEFAULT NULL COMMENT 'Foreign key to admin table - who made the change',
  `old_status` enum('Pending','Approved','Rejected','Under Review') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_status` enum('Pending','Approved','Rejected','Under Review') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `changed_by` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Admin username (legacy)',
  `remarks` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Admin notes for this status change',
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`history_id`, `application_id`, `admin_id`, `old_status`, `new_status`, `changed_by`, `remarks`, `changed_at`, `ip_address`) VALUES
(1, 8, NULL, 'Pending', 'Approved', 'MasterAdmin', '', '2026-01-31 04:27:27', NULL),
(2, 9, NULL, 'Pending', 'Under Review', 'MasterAdmin', '', '2026-01-31 07:02:59', NULL),
(3, 9, NULL, 'Under Review', 'Rejected', 'MasterAdmin', '', '2026-01-31 07:03:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ic_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zip_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oku_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `ic_number`, `email`, `phone_number`, `address`, `state`, `zip_code`, `username`, `password`, `profile_picture`, `oku_number`, `created_at`, `updated_at`) VALUES
(1, 'naufal', '041122070295', 'naufal@gmail.com', '0109094907', 'TELUK KUMBAR', 'Pulau Pinang', '11920', 'naufal', '$2y$10$tooq0k3WkXkIuDWxx0iAb.RXWJgxHmDCDAN2XTETEj7oV.G87NEte', 'uploads/profiles/1769833574_SAMPLE_IMAGE.jpg', 'OKU123456789', '2026-01-31 04:11:15', '2026-01-31 04:26:14');

-- --------------------------------------------------------

--
-- Structure for view `applications`
--
DROP TABLE IF EXISTS `applications`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `applications`  AS SELECT `ah`.`application_id` AS `application_id`, `ah`.`user_id` AS `user_id`, `ah`.`application_number` AS `application_number`, `ah`.`status` AS `application_status`, `ah`.`submission_date` AS `submission_date`, `ah`.`last_updated` AS `last_updated`, `ah`.`reviewed_by` AS `reviewed_by`, `ah`.`reviewed_at` AS `reviewed_at`, `ah`.`admin_remarks` AS `admin_remarks`, `ad`.`full_name` AS `full_name`, `ad`.`mykad` AS `mykad`, `ad`.`date_of_birth` AS `date_of_birth`, `ad`.`age` AS `age`, `ad`.`gender` AS `gender`, `ad`.`nationality` AS `nationality`, `ad`.`oku_card_number` AS `oku_card_number`, `ad`.`phone_number` AS `phone_number`, `ad`.`email_address` AS `email_address`, `ad`.`residential_address` AS `residential_address`, `ad`.`state` AS `state`, `ad`.`zip_code` AS `zip_code`, `ad`.`marital_status` AS `marital_status`, `ad`.`education_level` AS `education_level`, `ad`.`guardian_required` AS `guardian_required`, `ad`.`guardian_full_name` AS `guardian_full_name`, `ad`.`guardian_ic_number` AS `guardian_ic_number`, `ad`.`guardian_relationship` AS `guardian_relationship`, `ad`.`guardian_phone_number` AS `guardian_phone_number`, `ad`.`guardian_email` AS `guardian_email`, `ad`.`legal_authority_declaration` AS `legal_authority_declaration`, `ad`.`primary_category` AS `primary_category`, `ad`.`sub_category` AS `sub_category`, `ad`.`diagnosis_date` AS `diagnosis_date`, `ad`.`severity_level` AS `severity_level`, `ad`.`diagnosed_by` AS `diagnosed_by`, `ad`.`hospital_clinic` AS `hospital_clinic`, `ad`.`disability_additional_notes` AS `disability_additional_notes`, `ad`.`mobility_mode` AS `mobility_mode`, `ad`.`assistive_devices` AS `assistive_devices`, `ad`.`adl_independence` AS `adl_independence`, `ad`.`communication_method` AS `communication_method`, `ad`.`employment_status` AS `employment_status`, `ad`.`monthly_income` AS `monthly_income`, `ad`.`special_requirements` AS `special_requirements`, `ad`.`document_medical_report_path` AS `document_medical_report_path`, `ad`.`document_specialist_form_path` AS `document_specialist_form_path`, `ad`.`document_oku_card_path` AS `document_oku_card_path`, `ad`.`document_ic_copy_path` AS `document_ic_copy_path`, `ad`.`document_photo_path` AS `document_photo_path`, `ad`.`document_other_path` AS `document_other_path`, `ad`.`accuracy_confirmed` AS `accuracy_confirmed`, `ad`.`consent_given` AS `consent_given`, `ad`.`digital_signature` AS `digital_signature`, `ad`.`signature_date` AS `signature_date`, `ad`.`signature_ip_address` AS `signature_ip_address`, `ad`.`user_agent` AS `user_agent`, `ad`.`terms_accepted` AS `terms_accepted` FROM (`application_history` `ah` left join `applicant_details` `ad` on((`ah`.`application_id` = `ad`.`application_id`)))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `admin_username` (`admin_username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `applicant_details`
--
ALTER TABLE `applicant_details`
  ADD PRIMARY KEY (`applicant_id`),
  ADD UNIQUE KEY `application_id` (`application_id`),
  ADD KEY `idx_mykad` (`mykad`);

--
-- Indexes for table `application_history`
--
ALTER TABLE `application_history`
  ADD PRIMARY KEY (`application_id`),
  ADD UNIQUE KEY `application_number` (`application_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_submission_date` (`submission_date`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `idx_changed_at` (`changed_at`),
  ADD KEY `idx_new_status` (`new_status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `ic_number` (`ic_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `applicant_details`
--
ALTER TABLE `applicant_details`
  MODIFY `applicant_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `application_history`
--
ALTER TABLE `application_history`
  MODIFY `application_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `history_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_activity_log_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_log_application` FOREIGN KEY (`application_id`) REFERENCES `application_history` (`application_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_activity_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `applicant_details`
--
ALTER TABLE `applicant_details`
  ADD CONSTRAINT `fk_applicant_application_history` FOREIGN KEY (`application_id`) REFERENCES `application_history` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `application_history`
--
ALTER TABLE `application_history`
  ADD CONSTRAINT `fk_application_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `fk_status_admin` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_status_application_history` FOREIGN KEY (`application_id`) REFERENCES `application_history` (`application_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
