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
use MythicalClient\Chat\User;
use MythicalClient\Chat\Session;
use PragmaRX\Google2FA\Google2FA;
use MythicalSystems\CloudFlare\Turnstile;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;

$router->get('/api/user/auth/2fa/setup', function (): void {
    App::init();
    $appInstance = App::getInstance(true);

    $appInstance->allowOnlyGET();
    $google2fa = new Google2FA();
    $session = new Session($appInstance);

    $secret = $google2fa->generateSecretKey();
    $session->setInfo(UserColumns::TWO_FA_KEY, $secret, true);
    $appInstance->OK('Successfully generated secret key', ['secret' => $secret]);
});

$router->post('/api/user/auth/2fa/setup', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();
    $appInstance->allowOnlyPOST();
    /**
     * Process the turnstile response.
     *
     * IF the turnstile is enabled
     */
    if ($appInstance->getConfig()->getSetting(ConfigInterface::TURNSTILE_ENABLED, 'false') == 'true') {
        if (!isset($_POST['turnstileResponse']) || $_POST['turnstileResponse'] == '') {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'TURNSTILE_FAILED']);
        }
        $cfTurnstileResponse = $_POST['turnstileResponse'];
        if (!Turnstile::validate($cfTurnstileResponse, MythicalClient\CloudFlare\CloudFlareRealIP::getRealIP(), $config->getSetting(ConfigInterface::TURNSTILE_KEY_PRIV, 'XXXX'))) {
            $appInstance->BadRequest('Invalid TurnStile Key', ['error_code' => 'TURNSTILE_FAILED']);
        }
    }

    $google2fa = new Google2FA();

    if (!isset($_POST['code'])) {
        $appInstance->getLogger()->debug('Code missing');
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_CODE']);
    }

    $secret = User::getInfo($_COOKIE['user_token'], UserColumns::TWO_FA_KEY, true);
    $code = $_POST['code'];

    if ($google2fa->verifyKey($secret, $code, null, null, null)) {
        User::updateInfo($_COOKIE['user_token'], UserColumns::TWO_FA_ENABLED, 'true', encrypted: false);
        User::updateInfo($_COOKIE['user_token'], UserColumns::TWO_FA_BLOCKED, 'false', false);
        $appInstance->OK('Code valid go on!', ['secret' => $secret]);
    } else {
        $appInstance->Unauthorized('Code invalid', ['error_code' => 'INVALID_CODE']);
    }
});

$router->get('/api/auth/2fa/setup/kill', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $session->setInfo(UserColumns::TWO_FA_ENABLED, 'false', false);
    $session->setInfo(UserColumns::TWO_FA_KEY, '', false);

    header('location: /?href=api');

    exit;
});
