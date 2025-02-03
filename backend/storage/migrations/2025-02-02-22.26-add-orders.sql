CREATE TABLE
	`mythicalclient_orders` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`user` VARCHAR(36) NOT NULL,
		`service` INT (16) NOT NULL,
		`provider` INT (16) NOT NULL,
		`status` ENUM ('processed', 'processing', 'failed') NOT NULL DEFAULT 'processing',
		`deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		FOREIGN KEY (`user`) REFERENCES `mythicalclient_users`(`uuid`),
		FOREIGN KEY (`service`) REFERENCES `mythicalclient_services`(`id`),
		FOREIGN KEY (`provider`) REFERENCES `mythicalclient_addons`(`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;