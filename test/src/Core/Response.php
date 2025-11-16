<?php

namespace Tiny\Core;

class Response
{
    public function setStatusCode($code)
    {
        http_response_code($code);
    }
}