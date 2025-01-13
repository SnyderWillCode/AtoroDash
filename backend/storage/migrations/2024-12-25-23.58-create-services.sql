CREATE TABLE IF NOT EXISTS
    `mythicalclient_services` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `category` INT NOT NULL,
        `name` TEXT NOT NULL,
        `tagline` TEXT NOT NULL,
        `quantity` INT NOT NULL,
        `stock` INT NOT NULL,
        `stock_enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `uri` TEXT NOT NULL,
        `shortdescription` TEXT NOT NULL,
        `description` TEXT NOT NULL,
        `setup_fee` INT NOT NULL,
        `provider` INT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`category`) REFERENCES `mythicalclient_services_categories` (`id`),
        FOREIGN KEY (`provider`) REFERENCES `mythicalclient_addons` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
