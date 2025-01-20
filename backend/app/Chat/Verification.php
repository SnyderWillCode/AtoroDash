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
            self::db_Error('Failed to add new email verification: ' . $e->getMessage());
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
            self::db_Error('Failed to verify code: ' . $e->getMessage());

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
            self::db_Error('Failed to delete code from verify table: ' . $e->getMessage());

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
            self::db_Error('Failed to compute uuid: ' . $e->getMessage());

            return '';
        }
    }
}
