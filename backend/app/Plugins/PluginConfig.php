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

namespace MythicalClient\Plugins;

use MythicalClient\App;

class PluginConfig
{
    public static function getRequired(): array
    {
        return [
            'name' => 'string',
            'identifier' => 'string',
            'description' => 'string',
            'flags' => 'array',
            'version' => 'string',
            'target' => 'string',
            'author' => 'array',
            'icon' => 'string',
            'dependencies' => 'array',
            'type' => 'string',
        ];
    }

    /**
     * Check if the plugin config is valid.
     *
     * @param string $identifier The plugin identifier
     *
     * @return bool If the plugin config is valid
     */
    public static function isValidIdentifier(string $identifier): bool
    {
        App::getInstance(true)->getLogger()->debug('Checking identifier: ' . $identifier);
        if (empty($identifier)) {
            return false;
        }
        if (preg_match('/\s/', $identifier)) {
            return false;
        }
        if (preg_match('/^[a-zA-Z0-9_]+$/', $identifier) === 1) {
            App::getInstance(true)->getLogger()->debug('Plugin id is allowed: ' . $identifier);

            return true;
        }
        App::getInstance(true)->getLogger()->warning('Plugin id is not allowed: ' . $identifier);

        return false;

    }

    /**
     * Check if the plugin config is valid.
     *
     * @param array $config The plugin config
     *
     * @return bool If the plugin config is valid
     */
    public static function isConfigValid(array $config): bool
    {
        try {
            $app = App::getInstance(true);
            $app->getLogger()->debug('Processing config.. ' . $config['plugin']['name'] . '');

            $config_Requirements = self::getRequired();
            $config = $config['plugin'];

            if (!array_key_exists('identifier', $config)) {
                $app->getLogger()->warning('Missing identifier for plugin.');

                return false;
            }

            foreach ($config_Requirements as $key => $value) {
                if (!array_key_exists($key, $config)) {
                    $app->getLogger()->warning('Missing key for plugin: ' . $config['identifier'] . ' key: ' . $key);

                    return false;
                }

                if (gettype($config[$key]) !== $value) {
                    $app->getLogger()->warning('Invalid type for plugin: ' . $config['identifier'] . ' key: ' . $key);

                    return false;
                }
            }

            if (!PluginFlags::validFlags($config['flags'])) {
                $app->getLogger()->warning('Invalid flags for plugin: ' . $config['identifier']);

                return false;
            }

            if (self::isValidIdentifier($config['identifier']) == false) {
                $app->getLogger()->warning('Invalid identifier for plugin.');

                return false;
            }

            if (PluginTypes::isTypeAllowed($config['type']) == false) {
                $app->getLogger()->warning('Invalid type for plugin: ' . $config['identifier']);

                return false;
            }

            $app->getLogger()->debug('Done processing: ' . $config['name']);

            return true;

        } catch (\Exception $e) {
            $app->getLogger()->error('Error processing plugin config: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Get the plugin config.
     *
     * @param string $identifier The plugin identifier
     *
     * @return array The plugin config
     */
    public static function getConfig(string $identifier): array
    {
        return PluginHelper::getPluginConfig($identifier);
    }
}
