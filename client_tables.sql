-- ============================================================
--  Client Management Module - SQL Table Creation Script
--  Run this in phpMyAdmin or MySQL to create the tables
-- ============================================================

-- 1. Create clients table
CREATE TABLE IF NOT EXISTS `clients` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150)    NOT NULL,
    `company`    VARCHAR(200)    NULL DEFAULT NULL,
    `email`      VARCHAR(150)    NULL DEFAULT NULL,
    `phone`      VARCHAR(20)     NULL DEFAULT NULL,
    `address`    TEXT            NULL DEFAULT NULL,
    `gst_no`     VARCHAR(20)     NULL DEFAULT NULL,
    `pan_no`     VARCHAR(15)     NULL DEFAULT NULL,
    `status`     ENUM('active','inactive') NOT NULL DEFAULT 'active',
    `notes`      TEXT            NULL DEFAULT NULL,
    `created_at` TIMESTAMP       NULL DEFAULT NULL,
    `updated_at` TIMESTAMP       NULL DEFAULT NULL,
    `deleted_at` TIMESTAMP       NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================

-- 2. Create client_invoices table
CREATE TABLE IF NOT EXISTS `client_invoices` (
    `id`             BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `client_id`      BIGINT UNSIGNED  NOT NULL,
    `invoice_no`     VARCHAR(60)      NOT NULL,
    `invoice_items`  JSON             NULL DEFAULT NULL,
    `subtotal`       DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
    `tax_percent`    DECIMAL(5,2)     NOT NULL DEFAULT '0.00',
    `tax_amount`     DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
    `discount`       DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
    `total_amount`   DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
    `paid_amount`    DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
    `due_amount`     DECIMAL(12,2)    NOT NULL DEFAULT '0.00',
    `status`         ENUM('Paid','Partial','Unpaid') NOT NULL DEFAULT 'Unpaid',
    `due_date`       DATE             NULL DEFAULT NULL,
    `payment_date`   DATE             NULL DEFAULT NULL,
    `payment_method` VARCHAR(50)      NULL DEFAULT NULL,
    `transaction_id` VARCHAR(100)     NULL DEFAULT NULL,
    `notes`          TEXT             NULL DEFAULT NULL,
    `created_by`     BIGINT UNSIGNED  NULL DEFAULT NULL,
    `created_at`     TIMESTAMP        NULL DEFAULT NULL,
    `updated_at`     TIMESTAMP        NULL DEFAULT NULL,
    `deleted_at`     TIMESTAMP        NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `client_invoices_invoice_no_unique` (`invoice_no`),
    KEY `client_invoices_client_id_foreign` (`client_id`),
    CONSTRAINT `client_invoices_client_id_foreign`
        FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Done! Both tables are now ready to use.
-- ============================================================
