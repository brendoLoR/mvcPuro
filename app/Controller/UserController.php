<?php

namespace App\Controller;

use App\Core\Database\DBQuery;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Model\User;

class UserController extends BaseController
{
    public function index(): Response
    {
        return $this->response()->status(200)
            ->message('Index')
            ->json([
                'user' => User::all(),
            ]);
    }

    public function show($user_id): Response
    {
        
        return $this->response()->status(200)
            ->message('Index')
            ->json([
                'user' => User::find($user_id)->attributes,
            ]);
    }

    /**
     * @throws \Exception
     */
    public function create(): Response
    {
        $request = Request::getRequest();
        if (!$validated = $request->validate([
                'name' => 'required',
                'email' => ['required', 'email', 'unique:users',],
                'password' => 'required',
            ])) {
            return $this->response()->status(400)->message("Request error")->json($request->getErrorsMessages());
        }

        if (!($user = (new User($validated))->save())) {
            return $this->response()->status(501)->message("Error on server")->json([
                'status' => 501
            ]);
        }

        return $this->response()->status(200)->message("User created")->json([
            'userId' => $user->getAttribute('id') ?? '',
        ]);
    }


    public function delete($user_id): Response
    {
        return $this->response()->status(200)
            ->message('Index')
            ->json([
                'deleted' => (new User())->delete($user_id),
            ]);
    }

}