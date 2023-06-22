<?php

namespace Routes;

use App\Controller\IndexController;
use App\Controller\UserController;
use App\Middleware\AuthUserMiddleware;

$routes = [
    [
        'route' => '/^user\/(?P<user_id>\d+)$/',
        'parameters' => ['user_id',],
        'method' => 'GET',
        'action' => [UserController::class, 'index'],
        'middlewares' => [AuthUserMiddleware::class,]
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