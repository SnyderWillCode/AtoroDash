<?php

namespace MythicalClient\Cron;

use MythicalClient\App;
use MythicalClient\Chat\Database;
use MythicalClient\Chat\Orders\Orders;
use MythicalClient\Chat\Orders\OrdersConfig;
use MythicalClient\Chat\Services\Services;
use MythicalClient\Chat\User\User;
use MythicalClient\Plugins\PluginDB;
use MythicalClient\Plugins\PluginHelper;
use MythicalClient\Plugins\PluginManager;
use MythicalClient\Plugins\Providers\PluginProvider;
use MythicalClient\Plugins\Providers\PluginProviderHelper;
use MythicalSystems\Utils\BungeeChatApi;

class ServiceWorker
{
	public static function run()
	{
		if (APP_DEBUG == "true") {
			$cron = new Cron('service-worker', '1S');
			//TODO: REMOVE THIS
			Database::runSQL("UPDATE `mythicalclient_orders` SET `status` = 'processed' WHERE `mythicalclient_orders`.`id` = 1;");	
		} else {
			$cron = new Cron('service-worker', '1M');
		}
		$cron->runIfDue(function () {
			$app = \MythicalClient\App::getInstance(true);
			$chat = new \MythicalSystems\Utils\BungeeChatApi;

			$chat->sendOutputWithNewLine('&7Starting ServiceWorker.');

			$orders = Orders::getAllWithStatus('processed');
			foreach ($orders as $order) {
				$id = $order['id'];			
				$oderPrefix = '&7[Order-&5'.$id.'&7] &r';

				switch ($order['status']) {
					case 'processed':
						$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7is processed and ready for deployment.');
						Orders::updateStatus($id, 'deploying');
						$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7is now deploying.');

						$userUUID = $order['user'];
						$serviceID = $order['service'];
						$provider = $order['provider'];
						$days = $order['days_left'];

						self::deployOrder($chat, $app, (int)$id, $userUUID, (int)$serviceID, (int)$provider, (int)$days);

						break;
					case 'failed':
						$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7failed to process.');
						break;
					case 'deploying':
						$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7is already deploying...');
						break;
					case 'deployed':
						$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7has already been deployed.');
						break;
					default:
						$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7is in an unknown status.');
						break;
				}
			}
			$chat->sendOutputWithNewLine('&7ServiceWorker started.');
		});
	}

	private static function deployOrder(BungeeChatApi $chat, App $app, int $id, string $userUUID, int $serviceID, string $provider, int $days)
	{
		global $pluginManager;

		$userTOKEN = User::getTokenFromUUID($userUUID);
		$oderPrefix = '&7[Order-&5'.$id.'&7] &r';

		if (!$userTOKEN) {
			$chat->sendOutputWithNewLine($oderPrefix . '&7User not found.');
			return;
		}
		
		$order = Orders::getOrder((int)$id);
		if (!$order) {
			$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7not found.');
			return;
		}
		
		$orderConfig = OrdersConfig::getOrderConfig((int)$order['id']);
		if (!$orderConfig) {
			$chat->sendOutputWithNewLine($oderPrefix . '&7Order &5' . $id . ' &7Order config not found.');
			return;
		}
		
		$provider = PluginDB::convertIdToName((int) $provider);

		$providers = $pluginManager->getLoadedProviders();
		if (!in_array($provider, $providers)) {
			$chat->sendOutputWithNewLine($oderPrefix . '&7Provider &5' . $provider . ' &7not found.');
			return;
		}
		
		
		$service = Services::getServiceByID((int)$serviceID);
		if (!$service) {
			$chat->sendOutputWithNewLine($oderPrefix . '&7Service not found.');
			return;
		}
		
		$chat->sendOutputWithNewLine($oderPrefix . '&7Deploying order &5' . $id . ' &7to &5' . $provider . ' &7...');
		
		
	}
}