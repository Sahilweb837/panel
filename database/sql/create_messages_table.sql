-- =============================================================================
-- SQL Dump File: Create `messages` table in MySQL Database
-- Database: ccjkrdwumr (or your production/staging database)
-- =============================================================================

CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_role` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` enum('Normal','Important','Urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Normal',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `messages_receiver_role_index` (`receiver_role`),
  KEY `messages_is_read_index` (`is_read`),
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional Welcome Notice Seed Data
INSERT IGNORE INTO `messages` (`sender_id`, `receiver_id`, `receiver_role`, `subject`, `body`, `priority`, `is_read`, `created_at`, `updated_at`) VALUES
(1, NULL, 'all', 'Welcome to Internal Notice & Messaging Center', 'Hello everyone! You can now send direct messages, staff reports, student inquiries, and broadcast notices directly within the Fees Manager system.', 'Important', 0, NOW(), NOW());
