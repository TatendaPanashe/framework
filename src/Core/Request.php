<?php

namespace Kodomo\Core;

class Request
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $path = strtok($path, '?');
        return $path === '' ? '/' : $path;
    }

    public function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function input($key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public function all()
    {
        return array_merge($_GET, $_POST);
    }

    public function file($key)
    {
        return $_FILES[$key] ?? null;
    }

    public function isMethod($method)
    {
        return $this->getMethod() === strtoupper($method);
    }

    public function isGet()
    {
        return $this->isMethod('GET');
    }

    public function isPost()
    {
        return $this->isMethod('POST');
    }

    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    public function header($key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }

    public function bearerToken()
    {
        $header = $this->header('Authorization');
        
        if (str_starts_with($header ?? '', 'Bearer ')) {
            return substr($header, 7);
        }
        
        return null;
    }

    public function json()
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?? [];
    }

    public function ip()
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? 
               $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public function wantsJson()
    {
        return str_contains($this->header('Accept') ?? '', 'application/json');
    }

    public function isAjax()
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function has($key)
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public function only($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $results = [];
        
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $results[$key] = $this->input($key);
            }
        }
        
        return $results;
    }

    public function except($keys)
    {
        $keys = is_array($keys) ? $keys : func_get_args();
        $all = $this->all();
        
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        
        return $all;
    }
}