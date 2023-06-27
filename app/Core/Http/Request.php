<?php

namespace App\Core\Http;

use App\Core\Database\DBQuery;

/**
 * core general propose Request class
 */
final class Request
{
    public static self $request;
    public string $uri;
    public string $method;
    public mixed $body;
    public array $errors = [];

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

    /**
     * @throws \Exception
     */
    public function validate(array $rules): bool|array
    {
        $validations = $this->getValidations();

        $validated = [];
        foreach ($rules as $value => $rules) {
            if (!is_array($rules)) {
                $rules = [$rules];
            }
            foreach ($rules as $rule) {
                $rule = explode(":", $rule);
                if (!isset($validations[$rule[0]])) {
                    throw new \Exception("validation mismatch");
                }

                if (!$validations[$rule[0]]($this->body, $value, $rule[1] ?? null)) {
                    $this->errors[$value] = $this->getMessages()[$rule[0]]($value);
                    return false;
                }

            }
            $validated[$value] = $this->body[$value];
        }
        return $validated;
    }

    public function getErrorsMessages(): bool|array
    {
        return empty($this->errors) ? false : $this->errors;
    }

    private function getValidations(): array
    {
        return [
            'required' => fn($requestBody, $nedded, $attr = null) => isset($requestBody[$nedded]),
            'unique' => fn($requestBody, $nedded, $attr) => is_null(DBQuery::table($attr)
                ->select()
                ->where($nedded, '=', $requestBody[$nedded])
                ->first()),
        ];
    }

    private function getMessages(): array
    {
        return [
            'required' => fn($attribute) => "$attribute is required",
            'unique' => fn($attribute) => "$attribute must be unique",
        ];
    }
}