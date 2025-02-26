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
use MythicalClient\Plugins\PluginDB;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\Orders\Orders;
use MythicalClient\Chat\Services\Services;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\Orders\OrdersConfig;
use MythicalClient\Chat\Orders\OrdersInvoices;
use MythicalClient\Chat\Services\ServiceCategories;
use MythicalClient\Plugins\Providers\PluginProviderHelper;

$router->post('/api/user/services/(.*)/(.*)/order', function ($category, $service) {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyPOST();
    $session = new Session($appInstance);

    $service = Services::getServiceByUri($service);
    if (!$service) {
        $appInstance->NotFound('Service not found', ['error' => 'ERR_SERVICE_NOT_FOUND']);

        return;
    }

    $category = ServiceCategories::getCategoryByUri($category);
    if (!$category) {
        $appInstance->NotFound('Category not found', ['error' => 'ERR_CATEGORY_NOT_FOUND']);

        return;
    }

    $provider = PluginDB::convertIdToName((int) $service['provider']);
    global $pluginManager;
    $ordersConfig = [];
    $providers = $pluginManager->getLoadedProviders();

    if (in_array($provider, $providers)) {

        $requirements = PluginProviderHelper::getOrderRequirements($provider);

        // Validate that all required fields are present
        foreach ($requirements as $field => $requirement) {
            $requestData = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $appInstance->BadRequest('Invalid JSON in request body', [
                    'error' => 'ERR_INVALID_JSON',
                ]);

                return;
            }
            if ($requirement['required'] && (!isset($requestData[$field]) || empty($requestData[$field]))) {
                $appInstance->BadRequest('Missing required field', [
                    'error' => 'ERR_MISSING_REQUIRED_FIELD',
                    'field' => $field,
                ]);

                return;
            }
            $ordersConfig[$field] = $requestData[$field];
        }

        try {
            $order = Orders::create($session->getInfo(UserColumns::UUID, false), $service['id'], (int) $service['provider']);
            if ($order) {
                foreach ($ordersConfig as $field => $value) {
                    if ($value !== null) {
                        OrdersConfig::addConfig($order['id'], $field, $value);
                    }
                }
                // Create invoice for the order
                $invoice = OrdersInvoices::create(
                    $session->getInfo(UserColumns::UUID, false),
                    $order['id']
                );

                if (!$invoice) {
                    $appInstance->InternalServerError('Failed to create invoice', [
                        'error' => 'ERR_INVOICE_CREATION_FAILED',
                    ]);

                    return;
                }
                $appInstance->OK('Order created', [
                    'metadata' => [
                        'order' => $order,
                        'invoice' => $invoice,
                        'service' => $service,
                        'requirements' => $requirements,
                        'config' => $ordersConfig,
                        'providerName' => $provider,
                    ],
                ]);

            } else {
                $appInstance->InternalServerError('Order processing error', [
                    'error' => 'ERR_INTERNAL_SERVER_ERROR',
                    'message' => 'Failed to create order',
                ]);
            }
        } catch (Exception $e) {
            $appInstance->InternalServerError('Order processing error', [
                'error' => 'ERR_INTERNAL_SERVER_ERROR',
                'message' => $e->getMessage(),
            ]);
        }
    } else {
        $appInstance->NotFound('Provider not found', ['error' => 'ERR_PROVIDER_NOT_FOUND', 'providers' => $providers, 'provider' => $provider]);
    }
});

$router->get('/api/user/services/(.*)/(.*)/order', function ($category, $service) {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    new Session($appInstance);

    $category = ServiceCategories::getCategoryByUri($category);
    if (!$category) {
        $appInstance->NotFound('Category not found', ['error' => 'ERR_CATEGORY_NOT_FOUND']);

        return;
    }

    $service = Services::getServiceByUri($service);
    if (!$service) {
        $appInstance->NotFound('Service not found', ['error' => 'ERR_SERVICE_NOT_FOUND']);

        return;
    }

    $provider = PluginDB::convertIdToName((int) $service['provider']);
    global $pluginManager;
    $providers = $pluginManager->getLoadedProviders();

    if (in_array($provider, $providers)) {
        $requirements = PluginProviderHelper::getOrderRequirements($provider);
        $appInstance->OK('Here is the order!', [
            'service' => [
                'category' => $category,
                'service' => $service,
                'requirements' => $requirements,
            ],
        ]);
    } else {
        $appInstance->NotFound('Provider not found', ['error' => 'ERR_PROVIDER_NOT_FOUND', 'providers' => $providers, 'provider' => $provider]);
    }
});

$router->get('/api/user/orders', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);

    $orders = Orders::getAllUserOrders($session->getInfo(UserColumns::UUID, false));

    // Enhance each order with its service information
    foreach ($orders as &$order) {
        $service = Services::getServiceByID($order['service']);
        $order['service'] = $service;
    }

    $appInstance->OK('Here are your orders', ['orders' => $orders]);
});
