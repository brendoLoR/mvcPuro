<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

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