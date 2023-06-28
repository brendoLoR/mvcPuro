<?php
namespace App\Controller;

use App\Core\Http\Response;

class BaseController
{
    protected function response(): Response
    {
        return Response::getResponse();
    }
}