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

class ServicePrices extends Database
{
    public const PRICE_TABLE = 'mythicalclient_services_price';

    /**
     * Create a new service price entry.
     *
     * @param int $serviceId Service ID
     * @param int $type Price type ID
     * @param string|null $monthly Monthly price
     * @param string|null $quarterly Quarterly price
     * @param string|null $semiAnnually Semi-annually price
     * @param string|null $annually Annually price
     * @param string|null $biennially Biennially price
     * @param string|null $triennially Triennially price
     *
     * @return bool Success status
     */
    public static function createPrice(
        int $serviceId,
        int $type,
        ?string $monthly = null,
        ?string $quarterly = null,
        ?string $semiAnnually = null,
        ?string $annually = null,
        ?string $biennially = null,
        ?string $triennially = null,
    ): bool {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                INSERT INTO ' . self::PRICE_TABLE . ' 
                (service, type, monthly, quarterly, semi_annually, annually, biennially, triennially) 
                VALUES 
                (:service, :type, :monthly, :quarterly, :semi_annually, :annually, :biennially, :triennially)
            ');

            $stmt->bindParam(':service', $serviceId, \PDO::PARAM_INT);
            $stmt->bindParam(':type', $type, \PDO::PARAM_INT);
            $stmt->bindParam(':monthly', $monthly, \PDO::PARAM_STR);
            $stmt->bindParam(':quarterly', $quarterly, \PDO::PARAM_STR);
            $stmt->bindParam(':semi_annually', $semiAnnually, \PDO::PARAM_STR);
            $stmt->bindParam(':annually', $annually, \PDO::PARAM_STR);
            $stmt->bindParam(':biennially', $biennially, \PDO::PARAM_STR);
            $stmt->bindParam(':triennially', $triennially, \PDO::PARAM_STR);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to create service price: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get pricing for a specific service.
     *
     * @param int $serviceId Service ID
     * @param int|null $type Optional price type filter
     *
     * @return array Service pricing data
     */
    public static function getServicePricing(int $serviceId, ?int $type = null): array
    {
        try {
            $conn = self::getPdoConnection();

            $sql = 'SELECT * FROM ' . self::PRICE_TABLE . "
                   WHERE service = :service AND deleted = 'false'";

            if ($type !== null) {
                $sql .= ' AND type = :type';
            }

            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':service', $serviceId, \PDO::PARAM_INT);

            if ($type !== null) {
                $stmt->bindParam(':type', $type, \PDO::PARAM_INT);
            }

            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get service pricing: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Update service pricing.
     *
     * @param int $id Price entry ID
     * @param array $data Updated price data
     *
     * @return bool Success status
     */
    public static function updatePrice(int $id, array $data): bool
    {
        try {
            $conn = self::getPdoConnection();
            $updates = [];
            $params = [':id' => $id];

            $allowedFields = [
                'service' => \PDO::PARAM_INT,
                'type' => \PDO::PARAM_INT,
                'monthly' => \PDO::PARAM_STR,
                'quarterly' => \PDO::PARAM_STR,
                'semi_annually' => \PDO::PARAM_STR,
                'annually' => \PDO::PARAM_STR,
                'biennially' => \PDO::PARAM_STR,
                'triennially' => \PDO::PARAM_STR,
            ];

            foreach ($data as $field => $value) {
                if (isset($allowedFields[$field])) {
                    $updates[] = "$field = :$field";
                    $params[":$field"] = $value;
                }
            }

            if (empty($updates)) {
                return false;
            }

            $sql = 'UPDATE ' . self::PRICE_TABLE . ' 
                   SET ' . implode(', ', $updates) . ", date = CURRENT_TIMESTAMP 
                   WHERE id = :id AND deleted = 'false'";

            $stmt = $conn->prepare($sql);

            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value, $allowedFields[ltrim($param, ':')] ?? \PDO::PARAM_STR);
            }

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to update service price: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Delete a price entry.
     *
     * @param int $id Price entry ID
     *
     * @return bool Success status
     */
    public static function deletePrice(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                UPDATE ' . self::PRICE_TABLE . "
                SET deleted = 'true', date = CURRENT_TIMESTAMP
                WHERE id = :id
            ");

            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to delete service price: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get a specific price entry.
     *
     * @param int $id Price entry ID
     *
     * @return array|null Price data or null if not found
     */
    public static function getPrice(int $id): ?array
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                SELECT * FROM ' . self::PRICE_TABLE . "
                WHERE id = :id AND deleted = 'false'
                LIMIT 1
            ");

            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result ?: null;
        } catch (\Exception $e) {
            self::db_Error('Failed to get price entry: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Delete all prices for a service.
     *
     * @param int $serviceId Service ID
     *
     * @return bool Success status
     */
    public static function deleteServicePrices(int $serviceId): bool
    {
        try {
            $conn = self::getPdoConnection();

            $stmt = $conn->prepare('
                UPDATE ' . self::PRICE_TABLE . "
                SET deleted = 'true', date = CURRENT_TIMESTAMP
                WHERE service = :service
            ");

            $stmt->bindParam(':service', $serviceId, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to delete service prices: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get services by service ID.
     *
     * @param int $serviceId Service ID
     *
     * @return array Services data
     */
    public static function getServicesByService(int $serviceId): array
    {
        try {
            $conn = self::getPdoConnection();
            $stmt = $conn->prepare('SELECT * FROM ' . self::PRICE_TABLE . " WHERE service = :service AND deleted = 'false'");
            $stmt->execute([':service' => $serviceId]);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get services by service: ' . $e->getMessage());

            return [];
        }
    }
}
