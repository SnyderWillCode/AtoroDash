CREATE TABLE IF NOT EXISTS
    `mythicalclient_addons` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` TEXT NOT NULL,
        `type` INT NOT NULL,
        `display_name` TEXT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`type`) REFERENCES `mythicalclient_addons_types` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;