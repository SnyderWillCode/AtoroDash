CREATE TABLE IF NOT EXISTS
`mythicalclient_invoice_templates` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `name` INT NOT NULL,
        `content` INT NOT NULL,
        `enabled` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` ENUM ('false', 'true') NOT NULL DEFAULT 'false',
        `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
