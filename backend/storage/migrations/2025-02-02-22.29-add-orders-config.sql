CREATE TABLE
	`mythicalclient_orders_config` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`order` INT (16) NOT NULL,
		`kev` TEXT NOT NULL,
		`valuev` TEXT NOT NULL,
		`valuetype` ENUM (
			'text',
			'number',
			'password',
			'email',
			'select',
			'checkbox',
			'textarea'
		) NOT NULL,
		`locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`),
		FOREIGN KEY (`order`) REFERENCES `mythicalclient_orders` (`id`)
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;