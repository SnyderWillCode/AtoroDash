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
