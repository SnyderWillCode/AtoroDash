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

class Mails
{
    /**
     * Add a mail.
     *
     * @param string $subject Mail subject
     * @param string $body Mail body
     * @param string $uuid User UUID
     */
    public static function add(string $subject, string $body, string $uuid): bool
    {
        try {
            $dbConn = Database::getPdoConnection();
            $from = \MythicalClient\App::getInstance(true)->getConfig()->getSetting(\MythicalClient\Config\ConfigInterface::SMTP_FROM, 'system@mythical.systems');
            $stmt = $dbConn->prepare('INSERT INTO ' . self::getTableName() . ' (subject, body, `from`, `user`) VALUES (:subject, :body, :from, :user)');
            $stmt->bindParam(':subject', $subject);
            $stmt->bindParam(':body', $body);
            $stmt->bindParam(':from', $from);
            $stmt->bindParam(':user', $uuid);

            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Delete a mail.
     *
     * @param string $id Mail ID
     * @param string $uuid User UUID
     */
    public static function delete(string $id, string $uuid): bool
    {
        try {
            $dbConn = Database::getPdoConnection();
            $stmt = $dbConn->prepare('DELETE FROM ' . self::getTableName() . ' WHERE id = :id AND `user` = :user');
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user', $uuid);

            return $stmt->execute();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all mails for a user.
     *
     * @param string $uuid User UUID
     */
    public static function getAll(string $uuid): array
    {
        try {
            $dbConn = Database::getPdoConnection();
            $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTableName() . ' WHERE `user` = :user ORDER BY id DESC LIMIT 50');
            $stmt->bindParam(':user', $uuid);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get a mail.
     *
     * @param string $id Mail ID
     *
     * @return array Mail data
     */
    public static function get(string $id): array
    {
        try {
            $dbConn = Database::getPdoConnection();
            $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTableName() . ' WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if a mail exists.
     *
     * @param string $id Mail ID
     *
     * @return bool Does mail exist
     */
    public static function exists(string $id): bool
    {
        try {
            $dbConn = Database::getPdoConnection();
            $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTableName() . ' WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all mails for a user.
     *
     * @param string $uuid User UUID
     * @param string $id Mail ID
     *
     * @return bool Does user own email
     */
    public static function doesUserOwnEmail(string $uuid, string $id): bool
    {
        try {
            $dbConn = Database::getPdoConnection();
            $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTableName() . ' WHERE id = :id AND `user` = :user');
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user', $uuid);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function getTableName(): string
    {
        return 'mythicalclient_users_mails';
    }
}
