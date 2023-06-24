<?php
namespace App\Controller;
use App\Core\Database\DBQuery;
use app\Core\Http\Response;

class UserController extends BaseController
{
    public function index($user_id): \app\Core\Http\Response
    {
        return Response::getResponse()->status(200)
            ->message('Index')
            ->json([
                'teste' => 'resultado',
                'user' => DBQuery::table('users')
                    ->select(['name', 'email'])
                    ->where('id', '=', "$user_id")
                    ->first(),
            ]);
    }

}