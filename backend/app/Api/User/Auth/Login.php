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

namespace MythicalClient\Api\User\Auth;

use MythicalClient\App;
use MythicalClient\Mail\Mail;
use MythicalClient\Chat\User\User;
use MythicalSystems\CloudFlare\Turnstile;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;
use MythicalClient\Plugins\Events\Events\AuthEvent;

$router->add('/api/user/auth/login', function (): void {
	global $eventManager;
	$appInstance = App::getInstance(true);
	$config = $appInstance->getConfig();

	$appInstance->allowOnlyPOST();

	// Check login field
	if (!isset($_POST['login']) || $_POST['login'] == '') {
		$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => 'UNKNOWN', 'error_code' => 'MISSING_LOGIN']);
		$appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_LOGIN']);
	}

	// Check password field
	if (!isset($_POST['password']) || $_POST['password'] == '') {
		$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $_POST['login'], 'error_code' => 'MISSING_PASSWORD']);
		$appInstance->BadRequest('Bad Request', ['error_code' => 'MISSING_PASSWORD']);
	}

	// Process turnstile if enabled
	if ($appInstance->getConfig()->getSetting(ConfigInterface::TURNSTILE_ENABLED, 'false') == 'true') {
		if (!isset($_POST['turnstileResponse']) || $_POST['turnstileResponse'] == '') {
			$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $_POST['login'], 'error_code' => 'TURNSTILE_FAILED']);
			$appInstance->BadRequest('Bad Request', ['error_code' => 'TURNSTILE_FAILED']);
		}

		$cfTurnstileResponse = $_POST['turnstileResponse'];
		if (!Turnstile::validate($cfTurnstileResponse, CloudFlareRealIP::getRealIP(), $config->getSetting(ConfigInterface::TURNSTILE_KEY_PRIV, 'XXXX'))) {
			$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $_POST['login'], 'error_code' => 'TURNSTILE_FAILED']);
			$appInstance->BadRequest('Invalid TurnStile Key', ['error_code' => 'TURNSTILE_FAILED']);
		}
	}

	$login = $_POST['login'];
	$password = $_POST['password'];

	$loginResult = User::login($login, $password);
	if ($loginResult == 'false') {
		$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $login, 'error_code' => 'INVALID_CREDENTIALS']);
		$appInstance->BadRequest('Invalid login credentials', ['error_code' => 'INVALID_CREDENTIALS']);
	}

	// Check account verification if mail is enabled
	if (User::getInfo($login, UserColumns::VERIFIED, false) == 'false' && Mail::isEnabled()) {
		User::logout();
		$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $login, 'error_code' => 'ACCOUNT_NOT_VERIFIED']);
		$appInstance->BadRequest('Account not verified', ['error_code' => 'ACCOUNT_NOT_VERIFIED']);
	}

	// Check if account is banned
	if (User::getInfo($login, UserColumns::BANNED, false) != 'NO') {
		User::logout();
		$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $login, 'error_code' => 'ACCOUNT_BANNED']);
		$appInstance->BadRequest('Account is banned', ['error_code' => 'ACCOUNT_BANNED']);
	}

	// Check if account is deleted
	if (User::getInfo($login, UserColumns::DELETED, false) == 'true') {
		User::logout();
		$eventManager->emit(AuthEvent::onAuthLoginFailed(), ['login' => $login, 'error_code' => 'ACCOUNT_DELETED']);
		$appInstance->BadRequest('Account is deleted', ['error_code' => 'ACCOUNT_DELETED']);
	}

	// Handle 2FA if enabled
	if (User::getInfo($login, UserColumns::TWO_FA_ENABLED, false) == 'true') {
		User::updateInfo($login, UserColumns::TWO_FA_BLOCKED, 'true', false);
	}

	// Set cookie based on debug mode
	if (APP_DEBUG) {
		// Set the cookie to expire in 1 year if the app is in debug mode
		setcookie('user_token', $login, time() + 3600 * 31 * 360, '/');
	} else {
		setcookie('user_token', $login, time() + 3600, '/');
	}

	// Emit successful login event before sending response
	$eventManager->emit(AuthEvent::onAuthLoginSuccess(), ['login' => $login]);
	$appInstance->OK('Successfully logged in', []);
});
