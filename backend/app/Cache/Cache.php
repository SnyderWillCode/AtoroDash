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

namespace MythicalClient\Cache;

/**
 * Class Cache.
 *
 * Provides simple file-based caching functionality.
 */
class Cache
{
    protected static $cacheDir = APP_CACHE_DIR . '/other';

    /**
     * Stores a value in the cache with a specified expiration time.
     *
     * @param string $key the cache key used to reference the data
     * @param mixed $value the data to be cached
     * @param int $minutes number of minutes before the data expires
     *
     * @return void
     */
    public static function put($key, $value, $minutes = 60)
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }
        $filename = self::$cacheDir . '/' . md5($key);
        $data = [
            'expires' => time() + ($minutes * 60),
            'value' => $value,
        ];
        file_put_contents($filename, serialize($data));
    }

    /**
     * Retrieves a value from the cache by its key.
     *
     * @param string $key the cache key for the stored data
     *
     * @return mixed|null returns the stored data or null if not found or expired
     */
    public static function get($key)
    {
        $filename = self::$cacheDir . '/' . md5($key);
        if (!file_exists($filename)) {
            return null;
        }
        $data = unserialize(file_get_contents($filename));
        if (time() > $data['expires']) {
            unlink($filename);

            return null;
        }

        return $data['value'];
    }

    /**
     * Removes a cached entry by its key.
     *
     * @param string $key the cache key to remove
     *
     * @return void
     */
    public static function forget($key)
    {
        $filename = self::$cacheDir . '/' . md5($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * Clears all entries in the cache directory.
     *
     * @return void
     */
    public static function clear()
    {
        $files = glob(self::$cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Checks if a valid cached entry exists for the specified key.
     *
     * @param string $key the cache key to check
     *
     * @return bool true if a valid cached entry exists, otherwise false
     */
    public static function exists($key)
    {
        $filename = self::$cacheDir . '/' . md5($key);
        if (!is_file($filename)) {
            return false;
        }
        $data = @unserialize(file_get_contents($filename));
        if (!is_array($data) || !isset($data['expires'])) {
            return false;
        }

        return time() <= $data['expires'];
    }

    /**
     * Stores JSON data in the cache with a specified expiration time.
     *
     * @param string $key the cache key used to reference the JSON data
     * @param mixed $json the JSON data to be cached
     * @param int $minutes number of minutes before the data expires
     *
     * @return void
     */
    public static function putJson($key, $json, $minutes = 60)
    {
        self::put($key, $json, $minutes);
    }

    /**
     * Retrieves a previously stored JSON data by its key.
     *
     * @param string $key the cache key for the JSON data
     *
     * @return mixed|null returns the JSON data or null if not found or expired
     */
    public static function getJson($key)
    {
        return self::get($key);
    }
}
