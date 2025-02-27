<?php

namespace MythicalClient\Plugins\Events;

use MythicalClient\App;
use MythicalClient\Plugins\PluginEvents;
use MythicalClient\Plugins\PluginHelper;

class PluginEventProcessor {
	/**
     * Get the event class for a plugin.
     *
     * @param string $identifier The plugin identifier
     *
     * @return PluginEventRequirements|null The event class instance or null if not found
     */
    public static function getEventProcessor(string $identifier): ?PluginEventRequirements
    {
        try {
            // Get plugin config to verify it's a event
            $config = PluginHelper::getPluginConfig($identifier);
            if (empty($config) || $config['plugin']['type'] !== 'event') {
                return null;
            }

            // Build the expected event class name
            $eventClass = "MythicalClient\\Storage\\Addons\\{$identifier}\\Event";

            // Check if class exists and implements PluginEvent
            if (class_exists($eventClass) && is_subclass_of($eventClass, PluginEventRequirements::class)) {
                return new $eventClass();
            }

            return null;
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to get event class: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a plugin has a valid event implementation.
     *
     * @param string $identifier The plugin identifier
     *
     * @return bool True if plugin has valid event, false otherwise
     */
    public static function hasValidEvent(string $identifier): bool
    {
        return self::getEventProcessor($identifier) !== null;
    }
	/**
	 * Process an event for a plugin.
	 *
	 * @param string $identifier The plugin identifier
	 * @param \MythicalClient\Plugins\PluginEvents $event The event to process
	 * 
	 * @return void
	 */
	public static function processEvent(string $identifier, PluginEvents $event)
	{
		$eventProcessor = self::getEventProcessor($identifier);
		if ($eventProcessor !== null) {
			$eventProcessor->processEvents($event);
		}
	}
}
