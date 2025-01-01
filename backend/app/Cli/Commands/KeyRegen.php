<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * 2021-2025 (c) All rights reserved
 *
 * MIT License
 *
 * (c) MythicalSystems - All rights reserved
 * (c) NaysKutzu - All rights reserved
 * (c) Cassian Gherman- All rights reserved
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
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
