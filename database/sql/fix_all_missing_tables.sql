-- ============================================================================
-- FIX ALL MISSING TABLES & COLUMNS
-- Safe to run multiple times (uses IF NOT EXISTS / column checks)
-- Generated from Laravel migrations: 2026_06_09 through 2026_07_02
-- ============================================================================

-- ============================================================================
-- 1. CREATE TABLE: training_courses  (from 2026_06_24_112244)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `training_courses` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `short_code` VARCHAR(50) NULL,
    `duration` VARCHAR(50) NOT NULL DEFAULT '28 Days',
    `fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `description` TEXT NULL,
    `tenure_1_month` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tenure_3_months` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tenure_6_months` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `tenure_12_months` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    UNIQUE KEY `training_courses_name_unique` (`name`),
    INDEX `training_courses_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add deleted_at if table existed but column was missing (from 2026_07_02_110000)
SET @dbname = DATABASE();
SET @tablename = 'training_courses';
SET @columnname = 'deleted_at';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` ADD `deleted_at` TIMESTAMP NULL')
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 2. CREATE TABLE: trainings  (from 2026_06_25_000000)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `trainings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `slip_no` VARCHAR(60) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `father_name` VARCHAR(100) NULL,
    `email` VARCHAR(100) NOT NULL,
    `college` VARCHAR(150) NULL,
    `mobile` VARCHAR(20) NOT NULL,
    `training_course_id` BIGINT UNSIGNED NULL,
    `course_name` VARCHAR(150) NULL,
    `duration` VARCHAR(50) NOT NULL,
    `fees` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Cash',
    `payment_date` DATE NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'Unpaid',
    `upi_transaction_id` VARCHAR(100) NULL,
    `created_by` BIGINT UNSIGNED NOT NULL,
    `deleted_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `trainings_slip_no_unique` (`slip_no`),
    CONSTRAINT `trainings_training_course_id_foreign` FOREIGN KEY (`training_course_id`) REFERENCES `training_courses` (`id`) ON DELETE CASCADE,
    CONSTRAINT `trainings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- If trainings table existed without the extra columns (from 2026_06_25_000001)
SET @tablename = 'trainings';

SET @columnname = 'status';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `trainings` ADD `status` VARCHAR(20) NOT NULL DEFAULT \'Unpaid\' AFTER `payment_date`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'upi_transaction_id';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `trainings` ADD `upi_transaction_id` VARCHAR(100) NULL AFTER `status`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'course_name';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `trainings` ADD `course_name` VARCHAR(150) NULL AFTER `training_course_id`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 3. ADD COLUMNS: fee_invoices  (from 2026_06_09_102312 & 2026_06_27_050301)
-- ============================================================================
SET @tablename = 'fee_invoices';

