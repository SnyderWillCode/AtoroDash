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

namespace MythicalClient\Plugins;

class PluginFlags
{
    /**
     * Get the flags.
     */
    public static function getFlags(): array
    {
        return [
            'hasInstallScript',
            'hasRemovalScript',
            'hasUpdateScript',
            'developerIgnoreInstallScript',
            'developerEscalateInstallScript',
            'userEscalateInstallScript',
        ];
    }

    /**
     * Check if the flags are valid.
     *
     * @param array $flags The flags
     */
    public static function validFlags(array $flags): bool
    {
        $app = \MythicalClient\App::getInstance(true);
        try {
            $app->getLogger()->debug('Processing plugin flags');
            $flagList = PluginFlags::getFlags();
            foreach ($flagList as $flag) {
                if (in_array($flag, $flags)) {
                    $app->getLogger()->debug('Valid flag: ' . $flag);

                    return true;
                }
            }
            $app->getLogger()->debug('Invalid flags: ' . implode(', ', $flags));

            return false;
        } catch (\Exception $e) {
            $app->getLogger()->error('Failed to validate flags: ' . $e->getMessage());

            return false;
        }
    }
}
