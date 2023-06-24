<?php
namespace App\Controller;
use app\Core\Http\Response;

class IndexController extends BaseController
{
    public function index(): \app\Core\Http\Response
    {
        return Response::getResponse()->status(200)
            ->message('Index')
            ->json([
                'teste' => 'resultado'
            ]);
    }

}