SET @columnname = 'fee_items';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `fee_invoices` ADD `fee_items` TEXT NULL AFTER `fee_category`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'billing_month';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `fee_invoices` ADD `billing_month` TINYINT UNSIGNED NULL AFTER `fee_category`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'billing_year';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `fee_invoices` ADD `billing_year` INT UNSIGNED NULL AFTER `billing_month`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 4. ADD COLUMN: students.fee_tenure  (from 2026_06_13_064215)
-- ============================================================================
SET @tablename = 'students';
SET @columnname = 'fee_tenure';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `students` ADD `fee_tenure` VARCHAR(50) NULL AFTER `prospectus_fee`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 5. ADD COLUMN: courses.syllabus_path  (from 2026_06_30_075241)
-- ============================================================================
SET @tablename = 'courses';
SET @columnname = 'syllabus_path';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `courses` ADD `syllabus_path` VARCHAR(255) NULL AFTER `fee`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- 6. CREATE TABLE: student_assignments  (from 2026_06_30_084423)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `student_assignments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `subject` VARCHAR(255) NULL,
    `type` VARCHAR(255) NOT NULL DEFAULT 'Assignment',
    `due_date` DATE NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
    `priority` VARCHAR(255) NOT NULL DEFAULT 'Medium',
    `file_path` VARCHAR(255) NULL,
    `remarks` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `student_assignments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 7. CREATE TABLE: student_academic_records  (from 2026_06_30_094723)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `student_academic_records` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `exam_type` VARCHAR(255) NOT NULL,
    `subject` VARCHAR(255) NULL,
    `marks` DECIMAL(5,2) NULL,
    `max_marks` DECIMAL(5,2) NOT NULL DEFAULT 100.00,
    `grade` VARCHAR(255) NULL,
    `result_status` VARCHAR(255) NOT NULL DEFAULT 'Pass',
    `file_path` VARCHAR(255) NULL,
    `remarks` TEXT NULL,
    `exam_date` DATE NULL,
    `session` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `student_academic_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 8. ADD COLUMN: users.raw_password  (from 2026_07_01_105308)
-- ============================================================================
SET @tablename = 'users';
SET @columnname = 'raw_password';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `users` ADD `raw_password` VARCHAR(255) NULL AFTER `password`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Set default raw_password for existing staff users
UPDATE `users` SET `raw_password` = 'staff123' WHERE `role_id` = 2 AND `raw_password` IS NULL;

-- Set raw_password for student users from admission_no
UPDATE `users` u INNER JOIN `students` s ON u.id = s.user_id
SET u.raw_password = s.admission_no
WHERE u.role_id = 3 AND u.raw_password IS NULL;

-- ============================================================================
-- 9. CREATE TABLE: staff_offer_letters  (from 2026_07_02_100000)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `staff_offer_letters` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `offer_letter_no` VARCHAR(255) NOT NULL,
    `designation` VARCHAR(255) NOT NULL,
    `department` VARCHAR(255) NULL,
    `offered_salary` DECIMAL(10,2) NOT NULL,
    `joining_date` DATE NOT NULL,
    `valid_until` DATE NULL,
    `file_path` VARCHAR(255) NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
    `remarks` TEXT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    UNIQUE KEY `staff_offer_letters_offer_letter_no_unique` (`offer_letter_no`),
    CONSTRAINT `staff_offer_letters_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 10. CREATE TABLE: leave_applications  (from 2026_07_02_100000)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `leave_applications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `leave_type` VARCHAR(255) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `total_days` INT NULL,
    `reason` TEXT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'Pending',
    `admin_remarks` TEXT NULL,
    `attachment_path` VARCHAR(255) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `leave_applications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 11. CREATE TABLE: student_milestones  (from 2026_07_02_100000)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `student_milestones` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `course_id` BIGINT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `milestone_type` VARCHAR(255) NOT NULL DEFAULT 'Milestone',
    `target_date` DATE NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'Upcoming',
    `priority` VARCHAR(255) NOT NULL DEFAULT 'Medium',
    `source` VARCHAR(255) NOT NULL DEFAULT 'Syllabus',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `student_milestones_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `student_milestones_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 12. CREATE TABLE: staff_income_records  (from 2026_07_02_100000)
-- ============================================================================
CREATE TABLE IF NOT EXISTS `staff_income_records` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id` BIGINT UNSIGNED NOT NULL,
    `income_type` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `payment_method` VARCHAR(255) NULL,
    `reference_no` VARCHAR(255) NULL,
    `description` TEXT NULL,
    `income_date` DATE NOT NULL,
    `status` VARCHAR(255) NOT NULL DEFAULT 'Received',
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `staff_income_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- 13. ADD COLUMNS: users (firebase fields)  (from 2026_07_02_120000)
-- ============================================================================
SET @tablename = 'users';

SET @columnname = 'phone_number';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `users` ADD `phone_number` VARCHAR(255) NULL AFTER `email`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'is_phone_verified';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `users` ADD `is_phone_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `phone_number`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'firebase_uid';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `users` ADD `firebase_uid` VARCHAR(255) NULL AFTER `is_phone_verified`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnname = 'phone_verified_at';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    'ALTER TABLE `users` ADD `phone_verified_at` TIMESTAMP NULL AFTER `firebase_uid`'
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- DONE! All missing tables and columns have been created.
-- ============================================================================
SELECT 'All migrations applied successfully!' AS result;
