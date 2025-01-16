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

class Down extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $app = App::getInstance();

        if (file_exists(__DIR__ . '/../../../storage/caches/maintenance.php')) {
            $app->send('&cThe server is already in maintenance mode!');
            \MythicalClient\App::getInstance(true)->getLogger()->error('The server is already in maintenance mode!');
            exit;
        }
        \MythicalClient\App::getInstance(true)->getLogger()->info('The server is now in maintenance mode!');
        $fileTemplate = "<?php header('Content-Type: application/json');echo json_encode(['code'=>503,'message'=>'The application is under maintenance.','error'=>'Service Unavailable','success'=>false,],JSON_PRETTY_PRINT);die();";
        file_put_contents(__DIR__ . '/../../../storage/caches/maintenance.php', $fileTemplate);
        $app->send('&aThe server is now in maintenance mode.');
        exit;

    }

    public static function getDescription(): string
    {
        return 'Put the server from maintenance mode';
    }

    public static function getSubCommands(): array
    {
        return [];
    }
}
