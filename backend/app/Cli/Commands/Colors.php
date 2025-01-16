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

class Colors extends App implements CommandBuilder
{
    public static function execute(array $args): void
    {
        $app = App::getInstance();
        $colors = 'Colors: &0Black&r, &1Dark Blue&r, &2Dark Green&r, &3Dark Aqua&r, &4Dark Red&r, &5Dark Purple&r, &6Gold&r, &7Gray&r, &8Dark Gray&r, &9Blue&r, &aGreen&r, &bAqua&r, &cRed&r, &dLight Purple&r, &eYellow&r, &rWhite&r, &rReset&r, &lBold&r, &nUnderline&r, &mStrikethrough&r';

        $app->send($colors);
        $colors = '&0 0 &1 1 &2 2 &3 3 &4 4 &5 5 &6 6 &7 7 &8 8 &9 9 &a a &b b &c c &d d &e e &r r &l l &n n&r &m m &r';
        $app->send($colors);
    }

    public static function getDescription(): string
    {
        return '&c&l&n&oS&6&l&n&ou&e&l&n&op&a&l&n&op&3&l&n&oo&9&l&n&or&5&l&n&ot&c&l&n&oe&6&l&n&od&e&l&n&o &a&l&n&oC&3&l&n&oo&9&l&n&ol&5&l&n&oo&c&l&n&or&6&l&n&os&e&l&n&o &a&l&n&oB&3&l&n&oy&9&l&n&o &5&l&n&oO&c&l&n&ou&6&l&n&or&e&l&n&o &a&l&n&oA&3&l&n&ow&9&l&n&os&5&l&n&oo&c&l&n&om&6&l&n&oe&e&l&n&o &a&l&n&oC&3&l&n&oL&9&l&n&oI';
    }

    public static function getSubCommands(): array
    {
        return [];
    }
}
