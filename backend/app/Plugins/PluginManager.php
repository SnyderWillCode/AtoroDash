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
                                    $instance->getLogger()->debug('Plugin ' . $plugin . 'was loaded in the memory!');
                                    self::$plugins[] = $plugin;
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
