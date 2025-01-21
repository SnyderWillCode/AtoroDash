CREATE TABLE
    `mythicalclient_announcements_assets` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `announcements` int(16) NOT NULL,
        `images` TEXT NOT NULL,
		`deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`announcements`) REFERENCES `mythicalclient_announcements`(`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;