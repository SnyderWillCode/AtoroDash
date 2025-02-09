<?php

use MythicalClient\App;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\User\Session;

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


// Route handlers
$router->post('/api/webhooks/paypal', function() {
	$paypal = new MythicalClient\Gateways\PayPal();
	$paypal->handleIPN();
});

$router->get('/api/paypal/process', function() {
	App::init();
	$app = App::getInstance(true);
	$session = new Session($app);

	try {
		if (!isset($_GET['coins']) || empty($_GET['coins'])) {
			throw new \InvalidArgumentException('Missing coins parameter');
		}

		$paypal = new MythicalClient\Gateways\PayPal();
		$redirectUrl = $paypal->createPayment(
			(float)$_GET['coins'],
			$session->getInfo(UserColumns::UUID, false)
		);

		header("Location: $redirectUrl");
		exit;
	} catch (\Throwable $e) {
		$app->getLogger()->error('PayPal process error: ' . $e->getMessage());
		header('Location: /?error=payment_failed&message=' . $e->getMessage());
		exit;
	}
});

$router->add('/api/paypal/finish', function() {
	App::init();
	$app = App::getInstance(true);

	header('Location: /dashboard');
	exit;
});
