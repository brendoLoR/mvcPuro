<?php

namespace App\Controller;

use App\Core\Database\DBQuery;
use App\Core\Http\Request;
use App\Core\Http\Response;

class UserController extends BaseController
{
    public function index($user_id): Response
    {
        return Response::getResponse()->status(200)
            ->message('Index')
            ->json([
                'user' => DBQuery::table('users')
                    ->select(['name', 'email'])
                    ->where('id', '=', "$user_id")
                    ->first(),
            ]);
    }

    public function create(): Response
    {
        $request = Request::getRequest();
        if (!$validated = $request
            ->validate([
                'name' => 'required',
                'email' => ['required', 'unique:users'],
                'password' => 'required',
            ])) {
            return Response::getResponse()->status(400)
                ->message("Request error")
                ->json($request->getErrorsMessages());
        }

        if (!($user = DBQuery::table('users')
            ->insert($validated))) {
            return Response::getResponse()->status(501)
                ->message("Error on server")
                ->json([
                    'status' => 501
                ]);
        }
        return Response::getResponse()->status(200)
            ->message("User created")
            ->json([
                'userId' => $user['id'] ?? '',
            ]);

    }

}