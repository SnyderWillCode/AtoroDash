<?php

namespace MythicalClient\Storage\Addons\discordwebhook;

use MythicalClient\App;
use MythicalClient\Plugins\Events\Events\AppEvent;
use MythicalClient\Plugins\Events\Events\AuthEvent;
use MythicalClient\Plugins\Events\PluginEventRequirements;
use MythicalClient\Plugins\PluginEvents;

class Event implements PluginEventRequirements {
    public static function processEvents(PluginEvents $event) : void {
        $event->on(AppEvent::onAppLoad(), function () {
			App::getInstance(true)->getLogger()->debug("Discord webhook loaded");
		});

		$event->on(AuthEvent::onAuthLoginFailed(), function ($data, $data2) {
			App::getInstance(true)->getLogger()->debug("User login failed: " . $data. " Reason: " . $data2);
		});

		$event->on(AuthEvent::onAuthLoginSuccess(), function ($data) {
			App::getInstance(true)->getLogger()->debug("Discord webhook login success: " . $data['login']);
		});
    }
}