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
            if (empty($config)) {
                $app->getLogger()->warning('Plugin config is empty.');

                return false;
            }
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
