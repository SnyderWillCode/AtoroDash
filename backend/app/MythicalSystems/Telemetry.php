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

namespace MythicalClient\MythicalSystems;

use MythicalClient\App;

class Telemetry
{
    public static function send(string $telemetryCollection): void
    {
        try {
            App::getInstance(true)->getLogger()->debug('Sending telemetry data: ' . $telemetryCollection);

        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->debug('Failed to send telemetry data: ' . $e->getMessage());
            // No one cares!
        }
    }
}
