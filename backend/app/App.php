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
		\Sentry\init([
			'dsn' => 'https://0e107fab7de1d5810a80fc591cb91a45@o4508434822791168.ingest.de.sentry.io/4508675813736528',
			// Specify a fixed sample rate
			'traces_sample_rate' => 1.0,
			// Set a sampling rate for profiling - this is relative to traces_sample_rate
			'profiles_sample_rate' => 1.0,
		]);
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
