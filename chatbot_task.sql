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
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Daily Updates Table
CREATE TABLE IF NOT EXISTS `daily_updates` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `employee_id`    BIGINT          NOT NULL, -- points to employees.id
    `update_text`    TEXT            NOT NULL,
    `date`           DATE            NOT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Chatbot Interactions (for history log and learning dashboard)
CREATE TABLE IF NOT EXISTS `chatbot_interactions` (
    `id`             INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`        BIGINT UNSIGNED NULL DEFAULT NULL, -- points to users.id
    `query`          TEXT            NOT NULL,
    `response`       TEXT            NOT NULL,
    `created_at`     TIMESTAMP       NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
