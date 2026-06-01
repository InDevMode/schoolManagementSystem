-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 01, 2026 at 12:53 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `schoolmanagementsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `student_id` int UNSIGNED DEFAULT NULL,
  `attendance_type` tinyint DEFAULT NULL COMMENT '1: Present, 2: Late, 2:Absent, 4:half_day',
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `class_id`, `attendance_date`, `student_id`, `attendance_type`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 13, '2025-02-28', 8, 4, 17, 0, '2025-05-08 10:32:10', '2025-05-08 11:20:34'),
(2, 8, '2025-02-28', 19, 4, 17, 0, '2025-05-08 11:20:48', '2025-05-10 12:51:23'),
(3, 8, '2025-02-28', 11, 2, 17, 0, '2025-05-08 11:20:49', '2025-05-08 12:43:08'),
(4, 11, '2025-02-28', 7, 1, 17, 0, '2025-05-08 11:21:22', '2025-05-08 11:21:22'),
(5, 8, '2025-03-01', 19, 3, 17, 0, '2025-05-08 13:23:50', '2025-05-08 13:23:50'),
(6, 8, '2025-03-01', 11, 1, 3, 0, '2025-05-10 12:55:22', '2025-05-10 12:55:22'),
(7, 13, '2025-03-01', 8, 4, 3, 0, '2025-05-10 12:55:51', '2025-05-10 12:55:51'),
(8, 5, '2025-05-18', 10, 1, 17, 0, '2025-05-24 12:18:47', '2025-05-24 12:18:47'),
(9, 5, '2025-05-21', 10, 2, 17, 0, '2025-05-24 12:18:56', '2025-05-24 12:18:56'),
(10, 8, '2025-03-20', 19, 3, 17, 0, '2025-05-24 12:19:32', '2025-05-24 12:19:32'),
(11, 8, '2025-03-20', 11, 4, 17, 0, '2025-05-24 12:19:37', '2025-05-24 12:19:37'),
(12, 8, '2026-05-25', 19, 0, 17, 0, '2026-05-18 14:02:34', '2026-05-18 14:02:34');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint UNSIGNED NOT NULL,
  `receiver_id` int UNSIGNED DEFAULT NULL,
  `sender_id` int UNSIGNED DEFAULT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: unread, 1: read',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `receiver_id`, `sender_id`, `message`, `file`, `created_date`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 17, 2, 'Bonsoir Mr ATTOLOU', NULL, '2025-09-02 13:25:05', 1, 0, '2025-09-02 13:25:05', '2026-05-18 13:21:55'),
(2, 2, 17, 'Comment allez-vous ?', NULL, '2025-09-06 14:54:51', 1, 0, '2025-09-02 13:27:17', '2026-05-08 10:11:01'),
(3, 3, 2, 'Bonjour Mr Teacher.', NULL, '2025-09-06 14:38:02', 0, 0, '2025-09-02 15:27:40', '2025-09-06 14:38:02'),
(4, 3, 2, 'I hope you are doing well ?.', NULL, '2025-09-06 14:37:55', 0, 0, '2025-09-02 15:28:10', '2025-09-06 14:37:55'),
(5, 7, 2, 'Hi Black\r\nHow\'s it going to day ?', NULL, '2025-09-02 15:29:01', 1, 0, '2025-09-02 15:29:01', '2026-05-11 15:09:40'),
(6, 12, 2, 'Good evening Black Parent.\r\nWhat about you ?', NULL, '2025-09-02 15:31:37', 1, 0, '2025-09-02 15:31:37', '2025-09-04 15:51:18'),
(7, 17, 2, 'Je vais bien et chez vous ?', NULL, '2025-09-03 09:19:17', 1, 0, '2025-09-03 09:19:17', '2026-05-18 13:21:55'),
(8, 17, 2, 'Avez-vous déjà reçu votre programme de la semaine ?', NULL, '2025-09-03 09:19:43', 1, 0, '2025-09-03 09:19:43', '2026-05-18 13:21:55'),
(9, 17, 2, 'Sinon faite le moi savoir.', NULL, '2025-09-03 09:19:54', 1, 0, '2025-09-03 09:19:54', '2026-05-18 13:21:55'),
(10, 17, 2, 'Nous devons avancez au niveau du programme avec les apprenants.', NULL, '2025-09-03 09:20:17', 1, 0, '2025-09-03 09:20:17', '2026-05-18 13:21:55'),
(11, 14, 17, 'Bonjour Mr Black2 \r\nI hope you doing well ?', NULL, '2025-09-03 09:25:11', 1, 0, '2025-09-03 09:25:11', '2025-09-06 14:52:35'),
(13, 2, 17, 'Oui Monsieur ATTOLOU', NULL, '2025-09-03 13:44:44', 1, 0, '2025-09-03 13:44:44', '2026-05-08 10:11:01'),
(14, 2, 17, 'Je vais bien. J\'ai reçu le programme mais j\'ai quelques questions.', NULL, '2025-09-03 13:45:26', 1, 0, '2025-09-03 13:45:26', '2026-05-08 10:11:01'),
(15, 2, 12, 'I\'m doing well. \r\nAnd you ?', NULL, '2025-09-04 08:01:20', 1, 0, '2025-09-04 08:01:20', '2026-05-08 10:10:05'),
(16, 2, 7, 'Yes i\'m doing well. I waiting my week schedule Mr Main.', NULL, '2025-09-04 08:26:42', 1, 0, '2025-09-04 08:26:42', '2026-05-08 10:09:58'),
(17, 2, 21, 'Yes I\'m doing well. I wanna learn english.', NULL, '2025-09-04 09:53:18', 1, 0, '2025-09-04 09:53:18', '2026-05-08 15:25:43'),
(18, 17, 2, 'D\'accord. Je vous envoie le programme tout de suite.', 'H5.jpg', '2025-09-04 14:05:55', 1, 0, '2025-09-04 14:05:55', '2026-05-18 13:21:55'),
(19, 2, 17, 'Merci beaucoup. J\'ai reçu le programme.', '04092025050803-EtiquetteCandyChips1.pdf', '2025-09-04 15:08:03', 1, 0, '2025-09-04 15:08:03', '2026-05-08 10:11:01'),
(20, 17, 2, 'Un travail de maison d\'un apprenants.', '04092025051150-homework30062025091350ra4zgxflwkfjmtxay05v.xlsx', '2025-09-04 15:11:50', 1, 0, '2025-09-04 15:11:50', '2026-05-18 13:21:55'),
(21, 2, 12, '💯💯', NULL, '2025-09-04 15:48:27', 1, 0, '2025-09-04 15:48:27', '2026-05-08 10:10:05'),
(22, 2, 12, '👍️👍️👍️', NULL, '2025-09-04 15:50:40', 1, 0, '2025-09-04 15:50:40', '2026-05-08 10:10:05'),
(23, 2, 12, 'Mon ticket pour la marche...', 'chat_file04092025055117qhho6azd7goiksvirctp.jpg', '2025-09-04 15:51:17', 1, 0, '2025-09-04 15:51:17', '2026-05-08 10:10:05'),
(24, 2, 17, 'Bien reçu monsieur Main.', NULL, '2025-09-05 09:49:38', 1, 0, '2025-09-05 09:49:38', '2026-05-08 10:11:01'),
(25, 21, 2, 'Ok me too.\r\nI will send you the schedule. edited.', NULL, '2025-09-08 08:33:12', 0, 0, '2025-09-06 11:34:18', '2025-09-08 08:33:12'),
(26, 7, 2, 'Ok I really appreciate. I wanna be honest with you.🤣🤣', NULL, '2025-09-06 17:13:25', 1, 0, '2025-09-06 14:57:28', '2026-05-11 15:09:40'),
(27, 17, 2, 'Hey\r\nLong time no see', NULL, '2026-05-08 10:10:35', 1, 0, '2026-05-08 10:10:35', '2026-05-18 13:21:55'),
(28, 17, 2, '😂😂', NULL, '2026-05-08 10:11:01', 1, 0, '2026-05-08 10:11:01', '2026-05-18 13:21:55'),
(29, 2, 17, 'Yes I\'m good', NULL, '2026-05-08 10:12:11', 0, 0, '2026-05-08 10:12:11', '2026-05-08 10:12:11'),
(30, 2, 17, 'What\'s up ?', NULL, '2026-05-08 10:12:20', 0, 0, '2026-05-08 10:12:20', '2026-05-08 10:12:20'),
(31, 2, 7, 'Oh yeah I know \r\nYou\'re tripping bro.\r\nBut thanks', NULL, '2026-05-11 15:09:40', 0, 0, '2026-05-11 15:09:40', '2026-05-11 15:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `is_delete` tinyint NOT NULL DEFAULT '0',
  `created_by` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `name`, `amount`, `status`, `is_delete`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CI', 69000, 1, 0, 1, '2025-01-28 09:20:59', '2025-08-05 13:10:21'),
(2, 'CP', 79000, 1, 0, 1, '2025-01-28 09:21:07', '2025-08-05 13:10:08'),
(3, 'CE1', 95000, 1, 0, 1, '2025-01-28 09:21:20', '2025-08-05 13:09:53'),
(4, 'CE2', 85000, 1, 0, 1, '2025-01-28 09:21:38', '2025-08-05 13:09:40'),
(5, 'CM1', 90000, 1, 0, 1, '2025-01-28 09:21:57', '2025-08-05 13:08:12'),
(6, 'CM2', 100000, 0, 0, 1, '2025-01-28 09:22:07', '2025-08-05 13:08:25'),
(7, '6 ième', 180000, 0, 0, 2, '2025-01-28 09:25:31', '2025-08-05 13:09:07'),
(8, '5 ième', 130000, 1, 0, 2, '2025-01-28 09:25:38', '2025-08-05 13:09:22'),
(9, '4 ième', 140000, 0, 0, 2, '2025-01-28 09:25:45', '2025-08-05 13:07:47'),
(10, '3 ième', 120000, 1, 0, 2, '2025-01-28 09:25:51', '2025-08-05 13:07:19'),
(11, '2 nd A', 400000, 1, 0, 2, '2025-01-28 09:28:24', '2025-08-05 13:07:08'),
(12, '2 nd B', 230000, 0, 0, 2, '2025-01-28 09:28:33', '2025-08-05 13:07:31'),
(13, '2 nd C', 200000, 1, 0, 2, '2025-01-28 09:28:43', '2025-07-21 12:11:28'),
(14, '2 nd D', 150000, 1, 0, 2, '2025-01-28 09:28:50', '2025-07-17 08:56:36'),
(15, '1 ière A', 185000, 0, 0, 2, '2025-09-15 09:13:12', '2025-09-15 09:14:57'),
(16, 'Terminale A1', 230000, 1, 0, 2, '2025-09-16 11:09:59', '2025-09-16 11:09:59'),
(17, 'Terminale D', 250000, 1, 0, 2, '2025-09-16 11:10:15', '2025-09-16 11:10:15');

-- --------------------------------------------------------

--
-- Table structure for table `class_subject`
--

CREATE TABLE `class_subject` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED NOT NULL,
  `subject_id` int UNSIGNED NOT NULL,
  `coefficient` int NOT NULL DEFAULT '0',
  `created_by` int UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_subject`
