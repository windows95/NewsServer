<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Application;

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

try
{
    $di = new FactoryDefault();

    require_once(APP_PATH . '/config/router.php');
    require_once(APP_PATH . '/config/services.php');

    $config = $di->getConfig();

    require_once(APP_PATH . '/config/loader.php');

    $app = new Application($di);

    echo $app->handle($_SERVER['REQUEST_URI'])->getContent();
}
catch (\Exception $e)
{
    echo $e->getMessage();
}