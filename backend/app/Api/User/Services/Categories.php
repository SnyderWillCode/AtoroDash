<?php

use MythicalClient\App;
use MythicalClient\Chat\Services\ServiceCategories;
use MythicalClient\Chat\User\Session;


$router->add('/api/user/services/categories', function () {
	App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    new Session($appInstance);

	$appInstance->OK('Here you go, cuz i heard you want some announcements!', ['categories' => ServiceCategories::getAll()]);

});
