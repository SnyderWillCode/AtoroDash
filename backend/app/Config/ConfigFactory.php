<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * 2021-2025 (c) All rights reserved
 *
 * MIT License
 *
 * (c) MythicalSystems - All rights reserved
 * (c) NaysKutzu - All rights reserved
 * (c) Cassian Gherman- All rights reserved
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace MythicalClient\Config;

use MythicalSystems\Utils\XChaCha20;

class ConfigFactory
{
    private \PDO $db;
    private string $encryption_key;
    private array $cache = [];

    private string $table_name = 'mythicalclient_settings';

    public function __construct(\PDO $db)
    {
        try {
            $this->db = $db;
        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to the MYSQL Server! ', $e->getMessage());
        }
        $this->encryption_key = $_ENV['DATABASE_ENCRYPTION_KEY'];
    }

    /**
     * Get a setting from the database.
     *
     * @param string $name The name of the setting
     * @param mixed $fallback The fallback value if the setting is not found
     *
     * @return string|null The value of the setting
     */
    public function getSetting(string $name, ?string $fallback): ?string
    {
        // Check if the setting is in the cache
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            $result['value'] = XChaCha20::decrypt($result['value'], $this->encryption_key);
            // Store the result in the cache
            $this->cache[$name] = $result['value'];

            return $result['value'];
        }

        return $fallback ?? null;
    }

    /**
     * Set a setting in the database.
     *
     * @param string $name The name of the setting
     * @param string $value The value of the setting
     *
     * @throws \Exception If the setting already exists
     *
     * @return bool True if the setting was set successfully
     */
    public function setSetting(string $name, string $value): bool
    {
        $existingSetting = $this->getSetting($name, null);
        $encrypted_value = XChaCha20::encrypt($value, $this->encryption_key);
        if ($existingSetting) {
            $stmt = $this->db->prepare("UPDATE {$this->table_name} SET value = :value, date = NOW() WHERE name = :name");
        } else {
            $stmt = $this->db->prepare("INSERT INTO {$this->table_name} (name, value, date) VALUES (:name, :value, NOW())");
        }
        $result = $stmt->execute(['name' => $name, 'value' => $encrypted_value]);
        if ($result) {
            // Update the cache
            $this->cache[$name] = $value;
        }

        return $result;
    }
}
