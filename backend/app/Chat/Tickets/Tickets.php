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
        string $priority,
    ): int {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSERT INTO ' . self::TABLE_NAME . ' (user, department, priority, title, description' . ($service === null ? '' : ', service') . ') VALUES (:uuid, :department, :priority, :title, :description' . ($service === null ? '' : ', :service') . ')';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('uuid', $uuid, \PDO::PARAM_STR);
            $stmt->bindParam('department', $department, \PDO::PARAM_INT);
            $stmt->bindParam('priority', $priority, \PDO::PARAM_STR);
            if ($service !== null) {
                $stmt->bindParam('service', $service, \PDO::PARAM_INT);
            }
            $stmt->bindParam('title', $title, \PDO::PARAM_STR);
            $stmt->bindParam('description', $description, \PDO::PARAM_STR);
            $stmt->execute();

            return (int) $con->lastInsertId();
        } catch (\Exception $e) {
            self::db_Error('Failed to create ticket: ' . $e->getMessage());

            return 0;
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
            self::db_Error('Failed to delete ticket: ' . $e->getMessage());
        }

    }

    public static function exists(int $id): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            self::db_Error('Failed to check if ticket exists: ' . $e->getMessage());

            return false;
        }
    }

    public static function getAllTickets(int $limit = 150): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' ORDER BY id DESC LIMIT :limit';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get all tickets: ' . $e->getMessage());

            return [];
        }
    }

    public static function getAllTicketsByUser(string $uuid, int $limit = 150): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE user = :uuid ORDER BY id DESC LIMIT ' . $limit;
            $stmt = $con->prepare($sql);
            $stmt->bindParam('uuid', $uuid, \PDO::PARAM_STR);
            $stmt->execute();

            $tickets = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($tickets as $key => $ticket) {
                $tickets[$key]['department_id'] = $ticket['department'];
                $tickets[$key]['department'] = Departments::get((int) $ticket['department']);

                if (empty($tickets[$key]['department'])) {
                    $tickets[$key]['department'] = [
                        'id' => 0,
                        'name' => 'Deleted Department',
                        'description' => 'This department has been deleted.',
                        'time_open' => '08:30',
                        'time_close' => '17:30',
                        'enabled' => 'true',
                        'deleted' => 'false',
                        'locked' => 'false',
                        'date' => '2024-12-25 22:25:09',
                    ];
                }
            }

            return $tickets;
        } catch (\Exception $e) {
            self::db_Error('Failed to get all tickets by user: ' . $e->getMessage());

            return [];
        }
    }

    public static function getTicket(int $id): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam('id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            self::db_Error('Failed to get ticket: ' . $e->getMessage());

            return [];
        }
    }
}
