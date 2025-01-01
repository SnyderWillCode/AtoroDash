<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * 2021-2025 (c) All rights reserved
 *
 * MIT License
 *
 * (c) MythicalSystems - All rights reserved
 * (c) NaysKutzu - All rights reserved
 * (c) Cassian Gherman- All rights reserved
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use MythicalClient\App;
use MythicalClient\Chat\User;
use MythicalClient\Chat\Verification;
use MythicalSystems\CloudFlare\Turnstile;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;
use MythicalClient\Chat\columns\EmailVerificationColumns;

$router->get('/api/user/auth/reset', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    if (isset($_GET['code']) && $_GET['code'] != '') {
        $code = $_GET['code'];

        if (Verification::verify($code, EmailVerificationColumns::$type_password)) {
            $appInstance->OK('Code is valid', ['reset_code' => $code]);
        } else {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
        }
    } else {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_CODE']);
    }
});

$router->post('/api/user/auth/reset', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();

    if (!isset($_POST['email_code']) || $_POST['email_code'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_CODE']);
    }

    if (!isset($_POST['password']) || $_POST['password'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD']);
    }

    if (!isset($_POST['confirmPassword']) || $_POST['confirmPassword'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD_CONFIRM']);
    }

    if ($_POST['password'] != $_POST['confirmPassword']) {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'PASSWORDS_DO_NOT_MATCH']);
    }

    $code = $_POST['email_code'];
    $password = $_POST['password'];

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

    if (Verification::verify($code, EmailVerificationColumns::$type_password)) {
        $uuid = Verification::getUserUUID($code);
        if ($uuid == null || $uuid == '') {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
        }
        $userToken = User::getTokenFromUUID($uuid);
        if ($userToken == null || $userToken == '') {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
        }

        if (User::updateInfo($userToken, UserColumns::PASSWORD, $password, true) == true) {
            Verification::delete($code);
            $token = App::getInstance(true)->encrypt(date('Y-m-d H:i:s') . $uuid . random_bytes(16) . base64_encode($code));
            User::updateInfo($userToken, UserColumns::ACCOUNT_TOKEN, $token, true);
            $appInstance->OK('Password has been reset', []);
        } else {
            $appInstance->BadRequest('Failed to reset password', ['error_code' => 'FAILED_TO_RESET_PASSWORD']);
        }
    } else {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
    }
});
