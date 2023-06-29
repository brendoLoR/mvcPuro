<?php

namespace Routes;

use App\Controller\IndexController;
use App\Controller\LoginController;
use App\Controller\UserController;
use App\Middleware\AuthUserMiddleware;

$routes = [
    [
        'route' => '/^login$/',
        'method' => 'POST',
        'action' => [LoginController::class, 'login'],
    ],
    [
        'route' => '/^user$/',
        'method' => 'GET',
        'action' => [UserController::class, 'index'],
        'middlewares' => [AuthUserMiddleware::class,]
    ],
    [
        'route' => '/^user\/(?P<user_id>\d+)$/',
        'parameters' => ['user_id',],
        'method' => 'GET',
        'action' => [UserController::class, 'show'],
        'middlewares' => [AuthUserMiddleware::class,]
    ],
    [
        'route' => '/^user\/(?P<user_id>\d+)$/',
        'parameters' => ['user_id',],
        'method' => 'PUT',
        'action' => [UserController::class, 'update'],
        'middlewares' => [AuthUserMiddleware::class,]
    ],
    [
        'route' => '/^user\/(?P<user_id>\d+)$/',
        'parameters' => ['user_id',],
        'method' => 'DELETE',
        'action' => [UserController::class, 'delete'],
        'middlewares' => [AuthUserMiddleware::class,]
    ],
    [
        'route' => '/^user$/',
        'method' => 'POST',
        'action' => [UserController::class, 'create'],
    ],
    [
        'route' => '/^index$/',
        'method' => 'GET',
        'action' => [IndexController::class, 'index'],
    ],
    [
        'route' => '/^$/',
        'method' => 'GET',
        'action' => [IndexController::class, 'index'],
    ],
];