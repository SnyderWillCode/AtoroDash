<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
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
        $app->getLogger()->error($message, true);
    }
}
