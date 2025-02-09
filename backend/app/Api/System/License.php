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

use MythicalClient\App;

$router->add('/api/system/license', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $licenseValidator = $appInstance->getLicenseValidator();
    if ($licenseValidator->validate()) {
        $appInstance->OK('License is valid!', []);
    } else {
        $appInstance->BadRequest('License is not valid!', []);
    }
});
