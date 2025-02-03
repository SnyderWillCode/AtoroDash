<?php

namespace MythicalClient\Chat\Orders;

use PDO;

class OrdersConfig extends \MythicalClient\Chat\Database
{
    private const TABLE_NAME = 'mythicalclient_orders_config';

    /**
     * Add a configuration value for an order
     *
     * @param int $orderId Order ID
     * @param string $key Configuration key
     * @param string $value Configuration value
     * @return bool Success status
     */
    public static function addConfig(int $orderId, string $key, string $value): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' (`order`, kev, valuev) VALUES (:order, :key, :value)');
            return $query->execute([
                'order' => $orderId,
                'key' => $key,
                'value' => $value
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to add order config: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all configuration values for an order
     *
     * @param int $orderId Order ID
     * @return array Configuration values
     */
    public static function getOrderConfig(int $orderId): array
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE `order` = :order AND deleted = "false"');
            $query->execute(['order' => $orderId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get order config: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a specific configuration value for an order
     *
     * @param int $orderId Order ID
     * @param string $key Configuration key
     * @return array|false Configuration value or false if not found
     */
    public static function getConfigValue(int $orderId, string $key): array|false
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE `order` = :order AND kev = :key AND deleted = "false"');
            $query->execute([
                'order' => $orderId,
                'key' => $key
            ]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get config value: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update a configuration value
     *
     * @param int $orderId Order ID
     * @param string $key Configuration key
     * @param string $value New value
     * @return bool Success status
     */
    public static function updateConfigValue(int $orderId, string $key, string $value): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET valuev = :value WHERE `order` = :order AND kev = :key AND deleted = "false"');
            return $query->execute([
                'order' => $orderId,
                'key' => $key,
                'value' => $value
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to update config value: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a configuration value
     *
     * @param int $orderId Order ID
     * @param string $key Configuration key
     * @return bool Success status
     */
    public static function deleteConfig(int $orderId, string $key): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET deleted = "true" WHERE `order` = :order AND kev = :key');
            return $query->execute([
                'order' => $orderId,
                'key' => $key
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to delete config: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bulk add configuration values for an order
     *
     * @param int $orderId Order ID
     * @param array $configs Array of configs [['key' => string, 'value' => string, 'type' => string], ...]
     * @return bool Success status
     */
    public static function bulkAddConfig(int $orderId, array $configs): bool
    {
        try {
            $conn = self::getPdoConnection();
            $conn->beginTransaction();

            $query = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' (`order`, kev, valuev) VALUES (:order, :key, :value)');
            
            foreach ($configs as $config) {
                $success = $query->execute([
                    'order' => $orderId,
                    'key' => $config['key'],
                    'value' => $config['value']
                ]);

                if (!$success) {
                    $conn->rollBack();
                    return false;
                }
            }

            $conn->commit();
            return true;
        } catch (\Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            self::db_Error('Failed to bulk add config: ' . $e->getMessage());
            return false;
        }
    }
}