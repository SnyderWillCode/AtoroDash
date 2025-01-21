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

class Departments extends Database
{
    public const TABLE_NAME = 'mythicalclient_departments';

    public static function create(
        string $name,
        string $description,
        string $open,
        string $close,
    ): void {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSERT INTO ' . self::TABLE_NAME . ' (name, description, time_open, time_close) VALUES (:name, :description, :open, :close)';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':open', $open);
            $stmt->bindParam(':close', $close);
            $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to create department: ' . $e->getMessage());
        }
    }

    public static function update(
        int $id,
        string $name,
        string $description,
        string $open,
        string $close,
    ): void {
        try {
            if (!self::exists($id)) {
                App::getInstance(true)->getLogger()->warning('Department does not exist but tried to update it.', true);

                return;
            }
            $con = self::getPdoConnection();
            $sql = 'UPDATE ' . self::TABLE_NAME . ' SET name = :name, description = :description, time_open = :open, time_close = :close WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':open', $open);
            $stmt->bindParam(':close', $close);
            $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to update department: ' . $e->getMessage());
        }
    }

    public static function exists(int $id): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT id FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if department exists: ' . $e->getMessage());

            return false;
        }
    }

    public static function delete(int $id): bool
    {
        try {
            if (!self::exists($id)) {
                App::getInstance(true)->getLogger()->warning('Department does not exist but tried to delete it.', true);

                return false;
            }
            $con = self::getPdoConnection();
            $sql = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to delete department: ' . $e->getMessage());

            return false;
        }
    }

    public static function getAll(): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME;
            $stmt = $con->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get all departments: ' . $e->getMessage());

            return [];
        }
    }

    public static function get(int $id): array
    {
        try {
            if (!self::exists($id)) {
                App::getInstance(true)->getLogger()->warning('Department does not exist but tried to get it: ' . $id . '.', true);

                return [];
            }
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get department: ' . $e->getMessage());

            return [];
        }
    }
}
