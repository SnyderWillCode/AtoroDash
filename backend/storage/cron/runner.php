<?php
define('APP_STARTUP', microtime(true));
define('APP_START', microtime(true));
define('APP_PUBLIC', __DIR__);
define('APP_DIR', APP_PUBLIC . '/../../');
define('APP_STORAGE_DIR', APP_DIR . 'storage/');
define('APP_CACHE_DIR', APP_STORAGE_DIR . 'caches');
define('APP_CRON_DIR', APP_STORAGE_DIR . 'cron');
define('APP_LOGS_DIR', APP_STORAGE_DIR . 'logs');
define('APP_ADDONS_DIR', APP_STORAGE_DIR . 'addons');
define('APP_SOURCECODE_DIR', APP_DIR . 'app');
define('APP_ROUTES_DIR', APP_SOURCECODE_DIR . '/Api');
define('APP_DEBUG', true);
define('SYSTEM_OS_NAME', gethostname() . '/' . PHP_OS_FAMILY);
define('SYSTEM_KERNEL_NAME', php_uname('s'));
define('TELEMETRY', true);
define('APP_VERSION', '4.0.0.0-dev');
define('APP_UPSTREAM', 'github.com/mythicalltd/mythicaldash');

require(__DIR__ . '/../packages/autoload.php');

use MythicalClient\Cli\App;
use MythicalClient\Plugins\PluginManager;

$pluginManager = new PluginManager();
$pluginManager->loadKernel();

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