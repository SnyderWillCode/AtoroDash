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

namespace MythicalClient\Plugins\Dependencies;

interface Dependencies
{
    /**
     * Check if the dependency is installed.
     *
     * @param string $identifier the identifier of the dependency
     *
     * @return bool true if the dependency is installed, false otherwise
     */
    public static function isInstalled(string $identifier): bool;
}
