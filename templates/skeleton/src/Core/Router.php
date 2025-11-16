<?php

namespace Kodomo\Core;

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

        // Try exact match first
        $handler = $this->routes[$method][$path] ?? null;

        // If no exact match, try pattern matching for routes with parameters
        if (!$handler) {
            $handler = $this->matchPattern($method, $path);
        }

        if (!$handler) {
            $response->setStatusCode(404);
            return view('errors.404', ['path' => $path]);
        }

        if (is_array($handler)) {
            $controller = new $handler[0];
            $methodName = $handler[1];
            
            // Pass route parameters to controller method
            if (isset($handler[2])) {
                return $controller->$methodName(...$handler[2]);
            }
            
            return $controller->$methodName($request);
        }

        return call_user_func($handler, $request);
    }

    private function matchPattern($method, $path)
    {
        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            // Convert route pattern to regex
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";
            
            if (preg_match($pattern, $path, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                if (is_array($handler)) {
                    $handler[] = array_values($params);
                }
                
                return $handler;
            }
        }
        
        return null;
    }
}