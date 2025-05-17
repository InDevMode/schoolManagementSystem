-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: schoolManagementSystem
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `attendances`
--

DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `student_id` int unsigned DEFAULT NULL,
  `attendance_type` tinyint DEFAULT NULL COMMENT '1: Present, 2: Late, 2:Absent, 4:half_day',
  `created_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendances`
--

LOCK TABLES `attendances` WRITE;
/*!40000 ALTER TABLE `attendances` DISABLE KEYS */;
INSERT INTO `attendances` VALUES (1,13,'2025-02-28',8,4,17,'2025-05-08 10:32:10','2025-05-08 11:20:34'),(2,8,'2025-02-28',19,4,17,'2025-05-08 11:20:48','2025-05-10 12:51:23'),(3,8,'2025-02-28',11,2,17,'2025-05-08 11:20:49','2025-05-08 12:43:08'),(4,11,'2025-02-28',7,1,17,'2025-05-08 11:21:22','2025-05-08 11:21:22'),(5,8,'2025-03-01',19,3,17,'2025-05-08 13:23:50','2025-05-08 13:23:50'),(6,8,'2025-03-01',11,1,3,'2025-05-10 12:55:22','2025-05-10 12:55:22'),(7,13,'2025-03-01',8,4,3,'2025-05-10 12:55:51','2025-05-10 12:55:51');
/*!40000 ALTER TABLE `attendances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `is_delete` tinyint NOT NULL DEFAULT '0',
  `created_by` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class`
--

LOCK TABLES `class` WRITE;
/*!40000 ALTER TABLE `class` DISABLE KEYS */;
INSERT INTO `class` VALUES (1,'CI',1,0,1,'2025-01-28 09:20:59','2025-01-28 09:20:59'),(2,'CP',1,0,1,'2025-01-28 09:21:07','2025-01-28 09:21:07'),(3,'CE1',1,0,1,'2025-01-28 09:21:20','2025-01-29 14:22:11'),(4,'CE2',1,0,1,'2025-01-28 09:21:38','2025-01-29 14:22:00'),(5,'CM1',1,0,1,'2025-01-28 09:21:57','2025-01-28 09:21:57'),(6,'CM2',0,0,1,'2025-01-28 09:22:07','2025-01-28 09:22:07'),(7,'6 ième',0,0,2,'2025-01-28 09:25:31','2025-01-28 09:25:31'),(8,'5 ième',1,0,2,'2025-01-28 09:25:38','2025-01-28 09:25:38'),(9,'4 ième',0,0,2,'2025-01-28 09:25:45','2025-01-28 09:25:45'),(10,'3 ième',1,0,2,'2025-01-28 09:25:51','2025-01-28 09:25:51'),(11,'2 nd A',1,0,2,'2025-01-28 09:28:24','2025-01-28 09:28:24'),(12,'2 nd B',0,0,2,'2025-01-28 09:28:33','2025-01-28 09:28:33'),(13,'2 nd C',1,0,2,'2025-01-28 09:28:43','2025-02-03 12:43:36'),(14,'2 nd D',1,0,2,'2025-01-28 09:28:50','2025-02-11 10:07:22');
/*!40000 ALTER TABLE `class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_subject`
--

DROP TABLE IF EXISTS `class_subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_subject` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int unsigned NOT NULL,
  `subject_id` int unsigned NOT NULL,
  `created_by` int unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_subject`
--

LOCK TABLES `class_subject` WRITE;
/*!40000 ALTER TABLE `class_subject` DISABLE KEYS */;
INSERT INTO `class_subject` VALUES (1,4,0,2,1,0,'2025-01-28 12:01:26','2025-02-11 10:18:28'),(2,14,3,2,1,0,'2025-01-28 12:01:26','2025-01-28 12:01:26'),(3,14,2,2,1,0,'2025-01-28 12:01:26','2025-01-28 12:01:26'),(4,1,0,2,1,0,'2025-01-28 12:01:26','2025-02-11 10:18:12'),(5,2,0,2,1,0,'2025-01-28 12:02:58','2025-02-11 10:06:56'),(9,1,10,1,1,0,'2025-01-29 08:31:43','2025-01-29 08:31:43'),(11,3,8,2,1,0,'2025-01-29 14:24:53','2025-01-29 14:24:53'),(12,3,6,2,1,0,'2025-01-29 14:24:53','2025-01-29 14:24:53'),(15,2,9,17,0,0,'2025-02-08 13:42:42','2025-02-08 13:42:42'),(20,13,5,17,1,0,'2025-02-11 19:42:08','2025-02-11 19:42:08'),(21,13,4,17,1,0,'2025-02-11 19:42:08','2025-02-11 19:42:08'),(22,13,3,17,1,0,'2025-02-11 19:42:08','2025-02-11 19:42:08'),(23,13,2,17,1,0,'2025-02-11 19:42:08','2025-02-11 19:42:08'),(25,5,8,17,0,0,'2025-02-13 09:31:20','2025-03-08 12:19:18'),(26,5,6,17,1,0,'2025-02-13 09:31:20','2025-02-13 09:31:20'),(27,11,0,17,0,0,'2025-02-13 09:36:38','2025-03-08 11:40:25'),(28,7,0,17,0,0,'2025-02-13 09:36:38','2025-03-08 11:36:26'),(29,5,0,17,0,0,'2025-02-13 09:36:38','2025-03-08 11:58:38'),(30,4,0,17,0,0,'2025-02-13 09:36:38','2025-03-08 11:36:35'),(31,3,0,17,0,0,'2025-02-13 09:36:38','2025-03-08 11:36:17'),(32,2,0,17,0,0,'2025-02-13 09:36:38','2025-03-08 11:35:59'),(37,11,5,17,0,0,'2025-03-08 11:35:44','2025-03-08 11:35:44'),(38,11,2,17,0,0,'2025-03-08 11:35:44','2025-03-08 11:35:44'),(39,11,1,17,0,0,'2025-03-08 11:35:44','2025-03-08 11:35:44'),(40,8,5,17,1,0,'2025-03-08 12:31:31','2025-03-08 12:31:31'),(41,8,4,17,1,0,'2025-03-08 12:31:31','2025-03-08 12:31:31'),(42,8,3,17,1,0,'2025-03-08 12:31:31','2025-03-08 12:31:31'),(43,8,2,17,1,0,'2025-03-08 12:31:31','2025-03-08 12:31:31'),(44,8,7,17,1,0,'2025-03-08 12:31:31','2025-03-08 12:31:31');
/*!40000 ALTER TABLE `class_subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_teacher`
--

DROP TABLE IF EXISTS `class_teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_teacher` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int unsigned DEFAULT NULL,
  `teacher_id` int unsigned DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_teacher`
--

LOCK TABLES `class_teacher` WRITE;
/*!40000 ALTER TABLE `class_teacher` DISABLE KEYS */;
INSERT INTO `class_teacher` VALUES (9,4,5,2,1,0,'2025-01-29 14:22:43','2025-01-29 14:22:43'),(10,4,4,2,1,0,'2025-01-29 14:22:43','2025-01-29 14:22:43'),(11,10,6,2,1,0,'2025-01-29 14:23:18','2025-01-29 14:23:18'),(12,10,5,2,1,0,'2025-01-29 14:23:18','2025-01-29 14:23:18'),(23,13,3,1,1,0,'2025-01-30 09:35:36','2025-01-30 09:35:36'),(27,8,4,17,1,0,'2025-02-13 09:12:21','2025-02-13 09:12:21'),(28,8,3,17,1,0,'2025-02-13 09:12:21','2025-02-13 09:12:21'),(29,8,18,17,1,0,'2025-02-13 09:12:21','2025-02-13 09:12:21'),(35,11,18,17,0,0,'2025-03-08 11:18:24','2025-03-08 11:18:24'),(36,11,6,17,0,0,'2025-03-08 11:18:24','2025-03-08 11:18:24'),(37,11,5,17,0,0,'2025-03-08 11:18:24','2025-03-08 11:18:24'),(38,11,4,17,0,0,'2025-03-08 11:18:24','2025-03-08 11:18:24'),(39,11,3,17,1,0,'2025-03-08 11:18:24','2025-05-10 14:35:45');
/*!40000 ALTER TABLE `class_teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `class_timetable`
--

DROP TABLE IF EXISTS `class_timetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class_timetable` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int unsigned DEFAULT NULL,
  `subject_id` int unsigned DEFAULT NULL,
  `week_id` int unsigned DEFAULT NULL,
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `class_timetable`
--

LOCK TABLES `class_timetable` WRITE;
/*!40000 ALTER TABLE `class_timetable` DISABLE KEYS */;
INSERT INTO `class_timetable` VALUES (1,13,3,1,'08:00','12:00','0001','2025-02-01 11:50:39','2025-02-01 11:50:39'),(2,13,3,2,'14:00','18:00','0002','2025-02-01 11:50:39','2025-02-01 11:50:39'),(3,13,3,3,'10:00','13:00','0003','2025-02-01 11:50:39','2025-02-01 11:50:39'),(4,13,3,4,'09:00','11:00','0004','2025-02-01 11:50:39','2025-02-01 11:50:39'),(5,13,3,5,'10:00','14:00','0005','2025-02-01 11:50:39','2025-02-01 11:50:39'),(6,13,3,6,'15:00','18:00','0006','2025-02-01 11:50:39','2025-02-01 11:50:39'),(7,13,3,7,'16:00','18:00','0007','2025-02-01 11:50:39','2025-02-01 11:50:39'),(8,14,2,1,'08:30','10:30','B1','2025-02-01 11:53:52','2025-02-01 11:53:52'),(9,14,2,2,'12:30','12:30','B2','2025-02-01 11:53:52','2025-02-01 11:53:52'),(10,14,2,3,'14:30','16:30','B3','2025-02-01 11:53:52','2025-02-01 11:53:52'),(11,14,2,4,'16:30','18:30','B4','2025-02-01 11:53:52','2025-02-01 11:53:52'),(12,14,2,5,'09:00','12:00','B5','2025-02-01 11:53:52','2025-02-01 11:53:52'),(13,14,2,6,'14:00','17:00','B6','2025-02-01 11:53:52','2025-02-01 11:53:52'),(14,14,2,7,'10:30','12:30','b7','2025-02-01 11:53:52','2025-02-01 11:53:52'),(15,11,1,1,'15:00','18:00','B1','2025-02-01 11:59:12','2025-02-01 11:59:12'),(16,3,6,1,'08:00','09:00','Salle 1','2025-02-01 12:21:39','2025-02-01 12:21:39');
/*!40000 ALTER TABLE `class_timetable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exams`
--

DROP TABLE IF EXISTS `exams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exams`
--

LOCK TABLES `exams` WRITE;
/*!40000 ALTER TABLE `exams` DISABLE KEYS */;
INSERT INTO `exams` VALUES (1,'Evaluation 1','Note 1',17,0,'2025-02-10 15:28:57','2025-02-10 15:28:57'),(2,'Evaluation 2','Note 2',17,0,'2025-02-10 15:32:42','2025-02-10 15:32:42'),(3,'Evaluation 3','Note 3',17,0,'2025-02-10 15:32:58','2025-02-10 15:32:58'),(4,'Evaluation 4','Note 4',17,0,'2025-02-10 15:33:15','2025-02-10 15:33:15'),(5,'Evaluation 5','Note 5',17,0,'2025-02-10 15:33:34','2025-02-10 15:33:34'),(6,'Evaluation 6','Note 6',17,0,'2025-02-10 15:34:15','2025-02-10 15:34:15'),(7,'Evaluation 7','Note 7',17,0,'2025-02-10 15:34:31','2025-02-10 15:34:31'),(8,'Evaluation 8','Note 8',17,0,'2025-02-10 15:34:50','2025-02-10 15:34:50'),(9,'Evaluation 9','Note 9',17,0,'2025-02-10 15:35:07','2025-02-10 15:35:07'),(10,'Evaluation 10','Note 10',17,0,'2025-02-10 15:35:29','2025-02-10 15:35:29'),(11,'Evaluation 11','Note 11',17,0,'2025-02-10 15:35:44','2025-02-10 15:35:44'),(12,'Evaluation 12','Note 12',17,0,'2025-02-10 15:35:57','2025-02-10 15:35:57');
/*!40000 ALTER TABLE `exams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks_grade`
--

DROP TABLE IF EXISTS `marks_grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marks_grade` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent_from` int NOT NULL,
  `percent_to` int NOT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks_grade`
--

LOCK TABLES `marks_grade` WRITE;
/*!40000 ALTER TABLE `marks_grade` DISABLE KEYS */;
INSERT INTO `marks_grade` VALUES (1,'Grade B',10,40,17,0,'2025-04-29 08:21:19','2025-04-29 14:45:23'),(2,'Grade A',10,30,17,0,'2025-04-29 08:43:30','2025-04-29 14:45:12'),(3,'Grade C',30,60,17,0,'2025-04-29 14:45:45','2025-04-29 14:45:45'),(4,'Grade D',60,90,17,0,'2025-04-29 14:46:00','2025-04-29 14:46:00'),(5,'Grade E',40,60,17,0,'2025-04-29 14:46:23','2025-04-29 14:46:23'),(6,'Grade F',60,80,17,0,'2025-04-29 14:46:46','2025-04-29 14:46:46'),(7,'Grade G',80,100,17,0,'2025-04-29 14:47:02','2025-04-29 14:47:02');
/*!40000 ALTER TABLE `marks_grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `marks_register`
--

DROP TABLE IF EXISTS `marks_register`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marks_register` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `student_id` int unsigned DEFAULT NULL,
  `exam_id` int unsigned DEFAULT NULL,
  `class_id` int unsigned DEFAULT NULL,
  `subject_id` int unsigned DEFAULT NULL,
  `class_work` int DEFAULT NULL,
  `home_work` int DEFAULT NULL,
  `exam_work` int DEFAULT NULL,
  `test_work` int DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `passing_marks` int DEFAULT NULL,
  `full_marks` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marks_register`
--

LOCK TABLES `marks_register` WRITE;
/*!40000 ALTER TABLE `marks_register` DISABLE KEYS */;
INSERT INTO `marks_register` VALUES (1,11,6,8,5,20,20,10,10,17,0,'2025-02-20 16:22:51','2025-04-26 08:11:46',70,80),(2,11,6,8,7,15,15,15,30,17,1,'2025-02-20 16:22:51','2025-03-24 14:15:44',80,100),(3,11,6,8,11,10,20,19,16,17,0,'2025-02-20 16:22:51','2025-04-19 09:09:44',50,100),(4,19,6,8,5,20,20,20,20,3,0,'2025-02-21 13:26:08','2025-03-25 15:47:50',70,80),(5,19,6,8,7,30,36,15,10,3,0,'2025-02-21 13:45:14','2025-03-25 15:47:50',80,100),(6,19,6,8,11,15,30,25,30,3,0,'2025-02-21 13:45:14','2025-03-25 15:47:50',50,100),(7,8,6,13,5,20,5,15,10,3,0,'2025-03-15 13:15:11','2025-04-19 09:08:22',80,60);
/*!40000 ALTER TABLE `marks_register` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_reset_tokens_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2024_12_30_132802_add_is_delete_to_users_table',1),(6,'2025_01_02_130148_create_class_table',1),(7,'2025_01_13_131349_create_subject_table',1),(8,'2025_01_15_093712_create_class_subject_table',1),(9,'2025_01_22_110457_add_fields_to_users_table',1),(10,'2025_01_23_165723_add_other_fields_to_users_table',1),(11,'2025_01_29_091903_create_class_teacher_table',2),(13,'2025_01_31_093946_create_week_table',3),(14,'2025_01_30_164442_create_class_timetable_table',4),(15,'2025_02_10_135321_create_exams_table',5),(16,'2025_02_11_091850_create_schedules_table',6),(17,'2025_02_13_201447_add_fields_to_week_table',7),(18,'2025_02_20_153926_create_marks_register_table',8),(19,'2025_03_24_105743_add_passing_marks_and_full_marks_to_register_table',9),(20,'2025_04_29_095259_create_marks_grade_table',10),(21,'2025_05_08_102641_create_attendances_table',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedules`
--

DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exam_id` int unsigned DEFAULT NULL,
  `class_id` int unsigned DEFAULT NULL,
  `subject_id` int unsigned DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `start_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_marks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passing_marks` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `is_delete` tinyint NOT NULL DEFAULT '0' COMMENT '0: isntDeleted, 1: Deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedules`
--

LOCK TABLES `schedules` WRITE;
/*!40000 ALTER TABLE `schedules` DISABLE KEYS */;
INSERT INTO `schedules` VALUES (7,11,11,1,'2025-02-14','08:45','13:00','G0012','100','50',17,0,'2025-02-11 18:00:20','2025-02-11 18:00:20'),(19,6,13,5,'2025-03-04','11:00','13:00','AB4','60','80',17,0,'2025-02-12 15:07:45','2025-02-12 15:07:45'),(20,9,13,3,'2025-02-19','09:00','11:30','BB2','70','80',17,0,'2025-02-12 15:08:31','2025-02-12 15:08:31'),(21,5,13,4,'2025-02-21','14:30','17:30','RDC4','100','60',17,0,'2025-02-12 15:09:36','2025-02-12 15:09:36'),(22,2,13,2,'2025-02-11','08:00','10:30','B0012','50','80',17,0,'2025-02-12 15:16:56','2025-02-12 15:16:56'),(23,6,8,11,'2025-02-26','08:00','10:00','AB1','100','50',17,1,'2025-02-12 16:16:48','2025-02-20 11:09:50'),(24,1,8,2,'2025-02-20','14:00','16:00','G0011','100','50',17,0,'2025-02-13 09:39:25','2025-02-13 09:39:25'),(25,6,8,7,'2025-02-14','09:00','11:00','T001','100','80',17,1,'2025-02-13 14:41:16','2025-02-20 11:09:50'),(26,6,8,11,'2025-02-26','08:00','10:00','AB1','100','50',17,1,'2025-02-13 14:41:16','2025-02-20 11:09:50'),(27,6,8,5,'2025-02-27','14:30','16:30','AB04','80','70',17,0,'2025-02-20 11:09:50','2025-02-20 11:09:50'),(28,6,8,7,'2025-02-14','09:00','11:00','T001','100','80',17,0,'2025-02-20 11:09:50','2025-02-20 11:09:50'),(29,6,8,11,'2025-02-26','08:00','10:00','AB1','100','50',17,0,'2025-02-20 11:09:50','2025-02-20 11:09:50');
/*!40000 ALTER TABLE `schedules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subject` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` int unsigned NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `is_delete` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES (1,'Communication Ecrite','theoretical',2,1,0,'2025-01-28 09:30:11','2025-01-28 09:30:11'),(2,'Lecture','theoretical',2,1,0,'2025-01-28 09:30:37','2025-01-29 14:21:37'),(3,'Science de la Vie et de Terre','practical',2,1,0,'2025-01-28 09:30:53','2025-01-28 09:30:53'),(4,'Physique Chimie et Technologie','practical',2,1,0,'2025-01-28 09:31:08','2025-01-29 14:21:29'),(5,'Histoire Géographie','theoretical',2,1,0,'2025-01-28 12:04:43','2025-01-28 12:04:43'),(6,'EST','theoretical',2,1,0,'2025-01-28 12:06:37','2025-01-28 12:06:37'),(7,'EPS','practical',2,1,0,'2025-01-28 12:06:47','2025-01-28 12:06:47'),(8,'ES','theoretical',2,1,0,'2025-01-28 12:06:55','2025-01-28 12:06:55'),(9,'Expression écrite','theoretical',2,1,0,'2025-01-28 12:07:56','2025-01-28 12:07:56'),(10,'Dessin','practical',2,1,0,'2025-01-28 12:08:08','2025-01-28 12:08:08'),(11,'Anglais','theoretical',17,1,0,'2025-02-08 11:48:59','2025-03-24 14:55:49');
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
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
  `class_id` int unsigned DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int unsigned DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roll_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0: Inactive, 1: Active',
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` text COLLATE utf8mb4_unicode_ci,
  `qualification` text COLLATE utf8mb4_unicode_ci,
  `permanent_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_experience` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Tech','admintech@domain.bj','Admin',NULL,'$2y$12$A4u5B4HLF3WJJsHVgmD1ae6H9VSAyp4xTyEXB09Yh0vLpHRwpAV9S',1,0,'PMgcxRNNBA4WxaMhKDQvBg4F16Xjb1MdsJZjFb1fMF32rFHW4bC94QLPZc7K','2025-01-28 08:55:31','2025-02-07 14:03:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(2,'Main','adminmain@domain.bj','Admin',NULL,'$2y$12$ixt2ggCqglfKEtcrYdDS9Ovp4RILfeACC9Wvqhguro0PNVTddCcse',1,0,NULL,'2025-01-28 08:55:31','2025-02-07 14:03:17',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL),(3,'Teacher','teacherblack@gmail.com','Black',NULL,'$2y$12$Z9bxbrdNvMrHtSPHaTO3s.jcz4l2bJq/2B.3y7Kpgd/sUplOJ7wnK',2,0,NULL,'2025-01-28 09:13:13','2025-02-07 16:03:37','adresse',NULL,'2013-02-06',NULL,NULL,NULL,'2000-05-21','male',NULL,'0190919096',NULL,NULL,NULL,NULL,NULL,1,NULL,'Marié','qualification','permanent','note','experience'),(4,'Teacher1','teacherblack1@gmail.com','Black1',NULL,'$2y$12$BmHhF7B.mIgzqfuOxwLPruNrisRbrgSLbhMoYQIX/Yrv1StOBB4gO',2,0,'TiXEc1LLGvN1q7b1kmEhRdIFN4gZGQX4zrhrYFxnwA6ejEkmpMYrYVzaibXT','2025-01-28 09:14:54','2025-02-07 08:25:27','adresse 1',NULL,NULL,NULL,NULL,NULL,'1995-02-08','female',NULL,'0102030405',NULL,NULL,NULL,NULL,NULL,0,NULL,'Célibataire','qualification 1','permanent 1','note 1','experience 1'),(5,'Teacher2','teacherblack2@gmail.com','Black2',NULL,'$2y$12$T2OojBm60mlR5bMfNwKzJO1yFPk/vUO1tHQnRWPTC4V5QcBHD/Fki',2,0,NULL,'2025-01-28 09:17:45','2025-02-07 16:14:05','adresse 2',NULL,'2010-02-24',NULL,NULL,NULL,'1997-12-16','male',NULL,'0195041400',NULL,NULL,NULL,NULL,NULL,1,NULL,'Marié','qualification 2','permanent 2','note 2','experience 2'),(6,'Teacher3','teacherblack3@gmail.com','Black3',NULL,'$2y$12$YcA5l2FyMPeEsn6YcUsUhOodwiYYRbSdMnE07G4Cb9vuLFyHTyyl.',2,0,NULL,'2025-01-28 09:19:46','2025-02-07 16:13:31','adresse 3',NULL,'2013-02-12',NULL,NULL,NULL,'1996-02-20','female',NULL,'0105080906',NULL,NULL,NULL,NULL,NULL,0,NULL,'Célibataire','qualification 3','permanent 3','note 3','experience 3'),(7,'Student','studentblack@gmail.com','Black',NULL,'$2y$12$2xy5owvjJeZboJqO/ApALudYcAHMUil7qlijlYs915g.q9.XLS7m2',3,0,'n3aAUbmgBulqMyRoxYmFDZfm9QYRcBrQOUASNO0ccM7XPNLfbq2CPV8pW5IZ','2025-01-28 09:46:08','2025-02-08 12:08:40',NULL,'BJSMS00000','2025-01-23','a+','Caste',11,'2006-03-03','male','1.72','0123455678',NULL,12,NULL,'Chrétien','3',1,'63',NULL,NULL,NULL,NULL,NULL),(8,'Student1','studentblack1@gmail.com','Black1',NULL,'$2y$12$aP3ec.Ohm/9uZSn.XHJ7zuvHfIIdDutCRLMsuAlvn1ztfpdiLVkiq',3,0,NULL,'2025-01-28 09:49:26','2025-02-10 08:53:53',NULL,'BJSMS00001','0004-06-03','b+','Caste 1',13,'1999-02-05','female','1.80','0190919006',NULL,13,NULL,'Catholique','3',1,'65',NULL,NULL,NULL,NULL,NULL),(9,'Student2','studentblack2@gmail.com','Black2',NULL,'$2y$12$McxzsuMo2UADo5sEj3dRauqntLb4/u4eSG.lZDeZ5BbdYm6pzR/L2',3,0,NULL,'2025-01-28 09:51:10','2025-04-26 10:31:43',NULL,'BJSMS00002','2023-06-25','a-','Caste 2',10,'2000-04-05','male','1.76','0168980552',NULL,14,NULL,'Boudhiste','3',1,'85',NULL,NULL,NULL,NULL,NULL),(10,'Student3','studentblack3@gmail.com','Black3',NULL,'$2y$12$301ZZGllDXdGw51wHObaEOphMtdKSrByeowk2aY1fT9YJH8m7Tc7y',3,0,NULL,'2025-01-28 09:54:34','2025-04-26 10:31:34',NULL,'BJSMS00003','2023-08-26','a+','Caste 3',5,'2009-05-24','female','1.96','0195200400',NULL,NULL,NULL,'Chrétien','3',1,'58',NULL,NULL,NULL,NULL,NULL),(11,'Student4','studentblack4@gmail.com','Black4',NULL,'$2y$12$Pg2/evePPpJ7T3KUGsfb6.KZl.DzOssAwLgiRIxdUI6cHDWs.CS/O',3,0,NULL,'2025-01-28 09:57:13','2025-02-12 16:21:52',NULL,'BJSMS00004','2021-06-23','ab+','Caste 4',8,'1998-04-26','male','1.65','0105080906',NULL,16,NULL,'Céleste','3',1,'75',NULL,NULL,NULL,NULL,NULL),(12,'Parent','parentblack@gmail.com','Black',NULL,'$2y$12$0cBL9a4JziZiDWhysYRKh.irIwhyum/VGfxn8IEb89B01XfNWkmsW',4,0,NULL,'2025-01-28 09:58:30','2025-01-28 09:58:30','Abomey-Calavi',NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,'0190009096','Professeur',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(13,'Parent1','parentblack1@gmail.com','Black1',NULL,'$2y$12$HVJ0sJZ9eNOi6PGCCFBt..DEo8fg.HA8P2kFUR/kQdjD5JHGIQHu6',4,0,NULL,'2025-01-28 09:59:32','2025-02-12 15:04:01','Abomey-Calavi',NULL,NULL,NULL,NULL,NULL,NULL,'female',NULL,'0102030405','Menusier',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(14,'Parent2','parentblack2@gmail.com','Black2',NULL,'$2y$12$afvZEUB757KqNdHIoQcQpujKN1vwDT86om6cUlr.aJXmZVO3nJ61W',4,0,NULL,'2025-01-28 10:00:35','2025-01-28 10:00:35','Dèkoungbé',NULL,NULL,NULL,NULL,NULL,NULL,'male',NULL,'0190001400','Developpeur',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(15,'Parent3','parentblack3@gmail.com','Black3',NULL,'$2y$12$Gex7uHD3.hviW4tBpyZ41.yE/pd4kcebgi4nIQ2ZgzqvGcsjI35ze',4,0,NULL,'2025-01-28 10:01:23','2025-01-28 10:01:23','Cotonou',NULL,NULL,NULL,NULL,NULL,NULL,'female',NULL,'010200305','Docteur',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(16,'Parent4','parentblack4@gmail.com','Black4',NULL,'$2y$12$aKJ3lpK3QLSm.S3nuXfWMeTjj7oCYMhvGYiJfCPyukGMc4JZ6ggSS',4,0,NULL,'2025-01-28 10:02:28','2025-02-12 16:21:30','Ganhi',NULL,NULL,NULL,NULL,NULL,NULL,'female',NULL,'0102555405','Mécanicien',NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(17,'ATTOLOU','rattolou@vippinterstis.com','Régis',NULL,'$2y$12$CGGy58i94kFx3ehGp3iLBe7pGs1WxyRG.KIEs0xkm4ZUWOTfa2CDu',1,0,NULL,'2025-02-06 14:54:32','2025-02-06 14:54:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL),(18,'Teacher4','teacherblack4@gmail.com','Black4',NULL,'$2y$12$xiOp92XxWJkFXAIeUMBdo.h5psxIXA0Ej0S3wKEaQ/7XEr7YgeZAK',2,0,NULL,'2025-02-07 16:07:56','2025-02-07 16:12:21','Adresse actuelle 4',NULL,'2014-02-07',NULL,NULL,NULL,'1988-02-02','male',NULL,'0140520635',NULL,NULL,NULL,NULL,NULL,0,NULL,'Divorcé','Qualification 4','Adresse permanent 4','Note 4','Expérience 4'),(19,'Student5','studentblack5@gmail.com','Black5',NULL,'$2y$12$0H0y1UnCX2Bn4JFvECoCg.1ZqJX/UcJz7/2i4JBLlGovEoqtU3w72',3,0,NULL,'2025-02-20 16:54:13','2025-04-26 10:32:08',NULL,'BJSMS00005','2025-02-10','o+','caste 5',8,'1996-02-20','female','1.98','010508809',NULL,15,NULL,'Thron','3',1,'95',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `week`
--

DROP TABLE IF EXISTS `week`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `week` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `day` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `week`
--

LOCK TABLES `week` WRITE;
/*!40000 ALTER TABLE `week` DISABLE KEYS */;
INSERT INTO `week` VALUES (1,'Lundi','2025-01-31 09:41:27','2025-01-31 09:41:29',1),(2,'Mardi','2025-01-31 09:41:35','2025-01-31 09:41:34',2),(3,'Mercredi','2025-01-31 09:41:53','2025-01-31 09:41:55',3),(4,'Jeudi','2025-01-31 09:42:06','2025-01-31 09:42:08',4),(5,'Vendredi','2025-01-31 09:42:17','2025-01-31 09:42:18',5),(6,'Samedi','2025-01-31 09:42:25','2025-01-31 09:42:26',6),(7,'Dimanche','2025-01-31 09:42:34','2025-01-31 09:42:35',0);
/*!40000 ALTER TABLE `week` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-17 13:12:31
