<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * MIT License
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
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

    public static function getSubCommands(int $index): array
    {
        return [];
    }
}
