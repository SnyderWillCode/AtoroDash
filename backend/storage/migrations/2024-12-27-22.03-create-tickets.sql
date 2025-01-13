CREATE TABLE IF NOT EXISTS
    `mythicalclient_tickets` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `user` varchar(36) NOT NULL,
        `department` int(16) NOT NULL,
		`service` INT (16) NOT NULL,
		`title` TEXT NOT NULL,
		`description` TEXT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`),
		FOREIGN KEY (`service`) REFERENCES `mythicalclient_services` (`id`),
        FOREIGN KEY (`department`) REFERENCES `mythicalclient_departments` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;