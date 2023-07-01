<?php

namespace App\Controller;

use App\Core\Http\Response;
use App\Model\User;

class UserController extends BaseController
{
    public function index(): Response
    {
        return $this->response()->status(200)
            ->message('Index')
            ->json([
                'user' => User::paginate(),
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

        if (!$validated = $this->request()->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users',],
            'password' => 'required',
        ])) {
            return $this->abort(400, "Request error");
        }

        if (!($user = User::create($validated))) {
            return $this->abort(501, "Error on server");
        }

        return $this->response()->status(200)->message("User created")->json([
            'userId' => $user->getAttribute('id') ?? '',
        ]);
    }

    /**
     * @throws \Exception
     */
    public function update($user_id): Response
    {
        if ($this->request()->user()->getAttribute('id') != $user_id) {
            return $this->abort();

        }
        if (!$validated = $this->request()->validate([
            'name' => 'required',
            'email' => ['required', 'email', 'unique:users,' . $user_id],
            'password' => ['nullable'],
        ])) {
            return $this->abort(400, "Request error");
        }

        if(!$user = User::find($user_id, filter: false)){
            return $this->abort(404, "User not found");
        }

        if (!$user->update($validated)) {
            return $this->abort(501, "Error on server");
        }

        return $this->response()->status(200)->message("User updated")->json([
            'userId' => $user->getAttribute('id') ?? '',
        ]);
    }


    public function delete($user_id): Response
    {
        if ($this->request()->user()->getAttribute('id') != $user_id) {
            return $this->abort();
        }
        return $this->response()->status(200)
            ->message('Index')
            ->json([
                'deleted' => (new User())->delete($user_id),
            ]);
    }

}