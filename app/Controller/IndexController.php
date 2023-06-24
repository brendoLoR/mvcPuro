<?php
namespace App\Controller;

use App\Core\Http\Response;

class IndexController extends BaseController
{
    public function index(): Response
    {
        return Response::getResponse()->status(200)
            ->message('Index')
            ->json([
                'teste' => 'resultado'
            ]);
    }

}