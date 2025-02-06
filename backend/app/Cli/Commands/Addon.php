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
        global $pluginManager;
        define('APP_ADDONS_DIR', __DIR__ . '/../../../storage/addons');
        define('APP_DEBUG', false);
        $pluginManager->loadKernel();

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
                    $addons = $pluginManager->getLoadedMemoryPlugins();

                    $types = [
                        PluginTypes::$event,
                        PluginTypes::$provider,
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
