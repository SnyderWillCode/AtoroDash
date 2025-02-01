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
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\Services\Services;
use MythicalClient\Chat\Services\ServicePrices;
use MythicalClient\Chat\Services\ServiceCategories;

$router->add('/api/user/services/(.*)/services', function ($category) {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    new Session($appInstance);

    $category = ServiceCategories::getCategoryByUri($category);
    if (!$category) {
        $appInstance->NotFound('Category not found', ['error' => 'ERR_CATEGORY_NOT_FOUND']);

        return;
    }
    $services = Services::getServicesByCategory($category['id']);
    $servicesWithPrices = [];

    // Add prices to each service and filter out services without prices
    foreach ($services as $service) {
        $prices = ServicePrices::getServicesByService($service['id']);
        if (!empty($prices)) {
            $service['prices'] = $prices;
            $servicesWithPrices[] = $service;
        }
    }

    $appInstance->OK('Here are all the services!', ['services' => $servicesWithPrices]);
});
