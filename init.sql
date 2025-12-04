CREATE DATABASE IF NOT EXISTS `quelyd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `quelyd`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `quests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `type` ENUM('individual', 'collective', 'timed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'individual',
  `reward` INT NOT NULL DEFAULT 20,
  `min_participants` INT NULL DEFAULT NULL,
  `deadline` DATETIME NULL DEFAULT NULL,
  `status` ENUM('draft', 'active', 'completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `is_approved` BOOLEAN NOT NULL DEFAULT FALSE,
  `created_at` INT NOT NULL,
  `updated_at` INT NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`email`, `password_hash`, `name`, `role`)
VALUES (
    'admin@quelyd.local',
    '$2y$12$/zGk6QWsdhBQfQ5y4m.5tunZV6QEUaBgjTANnZAaiu/6kCzjrVHma',
    'Матвей Блантер',
    'admin'
)
ON DUPLICATE KEY UPDATE `role` = 'admin';
