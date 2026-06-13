-- RCT App Database Schema & Seed Data
-- Used for GitHub Actions CI/CD workflows and local setup

CREATE DATABASE IF NOT EXISTS `rct_app`;
USE `rct_app`;

-- Table structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'patient',
  `created_at` datetime DEFAULT current_timestamp(),
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `language` varchar(10) DEFAULT 'en',
  `status` varchar(20) DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `procedures`
DROP TABLE IF EXISTS `procedures`;
CREATE TABLE `procedures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `patient_procedure`
DROP TABLE IF EXISTS `patient_procedure`;
CREATE TABLE `patient_procedure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `procedure_id` int(11) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `group_type` varchar(20) DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `attendance`
DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `apt1` varchar(10) DEFAULT 'absent',
  `apt2` varchar(10) DEFAULT 'absent',
  `apt3` varchar(10) DEFAULT 'absent',
  `apt4` varchar(10) DEFAULT 'absent',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `baseline_responses`
DROP TABLE IF EXISTS `baseline_responses`;
CREATE TABLE `baseline_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `appointment` varchar(20) DEFAULT NULL,
  `q1` varchar(100) DEFAULT NULL,
  `q2` varchar(100) DEFAULT NULL,
  `q3` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `consent`
DROP TABLE IF EXISTS `consent`;
CREATE TABLE `consent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `consent_given` varchar(10) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `consent_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `anxiety_scores`
DROP TABLE IF EXISTS `anxiety_scores`;
CREATE TABLE `anxiety_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `procedure_id` int(11) DEFAULT NULL,
  `timepoint` varchar(50) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `knowledge_scores`
DROP TABLE IF EXISTS `knowledge_scores`;
CREATE TABLE `knowledge_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `procedure_id` int(11) DEFAULT NULL,
  `timepoint` varchar(50) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `satisfaction_scores`
DROP TABLE IF EXISTS `satisfaction_scores`;
CREATE TABLE `satisfaction_scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `procedure_id` int(11) DEFAULT NULL,
  `timepoint` varchar(50) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `scores`
DROP TABLE IF EXISTS `scores`;
CREATE TABLE `scores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `quiz1` int(11) DEFAULT NULL,
  `quiz2` int(11) DEFAULT NULL,
  `quiz3` int(11) DEFAULT NULL,
  `followup_1week` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `password_resets`
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `appointment_adherence`
DROP TABLE IF EXISTS `appointment_adherence`;
CREATE TABLE `appointment_adherence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `appointment_no` int(11) DEFAULT NULL,
  `attended` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `postop_adherence`
DROP TABLE IF EXISTS `postop_adherence`;
CREATE TABLE `postop_adherence` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) DEFAULT NULL,
  `q1` varchar(100) DEFAULT NULL,
  `q2` varchar(100) DEFAULT NULL,
  `q3` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ----------------------------------------------------
-- Seed Data for `procedures`
-- ----------------------------------------------------
LOCK TABLES `procedures` WRITE;
INSERT INTO `procedures` VALUES 
(1,'Apexification','Endodontic','Endodontic procedure to treat immature teeth with open apices'),
(2,'Apexogenesis','Endodontic','Endodontic procedure to promote root development in vital immature teeth'),
(3,'Pulpotomy','Endodontic','Removal of the coronal portion of the dental pulp'),
(4,'Pulpectomy','Endodontic','Complete removal of the pulp tissue from the root canal'),
(5,'Root Canal Treatment - Single Canal','Endodontic','Root canal treatment for a tooth with one canal'),
(6,'Root Canal Treatment - Multi Canal','Endodontic','Root canal treatment for a tooth with multiple canals'),
(7,'Root Canal Retreatment','Endodontic','Repeat root canal treatment for a previously treated tooth'),
(8,'Periapical Surgery','Endodontic','Surgical removal of the root tip to treat persistent infection'),
(9,'Direct Pulp Capping','Endodontic','Placement of material directly over exposed pulp to promote healing'),
(10,'Indirect Pulp Capping','Endodontic','Placement of material over nearly exposed pulp to prevent exposure'),
(11,'Composite Restoration','Restorative','Tooth coloured filling material to restore decayed or damaged teeth'),
(12,'GIC Restoration','Restorative','Glass ionomer cement restoration for decayed teeth'),
(13,'Amalgam Restoration','Restorative','Silver alloy filling for posterior tooth restoration'),
(14,'Crown - PFM','Restorative','Porcelain fused to metal crown for full tooth coverage'),
(15,'Crown - Zirconia','Restorative','Full zirconia crown for strength and natural appearance'),
(16,'Inlay','Restorative','Custom restoration fitted within the tooth cusps'),
(17,'Onlay','Restorative','Custom restoration covering one or more tooth cusps');
UNLOCK TABLES;

-- ----------------------------------------------------
-- Seed Data for `users` (Default Admin Account)
-- ----------------------------------------------------
LOCK TABLES `users` WRITE;
INSERT INTO `users` (name, email, password, phone, role, status) VALUES 
('Admin', 'admin@rct.com', '$2y$10$EZHPtp7iWGX6U5uYy8sm4eArZTIlYQwv69mHe/IHrz2NKDcndlhx.', '0000000000', 'admin', 'active');
UNLOCK TABLES;
