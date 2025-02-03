<?php

namespace MythicalClient\Chat\Orders;

use PDO;

class OrdersInvoices extends \MythicalClient\Chat\Database
{
    private const TABLE_NAME = 'mythicalclient_invoices';

    /**
     * Create a new invoice for an order
     *
     * @param string $user User UUID
     * @param int $orderId Order ID
     * @param string $paymentGateway Payment gateway name (default: MythicalPay)
     * @param ?string $dueDate Optional due date for the invoice
     * @return array|false Returns the created invoice or false on failure
     */
    public static function create(string $user, int $orderId, string $paymentGateway = 'MythicalPay', ?string $dueDate = null): array|false
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' 
                (user, `order`, payment_gateway, due_date) 
                VALUES (:user, :order, :payment_gateway, :due_date)');
            
            $success = $query->execute([
                'user' => $user,
                'order' => $orderId,
                'payment_gateway' => $paymentGateway,
                'due_date' => $dueDate
            ]);

            if ($success) {
                return self::getInvoice($conn->lastInsertId());
            }

            return false;
        } catch (\Exception $e) {
            self::db_Error('Failed to create invoice: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get an invoice by ID
     *
     * @param int $id Invoice ID
     * @return array|false Returns the invoice or false if not found
     */
    public static function getInvoice(int $id): array|false
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = :id AND deleted = "false"');
            $query->execute(['id' => $id]);
            return $query->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get invoice: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all invoices for a user
     *
     * @param string $user User UUID
     * @return array Returns array of invoices
     */
    public static function getUserInvoices(string $user): array
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE user = :user AND deleted = "false" ORDER BY created_at DESC');
            $query->execute(['user' => $user]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get user invoices: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all invoices for an order
     *
     * @param int $orderId Order ID
     * @return array Returns array of invoices
     */
    public static function getOrderInvoices(int $orderId): array
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE `order` = :order AND deleted = "false" ORDER BY created_at DESC');
            $query->execute(['order' => $orderId]);
            return $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get order invoices: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update invoice status
     *
     * @param int $id Invoice ID
     * @param string $status New status ('cancelled', 'pending', 'paid', 'refunded')
     * @return bool Success status
     */
    public static function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, ['cancelled', 'pending', 'paid', 'refunded'])) {
            return false;
        }

        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET status = :status WHERE id = :id AND deleted = "false"');
            
            // Update the corresponding timestamp based on status
            $timestampField = match($status) {
                'cancelled' => 'cancelled_at',
                'paid' => 'paid_at',
                'refunded' => 'refunded_at',
                default => null
            };

            if ($timestampField) {
                $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' 
                    SET status = :status, ' . $timestampField . ' = CURRENT_TIMESTAMP 
                    WHERE id = :id AND deleted = "false"');
            }

            return $query->execute([
                'id' => $id,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to update invoice status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update payment gateway
     *
     * @param int $id Invoice ID
     * @param string $gateway New payment gateway
     * @return bool Success status
     */
    public static function updatePaymentGateway(int $id, string $gateway): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET payment_gateway = :gateway WHERE id = :id AND deleted = "false"');
            return $query->execute([
                'id' => $id,
                'gateway' => $gateway
            ]);
        } catch (\Exception $e) {
            self::db_Error('Failed to update payment gateway: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete an invoice
     *
     * @param int $id Invoice ID
     * @return bool Success status
     */
    public static function delete(int $id): bool
    {
        try {
            $conn = self::getPdoConnection();
            $query = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET deleted = "true" WHERE id = :id');
            return $query->execute(['id' => $id]);
        } catch (\Exception $e) {
            self::db_Error('Failed to delete invoice: ' . $e->getMessage());
            return false;
        }
    }
} 