SET
    foreign_key_checks = 0;

CREATE TABLE IF NOT EXISTS
    `mythicalclient_users` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `username` text NOT NULL,
        `first_name` text NOT NULL,
        `last_name` text NOT NULL,
        `email` text NOT NULL,
        `password` text NOT NULL,
        `avatar` text DEFAULT 'https://www.gravatar.com/avatar',
        `background` text NOT NULL DEFAULT 'https://cdn.mythicalsystems.xyz/background.gif',
        `uuid` varchar(36) NOT NULL UNIQUE,
        `token` text NOT NULL,
        `role` int (11) NOT NULL DEFAULT 1,
        `first_ip` text NOT NULL,
        `last_ip` text NOT NULL,
        `banned` text DEFAULT 'NO',
        `verified` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `2fa_enabled` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `2fa_key` text DEFAULT NULL,
        `2fa_blocked` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `deleted` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `last_seen` datetime NOT NULL DEFAULT current_timestamp(),
        `first_seen` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`role`) REFERENCES `mythicalclient_roles` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS
    `mythicalclient_roles` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `real_name` text NOT NULL,
        `date` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

INSERT INTO
    `mythicalclient_roles` (`name`, `real_name`)
VALUES
    ('Default', 'default'),
    ('VIP', 'vip'),
    ('Support Buddy', 'supportbuddy'),
    ('Support', 'support'),
    ('Support LVL 3','supportlvl3'),
    ('Support LVL 4','supportlvl4'),
    ('Admin', 'admin'),
    ('Administrator','administrator');

CREATE TABLE IF NOT EXISTS
    `mythicalclient_users_mails` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `subject` text NOT NULL,
        `body` longtext NOT NULL,
        `from` text NOT NULL DEFAULT 'app@mythicalsystems.xyz',
        `user` varchar(36) NOT NULL,
        `read` int (11) NOT NULL DEFAULT 0,
        `deleted` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `date` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS
    `mythicalclient_users_activities` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `user` varchar(36) NOT NULL,
        `action` text NOT NULL,
        `ip_address` text NOT NULL,
        `deleted` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `date` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS
    `mythicalclient_users_apikeys` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `name` text NOT NULL,
        `user` varchar(36) NOT NULL,
        `type` enum ('r', 'rw') NOT NULL DEFAULT 'r',
        `value` text NOT NULL,
        `deleted` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `date` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS
    `mythicalclient_users_email_verification` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `code` text NOT NULL,
        `user` varchar(36) NOT NULL,
        `type` enum ('password', 'verify') NOT NULL DEFAULT 'verify',
        `date` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci AUTO_INCREMENT = 2;

CREATE TABLE IF NOT EXISTS
    `mythicalclient_users_notifications` (
        `id` int (11) NOT NULL AUTO_INCREMENT,
        `user` varchar(36) NOT NULL,
        `name` text NOT NULL,
        `description` text NOT NULL,
        `deleted` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `locked` enum ('false', 'true') NOT NULL DEFAULT 'false',
        `date` datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        FOREIGN KEY (`user`) REFERENCES `mythicalclient_users` (`uuid`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

SET
    foreign_key_checks = 1;