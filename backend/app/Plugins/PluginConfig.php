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
