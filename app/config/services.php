<?php

use Phalcon\Http\Response;

$di->setShared('config', function() {
    return include APP_PATH . '/config/config.php';
});

$di->setShared('view', function() {
    return new \Phalcon\Mvc\View();
});

$di->setShared('db', function() {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    return new $class($params);
});

$di->setShared('response', function() {
    $response = new Response();
    $response->setContentType('application/json', 'utf-8');
    $response->setHeader('Access-Control-Allow-Origin', '*');
    $response->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE');
    $response->sendHeaders();
    return $response;
});