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

namespace MythicalClient\Chat;

use PDO;

class Database
{
    private $pdo;

    /**
     * Database constructor.
     *
     * @param string $host the hostname or path to the database
     * @param string $dbName the name of the database (not used for sqlite)
     * @param string|null $username the username for the database connection (not used for sqlite)
     * @param string|null $password the password for the database connection (not used for sqlite)
     *
     * @throws \Exception if an unsupported database type is provided or the connection fails
     */
    public function __construct($host, $dbName, $username = null, $password = null)
    {
        $dsn = "mysql:host=$host;dbname=$dbName";
        try {
            $this->pdo = new \PDO($dsn, $username, $password);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Connection failed: ' . $e->getMessage());
        }
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Get the PDO connection.
     *
     * @return \PDO the PDO connection
     */
    public static function getPdoConnection(): \PDO
    {
        /**
         * Load the environment variables.
         */
        \MythicalClient\App::getInstance(true)->loadEnv();
        $con = new self($_ENV['DATABASE_HOST'], $_ENV['DATABASE_DATABASE'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);

        return $con->getPdo();
    }

    /**
     * Get the table row count.
     *
     * @param string $table the table name
     */
    public static function getTableRowCount(string $table): int
    {
        try {
            $query = self::getPdoConnection()->query('SELECT COUNT(*) FROM ' . $table);

            return (int) $query->fetchColumn();
        } catch (\Exception $e) {
            self::db_Error('Failed to get table row count: ' . $e->getMessage());

            return 0;
        }
    }

    /**
     * Check if a table exists.
     *
     * @param string $table the table name
     *
     * @return bool true if the table exists, false otherwise
     */
    public static function tableExists(string $table): bool
    {
        try {
            $query = self::getPdoConnection()->query("SHOW TABLES LIKE '$table'");

            return $query->rowCount() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if table exists: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get all tables in the database.
     */
    public static function getTables(): array
    {
        try {
            $query = self::getPdoConnection()->query('SHOW TABLES');

            return $query->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            self::db_Error('Failed to get tables: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Marks a record as deleted in the specified table by setting the 'deleted' column to 'true'.
     *
     * @param string $table the name of the table containing the record
     * @param int $row the ID of the record to mark as deleted
     */
    public static function markRecordAsDeleted(string $table, int $row): void
    {
        try {
            $query = self::getPdoConnection()->query('UPDATE ' . $table . " SET deleted = 'true' WHERE id = " . $row);
            $query->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to mark record as deleted: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Retrieves all records marked as deleted from the specified table.
     *
     * @param string $table the name of the table to query
     *
     * @return array array of deleted records in associative array format
     */
    public static function getDeletedRecords(string $table): array
    {
        try {
            $query = self::getPdoConnection()->query('SELECT * FROM ' . $table . " WHERE deleted = 'true'");

            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get deleted records: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Restores a previously deleted record by setting the 'deleted' column to 'false'.
     *
     * @param string $table the name of the table containing the record
     * @param int $row the ID of the record to restore
     */
    public static function restoreRecord(string $table, int $row): void
    {
        try {
            $query = self::getPdoConnection()->query('UPDATE ' . $table . " SET deleted = 'false' WHERE id = " . $row);
            $query->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to restore record: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Permanently deletes a record from the specified table.
     *
     * @param string $table the name of the table containing the record
     * @param int $row the ID of the record to delete
     */
    public static function deleteRecord(string $table, int $row): void
    {
        try {
            $query = self::getPdoConnection()->query('DELETE FROM ' . $table . ' WHERE id = ' . $row);
            $query->execute();

        } catch (\Exception $e) {
            self::db_Error('Failed to delete record: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Locks a record in the specified table by setting the 'locked' column to 'true'.
     *
     * @param string $table the name of the table containing the record
     * @param int $row the ID of the record to lock
     */
    public static function lockRecord(string $table, int $row): void
    {
        try {
            $query = self::getPdoConnection()->query('UPDATE ' . $table . " SET locked = 'true' WHERE id = " . $row);
            $query->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to lock record: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Unlocks a record in the specified table by setting the 'locked' column to 'false'.
     *
     * @param string $table the name of the table containing the record
     * @param int $row the ID of the record to unlock
     */
    public static function unlockRecord(string $table, int $row): void
    {
        try {
            $query = self::getPdoConnection()->query('UPDATE ' . $table . " SET locked = 'false' WHERE id = " . $row);
            $query->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to unlock record: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Checks if a specific record is locked.
     *
     * @param string $table the name of the table containing the record
     * @param int $row the ID of the record to check
     *
     * @return bool returns true if the record is locked, false otherwise
     */
    public static function isLocked(string $table, int $row): bool
    {
        try {
            $query = self::getPdoConnection()->query('SELECT locked FROM ' . $table . ' WHERE id = ' . $row);

            return $query->fetch(\PDO::FETCH_ASSOC)['locked'] == 'true';
        } catch (\Exception $e) {
            self::db_Error('Failed to check for lock: ' . $e->getMessage());

            return false;
        }
    }

    public static function db_Error(string $message): void
    {
        $app = \MythicalClient\App::getInstance(true);
        $app->getLogger()->error($message);
    }
}
