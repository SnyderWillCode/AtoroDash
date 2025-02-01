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

namespace MythicalClient\Chat\Tickets;

use MythicalClient\Chat\Database;

class Messages extends Database
{
    public const TABLE_NAME = 'mythicalclient_tickets_messages';

    /**
     * Retrieves all messages for a given ticket ID.
     *
     * @param int $ticketId The ID of the ticket to get messages for
     *
     * @return array Array of messages or empty array if none found/error occurs
     */
    public static function getMessagesByTicketId(int $ticketId): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE ticket = :ticket_id AND deleted = \'false\' ORDER BY id DESC';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('ticket_id', $ticketId, \PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$result) {
                return [];
            }

            return $result;
        } catch (\Exception $e) {
            self::db_Error('Failed to get messages by ticket id: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Creates a new message for a ticket.
     *
     * @param int $ticketId The ID of the ticket to create message for
     * @param string $message The message content
     * @param string $sender The sender of the message
     */
    public static function createMessage(int $ticketId, string $message, string $sender): void
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSERT INTO ' . self::TABLE_NAME . ' (ticket, user, message) VALUES (:ticket_id, :sender, :message)';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('ticket_id', $ticketId, \PDO::PARAM_INT);
            $stmt->bindParam('message', $message, \PDO::PARAM_STR);
            $stmt->bindParam('sender', $sender, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Exception $e) {
            self::db_Error('Failed to create message: ' . $e->getMessage());
        }
    }

    /**
     * Deletes a message by its ID.
     *
     * @param int $messageId The ID of the message to delete
     */
    public static function deleteMessage(int $messageId): void
    {
        try {
            $con = self::getPdoConnection();
            if (self::exists($messageId)) {
                $sql = 'UPDATE ' . self::TABLE_NAME . ' SET deleted = true WHERE id = :message_id';
                $stmt = $con->prepare($sql);
                $stmt->bindParam('message_id', $messageId, \PDO::PARAM_INT);
                $stmt->execute();
            } else {
                return;
            }
        } catch (\Exception $e) {
            self::db_Error('Failed to delete message: ' . $e->getMessage());
        }
    }

    /**
     * Checks if a message exists by its ID.
     *
     * @param int $messageId The ID of the message to check
     *
     * @return bool Returns true if message exists, false otherwise
     */
    public static function exists(int $messageId): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE id = :message_id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('message_id', $messageId, \PDO::PARAM_INT);
            $stmt->execute();

            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if message exists: ' . $e->getMessage());

            return false;
        }
    }
}
