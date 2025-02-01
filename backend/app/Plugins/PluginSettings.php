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

namespace MythicalClient\Plugins;

use MythicalClient\Chat\Database;

class PluginSettings extends PluginDB
{
    /**
     * Set a setting in the database.
     *
     * @param string $identifier The identifier of the plugin
     * @param string $key The key of the setting
     * @param array $settings The settings to set
     */
    public static function setSettings(string $identifier, string $key, array $settings): void
    {
        $conn = Database::getPdoConnection();

        // Check if setting already exists
        $stmt = $conn->prepare('
			SELECT id 
			FROM mythicalclient_addons_settings 
			WHERE identifier = :identifier 
			AND `key` = :key
			LIMIT 1
		');

        $stmt->execute([
            ':identifier' => $identifier,
            ':key' => $key,
        ]);

        $exists = $stmt->fetch(\PDO::FETCH_ASSOC);
        $value = $settings['value'] ?? '';

        if ($exists) {
            $stmt = $conn->prepare("
				UPDATE mythicalclient_addons_settings 
				SET value = :value, 
					date = CURRENT_TIMESTAMP,
					deleted = 'false'
				WHERE identifier = :identifier 
				AND `key` = :key
			");
        } else {
            $stmt = $conn->prepare("
				INSERT INTO mythicalclient_addons_settings 
				(identifier, `key`, value, locked, deleted, date) 
				VALUES (:identifier, :key, :value, 'false', 'false', CURRENT_TIMESTAMP)
			");
        }

        $stmt->execute([
            ':identifier' => $identifier,
            ':key' => $key,
            ':value' => $value,
        ]);
    }

    /**
     * Delete a setting from the database.
     *
     * @param string $identifier The identifier of the plugin
     * @param string $key The key of the setting
     */
    public static function deleteSettings(string $identifier, string $key): void
    {
        $conn = Database::getPdoConnection();

        $stmt = $conn->prepare("
			UPDATE mythicalclient_addons_settings 
			SET deleted = 'true',
				date = CURRENT_TIMESTAMP
			WHERE identifier = :identifier 
			AND `key` = :key
		");

        $stmt->execute([
            ':identifier' => $identifier,
            ':key' => $key,
        ]);
    }

    /**
     * Get a setting from the database.
     *
     * @param string $identifier The identifier of the plugin
     * @param string $key The key of the setting
     *
     * @return string The value of the setting
     */
    public static function getSetting(string $identifier, string $key): string
    {
        $conn = Database::getPdoConnection();

        $stmt = $conn->prepare("
			SELECT value 
			FROM mythicalclient_addons_settings 
			WHERE identifier = :identifier 
			AND `key` = :key 
			AND deleted = 'false'
			LIMIT 1
		");

        $stmt->execute([
            ':identifier' => $identifier,
            ':key' => $key,
        ]);

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result['value'] : '';
    }
}
