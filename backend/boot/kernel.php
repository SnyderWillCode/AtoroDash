<?php

use MythicalClient\Plugins\PluginManager;

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

try {
    if (file_exists(APP_DIR . 'storage/packages')) {
        require APP_DIR . 'storage/packages/autoload.php';
    } else {
        throw new Exception('Packages not installed looked at this path: ' . APP_DIR . 'storage/packages');
    }
} catch (Exception $e) {
    echo $e->getMessage();
    echo "\n";
    exit;
}

ini_set('expose_php', 'off');
header_remove('X-Powered-By');
header_remove('Server');

if (!is_writable(__DIR__)) {
    exit('Please make sure the root directory is writable.');
}

if (!is_writable(__DIR__ . '/../storage')) {
    exit('Please make sure the storage directory is writable.');
}

if (!extension_loaded('mysqli')) {
    exit('MySQL extension is not installed!');
}

if (!extension_loaded('curl')) {
    exit('Curl extension is not installed!');
}

if (!extension_loaded('gd')) {
    exit('GD extension is not installed!');
}

if (!extension_loaded('mbstring')) {
    exit('MBString extension is not installed!');
}

if (!extension_loaded('openssl')) {
    exit('OpenSSL extension is not installed!');
}

if (!extension_loaded('zip')) {
    exit('Zip extension is not installed!');
}

if (!extension_loaded('bcmath')) {
    exit('Bcmath extension is not installed!');
}

if (!extension_loaded('json')) {
    exit('JSON extension is not installed!');
}

if (!extension_loaded('sodium')) {
    exit('sodium extension is not installed!');
}
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    exit('This application requires at least PHP 8.1.0');
}


/**
 * Initialize the plugin manager.
 */
$pluginManager = new PluginManager();
global $pluginManager;