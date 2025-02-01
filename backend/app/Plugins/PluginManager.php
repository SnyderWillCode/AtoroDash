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

class PluginManager
{
    private static $plugins = [];

    public static function loadKernel(): void
    {
        try {
            $instance = App::getInstance(true);
            $plugins = PluginHelper::getPluginsDir();
            $plugins = scandir($plugins);
            foreach ($plugins as $plugin) {
                if ($plugin != '.' && $plugin != '' && $plugin != '..' && $plugin != '.gitignore' && $plugin != '.gitkeep') {
                    if (PluginConfig::isValidIdentifier($plugin)) {
                        $config = PluginHelper::getPluginConfig($plugin);
                        if (empty($config)) {
                            $instance->getLogger()->warning('Plugin config is empty for: ' . $plugin);
                        } else {
                            if (PluginConfig::isConfigValid($config)) {
                                if (!in_array($plugin, self::$plugins)) {
                                    if (PluginDependencies::checkDependencies($config)) {
                                        $instance->getLogger()->debug('Plugin ' . $plugin . ' was loaded in the memory!');
                                        self::$plugins[] = $plugin;
                                        if ($config['plugin']['type'] == 'event') {
                                            $instance->getLogger()->debug('Plugin ' . $plugin . ' is an event plugin!');
                                            PluginDB::registerPlugin($config['plugin']['identifier'], 1, $config['plugin']['name']);
                                        } elseif ($config['plugin']['type'] == 'provider') {
                                            $instance->getLogger()->debug('Plugin ' . $plugin . ' is a provider plugin!');
                                            PluginDB::registerPlugin($config['plugin']['identifier'], 2, $config['plugin']['name']);
                                        } elseif ($config['plugin']['type'] == 'components') {
                                            $instance->getLogger()->debug(message: 'Plugin ' . $plugin . ' is a components plugin!');
                                            PluginDB::registerPlugin($config['plugin']['identifier'], 4, $config['plugin']['name']);
                                        } else {
                                            $instance->getLogger()->warning('Plugin ' . $plugin . ' has an invalid type!');
                                        }
                                    } else {
                                        $instance->getLogger()->error('Plugin ' . $plugin . ' has unmet dependencies!');
                                    }
                                } else {
                                    $instance->getLogger()->error('Duplicated plugin identifier: ' . $plugin . '');
                                }
                            } else {
                                $instance->getLogger()->warning('Invalid config for plugin: ' . $plugin);
                            }
                        }
                    } else {
                        $instance->getLogger()->warning(message: 'Invalid plugin identifier: ' . $plugin);
                    }
                }
            }
        } catch (\Exception $e) {
            $instance->getLogger()->error('Failed to start plugins: ' . $e->getMessage());
        }
    }

    /**
     * Get the loaded memory plugins.
     *
     * @return array The loaded memory plugins
     */
    public static function getLoadedMemoryPlugins(): array
    {
        $instance = App::getInstance(true);
        try {
            return self::$plugins;
        } catch (\Exception $e) {
            $instance->getLogger()->error('Failed to get plugin names: ' . $e->getMessage());

            return [];
        }
    }
}
