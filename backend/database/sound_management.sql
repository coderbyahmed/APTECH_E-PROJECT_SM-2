-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2026 at 03:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sound_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `profile_image`, `address`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad Ahmed', 'ahmedazizkhan405@gmail.com', '$2y$12$tR0nZ0.oJypoEA8Dzum6Cu0GP8zA.zPjL6B03Yfbc7LsDz.K9oUF6', '/Aptech_E_Project_02/sound_management/uploads/admin-profile-image/admin_4c70d1caca1fb83d_1787911008.jpg', 'karachi', NULL, NULL, '2026-08-27 09:49:36', '2026-08-28 09:56:48');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_logs`
--

CREATE TABLE `admin_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `action` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `item_name` varchar(500) NOT NULL DEFAULT '',
  `item_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_activity_logs`
--

INSERT INTO `admin_activity_logs` (`id`, `admin_id`, `admin_name`, `action`, `module`, `item_name`, `item_id`, `created_at`) VALUES
(3, 1, 'Muhammad Ahmed', 'updated', 'user', 'hammad', 2, '2026-08-28 20:24:55'),
(4, 1, 'Muhammad Ahmed', 'created', 'artist', 'Shreya Ghoshal', 2, '2026-08-29 18:52:21'),
(5, 1, 'Muhammad Ahmed', 'created', 'artist', 'Armaan Malik', 3, '2026-08-29 18:52:41'),
(6, 1, 'Muhammad Ahmed', 'created', 'artist', 'Sonu Nigam', 4, '2026-08-29 18:53:01'),
(7, 1, 'Muhammad Ahmed', 'created', 'artist', 'Neha Kakkar', 5, '2026-08-29 18:53:35'),
(8, 1, 'Muhammad Ahmed', 'created', 'artist', 'Jubin Nautiyal', 6, '2026-08-29 18:54:12'),
(9, 1, 'Muhammad Ahmed', 'created', 'artist', 'Vishal Mishra', 7, '2026-08-29 18:54:39'),
(10, 1, 'Muhammad Ahmed', 'created', 'artist', 'Anuv Jain', 8, '2026-08-29 18:55:03'),
(11, 1, 'Muhammad Ahmed', 'created', 'artist', 'Karan Aujla', 9, '2026-08-29 18:55:36'),
(12, 1, 'Muhammad Ahmed', 'created', 'artist', 'Sid Sriram', 10, '2026-08-29 18:55:58'),
(13, 1, 'Muhammad Ahmed', 'created', 'artist', 'Badshah', 11, '2026-08-29 18:56:56'),
(14, 1, 'Muhammad Ahmed', 'created', 'artist', 'Jass Manak', 12, '2026-08-29 18:57:28'),
(15, 1, 'Muhammad Ahmed', 'created', 'artist', 'Darshan Raval', 13, '2026-08-29 18:57:57'),
(16, 1, 'Muhammad Ahmed', 'created', 'artist', 'Amit Trivedi', 14, '2026-08-29 18:58:21'),
(17, 1, 'Muhammad Ahmed', 'created', 'artist', 'A. R. Rahman', 15, '2026-08-29 18:58:46'),
(18, 1, 'Muhammad Ahmed', 'created', 'year', '2021', 3, '2026-08-29 19:02:34'),
(19, 1, 'Muhammad Ahmed', 'created', 'year', '2018', 4, '2026-08-29 19:12:36'),
(20, 1, 'Muhammad Ahmed', 'created', 'album', 'Baazaar', 2, '2026-08-29 19:13:17'),
(21, 1, 'Muhammad Ahmed', 'created', 'genre', 'Bollywood / Sad', 2, '2026-08-29 19:13:34'),
(22, 1, 'Muhammad Ahmed', 'created', 'music', 'Chhod Diya', 2, '2026-08-29 19:57:54'),
(23, 1, 'Muhammad Ahmed', 'created', 'year', '2014', 5, '2026-08-29 20:10:54'),
(24, 1, 'Muhammad Ahmed', 'created', 'album', 'CityLights', 3, '2026-08-29 20:11:24'),
(25, 1, 'Muhammad Ahmed', 'created', 'genre', 'Romantic / Bollywood', 3, '2026-08-29 20:11:53'),
(26, 1, 'Muhammad Ahmed', 'created', 'music', 'Muskurane Ki Wajah Tum Ho', 3, '2026-08-29 20:13:50'),
(27, 1, 'Muhammad Ahmed', 'updated', 'genre', 'Romantic/Bollywood', 3, '2026-08-29 20:14:25'),
(28, 1, 'Muhammad Ahmed', 'created', 'album', 'Aashiqui 2', 4, '2026-08-29 20:24:16'),
(29, 1, 'Muhammad Ahmed', 'created', 'music', 'Tum Hi Ho', 4, '2026-08-29 20:26:00'),
(30, 1, 'Muhammad Ahmed', 'deleted', 'music', 'Tum Hi ho', 1, '2026-08-29 20:26:17'),
(31, 1, 'Muhammad Ahmed', 'created', 'genre', 'Pop / Hip-Hop', 4, '2026-08-29 20:33:12'),
(32, 1, 'Muhammad Ahmed', 'created', 'album', 'DJ Waley Babu', 5, '2026-08-29 20:33:29'),
(33, 1, 'Muhammad Ahmed', 'created', 'year', '2015', 6, '2026-08-29 20:33:49'),
(34, 1, 'Muhammad Ahmed', 'created', 'music', 'DJ Waley Babu', 5, '2026-08-29 20:34:52'),
(35, 1, 'Muhammad Ahmed', 'created', 'genre', 'Indie Folk / Acoustic', 5, '2026-08-29 20:41:17'),
(36, 1, 'Muhammad Ahmed', 'created', 'album', 'Baarishein', 6, '2026-08-29 20:41:55'),
(37, 1, 'Muhammad Ahmed', 'created', 'music', 'Baarishein', 6, '2026-08-29 20:42:58'),
(38, 1, 'Muhammad Ahmed', 'created', 'album', 'Hamari Adhuri Kahani', 7, '2026-08-29 20:47:40'),
(39, 1, 'Muhammad Ahmed', 'created', 'music', 'Han Hasi Ban Gaye', 7, '2026-08-29 20:51:05'),
(40, 1, 'Muhammad Ahmed', 'created', 'genre', 'Pop / Romantic Pop', 6, '2026-08-29 20:57:38'),
(41, 1, 'Muhammad Ahmed', 'created', 'album', 'I Loved You', 8, '2026-08-29 20:57:51'),
(42, 1, 'Muhammad Ahmed', 'created', 'year', '2025', 7, '2026-08-29 20:58:04'),
(43, 1, 'Muhammad Ahmed', 'created', 'music', 'Nafrat', 8, '2026-08-29 21:00:53'),
(44, 1, 'Muhammad Ahmed', 'created', 'album', 'All Is Well', 9, '2026-08-29 21:03:55'),
(45, 1, 'Muhammad Ahmed', 'created', 'music', 'Baaton Ko Teri', 9, '2026-08-29 21:06:47'),
(46, 1, 'Muhammad Ahmed', 'created', 'language', 'Punjabi', 2, '2026-08-29 21:12:07'),
(47, 1, 'Muhammad Ahmed', 'created', 'album', 'Punjabi Pop / Bhangra', 10, '2026-08-29 21:12:38'),
(48, 1, 'Muhammad Ahmed', 'created', 'album', 'P-POP CULTURE', 11, '2026-08-29 21:12:54'),
(49, 1, 'Muhammad Ahmed', 'created', 'genre', 'Punjabi Pop / Bhangra', 7, '2026-08-29 21:13:07'),
(50, 1, 'Muhammad Ahmed', 'deleted', 'album', 'Punjabi Pop / Bhangra', 10, '2026-08-29 21:13:13'),
(51, 1, 'Muhammad Ahmed', 'created', 'music', 'Boyfriend', 10, '2026-08-29 21:14:24'),
(52, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:25:03'),
(53, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:25:46'),
(54, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:26:18'),
(55, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:26:47'),
(56, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:30:22'),
(57, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:34:53'),
(58, 1, 'Muhammad Ahmed', 'updated', 'music', 'Boyfriend', 10, '2026-08-29 21:41:13'),
(59, 1, 'Muhammad Ahmed', 'updated', 'music', 'Baaton Ko Teri', 9, '2026-08-29 21:42:02'),
(60, 1, 'Muhammad Ahmed', 'updated', 'music', 'Nafrat', 8, '2026-08-29 21:42:27'),
(61, 1, 'Muhammad Ahmed', 'updated', 'music', 'Han Hasi Ban Gaye', 7, '2026-08-29 21:42:40'),
(62, 1, 'Muhammad Ahmed', 'updated', 'music', 'Baarishein', 6, '2026-08-29 21:42:54'),
(63, 1, 'Muhammad Ahmed', 'updated', 'music', 'DJ Waley Babu', 5, '2026-08-29 21:43:07'),
(64, 1, 'Muhammad Ahmed', 'updated', 'music', 'Tum Hi Ho', 4, '2026-08-29 21:43:20'),
(65, 1, 'Muhammad Ahmed', 'updated', 'music', 'Muskurane Ki Wajah Tum Ho', 3, '2026-08-29 21:43:32'),
(66, 1, 'Muhammad Ahmed', 'updated', 'music', 'Chhod Diya', 2, '2026-08-29 21:43:46'),
(67, 1, 'Muhammad Ahmed', 'created', 'album', 'Saiyaara', 12, '2026-08-29 21:47:40'),
(68, 1, 'Muhammad Ahmed', 'created', 'genre', 'Romantic / Bollywood Pop', 8, '2026-08-29 21:48:01'),
(69, 1, 'Muhammad Ahmed', 'created', 'music', 'Tum Ho Toh', 11, '2026-08-29 21:50:27'),
(70, 1, 'Muhammad Ahmed', 'updated', 'music', 'Tum Ho Toh', 11, '2026-08-29 21:50:46'),
(71, 1, 'Muhammad Ahmed', 'created', 'album', 'Ek Deewane Ki Deewaniyat', 13, '2026-08-29 22:02:59'),
(72, 1, 'Muhammad Ahmed', 'created', 'music', 'Deewaniyat', 12, '2026-08-29 22:04:08'),
(73, 1, 'Muhammad Ahmed', 'created', 'music', 'Deewaniyat', 13, '2026-08-29 22:04:09'),
(74, 1, 'Muhammad Ahmed', 'deleted', 'music', 'Deewaniyat', 12, '2026-08-29 22:04:49'),
(75, 1, 'Muhammad Ahmed', 'updated', 'music', 'Deewaniyat', 13, '2026-08-29 22:05:17'),
(76, 1, 'Muhammad Ahmed', 'deleted', 'artist', 'Sonu Nigam', 4, '2026-08-29 22:06:38'),
(77, 1, 'Muhammad Ahmed', 'created', 'music', 'Barbaad', 14, '2026-08-29 22:12:09'),
(78, 1, 'Muhammad Ahmed', 'updated', 'music', 'Barbaad', 14, '2026-08-29 22:12:23'),
(79, 1, 'Muhammad Ahmed', 'created', 'year', '2019', 8, '2026-08-29 22:16:36'),
(80, 1, 'Muhammad Ahmed', 'created', 'album', 'Prada', 14, '2026-08-29 22:17:10'),
(81, 1, 'Muhammad Ahmed', 'created', 'music', 'Lehanga', 15, '2026-08-29 22:18:27'),
(82, 1, 'Muhammad Ahmed', 'updated', 'music', 'Lehanga', 15, '2026-08-29 22:18:40'),
(83, 1, 'Muhammad Ahmed', 'created', 'artist', 'Gurnazar', 16, '2026-08-29 22:22:26'),
(84, 1, 'Muhammad Ahmed', 'created', 'album', 'Marjaawaan', 15, '2026-08-29 22:22:38'),
(85, 1, 'Muhammad Ahmed', 'created', 'genre', 'Punjabi Pop / Romantic', 9, '2026-08-29 22:22:53'),
(86, 1, 'Muhammad Ahmed', 'created', 'music', 'Marjaawaan', 16, '2026-08-29 22:23:56'),
(87, 1, 'Muhammad Ahmed', 'updated', 'music', 'Marjaawaan', 16, '2026-08-29 22:24:08'),
(88, 1, 'Muhammad Ahmed', 'created', 'artist', 'Faheem Abdullah', 17, '2026-08-29 22:28:47'),
(89, 1, 'Muhammad Ahmed', 'created', 'music', 'Saiyaara', 17, '2026-08-29 22:31:20'),
(90, 1, 'Muhammad Ahmed', 'updated', 'music', 'Saiyaara', 17, '2026-08-29 22:31:37'),
(91, 1, 'Muhammad Ahmed', 'created', 'year', '2017', 9, '2026-08-29 22:35:03'),
(92, 1, 'Muhammad Ahmed', 'created', 'artist', 'Arijit Singh, Shashaa Tirupati', 18, '2026-08-29 22:35:42'),
(93, 1, 'Muhammad Ahmed', 'created', 'album', 'Half Girlfriend', 16, '2026-08-29 22:35:57'),
(94, 1, 'Muhammad Ahmed', 'created', 'music', 'Phir Bhi Tumko Chaahunga', 18, '2026-08-29 22:38:57'),
(95, 1, 'Muhammad Ahmed', 'updated', 'music', 'Phir Bhi Tumko Chaahunga', 18, '2026-08-29 22:39:16');

-- --------------------------------------------------------

--
-- Table structure for table `air`
--

CREATE TABLE `air` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `air`
--

INSERT INTO `air` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '2013', '2026-08-27 09:58:15', '2026-08-27 09:58:15'),
(3, '2021', '2026-08-29 10:02:34', '2026-08-29 10:02:34'),
(4, '2018', '2026-08-29 10:12:36', '2026-08-29 10:12:36'),
(5, '2014', '2026-08-29 11:10:54', '2026-08-29 11:10:54'),
(6, '2015', '2026-08-29 11:33:49', '2026-08-29 11:33:49'),
(7, '2025', '2026-08-29 11:58:04', '2026-08-29 11:58:04'),
(8, '2019', '2026-08-29 13:16:36', '2026-08-29 13:16:36'),
(9, '2017', '2026-08-29 13:35:03', '2026-08-29 13:35:03');

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'sad song', '2026-08-27 10:00:27', '2026-08-27 10:00:27'),
(2, 'Baazaar', '2026-08-29 10:13:17', '2026-08-29 10:13:17'),
(3, 'CityLights', '2026-08-29 11:11:24', '2026-08-29 11:11:24'),
(4, 'Aashiqui 2', '2026-08-29 11:24:16', '2026-08-29 11:24:16'),
(5, 'DJ Waley Babu', '2026-08-29 11:33:29', '2026-08-29 11:33:29'),
(6, 'Baarishein', '2026-08-29 11:41:55', '2026-08-29 11:41:55'),
(7, 'Hamari Adhuri Kahani', '2026-08-29 11:47:40', '2026-08-29 11:47:40'),
(8, 'I Loved You', '2026-08-29 11:57:51', '2026-08-29 11:57:51'),
(9, 'All Is Well', '2026-08-29 12:03:55', '2026-08-29 12:03:55'),
(11, 'P-POP CULTURE', '2026-08-29 12:12:54', '2026-08-29 12:12:54'),
(12, 'Saiyaara', '2026-08-29 12:47:40', '2026-08-29 12:47:40'),
(13, 'Ek Deewane Ki Deewaniyat', '2026-08-29 13:02:59', '2026-08-29 13:02:59'),
(14, 'Prada', '2026-08-29 13:17:10', '2026-08-29 13:17:10'),
(15, 'Marjaawaan', '2026-08-29 13:22:38', '2026-08-29 13:22:38'),
(16, 'Half Girlfriend', '2026-08-29 13:35:57', '2026-08-29 13:35:57');

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `artists`
--

INSERT INTO `artists` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Arijit singh', '2026-08-27 09:58:30', '2026-08-27 09:58:30'),
(2, 'Shreya Ghoshal', '2026-08-29 09:52:21', '2026-08-29 09:52:21'),
(3, 'Armaan Malik', '2026-08-29 09:52:41', '2026-08-29 09:52:41'),
(5, 'Neha Kakkar', '2026-08-29 09:53:35', '2026-08-29 09:53:35'),
(6, 'Jubin Nautiyal', '2026-08-29 09:54:12', '2026-08-29 09:54:12'),
(7, 'Vishal Mishra', '2026-08-29 09:54:39', '2026-08-29 09:54:39'),
(8, 'Anuv Jain', '2026-08-29 09:55:03', '2026-08-29 09:55:03'),
(9, 'Karan Aujla', '2026-08-29 09:55:36', '2026-08-29 09:55:36'),
(10, 'Sid Sriram', '2026-08-29 09:55:58', '2026-08-29 09:55:58'),
(11, 'Badshah', '2026-08-29 09:56:56', '2026-08-29 09:56:56'),
(12, 'Jass Manak', '2026-08-29 09:57:28', '2026-08-29 09:57:28'),
(13, 'Darshan Raval', '2026-08-29 09:57:57', '2026-08-29 09:57:57'),
(14, 'Amit Trivedi', '2026-08-29 09:58:21', '2026-08-29 09:58:21'),
(15, 'A. R. Rahman', '2026-08-29 09:58:46', '2026-08-29 09:58:46'),
(16, 'Gurnazar', '2026-08-29 13:22:26', '2026-08-29 13:22:26'),
(17, 'Faheem Abdullah', '2026-08-29 13:28:47', '2026-08-29 13:28:47'),
(18, 'Arijit Singh, Shashaa Tirupati', '2026-08-29 13:35:42', '2026-08-29 13:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `inquiry_type` varchar(50) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `full_name`, `email`, `phone`, `inquiry_type`, `subject`, `message`, `profile_image`, `is_read`, `created_at`, `updated_at`) VALUES
(4, 'ahmed', 'ahmedazizkhan405@gmail.com', '03178497732', 'feedback', 'feedback', 'hi', 'uploads/profile-img/profile_078eb074ac6ca4f7_1787825014.jpg', 0, '2026-08-28 02:04:02', '2026-08-28 02:04:02');

-- --------------------------------------------------------

--
-- Table structure for table `email_change_otps`
--

CREATE TABLE `email_change_otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `new_email` varchar(255) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` bigint(20) NOT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Sad song', '2026-08-27 09:59:33', '2026-08-27 09:59:33'),
(2, 'Bollywood / Sad', '2026-08-29 10:13:34', '2026-08-29 10:13:34'),
(3, 'Romantic/Bollywood', '2026-08-29 11:11:53', '2026-08-29 11:14:25'),
(4, 'Pop / Hip-Hop', '2026-08-29 11:33:12', '2026-08-29 11:33:12'),
(5, 'Indie Folk / Acoustic', '2026-08-29 11:41:17', '2026-08-29 11:41:17'),
(6, 'Pop / Romantic Pop', '2026-08-29 11:57:38', '2026-08-29 11:57:38'),
(7, 'Punjabi Pop / Bhangra', '2026-08-29 12:13:07', '2026-08-29 12:13:07'),
(8, 'Romantic / Bollywood Pop', '2026-08-29 12:48:01', '2026-08-29 12:48:01'),
(9, 'Punjabi Pop / Romantic', '2026-08-29 13:22:53', '2026-08-29 13:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Hindi', '2026-08-27 09:59:44', '2026-08-27 09:59:44'),
(2, 'Punjabi', '2026-08-29 12:12:07', '2026-08-29 12:12:07');

-- --------------------------------------------------------

--
-- Table structure for table `music`
--

CREATE TABLE `music` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `song_title` varchar(255) NOT NULL,
  `artist_id` bigint(20) UNSIGNED DEFAULT NULL,
  `album_id` bigint(20) UNSIGNED DEFAULT NULL,
  `year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `genre_id` bigint(20) UNSIGNED DEFAULT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `music_file` varchar(500) DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `status` enum('active','draft','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `music`
--

INSERT INTO `music` (`id`, `song_title`, `artist_id`, `album_id`, `year_id`, `genre_id`, `language_id`, `description`, `music_file`, `cover_image`, `duration`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Chhod Diya', 1, 2, 4, 2, 1, 'A soulful and melancholic Hindi song from the film Baazaar, performed by Arijit Singh. The song portrays the pain of letting go of a past relationship and moving forward.', 'uploads/music/music_454d28704c39db6f_1788001073.mpeg', 'uploads/covers/cover_c31581ff48c4359f_1788001074.jpg', '5:19', 'active', '2026-08-29 10:57:54', '2026-08-29 12:43:46'),
(3, 'Muskurane Ki Wajah Tum Ho', 1, 3, 5, 3, 1, 'A soulful romantic track by Arijit Singh from CityLights, expressing love, happiness, and the feeling of finding your reason to smile in someone special.', 'uploads/music/music_65c917705b1b2795_1788002030.mpeg', 'uploads/covers/cover_1b0bb7ee552f9fbe_1788002030.jpg', '5:34', 'active', '2026-08-29 11:13:50', '2026-08-29 12:43:32'),
(4, 'Tum Hi Ho', 1, 4, 1, 3, 1, 'A soulful romantic Bollywood classic by Arijit Singh from Aashiqui 2, expressing deep love, devotion, and the feeling that someone special is the center of one\'s life.', 'uploads/music/music_2e527fb7338ac8d3_1788002760.mpeg', 'uploads/covers/cover_d7de31aebc789b42_1788002760.jpg', '4:10', 'active', '2026-08-29 11:26:00', '2026-08-29 12:43:20'),
(5, 'DJ Waley Babu', 11, 5, 6, 4, 1, 'DJ Waley Babu is an upbeat Indian pop and hip-hop party track by Badshah and Aastha Gill. Released in 2015, the song became widely popular for its catchy beat, energetic vocals, and fun party vibe.', 'uploads/music/music_00426242ec60b56d_1788003292.mpeg', 'uploads/covers/cover_6e53547d4a688c67_1788003292.jpg', '2:35', 'active', '2026-08-29 11:34:52', '2026-08-29 12:43:07'),
(6, 'Baarishein', 8, 6, 4, 5, 1, 'Baarishein is a soothing Hindi indie song by Anuv Jain that beautifully captures the emotions of love, memories, and togetherness through gentle acoustic melodies and heartfelt lyrics.', 'uploads/music/music_e7a11910f6d555b9_1788003778.mpeg', 'uploads/covers/cover_a0afa5d34d49eac7_1788003778.jpg', '3:27', 'active', '2026-08-29 11:42:58', '2026-08-29 12:42:54'),
(7, 'Han Hasi Ban Gaye', 2, 7, 6, 3, 1, 'Hasi Ban Gaye is a beautiful romantic Bollywood song sung by Shreya Ghoshal from Hamari Adhuri Kahani. Its soulful melody and heartfelt lyrics express the feeling of finding happiness and emotional connection in someone special.', 'uploads/music/music_eaf1f4885ad63416_1788004265.mpeg', 'uploads/covers/cover_3b26043ac791fa62_1788004265.jpg', '3:12', 'active', '2026-08-29 11:51:05', '2026-08-29 12:42:40'),
(8, 'Nafrat', 13, 8, 7, 6, 1, 'Nafrat is an emotionally intense Hindi pop song by Darshan Raval that explores heartbreak, betrayal, and the painful transformation of love into resentment. The song is part of his I Loved You EP and combines soulful vocals with an emotional melody.', 'uploads/music/music_e101d1f617db7307_1788004853.mpeg', 'uploads/covers/cover_d63cc0a2faaef189_1788004853.jpg', '3:37', 'active', '2026-08-29 12:00:53', '2026-08-29 12:42:27'),
(9, 'Baaton Ko Teri', 1, 9, 6, 3, 1, 'Baaton Ko Teri is an emotional Hindi Bollywood song sung by Arijit Singh from All Is Well. The song expresses the pain of separation and the inability to forget someone you deeply love, with soulful vocals and a heartfelt melody.', 'uploads/music/music_396b71d9bb466d0c_1788005207.mpeg', 'uploads/covers/cover_b79bb3c6539f2213_1788005207.jpg', '4:06', 'active', '2026-08-29 12:06:47', '2026-08-29 12:42:02'),
(10, 'Boyfriend', 9, 11, 7, 7, 2, 'Boyfriend is an upbeat Punjabi pop track by Karan Aujla and Ikky that portrays a sweet romantic story about falling in love and finding someone who wins your heart. The song combines catchy Punjabi vocals with an energetic modern beat.', 'uploads/music/music_e0efe64ef83046b3_1788005664.mpeg', 'uploads/covers/cover_10bfe34e2928ca57_1788005664.jpg', '2:41', 'active', '2026-08-29 12:14:24', '2026-08-29 12:41:13'),
(11, 'Tum Ho Toh', 7, 12, 7, 8, 1, 'Tum Ho Toh is a soulful romantic Hindi song by Vishal Mishra from Saiyaara. The song beautifully expresses deep love and the feeling that life feels complete and meaningful when a special person is by your side.', 'uploads/music/music_8b9eb2503216ccf9_1788007827.mpeg', 'uploads/covers/cover_ab849ae6d8d51f4f_1788007827.jpg', '5:16', 'active', '2026-08-29 12:50:27', '2026-08-29 12:50:46'),
(13, 'Deewaniyat', 7, 13, 7, 3, 1, 'Deewaniyat is a soulful romantic Bollywood song by Vishal Mishra from Ek Deewane Ki Deewaniyat. The song captures intense love, longing, and the emotional feeling of being completely devoted to someone special.', 'uploads/music/music_a419ee37ad3a77c0_1788008648.mpeg', 'uploads/covers/cover_3bebe4b81ba73121_1788008649.jpg', '4:00', 'active', '2026-08-29 13:04:09', '2026-08-29 13:05:17'),
(14, 'Barbaad', 6, 12, 7, 8, 1, 'Barbaad is an emotional romantic Hindi song featuring the soulful vocals of Jubin Nautiyal and Tia. The song explores heartbreak, longing, and the pain of losing someone you deeply love, with a melodious and deeply emotional composition.', 'uploads/music/music_d07e205230053f2c_1788009129.mpeg', 'uploads/covers/cover_dfc33bd2a9bc7c19_1788009129.jpg', '3:44', 'active', '2026-08-29 13:12:09', '2026-08-29 13:12:23'),
(15, 'Lehanga', 12, 14, 8, 7, 2, 'Lehanga is a catchy Punjabi pop song by Jass Manak, known for its upbeat melody and romantic lyrics. Released in 2019, the song became widely popular for its energetic vibe and memorable hook.', 'uploads/music/music_e4f700b8c573368e_1788009507.mpeg', 'uploads/covers/cover_291fcd8cc04667a4_1788009507.jpg', '3:45', 'active', '2026-08-29 13:18:27', '2026-08-29 13:18:40'),
(16, 'Marjaawaan', 16, 15, 8, 9, 2, 'Marjaawaan is a romantic Punjabi pop song by Gurnazar, featuring soulful vocals and heartfelt lyrics about deep love, devotion, and emotional attachment.', 'uploads/music/music_f140d38a3ff9d57a_1788009836.mpeg', 'uploads/covers/cover_8979849099b815a8_1788009836.jpg', '4:27', 'active', '2026-08-29 13:23:56', '2026-08-29 13:24:08'),
(17, 'Saiyaara', 17, 12, 7, 3, 1, 'Saiyaara is a soulful romantic Hindi song from the 2025 Bollywood film Saiyaara. Sung by Faheem Abdullah and composed by Tanishk Bagchi, Faheem Abdullah and Arslan Nizami, the song beautifully expresses deep love, longing, memories, and the emotional bond between two people.', 'uploads/music/music_8188fe7c70352338_1788010280.mpeg', 'uploads/covers/cover_31acdd83348d4a6c_1788010280.jpg', '3:03', 'active', '2026-08-29 13:31:20', '2026-08-29 13:31:37'),
(18, 'Phir Bhi Tumko Chaahunga', 18, 16, 9, 3, 1, 'Phir Bhi Tumko Chaahunga is a soulful romantic Bollywood song from the movie Half Girlfriend, beautifully sung by Arijit Singh and Shashaa Tirupati. The song expresses deep, unconditional love and the promise of continuing to love someone despite separation and heartbreak.', 'uploads/music/music_0fa3c488e680c720_1788010737.mpeg', 'uploads/covers/cover_71c50d79be905918_1788010737.jpg', '6:01', 'active', '2026-08-29 13:38:57', '2026-08-29 13:39:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_otps`
--

CREATE TABLE `password_reset_otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` bigint(20) NOT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `music_id` bigint(20) UNSIGNED DEFAULT NULL,
  `video_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review_text` text NOT NULL,
  `status` enum('published','hidden') NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `music_id`, `video_id`, `rating`, `review_text`, `status`, `created_at`, `updated_at`) VALUES
(17, 1, NULL, 1, 5, 'hi', 'published', '2026-08-27 18:32:40', '2026-08-27 18:32:40'),
(18, 1, NULL, 1, 5, 'h', 'published', '2026-08-27 18:37:23', '2026-08-27 18:37:23'),
(19, 1, NULL, 1, 5, 'h', 'published', '2026-08-27 18:37:25', '2026-08-27 18:37:25'),
(21, 1, NULL, 1, 1, 'h', 'published', '2026-08-27 18:37:31', '2026-08-27 18:37:31'),
(22, 1, NULL, 1, 2, 'h', 'published', '2026-08-27 18:37:34', '2026-08-27 18:37:34'),
(23, 1, NULL, 1, 5, 'hrtr', 'published', '2026-08-27 18:37:40', '2026-08-27 18:37:40'),
(24, 1, NULL, 1, 5, 'hrthytr', 'published', '2026-08-27 18:37:45', '2026-08-28 08:23:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(10) NOT NULL,
  `profile_image` varchar(500) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_logout` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_id`, `profile_image`, `full_name`, `email`, `phone`, `address`, `password`, `status`, `created_at`, `updated_at`, `last_login`, `last_logout`) VALUES
(1, 'U0001', 'uploads/profile-img/profile_078eb074ac6ca4f7_1787825014.jpg', 'muhammad ahmed', 'hammadazizkhan405@gmail.com', '03178497732', 'karachi pakistan', '$2y$10$Ydyq5pTAQtM/K8ZNiA48geUACXSnfMf4AGff6DIYBk10wnlg7NGeS', 'active', '2026-08-27 10:03:34', '2026-08-28 10:23:06', '2026-08-29 11:51:53', '2026-08-28 11:25:28'),
(2, 'U0002', 'uploads/profile-img/profile_1a1e1574476c4f3c_1787916125.jpeg', 'hammad', 'ahmedazizkhan405@gmail.com', '03178497735', 'hammadazizkhan405@gmail.com', '$2y$10$kXrlijsD7ktfCc//rITMGO0BQBcy9a40/9doB5YYxou/YGh3E0eWW', 'inactive', '2026-08-28 11:22:05', '2026-08-28 11:24:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `video_title` varchar(255) NOT NULL,
  `artist_id` bigint(20) UNSIGNED DEFAULT NULL,
  `album_id` bigint(20) UNSIGNED DEFAULT NULL,
  `year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `genre_id` bigint(20) UNSIGNED DEFAULT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `video_path` varchar(500) DEFAULT NULL,
  `thumbnail_path` varchar(500) DEFAULT NULL,
  `duration` varchar(10) DEFAULT NULL,
  `status` enum('active','draft','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `video_title`, `artist_id`, `album_id`, `year_id`, `genre_id`, `language_id`, `description`, `video_path`, `thumbnail_path`, `duration`, `status`, `created_at`, `updated_at`) VALUES
(1, 'tum hi ho', 1, 1, 1, 1, 1, 'gfhdujdtjedtjetr', 'uploads/videos/video_68cb777d1dbcd2f2_1787828109.mp4', 'uploads/thumbnails/thumb_5b72e50325e0847f_1787828109.jpeg', NULL, 'active', '2026-08-27 10:55:09', '2026-08-27 10:55:09');

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `website_name` varchar(255) NOT NULL DEFAULT 'SOUND Group',
  `site_logo` varchar(500) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_address` text DEFAULT NULL,
  `facebook_url` varchar(500) DEFAULT NULL,
  `tiktok_url` varchar(500) DEFAULT NULL,
  `linkedin_url` varchar(500) DEFAULT NULL,
  `github_url` varchar(500) DEFAULT NULL,
  `footer_description` text DEFAULT NULL,
  `copyright_text` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `website_settings`
--

INSERT INTO `website_settings` (`id`, `website_name`, `site_logo`, `contact_email`, `contact_phone`, `contact_address`, `facebook_url`, `tiktok_url`, `linkedin_url`, `github_url`, `footer_description`, `copyright_text`, `created_at`, `updated_at`) VALUES
(2, 'SOUND GROUP', NULL, 'ahmedazizkhan405@gmail.com', '03178497732', 'karachi pakistan', 'https://www.facebook.com/share/1JMMWsmTFB/', 'tiktok.com/@coder_byahmed', 'https://www.linkedin.com/in/coderby-ahmad-415543374?utm_source=share_via&utm_content=profile&utm_medium=member_android', 'https://github.com/coderbyahmed', 'Discover music, videos, artists and more — all in one place. Your ultimate destination for streaming and exploring sound.', '© 2026 SOUND Group. All rights reserved.', '2026-08-28 03:09:13', '2026-08-28 03:43:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_email_unique` (`email`);

--
-- Indexes for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_activity_logs_admin_id_index` (`admin_id`),
  ADD KEY `admin_activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `air`
--
ALTER TABLE `air`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `air_name_unique` (`name`);

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `albums_name_unique` (`name`);

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `artists_name_unique` (`name`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_change_otps`
--
ALTER TABLE `email_change_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_change_otps_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `genres_name_unique` (`name`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `languages_name_unique` (`name`);

--
-- Indexes for table `music`
--
ALTER TABLE `music`
  ADD PRIMARY KEY (`id`),
  ADD KEY `music_artist_id_foreign` (`artist_id`),
  ADD KEY `music_album_id_foreign` (`album_id`),
  ADD KEY `music_year_id_foreign` (`year_id`),
  ADD KEY `music_genre_id_foreign` (`genre_id`),
  ADD KEY `music_language_id_foreign` (`language_id`);

--
-- Indexes for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_reset_otps_admin_id_foreign` (`admin_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_music_id_foreign` (`music_id`),
  ADD KEY `reviews_video_id_foreign` (`video_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `videos_artist_id_foreign` (`artist_id`),
  ADD KEY `videos_album_id_foreign` (`album_id`),
  ADD KEY `videos_year_id_foreign` (`year_id`),
  ADD KEY `videos_genre_id_foreign` (`genre_id`),
  ADD KEY `videos_language_id_foreign` (`language_id`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_activity_logs`
--
ALTER TABLE `admin_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `air`
--
ALTER TABLE `air`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `email_change_otps`
--
ALTER TABLE `email_change_otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `music`
--
ALTER TABLE `music`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_change_otps`
--
ALTER TABLE `email_change_otps`
  ADD CONSTRAINT `email_change_otps_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `music`
--
ALTER TABLE `music`
  ADD CONSTRAINT `music_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `music_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `music_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `music_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `music_year_id_foreign` FOREIGN KEY (`year_id`) REFERENCES `air` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `password_reset_otps`
--
ALTER TABLE `password_reset_otps`
  ADD CONSTRAINT `password_reset_otps_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_music_id_foreign` FOREIGN KEY (`music_id`) REFERENCES `music` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_video_id_foreign` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_album_id_foreign` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_artist_id_foreign` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_genre_id_foreign` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `videos_year_id_foreign` FOREIGN KEY (`year_id`) REFERENCES `air` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
