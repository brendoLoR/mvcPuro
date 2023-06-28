<?php

namespace App\Controller;

use App\Core\Http\Request;
use App\Model\User;

class LoginController extends BaseController
{
    public function login()
    {
        $request = Request::getRequest();
        if (!$validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ])) {
            return $this->response()->status(400)->message("Request error")->json($request->getErrorsMessages());
        }

        if(!$loggedUser = User::attemptLogin($validated['email'], $validated['password'])){
            return $this->response()->status(403)->message("Email or password invalid")->json([]);
        }

        return $this->response()->status(200)->message("logged in")->json(['user' => $loggedUser]);

    }
}