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

namespace MythicalClient\Chat\interface;

class UserActivitiesTypes
{
    public static string $login = 'auth:login';
    public static string $register = 'auth:register';

    /**
     * Get all types.
     *
     * @return array All types
     */
    public static function getTypes(): array
    {
        return [
            self::$login,
            self::$register,
        ];
    }
}
