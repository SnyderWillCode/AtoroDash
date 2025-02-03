<?php

namespace MythicalClient\Plugins\Providers;

interface PluginProvider
{

	/**
	 * Get the order requirements.
	 *
	 * @return array
	 */
	public static function getOrderRequirements() : array;

}