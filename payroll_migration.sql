-- ============================================================
--  Payroll & Razorpay Integration - SQL Migration Script
--  Run this in phpMyAdmin AFTER your main tables exist
--  Run in this exact order:
--    1. payroll_settings table (new)
--    2. ALTER employees (bank details)
--    3. ALTER salary_slips (payout tracking)
-- ============================================================

-- 1. Payroll Settings (Razorpay credentials)
CREATE TABLE IF NOT EXISTS `payroll_settings` (
    `id`                     INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `razorpay_key_id`        VARCHAR(100)    NULL DEFAULT NULL,
    `razorpay_key_secret`    TEXT            NULL DEFAULT NULL,
    `razorpay_account_number` VARCHAR(50)   NULL DEFAULT NULL,
    `mode`                   ENUM('test','live') NOT NULL DEFAULT 'test',
    `created_at`             TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`             TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default empty row so settings page always has a record
INSERT INTO `payroll_settings` (`mode`, `created_at`, `updated_at`)
SELECT 'test', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM `payroll_settings`);

-- ============================================================

-- 2. Add bank detail columns to employees table
ALTER TABLE `employees`
    ADD COLUMN IF NOT EXISTS `bank_account_no`          VARCHAR(30)  NULL DEFAULT NULL AFTER `biometric_id`,
    ADD COLUMN IF NOT EXISTS `bank_ifsc`                VARCHAR(15)  NULL DEFAULT NULL AFTER `bank_account_no`,
    ADD COLUMN IF NOT EXISTS `bank_name`                VARCHAR(100) NULL DEFAULT NULL AFTER `bank_ifsc`,
    ADD COLUMN IF NOT EXISTS `account_holder_name`      VARCHAR(150) NULL DEFAULT NULL AFTER `bank_name`,
    ADD COLUMN IF NOT EXISTS `razorpay_contact_id`      VARCHAR(100) NULL DEFAULT NULL AFTER `account_holder_name`,
    ADD COLUMN IF NOT EXISTS `razorpay_fund_account_id` VARCHAR(100) NULL DEFAULT NULL AFTER `razorpay_contact_id`;

-- ============================================================

-- 3. Add payout tracking columns to salary_slips table
ALTER TABLE `salary_slips`
    ADD COLUMN IF NOT EXISTS `razorpay_payout_id`   VARCHAR(100) NULL DEFAULT NULL AFTER `status`,
    ADD COLUMN IF NOT EXISTS `payout_status`         VARCHAR(50)  NULL DEFAULT NULL AFTER `razorpay_payout_id`,
    ADD COLUMN IF NOT EXISTS `payout_mode`           VARCHAR(20)  NULL DEFAULT NULL AFTER `payout_status`,
    ADD COLUMN IF NOT EXISTS `payout_initiated_at`   TIMESTAMP    NULL DEFAULT NULL AFTER `payout_mode`,
    ADD COLUMN IF NOT EXISTS `payout_response`       TEXT         NULL DEFAULT NULL AFTER `payout_initiated_at`;

-- ============================================================
--  Done! Payroll tables are ready.
--  Next: Install Razorpay PHP SDK from the app directory:
--    composer require razorpay/razorpay
-- ============================================================
