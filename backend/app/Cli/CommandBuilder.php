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

namespace MythicalClient\Cli;

interface CommandBuilder
{
    /**
     * The description of the command.
     *
     * @var string
     */
    public static function getDescription(): string;

    /**
     * The subcommands of the command.
     */
    public static function getSubCommands(): array;

    /**
     * Execute the command.
     *
     * @param array $args the arguments passed to the command
     */
    public static function execute(array $args): void;
}
