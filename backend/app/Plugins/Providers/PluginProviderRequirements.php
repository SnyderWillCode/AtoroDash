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

namespace MythicalClient\Plugins\Providers;

interface PluginProviderRequirements
{
    public const TEXT = 'text';
    public const NUMBER = 'number';
    public const PASSWORD = 'password';
    public const EMAIL = 'email';
    public const TEXT_AREA = 'textarea';

}
