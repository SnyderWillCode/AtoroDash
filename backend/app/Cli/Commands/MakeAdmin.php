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

namespace MythicalClient\Cli\Commands;

use MythicalClient\Cli\App;
use MythicalClient\Cli\CommandBuilder;

class MakeAdmin extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $app = App::getInstance();

        $app->send('&7What account do you want to give admin?');
        $app->send('');
        $line = trim(readline('> '));

    }

    public static function getDescription(): string
    {
        return 'Make an account a system admin!';
    }

    public static function getSubCommands(): array
    {
        return [];
    }
}
