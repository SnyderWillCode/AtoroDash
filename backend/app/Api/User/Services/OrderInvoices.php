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

$router->get('/api/user/invoices', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);

    $invoices = OrdersInvoices::getUserInvoices($session->getInfo(UserColumns::UUID, false));

    $appInstance->OK('Invoices fetched', [
        'invoices' => $invoices,
    ]);
});

$router->get('/api/user/invoice/(.*)', function ($id) {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);

    $invoice = OrdersInvoices::getInvoice($id);

    if (!$invoice) {
        $appInstance->NotFound('Invoice not found', []);

        return;
    }

    try {
        $orderInfo = Orders::getOrder($invoice['order']);
        $invoice['order'] = $orderInfo;

        $serviceInfo = Services::getService($orderInfo['service']);
        $invoice['order']['service'] = $serviceInfo;

        $categoryInfo = ServiceCategories::getCategoryById($serviceInfo['category']);
        $invoice['order']['service']['category'] = $categoryInfo;

        $providerInfo = PluginDB::convertIdToName((int) $serviceInfo['provider']);
        $invoice['order']['service']['provider'] = $providerInfo;

        $orderConfig = OrdersConfig::getOrderConfig($orderInfo['id']);
        $invoice['order']['service']['config'] = $orderConfig;

        $appInstance->OK('Invoice fetched', [
            'invoice' => $invoice,
        ]);
    } catch (Exception $e) {
        $appInstance->InternalServerError('Failed to fetch invoice', [
            'error' => 'ERR_INTERNAL_SERVER_ERROR',
            'message' => $e->getMessage(),
        ]);
    }
});

$router->post('/api/user/invoice/(.*)/pay', function ($id) {
    App::init();
    $appInstance = App::getInstance(true);
    $session = new Session($appInstance);

    $invoice = OrdersInvoices::getInvoice($id);

    if (!$invoice) {
        $appInstance->NotFound('Invoice not found', []);

        return;
    }

    $orderInfo = Orders::getOrder($invoice['order']);
    $serviceInfo = Services::getService($orderInfo['service']);
    $price = (int) $serviceInfo['price'];

    // Payment logic here
    if ((int) $session->getInfo(UserColumns::CREDITS, false) < $price) {
        $appInstance->Forbidden('Insufficient credits', [
            'error' => 'ERR_INSUFFICIENT_CREDITS',
        ]);

        return;
    }
    if ($invoice['status'] === 'paid') {
        $appInstance->Forbidden('Invoice already paid', [
            'error' => 'ERR_INVOICE_ALREADY_PAID',
        ]);

        return;
    }
    $newBalance = (int) $session->getInfo(UserColumns::CREDITS, false) - $price;
    $session->setInfo(UserColumns::CREDITS, $newBalance, false);
    OrdersInvoices::updateStatus($id, 'paid');
    Orders::updateStatus($invoice['order'], 'processed');
    Orders::updateDaysLeft($invoice['order'], 30);

    $appInstance->OK('Invoice status', [
        'price' => $price,
    ]);
});
