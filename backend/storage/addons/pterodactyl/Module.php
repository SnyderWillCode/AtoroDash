<?php

namespace MythicalClient\Storage\Addons\pterodactyl;

use MythicalClient\App;
use MythicalClient\Plugins\Events\Events\AppEvent;
use MythicalClient\Plugins\Providers\PluginProvider;
use MythicalClient\Plugins\Providers\PluginProviderRequirements;

class Module implements PluginProvider
{

	/**
	 * @inheritDoc
	 */
	public static function getOrderRequirements(): array
	{
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
	public static function getOrderConfig(): array
	{
		return [];
	}

	/**
	 * @inheritDoc
	 */
	public static function processEvents(\MythicalClient\Plugins\PluginEvents $event): void
	{
		$event->on(AppEvent::onRouterReady(), function ($router) {
			$router->add('/api/pterodactyl', function (): void {
				die("If you see this. this means that the pterodactyl provider is loaded successfully! and has been registered in the system!");
			});
		});

		$event->on(AppEvent::onAppLoad(), function () {
			App::getInstance(true)->getLogger()->debug("Pterodactyl provider loaded successfully!");
		});
	}
}