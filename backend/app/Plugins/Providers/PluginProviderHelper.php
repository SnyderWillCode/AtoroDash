<?php

namespace MythicalClient\Plugins\Providers;

use MythicalClient\App;
use MythicalClient\Plugins\PluginHelper;

class PluginProviderHelper {
	/**
	 * Get the provider class for a plugin
	 * 
	 * @param string $identifier The plugin identifier
	 * @return PluginProvider|null The provider class instance or null if not found
	 */
	public static function getProviderClass(string $identifier): ?PluginProvider {
		try {
			// Get plugin config to verify it's a provider
			$config = PluginHelper::getPluginConfig($identifier);
			if (empty($config) || $config['plugin']['type'] !== 'provider') {
				return null;
			}

			// Build the expected provider class name
			$providerClass = "MythicalClient\\Storage\\Addons\\{$identifier}\\Provider";
			
			// Check if class exists and implements PluginProvider
			if (class_exists($providerClass) && is_subclass_of($providerClass, PluginProvider::class)) {
				return new $providerClass();
			}

			return null;
		} catch (\Exception $e) {
			App::getInstance(true)->getLogger()->error('Failed to get provider class: ' . $e->getMessage());
			return null;
		}
	}

	/**
	 * Get order requirements for a provider plugin
	 *
	 * @param string $identifier The plugin identifier
	 * @return array The order requirements or empty array if not found
	 */
	public static function getOrderRequirements(string $identifier): array {
		try {
			$provider = self::getProviderClass($identifier);
			if ($provider) {
				return $provider->getOrderRequirements();
			}
			return [];
		} catch (\Exception $e) {
			App::getInstance(true)->getLogger()->error('Failed to get order requirements: ' . $e->getMessage());
			return [];
		}
	}

	/**
	 * Check if a plugin has a valid provider implementation
	 *
	 * @param string $identifier The plugin identifier
	 * @return bool True if plugin has valid provider, false otherwise
	 */
	public static function hasValidProvider(string $identifier): bool {
		return self::getProviderClass($identifier) !== null;
	}
}