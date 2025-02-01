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

namespace MythicalClient\Chat\Services;

use MythicalClient\Chat\Database;

class ServiceTagLines extends Database
{
    public const TABLE_NAME = 'mythicalclient_services_categories_features';

    /**
     * Create a new service tag line.
     *
     * @param string $name Tag line name
     * @param int $category Category ID
     * @param string $description Tag line description
     *
     * @return array|false Returns tag line data on success, false on failure
     */
    public static function create(string $name, int $category, string $description): array|false
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
				INSERT INTO ' . self::TABLE_NAME . ' 
				(name, category, description) 
				VALUES (:name, :category, :description)
			');

            $success = $stmt->execute([
                ':name' => $name,
                ':category' => $category,
                ':description' => $description,
            ]);

            if (!$success) {
                return false;
            }

            return self::getById($conn->lastInsertId());
        } catch (\Exception $e) {
            self::db_Error('Failed to create service tag line: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get a service tag line by ID.
     *
     * @param int $id Tag line ID
     *
     * @return array|false Returns tag line data or false if not found
     */
    public static function getById(int $id): array|false
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
				SELECT * FROM ' . self::TABLE_NAME . "
				WHERE id = :id AND deleted = 'false'
			");

            $stmt->execute([':id' => $id]);

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get service tag line: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get all tag lines for a category.
     *
     * @param int $categoryId Category ID
     * @param bool $includeDisabled Whether to include disabled tag lines
     *
     * @return array Returns array of tag lines
     */
    public static function getByCategoryId(int $categoryId, bool $includeDisabled = false): array
    {
        try {
            $conn = self::getPdoConnection();

            $sql = 'SELECT * FROM ' . self::TABLE_NAME . " 
				   WHERE category = :category_id AND deleted = 'false'";

            if (!$includeDisabled) {
                $sql .= " AND enabled = 'true'";
            }

            $sql .= ' ORDER BY date DESC';

            $stmt = $conn->prepare($sql);
            $stmt->execute([':category_id' => $categoryId]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get category tag lines: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Update a service tag line.
     *
     * @param int $id Tag line ID
     * @param array $data Update data
     *
     * @return bool Success status
     */
    public static function update(int $id, array $data): bool
    {
        try {
            $conn = self::getPdoConnection();

            $allowedFields = ['name', 'category', 'description', 'enabled', 'locked'];
            $updates = [];
            $params = [':id' => $id];

            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($updates)) {
                return false;
            }

            $sql = 'UPDATE ' . self::TABLE_NAME . ' 
					SET ' . implode(', ', $updates) . " 
					WHERE id = :id AND deleted = 'false'";

            $stmt = $conn->prepare($sql);

            return $stmt->execute($params);
        } catch (\Exception $e) {
            self::db_Error('Failed to update service tag line: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Soft delete a service tag line.
     *
     * @param int $id Tag line ID
     *
     * @return bool Success status
     */
    public static function delete(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
				UPDATE ' . self::TABLE_NAME . "
				SET deleted = 'true'
				WHERE id = :id AND locked = 'false'
			");

            return $stmt->execute([':id' => $id]);
        } catch (\Exception $e) {
            self::db_Error('Failed to delete service tag line: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Toggle tag line enabled status.
     *
     * @param int $id Tag line ID
     * @param bool $enabled Desired enabled status
     *
     * @return bool Success status
     */
    public static function toggleEnabled(int $id, bool $enabled): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
				UPDATE ' . self::TABLE_NAME . "
				SET enabled = :enabled
				WHERE id = :id AND deleted = 'false'
			");

            return $stmt->execute([
                ':id' => $id,
                ':enabled' => $enabled ? 'true' : 'false',
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to toggle service tag line status: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Toggle tag line locked status.
     *
     * @param int $id Tag line ID
     * @param bool $locked Desired locked status
     *
     * @return bool Success status
     */
    public static function toggleLocked(int $id, bool $locked): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
				UPDATE ' . self::TABLE_NAME . "
				SET locked = :locked
				WHERE id = :id AND deleted = 'false'
			");

            return $stmt->execute([
                ':id' => $id,
                ':locked' => $locked ? 'true' : 'false',
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to toggle service tag line lock status: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Check if a tag line exists.
     *
     * @param int $id Tag line ID
     *
     * @return bool Whether the tag line exists
     */
    public static function exists(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
				SELECT COUNT(*) FROM ' . self::TABLE_NAME . "
				WHERE id = :id AND deleted = 'false'
			");

            $stmt->execute([':id' => $id]);

            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if service tag line exists: ' . $e->getMessage());

            return false;
        }
    }
}
