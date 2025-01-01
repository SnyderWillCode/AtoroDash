<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * 2021-2025 (c) All rights reserved
 *
 * MIT License
 *
 * (c) MythicalSystems - All rights reserved
 * (c) NaysKutzu - All rights reserved
 * (c) Cassian Gherman- All rights reserved
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

use MythicalClient\App;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\columns\EmailVerificationColumns;

class Verification extends Database
{
    public const TABLE_NAME = 'mythicalclient_users_email_verification';

    /**
     * Add a new verification code to the database.
     *
     * @param string $code The verification code
     * @param string $uuid The user's UUID
     * @param EmailVerificationColumns|string $type The type of verification [password,verify]
     */
    public static function add(string $code, string $uuid, EmailVerificationColumns|string $type): void
    {
        try {
            if (User::exists(UserColumns::UUID, $uuid)) {
                $conn = self::getPdoConnection();
                $query = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' (code, user, type) VALUES (:code, :user, :type)');
                $query->execute(['code' => $code, 'user' => $uuid, 'type' => (string) $type]);
            } else {
                App::getInstance(true)->getLogger()->error("User with UUID {$uuid} does not exist.");

                return;
            }
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Chat/Verification.php) Failed to add code to verify table: ' . $e->getMessage());
        }
    }

    /**
     * Verify a code.
     *
     * @param string $code The code to verify
     * @param EmailVerificationColumns|string $type The type of verification [password,verify]
     */
    public static function verify(string $code, EmailVerificationColumns|string $type): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE code = :code AND type = :type');
            $query->execute(['code' => $code, 'type' => $type]);
            $result = $query->fetch();
            if (empty($result)) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            App::getInstance(softBoot: true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Chat/Verification.php) Failed to verify code table: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Delete a code.
     *
     * @param string $code The code to delete
     */
    public static function delete(string $code): void
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('DELETE FROM ' . self::TABLE_NAME . ' WHERE code = :code');
            $query->execute(['code' => $code]);

            return;
        } catch (\Exception $e) {
            App::getInstance(softBoot: true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Chat/Verification.php) Failed to delete verify code from table: ' . $e->getMessage());

            return;
        }
    }

    /**
     * Get the user's UUID from a code.
     *
     * @param string $code The code to get the user's UUID from
     *
     * @return string Get the user uuid
     */
    public static function getUserUUID(string $code): string
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE code = :code');
            $query->execute(['code' => $code]);
            $result = $query->fetch();
            if ($result === false) {
                return '';
            }

            return $result['user'];

        } catch (\Exception $e) {
            App::getInstance(softBoot: true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Chat/Verification.php) Failed to get user UUID from code: ' . $e->getMessage());

            return '';
        }
    }
}
