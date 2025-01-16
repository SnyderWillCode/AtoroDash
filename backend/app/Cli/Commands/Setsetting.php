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
use MythicalClient\Chat\Database;
use MythicalClient\Cli\CommandBuilder;
use MythicalClient\Config\ConfigFactory;

class Setsetting extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $cliApp = App::getInstance();
        if (!file_exists(__DIR__ . '/../../../storage/.env')) {
            $cliApp->send('&7The application is not setup!');
            exit;
        }

        $cliApp->send('&aPlease enter the setting you want to update:');
        $setting = readline('> ');

        $cliApp->send('&aPlease enter the value you want to set:');
        $value = readline('> ');
        \MythicalClient\App::getInstance(true)->loadEnv();

        try {
            $db = new Database($_ENV['DATABASE_HOST'], $_ENV['DATABASE_DATABASE'], $_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD']);
            $config = new ConfigFactory($db->getPdo());
            $config->setSetting($setting, $value);
        } catch (\Exception $e) {
            $cliApp->send('&cAn error occurred while connecting to the database: ' . $e->getMessage());
            exit;
        }

        $cliApp->send('&aSetting &e' . $setting . ' &ahas been set to &e' . $value);

        $cliApp->send('&aThe application has been setup!');
    }

    public static function getDescription(): string
    {
        return 'Update a setting!';
    }

    public static function getSubCommands(): array
    {
        return [];
    }
}
