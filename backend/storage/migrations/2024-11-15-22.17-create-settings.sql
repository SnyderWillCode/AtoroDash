CREATE TABLE IF NOT EXISTS
    `mythicalclient_settings` (
        `id` INT NOT NULL AUTO_INCREMENT COMMENT 'The id of the setting!',
        `name` TEXT NOT NULL COMMENT 'The name of the setting',
        `value` TEXT NOT NULL COMMENT 'The value of the setting!',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The date of the last modifed!',
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT = 'The settings table where we store the settings of the dash!';