CREATE TABLE
	`mythicalclient_paypal_payments` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`code` TEXT NOT NULL,
		`coins` INT NOT NULL,
        `user` varchar(36) NOT NULL,
		`status` ENUM ('processing', 'processed', 'failed') NOT NULL DEFAULT 'processing',
		`deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;