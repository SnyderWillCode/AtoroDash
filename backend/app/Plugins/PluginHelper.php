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

use Symfony\Component\Yaml\Yaml;

class PluginHelper extends PluginTypes
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
