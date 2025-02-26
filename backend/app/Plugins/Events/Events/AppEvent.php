<?php

namespace MythicalClient\Plugins\Events\Events;

use MythicalClient\Plugins\Events\PluginEvent;

class AppEvent implements PluginEvent {
	public static function onAppLoad() : string {
		return "app::Load";
	}
}