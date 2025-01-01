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
use MythicalClient\Chat\Mails;
use MythicalClient\Chat\Session;
use MythicalClient\Chat\columns\UserColumns;

$router->get('/api/user/session/emails', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    $appInstance->OK('User emails', [
        'emails' => Mails::getAll(User::getInfo($accountToken, UserColumns::UUID, false)),
    ]);
});

$router->get('/api/user/session/emails/(.*)/raw', function (string $id): void {
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();
    if ($id == '') {
        exit(header('location: /account'));
    }

    if (!is_numeric($id)) {
        exit(header('location: /account'));
    }
    $id = (int) $id;

    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    if (Mails::exists($id)) {
        if (Mails::doesUserOwnEmail(User::getInfo($accountToken, UserColumns::UUID, false), $id)) {
            $mail = Mails::get($id);
            header('Content-Type: text/html; charset=utf-8');
            echo $mail['body'];
            exit;
        }
        exit(header('location: /account'));

    }
    exit(header('location: /account'));

});

$router->delete('/api/user/session/emails/(.*)/delete', function (string $id): void {
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();
    if ($id == '') {
        $appInstance->BadRequest('Email not found!', ['error_code' => 'EMAIL_NOT_FOUND']);
    }

    if (!is_numeric($id)) {
        $appInstance->BadRequest('Email not found!', ['error_code' => 'EMAIL_NOT_FOUND']);
    }
    $id = (int) $id;

    $appInstance->allowOnlyDELETE();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    if (Mails::exists($id)) {
        if (Mails::doesUserOwnEmail(User::getInfo($accountToken, UserColumns::UUID, false), $id)) {
            Mails::delete($id, User::getInfo($accountToken, UserColumns::UUID, false));
            $appInstance->OK('Email deleted successfully!', []);
        } else {
            $appInstance->Unauthorized('Unauthorized', ['error_code' => 'UNAUTHORIZED']);
        }
    } else {
        $appInstance->BadRequest('Email not found!', ['error_code' => 'EMAIL_NOT_FOUND']);
    }
});
