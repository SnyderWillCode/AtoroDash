<?php

namespace MythicalClient\Chat\Tickets;

use MythicalClient\Chat\Database;

class Attachments extends Database
{
    public const TABLE_NAME = 'mythicalclient_tickets_attachments';

    /**
     * Adds a new attachment to a ticket
     * 
     * @param int $ticketId The ID of the ticket
     * @param string $filename The name of the attachment file
     * 
     * @return void
     */
    public static function addAttachment(int $ticketId, string $filename): void
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSERT INTO ' . self::TABLE_NAME . ' (ticket, file) VALUES (:ticket_id, :filename)';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('ticket_id', $ticketId, \PDO::PARAM_INT);
            $stmt->bindParam('filename', $filename, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Exception $ex) {
            self::db_Error('Error adding attachment: ' . $ex->getMessage());
        }
    }

	/**
	 * Retrieves all attachments for a given ticket ID.
	 *
	 * @param int $ticketId The ID of the ticket to get attachments for
	 *
	 * @return array Array of attachments or empty array if none found/error occurs
	 */
	public static function getAttachmentsByTicketId(int $ticketId): array
	{
		try {
			$con = self::getPdoConnection();
			$sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE ticket = :ticket_id AND deleted = \'false\'';
			$stmt = $con->prepare($sql);
			$stmt->bindParam('ticket_id', $ticketId, \PDO::PARAM_INT);
			$stmt->execute();
			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $ex) {
			self::db_Error('Error getting attachments: ' . $ex->getMessage());
			return [];
		}
	}

	/**
	 * Deletes an attachment from the database
	 * 
	 * @param int $attachmentId The ID of the attachment to delete
	 * 
	 * @return void
	 */
	public static function deleteAttachment(int $attachmentId): void
	{
		try {
			$con = self::getPdoConnection();
			$sql = 'UPDATE ' . self::TABLE_NAME . ' SET deleted = \'true\' WHERE id = :attachment_id';
			$stmt = $con->prepare($sql);
			$stmt->bindParam('attachment_id', $attachmentId, \PDO::PARAM_INT);
			$stmt->execute();
		} catch (\Exception $ex) {
			self::db_Error('Error deleting attachment: ' . $ex->getMessage());
		}
	}
}
