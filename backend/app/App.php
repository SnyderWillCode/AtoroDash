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

namespace MythicalClient;

use RateLimit\Rate;
use Router\Router as rt;
use RateLimit\RedisRateLimiter;
use MythicalClient\Chat\Database;
use MythicalSystems\Utils\XChaCha20;
use MythicalClient\Hooks\MythicalAPP;
use RateLimit\Exception\LimitExceeded;
use MythicalClient\Config\ConfigFactory;
use MythicalClient\Logger\LoggerFactory;
use MythicalClient\CloudFlare\CloudFlareRealIP;

class App extends MythicalAPP
{
    public static App $instance;
    public Database $db;

    public function __construct(bool $softBoot)
    {
        /**
         * Load the environment variables.
         */
        $this->loadEnv();

        /**
         * Instance.
         */
        self::$instance = $this;

        /**
         * Soft boot.
         *
         * If the soft boot is true, we do not want to initialize the database connection or the router.
         *
         * This is usefull for commands or other things that do not require the database connection.
         *
         * This is also a lite way to boot the application without initializing the database connection or the router!.
         */
        if ($softBoot) {
            return;
        }

        /**
         * Redis.
         */
        $redis = new FastChat\Redis();
        if ($redis->testConnection() == false) {
            define('REDIS_ENABLED', false);
        } else {
            define('REDIS_ENABLED', true);
        }

        // @phpstan-ignore-next-line
        $rateLimiter = new RedisRateLimiter(Rate::perMinute(RATE_LIMIT), new \Redis(), 'rate_limiting');
        try {
            $rateLimiter->limit(CloudFlareRealIP::getRealIP());
        } catch (LimitExceeded $e) {
            self::getLogger()->error('User: ' . $e->getMessage());
            self::init();
            self::ServiceUnavailable('You are being rate limited!', ['error_code' => 'RATE_LIMITED']);
        } catch (\Exception $e) {
            self::getLogger()->error('-----------------------------');
            self::getLogger()->error('REDIS SERVER IS DOWN');
            self::getLogger()->error('RATE LIMITING IS DISABLED');
            self::getLogger()->error('YOU SHOULD FIX THIS ASAP');
            self::getLogger()->error('NO SUPPORT WILL BE PROVIDED');
            self::getLogger()->error('-----------------------------');
        }

        /**
         * Database Connection.
         */
        try {
            $this->db = new Database($_ENV['DATABASE_HOST'], $_ENV['DATABASE_DATABASE'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
        } catch (\Exception $e) {
            self::init();
            self::InternalServerError($e->getMessage(), null);
        }
        /**
         * Email correction.
         */
        if ($this->getConfig()->getSetting('app_url', null) == null) {
            $this->getConfig()->setSetting('app_url', $_SERVER['HTTP_HOST']);
        }

        /**
         * Initialize the plugin manager.
         */
        Plugins\PluginManager::loadKernel();
        define('LOGGER', $this->getLogger());

        $router = new rt();
        $this->registerApiRoutes($router);

        try {
            $router->route();
        } catch (\Exception $e) {
            self::init();
            self::InternalServerError($e->getMessage(), null);
        }
    }

    /**
     * Register all api endpoints.
     *
     * @param rt $router The router instance
     */
    public function registerApiRoutes(rt $router): void
    {
        try {

            $routersDir = APP_ROUTES_DIR;
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($routersDir));
            $phpFiles = new \RegexIterator($iterator, '/\.php$/');
            foreach ($phpFiles as $phpFile) {
                try {
                    self::init();
                    include $phpFile->getPathname();
                } catch (\Exception $e) {
                    self::init();
                    self::InternalServerError($e->getMessage(), null);
                }
            }

            $router->add('/(.*)', function (): void {
                self::init();
                self::NotFound('The api route does not exist!', null);
            });
        } catch (\Exception $e) {
            self::init();
            self::InternalServerError($e->getMessage(), null);
        }
    }

    /**
     * Load the environment variables.
     */
    public function loadEnv(): void
    {
        try {
            if (file_exists(__DIR__ . '/../storage/.env')) {
                $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../storage/');
                $dotenv->load();

            } else {
                echo 'No .env file found';
                exit;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Update the value of an environment variable.
     *
     * @param string $key The key of the environment variable
     * @param string $value The value of the environment variable
     * @param bool $encode If the value should be encoded
     *
     * @return bool If the value was updated
     */
    public function updateEnvValue(string $key, string $value, bool $encode): bool
    {
        $envFile = __DIR__ . '/../storage/.env'; // Path to your .env file
        if (!file_exists($envFile)) {
            return false; // Return false if .env file doesn't exist
        }

        // Read the .env file into an array of lines
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $updated = false;
        foreach ($lines as &$line) {
            // Skip comments and lines that don't contain '='
            if (strpos(trim($line), '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            // Split the line into key and value
            [$envKey, $envValue] = explode('=', $line, 2);

            // Trim whitespace from the key
            if (trim($envKey) === $key) {
                // Update the value
                $line = "$key=\"$value\"";
                $updated = true;
            }
        }

        // If the key doesn't exist, add it
        if (!$updated) {
            $lines[] = "$key=$value";
        }

        // Write the updated lines back to the .env file
        return file_put_contents($envFile, implode(PHP_EOL, $lines)) !== false;
    }

    /**
     * Get the config factory.
     */
    public function getConfig(): ConfigFactory
    {
        if (isset(self::$instance->db)) {
            return new ConfigFactory(self::$instance->db->getPdo());
        }
        throw new \Exception('Database connection is not initialized.');
    }

    /**
     * Get the logger factory.
     */
    public function getLogger(): LoggerFactory
    {
        return new LoggerFactory(__DIR__ . '/../storage/logs/mythicalclient.log');
    }

    /**
     * Get the instance of the App class.
     */
    public static function getInstance(bool $softBoot): App
    {
        if (!isset(self::$instance)) {
            self::$instance = new self($softBoot);
        }

        return self::$instance;
    }

    /**
     * Encrypt the data.
     *
     * @param string $data The data to encrypt
     */
    public function encrypt(string $data): string
    {
        return XChaCha20::encrypt($data, $_ENV['DATABASE_ENCRYPTION_KEY'], true);
    }

    /**
     * Decrypt the data.
     *
     * @param string $data The data to decrypt
     *
     * @return void
     */
    public function decrypt(string $data): string
    {
        return XChaCha20::decrypt($data, $_ENV['DATABASE_ENCRYPTION_KEY'], true);
    }

    /**
     * Generate a random code.
     */
    public function generateCode(): string
    {
        $code = base64_encode(random_bytes(64));
        $code = str_replace('=', '', $code);
        $code = str_replace('+', '', $code);
        $code = str_replace('/', '', $code);

        return $code;
    }

    /**
     * Generate a random pin.
     */
    public function generatePin(): int
    {
        return random_int(100000, 999999);
    }
}
