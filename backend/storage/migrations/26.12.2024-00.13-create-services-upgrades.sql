CREATE TABLE
    `mythicalclient_services_upgrades` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `product_id_old` INT NOT NULL,
        `product-id_new` INT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`product_id_old`) REFERENCES `mythicalclient_services` (`id`),
        FOREIGN KEY (`product-id_new`) REFERENCES `mythicalclient_services` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
