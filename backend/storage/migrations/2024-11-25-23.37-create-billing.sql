CREATE TABLE IF NOT EXISTS
    `mythicalclient_billing` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `user` varchar(36) NOT NULL,
        `company_name` TEXT NULL DEFAULT NULL,
        `vat_number` TEXT NULL DEFAULT NULL,
        `address1` TEXT NOT NULL,
        `address2` TEXT NULL DEFAULT NULL,
        `city` TEXT NOT NULL,
        `country` TEXT NOT NULL,
        `state` TEXT NOT NULL,
        `postcode` TEXT NOT NULL,
        `enabled` ENUM('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
