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

class EmailVerificationColumns
{
    public static string $code = 'code';
    public static string $user = 'user';
    public static array $type = ['password', 'verify'];
    public static string $type_verify = 'verify';
    public static string $type_password = 'password';

    public static function getColumns(): array
    {
        return [
            'code' => 'code',
            'user' => 'user',
            'type' => ['password', 'verify'],
        ];
    }
}
