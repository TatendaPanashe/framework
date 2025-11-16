<?php

namespace Kodomo\Core;

class Response
{
    public function setStatusCode($code)
    {
        http_response_code($code);
    }

    public function json($data)
    {
        header('Content-Type: application/json');
        return json_encode($data);
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        header("Location: $url");
        exit;
    }
}