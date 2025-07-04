-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 01, 2025 at 08:18 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.28

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendances`
--

INSERT INTO `attendances` (`id`, `class_id`, `attendance_date`, `student_id`, `attendance_type`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 13, '2025-02-28', 8, 4, 17, '2025-05-08 10:32:10', '2025-05-08 11:20:34'),
(2, 8, '2025-02-28', 19, 4, 17, '2025-05-08 11:20:48', '2025-05-10 12:51:23'),
(3, 8, '2025-02-28', 11, 2, 17, '2025-05-08 11:20:49', '2025-05-08 12:43:08'),
(4, 11, '2025-02-28', 7, 1, 17, '2025-05-08 11:21:22', '2025-05-08 11:21:22'),
(5, 8, '2025-03-01', 19, 3, 17, '2025-05-08 13:23:50', '2025-05-08 13:23:50'),
(6, 8, '2025-03-01', 11, 1, 3, '2025-05-10 12:55:22', '2025-05-10 12:55:22'),
(7, 13, '2025-03-01', 8, 4, 3, '2025-05-10 12:55:51', '2025-05-10 12:55:51'),
(8, 5, '2025-05-18', 10, 1, 17, '2025-05-24 12:18:47', '2025-05-24 12:18:47'),
(9, 5, '2025-05-21', 10, 2, 17, '2025-05-24 12:18:56', '2025-05-24 12:18:56'),
(10, 8, '2025-03-20', 19, 3, 17, '2025-05-24 12:19:32', '2025-05-24 12:19:32'),
(11, 8, '2025-03-20', 11, 4, 17, '2025-05-24 12:19:37', '2025-05-24 12:19:37');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `is_delete` tinyint NOT NULL DEFAULT '0',
  `created_by` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `name`, `status`, `is_delete`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'CI', 1, 0, 1, '2025-01-28 09:20:59', '2025-01-28 09:20:59'),
(2, 'CP', 1, 0, 1, '2025-01-28 09:21:07', '2025-01-28 09:21:07'),
(3, 'CE1', 1, 0, 1, '2025-01-28 09:21:20', '2025-01-29 14:22:11'),
(4, 'CE2', 1, 0, 1, '2025-01-28 09:21:38', '2025-01-29 14:22:00'),
(5, 'CM1', 1, 0, 1, '2025-01-28 09:21:57', '2025-01-28 09:21:57'),
(6, 'CM2', 0, 0, 1, '2025-01-28 09:22:07', '2025-01-28 09:22:07'),
(7, '6 ième', 0, 0, 2, '2025-01-28 09:25:31', '2025-01-28 09:25:31'),
(8, '5 ième', 1, 0, 2, '2025-01-28 09:25:38', '2025-01-28 09:25:38'),
(9, '4 ième', 0, 0, 2, '2025-01-28 09:25:45', '2025-01-28 09:25:45'),
(10, '3 ième', 1, 0, 2, '2025-01-28 09:25:51', '2025-01-28 09:25:51'),
(11, '2 nd A', 1, 0, 2, '2025-01-28 09:28:24', '2025-01-28 09:28:24'),
(12, '2 nd B', 0, 0, 2, '2025-01-28 09:28:33', '2025-01-28 09:28:33'),
(13, '2 nd C', 1, 0, 2, '2025-01-28 09:28:43', '2025-02-03 12:43:36'),
(14, '2 nd D', 1, 0, 2, '2025-01-28 09:28:50', '2025-02-11 10:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `class_subject`
--

CREATE TABLE `class_subject` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED NOT NULL,
  `subject_id` int UNSIGNED NOT NULL,
  `created_by` int UNSIGNED NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_subject`
--

