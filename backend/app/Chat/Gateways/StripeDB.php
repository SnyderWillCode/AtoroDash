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

namespace MythicalClient\Chat\Gateways;

use MythicalClient\Chat\Database;

class StripeDB extends Database
{
    public const TABLE_NAME = 'mythicalclient_stripe_payments';

    /**
     * Create a new Stripe payment record.
     *
     * @param string $code Stripe payment code/token
     * @param int $coins Amount of coins
     * @param string $user User UUID
     *
     * @return bool Success status
     */
    public static function create(string $code, int $coins, string $user): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSERT INTO ' . self::TABLE_NAME . ' (code, coins, user) VALUES (:code, :coins, :user)';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':coins', $coins);
            $stmt->bindParam(':user', $user);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to create Stripe payment: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get a payment by its code.
     *
     * @param string $code Payment code
     *
     * @return array|false Payment data or false if not found
     */
    public static function getByCode(string $code): array|false
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . " WHERE code = :code AND deleted = 'false'";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get Stripe payment: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get all payments for a user.
     *
     * @param string $uuid User UUID
     *
     * @return array Array of payments
     */
    public static function getUserPayments(string $uuid): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . " WHERE user = :uuid AND deleted = 'false' ORDER BY date DESC";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':uuid', $uuid);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get user Stripe payments: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Update payment status.
     *
     * @param string $code Payment code
     * @param string $status New status ('processing'|'processed'|'failed')
     *
     * @return bool Success status
     */
    public static function updateStatus(string $code, string $status): bool
    {
        if (!in_array($status, ['processing', 'processed', 'failed'])) {
            return false;
        }

        try {
            $con = self::getPdoConnection();
            $sql = 'UPDATE ' . self::TABLE_NAME . " SET status = :status WHERE code = :code AND locked = 'false'";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->bindParam(':status', $status);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to update Stripe payment status: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Soft delete a payment record.
     *
     * @param string $code Payment code
     *
     * @return bool Success status
     */
    public static function delete(string $code): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'UPDATE ' . self::TABLE_NAME . " SET deleted = 'true' WHERE code = :code";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':code', $code);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to delete Stripe payment: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get pending payments.
     *
     * @return array Array of pending payments
     */
    public static function getPendingPayments(): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . " WHERE status = 'processing' AND deleted = 'false' AND locked = 'false'";
            $stmt = $con->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get pending Stripe payments: ' . $e->getMessage());

            return [];
        }
    }

    public static function getPendingPaymentsUser(string $uuid): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . " WHERE uuid = :uuid AND status = 'processing' AND deleted = 'false' AND locked = 'false'";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':uuid', $uuid);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get pending Stripe payments for user: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Check if a payment exists.
     *
     * @param string $code Payment code
     *
     * @return bool Whether payment exists
     */
    public static function exists(string $code): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . " WHERE code = :code AND deleted = 'false'";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();

            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if Stripe payment exists: ' . $e->getMessage());

            return false;
        }
    }

    public static function isPending(string $code): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . " WHERE code = :code AND status = 'processing' AND deleted = 'false'";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':code', $code);
            $stmt->execute();

            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if Stripe payment is pending: ' . $e->getMessage());

            return false;
        }
    }

    public static function cancelLastTransactionForUser(string $uuid): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'UPDATE ' . self::TABLE_NAME . " SET status = 'failed' WHERE user = :uuid AND status = 'processing' AND deleted = 'false' ORDER BY date DESC LIMIT 1";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':uuid', $uuid);

            return $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to cancel last Stripe payment for user: ' . $e->getMessage());

            return false;
        }
    }
}
