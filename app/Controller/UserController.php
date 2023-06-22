<?php
namespace App\Controller;
use App\Core\Response;

class UserController extends BaseController
{
    public function index($user_id): \App\Core\Response
    {
        return Response::getResponse()->status(200)
            ->message('Index')
            ->json([
                'teste' => 'resultado',
                'userId' => $user_id
            ]);
    }

}