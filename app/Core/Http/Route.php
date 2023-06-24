<?php

namespace app\Core\Http;

class Route
{
    public function __invoke($routes, $uri)
    {
        $method = Request::getRequest()->method;

        [$route, $matches] = $this->getResolvedRoute($routes, $uri, $method);
        $parametesNedde = $route['parameters'] ?? [];
        $parameters = [];
        foreach ($parametesNedde as $paramete) {
            $parameters[$paramete] = $matches[$paramete];
        }

        return ['action' => $route['action'], 'param' => $parameters, 'middlewares' => $route['middlewares'] ?? []];
    }

    /**
     * @throws \Exception
     */
    private function getResolvedRoute($routes, $uri, $method)
    {
        if (strpos($uri, '/') == 0) {
            $uri = substr($uri, 1);
        }
        if (strrpos($uri, '/') == strlen($uri) - 1) {
            $uri = substr($uri, 0, -1);
        }
        foreach ($routes as $route) {

            if (preg_match($route['route'], $uri, $matches) && strtoupper($method) == strtoupper($route['method'])) {
                return [$route, $matches];
            }
        }

        Response::getResponse()->status(404)->send('Route not found');
    }
}