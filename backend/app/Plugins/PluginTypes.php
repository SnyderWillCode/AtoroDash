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

namespace MythicalClient\Plugins;

class PluginTypes
{
    public static $event = 'event';
    public static $provider = 'provider';
    public static $components = 'components';

    /**
     * Get the types.
     *
     * @return array The types
     */
    public static function getTypes(): array
    {
        return [
            'event',
            'components',
            'provider'
        ];
    }

    /**
     * Check if the type is allowed.
     *
     * @param string $types The type
     *
     * @return bool If the type is allowed
     */
    public static function isTypeAllowed(string $types): bool
    {
        return in_array($types, self::getTypes());
    }
}
