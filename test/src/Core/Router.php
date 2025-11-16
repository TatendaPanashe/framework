<?php

namespace Tiny\Core;

class Router
{
    private array $routes = [];

    public function get($path, $handler)
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler)
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function put($path, $handler)
    {
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete($path, $handler)
    {
        $this->routes['DELETE'][$path] = $handler;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function resolve($request, $response)
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        $handler = $this->routes[$method][$path] ?? false;

        if (!$handler) {
            $response->setStatusCode(404);
            return "404 Not Found - $path";
        }

        if (is_array($handler)) {
            $controller = new $handler[0];
            return $controller->{$handler[1]}($request);
        }

        return call_user_func($handler, $request);
    }
}