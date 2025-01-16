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

namespace MythicalClient\Chat\columns;

class RolesColumns
{
    public static string $name = 'name';
    public static string $real_name = 'real_name';
    public static string $date = 'date';

    public static function getColumns(): array
    {
        return [
            'name' => 'name',
            'real_name' => 'real_name',
            'date' => 'date',
        ];
    }
}
