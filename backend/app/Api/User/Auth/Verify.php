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
