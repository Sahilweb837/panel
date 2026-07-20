-- =============================================================================
-- SQL Dump File: Create Course Milestones & Syllabus Coverage Table
-- Database: ccjkrdwumr (or your production/staging database)
-- =============================================================================

CREATE TABLE IF NOT EXISTS `course_milestones` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `milestone_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT 0,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL,
  `covered_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_milestones_course_id_foreign` (`course_id`),
  CONSTRAINT `course_milestones_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Seed Data for Course Milestones
INSERT INTO `course_milestones` (`course_id`, `milestone_title`, `description`, `order_index`, `is_completed`, `completed_at`, `covered_by`, `created_at`, `updated_at`) 
SELECT id, 'HTML5 & CSS3 Responsive Layouts', 'Building semantic HTML5 pages and flexbox/grid layouts.', 1, 1, NOW(), 'Lead Instructor', NOW(), NOW() FROM `courses` LIMIT 1;

INSERT INTO `course_milestones` (`course_id`, `milestone_title`, `description`, `order_index`, `is_completed`, `completed_at`, `covered_by`, `created_at`, `updated_at`) 
SELECT id, 'JavaScript ES6 & Async Fetch API', 'Mastering modern JS features, DOM manipulation and AJAX.', 2, 1, NOW(), 'Lead Instructor', NOW(), NOW() FROM `courses` LIMIT 1;

INSERT INTO `course_milestones` (`course_id`, `milestone_title`, `description`, `order_index`, `is_completed`, `completed_at`, `covered_by`, `created_at`, `updated_at`) 
SELECT id, 'Backend Fundamentals & Database SQL', 'Setting up REST APIs, MySQL relationships, and ORM.', 3, 0, NULL, NULL, NOW(), NOW() FROM `courses` LIMIT 1;
