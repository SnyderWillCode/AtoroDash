<?php
define('APP_STARTUP', microtime(true));
require(__DIR__ . '/../packages/autoload.php');

use MythicalClient\Cli\App;


App::sendOutputWithNewLine('&7Starting MythicalClient cron runner.');

foreach (glob(__DIR__ . '/php/*.php') as $file) {
	App::sendOutputWithNewLine("");
	App::sendOutputWithNewLine("|----");
	require_once $file;
	$className = 'MythicalClient\Cron\\' . basename($file, '.php');
	try {
		if (class_exists($className)) {
			$worker = new $className();
			App::sendOutputWithNewLine('&7Running &d' . $className. '&7.');
			$worker->run();
			App::sendOutputWithNewLine('&7Finished running &d' . $className . '&7.');
		} else {
			App::sendOutputWithNewLine('&7Class &d' . $className . '&7 not found');
		}
	} catch (\Exception $e) {
		App::sendOutputWithNewLine('&7Error running &d' . $className . '&7: &c' . $e->getMessage());
	}
}
App::sendOutputWithNewLine("|----");
App::sendOutputWithNewLine("");
App::sendOutputWithNewLine('&7Finished running all cron workers.');
App::sendOutputWithNewLine('&7Total execution time: &d' . round(microtime(true) - APP_STARTUP, 2) . 's');