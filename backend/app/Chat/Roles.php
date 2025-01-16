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

use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\columns\RolesColumns;

class Roles extends Database
{
    public const TABLE_NAME = 'mythicalclient_roles';

    /**
     * Get the list of roles.
     */
    public static function getList(): array
    {
        $con = self::getPdoConnection();
        $stmt = $con->prepare('SELECT * FROM ' . self::TABLE_NAME);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get the role info.
     *
     * @param \RolesInterface|string $real_name The role name
     * @param RolesColumns|string $info The column name
     *
     * @throws \InvalidArgumentException If the column name is invalid
     *
     * @return string|null The value of the column
     */
    public static function getInfo(\RolesInterface|string $real_name, RolesColumns|string $info): ?string
    {
        if (!in_array($info, RolesColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }
        $con = self::getPdoConnection();
        $stmt = $con->prepare('SELECT ' . $info . ' FROM ' . self::TABLE_NAME . ' WHERE real_name = :real_name');
        $stmt->bindParam(':real_name', $real_name);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Update the role info.
     *
     * @param \RolesInterface|string $real_name The role name
     * @param RolesColumns|string $info The column name
     * @param string $value The new value
     *
     * @throws \InvalidArgumentException If the column name is invalid
     */
    public static function updateInfo(\RolesInterface|string $real_name, RolesColumns|string $info, string $value): bool
    {
        if (!in_array($info, RolesColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }
        $con = self::getPdoConnection();
        $stmt = $con->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . $info . ' = :value WHERE real_name = :real_name');
        $stmt->bindParam(':real_name', $real_name);
        $stmt->bindParam(':value', $value);

        return $stmt->execute();
    }

    /**
     * Get the role name.
     *
     * @param string $uuid The user UUID
     *
     * @return string|null The role name
     */
    public static function getUserRoleName(string $uuid): ?string
    {
        $con = self::getPdoConnection();
        $id = User::getInfo(User::getTokenFromUUID($uuid), UserColumns::ROLE_ID, false);
        $stmt = $con->prepare('SELECT name FROM ' . self::TABLE_NAME . ' WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Get the role real name.
     *
     * @param string $uuid The user UUID
     *
     * @return string|null The role real name
     */
    public static function getUserRealName(string $uuid): ?string
    {
        $con = self::getPdoConnection();
        $id = User::getInfo(User::getTokenFromUUID($uuid), UserColumns::ROLE_ID, false);
        $stmt = $con->prepare('SELECT real_name FROM ' . self::TABLE_NAME . ' WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
