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

class ServiceCategories extends Database
{
    public const TABLE_NAME = 'mythicalclient_services_categories';

    /**
     * Create a new service category.
     *
     * @param string $name Category name
     * @param string $uri Category URI
     * @param string $headline Category headline
     * @param string $tagline Category tagline
     *
     * @return array|false Returns category data on success, false on failure
     */
    public static function create(string $name, string $uri, string $headline, string $tagline): array|false
    {
        $conn = self::getPdoConnection();

        $stmt = $conn->prepare('
			INSERT INTO ' . self::TABLE_NAME . ' 
			(name, uri, headline, tagline) 
			VALUES (:name, :uri, :headline, :tagline)
		');

        $success = $stmt->execute([
            ':name' => $name,
            ':uri' => $uri,
            ':headline' => $headline,
            ':tagline' => $tagline,
        ]);

        if (!$success) {
            return false;
        }

        return self::getById($conn->lastInsertId());
    }

    /**
     * Get a service category by ID.
     *
     * @param int $id Category ID
     *
     * @return array|false Returns category data or false if not found
     */
    public static function getById(int $id): array|false
    {
        $conn = self::getPdoConnection();

        $stmt = $conn->prepare('
			SELECT * FROM ' . self::TABLE_NAME . "
			WHERE id = :id AND deleted = 'false'
		");

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Get all active service categories.
     *
     * @param bool $includeDisabled Whether to include disabled categories
     *
     * @return array Returns array of categories
     */
    public static function getAll(bool $includeDisabled = false): array
    {
        $conn = self::getPdoConnection();

        $sql = 'SELECT * FROM ' . self::TABLE_NAME . " WHERE deleted = 'false'";
        if (!$includeDisabled) {
            $sql .= " AND enabled = 'true'";
        }
        $sql .= ' ORDER BY date DESC';

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Update a service category.
     *
     * @param int $id Category ID
     * @param array $data Update data
     *
     * @return bool Success status
     */
    public static function update(int $id, array $data): bool
    {
        $conn = self::getPdoConnection();

        $allowedFields = ['name', 'uri', 'headline', 'tagline', 'enabled', 'locked'];
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
    }

    /**
     * Soft delete a service category.
     *
     * @param int $id Category ID
     *
     * @return bool Success status
     */
    public static function delete(int $id): bool
    {
        $conn = self::getPdoConnection();

        $stmt = $conn->prepare('
			UPDATE ' . self::TABLE_NAME . "
			SET deleted = 'true'
			WHERE id = :id AND locked = 'false'
		");

        return $stmt->execute([':id' => $id]);
    }

    /**
     * Toggle category enabled status.
     *
     * @param int $id Category ID
     * @param bool $enabled Desired enabled status
     *
     * @return bool Success status
     */
    public static function toggleEnabled(int $id, bool $enabled): bool
    {
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
    }

    /**
     * Toggle category locked status.
     *
     * @param int $id Category ID
     * @param bool $locked Desired locked status
     *
     * @return bool Success status
     */
    public static function toggleLocked(int $id, bool $locked): bool
    {
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
    }

    /**
     * Check if a category exists by URI.
     *
     * @param string $uri Category URI
     * @param int|null $excludeId Optional ID to exclude from check
     *
     * @return bool Whether the URI exists
     */
    public static function existsByUri(string $uri, ?int $excludeId = null): bool
    {
        $conn = self::getPdoConnection();

        $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . "
				WHERE uri = :uri AND deleted = 'false'";

        $params = [':uri' => $uri];

        if ($excludeId !== null) {
            $sql .= ' AND id != :excludeId';
            $params[':excludeId'] = $excludeId;
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Get a service category by name.
     *
     * @param string $name Category name to search for
     *
     * @return array|false Returns category data or false if not found/error occurs
     */
    public static function getCategoryByName(string $name): array|false
    {
        if (empty($name)) {
            return false;
        }

        try {
            $conn = self::getPdoConnection();
            $stmt = $conn->prepare('
				SELECT * FROM ' . self::TABLE_NAME . " 
				WHERE name = :name AND deleted = 'false'
			");

            $stmt->execute([':name' => $name]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result === false ? false : $result;

        } catch (\PDOException $e) {
            self::db_Error('Failed to get category by name: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get a service category by URI.
     *
     * @param string $uri Category URI to search for
     *
     * @return array|false Returns category data or false if not found/error occurs
     */
    public static function getCategoryByUri(string $uri): array|false
    {
        if (empty($uri)) {
            return false;
        }

        try {
            $conn = self::getPdoConnection();
            $stmt = $conn->prepare('
				SELECT * FROM ' . self::TABLE_NAME . " 
				WHERE uri = :uri AND deleted = 'false'
			");

            $stmt->execute([':uri' => $uri]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result === false ? false : $result;

        } catch (\PDOException $e) {
            self::db_Error('Failed to get category by URI: ' . $e->getMessage());

            return false;
        }
    }
}
