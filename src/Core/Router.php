<?php

namespace Kodomo\Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'PATCH' => [],
        'DELETE' => [],
        'OPTIONS' => []
    ];

    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    public function put($path, $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function patch($path, $handler)
    {
        $this->addRoute('PATCH', $path, $handler);
    }

    public function delete($path, $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    public function options($path, $handler)
    {
        $this->addRoute('OPTIONS', $path, $handler);
    }

    public function any($path, $handler)
    {
        foreach (array_keys($this->routes) as $method) {
            $this->addRoute($method, $path, $handler);
        }
    }

    private function addRoute($method, $path, $handler)
    {
        $this->routes[$method][$path] = $handler;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function resolve(Request $request, Response $response)
    {
        $path = $request->getPath();
        $method = $request->getMethod();

        // Try exact match first
        $handler = $this->routes[$method][$path] ?? null;

        // If no exact match, try pattern matching for routes with parameters
        if (!$handler) {
            $handler = $this->matchPattern($method, $path);
        }

        if (!$handler) {
            $response->setStatusCode(404);
            return $this->handleNotFound($request);
        }

        return $this->executeHandler($handler, $request, $response);
    }

    private function matchPattern($method, $path)
    {
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            // Convert route pattern to regex
            $pattern = $this->convertRouteToRegex($route);
            
            if (preg_match($pattern, $path, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                return [
                    'handler' => $handler,
                    'params' => array_values($params)
                ];
            }
        }
        
        return null;
    }

    private function convertRouteToRegex($route)
    {
        // Replace {param} with named capture groups
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
        return "#^" . $pattern . "$#";
    }

    private function executeHandler($handler, Request $request, Response $response)
    {
        if (is_array($handler) && isset($handler['handler'])) {
            // Pattern-matched route with parameters
            $actualHandler = $handler['handler'];
            $params = $handler['params'] ?? [];
        } else {
            $actualHandler = $handler;
            $params = [];
        }

        if (is_array($actualHandler)) {
            // Controller method: [Controller::class, 'method']
            $controller = new $actualHandler[0]();
            $method = $actualHandler[1];
            
            // Pass request, response, and route parameters
            return $controller->$method($request, $response, ...$params);
        } elseif (is_callable($actualHandler)) {
            // Closure or callable
            return call_user_func($actualHandler, $request, $response, ...$params);
        } else {
            throw new \Exception("Invalid route handler");
        }
    }

    private function handleNotFound(Request $request)
    {
        if (file_exists(__DIR__ . '/../../src/Views/errors/404.php')) {
            return view('errors.404', ['path' => $request->getPath()]);
        } else {
            return "<h1>404 Not Found</h1><p>The requested path '{$request->getPath()}' was not found.</p>";
        }
    }

    public function group($prefix, $callback)
    {
        $previousPrefix = $this->currentGroupPrefix ?? '';
        $this->currentGroupPrefix = $previousPrefix . $prefix;

        call_user_func($callback, $this);

        $this->currentGroupPrefix = $previousPrefix;
    }

    private function applyGroupPrefix($path)
    {
        if (isset($this->currentGroupPrefix)) {
            return rtrim($this->currentGroupPrefix, '/') . '/' . ltrim($path, '/');
        }
        return $path;
    }
}