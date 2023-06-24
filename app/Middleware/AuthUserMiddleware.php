<?php

namespace App\Middleware;

use app\Core\Http\Request;
use app\Core\Http\Response;

class AuthUserMiddleware extends BaseMiddleware
{
    public function __invoke(Request $request): bool
    {
        if(true){
            return true;
        }

        Response::getResponse()->status(403)->send('Unauthorized request');
    }
}