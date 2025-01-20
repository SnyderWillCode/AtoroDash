<?php

namespace MythicalClient\Chat;

use MythicalClient\App;
use PDO;

class Tickets extends Database
{
	public const TABLE_NAME = "mythicalclient_tickets";
	public const TABLE_NAME_ATTACHMENTS = "mythicalclient_tickets_attachments";

	public static function create(
		string $uuid,
		int $department,
		int|null $service,
		string $title,
		string $description
	): void {
		try {
			$con = self::getPdoConnection();
			$sql = 'INSET INTO ' . self::TABLE_NAME . ' (uuid, department, service, title, description) VALUES (:uuid, :department, :service, :title, :description)';
			$stmt = $con->prepare($sql);
			$stmt->bindParam('uuid', $uuid, PDO::PARAM_STR);
			$stmt->bindParam('department', $department, PDO::PARAM_INT);
			$stmt->bindParam('service', $service, PDO::PARAM_INT);
			$stmt->bindParam('title', $title, PDO::PARAM_STR);
			$stmt->bindParam('description', $description, PDO::PARAM_STR);
			$stmt->execute();
		} catch (\Exception $e) {
			App::getInstance(true)->getLogger()->error('Failed to create ticket: ' . $e->getMessage());
		}
	}
}