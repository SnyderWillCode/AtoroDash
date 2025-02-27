<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
{
	public static function getRoutes(): array
	{
		return [];
	}
}

 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
 */

namespace MythicalClient\Plugins;

use MythicalClient\App;
use MythicalClient\Plugins\Events\PluginEventProcessor;

class PluginManager
{
    private array $plugins = [];
	private array $deployable_plugins = [];
    public function loadKernel(): void
    {
		global $eventManager;
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
                                if (!in_array($plugin, $this->plugins)) {
                                    if (PluginDependencies::checkDependencies($config)) {
                                        $instance->getLogger()->debug('Plugin ' . $plugin . ' was loaded in the memory!');
                                        $this->plugins[] = $plugin;
										if ($config['plugin']['can_deploy']) {
											$can_deploy = 'true';
											$this->deployable_plugins[] = $plugin;
										} else {
											$can_deploy = 'false';
										}
                                        PluginDB::registerPlugin($config['plugin']['identifier'], $config['plugin']['name'], $can_deploy);
										PluginEventProcessor::processEvent($config['plugin']['identifier'], $eventManager);
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
    public function getLoadedMemoryPlugins(): array
    {
        $instance = App::getInstance(true);
        try {
            return $this->plugins;
        } catch (\Exception $e) {
            $instance->getLogger()->error('Failed to get plugin names: ' . $e->getMessage());

            return [];
        }
    }

	public function getEventManager() : PluginEvents {
		return new PluginEvents();
	}

    public function getLoadedPlugins(): array
    {
        return $this->plugins;
    }

	public function getLoadedDeployablePlugins(): array {
		return $this->deployable_plugins;
	}

}
