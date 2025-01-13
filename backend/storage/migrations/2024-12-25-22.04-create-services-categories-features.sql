CREATE TABLE IF NOT EXISTS
    `mythicalclient_services_categories_features` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` TEXT NOT NULL , 
        `category` int(16) NOT NULL,
        `description` TEXT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`category`) REFERENCES `mythicalclient_services_categories` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;