<?php

namespace App\Core\Http;

/**
 * core general propose Request class
 */
final class Request
{
    public static self $request;
    public string $uri;
    public string $method;
    public mixed $body;

    private function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = $this->getRequestData();

    }

    /**
     * implements sigletom pattern for request
     * @return Request
     */
    public static function getRequest(): Request
    {
        if (!isset(self::$request)) {
            self::$request = new self();
        }

        return self::$request;
    }

    private function getRequestData(): mixed
    {
        if (!empty($_POST)) {
            return $_POST;
        }

        $post = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $post;
        }

        return [];
    }

}