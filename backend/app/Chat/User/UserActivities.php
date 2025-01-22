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

namespace MythicalClient\Chat\User;

use MythicalClient\Chat\Database;
use MythicalClient\Chat\interface\UserActivitiesTypes;

class UserActivities extends Database
{
    /**
     * Add user activity.
     *
     * @param string $uuid User UUID
     * @param string|UserActivitiesTypes $type Activity type
     * @param string $ipv4 IP address
     */
    public static function add(string $uuid, string|UserActivitiesTypes $type, string $ipv4): bool
    {
        try {
            $dbConn = Database::getPdoConnection();

            $stmt = $dbConn->prepare('INSERT INTO ' . self::getTable() . ' (user, action, ip_address) VALUES (:user, :action, :ip_address)');

            return $stmt->execute([
                ':user' => $uuid,
                ':action' => $type,
                ':ip_address' => $ipv4,
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to add user activity: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get user activities.
     *
     * @param string $uuid User UUID
     */
    public static function get(string $uuid): array
    {
        try {
            $dbConn = Database::getPdoConnection();

            $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTable() . ' WHERE user = :user LIMIT 125');
            $stmt->execute([
                ':user' => $uuid,
            ]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get user activities: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get all user activities.
     *
     * @param int $limit Limit
     */
    public static function getAll(int $limit = 50): array
    {
        try {
            $dbConn = Database::getPdoConnection();

            $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTable() . ' LIMIT ' . $limit);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get all user activities: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get table name.
     *
     * @return string Table name
     */
    public static function getTable(): string
    {
        return 'mythicalclient_users_activities';
    }
}
