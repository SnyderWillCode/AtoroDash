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
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;

$router->add('/api/user/auth/register', function (): void {
    global $eventManager;
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
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['firstName' => 'UNKNOWN', 'error_code' => 'MISSING_FIRST_NAME']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_FIRST_NAME']);
    }
    if (!isset($_POST['lastName']) || $_POST['lastName'] == '') {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['lastName' => 'UNKNOWN', 'error_code' => 'MISSING_LAST_NAME']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_LAST_NAME']);
    }
    if (!isset($_POST['email']) || $_POST['email'] == '') {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['email' => 'UNKNOWN', 'error_code' => 'MISSING_EMAIL']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_EMAIL']);
    }
    if (!isset($_POST['password']) || $_POST['password'] == '') {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['password' => 'UNKNOWN', 'error_code' => 'MISSING_PASSWORD']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD']);
    }
    if (!isset($_POST['username']) || $_POST['username'] == '') {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['username' => 'UNKNOWN', 'error_code' => 'MISSING_USERNAME']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_USERNAME']);
    }

    // Add validation for first name (only letters)
    if (!preg_match('/^[a-zA-Z]+$/', $_POST['firstName'])) {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['firstName' => $_POST['firstName'], 'error_code' => 'INVALID_FIRST_NAME']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_FIRST_NAME']);
    }

    // Add validation for last name (only letters)
    if (!preg_match('/^[a-zA-Z]+$/', $_POST['lastName'])) {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['lastName' => $_POST['lastName'], 'error_code' => 'INVALID_LAST_NAME']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_LAST_NAME']);
    }

    // Add validation for username (alphanumeric, no spaces or special chars)
    if (!preg_match('/^[a-zA-Z0-9]+$/', $_POST['username'])) {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['username' => $_POST['username'], 'error_code' => 'INVALID_USERNAME']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_USERNAME']);
    }

    // Add validation for email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['email' => $_POST['email'], 'error_code' => 'INVALID_EMAIL']);
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_EMAIL']);
    }

    /**
     * Process the turnstile response.
     *
     * IF the turnstile is enabled
     */
    if ($appInstance->getConfig()->getSetting(ConfigInterface::TURNSTILE_ENABLED, 'false') == 'true') {
        if (!isset($_POST['turnstileResponse']) || $_POST['turnstileResponse'] == '') {
			$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['error_code' => 'MISSING_TURNSTILE_RESPONSE']);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'TURNSTILE_FAILED']);
        }
        $cfTurnstileResponse = $_POST['turnstileResponse'];
        if (!Turnstile::validate($cfTurnstileResponse, CloudFlareRealIP::getRealIP(), $config->getSetting(ConfigInterface::TURNSTILE_KEY_PRIV, 'XXXX'))) {
			$eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['error_code' => 'TURNSTILE_FAILED']);
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
            $eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['error_code' => 'USERNAME_ALREADY_IN_USE']);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'USERNAME_ALREADY_IN_USE']);
        }
        if (User::exists(UserColumns::EMAIL, $email)) {
            $eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['error_code' => 'EMAIL_ALREADY_IN_USE']);
            $appInstance->BadRequest('Bad Request', ['error_code' => 'EMAIL_ALREADY_IN_USE']);
        }
        User::register($username, $password, $email, $firstName, $lastName, CloudFlareRealIP::getRealIP());
        $eventManager->emit(AuthEvent::onAuthRegisterSuccess(), ['username' => $username, 'email' => $email]);
        App::OK('User registered', []);

    } catch (Exception $e) {
        $eventManager->emit(AuthEvent::onAuthRegisterFailed(), ['error_code' => 'DATABASE_ERROR']);
        $appInstance->InternalServerError('Internal Server Error', ['error_code' => 'DATABASE_ERROR']);
    }

});
