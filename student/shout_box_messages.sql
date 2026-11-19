-- Run once in the MySQL database used by ../src/connection.php.
CREATE TABLE IF NOT EXISTS `shout_box_messages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` VARCHAR(100) NOT NULL,
    `sender_name` VARCHAR(255) NOT NULL,
    `sender_initials` VARCHAR(10) NOT NULL DEFAULT 'ST',
    `message` VARCHAR(500) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_shout_box_created` (`is_deleted`, `id`),
    KEY `idx_shout_box_student` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
