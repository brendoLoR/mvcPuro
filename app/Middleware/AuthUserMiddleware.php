<?php

namespace App\Middleware;

use app\Core\Http\Request;
use app\Core\Http\Response;
use App\Model\User;

class AuthUserMiddleware extends BaseMiddleware
{
    public function __invoke(Request $request): bool
    {
        if($request->authorizarion !== false && $user = User::checkToken($request->authorizarion)){
            Request::getRequest()->setUser($user);
            return true;
        }

        Response::getResponse()->status(403)->send('Unauthorized request');
        return false;
    }
}