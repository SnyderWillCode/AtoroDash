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

use Symfony\Component\Yaml\Yaml;

class PluginHelper
{
    /**
     * Get the plugins directory.
     *
     * @return string The plugins directory
     */
    public static function getPluginsDir(): string
    {
        try {
            $pluginsDir = APP_ADDONS_DIR;
            if (is_dir($pluginsDir) && is_readable($pluginsDir) && is_writable($pluginsDir)) {
                return $pluginsDir;
            }
            return '';
        } catch (\Exception) {
            return '';
        }
    }

    /**
     * Get the plugin config.
     *
     * @param string $identifier The plugin identifier
     *
     * @return array The plugin config
     */
    public static function getPluginConfig(string $identifier): array
    {
        $app = \MythicalClient\App::getInstance(true);
        try {
            $app->getLogger()->debug('Getting plugin config for: ' . $identifier . '');
            if (file_exists(self::getPluginsDir() . '/' . $identifier . '/conf.yml')) {
                $app->getLogger()->debug('Got plugin config for: ' . $identifier . '');

                return Yaml::parseFile(self::getPluginsDir() . '/' . $identifier . '/conf.yml');
            }
            $app->getLogger()->debug('Failed to get plugin config for: ' . $identifier . '');

            return [];
        } catch (\Exception) {
            $app->getLogger()->warning('Failed to get plugin config for: ' . self::getPluginConfig($identifier) . '');

            return [];
        }
    }
}
