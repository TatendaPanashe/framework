<?php

namespace Tiny\Controllers;

class HomeController
{
    public function index()
    {
        return view('home');
    }
}

function view($viewName, $data = [])
{
    extract($data);
    ob_start();
    include __DIR__ . "/../Views/{$viewName}.php";
    return ob_get_clean();
}