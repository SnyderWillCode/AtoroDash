CREATE TABLE
	`mythicalclient_addons_settings` (
		`id` INT NOT NULL AUTO_INCREMENT,
		`identifier` TEXT NOT NULL,
		`key` TEXT NOT NULL,
		`value` TEXT NOT NULL,
		`locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
		`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;