-- ============================================================
-- STEP 1: Create training_courses table (run this first)
-- ============================================================
CREATE TABLE IF NOT EXISTS `training_courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '28 Days',
  `fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tenure_1_month` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tenure_3_months` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tenure_6_months` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tenure_12_months` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `training_courses_name_unique` (`name`),
  KEY `training_courses_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- STEP 2: Update trainings table - replace courses FK with training_courses
-- ============================================================
-- First drop the old foreign key if it exists
SET @constraint_name = (
    SELECT CONSTRAINT_NAME
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'course_id'
      AND REFERENCED_TABLE_NAME IS NOT NULL
    LIMIT 1
);

SET @sql = IF(
    @constraint_name IS NOT NULL,
    CONCAT('ALTER TABLE `trainings` DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add training_course_id column if not exists
SET @col_exists = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'training_course_id'
);

SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `trainings` ADD COLUMN `training_course_id` bigint(20) unsigned DEFAULT NULL AFTER `mobile`, ADD KEY `trainings_tcourse_id_foreign` (`training_course_id`), ADD CONSTRAINT `trainings_tcourse_id_foreign` FOREIGN KEY (`training_course_id`) REFERENCES `training_courses` (`id`) ON DELETE SET NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop old course_id column if exists
SET @col_exists2 = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'course_id'
);

SET @sql = IF(
    @col_exists2 > 0,
    'ALTER TABLE `trainings` DROP INDEX `trainings_course_id_foreign`, DROP COLUMN `course_id`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Make course_name NOT NULL in trainings
SET @col_exists3 = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'course_name'
      AND IS_NULLABLE = 'YES'
);

SET @sql = IF(
    @col_exists3 > 0,
    'ALTER TABLE `trainings` MODIFY COLUMN `course_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add payment_method column if missing
SET @col_exists4 = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'payment_method'
);

SET @sql = IF(
    @col_exists4 = 0,
    'ALTER TABLE `trainings` ADD COLUMN `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'Cash\' AFTER `fees`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add upi_transaction_id column if missing
SET @col_exists5 = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'upi_transaction_id'
);

SET @sql = IF(
    @col_exists5 = 0,
    'ALTER TABLE `trainings` ADD COLUMN `upi_transaction_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL AFTER `payment_method`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add status column if missing
SET @col_exists6 = (
    SELECT COUNT(*)
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND COLUMN_NAME = 'status'
);

SET @sql = IF(
    @col_exists6 = 0,
    'ALTER TABLE `trainings` ADD COLUMN `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT \'Unpaid\' AFTER `payment_date`',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add slip_no unique if missing
SET @idx_exists = (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'trainings'
      AND INDEX_NAME = 'slip_no'
);

SET @sql = IF(
    @idx_exists = 0,
    'ALTER TABLE `trainings` ADD UNIQUE KEY `slip_no` (`slip_no`)',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;