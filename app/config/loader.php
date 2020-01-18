<?php

$loader = new \Phalcon\Loader();

$loader->registerNamespaces([
    'Api' => $config->application->libraryDir,
])->register();

$loader->registerDirs([
    $config->application->controllersDir,
    $config->application->modelsDir,
])->register();
