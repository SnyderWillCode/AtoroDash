CREATE TABLE
    `mythicalclient_announcements` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `title` TEXT NOT NULL,
        `shortDescription` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;