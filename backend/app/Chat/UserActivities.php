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

use MythicalClient\Chat\interface\UserActivitiesTypes;

class UserActivities
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
        $dbConn = Database::getPdoConnection();

        $stmt = $dbConn->prepare('INSERT INTO ' . self::getTable() . ' (user, action, ip_address) VALUES (:user, :action, :ip_address)');

        return $stmt->execute([
            ':user' => $uuid,
            ':action' => $type,
            ':ip_address' => $ipv4,
        ]);
    }

    /**
     * Get user activities.
     *
     * @param string $uuid User UUID
     */
    public static function get(string $uuid): array
    {
        $dbConn = Database::getPdoConnection();

        $stmt = $dbConn->prepare('SELECT * FROM ' . self::getTable() . ' WHERE user = :user LIMIT 125');
        $stmt->execute([
            ':user' => $uuid,
        ]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
