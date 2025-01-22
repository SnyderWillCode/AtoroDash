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

namespace MythicalClient\Chat\User;

class Can
{
    /**
     * Check if the user can access the admin UI.
     *
     * @param int $id The role id!
     *
     * @return bool Returns true if the user can access the admin UI
     */
    public static function canAccessAdminUI(int $id): bool
    {
        return in_array($id, [3, 4, 5, 6, 7, 8], true);
    }
}
