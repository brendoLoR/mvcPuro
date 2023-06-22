<?php
namespace App\Controller;
use App\Core\Response;

class IndexController extends BaseController
{
    public function index(): \App\Core\Response
    {
        return Response::getResponse()->status(200)
            ->message('Index')
            ->json([
                'teste' => 'resultado'
            ]);
    }

}