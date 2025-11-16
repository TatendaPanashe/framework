<?php

namespace Kodomo\Core;

class Response
{
    private array $headers = [];

    public function setStatusCode($code)
    {
        http_response_code($code);
        return $this;
    }

    public function header($key, $value)
    {
        $this->headers[$key] = $value;
        header("$key: $value");
        return $this;
    }

    public function json($data, $statusCode = 200)
    {
        $this->setStatusCode($statusCode)
             ->header('Content-Type', 'application/json');
        
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode)
             ->header('Location', $url);
        
        exit;
    }

    public function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return $this->redirect($referer);
    }

    public function with($key, $value)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION['flash'][$key] = $value;
        return $this;
    }

    public function withErrors($errors)
    {
        return $this->with('errors', $errors);
    }

    public function withInput()
    {
        return $this->with('old', $this->all());
    }

    public function download($filePath, $name = null, $headers = [])
    {
        if (!file_exists($filePath)) {
            $this->setStatusCode(404);
            return "File not found";
        }

        $name = $name ?? basename($filePath);
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        $this->header('Content-Type', $mimeType)
             ->header('Content-Disposition', "attachment; filename=\"$name\"")
             ->header('Content-Length', $fileSize)
             ->header('Pragma', 'no-cache')
             ->header('Expires', '0');

        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        readfile($filePath);
        exit;
    }

    public function stream($callback, $headers = [])
    {
        $this->header('Content-Type', 'text/plain')
             ->header('Cache-Control', 'no-cache')
             ->header('X-Accel-Buffering', 'no');

        foreach ($headers as $key => $value) {
            $this->header($key, $value);
        }

        if (is_callable($callback)) {
            call_user_func($callback);
        }

        exit;
    }

    public function noContent()
    {
        $this->setStatusCode(204);
        return '';
    }

    public function view($view, $data = [], $statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        return view($view, $data);
    }

    public function plain($content, $statusCode = 200)
    {
        $this->setStatusCode($statusCode)
             ->header('Content-Type', 'text/plain');
        
        return $content;
    }

    public function html($content, $statusCode = 200)
    {
        $this->setStatusCode($statusCode)
             ->header('Content-Type', 'text/html');
        
        return $content;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}