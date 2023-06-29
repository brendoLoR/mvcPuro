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
            return $this->abort(400, "Request error");
        }

        if(!$loggedUser = User::attemptLogin($validated['email'], $validated['password'])){
            return $this->abort(403, "user does not exist or that the password is invalid");
        }

        return $this->response()->status(200)->message("logged in")->json(['token' => $loggedUser]);

    }
}