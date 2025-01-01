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
use MythicalClient\Cli\CommandBuilder;

class Help extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $cmdInstance = self::getInstance();
        $cmdInstance->send($cmdInstance->bars);
        $cmdInstance->send('&5&lMythical&d&lDash &7- &d&lHelp');
        $cmdInstance->send('');

        $commands = scandir(__DIR__);

        foreach ($commands as $command) {
            if ($command === '.' || $command === '..' || $command === 'Command.php') {
                continue;
            }

            $command = str_replace('.php', '', $command);
            $commandClass = "MythicalClient\\Cli\\Commands\\$command";
            $commandFile = __DIR__ . "/$command.php";

            require_once $commandFile;

            if (!class_exists($commandClass)) {
                return;
            }

            $description = $commandClass::getDescription();
            $command = lcfirst($command);
            $subCommands = $commandClass::getSubCommands();
            $cmdInstance->send("&b{$command} &8> &7{$description}");

            if (!empty($subCommands)) {
                foreach ($subCommands as $subCommand => $description) {
                    $cmdInstance->send("    &8> &b{$command} {$subCommand} &8- &7{$description}");
                }
            }

        }
        $cmdInstance->send('');
        $cmdInstance->send($cmdInstance->bars);
    }

    public static function getDescription(): string
    {
        return 'Get help for all commands';
    }

    public static function getSubCommands(): array
    {
        return [];
    }
}
