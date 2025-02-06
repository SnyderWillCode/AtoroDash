<?php
use MythicalClient\App;
use MythicalClient\Plugins\PluginDB;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\Orders\Orders;
use MythicalClient\Chat\Services\Services;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\Orders\OrdersConfig;
use MythicalClient\Chat\Orders\OrdersInvoices;
use MythicalClient\Chat\Services\ServiceCategories;
use MythicalClient\Plugins\Providers\PluginProviderHelper;


$router->add('/api/user/addfunds', function () {
	global $pluginManager;
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyPOST();
    $session = new Session($appInstance);

	
});
