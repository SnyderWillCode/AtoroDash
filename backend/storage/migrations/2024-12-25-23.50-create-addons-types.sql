CREATE TABLE IF NOT EXISTS
    `mythicalclient_addons_types` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` TEXT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'true',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO
    `mythicalclient_addons_types` (`name`)
VALUES
    ('event'),
    ('gateway'),
    ('provider'),
    ('components');