<?php

// Web Routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/about', [\App\Controllers\HomeController::class, 'about']);
$router->get('/contact', [\App\Controllers\HomeController::class, 'contact']);

// User Routes
$router->get('/users', [\App\Controllers\UserController::class, 'index']);
$router->get('/users/{id}', [\App\Controllers\UserController::class, 'show']);
$router->get('/users/create', [\App\Controllers\UserController::class, 'create']);
$router->post('/users', [\App\Controllers\UserController::class, 'store']);

// Example of closure route
$router->get('/hello', function() {
    return "Hello from Kodomo Framework!";
});