-- ============================================
-- Student Portal Feature SQL Dump
-- Tables: student_milestones, student_assignments, student_academic_records
-- Database: institute_erp
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for student_milestones
-- Represents milestones derived from course syllabus.
-- Students can VIEW these milestones (READ-ONLY).
-- --------------------------------------------------------
DROP TABLE IF EXISTS `student_milestones`;
CREATE TABLE `student_milestones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `milestone_type` varchar(255) NOT NULL DEFAULT 'Milestone',
  `target_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Upcoming',
  `priority` varchar(255) NOT NULL DEFAULT 'Medium',
  `source` varchar(255) NOT NULL DEFAULT 'Syllabus',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `student_milestones_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_milestones_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for student_assignments
-- Represents assignments, projects, labs, etc.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `student_assignments`;
CREATE TABLE `student_assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Assignment',
  `due_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `priority` varchar(255) NOT NULL DEFAULT 'Medium',
  `file_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `student_assignments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for student_academic_records
-- Represents exam results and grades.
-- --------------------------------------------------------
DROP TABLE IF EXISTS `student_academic_records`;
CREATE TABLE `student_academic_records` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `student_id` bigint(20) unsigned NOT NULL,
  `exam_type` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `marks` decimal(5,2) DEFAULT NULL,
  `max_marks` decimal(5,2) NOT NULL DEFAULT 100.00,
  `grade` varchar(255) DEFAULT NULL,
  `result_status` varchar(255) NOT NULL DEFAULT 'Pass',
  `file_path` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `session` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `student_academic_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
