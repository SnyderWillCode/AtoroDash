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

$router->add('/api/user/auth/forgot', function (): void {
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();
    /**
     * Check if the required fields are set.
     *
     * @var string
     */
    if (!isset($_POST['email']) || $_POST['email'] == '') {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_EMAIL']);
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
    $email = $_POST['email'];

    if (User::exists(UserColumns::EMAIL, $email)) {

        if (User::forgotPassword($email)) {
            $appInstance->OK('Successfully sent email', []);
        } else {
            $appInstance->BadRequest('Failed to send email', ['error_code' => 'FAILED_TO_SEND_EMAIL']);
        }

    } else {
        $appInstance->BadRequest('Email does not exist', ['error_code' => 'EMAIL_DOES_NOT_EXIST']);
    }

});
