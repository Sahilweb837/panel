-- ============================================
-- Staff Portal Feature SQL Dump
-- Tables: staff_offer_letters, leave_applications, staff_income_records
-- Database: institute_erp
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for staff_offer_letters
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff_offer_letters`;
CREATE TABLE `staff_offer_letters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `offer_letter_no` varchar(255) NOT NULL UNIQUE,
  `designation` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `offered_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `joining_date` date NOT NULL,
  `valid_until` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `staff_offer_letters_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for leave_applications
-- --------------------------------------------------------
DROP TABLE IF EXISTS `leave_applications`;
CREATE TABLE `leave_applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `leave_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `admin_remarks` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `leave_applications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for staff_income_records
-- --------------------------------------------------------
DROP TABLE IF EXISTS `staff_income_records`;
CREATE TABLE `staff_income_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned NOT NULL,
  `income_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_no` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `income_date` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Received',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `staff_income_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
