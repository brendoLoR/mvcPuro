<?php
namespace App\Controller;

use App\Core\Http\Request;
use App\Core\Http\Response;

class BaseController
{
    protected function response(): Response
    {
        return Response::getResponse();
    }
    protected function request(): Request
    {
        return Request::getRequest();
    }

    /**
     * @param int|null $status
     * @param string|null $message
     * @return Response
     */
    protected function abort(?int $status = 403, ?string $message = "Not authorized"): Response
    {
        return $this->response()->status($status)
            ->message($message)
            ->json(['errors' => $this->request()->getErrorsMessages()]);
    }
}