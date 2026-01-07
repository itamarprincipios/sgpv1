-- Database: sgp_system

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
CREATE TABLE `schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `inep_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`id`, `name`, `inep_code`) VALUES
(1, 'Escola Municipal Exemplo 1', '12345678'),
(2, 'Escola Municipal Exemplo 2', '87654321');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('semed','coordinator','professor') NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `is_physical_education` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `school_id` (`school_id`),
  KEY `class_id` (`class_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `school_id`, `name`, `email`, `password`, `role`, `whatsapp`) VALUES
(1, NULL, 'Admin SEMED', 'semed@sgp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'semed', '5511999999999'),
(2, 1, 'Coord. Escola 1', 'coord1@sgp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coordinator', '5511988888888'),
(3, 1, 'Prof. João', 'joao@sgp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professor', '5511977777777'),
(4, 2, 'Coord. Escola 2', 'coord2@sgp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coordinator', '5511966666666');

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Ex: Planejamento Março',
  `description` text DEFAULT NULL,
  `bimester` int(1) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` date DEFAULT NULL,
  `deadline` datetime NOT NULL,
  `opening_date` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_physical_education` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `period_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('planejamento','relatorio') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('pendente','enviado','atrasado','aprovado','rejeitado','ajustado') NOT NULL DEFAULT 'enviado',
  `feedback` text DEFAULT NULL,
  `score_base` decimal(5,2) DEFAULT 0.00,
  `penalty_delay` decimal(5,2) DEFAULT 0.00,
  `rejection_count` int(11) DEFAULT 0,
  `rejection_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `penalty_resubmission` decimal(5,2) DEFAULT 0.00,
  `score_final` decimal(5,2) DEFAULT 0.00,
  `submitted_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `period_id` (`period_id`),
  CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;

-- --------------------------------------------------------

--
-- Table structure for table 'user_medals'
--

DROP TABLE IF EXISTS `user_medals`;
CREATE TABLE `user_medals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `medal_type` varchar(50) NOT NULL,
  `period_type` varchar(20) NOT NULL,
  `reference_date` date NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  UNIQUE KEY `unique_medal` (`user_id`, `medal_type`, `reference_date`),
  CONSTRAINT `user_medals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
