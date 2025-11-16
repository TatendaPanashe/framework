<?php

namespace Tiny\Core;

class App
{
    public Router $router;
    public Request $request;
    public Response $response;

    public function __construct()
    {
        $this->router = new Router();
        $this->request = new Request();
        $this->response = new Response();
    }

    public function run()
    {
        echo $this->router->resolve($this->request, $this->response);
    }
}