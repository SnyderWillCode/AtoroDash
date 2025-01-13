<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * ### Preamble
 * This license governs the use, modification, and distribution of the software known as MythicalClient or MythicalDash ("the Software"). By using the Software, you agree to the terms outlined in this document. These terms aim to protect the Software’s integrity, ensure fair use, and establish guidelines for authorized distribution, modification, and commercial use.
 *
 * For any inquiries, abuse reports, or violation notices, contact us at [abuse@mythicalsystems.xyz](mailto:abuse@mythicalsystems.xyz).
 *
 * ## 1. Grant of License
 *
 * ### 1.1. Usage Rights
 * - You are granted a non-exclusive, revocable license to use the Software, provided you comply with the terms herein.
 * - The Software must be linked to an active account on our public platform, MythicalSystems.
 *
 * ### 1.2. Modification Rights
 * - You may modify the Software only for personal use and must not distribute modified versions unless explicitly approved by MythicalSystems or Cassian Gherman.
 *
 * ### 1.3. Redistribution and Commercial Use
 * - Redistribution of the Software, whether modified or unmodified, is strictly prohibited unless explicitly authorized in writing by MythicalSystems or Cassian Gherman.
 * - Selling the Software or its derivatives is only permitted on authorized marketplaces specified by MythicalSystems.
 * - Unauthorized leaking, sharing, or redistribution of the Software or its components is illegal and subject to legal action.
 *
 * ### 1.4. Third-Party Addons and Plugins
 * - The creation, sale, and distribution of third-party addons or plugins for the Software are permitted, provided they comply with this license.
 * - All third-party addons or plugins must not attempt to bypass, modify, or interfere with the core functionality or licensing requirements of the Software.
 *
 * ## 2. Restrictions
 *
 * ### 2.1. Account Requirement
 * - The Software requires an active account on MythicalSystems. Attempts to modify, bypass, or remove this requirement are strictly prohibited.
 *
 * ### 2.2. Unauthorized Use
 * - Use of the Software to perform unauthorized actions, including but not limited to exploiting vulnerabilities, bypassing authentication, or reverse engineering, is prohibited.
 *
 * ### 2.3. Leaking and Distribution
 * - Any unauthorized leaking, sharing, or distribution of the Software is a direct violation of this license. Legal action will be taken against violators.
 * - Leaked or pirated copies of the Software are considered illegal, and users found utilizing such versions will face immediate termination of access and potential legal consequences.
 *
 * ### 2.4. Modification of Terms
 * - The terms and conditions of this license may not be modified, replaced, or overridden in any distributed version of the Software.
 *
 * ## 3. Attribution and Copyright
 *
 * ### 3.1. Attribution
 * - You must retain all copyright notices, attributions, and references to MythicalSystems and Cassian Gherman in all copies, derivatives, or distributions of the Software.
 *
 * ### 3.2. Copyright Invariance
 * - Copyright notices must remain intact and unaltered in all versions of the Software, including modified versions.
 *
 * ## 4. Legal and Liability Terms
 *
 * ### 4.1. Disclaimer of Liability
 * - The Software is provided "as is," without any warranty, express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, or non-infringement.
 * - MythicalSystems and Cassian Gherman shall not be held liable for any damages arising from the use, misuse, or inability to use the Software, including but not limited to:
 * 	- Loss of data, profits, or revenue.
 * 	- Security vulnerabilities such as SQL injection, zero-day exploits, or other potential risks.
 * 	- System failures, downtime, or disruptions.
 *
 * ### 4.2. Enforcement
 * - Violations of this license will result in immediate termination of access to the Software and may involve legal action.
 * - MythicalSystems reserves the right to suspend or terminate access to any user, client, or hosting provider without prior notice.
 *
 * ### 4.3. No Guarantees
 * - MythicalSystems does not guarantee uninterrupted or error-free operation of the Software.
 *
 * ## 5. Privacy and Data Sharing
 *
 * ### 5.1. Public Information
 * - Some user information may be shared with third parties or made publicly visible in accordance with our Privacy Policy and Terms of Service. For more details, please visit:
 * 	- [Privacy Policy](https://www.mythical.systems/privacy)
 * 	- [Terms of Service](https://www.mythical.systems/terms)
 *
 * ### 5.2. Data Collection
 * - The Software may collect and transmit anonymized usage data to improve performance and functionality.
 *
 * ## 6. Governing Law
 *
 * ### 6.1. Jurisdiction
 * - This license shall be governed and construed in accordance with the laws of Austria.
 *
 * ### 6.2. Dispute Resolution
 * - All disputes arising under or in connection with this license shall be subject to the exclusive jurisdiction of the courts in Austria.
 *
 * ## 7. Termination
 *
 * ### 7.1. Violation of Terms
 * - MythicalSystems reserves the right to terminate access to the Software for any user found in violation of this license.
 *
 * ### 7.2. Immediate Termination
 * - Termination may occur immediately and without prior notice.
 *
 * ## 8. Contact Information
 * For abuse reports, legal inquiries, or support, contact [abuse@mythicalsystems.xyz](mailto:abuse@mythicalsystems.xyz).
 *
 * ## 9. Acceptance
 * By using, modifying, or distributing the Software, you agree to the terms outlined in this license.
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
