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

namespace MythicalClient;

use Router\Router as rt;
use MythicalClient\Chat\Database;
use MythicalSystems\Utils\XChaCha20;
use MythicalClient\Hooks\MythicalAPP;
use MythicalClient\Config\ConfigFactory;
use MythicalClient\Logger\LoggerFactory;

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
         * Redis.
         */
        $redis = new FastChat\Redis();
        if ($redis->testConnection() == false) {
            self::init();
            self::InternalServerError('Failed to connect to Redis', null);
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

    public function generateCode(): string
    {
        $code = base64_encode(random_bytes(64));
        $code = str_replace('=', '', $code);
        $code = str_replace('+', '', $code);
        $code = str_replace('/', '', $code);

        return $code;
    }
}
