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
use MythicalSystems\Utils\XChaCha20;
use MythicalClient\Cli\CommandBuilder;

class KeyRegen extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $app = App::getInstance();
        if (in_array('-force', $args)) {
            $isForced = true;
        } else {
            $isForced = false;
        }

        if (!$isForced) {
            $app->send('&7Are you sure you want to reset the key? This will corupt all data there may be in the database! Type &ayes &7to continue or &cno &7to cancel.');
            $app->send('&7This action is irreversible!');
            $app->send('&7Type your answer below:');
            $line = trim(readline('> '));
            if ($line !== 'yes') {
                $app->send('&cAction cancelled.');

                return;
            }
            $isForced = true; // If the user types yes, then we can force the key reset

        }

        if ($isForced) {
            $mainApp = \MythicalClient\App::getInstance(true);
            $mainApp->loadEnv();
            $mainApp->getLogger()->warning('Old encryption key was: ' . $_ENV['DATABASE_ENCRYPTION_KEY']);
            $app->send(message: '&7Old encryption key was: &e' . $_ENV['DATABASE_ENCRYPTION_KEY']);
            $newKey = XChaCha20::generateStrongKey(true);
            $mainApp->updateEnvValue('DATABASE_ENCRYPTION_KEY', $newKey, true);
            sleep(3);
            $_ENV['DATABASE_ENCRYPTION_KEY'] = $newKey;
            $mainApp->getLogger()->warning('New encryption key is: ' . $_ENV['DATABASE_ENCRYPTION_KEY']);
            $app->send(message: '&7New encryption key is: &e' . $_ENV['DATABASE_ENCRYPTION_KEY']);
            $app->send(message: '&7Key reset successfully!');
        } else {
            $app->send('&cAction cancelled.');

            return;
        }
    }

    public static function getDescription(): string
    {
        return 'Regenerate the encryption key';
    }

    public static function getSubCommands(): array
    {
        return [];
    }
}
