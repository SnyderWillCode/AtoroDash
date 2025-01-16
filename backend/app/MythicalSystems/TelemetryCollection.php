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

namespace MythicalClient\MythicalSystems;

interface TelemetryCollection
{
    public const USER_NEW = 'user:new';
    public const SERVICE_NEW = 'service:new';
    public const TICKET_OPEN = 'ticket:open';

}
