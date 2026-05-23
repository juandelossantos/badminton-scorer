-- Badminton Scorer - Production Database Setup
-- Run this in phpMyAdmin or mysql CLI on your hosting

CREATE DATABASE IF NOT EXISTS `huitacad_badmintonscore` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `huitacad_badmintonscore`;

CREATE TABLE IF NOT EXISTS `matches` (
  `id` VARCHAR(8) NOT NULL,
  `control_token` VARCHAR(32) NOT NULL,
  `mode` ENUM('singles','doubles') NOT NULL DEFAULT 'doubles',
  `player1_names` JSON NOT NULL,
  `player2_names` JSON NOT NULL,
  `sets_to_win` TINYINT NOT NULL DEFAULT 2,
  `points_per_set` TINYINT NOT NULL DEFAULT 21,
  `status` ENUM('active','completed','abandoned') NOT NULL DEFAULT 'active',
  `current_set` TINYINT NOT NULL DEFAULT 1,
  `server` TINYINT NOT NULL DEFAULT 1,
  `service_side` ENUM('left','right') NOT NULL DEFAULT 'right',
  `current_p1` TINYINT NOT NULL DEFAULT 0,
  `current_p2` TINYINT NOT NULL DEFAULT 0,
  `sets_data` JSON NOT NULL,
  `winner` TINYINT DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ended_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_updated` (`status`, `updated_at`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
