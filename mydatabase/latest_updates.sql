-- Update SQL Script for Cloudways Database
-- This file contains the latest database schema updates to fix the "Unknown column 'fee_tenure'" error.
-- You can import this file directly into your PHPMyAdmin on the live server.

-- 1. Add the missing 'fee_tenure' column to the students table
ALTER TABLE `students` ADD COLUMN `fee_tenure` VARCHAR(50) NULL AFTER `prospectus_fee`;

-- Note: If you encounter any other missing columns related to recent fee features, 
-- they were likely from the previous migrations. Just in case, here is the SQL for them (commented out):
-- ALTER TABLE `students` ADD COLUMN `discount` DECIMAL(10,2) DEFAULT '0.00' AFTER `biometric_id`;
-- ALTER TABLE `students` ADD COLUMN `registration_fee` DECIMAL(10,2) DEFAULT '0.00' AFTER `discount`;
-- ALTER TABLE `students` ADD COLUMN `prospectus_fee` DECIMAL(10,2) DEFAULT '0.00' AFTER `registration_fee`;
