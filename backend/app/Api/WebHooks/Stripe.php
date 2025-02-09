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
use MythicalClient\Chat\User\User;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\Gateways\StripeDB;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;

$router->add('/api/webhooks/stripe', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $appInstance->OK('Stripe Webhook received', []);
});

$router->add('/api/stripe/processed', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();

    if (isset($_GET['code']) && !$_GET['code'] == '') {
        $code = $_GET['code'];
        $stripe = StripeDB::getByCode($code);
        if ($stripe) {
            $uuid = $stripe['user'];
            $coins = $stripe['coins'];

            $token = User::getTokenFromUUID($uuid);
            if (StripeDB::isPending($code)) {

                Stripe\Stripe::setApiKey($appInstance->getConfig()->getSetting(ConfigInterface::STRIPE_SECRET_KEY, 'NULL'));
                $coins = User::getInfo($token, UserColumns::CREDITS, false) + $coins;
                $coins = User::updateInfo($token, UserColumns::CREDITS, $coins, false);
                StripeDB::updateStatus($code, 'processed');

                exit(header('location: /?success=coins_added'));
            }
            exit(header('location: /?error=stripe_error=invalid_code'));

        }
        exit(header('location: /?error=stripe_error=invalid_code'));

    }
    exit(header('location: /?error=missing_data'));

});

$router->add('/api/stripe/cancelled', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);
    StripeDB::cancelLastTransactionForUser($session->getInfo(UserColumns::UUID, false));
    exit(header('location: /?error=stripe_error=cancelled'));
});

$router->add('/api/stripe/process', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);

    if (isset($_GET['coins']) && !$_GET['coins'] == '') {
        $coins = $_GET['coins'];
        $uuid = $session->getInfo(UserColumns::UUID, false);
        $code = bin2hex(random_bytes(16));
        if (StripeDB::create($code, $coins, $uuid)) {
            try {
                Stripe\Stripe::setApiKey($appInstance->getConfig()->getSetting(ConfigInterface::STRIPE_SECRET_KEY, 'NULL'));

                $checkout_session = Stripe\Checkout\Session::create([
                    'mode' => 'payment',
                    'customer_email' => $session->getInfo(UserColumns::EMAIL, false),
                    'success_url' => 'https://' . $appInstance->getConfig()->getSetting(ConfigInterface::APP_URL, 'framework.mythical.systems') . '/api/stripe/processed?code=' . $code,
                    'cancel_url' => 'https://' . $appInstance->getConfig()->getSetting(ConfigInterface::APP_URL, 'framework.mythical.systems') . '/api/stripe/cancelled',
                    'line_items' => [
                        [
                            'quantity' => 1,
                            'price_data' => [
                                'currency' => 'EUR',
                                'unit_amount' => $coins * 100,
                                'product_data' => [
                                    'name' => 'Account Topup',
                                    'description' => 'Topup your account with ' . $coins . ' coins!',
                                    'images' => [],
                                ],
                            ],
                        ],
                    ],
                ]);
                http_response_code(303);
                header('location: ' . $checkout_session->url);
                exit;
            } catch (Exception $e) {
                exit(header('location: /?error=stripe_error=' . $e->getMessage()));
            }
        } else {
            exit(header('location: /?error=db_error'));
        }
    } else {
        exit(header('location: /?error=missing_data'));
    }
});
