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

namespace MythicalClient\Plugins\Providers;

use MythicalClient\Plugins\Events\PluginEventRequirements;

interface PluginProvider extends PluginEventRequirements
{
    /**
     * Get the order requirements.
     */
    public static function getOrderRequirements(): array;

    /**
     * Get the plugin order config.
     */
    public static function getOrderConfig(): array;

}