INSERT INTO `class_subject` (`id`, `class_id`, `subject_id`, `created_by`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 4, 0, 2, 1, 0, '2025-01-28 12:01:26', '2025-02-11 10:18:28'),
(2, 14, 3, 2, 1, 0, '2025-01-28 12:01:26', '2025-01-28 12:01:26'),
(3, 14, 2, 2, 1, 0, '2025-01-28 12:01:26', '2025-01-28 12:01:26'),
(4, 1, 0, 2, 1, 0, '2025-01-28 12:01:26', '2025-02-11 10:18:12'),
(5, 2, 0, 2, 1, 0, '2025-01-28 12:02:58', '2025-02-11 10:06:56'),
(9, 1, 10, 1, 1, 0, '2025-01-29 08:31:43', '2025-01-29 08:31:43'),
(11, 3, 8, 2, 1, 0, '2025-01-29 14:24:53', '2025-01-29 14:24:53'),
(12, 3, 6, 2, 1, 0, '2025-01-29 14:24:53', '2025-01-29 14:24:53'),
(15, 2, 9, 17, 0, 0, '2025-02-08 13:42:42', '2025-02-08 13:42:42'),
(20, 13, 5, 17, 1, 0, '2025-02-11 19:42:08', '2025-02-11 19:42:08'),
(21, 13, 4, 17, 1, 0, '2025-02-11 19:42:08', '2025-02-11 19:42:08'),
(22, 13, 3, 17, 1, 0, '2025-02-11 19:42:08', '2025-02-11 19:42:08'),
(23, 13, 2, 17, 1, 0, '2025-02-11 19:42:08', '2025-02-11 19:42:08'),
(25, 5, 8, 17, 0, 0, '2025-02-13 09:31:20', '2025-03-08 12:19:18'),
(26, 5, 6, 17, 1, 0, '2025-02-13 09:31:20', '2025-02-13 09:31:20'),
(27, 11, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:40:25'),
(28, 7, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:36:26'),
(29, 5, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:58:38'),
(30, 4, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:36:35'),
(31, 3, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:36:17'),
(32, 2, 0, 17, 0, 0, '2025-02-13 09:36:38', '2025-03-08 11:35:59'),
(37, 11, 5, 17, 0, 0, '2025-03-08 11:35:44', '2025-03-08 11:35:44'),
(38, 11, 2, 17, 0, 0, '2025-03-08 11:35:44', '2025-03-08 11:35:44'),
(39, 11, 1, 17, 1, 0, '2025-03-08 11:35:44', '2025-06-30 09:15:45'),
(40, 8, 5, 17, 1, 0, '2025-03-08 12:31:31', '2025-03-08 12:31:31'),
(41, 8, 4, 17, 1, 0, '2025-03-08 12:31:31', '2025-03-08 12:31:31'),
(42, 8, 3, 17, 1, 0, '2025-03-08 12:31:31', '2025-03-08 12:31:31'),
(43, 8, 2, 17, 1, 0, '2025-03-08 12:31:31', '2025-03-08 12:31:31'),
(44, 8, 7, 17, 1, 0, '2025-03-08 12:31:31', '2025-03-08 12:31:31');

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
(29, 8, 18, 17, 1, 0, '2025-02-13 09:12:21', '2025-02-13 09:12:21'),
(35, 11, 18, 17, 0, 0, '2025-03-08 11:18:24', '2025-03-08 11:18:24'),
(36, 11, 6, 17, 0, 0, '2025-03-08 11:18:24', '2025-03-08 11:18:24'),
(37, 11, 5, 17, 0, 0, '2025-03-08 11:18:24', '2025-03-08 11:18:24'),
(38, 11, 4, 17, 0, 0, '2025-03-08 11:18:24', '2025-03-08 11:18:24'),
(39, 11, 3, 17, 1, 0, '2025-03-08 11:18:24', '2025-05-10 14:35:45'),
(40, 5, 6, 17, 1, 0, '2025-05-24 12:28:15', '2025-05-24 12:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `class_timetable`
--

CREATE TABLE `class_timetable` (
  `id` bigint UNSIGNED NOT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `subject_id` int UNSIGNED DEFAULT NULL,
  `week_id` int UNSIGNED DEFAULT NULL,
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_timetable`
--

INSERT INTO `class_timetable` (`id`, `class_id`, `subject_id`, `week_id`, `start_time`, `end_time`, `room_number`, `created_at`, `updated_at`) VALUES
(1, 13, 3, 1, '08:00', '12:00', '0001', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(2, 13, 3, 2, '14:00', '18:00', '0002', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(3, 13, 3, 3, '10:00', '13:00', '0003', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(4, 13, 3, 4, '09:00', '11:00', '0004', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(5, 13, 3, 5, '10:00', '14:00', '0005', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(6, 13, 3, 6, '15:00', '18:00', '0006', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(7, 13, 3, 7, '16:00', '18:00', '0007', '2025-02-01 11:50:39', '2025-02-01 11:50:39'),
(8, 14, 2, 1, '08:30', '10:30', 'B1', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(9, 14, 2, 2, '12:30', '12:30', 'B2', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(10, 14, 2, 3, '14:30', '16:30', 'B3', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(11, 14, 2, 4, '16:30', '18:30', 'B4', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(12, 14, 2, 5, '09:00', '12:00', 'B5', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(13, 14, 2, 6, '14:00', '17:00', 'B6', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(14, 14, 2, 7, '10:30', '12:30', 'b7', '2025-02-01 11:53:52', '2025-02-01 11:53:52'),
(15, 11, 1, 1, '15:00', '18:00', 'B1', '2025-02-01 11:59:12', '2025-02-01 11:59:12'),
(16, 3, 6, 1, '08:00', '09:00', 'Salle 1', '2025-02-01 12:21:39', '2025-02-01 12:21:39');

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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `name`, `note`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Evaluation 1', 'Note 1', 17, 0, '2025-02-10 15:28:57', '2025-02-10 15:28:57'),
(2, 'Evaluation 2', 'Note 2', 17, 0, '2025-02-10 15:32:42', '2025-02-10 15:32:42'),
(3, 'Evaluation 3', 'Note 3', 17, 0, '2025-02-10 15:32:58', '2025-02-10 15:32:58'),
(4, 'Evaluation 4', 'Note 4', 17, 0, '2025-02-10 15:33:15', '2025-02-10 15:33:15'),
(5, 'Evaluation 5', 'Note 5', 17, 0, '2025-02-10 15:33:34', '2025-02-10 15:33:34'),
(6, 'Evaluation 6', 'Note 6', 17, 0, '2025-02-10 15:34:15', '2025-02-10 15:34:15'),
(7, 'Evaluation 7', 'Note 7', 17, 0, '2025-02-10 15:34:31', '2025-02-10 15:34:31'),
(8, 'Evaluation 8', 'Note 8', 17, 0, '2025-02-10 15:34:50', '2025-02-10 15:34:50'),
(9, 'Evaluation 9', 'Note 9', 17, 0, '2025-02-10 15:35:07', '2025-02-10 15:35:07'),
(10, 'Evaluation 10', 'Note 10', 17, 0, '2025-02-10 15:35:29', '2025-02-10 15:35:29'),
(11, 'Evaluation 11', 'Note 11', 17, 0, '2025-02-10 15:35:44', '2025-02-10 15:35:44'),
(12, 'Evaluation 12', 'Note 12', 17, 0, '2025-02-10 15:35:57', '2025-02-10 15:35:57');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marks_grade`
--

CREATE TABLE `marks_grade` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `class_work` int DEFAULT NULL,
  `home_work` int DEFAULT NULL,
  `exam_work` int DEFAULT NULL,
  `test_work` int DEFAULT NULL,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `passing_marks` int DEFAULT NULL,
  `full_marks` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `marks_register`
--

INSERT INTO `marks_register` (`id`, `student_id`, `exam_id`, `class_id`, `subject_id`, `class_work`, `home_work`, `exam_work`, `test_work`, `created_by`, `is_delete`, `created_at`, `updated_at`, `passing_marks`, `full_marks`) VALUES
(1, 11, 6, 8, 5, 20, 20, 10, 10, 17, 0, '2025-02-20 16:22:51', '2025-04-26 08:11:46', 70, 80),
(2, 11, 6, 8, 7, 15, 15, 15, 30, 17, 1, '2025-02-20 16:22:51', '2025-03-24 14:15:44', 80, 100),
(3, 11, 6, 8, 11, 10, 20, 19, 16, 17, 0, '2025-02-20 16:22:51', '2025-04-19 09:09:44', 50, 100),
(4, 19, 6, 8, 5, 20, 20, 20, 20, 3, 0, '2025-02-21 13:26:08', '2025-03-25 15:47:50', 70, 80),
(5, 19, 6, 8, 7, 30, 36, 15, 10, 3, 0, '2025-02-21 13:45:14', '2025-03-25 15:47:50', 80, 100),
(6, 19, 6, 8, 11, 15, 30, 25, 30, 3, 0, '2025-02-21 13:45:14', '2025-03-25 15:47:50', 50, 100),
(7, 8, 6, 13, 5, 20, 5, 15, 10, 3, 0, '2025-03-15 13:15:11', '2025-04-19 09:08:22', 80, 60);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(24, '2025_06_26_095209_create_works_table', 13);

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
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_marks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passing_marks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
(29, 6, 8, 11, '2025-02-26', '08:00', '10:00', 'AB1', '100', '50', 17, 0, '2025-02-20 11:09:50', '2025-02-20 11:09:50');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(1, 'Communication Ecrite', 'theoretical', 2, 1, 0, '2025-01-28 09:30:11', '2025-01-28 09:30:11'),
(2, 'Lecture', 'theoretical', 2, 1, 0, '2025-01-28 09:30:37', '2025-01-29 14:21:37'),
(3, 'Science de la Vie et de Terre', 'practical', 2, 1, 0, '2025-01-28 09:30:53', '2025-01-28 09:30:53'),
(4, 'Physique Chimie et Technologie', 'practical', 2, 1, 0, '2025-01-28 09:31:08', '2025-01-29 14:21:29'),
(5, 'Histoire Géographie', 'theoretical', 2, 1, 0, '2025-01-28 12:04:43', '2025-01-28 12:04:43'),
(6, 'EST', 'theoretical', 2, 1, 0, '2025-01-28 12:06:37', '2025-01-28 12:06:37'),
(7, 'EPS', 'practical', 2, 1, 0, '2025-01-28 12:06:47', '2025-01-28 12:06:47'),
(8, 'ES', 'theoretical', 2, 1, 0, '2025-01-28 12:06:55', '2025-01-28 12:06:55'),
(9, 'Expression écrite', 'theoretical', 2, 1, 0, '2025-01-28 12:07:56', '2025-01-28 12:07:56'),
(10, 'Dessin', 'practical', 2, 1, 0, '2025-01-28 12:08:08', '2025-01-28 12:08:08'),
(11, 'Anglais', 'theoretical', 17, 1, 0, '2025-02-08 11:48:59', '2025-03-24 14:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` tinyint NOT NULL DEFAULT '1',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `admission_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `blood_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caste` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_id` int UNSIGNED DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roll_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` text COLLATE utf8mb4_unicode_ci,
  `qualification` text COLLATE utf8mb4_unicode_ci,
  `permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_experience` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `last_name`, `email_verified_at`, `password`, `user_type`, `is_delete`, `remember_token`, `created_at`, `updated_at`, `address`, `admission_number`, `admission_date`, `blood_group`, `caste`, `class_id`, `date_of_birth`, `gender`, `height`, `mobile_number`, `occupation`, `parent_id`, `profile_picture`, `religion`, `roll_number`, `status`, `weight`, `marital_status`, `qualification`, `permanent_address`, `note`, `work_experience`) VALUES
(1, 'Tech', 'admintech@domain.bj', 'Admin', NULL, '$2y$12$A4u5B4HLF3WJJsHVgmD1ae6H9VSAyp4xTyEXB09Yh0vLpHRwpAV9S', 1, 0, 'PMgcxRNNBA4WxaMhKDQvBg4F16Xjb1MdsJZjFb1fMF32rFHW4bC94QLPZc7K', '2025-01-28 08:55:31', '2025-02-07 14:03:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Main', 'adminmain@domain.bj', 'Admin', NULL, '$2y$12$ixt2ggCqglfKEtcrYdDS9Ovp4RILfeACC9Wvqhguro0PNVTddCcse', 1, 0, NULL, '2025-01-28 08:55:31', '2025-02-07 14:03:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Teacher', 'teacherblack@gmail.com', 'Black', NULL, '$2y$12$Z9bxbrdNvMrHtSPHaTO3s.jcz4l2bJq/2B.3y7Kpgd/sUplOJ7wnK', 2, 0, NULL, '2025-01-28 09:13:13', '2025-02-07 16:03:37', 'adresse', NULL, '2013-02-06', NULL, NULL, NULL, '2000-05-21', 'male', NULL, '0190919096', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'Marié', 'qualification', 'permanent', 'note', 'experience'),
(4, 'Teacher1', 'teacherblack1@gmail.com', 'Black1', NULL, '$2y$12$BmHhF7B.mIgzqfuOxwLPruNrisRbrgSLbhMoYQIX/Yrv1StOBB4gO', 2, 0, 'TiXEc1LLGvN1q7b1kmEhRdIFN4gZGQX4zrhrYFxnwA6ejEkmpMYrYVzaibXT', '2025-01-28 09:14:54', '2025-02-07 08:25:27', 'adresse 1', NULL, NULL, NULL, NULL, NULL, '1995-02-08', 'female', NULL, '0102030405', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Célibataire', 'qualification 1', 'permanent 1', 'note 1', 'experience 1'),
(5, 'Teacher2', 'teacherblack2@gmail.com', 'Black2', NULL, '$2y$12$T2OojBm60mlR5bMfNwKzJO1yFPk/vUO1tHQnRWPTC4V5QcBHD/Fki', 2, 0, NULL, '2025-01-28 09:17:45', '2025-02-07 16:14:05', 'adresse 2', NULL, '2010-02-24', NULL, NULL, NULL, '1997-12-16', 'male', NULL, '0195041400', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'Marié', 'qualification 2', 'permanent 2', 'note 2', 'experience 2'),
(6, 'Teacher3', 'teacherblack3@gmail.com', 'Black3', NULL, '$2y$12$YcA5l2FyMPeEsn6YcUsUhOodwiYYRbSdMnE07G4Cb9vuLFyHTyyl.', 2, 0, NULL, '2025-01-28 09:19:46', '2025-02-07 16:13:31', 'adresse 3', NULL, '2013-02-12', NULL, NULL, NULL, '1996-02-20', 'female', NULL, '0105080906', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Célibataire', 'qualification 3', 'permanent 3', 'note 3', 'experience 3'),
(7, 'Student', 'studentblack@gmail.com', 'Black', NULL, '$2y$12$2xy5owvjJeZboJqO/ApALudYcAHMUil7qlijlYs915g.q9.XLS7m2', 3, 0, 'A4Gq9P12qfL9BxeiBCGLlfBdOlMzzQBEu7cWuwdKWRR0xRT9IzdzmWYtBUtL', '2025-01-28 09:46:08', '2025-02-08 12:08:40', NULL, 'BJSMS00000', '2025-01-23', 'a+', 'Caste', 11, '2006-03-03', 'male', '1.72', '0123455678', NULL, 12, NULL, 'Chrétien', '3', 1, '63', NULL, NULL, NULL, NULL, NULL),
(8, 'Student1', 'studentblack1@gmail.com', 'Black1', NULL, '$2y$12$aP3ec.Ohm/9uZSn.XHJ7zuvHfIIdDutCRLMsuAlvn1ztfpdiLVkiq', 3, 0, NULL, '2025-01-28 09:49:26', '2025-02-10 08:53:53', NULL, 'BJSMS00001', '0004-06-03', 'b+', 'Caste 1', 13, '1999-02-05', 'female', '1.80', '0190919006', NULL, 13, NULL, 'Catholique', '3', 1, '65', NULL, NULL, NULL, NULL, NULL),
(9, 'Student2', 'studentblack2@gmail.com', 'Black2', NULL, '$2y$12$McxzsuMo2UADo5sEj3dRauqntLb4/u4eSG.lZDeZ5BbdYm6pzR/L2', 3, 0, NULL, '2025-01-28 09:51:10', '2025-04-26 10:31:43', NULL, 'BJSMS00002', '2023-06-25', 'a-', 'Caste 2', 10, '2000-04-05', 'male', '1.76', '0168980552', NULL, 14, NULL, 'Boudhiste', '3', 1, '85', NULL, NULL, NULL, NULL, NULL),
(10, 'Student3', 'studentblack3@gmail.com', 'Black3', NULL, '$2y$12$301ZZGllDXdGw51wHObaEOphMtdKSrByeowk2aY1fT9YJH8m7Tc7y', 3, 0, NULL, '2025-01-28 09:54:34', '2025-05-24 12:17:48', NULL, 'BJSMS00003', '2023-08-26', 'a+', 'Caste 3', 5, '2009-05-24', 'female', '1.96', '0195200400', NULL, 15, NULL, 'Chrétien', '3', 1, '58', NULL, NULL, NULL, NULL, NULL),
(11, 'Student4', 'studentblack4@gmail.com', 'Black4', NULL, '$2y$12$Pg2/evePPpJ7T3KUGsfb6.KZl.DzOssAwLgiRIxdUI6cHDWs.CS/O', 3, 0, NULL, '2025-01-28 09:57:13', '2025-02-12 16:21:52', NULL, 'BJSMS00004', '2021-06-23', 'ab+', 'Caste 4', 8, '1998-04-26', 'male', '1.65', '0105080906', NULL, 16, NULL, 'Céleste', '3', 1, '75', NULL, NULL, NULL, NULL, NULL),
(12, 'Parent', 'parentblack@gmail.com', 'Black', NULL, '$2y$12$0cBL9a4JziZiDWhysYRKh.irIwhyum/VGfxn8IEb89B01XfNWkmsW', 4, 0, NULL, '2025-01-28 09:58:30', '2025-01-28 09:58:30', 'Abomey-Calavi', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '0190009096', 'Professeur', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Parent1', 'parentblack1@gmail.com', 'Black1', NULL, '$2y$12$HVJ0sJZ9eNOi6PGCCFBt..DEo8fg.HA8P2kFUR/kQdjD5JHGIQHu6', 4, 0, NULL, '2025-01-28 09:59:32', '2025-02-12 15:04:01', 'Abomey-Calavi', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '0102030405', 'Menusier', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Parent2', 'parentblack2@gmail.com', 'Black2', NULL, '$2y$12$afvZEUB757KqNdHIoQcQpujKN1vwDT86om6cUlr.aJXmZVO3nJ61W', 4, 0, NULL, '2025-01-28 10:00:35', '2025-01-28 10:00:35', 'Dèkoungbé', NULL, NULL, NULL, NULL, NULL, NULL, 'male', NULL, '0190001400', 'Developpeur', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Parent3', 'parentblack3@gmail.com', 'Black3', NULL, '$2y$12$Gex7uHD3.hviW4tBpyZ41.yE/pd4kcebgi4nIQ2ZgzqvGcsjI35ze', 4, 0, NULL, '2025-01-28 10:01:23', '2025-01-28 10:01:23', 'Cotonou', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '010200305', 'Docteur', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'Parent4', 'parentblack4@gmail.com', 'Black4', NULL, '$2y$12$aKJ3lpK3QLSm.S3nuXfWMeTjj7oCYMhvGYiJfCPyukGMc4JZ6ggSS', 4, 0, NULL, '2025-01-28 10:02:28', '2025-02-12 16:21:30', 'Ganhi', NULL, NULL, NULL, NULL, NULL, NULL, 'female', NULL, '0102555405', 'Mécanicien', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'ATTOLOU', 'rattolou@vippinterstis.com', 'Régis', NULL, '$2y$12$P5kScQg0yPp7Z00xUuXhaO1o5uyV1mbz7EIRQYN/bk.taJJrLyQWe', 1, 0, 'M5yFwQHrk5o7W0cxowRxAjXt3Iauy8V8ba9YHt4tvPIYkSP64mKUqrs0vDnK', '2025-02-06 14:54:32', '2025-06-24 14:47:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Teacher4', 'teacherblack4@gmail.com', 'Black4', NULL, '$2y$12$xiOp92XxWJkFXAIeUMBdo.h5psxIXA0Ej0S3wKEaQ/7XEr7YgeZAK', 2, 0, NULL, '2025-02-07 16:07:56', '2025-02-07 16:12:21', 'Adresse actuelle 4', NULL, '2014-02-07', NULL, NULL, NULL, '1988-02-02', 'male', NULL, '0140520635', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'Divorcé', 'Qualification 4', 'Adresse permanent 4', 'Note 4', 'Expérience 4'),
(19, 'Student5', 'studentblack5@gmail.com', 'Black5', NULL, '$2y$12$0H0y1UnCX2Bn4JFvECoCg.1ZqJX/UcJz7/2i4JBLlGovEoqtU3w72', 3, 0, NULL, '2025-02-20 16:54:13', '2025-04-26 10:32:08', NULL, 'BJSMS00005', '2025-02-10', 'o+', 'caste 5', 8, '1996-02-20', 'female', '1.98', '010508809', NULL, 15, NULL, 'Thron', '3', 1, '95', NULL, NULL, NULL, NULL, NULL),
(20, 'ATTOLOU', 'ferencattolou21@gmail.com', 'Ferenc', NULL, '$2y$12$kEsOwgeMrPxOui7iWQUo3eKCkXGRXBjLR3k8qGwYwSEAlK9tJXKey', 1, 0, NULL, '2025-06-23 13:34:18', '2025-06-25 09:02:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'AGBANNONDE', 'magbanonde@vippinterstis.com', 'Warris', NULL, '$2y$12$BVjQBmO4IBcSJPZ0Zrjh1uQ1.rz8Ryc/uyiLQ1fA5g6XyFVPGCn6.', 1, 0, NULL, '2025-06-25 09:01:47', '2025-06-25 12:13:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `week`
--

CREATE TABLE `week` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `document_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `works`
--

INSERT INTO `works` (`id`, `class_id`, `subject_id`, `work_date`, `submission_date`, `document_file`, `description`, `created_by`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 13, 2, '2025-06-26', '2025-07-03', 'homework26062025030326apfyopmeyjffilny31st.pdf', '<p>Le nom du fichier s\'affiche en dessous du texte \"PDF, DOCX, PPTX...\"</p>', 17, 0, '2025-06-26 13:03:26', '2025-06-26 13:03:26'),
(2, 14, 3, '2025-06-26', '2025-07-04', 'homework27062025031218ukobcoorewx2uvrwxuo0.pdf', '<p>Souhaite-tu que je t’aide aussi à afficher dynamiquement le type ou le statut de la matière dans un badge à côté du nom ? Ça pourrait enrichir visuellement la liste déroulante.</p>', 17, 0, '2025-06-27 13:12:18', '2025-06-27 13:12:18'),
(3, 13, 4, '2025-06-27', '2025-07-04', 'homework27062025032301vohcwdbbp34nzjkl1il5.pdf', '<p>Merci pour le code, Régis. Ça m’éclaire beaucoup mieux sur la structure.</p>', 17, 0, '2025-06-27 13:23:01', '2025-06-27 13:23:01'),
(4, 8, 5, '2025-06-27', '2025-07-01', 'homework27062025032928jf1p3abytxbyqbc7yk3c.pdf', '<p>Merci pour le code, Régis. Photo de Régis...<br></p>', 17, 0, '2025-06-27 13:29:28', '2025-06-27 13:29:28'),
(5, 8, 7, '2025-06-28', '2025-07-02', 'homework27062025033012mp1athnyqkipffemnndr.pdf', '<p>Souhaite-tu que je t’aide aussi à afficher dynamiquement le type ou le statut de la matière dans un badge à côté du nom ? <br></p>', 17, 0, '2025-06-27 13:30:12', '2025-06-27 13:30:12'),
(6, 3, 6, '2025-06-29', '2025-07-05', 'homework27062025033046oo6cepbsb6omelbewqeh.pptx', '<p>Ça pourrait enrichir visuellement la liste déroulante.</p>', 17, 0, '2025-06-27 13:30:46', '2025-06-27 13:30:46'),
(7, 8, 4, '2025-06-30', '2025-07-07', 'homework30062025091350ra4zgxflwkfjmtxay05v.xlsx', '<p>Création d\'un nouveau travail de maison pour la classe de 5ième avec la matière Physique Chimie et Technologie. J\'ai ajouté un fichier excel pour la modification.<br></p>', 17, 0, '2025-06-30 06:59:54', '2025-06-30 07:13:50'),
(8, 11, 1, '2025-06-30', '2025-07-08', '', '<p>Première création d\'un travail de maison dans la session d\'un professeur.<br></p>', 3, 0, '2025-06-30 09:18:25', '2025-06-30 09:25:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `class_subject`
--
ALTER TABLE `class_subject`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `class_teacher`
--
ALTER TABLE `class_teacher`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `class_timetable`
--
ALTER TABLE `class_timetable`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `communicates`
--
ALTER TABLE `communicates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `marks_grade`
--
ALTER TABLE `marks_grade`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `marks_register`
--
ALTER TABLE `marks_register`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `noticeboard_messages`
--
ALTER TABLE `noticeboard_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `subject`
--
ALTER TABLE `subject`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `week`
--
ALTER TABLE `week`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `works`
--
ALTER TABLE `works`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
