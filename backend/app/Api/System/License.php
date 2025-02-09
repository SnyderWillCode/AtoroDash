<?php

use MythicalClient\App;

$router->add('/api/system/license', function (): void {
	App::init();
	$appInstance = App::getInstance(true);
	$config = $appInstance->getConfig();

	$licenseValidator = $appInstance->getLicenseValidator();
	if ($licenseValidator->validate()) {
		$appInstance->OK("License is valid!", []);
	} else {
		$appInstance->BadRequest("License is not valid!", []);
	}
});
