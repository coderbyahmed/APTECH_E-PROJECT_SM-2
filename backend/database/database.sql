-- ============================================
-- SOUND Group — Core PHP Application
-- Complete Database Structure
-- ============================================
-- Create Database
CREATE DATABASE IF NOT EXISTS `sound_management` DEFAULT CHARACTER
SET
    utf8mb4 DEFAULT COLLATE utf8mb4_unicode_ci;

USE `sound_management`;

-- -------------------------------------------
-- Admin Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `admin` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
        `remember_token` VARCHAR(100) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `admin_email_unique` (`email`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Password Reset OTPs Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `password_reset_otps` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `admin_id` BIGINT UNSIGNED NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `otp_hash` VARCHAR(255) NOT NULL,
        `expires_at` BIGINT NOT NULL,
        `verified_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `password_reset_otps_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Email Change OTPs Table
-- (4-digit OTPs for changing the admin email address)
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `email_change_otps` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `admin_id` BIGINT UNSIGNED NOT NULL,
        `new_email` VARCHAR(255) NOT NULL,
        `otp_hash` VARCHAR(255) NOT NULL,
        `expires_at` BIGINT NOT NULL,
        `verified_at` TIMESTAMP NULL DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `email_change_otps_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Default Admin Account
-- Password: Admin@12345
-- -------------------------------------------
INSERT INTO
    `admin` (
        `name`,
        `email`,
        `password`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        'Muhammad Ahmed',
        'ahmedazizkhan405@gmail.com',
        '$2y$12$tR0nZ0.oJypoEA8Dzum6Cu0GP8zA.zPjL6B03Yfbc7LsDz.K9oUF6',
        NOW (),
        NOW ()
    );

-- -------------------------------------------
-- Users Table (Website Registered Users)
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `users` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` VARCHAR(10) NOT NULL,
        `profile_image` VARCHAR(500) DEFAULT NULL,
        `full_name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `address` VARCHAR(500) DEFAULT NULL,
        `password` VARCHAR(255) NOT NULL,
        `status` ENUM ('active', 'inactive') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        `last_login` TIMESTAMP NULL DEFAULT NULL,
        `last_logout` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `users_user_id_unique` (`user_id`),
        UNIQUE KEY `users_email_unique` (`email`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ============================================
-- Category Management Tables
-- ============================================
-- -------------------------------------------
-- AIR (Year) Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `air` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `air_name_unique` (`name`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Artists Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `artists` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `artists_name_unique` (`name`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Albums Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `albums` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `albums_name_unique` (`name`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Genres Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `genres` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `genres_name_unique` (`name`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Languages Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `languages` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `languages_name_unique` (`name`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Music Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `music` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `song_title` VARCHAR(255) NOT NULL,
        `artist_id` BIGINT UNSIGNED DEFAULT NULL,
        `album_id` BIGINT UNSIGNED DEFAULT NULL,
        `year_id` BIGINT UNSIGNED DEFAULT NULL,
        `genre_id` BIGINT UNSIGNED DEFAULT NULL,
        `language_id` BIGINT UNSIGNED DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `music_file` VARCHAR(500) DEFAULT NULL,
        `cover_image` VARCHAR(500) DEFAULT NULL,
        `status` ENUM ('active', 'draft', 'inactive') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `music_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
        CONSTRAINT `music_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL,
        CONSTRAINT `music_year_id_foreign` FOREIGN KEY (`year_id`) REFERENCES `air` (`id`) ON DELETE SET NULL,
        CONSTRAINT `music_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL,
        CONSTRAINT `music_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Videos Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `videos` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `video_title` VARCHAR(255) NOT NULL,
        `artist_id` BIGINT UNSIGNED DEFAULT NULL,
        `album_id` BIGINT UNSIGNED DEFAULT NULL,
        `year_id` BIGINT UNSIGNED DEFAULT NULL,
        `genre_id` BIGINT UNSIGNED DEFAULT NULL,
        `language_id` BIGINT UNSIGNED DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `video_path` VARCHAR(500) DEFAULT NULL,
        `thumbnail_path` VARCHAR(500) DEFAULT NULL,
        `status` ENUM ('active', 'draft', 'inactive') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `videos_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
        CONSTRAINT `videos_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL,
        CONSTRAINT `videos_year_id_foreign` FOREIGN KEY (`year_id`) REFERENCES `air` (`id`) ON DELETE SET NULL,
        CONSTRAINT `videos_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL,
        CONSTRAINT `videos_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Reviews Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `reviews` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `user_id` BIGINT UNSIGNED NOT NULL,
        `music_id` BIGINT UNSIGNED DEFAULT NULL,
        `video_id` BIGINT UNSIGNED DEFAULT NULL,
        `rating` TINYINT UNSIGNED NOT NULL,
        `review_text` TEXT NOT NULL,
        `status` ENUM ('published', 'hidden') NOT NULL DEFAULT 'published',
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
        CONSTRAINT `reviews_music_id_foreign` FOREIGN KEY (`music_id`) REFERENCES `music` (`id`) ON DELETE CASCADE,
        CONSTRAINT `reviews_video_id_foreign` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Contact Messages Table
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `contact_messages` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        `full_name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(30) DEFAULT NULL,
        `inquiry_type` VARCHAR(50) NOT NULL,
        `subject` VARCHAR(255) NOT NULL,
        `message` TEXT NOT NULL,
        `profile_image` VARCHAR(500) DEFAULT NULL,
        `is_read` TINYINT (1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- -------------------------------------------
-- Website Settings Table
-- Single-row configuration table for site-wide info
-- -------------------------------------------
CREATE TABLE
    IF NOT EXISTS `website_settings` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `website_name` VARCHAR(255) NOT NULL DEFAULT 'SOUND Group',
        `site_logo` VARCHAR(500) DEFAULT NULL,
        `contact_email` VARCHAR(255) DEFAULT NULL,
        `contact_phone` VARCHAR(50) DEFAULT NULL,
        `contact_address` TEXT DEFAULT NULL,
        `facebook_url` VARCHAR(500) DEFAULT NULL,
        `tiktok_url` VARCHAR(500) DEFAULT NULL,
        `linkedin_url` VARCHAR(500) DEFAULT NULL,
        `github_url` VARCHAR(500) DEFAULT NULL,
        `footer_description` TEXT DEFAULT NULL,
        `copyright_text` VARCHAR(500) DEFAULT NULL,
        `created_at` TIMESTAMP NULL DEFAULT NULL,
        `updated_at` TIMESTAMP NULL DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Seed website settings with default values
INSERT INTO
    `website_settings` (
        `website_name`,
        `site_logo`,
        `contact_email`,
        `contact_phone`,
        `contact_address`,
        `facebook_url`,
        `tiktok_url`,
        `linkedin_url`,
        `github_url`,
        `footer_description`,
        `copyright_text`,
        `created_at`,
        `updated_at`
    )
VALUES
    (
        'SOUND Group',
        NULL,
        'info@soundgroup.com',
        '+92 317 849 7732',
        'Pakistan',
        'https://www.facebook.com/soundgroup',
        'https://www.tiktok.com/@soundgroup',
        'https://www.linkedin.com/company/soundgroup',
        'https://github.com/soundgroup',
        'Discover music, videos, artists and more — all in one place. Your ultimate destination for streaming and exploring sound.',
        '&copy; 2026 SOUND Group. All rights reserved.',
        NOW (),
        NOW ()
    );