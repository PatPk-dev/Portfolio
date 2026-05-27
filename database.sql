-- คำสั่ง SQL สำหรับสร้างฐานข้อมูลและตารางจัดการ Portfolio
-- สามารถคัดลอกคำสั่งทั้งหมดไปรันในเครื่องมือ phpMyAdmin ของ XAMPP ได้ทันที

CREATE DATABASE IF NOT EXISTS `portfolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio`;

-- 1. สร้างตารางจัดเก็บข้อมูลโปรเจกต์
CREATE TABLE IF NOT EXISTS `projects` (
    `id` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `original_url` VARCHAR(1000) NOT NULL,
    `image_url` LONGTEXT NULL,
    `status` VARCHAR(50) DEFAULT 'published',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
