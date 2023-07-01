<?php

namespace App\Core\Http;

class Response
{
    private static self $response;

    public int $statusCode = 200;

    public array $jsonBody = [];

    public string $message = '';
    private string|false $view = false;

    private function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-type: application/json");
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
    }

    public static function getResponse(): Response
    {
        if (!isset(self::$response)) {
            self::$response = new self();
        }

        return self::$response;
    }

    public function status(int $status): static
    {
        $this->statusCode = $status;
        http_response_code($status);
        return $this;
    }

    public function json(array $body): static
    {
        $this->jsonBody = $body;

        return $this;
    }

    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function send(string $message = null, array $body = null, int $status = null, bool $noBody = false): void
    {
        if ($this->view){
            echo $this->view;
            exit();
        }

        if (!is_null($message)) {
            $this->message($message);
        }
        if (!is_null($body)) {
            $this->json($body);
        }
        if (!is_null($status)) {
            $this->status($status);
        }


        $response = json_encode([
            'status' => $this->statusCode,
            'message' => $this->message,
            'data' => $this->jsonBody,
        ]);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new \Exception("Invalid response body", 501);
        }

        echo $response;
        exit();
    }

    /**
     * @throws \Exception
     */
    public function view(string $tamplate)
    {

        if (!file_exists($file = __DIR__ . '/../../Views/' . $tamplate . '.php')) {
            echo $this->status(500)->message('View not found')->send();
            exit();
        }


        ob_start();
        include $file;
        $content = ob_get_contents();
        ob_end_clean();
        header("Content-type: text/html; charset=utf-8");
        $this->view = $content;

        return $this;
    }
}