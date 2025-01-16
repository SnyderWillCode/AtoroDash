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

class AnnouncementsTags extends Database
{
    public const TABLE_NAME = 'mythicalclient_announcements_tags';

    /**
     * Create a new announcement tag.
     */
    public static function create(int $announcementId, string $tag): void
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'INSERT INTO ' . self::TABLE_NAME . ' (announcements, tag, date) VALUES (:announcementId, :tag, NOW())';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':announcementId', $announcementId);
            $stmt->bindParam(':tag', $tag);
            $stmt->execute();
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to create announcement tag: ' . $e->getMessage());
        }
    }

    /**
     * Delete an announcement tag.
     */
    public static function delete(int $id): void
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to delete announcement tag: ' . $e->getMessage());
        }
    }

    /**
     * Get all announcement tags.
     */
    public static function getAll(int $id): array
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT * FROM ' . self::TABLE_NAME . ' WHERE announcements = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to get all announcement tags: ' . $e->getMessage());

            return [];
        }
    }

    /**
     * Check if an announcement tag exists.
     *
     * @param int $id The id of the announcement tag
     *
     * @return bool True if the announcement tag exists, false otherwise
     */
    public static function exists(int $id): bool
    {
        try {
            $con = self::getPdoConnection();
            $sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ' WHERE id = :id';
            $stmt = $con->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to check if announcement tag exists: ' . $e->getMessage());

            return false;
        }
    }
}
