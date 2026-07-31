-- =============================================================================
-- SQL Dump File: Create `settings` table in MySQL Database
-- Database: ccjkrdwumr (or your production/staging database)
-- =============================================================================

CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial System Settings Seed Data
INSERT IGNORE INTO `settings` (`key`, `value`, `group`, `created_at`, `updated_at`) VALUES
('institute_name', 'Netcoder Learning Institute', 'general', NOW(), NOW()),
('tagline', 'Excellence in Education & Training', 'general', NOW(), NOW()),
('contact_email', 'support@netcoder.in', 'general', NOW(), NOW()),
('contact_phone', '+91 98765 43210', 'general', NOW(), NOW()),
('address', 'Main Campus, IT Park Road, City Center', 'general', NOW(), NOW()),
('logo_url', 'images/logo.png', 'general', NOW(), NOW()),
('timezone', 'Asia/Kolkata', 'general', NOW(), NOW()),
('currency_symbol', '₹', 'financial', NOW(), NOW()),
('default_registration_fee', '500', 'financial', NOW(), NOW()),
('default_prospectus_fee', '200', 'financial', NOW(), NOW()),
('daily_late_fine', '50', 'financial', NOW(), NOW()),
('invoice_prefix', 'ADM-', 'financial', NOW(), NOW()),
('default_fee_tenure', '1 Month', 'financial', NOW(), NOW()),
('invoice_terms', '1. Fees once paid are non-refundable & non-transferable.\n2. Receipts must be presented for all official inquiries.\n3. Late fee fine applies after the due date.', 'wording', NOW(), NOW()),
('receipt_footer', 'Thank you for choosing Netcoder Learning Institute. This is a computer-generated receipt.', 'wording', NOW(), NOW()),
('salary_slip_note', 'Confidential salary slip. Issued for internal staff records.', 'wording', NOW(), NOW()),
('welcome_email_text', 'Welcome to Netcoder Fees Manager! Your student account has been created successfully.', 'wording', NOW(), NOW()),
('primary_color', '#ff5532', 'appearance', NOW(), NOW()),
('default_theme', 'light', 'appearance', NOW(), NOW()),
('font_family', 'Poppins', 'appearance', NOW(), NOW()),
('min_password_length', '6', 'security', NOW(), NOW()),
('allow_subadmin_password_reset', '1', 'security', NOW(), NOW());
