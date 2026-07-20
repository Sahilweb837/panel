-- =============================================================================
-- SQL Dump File: Add Geolocation and Online Heartbeat Fields
-- Database: ccjkrdwumr (or your production/staging database)
-- =============================================================================

-- Add last_seen_at timestamp column to users table
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `last_seen_at` timestamp NULL DEFAULT NULL AFTER `status`;

-- Add Geolocation columns to employee_attendances table
ALTER TABLE `employee_attendances` 
ADD COLUMN IF NOT EXISTS `latitude` decimal(10,7) DEFAULT NULL AFTER `device_name`,
ADD COLUMN IF NOT EXISTS `longitude` decimal(10,7) DEFAULT NULL AFTER `latitude`,
ADD COLUMN IF NOT EXISTS `location_address` varchar(255) DEFAULT NULL AFTER `longitude`;
