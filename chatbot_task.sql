-- ============================================================
--  Chatbot, Tasks, & Daily Updates - SQL Migration Script
--  Run this in phpMyAdmin if not running migrations
-- ============================================================

-- 1. Tasks Table
CREATE TABLE IF NOT EXISTS `tasks` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`          VARCHAR(255)    NOT NULL,
    `description`    TEXT            NULL,
    `assigned_to`    BIGINT          NOT NULL, -- points to employees.id
    `priority`       ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
    `status`         ENUM('Pending','In Progress','Completed') NOT NULL DEFAULT 'Pending',
    `due_date`       DATE            NULL DEFAULT NULL,
    `created_by`     BIGINT UNSIGNED NULL DEFAULT NULL, -- points to users.id
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    `deleted_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_tasks_assigned_to` (`assigned_to`),
    INDEX `idx_tasks_status` (`status`),
    INDEX `idx_tasks_priority` (`priority`),
    INDEX `idx_tasks_due_date` (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Daily Updates Table
CREATE TABLE IF NOT EXISTS `daily_updates` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `employee_id`    BIGINT          NOT NULL, -- points to employees.id
    `update_text`    TEXT            NOT NULL,
    `date`           DATE            NOT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_daily_updates_employee` (`employee_id`),
    INDEX `idx_daily_updates_date` (`date`),
    UNIQUE KEY `uk_daily_update_per_day` (`employee_id`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Chatbot Interactions (for history log and analytics)
CREATE TABLE IF NOT EXISTS `chatbot_interactions` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`        BIGINT UNSIGNED NULL DEFAULT NULL, -- points to users.id
    `query`          TEXT            NOT NULL,
    `response`       TEXT            NOT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_chatbot_user` (`user_id`),
    INDEX `idx_chatbot_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  NOTES:
--  • The chatbot supports 13+ query types:
--    - pending fees, new students, student stats, staff overview
--    - task summary, attendance, expenses, revenue, courses
--    - biometric status, system diagnostics, notifications, help
--  • Chatbot interactions are logged for analytics and history
--  • Tasks support priority (Low/Medium/High) and status tracking
--  • Daily updates enforce one entry per employee per day (unique key)
-- ============================================================
