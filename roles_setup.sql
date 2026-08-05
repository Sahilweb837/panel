-- ============================================
-- SQL Script: Setup and Fix Roles
-- Database: institute_erp
-- ============================================

-- 1. Create roles table if it doesn't exist
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_role_name_unique` (`role_name`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insert standard roles if they do not exist
INSERT INTO `roles` (`role_name`, `slug`, `description`, `created_at`, `updated_at`)
VALUES 
  ('Super Admin', 'super-admin', 'Full system administrator.', NOW(), NOW()),
  ('Root Admin', 'root-admin', 'Root-level institute administrator.', NOW(), NOW()),
  ('Admin', 'admin', 'Institute operations administrator.', NOW(), NOW()),
  ('Staff', 'staff', 'Teaching or office staff.', NOW(), NOW()),
  ('Student', 'student', 'Enrolled student.', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
  `description` = VALUES(`description`),
  `updated_at` = NOW();

-- 3. Verify roles inserted successfully
SELECT * FROM `roles`;
