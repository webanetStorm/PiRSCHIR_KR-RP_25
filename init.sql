CREATE DATABASE IF NOT EXISTS `quelyd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `quelyd`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '',
    `role` ENUM('user', 'admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`email`, `password_hash`, `name`, `role`)
VALUES (
    'admin@quelyd.local',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Матвей Блантер',
    'admin'
)
ON DUPLICATE KEY UPDATE `role` = 'admin';
