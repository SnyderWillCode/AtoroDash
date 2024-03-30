<?php 
use MythicalDash\Kernel\Kernel;

try {
    if (file_exists('../vendor/autoload.php')) {
        require('../vendor/autoload.php');
    } else {
        die('Hello, it looks like you did not run: "<code>composer install --no-dev --optimize-autoloader</code>". Please run that and refresh the page');
    }
} catch (Exception $e) {
    die('Hello, it looks like you did not run: <code>composer install --no-dev --optimize-autoloader</code> Please run that and refresh');
}
use Smarty\Smarty;
use MythicalDash\Handlers\ConfigHandler as cfg; 
use MythicalSystems\Api\ResponseHandler as rsp;
use MythicalSystems\Api\Api as api;
$router = new \Router\Router();
$renderer = new Smarty();

define('DIR_TEMPLATE', __DIR__. '/../themes/default');
define('DIR_CACHE', __DIR__.'/../cache');
define('DIR_COMPILE', __DIR__.'/../compile');
define('DIR_CONFIG', __DIR__.'/../config');

if (!Kernel::checkRenderDirs(DIR_CACHE,DIR_CONFIG,DIR_COMPILE,DIR_TEMPLATE) == TRUE) {
    die("We are sorry but we have no read/write permissions to our directory\n chown -R www-data:www-data /var/www/mythicaldash/*");
}
$renderer->setTemplateDir(DIR_TEMPLATE);
$renderer->setCacheDir(DIR_CACHE);
$renderer->setCompileDir(DIR_COMPILE);
$renderer->setConfigDir(DIR_CONFIG);
/**
 * Don't know what this line of code does! :(
 */
$renderer->setEscapeHtml(true);

// Routes for API
$routesAPIDirectory = __DIR__ . '/../routes/api/';
$iterator2 = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesAPIDirectory));
$phpApiFiles = new RegexIterator($iterator2, '/\.php$/');

foreach ($phpApiFiles as $phpApiFile) {
    try {
        include $phpApiFile->getPathname();
    } catch (Exception $ex) {
        api::init();
        rsp::InternalServerError($e->getMessage());
    }
}

$router->add('/api/(.*)', function () {
    api::init();
    rsp::NotFound("The api route does not exist!");
});

$routesViewDirectory = __DIR__ . '/../routes/views/';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($routesViewDirectory));
$phpViewFiles = new RegexIterator($iterator, '/\.php$/');

foreach ($phpViewFiles as $phpViewFile) {
    try {
        http_response_code(200);
        include $phpViewFile->getPathname();
    } catch (Exception $ex) {
        http_response_code(500);
        die('Failed to start app: ' . $ex->getMessage());
    }
}

$router->add('/(.*)', function () {
    die("Route not found!");
});

try {
    $router->route();
} catch (Exception $e) {
    die('Failed to start app: ' . $e->getMessage());
}
