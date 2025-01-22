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
use MythicalClient\Mail\Mail;
use MythicalClient\Chat\User\User;
use MythicalSystems\CloudFlare\Turnstile;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;

$router->add('/api/user/auth/login', function (): void {
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();

    /**
     * Check if the required fields are set.
     *
     * @var string
     */
    if (!isset($_POST['login']) || $_POST['login'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_LOGIN']);
    }

    if (!isset($_POST['password']) || $_POST['password'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD']);
    }

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
        if (!Turnstile::validate($cfTurnstileResponse, CloudFlareRealIP::getRealIP(), $config->getSetting(ConfigInterface::TURNSTILE_KEY_PRIV, 'XXXX'))) {
            $appInstance->BadRequest('Invalid TurnStile Key', ['error_code' => 'TURNSTILE_FAILED']);
        }
    }
    $login = $_POST['login'];
    $password = $_POST['password'];

    $login = User::login($login, $password);
    setcookie('user_token', $login, time() + 3600, '/');

    if ($login == 'false') {
        $appInstance->BadRequest('Invalid login credentials', ['error_code' => 'INVALID_CREDENTIALS']);
    } else {
        if (User::getInfo($login, UserColumns::VERIFIED, false) == 'false') {
            if (Mail::isEnabled() == true) {
                User::logout();
                $appInstance->BadRequest('Account not verified', ['error_code' => 'ACCOUNT_NOT_VERIFIED']);
            }
        }

        if (User::getInfo($login, UserColumns::BANNED, false) != 'NO') {
            User::logout();
            $appInstance->BadRequest('Account is banned', ['error_code' => 'ACCOUNT_BANNED']);
        }

        if (User::getInfo($login, UserColumns::DELETED, false) == 'true') {
            User::logout();
            $appInstance->BadRequest('Account is deleted', ['error_code' => 'ACCOUNT_DELETED']);
        }

        if (User::getInfo($login, UserColumns::TWO_FA_ENABLED, false) == 'true') {
            User::updateInfo($login, UserColumns::TWO_FA_BLOCKED, 'true', false);
        }
        $appInstance->OK('Successfully logged in', []);
    }
});
