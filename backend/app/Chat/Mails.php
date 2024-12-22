<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * MIT License
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
     * 
     * @return bool
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
     * 
     * @return bool
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
     * 
     * @return array
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
