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

namespace MythicalClient\Chat;

use MythicalClient\App;

class Tickets extends Database
{
    public const TABLE_NAME = 'mythicalclient_tickets';
    public const TABLE_NAME_ATTACHMENTS = 'mythicalclient_tickets_attachments';

    public static function create(
        string $uuid,
        int $department,
        ?int $service,
        string $title,
        string $description,
    ): void {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSET INTO ' . self::TABLE_NAME . ' (uuid, department, service, title, description) VALUES (:uuid, :department, :service, :title, :description)';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('uuid', $uuid, \PDO::PARAM_STR);
            $stmt->bindParam('department', $department, \PDO::PARAM_INT);
            $stmt->bindParam('service', $service, \PDO::PARAM_INT);
            $stmt->bindParam('title', $title, \PDO::PARAM_STR);
            $stmt->bindParam('description', $description, \PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Exception $e) {
			self::db_Error("Failed to create ticket: " . $e->getMessage());
		}
	}

	public static function update(
		int $id,
		string $uuid,
		int $department,
		?int $service,
		string $title,
		string $description,
	): void {
		try {
			$con = self::getPdoConnection();
			$sql = 'UPDATE ' . self::TABLE_NAME . ' SET uuid = :uuid, department = :department, service = :service, title = :title, description = :description WHERE id = :id';
			$stmt = $con->prepare($sql);
			$stmt->bindParam('id', $id, \PDO::PARAM_INT);
			$stmt->bindParam('uuid', $uuid, \PDO::PARAM_STR);
			$stmt->bindParam('department', $department, \PDO::PARAM_INT);
			$stmt->bindParam('service', $service, \PDO::PARAM_INT);
			$stmt->bindParam('title', $title, \PDO::PARAM_STR);
			$stmt->bindParam('description', $description, \PDO::PARAM_STR);
			$stmt->execute();
		} catch (\Exception $e) {
			self::db_Error("Failed to update ticket: " . $e->getMessage());
		}
    }

	public static function delete(int $id): void
	{
		try {
			$con = self::getPdoConnection();
			$sql = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE id = :id';
			$stmt = $con->prepare($sql);
			$stmt->bindParam('id', $id, \PDO::PARAM_INT);
			$stmt->execute();
		} catch (\Exception $e) {
			self::db_Error("Failed to delete ticket: " . $e->getMessage());
		}
	}
}
