<?php

$router = $di->getRouter();
$router->removeExtraSlashes(true);

// Авторы
$router->addGet('/api/authors', [
    'controller' => 'Authors',
    'action' => 'list',
]);

$router->addPost('/api/authors', [
    'controller' => 'Authors',
    'action' => 'create'
]);

$router->addDelete('/api/authors/{id:[0-9]+}', [
    'controller' => 'Authors',
    'action' => 'delete'
]);

$router->addPut('/api/authors/{id:[0-9]+}', [
    'controller' => 'Authors',
    'action' => 'update'
]);


// Новости
$router->addGet('/api/news', [
    'controller' => 'News',
    'action' => 'list'
]);

$router->addGet('/api/news/{id:[0-9]+}', [
    'controller' => 'News',
    'action' => 'newsItem'
]);

$router->addGet('/api/news/author/{id:[0-9]+}', [
    'controller' => 'News',
    'action' => 'listByAuthor'
]);

$router->addPost('/api/news', [
    'controller' => 'News',
    'action' => 'create'
]);

$router->addDelete('/api/news/{id:[0-90]+}', [
    'controller' => 'News',
    'action' => 'delete'
]);

$router->addPut('/api/news/{id:[0-9]+}', [
    'controller' => 'News',
    'action' => 'update'
]);

$router->handle($_SERVER['REQUEST_URI']);