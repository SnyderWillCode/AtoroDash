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
use MythicalClient\Plugins\Events\Events\AuthEvent;
use MythicalSystems\CloudFlare\Turnstile;
use MythicalClient\Chat\User\Verification;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;
use MythicalClient\Chat\columns\EmailVerificationColumns;

$router->get('/api/user/auth/reset', function (): void {
	global $eventManager;
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    if (isset($_GET['code']) && $_GET['code'] != '') {
        $code = $_GET['code'];

        if (Verification::verify($code, EmailVerificationColumns::$type_password)) {
			$eventManager->emit(AuthEvent::onAuthResetPasswordSuccess(), ['code' => $code]);
            $appInstance->OK('Code is valid', ['reset_code' => $code]);
        } else {
			$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => $code]);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
        }
    } else {
		$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_CODE']);
    }
});

$router->post('/api/user/auth/reset', function (): void {
    App::init();
	global $eventManager;
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();

    if (!isset($_POST['email_code']) || $_POST['email_code'] == '') {
		$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_CODE']);
    }

    if (!isset($_POST['password']) || $_POST['password'] == '') {
		$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD']);
    }

    if (!isset($_POST['confirmPassword']) || $_POST['confirmPassword'] == '') {
		$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD_CONFIRM']);
    }

    if ($_POST['password'] != $_POST['confirmPassword']) {
		$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
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
			$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'TURNSTILE_FAILED']);
        }
        $cfTurnstileResponse = $_POST['turnstileResponse'];
        if (!Turnstile::validate($cfTurnstileResponse, CloudFlareRealIP::getRealIP(), $config->getSetting(ConfigInterface::TURNSTILE_KEY_PRIV, 'XXXX'))) {
			$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
            $appInstance->BadRequest('Invalid TurnStile Key', ['error_code' => 'TURNSTILE_FAILED']);
        }
    }

    if (Verification::verify($code, EmailVerificationColumns::$type_password)) {
        $uuid = Verification::getUserUUID($code);
        if ($uuid == null || $uuid == '') {
			$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
        }
        $userToken = User::getTokenFromUUID($uuid);
        if ($userToken == null || $userToken == '') {
			$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
        }

        if (User::updateInfo($userToken, UserColumns::PASSWORD, $password, true) == true) {
            Verification::delete($code);
            $token = App::getInstance(true)->encrypt(date('Y-m-d H:i:s') . $uuid . random_bytes(16) . base64_encode($code));
            User::updateInfo($userToken, UserColumns::ACCOUNT_TOKEN, $token, true);
			$eventManager->emit(AuthEvent::onAuthResetPasswordSuccess(), ['code' => $code]);
			$appInstance->OK('Password has been reset', []);
        } else {
			$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
            $appInstance->BadRequest('Failed to reset password', ['error_code' => 'FAILED_TO_RESET_PASSWORD']);
        }
    } else {
		$eventManager->emit(AuthEvent::onAuthResetPasswordFailed(), ['code' => 'UNKNOWN']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_CODE']);
    }
});
