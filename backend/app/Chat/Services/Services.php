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

class Services extends Database
{
    public const SERVICE_TABLE = 'mythicalclient_services';

    /**
     * Create a new service.
     *
     * @param int $category Category ID
     * @param string $name Service name
     * @param string $tagline Service tagline
     * @param int $quantity Default quantity
     * @param int $stock Stock amount
     * @param bool $stockEnabled Whether stock is enabled
     * @param string $uri Service URI
     * @param string $shortDescription Short description
     * @param string $description Full description
     * @param int $setupFee Setup fee amount
     * @param int $provider Provider ID
     *
     * @return bool Success status
     */
    public static function createService(
        int $category,
        string $name,
        string $tagline,
        int $quantity,
        int $stock,
        bool $stockEnabled,
        string $uri,
        string $shortDescription,
        string $description,
        int $setupFee,
        int $provider,
    ): bool {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                INSERT INTO ' . self::SERVICE_TABLE . ' 
                (category, name, tagline, quantity, stock, stock_enabled, uri, 
                shortdescription, description, setup_fee, provider) 
                VALUES 
                (:category, :name, :tagline, :quantity, :stock, :stock_enabled, :uri,
                :shortdescription, :description, :setup_fee, :provider)
            ');

            $stmt->bindParam(':category', $category, \PDO::PARAM_INT);
            $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
            $stmt->bindParam(':tagline', $tagline, \PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
            $stmt->bindParam(':stock', $stock, \PDO::PARAM_INT);
            $stockEnabledStr = $stockEnabled ? 'true' : 'false';
            $stmt->bindParam(':stock_enabled', $stockEnabledStr, \PDO::PARAM_STR);
            $stmt->bindParam(':uri', $uri, \PDO::PARAM_STR);
            $stmt->bindParam(':shortdescription', $shortDescription, \PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, \PDO::PARAM_STR);
            $stmt->bindParam(':setup_fee', $setupFee, \PDO::PARAM_INT);
            $stmt->bindParam(':provider', $provider, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to create service: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get a service by ID.
     *
     * @param int $id Service ID
     *
     * @return array|null Service data or null if not found
     */
    public static function getService(int $id): ?array
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                SELECT * FROM ' . self::SERVICE_TABLE . "
                WHERE id = :id AND deleted = 'false'
                LIMIT 1
            ");

            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (\Exception $e) {
            self::db_Error('Failed to get service: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Update a service.
     *
     * @param int $id Service ID
     * @param array $data Updated service data
     *
     * @return bool Success status
     */
    public static function updateService(int $id, array $data): bool
    {
        try {
            $conn = self::getPdoConnection();
            $updates = [];
            $params = [':id' => $id];

            $allowedFields = [
                'category' => \PDO::PARAM_INT,
                'name' => \PDO::PARAM_STR,
                'tagline' => \PDO::PARAM_STR,
                'quantity' => \PDO::PARAM_INT,
                'stock' => \PDO::PARAM_INT,
                'stock_enabled' => \PDO::PARAM_STR,
                'uri' => \PDO::PARAM_STR,
                'shortdescription' => \PDO::PARAM_STR,
                'description' => \PDO::PARAM_STR,
                'setup_fee' => \PDO::PARAM_INT,
                'provider' => \PDO::PARAM_INT,
                'enabled' => \PDO::PARAM_STR,
            ];

            foreach ($data as $field => $value) {
                if (isset($allowedFields[$field])) {
                    if ($field === 'stock_enabled' || $field === 'enabled') {
                        $value = $value ? 'true' : 'false';
                    }
                    $updates[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($updates)) {
                return false;
            }

            $sql = 'UPDATE ' . self::SERVICE_TABLE . ' 
                   SET ' . implode(', ', $updates) . ", date = CURRENT_TIMESTAMP 
                   WHERE id = :id AND deleted = 'false'";

            $stmt = $conn->prepare($sql);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value, $allowedFields[ltrim($param, ':')] ?? \PDO::PARAM_STR);
            }

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to update service: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Delete a service.
     *
     * @param int $id Service ID
     *
     * @return bool Success status
     */
    public static function deleteService(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                UPDATE ' . self::SERVICE_TABLE . "
                SET deleted = 'true', date = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to delete service: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * List all services.
     *
     * @param bool $includeDisabled Whether to include disabled services
     * @param int|null $category Filter by category ID
     * @param int|null $provider Filter by provider ID
     *
     * @return array List of services
     */
    public static function listServices(bool $includeDisabled = false, ?int $category = null, ?int $provider = null): array
    {
        try {
            $conn = self::getPdoConnection();
            $where = ["deleted = 'false'"];
            $params = [];

            if (!$includeDisabled) {
                $where[] = "enabled = 'true'";
            }

            if ($category !== null) {
                $where[] = 'category = :category';
                $params[':category'] = $category;
            }

            if ($provider !== null) {
                $where[] = 'provider = :provider';
                $params[':provider'] = $provider;
            }

            $sql = 'SELECT * FROM ' . self::SERVICE_TABLE . '
                   WHERE ' . implode(' AND ', $where) . '
                   ORDER BY date DESC';

            $stmt = $conn->prepare($sql);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value, \PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to list services: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get services by category.
     *
     * @param int $categoryId Category ID
     *
     * @return array Services data
     */
    public static function getServicesByCategory(int $categoryId): array
    {
        try {
            $conn = self::getPdoConnection();
            $stmt = $conn->prepare('SELECT * FROM ' . self::SERVICE_TABLE . " WHERE category = :category AND deleted = 'false' AND enabled = 'true'");
            $stmt->execute([':category' => $categoryId]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get services by category: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Get service by URI.
     *
     * @param string $uri The service URI
     *
     * @return array|null The service data or null if not found
     */
    public static function getServiceByUri(string $uri): ?array
    {
        try {
            $conn = self::getPdoConnection();
            $stmt = $conn->prepare('SELECT * FROM ' . self::SERVICE_TABLE . " WHERE uri = :uri AND deleted = 'false' AND enabled = 'true' LIMIT 1");
            $stmt->bindParam(':uri', $uri);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (\Exception $e) {
            error_log('Error in getServiceByUri: ' . $e->getMessage());

            return null;
        }
    }
}
