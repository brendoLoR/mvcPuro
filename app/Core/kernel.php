<?php

namespace App\Core;

use App\Core\Database\DB;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\Route;

/**
 * initialize Request and Response
 */
DB::init();
$request = Request::getRequest();
Response::getResponse();

/**
 * load routes
 */
require_once __DIR__ . '/../routes/api.php';

$action = ((new Route())($routes, $request->uri));

foreach ($action['middlewares'] as $middleware) {
    if ((new $middleware)($request)) {
        continue;
    }
    try {
        Response::getResponse()->status(419)->send();
    } catch (\Exception $e) {
        Response::getResponse()->status(500)->send(noBody: true);

    }
}

/**
 * inicialize controller and call action
 */
$controller = new $action['action'][0]();
$response = call_user_func_array([$controller, $action['action'][1]], $action['param']);
/**
 * send response
 */
if ($response instanceof Response) {
    try {
        $response->send();
    } catch (\Exception $e) {
        Response::getResponse()->status(500)->send(noBody: true);
    }
}
