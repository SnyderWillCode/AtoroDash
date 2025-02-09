<?php

namespace MythicalClient\Gateways;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use MythicalClient\App;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\Gateways\PayPalDB;
use MythicalClient\Chat\User\User;
use MythicalClient\Config\ConfigInterface;

class PayPal {
	private const SANDBOX_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	private const LIVE_URL = 'https://www.paypal.com/cgi-bin/webscr';
	private const IPN_SANDBOX_URL = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
	private const IPN_LIVE_URL = 'https://ipnpb.paypal.com/cgi-bin/webscr';

	private App $app;
	private Client $client;
	private bool $isSandbox;
	private string $businessEmail;
	private string $appUrl;

	public function __construct() {
		$this->app = App::getInstance(true);
		$this->isSandbox = $this->app->getConfig()->getSetting(ConfigInterface::PAYPAL_IS_SANDBOX, 'false') === 'true';
		$this->businessEmail = $this->app->getConfig()->getSetting(ConfigInterface::PAYPAL_CLIENT_ID, '');
		$this->appUrl = $this->app->getConfig()->getSetting(ConfigInterface::APP_URL, '');

		$this->client = new Client([
			'timeout' => 30,
			'connect_timeout' => 10,
			'verify' => true,
			'http_errors' => false,
			'headers' => [
				'User-Agent' => 'MythicalClient-PayPal/1.0',
				'Connection' => 'Close'
			]
		]);

		if (empty($this->businessEmail)) {
			throw new \RuntimeException('PayPal business email not configured');
		}
	}

	public function handleIPN(): void {
		try {
			$postData = $this->getPostData();
			$verificationResponse = $this->verifyIPNMessage($postData);

			if ($verificationResponse === 'VERIFIED') {
				$this->processVerifiedIPN($postData);
			}

			header('HTTP/1.1 200 OK');
		} catch (\Throwable $e) {
			header('HTTP/1.1 500 Internal Server Error');
		}
	}

	public function createPayment(float $amount, string $uuid): string {
		try {
			$code = bin2hex(random_bytes(16));
			PayPalDB::create($code, $amount, $uuid);

			$params = [
				'cmd' => '_xclick',
				'business' => $this->businessEmail,
				'item_name' => 'Account Topup',
				'item_number' => $code,
				'amount' => number_format($amount, 2, '.', ''),
				'currency_code' => 'EUR',
				'custom' => $code . '|' . $uuid,
				'no_shipping' => '1',
				'no_note' => '1',
				'charset' => 'UTF-8',
				'rm' => '2',
				'return' => "https://{$this->appUrl}/api/paypal/finish",
				'cancel_return' => "https://{$this->appUrl}/dashboard",
				'notify_url' => "https://{$this->appUrl}/api/webhooks/paypal",
				'bn' => 'MythicalClient_BuyNow_WPS_US'
			];

			return ($this->isSandbox ? self::SANDBOX_URL : self::LIVE_URL) . 
				   '?' . http_build_query($params);

		} catch (\Throwable $e) {
			$this->app->getLogger()->error('Failed to create PayPal payment: ' . $e->getMessage());
			throw $e;
		}
	}

	private function getPostData(): array {
		$raw = file_get_contents('php://input');
		parse_str($raw, $postData);
		return $postData;
	}

	private function verifyIPNMessage(array $postData): string {
		try {
			$verifyData = array_merge(['cmd' => '_notify-validate'], $postData);
			
			$response = $this->client->post(
				$this->isSandbox ? self::IPN_SANDBOX_URL : self::IPN_LIVE_URL,
				[
					RequestOptions::FORM_PARAMS => $verifyData,
					RequestOptions::HEADERS => [
						'User-Agent' => 'MythicalClient-PayPal-IPN/1.0',
						'Connection' => 'Close'
					]
				]
			);

			return (string) $response->getBody();

		} catch (GuzzleException $e) {
			throw new \RuntimeException("IPN Verification Failed: {$e->getMessage()}");
		}
	}

	private function processVerifiedIPN(array $postData): void {
		if ($postData['payment_status'] !== 'Completed') {
			return;
		}

		[$code, $uuid] = explode('|', $postData['custom']);

		if (!PayPalDB::exists($code)) {
			throw new \RuntimeException("Payment code not found: $code");
		}

		if (!PayPalDB::isPending($code)) {
			return;
		}

		$this->creditUserAccount($code, $uuid);
	}

	private function creditUserAccount(string $code, string $uuid): void {
		PayPalDB::updateStatus($code, 'processed');
		
		$token = User::getTokenFromUUID($uuid);
		$currentCredits = User::getInfo($token, UserColumns::CREDITS, false);
		$payment = PayPalDB::getByCode($code);
		
		User::updateInfo(
			$token, 
			UserColumns::CREDITS, 
			$currentCredits + $payment['coins'],
			false
		);
	}
}
