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
use MythicalSystems\CloudFlare\Turnstile;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;

$router->add('/api/user/auth/register', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();
    /**
     * Check if the required fields are set.
     *
     * @var string
     */
    if (!isset($_POST['firstName']) || $_POST['firstName'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_FIRST_NAME']);
    }
    if (!isset($_POST['lastName']) || $_POST['lastName'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_LAST_NAME']);
    }
    if (!isset($_POST['email']) || $_POST['email'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_EMAIL']);
    }

    if (!isset($_POST['password']) || $_POST['password'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD']);
    }

    if (!isset($_POST['username']) || $_POST['username'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_USERNAME']);
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

    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $username = $_POST['username'];

    /**
     * Check if the email is already in use.
     *
     * @var bool
     */
    try {
        if (User::exists(UserColumns::USERNAME, $username)) {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'USERNAME_ALREADY_IN_USE']);
        }
        if (User::exists(UserColumns::EMAIL, $email)) {
            $appInstance->BadRequest('Bad Request', ['error_code' => 'EMAIL_ALREADY_IN_USE']);
        }
        User::register($username, $password, $email, $firstName, $lastName, CloudFlareRealIP::getRealIP());
        App::OK('User registered', []);

    } catch (Exception $e) {
        $appInstance->InternalServerError('Internal Server Error', ['error_code' => 'DATABASE_ERROR']);
    }

});
