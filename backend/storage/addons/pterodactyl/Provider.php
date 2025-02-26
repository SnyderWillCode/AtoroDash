<?php 

namespace MythicalClient\Storage\Addons\pterodactyl;

use MythicalClient\Plugins\Providers\PluginProvider;
use MythicalClient\Plugins\Providers\PluginProviderRequirements;

class Provider implements PluginProvider
{

	/**
	 * @inheritDoc
	 */
	public static function getOrderRequirements(): array {
		return [
			'server_name' => [
				'type' => PluginProviderRequirements::TEXT,
				'label' => 'Server Name',
				'required' => true,
				'placeholder' => 'Server Name'
			],
			'server_description' => [
				'type' => PluginProviderRequirements::TEXT_AREA,
				'label' => 'Server Description',
				'required' => false,
				'placeholder' => 'Server Description',
			],
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function getOrderConfig(): array {
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public static function getAdminConfig(): array {
		return [];
	}
}