<?php

namespace App\Controller;

use App\Core\Http\Response;
use App\Model\Drink;
use App\Model\User;

class UserDrinkController extends BaseController
{
    /**
     * @throws \Exception
     */
    public function drink($user_id): Response
    {
        if ($this->request()->user()->getAttribute('id') != $user_id) {
            return $this->abort();

        }
        if (!$validated = $this->request()->validate([
            'drink' => ['required', 'instanceOf:' . FILTER_VALIDATE_INT],
        ])) {
            return $this->abort(400, "Request error");
        }

        if (!Drink::create([
            'user_id' => $user_id,
            ...$validated
        ])) {
            return $this->abort(501, "Error on server");
        }

        return $this->response()->status(200)->message("Drink registreded")->json([
            'user' => User::find($user_id)->attributes,
        ]);
    }


}