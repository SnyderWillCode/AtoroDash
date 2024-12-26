CREATE TABLE
    `mythicalclient_services_price` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `service` INT NOT NULL,
        `type` INT NOT NULL,
        `monthly` TEXT NULL,
        `quarterly` TEXT NULL,
        `semi_annually` TEXT NULL,
        `annually` TEXT NULL,
        `biennially` TEXT NULL,
        `triennially` TEXT NULL,
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`type`) REFERENCES `mythicalclient_services_price_types` (`id`),
        FOREIGN KEY (`service`) REFERENCES `mythicalclient_services` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
