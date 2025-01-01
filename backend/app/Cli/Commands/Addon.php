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

namespace MythicalClient\Cli\Commands;

use MythicalClient\Cli\App;
use MythicalClient\Cli\CommandBuilder;
use MythicalClient\Plugins\PluginTypes;

class Addon extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {

        /**
         * Initialize the plugin manager.
         */
        require __DIR__ . '/../../../boot/kernel.php';
        define('APP_ADDONS_DIR', __DIR__ . '/../../../storage/addons');
        define('APP_DEBUG', false);
        \MythicalClient\Plugins\PluginManager::loadKernel();

        if (count($args) > 0) {
            switch ($args[1]) {
                case 'install':
                    // Install an addon.
                    break;
                case 'uninstall':
                    // Uninstall an addon.
                    break;
                case 'list':
                    self::getInstance()->send('&5&lMythical&d&lDash &7- &d&lAddons');
                    self::getInstance()->send('');
                    $addons = \MythicalClient\Plugins\PluginManager::getLoadedMemoryPlugins();

                    $types = [
                        PluginTypes::$event,
                        PluginTypes::$provider,
                        PluginTypes::$gateway,
                        PluginTypes::$components,
                    ];

                    foreach ($types as $type) {
                        if ($type == PluginTypes::$event) {
                            self::getInstance()->send('&5&lEvents Plugins:');
                            self::getInstance()->send('&f(Typical plugins that listen to events)');
                            self::getInstance()->send('');
                        } elseif ($type == PluginTypes::$provider) {
                            self::getInstance()->send('&5&lProviders Plugins:');
                            self::getInstance()->send('&f(Typical plugins that process purchases and create services!)');
                            self::getInstance()->send('');
                        } elseif ($type == PluginTypes::$gateway) {
                            self::getInstance()->send('&5&lGateways Plugins:');
                            self::getInstance()->send('&f(Typical plugins that handle payment gateways!)');
                            self::getInstance()->send('');
                        } elseif ($type == PluginTypes::$components) {
                            self::getInstance()->send('&5&lComponents Plugins:');
                            self::getInstance()->send('&f(Typical plugins that add new components to the frontend!)');
                            self::getInstance()->send('');
                        }
                        foreach ($addons as $plugin) {
                            $addonConfig = \MythicalClient\Plugins\PluginConfig::getConfig($plugin);
                            $name = $addonConfig['plugin']['name'];
                            $version = $addonConfig['plugin']['version'];
                            $description = $addonConfig['plugin']['description'];
                            if ($addonConfig['plugin']['type'] == $type) {
                                self::getInstance()->send("&7 - &b{$name} &8> &d{$version} &8> &7{$description}");
                                self::getInstance()->send('');
                            }
                        }
                    }
                    self::getInstance()->send('');
                    break;
                case 'update':
                    // Update an addon.
                    break;
                default:
                    self::getInstance()->send('&cInvalid subcommand!');
                    break;
            }
        } else {
            self::getInstance()->send('&cPlease provide a subcommand!');
        }
    }

    public static function getDescription(): string
    {
        return 'Manage your addons form the command line.';
    }

    public static function getSubCommands(): array
    {
        return [
            'install' => 'Install an addon.',
            'uninstall ' => 'Uninstall an addon.',
            'list' => 'List all installed addons.',
        ];
    }
}
