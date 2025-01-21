CREATE TABLE
	`mythicalclient_tickets_messages` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`ticket` INT (16) NOT NULL,
        `user` varchar(36) NOT NULL,
		`message` TEXT NOT NULL,
		`deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		FOREIGN KEY (`ticket`) REFERENCES `mythicalclient_tickets`(`id`),
		FOREIGN KEY (`user`) REFERENCES `mythicalclient_users`(`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;