--

INSERT INTO `class_subject` (`id`, `class_id`, `subject_id`, `coefficient`, `created_by`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 4, 7, 0, 2, 1, 0, '2025-01-28 12:01:26', '2025-02-11 10:18:28'),
(4, 1, 16, 0, 2, 1, 0, '2025-01-28 12:01:26', '2025-02-11 10:18:12'),
(5, 2, 13, 0, 2, 1, 0, '2025-01-28 12:02:58', '2025-02-11 10:06:56'),
(9, 1, 10, 0, 1, 1, 0, '2025-01-29 08:31:43', '2025-01-29 08:31:43'),
(11, 3, 8, 0, 2, 1, 0, '2025-01-29 14:24:53', '2025-01-29 14:24:53'),
(12, 3, 6, 0, 2, 1, 0, '2025-01-29 14:24:53', '2025-01-29 14:24:53'),
(15, 2, 9, 0, 17, 0, 0, '2025-02-08 13:42:42', '2025-02-08 13:42:42'),
(25, 5, 8, 0, 17, 0, 0, '2025-02-13 09:31:20', '2025-03-08 12:19:18'),
(26, 5, 6, 0, 17, 1, 0, '2025-02-13 09:31:20', '2025-02-13 09:31:20'),
(28, 7, 13, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:36:26'),
(29, 5, 12, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:58:38'),
(30, 4, 18, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:36:35'),
(31, 3, 16, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:36:17'),
(32, 2, 14, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:35:59'),
(57, 11, 11, 3, 2, 1, 0, '2025-09-15 09:24:05', '2025-09-15 09:24:05'),
(58, 11, 7, 3, 2, 1, 0, '2025-09-15 09:24:05', '2025-09-15 09:24:05'),
(59, 11, 5, 3, 2, 1, 0, '2025-09-15 09:24:05', '2025-09-15 09:24:05'),
(60, 11, 3, 3, 2, 1, 0, '2025-09-15 09:24:05', '2025-09-15 09:24:05'),
(61, 11, 2, 3, 2, 1, 0, '2025-09-15 09:24:05', '2025-09-15 09:24:05'),
(62, 11, 1, 3, 2, 1, 0, '2025-09-15 09:24:05', '2025-09-15 09:24:05'),
(63, 8, 7, 2, 2, 1, 0, '2025-09-15 09:24:49', '2025-09-15 09:24:49'),
(64, 8, 5, 2, 2, 1, 0, '2025-09-15 09:24:49', '2025-09-15 09:24:49'),
(65, 8, 4, 2, 2, 1, 0, '2025-09-15 09:24:49', '2025-09-15 09:24:49'),
(66, 8, 3, 2, 2, 1, 0, '2025-09-15 09:24:49', '2025-09-15 09:24:49'),
(67, 8, 2, 2, 2, 1, 0, '2025-09-15 09:24:49', '2025-09-15 09:24:49'),
(70, 13, 5, 4, 2, 1, 0, '2025-09-15 09:26:00', '2025-09-15 09:26:00'),
(71, 13, 4, 4, 2, 1, 0, '2025-09-15 09:26:00', '2025-09-15 09:26:00'),
(72, 13, 3, 4, 2, 1, 0, '2025-09-15 09:26:00', '2025-09-15 09:26:00'),
(73, 13, 2, 4, 2, 1, 0, '2025-09-15 09:26:00', '2025-09-15 09:26:00'),
(74, 14, 3, 5, 2, 1, 0, '2025-09-15 14:21:06', '2025-09-15 14:21:06'),
(75, 14, 11, 5, 2, 1, 0, '2025-09-15 14:21:06', '2025-09-15 14:21:06'),
(76, 14, 5, 5, 2, 1, 0, '2025-09-15 14:21:06', '2025-09-15 14:21:06'),
(77, 14, 4, 5, 2, 1, 0, '2025-09-15 14:21:06', '2025-09-15 14:21:06'),
(78, 14, 2, 5, 2, 1, 0, '2025-09-15 14:21:06', '2025-09-15 14:21:06'),
(79, 14, 1, 5, 2, 1, 0, '2025-09-15 14:21:06', '2025-09-15 14:21:06'),
(80, 17, 17, 5, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:57:10'),
(81, 17, 13, 3, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:57:42'),
(82, 17, 12, 4, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:58:39'),
(83, 17, 7, 2, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:58:30'),
(84, 17, 5, 3, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:58:23'),
(85, 17, 4, 4, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:58:14'),
(86, 17, 3, 5, 2, 1, 0, '2025-09-16 11:57:10', '2025-09-16 11:57:10');

-- --------------------------------------------------------

--
-- Table structure for table `class_teacher`
--

CREATE TABLE `class_teacher` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `teacher_id` int UNSIGNED DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_teacher`
--

INSERT INTO `class_teacher` (`id`, `class_id`, `teacher_id`, `created_by`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(9, 4, 5, 2, 1, 0, '2025-01-29 14:22:43', '2025-01-29 14:22:43'),
(10, 4, 4, 2, 1, 0, '2025-01-29 14:22:43', '2025-01-29 14:22:43'),
(11, 10, 6, 2, 1, 0, '2025-01-29 14:23:18', '2025-01-29 14:23:18'),
(12, 10, 5, 2, 1, 0, '2025-01-29 14:23:18', '2025-01-29 14:23:18'),
(23, 13, 3, 1, 1, 0, '2025-01-30 09:35:36', '2025-01-30 09:35:36'),
(27, 8, 4, 17, 1, 0, '2025-02-13 09:12:21', '2025-02-13 09:12:21'),
(28, 8, 3, 17, 1, 0, '2025-02-13 09:12:21', '2025-02-13 09:12:21'),
(29, 8, 18, 17, 0, 0, '2025-02-13 09:12:21', '2025-09-13 02:12:52'),
(35, 11, 18, 17, 0, 0, '2025-03-08 11:18:24', '2025-09-13 02:12:44'),
(36, 6, 26, 17, 1, 0, '2025-03-08 11:18:24', '2025-09-11 09:14:50'),
(37, 5, 25, 17, 1, 0, '2025-03-08 11:18:24', '2025-09-11 09:14:38'),
(38, 4, 26, 17, 1, 0, '2025-03-08 11:18:24', '2025-09-11 09:09:31'),
(39, 18, 25, 17, 1, 0, '2025-03-08 11:18:24', '2025-08-23 08:43:09'),
(40, 5, 6, 17, 1, 0, '2025-05-24 12:28:15', '2025-05-24 12:28:15'),
(41, 17, 25, 2, 1, 0, '2025-09-16 15:15:17', '2025-09-16 15:15:17'),
(42, 17, 18, 2, 1, 0, '2025-09-16 15:15:17', '2025-09-16 15:15:17'),
(43, 17, 26, 2, 1, 0, '2025-09-16 15:15:17', '2025-09-16 15:15:17');

-- --------------------------------------------------------

--
-- Table structure for table `class_timetable`
--

CREATE TABLE `class_timetable` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `subject_id` int UNSIGNED DEFAULT NULL,
  `week_id` int UNSIGNED DEFAULT NULL,
  `start_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_timetable`
--

INSERT INTO `class_timetable` (`id`, `class_id`, `subject_id`, `week_id`, `start_time`, `end_time`, `room_number`, `is_delete`, `created_at`, `updated_at`) VALUES
(8, 14, 2, 1, '08:30', '10:30', 'B1', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(9, 14, 2, 2, '12:30', '12:30', 'B2', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(10, 14, 2, 3, '14:30', '16:30', 'B3', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(11, 14, 2, 4, '16:30', '18:30', 'B4', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(12, 14, 2, 5, '09:00', '12:00', 'B5', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(13, 14, 2, 6, '14:00', '17:00', 'B6', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(14, 14, 2, 7, '10:30', '12:30', 'b7', 0, '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(15, 11, 1, 1, '15:00', '18:00', 'B1', 0, '2025-02-01 11:59:12', '2025-02-01 11:59:12'),
(16, 3, 6, 1, '08:00', '09:00', 'Salle 1', 0, '2025-02-01 12:21:39', '2025-02-01 12:21:39'),
(18, 17, 5, 1, '08:25', '09:20', 'SALLE C10', 0, '2025-09-16 12:37:06', '2025-09-16 12:37:06'),
(19, 17, 5, 2, '15:30', '17:30', 'SALLE C10', 0, '2025-09-16 12:37:06', '2025-09-16 12:37:06'),
(20, 17, 5, 3, '07:30', '08:25', 'SALLE C10', 0, '2025-09-16 12:37:06', '2025-09-16 12:37:06'),
(21, 17, 7, 5, '15:30', '16:30', 'Terrain', 0, '2025-09-16 12:37:55', '2025-09-16 12:37:55'),
(22, 17, 3, 4, '13:30', '15:30', 'SALLE B2', 0, '2025-09-16 12:39:06', '2025-09-16 12:39:06'),
(23, 17, 3, 5, '07:30', '10:15', 'SALLE B2', 0, '2025-09-16 12:39:06', '2025-09-16 12:39:06'),
(24, 17, 4, 2, '08:25', '10:15', 'SALLE D2', 0, '2025-09-16 12:40:56', '2025-09-16 12:40:56'),
(25, 17, 4, 5, '14:30', '15:30', 'SALLE D2', 0, '2025-09-16 12:40:56', '2025-09-16 12:40:56'),
(26, 17, 12, 2, '10:30', '12:20', 'SALLE C10', 0, '2025-09-16 12:42:27', '2025-09-16 12:42:27'),
(27, 17, 12, 3, '10:30', '11:25', 'SALLE C10', 0, '2025-09-16 12:42:27', '2025-09-16 12:42:27'),
(28, 17, 12, 4, '10:30', '12:20', 'SALLE C10', 0, '2025-09-16 12:42:27', '2025-09-16 12:42:27'),
(29, 17, 13, 1, '07:30', '08:25', 'SALLE C10', 0, '2025-09-16 12:43:26', '2025-09-16 12:43:26'),
(30, 17, 13, 5, '10:30', '12:20', 'SALLE C10', 0, '2025-09-16 12:43:26', '2025-09-16 12:43:26'),
(31, 17, 17, 1, '10:30', '11:25', 'SALLE C10', 0, '2025-09-16 12:44:19', '2025-09-16 12:44:19'),
(32, 17, 17, 2, '08:25', '10:15', 'SALLE C10', 0, '2025-09-16 12:44:19', '2025-09-16 12:44:19');

-- --------------------------------------------------------

--
-- Table structure for table `communicates`
--

CREATE TABLE `communicates` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notice_date` date DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `communicates`
--

INSERT INTO `communicates` (`id`, `title`, `notice_date`, `publish_date`, `message`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Premier message', '2025-06-07', '2025-06-14', '<p><b>Signification et origine<br></b><br>Je ne suis pas de cette <b>nationalité </b>mais par contre je peux m\'exprimer comme eux et vous ne verrez aucune différence concernant le ton de ma voix et la différence dans la <b>prononciation</b>.<br>Actuelleme', 17, 0, '2025-06-07 12:32:13', '2025-06-17 07:41:54'),
(2, 'Deuxième message', '2025-06-20', '2025-06-27', '<p><b>Motivation and Football</b><br>You win when your mind is stronger than your emotions...<br><br>Les fans de football se rendent à Philadelphie pour assister à la <b>Coupe du Monde des Clubs de FIFA 2025</b> au <b>Linclon Financial Fiel Philadelphie.<', 17, 0, '2025-06-13 13:03:32', '2025-06-17 07:43:07'),
(3, 'Troisième message', '2025-06-21', '2025-06-28', '<p><b>Signification et origine du Requin blanc<br></b><br>Le grand <b>Requin blanc</b> est prédateur marin redouté, mesurant généralement entre 4 et 7 mètres de long. Il appartient à la famille des <b>Lamnidae </b>et se trouve dans les eaux tempérées.<br>Les <b>Canadiens </b>ont sollicité l\'arbitrage auprès du Centre international pour le règlement des différends relatifs aux investissements afin de régler les désaccords avec le Mali. <br><br></p>', 17, 0, '2025-06-13 13:14:12', '2025-06-17 08:42:17'),
(4, 'Quatrième message', '2025-06-17', '2025-06-24', '<p>Le <b>Gabon </b>sur la nouvelle liste des pays interdits d\'entrer au USA.<br><br>L\'administration de <b>Trump </b>à publié une nouvelle liste de <b>36 pays</b> donc <b>25 pays africains</b> vont connaître de nouvelles restrictions pour l\'entrée aux USA.<br><i>« Esmaeil Fekri, un agent du Mossad reconnu coupable des crimes capitaux de +corruption sur terre+ et de +moharebeh+ (guerre contre Dieu, ndlr) a été pendu »</i>, a indiqué le site d’information Mizan Online, organe du pouvoir judiciaire.<br></p>', 17, 0, '2025-06-17 08:04:23', '2025-06-17 08:40:49'),
(5, 'Cinquième message', '2025-06-17', '2025-07-01', '<p><b>L\'Iran exécute un agent du Mossad israélien arrêté en 2023.<br><br></b>La justice iranienne a annoncé avoir pendu lundi un homme arrêté en 2023 et reconnu coupable d’être un agent du Mossad, le service de renseignement extérieur d’Israël, au quatrième.<br>Barrick Gold est en conflit avec les dirigeants militaires du Mali pour des taxes impayées et des contrats inéquitables avec les gouvernements précédents. <br><br>Le conflit a abouti à un mandat d\'arrêt en décembre 2024 contre le PDG de Barrick, Mark Bristow, et à l\'offre de la société de payer 370 millions de dollars au gouvernement. <br></p>', 17, 0, '2025-06-17 08:07:34', '2025-06-17 08:40:13'),
(6, 'Sixième message', '2025-06-17', '2025-07-08', '<p><b>La guerre.</b></p><p><b><br></b>L’exécution a eu lieu une fois toutes les procédures légales accomplies et la confirmation du verdict par la Cour suprême, a précisé Mizan. Les forces aérospatiales russes ont perdu un autre chasseur <b>Sukhoi Su-25 en Ukraine</b>, mais la perte de cet appareil n\'est pas due aux défenses aériennes ukrainiennes. Les images vidéo de l\'incident semblent montrer que l\'avion a été abattu par un autre avion russe.<br><br>Barrick Gold est en conflit avec les dirigeants militaires du Mali pour des taxes impayées et des contrats inéquitables avec les gouvernements précédents. <br><br></p>', 17, 0, '2025-06-17 08:15:38', '2025-06-17 08:39:29'),
(7, 'Septième message', '2025-06-18', '2025-06-25', '<p><b>Football : <br></b>Paul Pogba pourrait faire son grand retour avec l\'AS Monaco.<br><br>L\'ancien milieu de terrain de la <b>Juventus </b>est de nouveau autorisé à jouer en compétition après la fin de sa suspension pour dopage.C\'est le retour que l\'on n\'attendait plus. Le footballeur français Paul Pogba pourrait rejoindre l\'AS Monaco dès la saison prochaine. <br><br>L\'ancien milieu de terrain de la Juventus de Turin et de Manchester United espère faire son retour dans le football après une suspension de 18 mois pour dopage.</p>', 17, 0, '2025-06-17 08:26:14', '2025-06-17 08:36:45'),
(8, 'Huitième message', '2025-06-24', '2025-06-24', '<p>Message à tous les apprenants de la classe de 6ième.<br></p>', 17, 0, '2025-06-23 12:49:58', '2025-06-24 12:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `period_id` bigint UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('planned','in_progress','completed','graded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `name`, `created_by`, `is_delete`, `created_at`, `updated_at`, `period_id`, `start_date`, `end_date`, `status`) VALUES
(1, 'Evaluation 1', 17, 0, '2025-02-10 15:28:57', '2025-02-10 15:28:57', NULL, NULL, NULL, 'planned'),
(2, 'Evaluation 2', 17, 0, '2025-02-10 15:32:42', '2025-02-10 15:32:42', NULL, NULL, NULL, 'planned'),
(3, 'Evaluation 3', 17, 0, '2025-02-10 15:32:58', '2025-02-10 15:32:58', NULL, NULL, NULL, 'planned'),
(4, 'Evaluation 4', 17, 0, '2025-02-10 15:33:15', '2025-02-10 15:33:15', NULL, NULL, NULL, 'planned'),
(5, 'Evaluation 5', 17, 0, '2025-02-10 15:33:34', '2025-02-10 15:33:34', NULL, NULL, NULL, 'planned'),
(6, 'Evaluation 6', 17, 0, '2025-02-10 15:34:15', '2025-02-10 15:34:15', NULL, NULL, NULL, 'planned'),
(7, 'Evaluation 7', 17, 0, '2025-02-10 15:34:31', '2025-02-10 15:34:31', NULL, NULL, NULL, 'planned'),
(8, 'Evaluation 8', 17, 0, '2025-02-10 15:34:50', '2025-02-10 15:34:50', NULL, NULL, NULL, 'planned'),
(9, 'Evaluation 9', 17, 0, '2025-02-10 15:35:07', '2025-02-10 15:35:07', NULL, NULL, NULL, 'planned'),
(10, 'Evaluation 10', 17, 0, '2025-02-10 15:35:29', '2025-02-10 15:35:29', NULL, NULL, NULL, 'planned'),
(11, 'Evaluation 11', 17, 0, '2025-02-10 15:35:44', '2025-09-20 11:11:54', 4, '2025-12-20', '2025-12-27', ''),
(12, 'Evaluation 12', 17, 0, '2025-02-10 15:35:57', '2025-02-10 15:35:57', NULL, NULL, NULL, 'planned'),
(13, 'Evaluation 13', 2, 0, '2025-09-20 10:49:40', '2026-05-08 13:20:58', 3, '2025-11-24', '2025-12-01', 'planned');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feescollections`
--

CREATE TABLE `feescollections` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `student_id` int UNSIGNED DEFAULT NULL,
  `total_amount` int DEFAULT NULL,
  `paid_amount` int DEFAULT NULL,
  `remaning_amount` int DEFAULT NULL,
  `payment_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remark` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payment_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `is_payment` tinyint NOT NULL DEFAULT '1' COMMENT '0: Not Paid, 1: Paid',
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feescollections`
--

INSERT INTO `feescollections` (`id`, `class_id`, `student_id`, `total_amount`, `paid_amount`, `remaning_amount`, `payment_type`, `remark`, `payment_data`, `payment_status`, `is_payment`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 5, 10, 90000, 50000, 40000, 'check', 'Première tranche de contributions.', NULL, 'Paid', 1, 2, 0, '2025-08-14 15:07:15', '2025-08-14 15:07:15'),
(2, 14, 22, 150000, 50000, 100000, 'transfer', 'Première tranche de contribution', NULL, 'Paid', 1, 2, 0, '2025-08-14 15:15:29', '2025-08-14 15:15:29'),
(3, 8, 19, 130000, 30000, 100000, 'cash', 'Première tranche de contribution.', NULL, 'Paid', 1, 2, 0, '2025-08-14 15:16:25', '2025-08-14 15:16:25'),
(4, 10, 9, 120000, 20000, 100000, 'cash', 'Première tranche de paiement des frais de scolarité.', NULL, 'Paid', 1, 2, 0, '2025-08-16 12:02:38', '2025-08-16 12:02:38'),
(5, 14, 22, 150000, 30000, 70000, 'cash', 'Deuxième tranche du paiement des frais de scolarité.', NULL, 'Paid', 1, 17, 0, '2025-08-16 12:04:24', '2025-08-16 12:04:24'),
(6, 14, 22, 150000, 40000, 30000, 'check', 'Troisième tranche de la contribution.', NULL, 'Paid', 1, 17, 0, '2025-08-18 06:53:51', '2025-08-18 06:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `homework`
--

CREATE TABLE `homework` (
  `id` bigint UNSIGNED NOT NULL,
  `work_id` int UNSIGNED DEFAULT NULL,
  `student_id` int UNSIGNED DEFAULT NULL,
  `document_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` enum('hold','submitted','done','processed','resolved') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hold',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homework`
--

INSERT INTO `homework` (`id`, `work_id`, `student_id`, `document_file`, `description`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 4, 11, 'homework_student07072025093435usvqqa6sgiyztvibibd6.xlsx', '<p>Soumission du travail de maison demandé par Josué...<br></p>', 'submitted', 0, '2025-07-07 07:34:35', '2025-07-07 07:34:35'),
(2, 7, 11, 'homework_student07072025095205uycwrinf0h7e2rgbnzk8.pdf', '<p>Soumission du travail de maison concernant la science physique technologie ...<br></p>', 'submitted', 0, '2025-07-07 07:52:05', '2025-07-07 07:52:05'),
(3, 7, 19, 'homework_student07072025041912dpuhqk0oojvi4bc6acde.docx', '<p>Laravel attend automatiquement une chaîne <em>déjà </em>hashée...</p>', 'submitted', 0, '2025-07-07 14:19:12', '2025-07-07 14:19:12'),
(4, 4, 19, 'homework_student07072025042859v6txdhyewvsu6notbu5q.pdf', '<p>Soumission de mon curriculum vitae comme travail de maison...<br></p>', 'submitted', 0, '2025-07-07 14:28:59', '2025-07-07 14:28:59'),
(5, 5, 19, 'homework_student07072025043123jfqhu1jeihqtbvpqcbct.xlsx', 'Souhaites-tu aussi que le <code data-start=\"2626\" data-end=\"2634\">status</code> dans <code data-start=\"2640\" data-end=\"2647\">works</code> soit mis à jour <strong data-start=\"2664\" data-end=\"2711\">uniquement quand tous les élèves ont soumis</strong> ?', 'submitted', 0, '2025-07-07 14:31:23', '2025-07-07 14:31:23'),
(6, 5, 11, 'homework_student07072025044754yx2q5wucyg0mfbsscsbr.pdf', '<p>Protocole de transcription ...<br></p>', 'submitted', 0, '2025-07-07 14:47:54', '2025-07-07 14:47:54');

-- --------------------------------------------------------

--
-- Table structure for table `marks_grade`
--

CREATE TABLE `marks_grade` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent_from` int NOT NULL,
  `percent_to` int NOT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marks_grade`
--

INSERT INTO `marks_grade` (`id`, `name`, `percent_from`, `percent_to`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Grade B', 10, 40, 17, 0, '2025-04-29 08:21:19', '2025-04-29 14:45:23'),
(2, 'Grade A', 10, 30, 17, 0, '2025-04-29 08:43:30', '2025-04-29 14:45:12'),
(3, 'Grade C', 30, 60, 17, 0, '2025-04-29 14:45:45', '2025-04-29 14:45:45'),
(4, 'Grade D', 60, 90, 17, 0, '2025-04-29 14:46:00', '2025-04-29 14:46:00'),
(5, 'Grade E', 40, 60, 17, 0, '2025-04-29 14:46:23', '2025-04-29 14:46:23'),
(6, 'Grade F', 60, 80, 17, 0, '2025-04-29 14:46:46', '2025-04-29 14:46:46'),
(7, 'Grade G', 80, 100, 17, 0, '2025-04-29 14:47:02', '2025-04-29 14:47:02');

-- --------------------------------------------------------

--
-- Table structure for table `marks_register`
--

CREATE TABLE `marks_register` (
  `id` bigint UNSIGNED NOT NULL,
  `student_id` int UNSIGNED DEFAULT NULL,
  `exam_id` int UNSIGNED DEFAULT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `subject_id` int UNSIGNED DEFAULT NULL,
  `class_work` decimal(5,2) DEFAULT NULL,
  `home_work` decimal(5,2) DEFAULT NULL,
  `exam_work` decimal(5,2) DEFAULT NULL,
  `test_work` decimal(5,2) DEFAULT NULL,
  `quiz_1` decimal(5,2) DEFAULT NULL,
  `quiz_2` decimal(5,2) DEFAULT NULL,
  `quiz_3` decimal(5,2) DEFAULT NULL,
  `quiz_4` decimal(5,2) DEFAULT NULL,
  `quiz_5` decimal(5,2) DEFAULT NULL,
  `assignment_1` decimal(5,2) DEFAULT NULL,
  `assignment_2` decimal(5,2) DEFAULT NULL,
  `assignment_3` decimal(5,2) DEFAULT NULL,
  `passing_marks` decimal(6,2) DEFAULT NULL,
  `full_marks` decimal(6,2) DEFAULT NULL,
  `total_marks` decimal(6,2) DEFAULT NULL,
  `quiz_average` decimal(5,2) DEFAULT NULL,
  `assignment_average` decimal(5,2) DEFAULT NULL,
  `coefficient` decimal(3,1) NOT NULL DEFAULT '1.0',
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marks_register`
--

INSERT INTO `marks_register` (`id`, `student_id`, `exam_id`, `class_id`, `subject_id`, `class_work`, `home_work`, `exam_work`, `test_work`, `quiz_1`, `quiz_2`, `quiz_3`, `quiz_4`, `quiz_5`, `assignment_1`, `assignment_2`, `assignment_3`, `passing_marks`, `full_marks`, `total_marks`, `quiz_average`, `assignment_average`, `coefficient`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 19, 6, 8, 5, 0.00, 0.00, 0.00, 0.00, 5.00, 5.00, 5.00, NULL, NULL, 10.00, 10.00, 10.00, 70.00, 80.00, 45.00, 5.00, 10.00, 2.0, 2, 0, '2025-09-23 15:26:31', '2025-09-24 15:49:49'),
(2, 19, 6, 8, 7, 2.00, 2.00, NULL, NULL, 6.00, 6.00, 6.00, NULL, NULL, 11.00, 11.00, NULL, 80.00, 100.00, 44.00, 6.00, 11.00, 2.0, 2, 0, '2025-09-23 15:26:39', '2025-09-24 15:49:49'),
(3, 19, 6, 8, 11, 3.00, NULL, NULL, 3.00, 7.00, 7.00, NULL, NULL, NULL, 13.00, 13.00, NULL, 50.00, 100.00, 46.00, 7.00, 13.00, 1.0, 2, 0, '2025-09-23 15:26:40', '2025-09-24 15:49:49'),
(4, 11, 6, 8, 5, NULL, 11.00, NULL, NULL, 1.00, NULL, 5.00, NULL, 3.00, 1.00, 7.00, NULL, 70.00, 80.00, 28.00, 3.00, 4.00, 2.0, 2, 0, '2025-09-25 15:54:49', '2025-09-25 15:54:49'),
(5, 11, 6, 8, 7, NULL, NULL, NULL, 17.00, NULL, 8.00, 4.00, 6.00, NULL, NULL, 8.00, 6.00, 80.00, 100.00, 49.00, 6.00, 7.00, 2.0, 2, 0, '2025-09-25 15:54:49', '2025-09-25 15:54:49'),
(6, 11, 6, 8, 11, 11.00, NULL, NULL, NULL, 8.00, NULL, 4.00, 3.00, NULL, 5.00, 3.00, NULL, 50.00, 100.00, 34.00, 5.00, 4.00, 1.0, 2, 0, '2025-09-25 15:54:49', '2025-09-25 15:54:49');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_12_30_132802_add_is_delete_to_users_table', 1),
(6, '2025_01_02_130148_create_class_table', 1),
(7, '2025_01_13_131349_create_subject_table', 1),
(8, '2025_01_15_093712_create_class_subject_table', 1),
(9, '2025_01_22_110457_add_fields_to_users_table', 1),
(10, '2025_01_23_165723_add_other_fields_to_users_table', 1),
(11, '2025_01_29_091903_create_class_teacher_table', 2),
(13, '2025_01_31_093946_create_week_table', 3),
(14, '2025_01_30_164442_create_class_timetable_table', 4),
(15, '2025_02_10_135321_create_exams_table', 5),
(16, '2025_02_11_091850_create_schedules_table', 6),
(17, '2025_02_13_201447_add_fields_to_week_table', 7),
(18, '2025_02_20_153926_create_marks_register_table', 8),
(19, '2025_03_24_105743_add_passing_marks_and_full_marks_to_register_table', 9),
(20, '2025_04_29_095259_create_marks_grade_table', 10),
(21, '2025_05_08_102641_create_attendances_table', 11),
(22, '2025_06_07_134750_create_communicates_table', 12),
(23, '2025_06_07_141213_create_noticeboard_messages_table', 12),
(24, '2025_06_26_095209_create_works_table', 13),
(25, '2025_07_07_091845_create_homework_table', 14),
(26, '2025_07_07_112930_add_status_to_homework_table', 15),
(27, '2025_07_16_105118_add_coefficient_to_subject_table', 16),
(28, '2025_07_17_102722_add_amount_to_class_table', 17),
(29, '2025_08_01_150432_create_feescollections_table', 18),
(30, '2025_08_06_111621_add_is_payment_to_feescollections_table', 19),
(31, '2025_08_07_091334_create_settings_table', 20),
(32, '2025_08_07_162733_add_payment_data_to_feescollections_table', 21),
(33, '2025_08_07_163345_add_another_data_to_feescollections_table', 22),
(34, '2025_08_08_164602_add_key_to_settings_table', 23),
(35, '2025_08_08_165628_add_key_to_settings_table', 24),
(36, '2025_08_19_125602_add_is_delete_to_class_timetable_table', 25),
(37, '2025_08_19_131640_add_is_delete_to_class_timetable_table', 26),
(38, '2025_08_20_092622_add_is_delete_to_attendances_table', 27),
(39, '2025_08_22_091600_add_favicon_logo_to_settings_table', 28),
(40, '2025_09_01_095749_create_chats_table', 29),
(41, '2025_09_01_100721_create_chats_table', 30),
(42, '2025_09_01_101011_create_chats_table', 31),
(43, '2025_09_01_141826_add_last_login_to_users_table', 32),
(44, '2025_09_02_110406_add_created_date_to_chats_table', 33),
(45, '2025_09_10_104539_add_coefficient_to_class_subject_table', 34),
(46, '2025_09_10_105756_add_other_fields_to_settings_table', 35),
(47, '2025_09_10_112649_add_other_fields_to_settings_table', 36),
(48, '2025_09_13_020246_add_created_by_to_users_table', 37),
(49, '2025_09_15_143524_add_fiels_to_marks_register_table', 38),
(50, '2025_09_17_152634_add_period_type_to_settings_table', 39),
(51, '2025_09_17_153133_create_periods_table', 39),
(52, '2025_09_17_160758_add_fields_to_exams_table', 40),
(53, '2025_09_18_150355_add_created_by_to_periods_table', 41),
(54, '2025_09_18_150543_add_created_by_to_periods_table', 42),
(55, '2025_09_23_153806_add_other_fields_to_marks_register_table', 43),
(56, '2025_09_23_154810_add_fields_to_marks_register_table', 44),
(57, '2025_09_23_163303_add_marks_register_table', 45),
(58, '2025_09_23_165411_add_marks_register_table', 46),
(59, '2026_05_07_112957_create_permission_tables', 47),
(60, '2026_05_09_125149_add_fedapay_to_settings_table', 48);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 12),
(4, 'App\\Models\\User', 13),
(4, 'App\\Models\\User', 14),
(4, 'App\\Models\\User', 15),
(4, 'App\\Models\\User', 16),
(1, 'App\\Models\\User', 17),
(2, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(1, 'App\\Models\\User', 20),
(1, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(4, 'App\\Models\\User', 23),
(4, 'App\\Models\\User', 24),
(2, 'App\\Models\\User', 25),
(2, 'App\\Models\\User', 26),
(4, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29);

-- --------------------------------------------------------

--
-- Table structure for table `noticeboard_messages`
--

CREATE TABLE `noticeboard_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `communicates_id` int UNSIGNED DEFAULT NULL,
  `message_to` tinyint NOT NULL COMMENT 'user_type',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `noticeboard_messages`
--

INSERT INTO `noticeboard_messages` (`id`, `communicates_id`, `message_to`, `created_at`, `updated_at`) VALUES
(10, 1, 4, '2025-06-17 07:41:54', '2025-06-17 07:41:54'),
(11, 2, 3, '2025-06-17 07:43:07', '2025-06-17 07:43:07'),
(19, 7, 3, '2025-06-17 08:36:45', '2025-06-17 08:36:45'),
(20, 6, 2, '2025-06-17 08:39:29', '2025-06-17 08:39:29'),
(21, 6, 3, '2025-06-17 08:39:29', '2025-06-17 08:39:29'),
(22, 6, 4, '2025-06-17 08:39:29', '2025-06-17 08:39:29'),
(23, 5, 2, '2025-06-17 08:40:13', '2025-06-17 08:40:13'),
(24, 4, 2, '2025-06-17 08:40:49', '2025-06-17 08:40:49'),
(25, 3, 4, '2025-06-17 08:42:17', '2025-06-17 08:42:17'),
(27, 8, 3, '2025-06-24 12:19:42', '2025-06-24 12:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

CREATE TABLE `periods` (
  `id` bigint UNSIGNED NOT NULL,
  `settings_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`id`, `settings_id`, `name`, `start_date`, `end_date`, `is_current`, `status`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 1, 'Année Scolaire 2025 - 2026', '2025-09-15', '2026-06-22', 0, 1, 2, 0, '2025-09-18 15:24:18', '2025-09-18 15:24:18'),
(2, 1, 'Année Scolaire 2026 - 2027', '2026-09-14', '2026-06-12', 0, 1, 2, 0, '2025-09-18 15:25:44', '2025-09-22 15:38:16'),
(3, 1, '1er Semestre', '2025-10-27', '2025-11-03', 0, 1, 2, 0, '2025-09-18 15:56:24', '2025-09-22 15:36:22'),
(4, 1, '2ième Semestre', '2026-02-16', '2026-02-23', 0, 1, 2, 0, '2025-09-18 16:00:16', '2025-09-22 15:37:59'),
(5, 1, '1er Trimestre edited', '2026-04-20', '2026-04-27', 0, 0, 2, 0, '2025-09-20 09:35:01', '2025-09-22 16:02:29'),
(6, 1, '2ième Trimestre', '2026-02-02', '2026-02-09', 0, 0, 2, 0, '2025-09-20 09:38:02', '2025-09-22 15:38:12');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admins.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(2, 'admins.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(3, 'admins.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(4, 'admins.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(5, 'teachers.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(6, 'teachers.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(7, 'teachers.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(8, 'teachers.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(9, 'students.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(10, 'students.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(11, 'students.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(12, 'students.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(13, 'parents.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(14, 'parents.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(15, 'parents.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(16, 'parents.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(17, 'classes.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(18, 'classes.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(19, 'classes.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(20, 'classes.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(21, 'subjects.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(22, 'subjects.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(23, 'subjects.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(24, 'subjects.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(25, 'assign.subjects', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(26, 'assign.classes', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(27, 'timetable.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(28, 'timetable.manage', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(29, 'exams.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(30, 'exams.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(31, 'exams.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(32, 'exams.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(33, 'marks.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(34, 'marks.manage', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(35, 'attendance.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(36, 'attendance.manage', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(37, 'homework.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(38, 'homework.create', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(39, 'homework.edit', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(40, 'homework.delete', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(41, 'fees.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(42, 'fees.manage', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(43, 'noticeboard.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(44, 'noticeboard.manage', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(45, 'mail.send', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(46, 'settings.view', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(47, 'settings.manage', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(48, 'chat.access', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(2, 'teacher', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(3, 'student', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41'),
(4, 'parent', 'web', '2026-05-07 13:37:41', '2026-05-07 13:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(9, 2),
(27, 2),
(29, 2),
(33, 2),
(34, 2),
(35, 2),
(36, 2),
(37, 2),
(38, 2),
(39, 2),
(40, 2),
(43, 2),
(48, 2),
(27, 3),
(29, 3),
(33, 3),
(35, 3),
(37, 3),
(41, 3),
(43, 3),
(48, 3),
(9, 4),
(27, 4),
(29, 4),
(33, 4),
(35, 4),
(37, 4),
(41, 4),
(43, 4),
(48, 4);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `exam_id` int UNSIGNED DEFAULT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `subject_id` int UNSIGNED DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `start_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_marks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passing_marks` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `exam_id`, `class_id`, `subject_id`, `exam_date`, `start_time`, `end_time`, `room_number`, `full_marks`, `passing_marks`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(7, 11, 11, 1, '2025-02-14', '08:45', '13:00', 'G0012', '100', '50', 17, 0, '2025-02-11 18:00:20', '2025-02-11 18:00:20'),
(19, 6, 13, 5, '2025-03-04', '11:00', '13:00', 'AB4', '60', '80', 17, 0, '2025-02-12 15:07:45', '2025-02-12 15:07:45'),
(20, 9, 13, 3, '2025-02-19', '09:00', '11:30', 'BB2', '70', '80', 17, 0, '2025-02-12 15:08:31', '2025-02-12 15:08:31'),
(21, 5, 13, 4, '2025-02-21', '14:30', '17:30', 'RDC4', '100', '60', 17, 0, '2025-02-12 15:09:36', '2025-02-12 15:09:36'),
(22, 2, 13, 2, '2025-02-11', '08:00', '10:30', 'B0012', '50', '80', 17, 0, '2025-02-12 15:16:56', '2025-02-12 15:16:56'),
(23, 6, 8, 11, '2025-02-26', '08:00', '10:00', 'AB1', '100', '50', 17, 1, '2025-02-12 16:16:48', '2025-02-20 11:09:50'),
(24, 1, 8, 2, '2025-02-20', '14:00', '16:00', 'G0011', '100', '50', 17, 0, '2025-02-13 09:39:25', '2025-02-13 09:39:25'),
(25, 6, 8, 7, '2025-02-14', '09:00', '11:00', 'T001', '100', '80', 17, 1, '2025-02-13 14:41:16', '2025-02-20 11:09:50'),
(26, 6, 8, 11, '2025-02-26', '08:00', '10:00', 'AB1', '100', '50', 17, 1, '2025-02-13 14:41:16', '2025-02-20 11:09:50'),
(27, 6, 8, 5, '2025-02-27', '14:30', '16:30', 'AB04', '80', '70', 17, 0, '2025-02-20 11:09:50', '2025-02-20 11:09:50'),
(28, 6, 8, 7, '2025-02-14', '09:00', '11:00', 'T001', '100', '80', 17, 0, '2025-02-20 11:09:50', '2025-02-20 11:09:50'),
(29, 6, 8, 11, '2025-02-26', '08:00', '10:00', 'AB1', '100', '50', 17, 0, '2025-02-20 11:09:50', '2025-02-20 11:09:50'),
(30, 12, 11, 2, '2025-09-18', '08:00', '13:00', 'NS001', '100', '50', 2, 0, '2025-09-11 08:40:35', '2025-09-11 08:40:35'),
(31, 12, 11, 3, '2025-09-25', '09:00', '14:00', 'NS002', '100', '50', 2, 0, '2025-09-11 08:40:35', '2025-09-11 08:40:35'),
(32, 12, 11, 7, '2025-10-02', '10:00', '15:00', 'NS003', '100', '50', 2, 0, '2025-09-11 08:40:35', '2025-09-11 08:40:35'),
(33, 12, 11, 11, '2025-10-09', '11:00', '16:00', 'NS004', '100', '50', 2, 0, '2025-09-11 08:40:35', '2025-09-11 08:40:35'),
(34, 12, 11, 1, '2025-10-16', '12:00', '17:00', 'NS005', '100', '50', 2, 0, '2025-09-11 08:40:35', '2025-09-11 08:40:35'),
(35, 12, 11, 5, '2025-10-23', '13:00', '18:00', 'NS006', '100', '50', 2, 0, '2025-09-11 08:40:35', '2025-09-11 08:40:35');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `school_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uai_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `period_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paypal_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kkiapay_public_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kkiapay_private_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `kkiapay_secret_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stripe_public_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `stripe_secret_key` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fedapay_public_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fedapay_secret_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `school_name`, `address`, `phone`, `email`, `uai_number`, `school_type`, `period_type`, `paypal_email`, `kkiapay_public_key`, `kkiapay_private_key`, `kkiapay_secret_key`, `stripe_public_key`, `stripe_secret_key`, `fedapay_public_key`, `fedapay_secret_key`, `favicon`, `logo`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'School Management System', 'Cotonou / Hometown', '0190919096', 'schoolms@sms.bj', 'RCCM N°12458', 'private', NULL, 'regisattolou21@outlook.fr', 'd949680a85e3f3f61e79dbb39e9f612b73444b72', 'pk_5d50009d5f3788fa52eb78c58ba532d0312f9295d85437823346d9432673e99a', 'sk_3add2f8bc4bdea07353c54ed53656de28480203009eda574baccfd73df50a923', 'pk_test_51RtnMzRu7Cqiksla7Z7pbvs9d46RbUlP1DuVmmPQQrNVj8ZK629xm7X2IN9Wvpn927AwtU03sqi0fV5l0kGYIKa000fCRdU6TU', 'sk_test_51RtnMzRu7CqikslaXcHrR2BloFYZ3V1JcnXqmTVuBYzZN40IJn4RsNlMoxWKrcZ5rRTX1290R1rtM7WdIDVEaUzK00foaOH3fN', 'pk_live_Up4sCRDlx8nVFMIlk_8sqBsc', 'sk_live_AGZrLDMDMnhceos3Sro5Bnys', NULL, NULL, 1, 0, '2025-08-08 16:32:46', '2026-05-09 11:51:45');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `is_delete` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`id`, `name`, `type`, `created_by`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Communication Ecrite (CE)', 'theoretical', 2, 1, 0, '2025-09-15 15:38:23', '2025-09-15 15:38:23'),
(2, 'Lecture', 'theoretical', 2, 1, 0, '2025-09-15 15:38:34', '2025-09-15 15:38:34'),
(3, 'Sciences de la Vie et de la Terre (SVT)', 'practical', 2, 1, 0, '2025-09-15 15:38:48', '2025-09-15 15:38:48'),
(4, 'Physique Chimie Technologie (PCT)', 'practical', 2, 1, 0, '2025-09-15 15:39:04', '2025-09-15 15:39:04'),
(5, 'Histoire Géographie (HG)', 'theoretical', 2, 1, 0, '2025-09-15 15:39:16', '2025-09-15 15:39:16'),
(6, 'Éducation Scientifique et Technologique (EST)', 'theoretical', 2, 1, 0, '2025-09-15 15:39:46', '2025-09-15 15:39:46'),
(7, 'Éducation Physique et Sportive (EPS)', 'practical', 2, 1, 0, '2025-09-15 15:39:59', '2025-09-15 15:39:59'),
(8, 'Expression Écrite (EÉ)', 'theoretical', 2, 1, 0, '2025-09-15 15:40:53', '2025-09-15 15:40:53'),
(9, 'Dessin', 'practical', 2, 1, 0, '2025-09-15 15:41:06', '2025-09-15 15:41:06'),
(10, 'Anglais (Langue vivante 1)', 'practical', 2, 1, 0, '2025-09-15 15:41:37', '2025-09-15 15:41:37'),
(11, 'Espagnol (Langue vivante 2)', 'practical', 2, 1, 0, '2025-09-15 15:42:27', '2025-09-15 15:42:27'),
(12, 'Mathématiques', 'practical', 2, 1, 0, '2025-09-15 15:42:42', '2025-09-15 15:42:42'),
(13, 'Français', 'theoretical', 2, 1, 0, '2025-09-15 15:42:55', '2025-09-15 15:42:55'),
(14, 'Allemand (Langue vivante 2)', 'practical', 2, 1, 0, '2025-09-15 15:43:15', '2025-09-15 15:43:15'),
(15, 'Éducation Musicale (EM)', 'practical', 2, 1, 0, '2025-09-15 15:43:32', '2025-09-15 15:43:32'),
(16, 'Arts Plastiques (AP)', 'practical', 2, 1, 0, '2025-09-15 15:43:50', '2025-09-15 15:43:50'),
(17, 'Philosophie', 'theoretical', 2, 1, 0, '2025-09-15 15:44:45', '2025-09-15 15:44:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` tinyint NOT NULL DEFAULT '1',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `admission_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `blood_group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caste` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roll_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `weight` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `qualification` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `permanent_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_experience` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `last_name`, `email_verified_at`, `password`, `user_type`, `is_delete`, `remember_token`, `last_login`, `created_at`, `updated_at`, `address`, `admission_number`, `admission_date`, `blood_group`, `caste`, `class_id`, `date_of_birth`, `gender`, `height`, `mobile_number`, `occupation`, `parent_id`, `profile_picture`, `religion`, `roll_number`, `status`, `weight`, `marital_status`, `qualification`, `permanent_address`, `note`, `work_experience`, `created_by`) VALUES
(1, 'Tech', 'admintech@domain.bj', 'Admin', NULL, '$2y$12$RvTXaeEdLHY9LsomLqxaz..o3SqAqrmilD7nzuteWAhmKQidD.t66', 1, 0, 'PMgcxRNNBA4WxaMhKDQvBg4F16Xjb1MdsJZjFb1fMF32rFHW4bC94QLPZc7K', NULL, '2025-01-28 08:55:31', '2026-05-18 13:20:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 17),
(2, 'Main', 'adminmain@domain.bj', 'Admin', NULL, '$2y$12$RvTXaeEdLHY9LsomLqxaz..o3SqAqrmilD7nzuteWAhmKQidD.t66', 1, 0, 'rDQN3Nuaj7LwhWyrzFZg81PTuH3Q0NVxjDsRky3tgTkqshSAAjPhubmx5Ogp', '2026-05-08 15:46:16', '2025-01-28 08:55:31', '2026-05-18 13:20:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin21082025045452cgy525xsvmffsjbes3es.jpeg', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17),
(3, 'Teacher', 'teacherblack@gmail.com', 'Black', NULL, '$2y$12$Z9bxbrdNvMrHtSPHaTO3s.jcz4l2bJq/2B.3y7Kpgd/sUplOJ7wnK', 2, 0, NULL, '2025-09-13 06:04:56', '2025-01-28 09:13:13', '2025-09-13 06:04:56', 'adresse', NULL, '2013-02-06', NULL, NULL, NULL, '2000-05-21', 'male', NULL, '0190919096', NULL, NULL, 'teacher220820251022156hwl6gbmjkqqippy5us3.jpg', NULL, NULL, 1, NULL, 'Marié', 'qualification', 'permanent', 'note', 'experience', 1),
(4, 'Teacher1', 'teacherblack1@gmail.com', 'Black1', NULL, '$2y$12$BmHhF7B.mIgzqfuOxwLPruNrisRbrgSLbhMoYQIX/Yrv1StOBB4gO', 2, 0, 'TiXEc1LLGvN1q7b1kmEhRdIFN4gZGQX4zrhrYFxnwA6ejEkmpMYrYVzaibXT', NULL, '2025-01-28 09:14:54', '2025-02-07 08:25:27', 'adresse 1', NULL, NULL, NULL, NULL, NULL, '1995-02-08', 'female', NULL, '0102030405', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Célibataire', 'qualification 1', 'permanent 1', 'note 1', 'experience 1', 1),
(5, 'Teacher2', 'teacherblack2@gmail.com', 'Black2', NULL, '$2y$12$T2OojBm60mlR5bMfNwKzJO1yFPk/vUO1tHQnRWPTC4V5QcBHD/Fki', 2, 0, NULL, NULL, '2025-01-28 09:17:45', '2025-02-07 16:14:05', 'adresse 2', NULL, '2010-02-24', NULL, NULL, NULL, '1997-12-16', 'male', NULL, '0195041400', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'Marié', 'qualification 2', 'permanent 2', 'note 2', 'experience 2', 20),
(6, 'Teacher3', 'teacherblack3@gmail.com', 'Black3', NULL, '$2y$12$YcA5l2FyMPeEsn6YcUsUhOodwiYYRbSdMnE07G4Cb9vuLFyHTyyl.', 2, 0, NULL, NULL, '2025-01-28 09:19:46', '2025-02-07 16:13:31', 'adresse 3', NULL, '2013-02-12', NULL, NULL, NULL, '1996-02-20', 'female', NULL, '0105080906', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Célibataire', 'qualification 3', 'permanent 3', 'note 3', 'experience 3', 20),
(7, 'Student', 'studentblack@gmail.com', 'Black', NULL, '$2y$12$3roLWXPohMVfuWfTs6xdRexaE52MvrITTbNi1RQgXnUm50B9iXVFy', 3, 0, '23xMwNrBArp6fzOZlKNn4Mh1GFh716aQXmTFEf2kLLKCK3xLn4hIQK0JeDHr', '2026-05-11 15:09:40', '2025-01-28 09:46:08', '2026-05-11 15:09:40', NULL, 'BJSMS00000', '2025-01-23', 'a+', 'Caste', 11, '2006-03-03', 'male', '1.72', '0123455678', NULL, 12, NULL, 'Chrétien', '3', 1, '63', NULL, NULL, NULL, NULL, NULL, 2),
(8, 'Student1', 'studentblack1@gmail.com', 'Black1', NULL, '$2y$12$aP3ec.Ohm/9uZSn.XHJ7zuvHfIIdDutCRLMsuAlvn1ztfpdiLVkiq', 3, 0, NULL, NULL, '2025-01-28 09:49:26', '2025-02-10 08:53:53', NULL, 'BJSMS00001', '0004-06-03', 'b+', 'Caste 1', 13, '1999-02-05', 'female', '1.80', '0190919006', NULL, 13, NULL, 'Catholique', '3', 1, '65', NULL, NULL, NULL, NULL, NULL, 2),
(9, 'Student2', 'studentblack2@gmail.com', 'Black2', NULL, '$2y$12$McxzsuMo2UADo5sEj3dRauqntLb4/u4eSG.lZDeZ5BbdYm6pzR/L2', 3, 0, NULL, NULL, '2025-01-28 09:51:10', '2025-04-26 10:31:43', NULL, 'BJSMS00002', '2023-06-25', 'a-', 'Caste 2', 10, '2000-04-05', 'male', '1.76', '0168980552', NULL, 14, NULL, 'Boudhiste', '3', 1, '85', NULL, NULL, NULL, NULL, NULL, 21),
(10, 'Student3', 'studentblack3@gmail.com', 'Black3', NULL, '$2y$12$301ZZGllDXdGw51wHObaEOphMtdKSrByeowk2aY1fT9YJH8m7Tc7y', 3, 0, NULL, NULL, '2025-01-28 09:54:34', '2025-09-13 03:53:15', NULL, 'BJSMS00003', '2023-08-26', 'a+', 'Caste 3', 5, '2009-05-24', 'female', '1.96', '0195200400', NULL, 15, NULL, 'Chrétien', '3', 0, '58', NULL, NULL, NULL, NULL, NULL, 21),
(11, 'Student4', 'studentblack4@gmail.com', 'Black4', NULL, '$2y$12$Pg2/evePPpJ7T3KUGsfb6.KZl.DzOssAwLgiRIxdUI6cHDWs.CS/O', 3, 1, NULL, '2025-09-08 16:22:03', '2025-01-28 09:57:13', '2025-11-12 14:14:15', NULL, 'BJSMS00004', '2021-06-23', 'ab+', 'Caste 4', 8, '1998-04-26', 'male', '1.65', '0105080906', NULL, 16, 'student08092025114405wsvngacamoxtkhxpizhl.jpg', 'Céleste', '3', 1, '75', NULL, NULL, NULL, NULL, NULL, 17),
(12, 'Parent', 'parentblack@gmail.com', 'Black', NULL, '$2y$12$0cBL9a4JziZiDWhysYRKh.irIwhyum/VGfxn8IEb89B01XfNWkmsW', 4, 0, NULL, '2025-09-13 05:49:52', '2025-01-28 09:58:30', '2025-09-13 05:49:52', 'Abomey-Calavi', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '0190009096', 'Professeur', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17),
(13, 'Parent1', 'parentblack1@gmail.com', 'Black1', NULL, '$2y$12$HVJ0sJZ9eNOi6PGCCFBt..DEo8fg.HA8P2kFUR/kQdjD5JHGIQHu6', 4, 0, NULL, NULL, '2025-01-28 09:59:32', '2025-02-12 15:04:01', 'Abomey-Calavi', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '0102030405', 'Menusier', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(14, 'Parent2', 'parentblack2@gmail.com', 'Black2', NULL, '$2y$12$afvZEUB757KqNdHIoQcQpujKN1vwDT86om6cUlr.aJXmZVO3nJ61W', 4, 0, NULL, NULL, '2025-01-28 10:00:35', '2025-01-28 10:00:35', 'Dèkoungbé', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '0190001400', 'Developpeur', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(15, 'Parent3', 'parentblack3@gmail.com', 'Black3', NULL, '$2y$12$Gex7uHD3.hviW4tBpyZ41.yE/pd4kcebgi4nIQ2ZgzqvGcsjI35ze', 4, 0, NULL, NULL, '2025-01-28 10:01:23', '2025-08-22 14:05:53', 'Cotonou', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '010200305', 'Docteur', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(16, 'Parent4', 'parentblack4@gmail.com', 'Black4', NULL, '$2y$12$aKJ3lpK3QLSm.S3nuXfWMeTjj7oCYMhvGYiJfCPyukGMc4JZ6ggSS', 4, 0, NULL, NULL, '2025-01-28 10:02:28', '2025-08-22 14:05:29', 'Ganhi', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '0102555405', 'Mécanicien', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(17, 'ATTOLOU', 'rattolou@vippinterstis.com', 'Régis', NULL, '$2y$12$RvTXaeEdLHY9LsomLqxaz..o3SqAqrmilD7nzuteWAhmKQidD.t66', 1, 0, 'c7WjKYhLEeNkeo0JDDWzaK51d8MmthKBQdH9CSzM1xkGGLXIoVeLnt0Vt7pb', '2026-06-01 10:01:51', '2025-02-06 14:54:32', '2026-06-01 10:01:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 20),
(18, 'Teacher4', 'teacherblack4@gmail.com', 'Black4', NULL, '$2y$12$xiOp92XxWJkFXAIeUMBdo.h5psxIXA0Ej0S3wKEaQ/7XEr7YgeZAK', 2, 0, NULL, NULL, '2025-02-07 16:07:56', '2025-02-07 16:12:21', 'Adresse actuelle 4', NULL, '2014-02-07', NULL, NULL, NULL, '1988-02-02', 'male', NULL, '0140520635', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Divorcé', 'Qualification 4', 'Adresse permanent 4', 'Note 4', 'Expérience 4', 20),
(19, 'Student5', 'studentblack5@gmail.com', 'Black5', NULL, '$2y$12$oJmBhqQD42P6hCRUOLQlHeCKNNRpYuUOsxuJm5lWpcP883wYNT/Za', 3, 0, NULL, '2026-05-11 14:51:31', '2025-02-20 16:54:13', '2026-05-11 14:51:31', NULL, 'BJSMS00005', '2025-02-10', 'o+', 'caste 5', 8, '1996-02-20', 'female', '1.98', '010508809', NULL, 15, NULL, 'Thron', '3', 1, '95', NULL, NULL, NULL, NULL, NULL, 21),
(20, 'ATTOLOU', 'ferencattolou21@gmail.com', 'Ferenc', NULL, '$2y$12$RvTXaeEdLHY9LsomLqxaz..o3SqAqrmilD7nzuteWAhmKQidD.t66', 1, 0, NULL, NULL, '2025-06-23 13:34:18', '2026-05-18 13:20:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'admin21082025031739cwfkxww3r3ciegfvmq0b.jpg', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 21),
(21, 'AGBANNONDE', 'magbanonde@vippinterstis.com', 'Warris', NULL, '$2y$12$4.NrVTzlbS5DP4dSWZaZ4.rtLkztg72k9nqpUqCaNR3TvsL05SzPO', 1, 0, NULL, '2025-09-04 11:08:18', '2025-06-25 09:01:47', '2026-05-18 13:23:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 17),
(22, 'Student6', 'studentblack6@gmail.com', 'Black6', NULL, '$2y$12$0zKgQXTX2s.o9rBYX1asde8xDQDzHHuAjVwdM0PEfAM1.wokM2jrS', 3, 0, NULL, '2025-09-16 14:37:19', '2025-07-07 13:54:35', '2025-09-16 14:37:19', NULL, 'BJSMS00006', '2024-08-20', 'ab-', 'Caste 6', 14, '2006-03-14', 'male', '2 01', '0194241400', NULL, 24, 'student21082025044324ifrml5nxfdgddiwghdpk.jpg', 'Judaîsme', '3', 1, '68', NULL, NULL, NULL, NULL, NULL, 1),
(23, 'Parent5', 'parentblack5@gmail.com', 'Black5', NULL, '$2y$12$oFzgqwnPPocR8jD0WivhZuUV3VRFZkQlpkYEvzuhTRhrQOVF37aiG', 4, 0, NULL, NULL, '2025-08-16 12:44:06', '2025-08-16 12:44:06', 'Porto-Novo', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '0158966352', 'Conseiller Pédagogique', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(24, 'Parent6', 'parentblack6@gmail.com', 'Black6', NULL, '$2y$12$T3zXeSbo5qdDrnB7zPCHweGpZDivlmoxzuyYJuESCjmCoFerFzFGu', 4, 0, NULL, NULL, '2025-08-16 12:45:25', '2025-08-22 13:48:16', 'Akpakpa', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '0196635254', 'Agent de sécurité', NULL, 'parent22082025034816pvu8tm2hhzzdweutxqou.jpg', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 20),
(25, 'Teacher5', 'teacherblack5@gmail.com', 'Black5', NULL, '$2y$12$UIzKD.BTfVT1CaIJJv/zDuwpGw4/pNwME8vAAHfSkSVzHkdSeO6FS', 2, 0, NULL, NULL, '2025-08-23 09:19:42', '2025-08-23 09:19:42', 'Akassato', NULL, '2025-08-01', NULL, NULL, NULL, '1996-08-22', 'male', NULL, '0152638596', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'Marié', 'Anglais', 'Ouèdo', 'Note5', 'Expérience5', 21),
(26, 'Amina', 'aminakouassi@example.com', 'KOUASSI', NULL, '$2y$12$oaufL5Ez.Nmf2QlmKXfdGueNEV2y8DEgARAySP3N/YWPnuVUhMMnW', 2, 0, NULL, '2025-09-16 11:18:34', '2025-09-16 11:17:48', '2025-09-16 11:18:34', 'Abomey-Calavi', NULL, '2025-09-01', NULL, NULL, NULL, '1997-05-15', 'female', NULL, '0197000001', NULL, NULL, 'teacher16092025011747ktkuegywozqxupqqmizx.jpg', NULL, NULL, 1, NULL, 'Célibataire', 'Professeur de Mathématiques', 'Quartier Zogbadjè', 'Passionné de sciences', '5 ans', 2),
(27, 'AGBODANOU', 'yannickagbodanou@sms.com', 'Yannick', NULL, '$2y$12$zpq97E5Wtrfi9jObyF4meOJJ3yh1FrN9uVZ/6l5tFjWE6epkSueHm', 3, 1, NULL, '2025-09-17 12:10:03', '2025-09-16 11:37:29', '2025-11-06 15:30:06', NULL, 'ADM2025002', '2025-08-14', 'a+', 'Caste7', 17, '2009-11-22', 'male', '1.50', '0197000002', NULL, 28, 'student16092025013729szonzqca6uwnzrjdlotr.png', 'Musulman', 'RN002', 1, '65', NULL, NULL, NULL, NULL, NULL, 2),
(28, 'MENSAH', 'chantalmensah@example.com', 'Chantal', NULL, '$2y$12$iUJEaPPu63NapDUtS2Y/4.d3Q3JV6u9gFfhx79QSGPL6uhsQvZvcG', 4, 0, NULL, '2025-09-16 13:26:23', '2025-09-16 11:39:19', '2025-09-16 13:26:23', 'Cotonou, Akpakpa', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '0197000003', 'Commerçante', NULL, 'parent160920250139193kpa0bhwu1s92olv9hwc.jpg', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 2),
(29, 'TCHIBOZO', 'ulrichtchibozo@sms.com', 'Ulrich', NULL, '$2y$12$goOO9HdtrLGg0fLvDJALGu8blaz.E34KDF/bg/e4HYn1INiNUOhSO', 3, 0, NULL, NULL, '2025-09-16 13:23:45', '2025-09-16 13:26:00', NULL, 'ADM2025001', '2025-04-02', 'b+', 'Caste 9', 17, '2003-08-16', 'male', '1.80', '0197000006', NULL, 28, 'student16092025032345zmfsekykpfztj6r6p3ft.jpg', 'Chrétienne', 'RN004', 1, '85', NULL, NULL, NULL, NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `week`
--

CREATE TABLE `week` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `day` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `week`
--

INSERT INTO `week` (`id`, `name`, `created_at`, `updated_at`, `day`) VALUES
(1, 'Lundi', '2025-01-31 09:41:27', '2025-01-31 09:41:29', 1),
(2, 'Mardi', '2025-01-31 09:41:35', '2025-01-31 09:41:34', 2),
(3, 'Mercredi', '2025-01-31 09:41:53', '2025-01-31 09:41:55', 3),
(4, 'Jeudi', '2025-01-31 09:42:06', '2025-01-31 09:42:08', 4),
(5, 'Vendredi', '2025-01-31 09:42:17', '2025-01-31 09:42:18', 5),
(6, 'Samedi', '2025-01-31 09:42:25', '2025-01-31 09:42:26', 6),
(7, 'Dimanche', '2025-01-31 09:42:34', '2025-01-31 09:42:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `works`
--

CREATE TABLE `works` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `subject_id` int UNSIGNED DEFAULT NULL,
  `work_date` date DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `document_file` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `works`
--

INSERT INTO `works` (`id`, `class_id`, `subject_id`, `work_date`, `submission_date`, `document_file`, `description`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 13, 2, '2025-06-26', '2025-07-03', 'homework_admin01072025063538egx7st9q4ihuhdjqnyck.txt', '<p>Le nom du fichier s\'affiche en dessous du texte \"PDF, DOCX, PPTX...\"</p>', 17, 0, '2025-06-26 13:03:26', '2025-07-01 16:35:38'),
(2, 14, 3, '2025-06-26', '2025-07-04', 'homework27062025031218ukobcoorewx2uvrwxuo0.pdf', '<p>Souhaite-tu que je t’aide aussi à afficher dynamiquement le type ou le statut de la matière dans un badge à côté du nom ? Ça pourrait enrichir visuellement la liste déroulante.</p>', 17, 0, '2025-06-27 13:12:18', '2025-06-27 13:12:18'),
(3, 13, 4, '2025-06-27', '2025-07-04', 'homework27062025032301vohcwdbbp34nzjkl1il5.pdf', '<p>Merci pour le code, Régis. Ça m’éclaire beaucoup mieux sur la structure.</p>', 17, 0, '2025-06-27 13:23:01', '2025-06-27 13:23:01'),
(4, 8, 5, '2025-06-27', '2025-07-01', 'homework27062025032928jf1p3abytxbyqbc7yk3c.pdf', '<p>Merci pour le code, Régis. Photo de Régis...<br></p>', 17, 0, '2025-06-27 13:29:28', '2025-06-27 13:29:28'),
(5, 8, 7, '2025-06-28', '2025-07-02', 'homework_admin01072025063451pg71omrwowfl3zmwizir.JPEG', '<p><b>Titre du travail&nbsp;</b></p><p>Souhaite-tu que je t’aide aussi à afficher dynamiquement le type ou le statut de la matière dans un badge à côté du nom ? </p><p><strong>Attention</strong> : assure-toi que le contenu est sécurisé avant d’afficher du HTML brut, surtout s’il provient d’un utilisateur. Tu pourrais envisager d’utiliser <code>@sanitize</code> ou d’envelopper la sortie avec une vérification conditionnelle ou un purificateur comme.</p>', 17, 0, '2025-06-27 13:30:12', '2025-07-07 13:26:51'),
(6, 3, 6, '2025-06-29', '2025-07-05', 'homework_admin01072025063111uny3z47j6s4m29jraz3b.docx', '<p>Ça pourrait enrichir visuellement la liste déroulante.</p>', 17, 0, '2025-06-27 13:30:46', '2025-07-01 16:31:11'),
(7, 8, 4, '2025-06-30', '2025-07-07', 'homework_admin01072025063057vcephj8dvlzhujuy6rqs.xlsx', '<p>Création d\'un nouveau travail de maison pour la classe de 5ième avec la matière Physique Chimie et Technologie. J\'ai ajouté un fichier excel pour la modification.<br></p>', 17, 0, '2025-06-30 06:59:54', '2025-07-01 16:30:57'),
(8, 11, 1, '2025-06-30', '2025-07-08', 'homework_admin01072025062319fbvqgsgmimxy5mtad74t.pdf', '<p>Première création d\'un travail de maison dans la session d\'un professeur.<br></p>', 3, 0, '2025-06-30 09:18:25', '2025-07-01 16:23:19'),
(9, 11, 5, '2025-07-01', '2025-07-08', 'homework_admin01072025062146ectgcdch2ol3ml4as1ct.pdf', '<p>Salut à tous je voudrais faire une affiche dans canva...<br></p>', 2, 0, '2025-07-01 16:21:46', '2025-07-01 16:21:46'),
(10, 17, 3, '2025-10-01', '2025-10-08', 'homework_admin16092025024955p5h8eydeqyykstgjauzt.xlsx', '', 2, 0, '2025-09-16 12:49:55', '2025-09-16 12:49:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_subject`
--
ALTER TABLE `class_subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_teacher`
--
ALTER TABLE `class_teacher`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_timetable`
--
ALTER TABLE `class_timetable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `communicates`
--
ALTER TABLE `communicates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_period_id_foreign` (`period_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feescollections`
--
ALTER TABLE `feescollections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `homework`
--
ALTER TABLE `homework`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marks_grade`
--
ALTER TABLE `marks_grade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marks_register`
--
ALTER TABLE `marks_register`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `noticeboard_messages`
--
ALTER TABLE `noticeboard_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `periods`
--
ALTER TABLE `periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `periods_settings_id_foreign` (`settings_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `week`
--
ALTER TABLE `week`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `works`
--
ALTER TABLE `works`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `class_subject`
--
ALTER TABLE `class_subject`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `class_teacher`
--
ALTER TABLE `class_teacher`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `class_timetable`
--
ALTER TABLE `class_timetable`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `communicates`
--
ALTER TABLE `communicates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feescollections`
--
ALTER TABLE `feescollections`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `homework`
--
ALTER TABLE `homework`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `marks_grade`
--
ALTER TABLE `marks_grade`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `marks_register`
--
ALTER TABLE `marks_register`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `noticeboard_messages`
--
ALTER TABLE `noticeboard_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `periods`
--
ALTER TABLE `periods`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `week`
--
ALTER TABLE `week`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `works`
--
ALTER TABLE `works`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_settings_id_foreign` FOREIGN KEY (`settings_id`) REFERENCES `settings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
