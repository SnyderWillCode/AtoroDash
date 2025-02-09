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

namespace MythicalClient\Chat\Orders;

class Orders extends \MythicalClient\Chat\Database
{
    private const TABLE_NAME = 'mythicalclient_orders';

    /**
     * Create a new order.
     *
     * @param string $user User UUID
     * @param int $service Service ID
     * @param int $provider Provider ID
     *
     * @return array|false Returns the created order or false on failure
     */
    public static function create(string $user, int $service, int $provider): array|false
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' (user, service, provider) VALUES (:user, :service, :provider)');
            $success = $query->execute([
                'user' => $user,
                'service' => $service,
                'provider' => $provider,
            ]);

            if ($success) {
                return self::getOrder($conn->lastInsertId());
            }

            return false;
        } catch (\Exception $e) {
            self::db_Error('Failed to create order: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get an order by ID.
     *
     * @param int $id Order ID
     *
     * @return array|false Returns the order or false if not found
     */
    public static function getOrder(int $id): array|false
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = :id AND deleted = "false"');
            $query->execute(['id' => $id]);

            return $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get order: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get all orders for a user.
     *
     * @param string $user User UUID
     *
     * @return array Returns array of orders
     */
    public static function getUserOrders(string $user): array
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE user = :user AND deleted = "false" ORDER BY date DESC');
            $query->execute(['user' => $user]);

            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get user orders: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Update order status.
     *
     * @param int $id Order ID
     * @param string $status New status ('processed', 'processing', 'failed')
     *
     * @return bool Success status
     */
    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['processed', 'processing', 'failed', 'deployed', 'deploying'])) {
            return false;
        }

        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET status = :status WHERE id = :id AND deleted = "false"');

            return $query->execute([
                'id' => $id,
                'status' => $status,
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to update order status: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Update order days left.
     */
    public static function updateDaysLeft(int $id, int $days): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET days_left = :days WHERE id = :id AND deleted = "false"');

            return $query->execute([
                'id' => $id,
                'days' => $days,
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to update order days left: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Soft delete an order.
     *
     * @param int $id Order ID
     *
     * @return bool Success status
     */
    public static function delete(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET deleted = "true" WHERE id = :id');

            return $query->execute(['id' => $id]);
        } catch (\Exception $e) {
            self::db_Error('Failed to delete order: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Check if an order exists.
     *
     * @param int $id Order ID
     *
     * @return bool Whether the order exists
     */
    public static function exists(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE id = :id AND deleted = "false"');
            $query->execute(['id' => $id]);

            return (int) $query->fetchColumn() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check order existence: ' . $e->getMessage());

            return false;
        }
    }
}
