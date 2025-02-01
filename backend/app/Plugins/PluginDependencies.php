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

use MythicalClient\Plugins\Dependencies\ComposerDependencies;
use MythicalClient\Plugins\Dependencies\PhpVersionDependencies;
use MythicalClient\Plugins\Dependencies\PhpExtensionDependencies;

class PluginDependencies
{
    public static function checkDependencies(array $dependencies): bool
    {
        $requirements = $dependencies['plugin']['dependencies'];
        foreach ($requirements as $dependency) {
            // Check if the requirement is a composer package
            if (strpos($dependency, 'composer=') === 0) {
                $composerVersion = substr($dependency, strlen('composer='));
                if (!ComposerDependencies::isInstalled($composerVersion)) {
                    return false;
                }
            }

            // Check if the requirement is a php version
            if (strpos($dependency, 'php=') === 0) {
                $phpVersion = substr($dependency, strlen('php='));
                if (!PhpVersionDependencies::isInstalled($phpVersion)) {
                    return false;
                }
            }

            // Check if the requirement is a php extension
            if (strpos($dependency, 'php-ext=') === 0) {
                $ext = substr($dependency, strlen('php-ext='));
                if (!PhpExtensionDependencies::isInstalled($ext)) {
                    return false;
                }
            }
        }

        return true;
    }
}
