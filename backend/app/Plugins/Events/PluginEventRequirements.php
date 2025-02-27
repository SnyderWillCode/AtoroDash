<?php

namespace MythicalClient\Plugins\Events;

use MythicalClient\Plugins\PluginEvents;

interface PluginEventRequirements {
	public static function processEvents(PluginEvents $event) : void;
}