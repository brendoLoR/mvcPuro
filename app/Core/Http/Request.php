<?php

namespace App\Core\Http;

use App\Core\Database\DBQuery;
use App\Model\User;
use Exception;

/**
 * core general propose Request class
 */
final class Request
{
    public static self $request;
    public string $uri;
    public string $method;
    public mixed $body;
    public array|bool $headers;
    public string|bool $authorizarion;
    public array $errors = [];
    private User $user;

    private function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = $this->getRequestData();
        $this->headers = getallheaders();

        $this->authorizarion = isset($this->headers['Authorization']) ?
            str_replace('Bearer ', '', $this->headers['Authorization']) :
            false;
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
        $method = $_SERVER['REQUEST_METHOD'];

        return match ($method) {
            'GET' => $_GET,
            'POST' => $_POST,
            'PUT' => $this->getPut(),
            default => []
        };

    }

    private static function exists($field, mixed $value, $table)
    {
        $table = explode(',', $table);
        $query = DBQuery::table($table[0])
            ->select()
            ->where($field, '=', $value);
        if (isset($table[1])) {
            $query->where('id', '<>', $table[1]);
        }
        return $query->first();
    }

    /**
     * @throws Exception
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
                    throw new Exception("validation mismatch");
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
            'nullable' => fn($requestBody, $nedded, $attr = null) => true,
            'equals' => fn($requestBody, $nedded, $attr = null) => $requestBody[$nedded] == $attr,
            'email' => fn($requestBody, $nedded, $attr = null) => filter_var($requestBody[$nedded], FILTER_VALIDATE_EMAIL),
            'instanceOf' => fn($requestBody, $nedded, $attr = null) => filter_var($requestBody[$nedded], intval($attr)),
            'unique' => fn($requestBody, $nedded, $attr) => (self::exists($nedded, $requestBody[$nedded], $attr)) == false,
            'exists' => fn($requestBody, $nedded, $attr) => (self::exists($nedded, $requestBody[$nedded], $attr)) != false,
        ];
    }

    private function getMessages(): array
    {
        return [
            'required' => fn($attribute) => "$attribute is required",
            'instanceOf' => fn($attribute) => "$attribute is invalid",
            'email' => fn($attribute) => "$attribute must be valid email",
            'unique' => fn($attribute) => "$attribute must be unique",
            'exists' => fn($attribute) => "$attribute must exists",
        ];
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function user(): User|bool
    {
        return $this->user ?? false;
    }

    /**
     * @return array
     */
    private function getPut(): array
    {
        parse_str(file_get_contents("php://input"), $putData);
        foreach ($putData as $key => $value) {
            unset($putData[$key]);
            $putData[str_replace('amp;', '', $key)] = $value;
        }

        return $putData;
    }
}