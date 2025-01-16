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
use MythicalClient\Chat\Verification;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\columns\EmailVerificationColumns;

$router->get('/api/user/auth/verify', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    if (isset($_GET['code']) && $_GET['code'] != '') {
        $code = $_GET['code'];

        if (Verification::verify($code, EmailVerificationColumns::$type_verify)) {
            if (User::exists(UserColumns::UUID, Verification::getUserUUID($code))) {
                $token = User::getInfo(User::getTokenFromUUID(Verification::getUserUUID($code)), UserColumns::ACCOUNT_TOKEN, false);
                if ($token != null && $token != '') {
                    setcookie('user_token', $token, time() + 3600, '/');
                    User::updateInfo(User::getTokenFromUUID(Verification::getUserUUID($code)), UserColumns::VERIFIED, 'true', false);
                    Verification::delete($code);
                    exit(header('location: /'));
                }
                $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_USER', 'email_code' => $code]);

            } else {
                $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_USER', 'email_code' => $code]);
            }
        } else {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE', 'email_code' => $code]);
        }
    } else {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_CODE']);
    }
});
