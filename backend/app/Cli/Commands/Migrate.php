<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * ### Preamble
 * This license governs the use, modification, and distribution of the software known as MythicalClient or MythicalDash ("the Software"). By using the Software, you agree to the terms outlined in this document. These terms aim to protect the Software’s integrity, ensure fair use, and establish guidelines for authorized distribution, modification, and commercial use.
 *
 * For any inquiries, abuse reports, or violation notices, contact us at [abuse@mythicalsystems.xyz](mailto:abuse@mythicalsystems.xyz).
 *
 * ## 1. Grant of License
 *
 * ### 1.1. Usage Rights
 * - You are granted a non-exclusive, revocable license to use the Software, provided you comply with the terms herein.
 * - The Software must be linked to an active account on our public platform, MythicalSystems.
 *
 * ### 1.2. Modification Rights
 * - You may modify the Software only for personal use and must not distribute modified versions unless explicitly approved by MythicalSystems or Cassian Gherman.
 *
 * ### 1.3. Redistribution and Commercial Use
 * - Redistribution of the Software, whether modified or unmodified, is strictly prohibited unless explicitly authorized in writing by MythicalSystems or Cassian Gherman.
 * - Selling the Software or its derivatives is only permitted on authorized marketplaces specified by MythicalSystems.
 * - Unauthorized leaking, sharing, or redistribution of the Software or its components is illegal and subject to legal action.
 *
 * ### 1.4. Third-Party Addons and Plugins
 * - The creation, sale, and distribution of third-party addons or plugins for the Software are permitted, provided they comply with this license.
 * - All third-party addons or plugins must not attempt to bypass, modify, or interfere with the core functionality or licensing requirements of the Software.
 *
 * ## 2. Restrictions
 *
 * ### 2.1. Account Requirement
 * - The Software requires an active account on MythicalSystems. Attempts to modify, bypass, or remove this requirement are strictly prohibited.
 *
 * ### 2.2. Unauthorized Use
 * - Use of the Software to perform unauthorized actions, including but not limited to exploiting vulnerabilities, bypassing authentication, or reverse engineering, is prohibited.
 *
 * ### 2.3. Leaking and Distribution
 * - Any unauthorized leaking, sharing, or distribution of the Software is a direct violation of this license. Legal action will be taken against violators.
 * - Leaked or pirated copies of the Software are considered illegal, and users found utilizing such versions will face immediate termination of access and potential legal consequences.
 *
 * ### 2.4. Modification of Terms
 * - The terms and conditions of this license may not be modified, replaced, or overridden in any distributed version of the Software.
 *
 * ## 3. Attribution and Copyright
 *
 * ### 3.1. Attribution
 * - You must retain all copyright notices, attributions, and references to MythicalSystems and Cassian Gherman in all copies, derivatives, or distributions of the Software.
 *
 * ### 3.2. Copyright Invariance
 * - Copyright notices must remain intact and unaltered in all versions of the Software, including modified versions.
 *
 * ## 4. Legal and Liability Terms
 *
 * ### 4.1. Disclaimer of Liability
 * - The Software is provided "as is," without any warranty, express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, or non-infringement.
 * - MythicalSystems and Cassian Gherman shall not be held liable for any damages arising from the use, misuse, or inability to use the Software, including but not limited to:
 * 	- Loss of data, profits, or revenue.
 * 	- Security vulnerabilities such as SQL injection, zero-day exploits, or other potential risks.
 * 	- System failures, downtime, or disruptions.
 *
 * ### 4.2. Enforcement
 * - Violations of this license will result in immediate termination of access to the Software and may involve legal action.
 * - MythicalSystems reserves the right to suspend or terminate access to any user, client, or hosting provider without prior notice.
 *
 * ### 4.3. No Guarantees
 * - MythicalSystems does not guarantee uninterrupted or error-free operation of the Software.
 *
 * ## 5. Privacy and Data Sharing
 *
 * ### 5.1. Public Information
 * - Some user information may be shared with third parties or made publicly visible in accordance with our Privacy Policy and Terms of Service. For more details, please visit:
 * 	- [Privacy Policy](https://www.mythical.systems/privacy)
 * 	- [Terms of Service](https://www.mythical.systems/terms)
 *
 * ### 5.2. Data Collection
 * - The Software may collect and transmit anonymized usage data to improve performance and functionality.
 *
 * ## 6. Governing Law
 *
 * ### 6.1. Jurisdiction
 * - This license shall be governed and construed in accordance with the laws of Austria.
 *
 * ### 6.2. Dispute Resolution
 * - All disputes arising under or in connection with this license shall be subject to the exclusive jurisdiction of the courts in Austria.
 *
 * ## 7. Termination
 *
 * ### 7.1. Violation of Terms
 * - MythicalSystems reserves the right to terminate access to the Software for any user found in violation of this license.
 *
 * ### 7.2. Immediate Termination
 * - Termination may occur immediately and without prior notice.
 *
 * ## 8. Contact Information
 * For abuse reports, legal inquiries, or support, contact [abuse@mythicalsystems.xyz](mailto:abuse@mythicalsystems.xyz).
 *
 * ## 9. Acceptance
 * By using, modifying, or distributing the Software, you agree to the terms outlined in this license.
 */

namespace MythicalClient\Cli\Commands;

use MythicalClient\Cli\App;
use MythicalClient\Chat\Database;
use MythicalClient\Cli\CommandBuilder;

class Migrate extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $cliApp = App::getInstance();
        if (!file_exists(__DIR__ . '/../../../storage/.env')) {
            \MythicalClient\App::getInstance(true)->getLogger()->warning('Executed a command without a .env file');
            $cliApp->send('The .env file does not exist. Please create one before running this command');
            exit;
        }
        $sqlScript = self::getMigrationSQL();
        try {
            \MythicalClient\App::getInstance(true)->loadEnv();
            $db = new Database($_ENV['DATABASE_HOST'], $_ENV['DATABASE_DATABASE'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
        } catch (\Exception $e) {
            $cliApp->send('&cFailed to connect to the database: &r' . $e->getMessage());
            exit;
        }
        $cliApp->send('&aConnected to the database!');

        /**
         * Check if the migrations table exists.
         */
        try {
            $query = $db->getPdo()->query("SHOW TABLES LIKE 'mythicalclient_migrations'");
            if ($query->rowCount() > 0) {
                $cliApp->send('&7The migrations table already exists!');
            } else {
                $db->getPdo()->exec(statement: $sqlScript);
                $cliApp->send('&7The migrations table has been created!');
            }
        } catch (\Exception $e) {
            $cliApp->send('&cFailed to create the migrations table: &r' . $e->getMessage());
            exit;
        }
        /**
         * Get all the migration scripts.
         */
        $migrations = scandir(__DIR__ . '/../../../storage/migrations/');
        foreach ($migrations as $migration) {
            /**
             * Skip the . and .. directories.
             */
            if ($migration == '.' || $migration == '..') {
                continue;
            }
            /**
             * Get the migration content.
             */
            $migration = __DIR__ . "/../../../storage/migrations/$migration";
            $migrationContent = file_get_contents($migration);
            $migrationName = explode('/', $migration);
            $migrationName = end($migrationName);

            /**
             * Check if the migration was already executed.
             */
            $stmt = $db->getPdo()->prepare("SELECT COUNT(*) FROM mythicalclient_migrations WHERE script = :script AND migrated = 'true'");
            $stmt->execute(['script' => $migrationName]);
            $migrationExists = $stmt->fetchColumn();

            if ($migrationExists > 0) {
                $cliApp->send("&7Migration already executed: &e$migrationName");
                continue;
            }

            /**
             * Execute the migration.
             */
            try {
                $db->getPdo()->exec($migrationContent);
                $cliApp->send("&7Migration executed successfully: &e$migrationName");
            } catch (\Exception $e) {
                $cliApp->send('&cFailed to execute migration: &8[&4' . $migrationName . '&8] &r' . $e->getMessage());
                exit;
            }

            /**
             * Save the migration to the database.
             */
            try {
                $stmt = $db->getPdo()->prepare('INSERT INTO mythicalclient_migrations (script, migrated) VALUES (:script, :migrated)');
                $stmt->execute([
                    'script' => $migrationName,
                    'migrated' => 'true',
                ]);
                $cliApp->send('&aMigration saved to the database!');
            } catch (\Exception $e) {
                $cliApp->send('&cFailed to save the migration to the database: &r' . $e->getMessage());
                exit;
            }
        }
        $cliApp->send('&aAll migrations have been executed!');
    }

    public static function getDescription(): string
    {
        return 'Migrate the database to the latest version';
    }

    public static function getSubCommands(): array
    {
        return [];
    }

    private static function getMigrationSQL(): string
    {
        return "CREATE TABLE IF NOT EXISTS `mythicalclient_migrations` (
            `id` INT NOT NULL AUTO_INCREMENT COMMENT 'The id of the migration!',
            `script` TEXT NOT NULL COMMENT 'The script to be migrated!',
            `migrated` ENUM('true','false') NOT NULL DEFAULT 'true' COMMENT 'Did we migrate this already?',
            `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'The date from when this was executed!',
            PRIMARY KEY (`id`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT = 'The migrations table is table where save the sql migrations!';";
    }
}
